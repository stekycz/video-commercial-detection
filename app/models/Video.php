<?php

namespace stekycz\vmw\models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\IdentifiedEntity;



/**
 * @ORM\Entity
 * @ORM\Table(name="videos", indexes={
 * 		@ORM\Index(columns={"created"})
 * })
 *
 * @property-read string $name
 * @property-read string $filename
 * @property string|NULL $directory
 * @property bool $lock
 * @property \DateTime|NULL $processed
 * @property-read \DateTime $created
 * @property-read \stekycz\vmw\models\Commercial[] $commercials
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

	/**
	 * @ORM\Column(type="string", length=50, name="directory", nullable=true)
	 * @var string
	 */
	protected $directory;

	/**
	 * @ORM\Column(type="boolean", name="lock", nullable=false)
	 * @var bool
	 */
	protected $lock = FALSE;

	/**
	 * @ORM\Column(type="datetime", name="processed", nullable=true)
	 * @var \DateTime
	 */
	protected $processed;

	/**
	 * @ORM\Column(type="datetime", name="created", nullable=false)
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @ORM\OneToMany(targetEntity="\stekycz\vmw\models\Commercial", indexBy="id", mappedBy="video", cascade={"persist", "remove"})
	 * @var \stekycz\vmw\models\Commercial[]|\Doctrine\Common\Collections\Collection
	 */
	private $commercials;



	public function __construct($name, $filename)
	{
		parent::__construct();
		$this->name = $name;
		$this->filename = $filename;
		$this->created = new \DateTime();
		$this->commercials = new ArrayCollection();
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



	/**
	 * @return \DateTime|NULL
	 */
	public function getProcessed()
	{
		return $this->processed === NULL
			? NULL
			: clone $this->processed;
	}



	/**
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return clone $this->created;
	}



	/**
	 * @param \stekycz\vmw\models\Commercial $commercial
	 * @return \stekycz\vmw\models\Video
	 */
	public function addCommercial(Commercial $commercial)
	{
		$this->commercials->add($commercial);

		return $this;
	}



	/**
	 * @return \stekycz\vmw\models\Commercial[]
	 */
	public function getCommercials()
	{
		return $this->commercials->toArray();
	}

}
