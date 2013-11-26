<?php

namespace stekycz\vmw\models\Detector;

use Nette\Object;
use stekycz\vmw\models\Video;



class CutDetector extends Object
{

	/**
	 * @param \stekycz\vmw\models\Video $video
	 * @return int[]
	 */
	public function detectScenes(Video $video)
	{
		return [0, 151, 3285]; // TODO
	}

}
