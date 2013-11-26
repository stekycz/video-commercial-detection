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



	protected function configure()
	{
		$this->setName('vmw:processVideo')
			 ->setDescription('Processes video to detect commercials');
	}



	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->videoCutRangeConvertor = $this->getHelper('container')->getByType('stekycz\vmw\models\VideoCutRangeConvertor');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$videoPath = __DIR__ . "/../../files/video.mp4";

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

		return 0;
	}

}
