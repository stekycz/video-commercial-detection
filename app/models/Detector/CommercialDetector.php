<?php

namespace stekycz\vmw\models\Detector;

use Nette\Object;
use stekycz\Binaries\ToWavDriver;
use stekycz\vmw\models\Video;



class CommercialDetector extends Object
{

	const MIN_IMPORTANT_CUT_DELAY = 25; // 1 second in frames

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

		return $cutFrameNumbers; // TODO
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
