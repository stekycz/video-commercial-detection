<?php

namespace stekycz\vmw\models\Detector;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
use Kdyby\Doctrine\EntityDao;
use Nette\Object;
use stekycz\vmw\models\Commercial;
use stekycz\vmw\models\FFMpeg\ClipFilter;
use stekycz\vmw\models\Video;



class VideoCutRangeConvertor extends Object
{

	const VIDEO_WIDTH = 320;
	const VIDEO_HEIGHT = 240;

	const FRAMES_PER_SECOND = 25; // 25
	const FRAMES_PER_MINUTE = 1500; // 25 * 60
	const FRAMES_PER_HOUR = 90000; // 25 * 60 * 60

	/**
	 * @var string
	 */
	private $originalFilePath;

	/**
	 * @var string
	 */
	private $cutFilesPath;

	/**
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	private $videoDao;

	/**
	 * @var \FFMpeg\FFMpeg
	 */
	private $ffmpeg;



	public function __construct($originalFilePath, $cutFilesPath, EntityDao $videoDao, FFMpeg $ffmpeg)
	{
		$this->originalFilePath = rtrim($originalFilePath, DIRECTORY_SEPARATOR);
		$this->cutFilesPath = rtrim($cutFilesPath, DIRECTORY_SEPARATOR);
		$this->videoDao = $videoDao;
		$this->ffmpeg = $ffmpeg;
	}



	/**
	 * @param \stekycz\vmw\models\Video $video
	 * @param int $frame
	 * @return \stekycz\vmw\models\Commercial
	 */
	public function createClip(Video $videoEntity, $frame)
	{
		$begin = self::frameToTimeCode(max($frame - (1 * self::FRAMES_PER_SECOND), 0)); // Move 1s backward
		$duration = TimeCode::fromSeconds(2);

		$video = $this->ffmpeg->open($this->originalFilePath . "/" . $videoEntity->filename);

		$video->filters()->resize(new Dimension(self::VIDEO_WIDTH, self::VIDEO_HEIGHT), ResizeFilter::RESIZEMODE_INSET);
		$video->addFilter(new ClipFilter($begin, $duration));

		$format = new X264();
		$directory = $this->getVideoDirectory($videoEntity);
		$filename = $frame . ".mp4";
		$video->save($format, $directory . "/" . $filename);

		list($time, $frame) = self::frameToTime($frame);
		$commercial = new Commercial($videoEntity, $filename, $time, $frame);
		$this->videoDao->related("commercials")->save($commercial);

		return $commercial;
	}



	private function getVideoDirectory(Video $video)
	{
		if ($video->directory === NULL) {
			$video->directory = sha1($video->id . $video->filename . $video->created->format("Y-m-d H:i:s"));
			$this->videoDao->save($video);
		}
		$directory = $this->cutFilesPath . "/" . $video->directory;
		if (!file_exists($directory)) {
			mkdir($directory, 0777, TRUE);
		}

		return $directory;
	}



	/**
	 * @param int $frame
	 * @return \DateTime
	 */
	private static function frameToTime($frame)
	{
		$hours = floor($frame / self::FRAMES_PER_HOUR);
		$frame = $frame % self::FRAMES_PER_HOUR;

		$minutes = floor($frame / self::FRAMES_PER_MINUTE);
		$frame = $frame % self::FRAMES_PER_MINUTE;

		$seconds = floor($frame / self::FRAMES_PER_SECOND);
		$frame = $frame % self::FRAMES_PER_SECOND;

		$time = new \DateTime();
		return [
			$time->setTime($hours, $minutes, $seconds),
			$frame
		];
	}



	private static function frameToTimeCode($frame)
	{
		list($time, $frame) = self::frameToTime($frame);

		return new TimeCode($time->format("H"), $time->format("i"), $time->format("s"), $frame);
	}

}
