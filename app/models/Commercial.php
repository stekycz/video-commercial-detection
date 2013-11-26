<?php

namespace stekycz\vmw\models;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\IdentifiedEntity;



/**
 * @ORM\Entity
 * @ORM\Table(name="commercials")
 *
 * @property-read \stekycz\vmw\models\Video $video
 * @property-read string $filename
 */
class Commercial extends IdentifiedEntity
{

	/**
	 * @ORM\ManyToOne(targetEntity="\stekycz\vmw\models\Video", inversedBy="commercials", cascade={"persist"})
	 * @ORM\JoinColumn(name="video_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 * @var \stekycz\vmw\models\Video
	 */
	private $video;

	/**
	 * @ORM\Column(type="string", length=50, name="filename", nullable=false)
	 * @var string
	 */
	private $filename;



	public function __construct(Video $video, $filename)
	{
		parent::__construct();
		$this->video = $video->addCommercial($this);
		$this->filename = $filename;
	}



	/**
	 * @return \stekycz\vmw\models\Video
	 */
	public function getVideo()
	{
		return $this->video;
	}



	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

}
