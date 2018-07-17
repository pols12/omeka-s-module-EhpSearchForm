<?php
namespace EhpSearchForm\Service\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use EhpSearchForm\Form\FilterFieldset;

class FilterFieldsetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $fieldset = new FilterFieldset(null, $options);

        return $fieldset;
    }
}
