<?php
namespace EhpSearchForm\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;
use Omeka\Form\Element\ResourceSelect;
use EhpSearchForm\Form\FilterFieldset;
use Omeka\Api;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as ElementManager;

class BiblioForm extends Form
{
	/** @var Api\Manager */
	protected $api;
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
		$this->add($this->typeField());
		$this->add($this->journalField());
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
		$inputFilter->get('type')->setRequired(false);
		$inputFilter->get('journal')->setRequired(false);

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

	/**
	 * 
	 * @return array Config of the select field enabling to choose the document
	 * type (article, book…).
	 */
	protected function typeField()
	{
		$options=[];
		foreach ($this->settings()['resource_classes'] as $configOption) {
			$configOptionArray = explode('→', $configOption);
			$options[$configOptionArray[0]]=$configOptionArray[1];
		}
		return [
			'name' => 'type',
			'type' => Element\Select::class,
			'attributes' => [
                'class' => 'chosen-select',
				'data-placeholder' => 'Document type', // @translate
			],
			'options' => [
				'label' => 'Document type', // @translate
				'empty_option' => '',
				'value_options' => $options,
			],
		];
	}

	/**
	 * To look for texts which were published in a specified journal.
	 * @return array Config of the select field enabling to choose a journal in
	 * which the Poincaré’s text was published.
	 */
	protected function journalField()
	{
		$itemSetId = $this->settings()['journal']['journals_itemset'];
		return [
			'type' => ResourceSelect::class,
			'name' => 'journal',
			'attributes' => [
                'class' => 'chosen-select',
				'data-placeholder' => 'Journal name', // @translate
			],
			'options' => [
				'label' => 'Published in', // @translate
				'empty_option' => '',
				'resource_value_options' => [
					'resource' => 'items',
					'query' => ['is_public' => true, 'item_set_id' => $itemSetId],
					'option_text_callback' => function (Api\Representation\ItemRepresentation $item) {
						return $item->displayTitle();
					},
				],
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
