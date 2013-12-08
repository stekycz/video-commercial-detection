<?php

namespace stekycz\vmw\models\Detector;

use Nette\Object;
use Nette\Utils\Strings;
use stekycz\Binaries\Mpeg7Driver;
use stekycz\Binaries\ToWavDriver;
use stekycz\vmw\models\Video;



class CommercialDetector extends Object
{

	const MIN_IMPORTANT_CUT_DELAY = 25; // 1 second in frames

	/**
	 * @param \stekycz\vmw\models\Video $video
	 * @param int[] $cutFrameNumbers
	 * @return int[]
	 */
	public function detectPossibleCommercials(Video $video, array $cutFrameNumbers)
	{
		$cutFrameNumbers = self::filterCloseDelays($cutFrameNumbers);

		$audioFile = __DIR__ . "/../../../temp/sounds/" . $video->filename . ".wav";
		$driver = ToWavDriver::create();
		$driver->command([
			__DIR__ . "/../../../files/" . $video->filename,
			$audioFile
		]);

		$mpeg7outputFile = __DIR__ . "/../../../temp/sounds/" . $video->filename . ".mp7";
		$driver = Mpeg7Driver::create();
		$driver->command([
			$audioFile,
			$mpeg7outputFile,
		]);

		@unlink($audioFile);

		$xml = new \SimpleXMLElement($mpeg7outputFile, 0, TRUE);
		$dataElements = (array) $xml->xpath("//mpeg7:Description/mpeg7:MultimediaContent/mpeg7:Audio/mpeg7:AudioDescriptor/mpeg7:SeriesOfScalar/mpeg7:Raw");
		$valuesByFrames = [];
		foreach ($dataElements as $el) {
			foreach (Strings::split((string) $el, '/\s+/') as $value) {
				$valuesByFrames[] = floatval($value);
			}
		}

		@unlink($mpeg7outputFile);

		$commercialFrames = [];
		foreach ($cutFrameNumbers as $frame) {
			if (self::isCommercial($valuesByFrames, $frame)) {
				$commercialFrames[] = $frame;
			}
		}

		return $commercialFrames;
	}



	private static function isCommercial(array $valuesByFrames, $frame)
	{
		$isChange = TRUE;

		$beforeAvg = self::avg(array_slice($valuesByFrames, $frame - 26, 25));
		$afterAvg = self::avg(array_slice($valuesByFrames, $frame, 25));
		$difference = abs($beforeAvg - $afterAvg);
		dlog($frame, $beforeAvg, $afterAvg);
		$isChange = $isChange && (
				($beforeAvg >= 0.0005 && $beforeAvg <= 0.003)
				|| ($afterAvg >= 0.0005 && $afterAvg <= 0.003)
			) && $difference <= 0.0014 && $difference >= 0.0010;

		return $isChange;
	}



	private static function min(array $values)
	{
		return array_reduce($values, function ($sum, $value) {
			return $sum > $value ? $value : $sum;
		}, PHP_INT_MAX);
	}



	private static function max(array $values)
	{
		return array_reduce($values, function ($sum, $value) {
			return $sum < $value ? $value : $sum;
		}, -1);
	}



	private static function avg(array $values)
	{
		return array_sum($values) / count($values);
	}



	private static function mean(array $values)
	{
		sort($values, SORT_NUMERIC);
		return $values[(int) floor(count($values) / 2)];
	}



	private static function filterCloseDelays(array $cutFrameNumbers)
	{
		$filtered = [];
		$lastFrame = NULL;
		foreach ($cutFrameNumbers as $frame) {
			if ($lastFrame === NULL || ($frame - $lastFrame) > self::MIN_IMPORTANT_CUT_DELAY) {
				if ($lastFrame !== NULL) {
					$filtered[] = $lastFrame;
				}
			}
			$lastFrame = $frame;
		}
		if ($lastFrame !== NULL) {
			$filtered[] = $lastFrame;
		}

		return $filtered;
	}

}
