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

namespace EhpSearchForm\FormAdapter;

use Search\Query;
use Search\FormAdapter\FormAdapterInterface;

class LetterFormAdapter implements FormAdapterInterface
{
	public function getLabel()
	{
		return 'Letter';
	}

	public function getFormClass()
	{
		return \EhpSearchForm\Form\LetterForm::class;
	}

	public function getFormPartial()
	{
		return 'ehp-search-form/letter-form';
	}

	public function getConfigFormClass()
	{
		return \EhpSearchForm\Form\LetterFormConfigFieldset::class;
	}

	/**
	 * Called by {@see \Search\Controller\IndexController::searchAction} (l.77)
	 * @see \Solr\Querier::query()
	 * @param array $data Submitted data.
	 * @param array $formSettings Search page config from admin interface.
	 * @return Query
	 */
	public function toQuery(array $data, array $formSettings)
	{
		$query = new Query();

		if (isset($formSettings['is_public_field'])) {
			$query->addFilter($formSettings['is_public_field'], true);
		}

		if (isset($data['q'])) {
			$query->setQuery($data['q']);
			$query->addFilter($formSettings['itemset']['item_set_id_field'],
					$formSettings['itemset']['letters_itemset']);
		}

		if (isset($data['date']['from']) && !empty($data['date']['from'])
				|| isset($data['date']['to']) && !empty($data['date']['to'])) {
			if(!isset($formSettings['date_range_field']))
				throw new \Exception('Date range field is not defined in configuration page.');
			$field = $formSettings['date_range_field'];
			$start = (new \DateTime($data['date']['from']))->format("Y-m-d\TH:i:s.z\Z");
			$end = (new \DateTime($data['date']['to']))->format("Y-m-d\TH:i:s.z\Z");
			if ($start || $end) {
				$query->addDateRangeFilter($field, $start, $end);
			}
		}

		//Query edit to search for penpal in both writer and addressee fields
		if(isset($data['penpal'])) {
			$queryWithPenpal = $query->getQuery()=='' ? '*:*' : $query->getQuery();
			$queryWithPenpal .=
					' AND (dm2e_writer_txt_fr:"'.$data['penpal']
					.'" OR gndo_addressee_txt:"'.$data['penpal'].'")';
			$query->setQuery($queryWithPenpal);
		}

		if (isset($data['text']['filters'])) {
			foreach ($data['text']['filters'] as $filter) {
				if (!empty($filter['value'])) {
					$query->addFilter($filter['field'], $filter['value']);
				}
			}
		}

		return $query;
	}
}
