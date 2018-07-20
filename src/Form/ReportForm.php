<?php
namespace EhpSearchForm\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;
use EhpSearchForm\Form\FilterFieldset;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as ElementManager;

class ReportForm extends Form
{
	/** @var ElementManager */
	private $formElementManager;
	
	protected function settings() {
		return $this->getOption('search_page')->settings()['form'];
	}

	public function init()
	{
		//Champ texte de recherche générique
		$this->add([
			'name' => 'q',
			'type' => Element\Text::class,
			'options' => [
				'label' => 'Query', // @translate
			],
			'attributes' => [
				'size' => 40,
				'placeholder' => 'Search for…', // @translate
			],
		]);

		$this->add($this->dateFieldset());
		$this->add($this->filtersFieldset());

		$this->add([
			'name' => 'submit',
			'type' => Element\Submit::class,
			'attributes' => [
				'value' => 'Submit', // @translate
			],
		]);
	}

	public function getInputFilter()
	{
		$inputFilter = parent::getInputFilter();

		$inputFilter->get('date')->get('from')->setRequired(false);
		$inputFilter->get('date')->get('to')->setRequired(false);

		return $inputFilter;
	}

	public function setFormElementManager(ElementManager $formElementManager)
	{
		$this->formElementManager = $formElementManager;
	}
	
	/**
	 * 
	 * @return Fieldset Deux champs (intervalle de dates)
	 */
	protected function dateFieldset()
	{
		$fieldset = new Fieldset('date');
		$fieldset->setLabel('Date interval'); // @translate

		$fieldset->add([
			'name' => 'from',
			'type' => Element\Date::class,
			'options' => [
				'label' => 'Published after', // @translate
			],
			'attributes' => [
				'placeholder' => 'YYYY-MM-DD', // @translate
			],
		]);

		$fieldset->add([
			'name' => 'to',
			'type' => Element\Date::class,
			'options' => [
				'label' => 'Published before', // @translate
			],
			'attributes' => [
				'placeholder' => 'YYYY-MM-DD', // @translate
			],
		]);

		return $fieldset;
	}

	/**
	 * 
	 * @return Fieldset Ensemble infini de FilterFieldset
	 */
	protected function filtersFieldset()
	{
		return [
			'type' => Element\Collection::class,
			'name' => 'filters',
			'options' => [
				'manual_label' => 'Filters', // @translate
				'count' => 2,
				'should_create_template' => true,
				'allow_add' => true,
				'target_element' => $this->getFilterFieldset(),
			],
		];
	}

	protected function getForm($name, $options)
	{
		return $this->formElementManager->get($name, $options);
	}

	/**
	 * 
	 * @return FilterFieldset Un champ text et un champ select qui permet de
	 * choisir un des champs Solr parmi ceux autorisés depuis le formulaire de
	 * config.
	 */
	protected function getFilterFieldset()
	{
		return $this->formElementManager
				->get(FilterFieldset::class, $this->getOptions());
	}
}
