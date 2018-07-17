<?php
namespace EhpSearchForm\Service\Form;

use EhpSearchForm\Form\LetterForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class LetterFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new LetterForm(null, $options);
        $form->setApiManager($services->get('Omeka\ApiManager'));
        $form->setFormElementManager($services->get('FormElementManager'));

        return $form;
    }
}
