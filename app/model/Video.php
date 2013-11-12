<?php

namespace stekycz\vmw\models;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\IdentifiedEntity;



/**
 * @ORM\Entity
 * @ORM\Table(name="videos")
 *
 * @property-read string $name
 */
class Video extends IdentifiedEntity
{

	/**
	 * @ORM\Column(type="string", length=50, name="name", nullable=false)
	 * @var string
	 */
	private $name;



	public function __construct($name)
	{
		parent::__construct();
		$this->name = $name;
	}



	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

}
