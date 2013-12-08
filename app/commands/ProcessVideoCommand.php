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
			try {
				$video->locked = TRUE;
				$this->videoFinder->save($video);

				$output->writeln("Begin detection of scene cuts...");
				$cutFrameNumbers = $this->cutDetector->detectScenes($video);
				$output->writeln("Finished detection of scene cuts...");

				$output->writeln("Begin detection of possible commercials...");
				$commercials = $this->commercialDetector->detectPossibleCommercials($video, $cutFrameNumbers);
				$output->writeln("Finished detection of possible commercials...");

				$output->writeln("Begin generation of detected commercial cuts...");
				/** @var \Symfony\Component\Console\Helper\ProgressHelper $progress */
				$progress = $this->getHelperSet()->get('progress');
				$progress->setFormat($progress::FORMAT_VERBOSE);
				$progress->start($output, count($commercials));

				foreach ($commercials as $frame) {
					$this->videoCutRangeConvertor->createClip($video, $frame);
					$progress->advance();
				}

				$progress->finish();
				$output->writeln("Finished generation of detected commercial cuts...");

				$video->processed = new \DateTime();
			} catch (\Exception $e) {
				throw $e;
			} finally {
				$video->locked = FALSE;
				$this->videoFinder->save($video);
			}

			$output->writeln("<info>Video process finished</info>");
		} else {
			$output->writeln("<comment>No video found to be processed</comment>");
		}

		return 0;
	}

}
