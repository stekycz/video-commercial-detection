<?php

namespace stekycz\vmw\models\Detector;

use Nette\Object;
use Nette\Utils\Strings;
use stekycz\Binaries\FindCutsDriver;
use stekycz\vmw\models\Video;



class CutDetector extends Object
{

	/**
	 * @param \stekycz\vmw\models\Video $video
	 * @return int[]
	 */
	public function detectScenes(Video $video)
	{
		$cutsFilename = __DIR__ . "/../../../temp/scenes/" . $video->filename . ".cuts.txt";
		$driver = FindCutsDriver::create();
		$driver->command([
			__DIR__ . "/../../../files/" . $video->filename,
			$cutsFilename,
			__DIR__ . "/../../../temp/scenes"
		]);

		$output = file_get_contents($cutsFilename);
		if ($output === FALSE) {
			@unlink($cutsFilename);
			return [];
		}
		@unlink($cutsFilename);

		$frames = [];
		foreach (array_slice(Strings::split($output, '/\n/'), 4) as $line) {
			if ($matches = Strings::match($line, '/^(\d+)\s+(\d+)\s+(\d+).*$/')) {
				$frames[] = (int) $matches[2];
			}
		}

		return $frames;
	}

}
