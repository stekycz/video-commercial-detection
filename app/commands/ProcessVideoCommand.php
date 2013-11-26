<?php

namespace stekycz\vmw\models;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



class ProcessVideoCommand extends Command
{

	/**
	 * @var \stekycz\vmw\models\VideoCutRangeConvertor
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
		$this->videoCutRangeConvertor = $this->getHelper('container')->getByType('stekycz\vmw\models\VideoCutRangeConvertor');
		$this->videoFinder = $this->getHelper('container')->getByType('stekycz\vmw\models\VideoFinder');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$video = $this->videoFinder->findOldestUnprocessed();
		if ($video !== NULL) {
			$videoPath = __DIR__ . "/../../files/" . $video->filename;

			/** @var \Symfony\Component\Console\Helper\ProgressHelper $progress */
			$progress = $this->getHelperSet()->get('progress');
			$progress->setFormat($progress::FORMAT_VERBOSE);
			$progress->start($output, 3);

			$this->videoCutRangeConvertor->createRangeVideo($videoPath, 0);
			$progress->advance();
			$this->videoCutRangeConvertor->createRangeVideo($videoPath, 151);
			$progress->advance();
			$this->videoCutRangeConvertor->createRangeVideo($videoPath, 3285);
			$progress->advance();

			$progress->finish();

			$output->writeln("<info>Video process finished</info>");
		} else {
			$output->writeln("<comment>No video found to be processed</comment>");
		}

		return 0;
	}

}
