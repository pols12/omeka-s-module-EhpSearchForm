<?php
namespace EhpSearchForm\Service\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use EhpSearchForm\Form\BiblioFormConfigFieldset;

class BiblioFormConfigFieldsetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new BiblioFormConfigFieldset(null, $options);
		$form->setApi($services->get('Omeka\ApiManager'));

        return $form;
    }
}
