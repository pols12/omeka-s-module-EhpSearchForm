<?php

/*
 * Copyright Pols12 for LHSP-AHP, 2018
 * Based on PslSearchForm, copyright BibLibre, 2016
 *
 * PslSearchForm is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */

namespace EhpSearchForm\Form;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Omeka\Form\Element\ResourceSelect;
use Omeka\Api\Representation\ItemSetRepresentation;
use Omeka\Api;

/**
 * Formulaire de configuration de la recherche
 */
class BiblioFormConfigFieldset extends Fieldset
{
	protected $api;
	
	public  function setApi(Api\Manager $api) {
		$this->api = $api;
	}
	
	public function init()
    {
        $this->add($this->getAdvancedFieldsFieldset());
		
		$this->add($this->itemSetFieldset());

		// Pour choisir le champ qui contient la date
		// (donc à dupliquer s’il y a plusieurs dates)
        $this->add([
            'name' => 'date_range_field',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Date range field', // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => 'None', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
		
		$this->add($this->journalFieldset());

		$this->classFields();		
    }

    protected function getAdvancedFieldsFieldset()
    {
        $advancedFieldsFieldset = new Fieldset('advanced-fields');
        $advancedFieldsFieldset->setLabel('Advanced search fields'); // @translate
        $advancedFieldsFieldset->setAttribute('data-sortable', '1');

        $fields = $this->getAvailableFields();
        $weights = range(0, count($fields));
        $weight_options = array_combine($weights, $weights);
        $weight = 0;
        foreach ($fields as $field) {
            $fieldset = new Fieldset($field['name']);
            $fieldset->setLabel($this->getFieldLabel($field));

            $displayFieldset = new Fieldset('display');
            $displayFieldset->add([
                'name' => 'label',
                'type' => Element\Text::class,
                'options' => [
                    'label' => 'Label', // @translate
                ],
            ]);
            $fieldset->add($displayFieldset);

            $fieldset->add([
                'name' => 'enabled',
                'type' => Element\Checkbox::class,
                'options' => [
                    'label' => 'Enabled', // @translate
                ],
            ]);

            $fieldset->add([
                'name' => 'weight',
                'type' => Element\Select::class,
                'options' => [
                    'label' => 'Weight',
                    'value_options' => $weight_options,
                ],
                'attributes' => [
                    'value' => $weight++,
                ],
            ]);

            $advancedFieldsFieldset->add($fieldset);
        }

        return $advancedFieldsFieldset;
    }

    protected function getAvailableFields()
    {
		/* @var $searchPage \Search\Api\Representation\SearchPageRepresentation */
		$searchPage = $this->getOption('search_page');
		/* @var $searchAdapter \Solr\Adapter */
		$searchAdapter = $searchPage->index()->adapter();
        return $searchAdapter->getAvailableFields($searchPage->index());
    }

    protected function getFieldsOptions()
    {
        $options = [];
        foreach ($this->getAvailableFields() as $name => $field) {
            if (isset($field['label'])) {
                $options[$name] = sprintf('%s (%s)', $field['label'], $name);
            } else {
                $options[$name] = $name;
            }
        }
        return $options;
    }

    protected function getFieldLabel($field)
    {
        $searchPage = $this->getOption('search_page');
        $settings = $searchPage->settings();

        $name = $field['name'];
        $label = isset($field['label']) ? $field['label'] : null;
        if (isset($settings['form']['advanced-fields'][$name])) {
            $fieldSettings = $settings['form']['advanced-fields'][$name];

            if (isset($fieldSettings['display']['label'])
                && $fieldSettings['display']['label'])
            {
                $label = $fieldSettings['display']['label'];
            }
        }
        $label = $label ? sprintf('%s (%s)', $label, $field['name']) : $field['name'];

        return $label;
    }

	private function itemSetFieldset() {
		$fieldset = new Fieldset('itemset');
		
		// Pour choisir l’item set qui contient les lettres
		$this->add([
			'type' => ResourceSelect::class,
			'name' => 'biblio_itemset',
			'attributes' => [
                'class' => 'chosen-select',
				'data-placeholder' => 'Select an item set', // @translate
				'required' => true,
			],
			'options' => [
				'label' => 'Publications item set', // @translate
				'info' => 'Item set which contains texts written by Poincaré', // @translate
				'empty_option' => '',
				'resource_value_options' => [
					'resource' => 'item_sets',
					'query' => [],
					'option_text_callback' => function (ItemSetRepresentation $itemset) {
						return $itemset->displayTitle();
					},
				],
			],
		]);
		$fieldset->add($this->get('biblio_itemset'));
		$this->remove('biblio_itemset');
		
		// Pour choisir le champ qui contient les id des item sets
        $fieldset->add([
            'name' => 'item_set_id_field',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Item set id field', // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => 'None', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
		
		return $fieldset;
	}
	/**
	 * 
	 * @return Fieldset Fields to filter search results by journal.
	 */
	private function journalFieldset() {
		$fieldset = new Fieldset('journal');
		
		//Field containing journal id for article indexing
		$fieldset->add([
			'name' => 'journal_id_field',
			'type' => Element\Select::class,
            'options' => [
                'label' => 'Journal id field', // @translate
                'info' => 'Field which index Journal’s id from Article items.', // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => 'None', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
		]);
		
		// Pour choisir l’item set qui contient les journaux
		$this->add([
			'type' => ResourceSelect::class,
			'name' => 'journals_itemset',
			'attributes' => [
                'class' => 'chosen-select',
				'data-placeholder' => 'Select an item set', // @translate
				'required' => true,
			],
			'options' => [
				'label' => 'Journals item set', // @translate
				'info' => 'Item set which contains journals in which Poincaré has published', // @translate
				'empty_option' => '',
				'resource_value_options' => [
					'resource' => 'item_sets',
					'query' => [],
					'option_text_callback' => function (ItemSetRepresentation $itemset) {
						return $itemset->displayTitle();
					},
				],
			],
		]);
        $fieldset->add($this->get('journals_itemset'));
		$this->remove('journals_itemset');
		
		return $fieldset;
	}

	/**
	 * Adds fields to let choose several resource classes.
	 */
	private function classFields() {
		// Pour choisir les ResourceClass qui peuvent être choisies lors de la recherche
        $this->add([
            'name' => 'resource_classes',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Searchable document types', // @translate
				'info' => 'Possible resource classes for searched items.', // @translate
                'value_options' => $this->resourceClassSelectOptions(),
                'empty_option' => '',
            ],
            'attributes' => [
                'class' => 'chosen-select',
				'data-placeholder' => 'Select resource classes', // @translate
				'multiple' => true,
				'required' => true,
			],
        ]);
		
		// Pour choisir le champ qui indexe les ResourceClass
        $this->add([
            'name' => 'class_field',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Resource class field', // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => 'None', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
	}
	
	/**
	 * @return array Resource class list, indexed by their label.
	 */
	protected function resourceClassSelectOptions()
	{
		/* @var $resourceClasses Api\Representation\ResourceClassRepresentation[] */
		$resourceClasses = $this->api->search('resource_classes')->getContent();
		$options = [];
		foreach ($resourceClasses as $resourceClass) {
			$options[$resourceClass->term().'→'.$resourceClass->localName()]
					= $resourceClass->localName();
		}

		return $options;
	}

}
