<?php

use stekycz\vmw\forms\IUploadFormFactory;



class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
	}



	/**
	 * @param \stekycz\vmw\forms\IUploadFormFactory $factory
	 * @return \Nette\Forms\Form
	 */
	protected function createComponentUploadForm(IUploadFormFactory $factory)
	{
		$form = $factory->create();
		$form->onSuccess[] = function () {
			$this->successFlashMessage("Soubor byl úspěšně nahrán a v blízké době bude zprocesován.");
		};

		return $form;
	}



	public function renderResult($id)
	{
	}

}
