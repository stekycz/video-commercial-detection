<?php

namespace stekycz\vmw\forms;

use Kdyby\Doctrine\EntityDao;
use Nette\Forms\Form;
use stekycz\vmw\models\Video;



class UploadForm extends BaseForm
{

	/**
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	private $videoDao;



	public function __construct(EntityDao $videoDao)
	{
		parent::__construct();
		$this->videoDao = $videoDao;

		$this->addUpload("file", "Soubor videa")
			->setRequired("Vyberte prosím soubor videa k uploadu.")
			->addRule(Form::MIME_TYPE, "Nahrávaný soubor není podporavý typ videa.", "video/mp4");

		$this->addSubmit("sender", "Upload");

		$this->onSuccess[] = $this->processForm;
	}



	public function processForm(UploadForm $form)
	{
		/** @var \Nette\Http\FileUpload $file */
		$file = $form->values->file;
		$this->videoDao->save(new Video($file->name, sha1(time() . "|" . microtime(TRUE) . "|" . $file->name)));
	}

}

interface IUploadFormFactory
{

	/**
	 * @return \stekycz\vmw\forms\UploadForm
	 */
	public function create();

}
