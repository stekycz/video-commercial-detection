<?php

namespace stekycz\vmw\models;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Nette\Object;
use stekycz\vmw\models\FFMpeg\ClipFilter;



class VideoCutRangeConvertor extends Object
{

	const FRAMES_PER_SECOND = 25; // 25
	const FRAMES_PER_MINUTE = 1500; // 25 * 60
	const FRAMES_PER_HOUR = 90000; // 25 * 60 * 60

	/**
	 * @var string
	 */
	private $cutFilesPath;

	/**
	 * @var \FFMpeg\FFMpeg
	 */
	private $ffmpeg;



	public function __construct($cutFilesPath, FFMpeg $ffmpeg)
	{
		$this->cutFilesPath = rtrim($cutFilesPath, '/');
		$this->ffmpeg = $ffmpeg;
	}



	public function createRangeVideo($videoPath, $frame)
	{
		$begin = self::frameToTimeCode(max($frame - (1 * self::FRAMES_PER_SECOND), 0));
		$duration = TimeCode::fromSeconds(2);

		$video = $this->ffmpeg->open($videoPath);

		$video->addFilter(new ClipFilter($begin, $duration));

		$format = new X264();
		$videoDir = sha1($videoPath);
		mkdir($this->cutFilesPath . "/" . $videoDir);
		$video->save($format, $this->cutFilesPath . "/$videoDir/$frame.mp4");
	}



	private static function frameToTimeCode($frame)
	{
		$hours = floor($frame / self::FRAMES_PER_HOUR);
		$frame = $frame % self::FRAMES_PER_HOUR;

		$minutes = floor($frame / self::FRAMES_PER_MINUTE);
		$frame = $frame % self::FRAMES_PER_MINUTE;

		$seconds = floor($frame / self::FRAMES_PER_SECOND);
		$frame = $frame % self::FRAMES_PER_SECOND;

		return new TimeCode($hours, $minutes, $seconds, $frame);
	}

}
