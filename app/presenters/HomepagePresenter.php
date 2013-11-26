<?php

use stekycz\vmw\components\ICommercialControlFactory;
use stekycz\vmw\forms\IUploadFormFactory;
use Nette\Application\UI\Multiplier;



class HomepagePresenter extends BasePresenter
{

	/**
	 * @var \Kdyby\Doctrine\EntityDao
	 * @autowire(class="stekycz\vmw\models\Video", factory="Kdyby\Doctrine\EntityDaoFactory")
	 */
	protected $videoDao;

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
		$this->video = $this->videoDao->find($id);
	}



	public function renderResult($id)
	{
		$this->template->video = $this->video;
	}

}
