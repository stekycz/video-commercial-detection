<?php

abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	use \Kdyby\Autowired\AutowireProperties;
	use \Kdyby\Autowired\AutowireComponentFactories;



	protected function successFlashMessage($message)
	{
		$this->flashMessage($message, "success");
	}



	protected function errorFlashMessage($message)
	{
		$this->flashMessage($message, "danger");
	}



	protected function warningFlashMessage($message)
	{
		$this->flashMessage($message, "warning");
	}

}
