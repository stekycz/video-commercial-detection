<?php

namespace stekycz\vmw\models\Detector;

use Alchemy\BinaryDriver\Configuration;
use Alchemy\BinaryDriver\ProcessBuilderFactory;
use Monolog\Logger;
use Nette\Object;
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
		$driver = FindCutsDriver::create();
		$driver->command([
			__DIR__ . "/../../../files/" . $video->filename,
			__DIR__ . "/../../../temp/scenes/" . $video->filename . ".cuts.txt",
			__DIR__ . "/../../../temp/scenes"
		]);

		return [0, 151, 3285]; // TODO
	}

}
