<?php

namespace stekycz\vmw\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



class ProcessVideoCommand extends Command
{

	/**
	 * @var \stekycz\vmw\models\Detector\CutDetector
	 */
	private $cutDetector;

	/**
	 * @var \stekycz\vmw\models\Detector\CommercialDetector
	 */
	private $commercialDetector;

	/**
	 * @var \stekycz\vmw\models\Detector\VideoCutRangeConvertor
	 */
	private $videoCutRangeConvertor;

	/**
	 * @var \stekycz\vmw\models\VideoFinder
	 */
	private $videoFinder;



	protected function configure()
	{
		$this->setName('vmw:processVideo')
			 ->setDescription('Processes video to detect commercials');
	}



	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getHelper('container');
		$this->cutDetector = $container->getByType('stekycz\vmw\models\Detector\CutDetector');
		$this->commercialDetector = $container->getByType('stekycz\vmw\models\Detector\CommercialDetector');
		$this->videoCutRangeConvertor = $container->getByType('stekycz\vmw\models\Detector\VideoCutRangeConvertor');
		$this->videoFinder = $container->getByType('stekycz\vmw\models\VideoFinder');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$video = $this->videoFinder->findOldestUnprocessed();
		if ($video !== NULL) {
			$video->locked = TRUE;
			$this->videoFinder->save($video);

			$cutFrameNumbers = $this->cutDetector->detectScenes($video);
			$commercials = $this->commercialDetector->detectPossibleCommercials($cutFrameNumbers);

			/** @var \Symfony\Component\Console\Helper\ProgressHelper $progress */
			$progress = $this->getHelperSet()->get('progress');
			$progress->setFormat($progress::FORMAT_VERBOSE);
			$progress->start($output, count($commercials));

			foreach ($commercials as $frame) {
				$this->videoCutRangeConvertor->createClip($video, $frame);
				$progress->advance();
			}

			$progress->finish();

			$video->locked = FALSE;
			$video->processed = new \DateTime();
			$this->videoFinder->save($video);

			$output->writeln("<info>Video process finished</info>");
		} else {
			$output->writeln("<comment>No video found to be processed</comment>");
		}

		return 0;
	}

}
