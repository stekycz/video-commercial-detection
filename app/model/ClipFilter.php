<?php

namespace stekycz\vmw\models;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Media\Video as FFVideo;



class ClipFilter implements VideoFilterInterface
{

	/**
	 * @var \FFMpeg\Coordinate\TimeCode
	 */
	private $begin;

	/**
	 * @var \FFMpeg\Coordinate\TimeCode
	 */
	private $duration;

	/**
	 * @var integer
	 */
	private $priority;



	public function __construct(TimeCode $begin, TimeCode $duration, $priority = 0)
	{
		$this->begin = $begin;
		$this->duration = $duration;
		$this->priority = $priority;
	}

	/**
	 * Returns the priority of the filter.
	 *
	 * @return integer
	 */
	public function getPriority()
	{
		return $this->priority;
	}



	/**
	 * Applies the filter on the the Video media given an format.
	 *
	 * @param Video $video
	 * @param VideoInterface $format
	 *
	 * @return array An array of arguments
	 */
	public function apply(FFVideo $video, VideoInterface $format)
	{
		$commands = array();

		$commands[] = '-ss';
		$commands[] = (string) $this->begin;

		$commands[] = '-t';
		$commands[] = (string) $this->duration;

		return $commands;
	}

}
