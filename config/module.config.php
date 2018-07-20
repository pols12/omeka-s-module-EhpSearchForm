<?php

namespace EhpSearchForm;
return [
    'form_elements' => [
        'factories' => [
			Form\LetterForm::class => Service\Form\LetterFormFactory::class,
			Form\BiblioForm::class => Service\Form\BiblioFormFactory::class,
			Form\ReportForm::class => Service\Form\ReportFormFactory::class,
			Form\FilterFieldset::class => Service\Form\FilterFieldsetFactory::class,
			Form\LetterFormConfigFieldset::class => Service\Form\LetterFormConfigFieldsetFactory::class,
			Form\BiblioFormConfigFieldset::class => Service\Form\BiblioFormConfigFieldsetFactory::class,
			Form\ReportFormConfigFieldset::class => Service\Form\ReportFormConfigFieldsetFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'search_form_adapters' => [
        'invokables' => [
            'letter' => FormAdapter\LetterFormAdapter::class,
            'biblio' => FormAdapter\BiblioFormAdapter::class,
            'report' => FormAdapter\ReportFormAdapter::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'        => 'gettext',
                'base_dir'    => __DIR__ . '/../language',
                'pattern'     => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
];
