<?php

namespace stekycz\vmw\models\Detector;

use Nette\Object;



class CommercialDetector extends Object
{

	/**
	 * @param int[] $cutFrameNumbers
	 * @return int[]
	 */
	public function detectPossibleCommercials(array $cutFrameNumbers)
	{
		return $cutFrameNumbers; // TODO
	}

}
