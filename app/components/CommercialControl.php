<?php

namespace stekycz\vmw\components;



class CommercialControl extends BaseControl
{

	private $filepath;



	public function __construct($filepath)
	{
		parent::__construct();
		$this->filepath = $filepath;
	}



	public function render()
	{
		$this->template->filepath = $this->filepath;

		$this->template->setFile(__DIR__ . "/commercial-control.latte");
		$this->template->render();
	}

}



interface ICommercialControlFactory
{

	/**
	 * @param string $filepath
	 * @return \stekycz\vmw\components\CommercialControl
	 */
	public function create($filepath);

}
