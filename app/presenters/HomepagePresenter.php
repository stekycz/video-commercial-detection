<?php

use stekycz\vmw\components\ICommercialControlFactory;
use stekycz\vmw\forms\IUploadFormFactory;
use Nette\Application\UI\Multiplier;



class HomepagePresenter extends BasePresenter
{

	private static $files = array(
		0 => "64f05823a65bb3146f5ae6fe987fbebc0b80b3b3/0.mp4",
		151 => "64f05823a65bb3146f5ae6fe987fbebc0b80b3b3/151.mp4",
		3285 => "64f05823a65bb3146f5ae6fe987fbebc0b80b3b3/3285.mp4",
	);

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



	/**
	 * @param \stekycz\components\ICommercialControlFactory
	 * @return \stekycz\components\CommercialControl
	 */
	protected function createComponentCommercial(ICommercialControlFactory $factory)
	{
		$control = new Multiplier(function ($id) use ($factory) {
			return $factory->create(self::$files[$id]);
		});

		return $control;
	}



	public function renderResult($id)
	{
		$this->template->files = self::$files;
	}

}
