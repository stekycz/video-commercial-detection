<?php

namespace stekycz\vmw\models\Detector;

use Nette\Object;
use Nette\Utils\Strings;
use stekycz\Binaries\Mpeg7Driver;
use stekycz\Binaries\ToWavDriver;
use stekycz\vmw\models\Video;



class CommercialDetector extends Object
{

	const MIN_IMPORTANT_CUT_DELAY = 25; // 1 second in frames

	const DIFFERENCE_LEVEL = 2;

	/**
	 * @param \stekycz\vmw\models\Video $video
	 * @param int[] $cutFrameNumbers
	 * @return int[]
	 */
	public function detectPossibleCommercials(Video $video, array $cutFrameNumbers)
	{
		$cutFrameNumbers = self::filterCloseDelays($cutFrameNumbers);

		$audioFile = __DIR__ . "/../../../temp/sounds/" . $video->filename . ".wav";
		$driver = ToWavDriver::create();
		$driver->command([
			__DIR__ . "/../../../files/" . $video->filename,
			$audioFile
		]);

		$mpeg7outputFile = __DIR__ . "/../../../temp/sounds/" . $video->filename . ".mp7";
		$driver = Mpeg7Driver::create();
		$driver->command([
			$audioFile,
			$mpeg7outputFile,
		]);

		$xml = new \SimpleXMLElement($mpeg7outputFile, 0, TRUE);
		$dataElements = (array) $xml->xpath("//AudioDescriptor[xsi:type='AudioPowerType']/SeriesOfScalar/Raw");
		$valuesByFrames = [];
		foreach ($dataElements as $el) {
			foreach (Strings::split((string) $el, '/\s+/') as $value) {
				$valuesByFrames[] = floatval($value);
			}
		}

		$commercialFrames = [];
		foreach ($valuesByFrames as $frame => $value) {
			if (abs(self::avg(array_slice($valuesByFrames, $frame - 6, 5)) - self::avg(array_slice($valuesByFrames, $frame, 5))) >= self::DIFFERENCE_LEVEL) {
				$commercialFrames[] = $frame;
			}
		}

		return $commercialFrames;
	}



	private static function avg(array $values)
	{
		return array_sum($values) / count($values);
	}



	private static function filterCloseDelays(array $cutFrameNumbers)
	{
		$filtered = [];
		$lastFrame = NULL;
		foreach ($cutFrameNumbers as $frame) {
			if ($lastFrame === NULL || ($frame - $lastFrame) > self::MIN_IMPORTANT_CUT_DELAY) {
				if ($lastFrame !== NULL) {
					$filtered[] = $lastFrame;
				}
			}
			$lastFrame = $frame;
		}
		if ($lastFrame !== NULL) {
			$filtered[] = $lastFrame;
		}

		return $filtered;
	}

}
