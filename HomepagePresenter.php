<?php
namespace App\Presenters;

use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault()
	{
		$this->template->kosmonauti = $this->database->table('kosmonauti')->order('prijmeni');
	}
	
	protected function createComponentInsertForm()
	{
		$form = new Nette\Application\UI\Form;

		$form->addText('prijmeni', '')
			->setAttribute("placeholder", "Příjmení")
			->setRequired("Příjmení je povinný údaj!");
		
		$form->addText('jmeno', '')
			->setAttribute("placeholder", "Jméno")
			->setRequired("Jméno je povinný údaj!");
			
		$form->addText("datum_narozeni", "")
		->setRequired("Datum je povinný údaj!")
		->setAttribute("class", "dtpicker")
		->setAttribute("placeholder", "dd.mm.rrrr")
		->addRule($form::PATTERN, "Datum musí být ve formátu dd.mm.rrrr", "(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20)\d\d");

		$form->addText('superschopnost', '')
			->setAttribute("placeholder", "Superschopnost")
			->setRequired("Superschopnost je povinný údaj!");

		$form->addSubmit('send', 'Vložit kosmonauta');
		$form->onSuccess[] = [$this, 'insertFormSucceeded'];

		return $form;
	}
	
	public function insertFormSucceeded($form, $values)
	{
		
		$yourDateTime = $values->datum_narozeni;

		$this->database->table('kosmonauti')->insert([
			'jmeno' => $values->jmeno,
			'prijmeni' => $values->prijmeni,
			'datum_narozeni' => date('Y-m-d', strtotime($yourDateTime)),
			'superschopnost' => $values->superschopnost,
		]);

		$this->flashMessage('Kosmonaut byl úspěšně přidán!', 'success');
		$this->redirect('this');
	}
	
	public function handleSmazatKosmonauta($id)
	{
		$this->database->table('kosmonauti')->where('id', $id)->delete();
		$this->flashMessage('Kosmonaut byl úspěšně odstraněn!', 'success');
		$this->redirect('this');
	}
}