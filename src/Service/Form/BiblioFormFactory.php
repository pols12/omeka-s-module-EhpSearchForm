<?php
namespace EhpSearchForm\Service\Form;

use EhpSearchForm\Form\BiblioForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BiblioFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new BiblioForm(null, $options);
        $form->setApiManager($services->get('Omeka\ApiManager'));
        $form->setFormElementManager($services->get('FormElementManager'));

        return $form;
    }
}
