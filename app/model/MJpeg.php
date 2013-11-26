<?php

namespace stekycz\vmw\models;

use FFMpeg\Format\Video\DefaultVideo;



class MJpeg extends DefaultVideo
{

	public function __construct($videoCodec = 'mjpeg')
	{
		$this
			->setVideoCodec($videoCodec);
	}



	/**
	 * {@inheritDoc}
	 */
	public function getAvailableVideoCodecs()
	{
		return array('mjpeg');
	}



	/**
	 * Returns the list of available audio codecs for this format.
	 *
	 * @return array
	 */
	public function getAvailableAudioCodecs()
	{
		return array();
	}



	/**
	 * Returns true if the current format supports B-Frames.
	 *
	 * @see https://wikipedia.org/wiki/Video_compression_picture_types
	 *
	 * @return Boolean
	 */
	public function supportBFrames()
	{
		return FALSE;
	}

}
