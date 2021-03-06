<?php

use stekycz\vmw\components\ICommercialControlFactory;
use stekycz\vmw\forms\IUploadFormFactory;
use Nette\Application\UI\Multiplier;



class HomepagePresenter extends BasePresenter
{

	/**
	 * @var \stekycz\vmw\models\VideoFinder
	 * @autowire
	 */
	protected $videoFinder;

	/**
	 * @var \stekycz\vmw\models\Video|NULL
	 */
	private $video;



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
			return $factory->create($this->video->commercials[$id]);
		});

		return $control;
	}



	public function actionResult($id)
	{
		$this->video = $this->videoFinder->find($id);
		if ($this->video === NULL) {
			$this->error("Video with ID '$id' was not found.");
		}
	}



	public function renderResult($id)
	{
		if ($this->video->locked) {
			$this->warningFlashMessage("Video se zpracovává. Zkuste to prosím později.");
		} elseif ($this->video->processed === NULL) {
			$this->warningFlashMessage("Video ještě nebylo zpracováno. Zkuste to prosím později.");
		}
		$this->template->video = $this->video;
	}

}
