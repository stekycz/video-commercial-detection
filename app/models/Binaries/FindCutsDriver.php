<?php

namespace stekycz\Binaries;

use Alchemy\BinaryDriver\AbstractBinary;
use Alchemy\BinaryDriver\Configuration;
use Alchemy\BinaryDriver\ConfigurationInterface;
use Alchemy\BinaryDriver\Exception\ExecutableNotFoundException as BinaryDriverExecutableNotFound;
use FFMpeg\Exception\ExecutableNotFoundException;
use Psr\Log\LoggerInterface;



class FindCutsDriver extends AbstractBinary
{

	public function getName()
	{
		return "find-cuts.sh";
	}



	public static function create(LoggerInterface $logger = NULL, $configuration = array())
	{
		if (!$configuration instanceof ConfigurationInterface) {
			$configuration = new Configuration($configuration);
		}

		if (!$configuration->has('timeout')) {
			$configuration->set('timeout', 1800);
		}

		try {
			return static::load(__DIR__ . "/../../../bin/find-cuts.sh", $logger, $configuration);
		} catch (BinaryDriverExecutableNotFound $e) {
			throw new ExecutableNotFoundException('Unable to load find-cuts.sh', $e->getCode(), $e);
		}
	}

}
