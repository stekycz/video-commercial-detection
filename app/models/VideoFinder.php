<?php

namespace stekycz\vmw\models;

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
	 * @return \stekycz\vmw\models\Video|NULL
	 */
	public function find($id)
	{
		return $this->videoDao->find($id);
	}



	/**
	 * @return \stekycz\vmw\models\Video|NULL
	 */
	public function findOldestUnprocessed()
	{
		return $this->videoDao->findOneBy(array(
			"locked" => FALSE,
			"processed" => NULL,
		), array(
			"created" => "ASC",
		));
	}



	/**
	 * @param \stekycz\vmw\models\Video $video
	 * @return \stekycz\vmw\models\VideoFinder
	 */
	public function save(Video $video)
	{
		$this->videoDao->save($video);

		return $this;
	}

}
