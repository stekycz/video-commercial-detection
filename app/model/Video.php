<?php

namespace stekycz\vmw\models;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\IdentifiedEntity;



/**
 * @ORM\Entity
 * @ORM\Table(name="videos")
 *
 * @property-read string $name
 * @property-read string $filename
 */
class Video extends IdentifiedEntity
{

	/**
	 * @ORM\Column(type="string", length=255, name="name", nullable=false)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=50, name="filename", nullable=false)
	 * @var string
	 */
	private $filename;



	public function __construct($name, $filename)
	{
		parent::__construct();
		$this->name = $name;
		$this->filename = $filename;
	}



	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}



	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

}
