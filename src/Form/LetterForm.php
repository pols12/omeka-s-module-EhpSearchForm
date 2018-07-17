<?php
namespace EhpSearchForm\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;
use EhpSearchForm\Form\FilterFieldset;
use Omeka\Api;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as ElementManager;

class LetterForm extends Form
{
	/** @var Api\Manager */
	protected $api;
	/** @var ElementManager */
	private $formElementManager;

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
		$this->add($this->penpalField());
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
		$inputFilter->get('penpal')->setRequired(false);

		return $inputFilter;
	}

	public function setApiManager(Api\Manager $apiManager)
	{
		$this->api = $apiManager;
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
				'label' => 'Written after', // @translate
			],
			'attributes' => [
				'placeholder' => 'YYYY-MM-DD', // @translate
			],
		]);

		$fieldset->add([
			'name' => 'to',
			'type' => Element\Date::class,
			'options' => [
				'label' => 'Written before', // @translate
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

	/**
	 * 
	 * @return array Config of the select field enabling to choose the penpal.
	 */
	protected function penpalField()
	{
		return [
			'name' => 'penpal',
			'type' => Element\Select::class,
			'attributes' => [
                'class' => 'chosen-select',
				'data-placeholder' => 'Name of Poincaré’s penpal', // @translate
			],
			'options' => [
				'label' => 'Penpal', // @translate
				'empty_option' => '',
				'value_options' => $this->getPenpalOptions(),
			],
		];
	}

	/**
	 * @return array Liste des correspondants, indexés par leur nom.
	 */
	protected function getPenpalOptions()
	{
		$itemSetId = $this->getOption('search_page')->settings()['form']['penpals_itemset'];

		/* @var $items Api\Representation\ItemRepresentation[] */
		$items = $this->api->search('items', [
			'is_public' => true,
			'item_set_id' => $itemSetId,
		])->getContent();
		$options = [];
		foreach ($items as $item) {
			$options[$item->displayTitle()] = $item->displayTitle();
		}

		return $options;
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
