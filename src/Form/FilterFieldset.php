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

class FilterFieldset extends Fieldset
{
    public function init()
    {
        $this->setAttributes([
            'class' => 'filter',
        ]);

        $this->add([
            'type' => Element\Select::class,
            'name' => 'field',
            'options' => [
                'value_options' => $this->getFieldOptions(),
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'value',
        ]);
    }

    protected function sortByWeight($fields, $settings) {
        uksort($fields, function($a, $b) use ($settings) {
            $aWeight = $settings[$a]['weight'];
            $bWeight = $settings[$b]['weight'];
            return $aWeight - $bWeight;
        });
        return $fields;
    }

	/**
	 * 
	 * @return array 
	 */
    protected function getFieldOptions()
    {
        $searchPage = $this->getOption('search_page');
        $searchIndex = $searchPage->index();
        $availableFields = $searchIndex->adapter()->getAvailableFields($searchIndex);
        $settings = $searchPage->settings();
        $formSettings = $settings['form'];

        $options = [];
        foreach ($formSettings['advanced-fields'] as $name => $field) {
            if ($field['enabled'] && isset($availableFields[$name])) {
                if (isset($field['display']['label']) && $field['display']['label']) {
                    $label = $field['display']['label'];
                } elseif (isset($availableFields[$name]['label']) && $availableFields[$name]['label']) {
                    $label = $availableFields[$name]['label'];
                } else {
                    $label = $name;
                }
                $options[$name] = $label;
            }
        }

        $options = $this->sortByWeight($options, $formSettings['advanced-fields']);

        return $options;
    }
}
