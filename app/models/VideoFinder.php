<?php

namespace stekycz;

use Kdyby\Doctrine\EntityDao;
use Nette\Object;



class VideoFinder extends Object
{

	/**
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	private $videoDao;



	public function __construct(EntityDao $videoDao)
	{
		$this->videoDao = $videoDao;
	}



	/**
	 * @param int $id
	 * @return \stekycz\vmw\models\Video
	 */
	public function find($id)
	{
		return $this->videoDao->find($id);
	}

}
