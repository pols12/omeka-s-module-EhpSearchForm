<?php
namespace EhpSearchForm\Service\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use EhpSearchForm\Form\ReportFormConfigFieldset;

class ReportFormConfigFieldsetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ReportFormConfigFieldset(null, $options);
    }
}
