<?php

namespace stekycz\vmw\components;

use stekycz\vmw\models\Commercial;



class CommercialControl extends BaseControl
{

	private $commercial;



	public function __construct(Commercial $commercial)
	{
		parent::__construct();
		$this->commercial = $commercial;
	}



	public function render()
	{
		$this->template->commercial = $this->commercial;

		$this->template->setFile(__DIR__ . "/commercial-control.latte");
		$this->template->render();
	}

}



interface ICommercialControlFactory
{

	/**
	 * @param \stekycz\vmw\models\Commercial $commercial
	 * @return \stekycz\vmw\components\CommercialControl
	 */
	public function create(Commercial $commercial);

}
