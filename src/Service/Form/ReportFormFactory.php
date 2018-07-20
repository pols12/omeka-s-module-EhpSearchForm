<?php
namespace EhpSearchForm\Service\Form;

use EhpSearchForm\Form\ReportForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ReportFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new ReportForm(null, $options);
        $form->setFormElementManager($services->get('FormElementManager'));

        return $form;
    }
}
