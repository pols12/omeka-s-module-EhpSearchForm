<?php
namespace EhpSearchForm\Service\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use EhpSearchForm\Form\LetterFormConfigFieldset;

class LetterFormConfigFieldsetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new LetterFormConfigFieldset(null, $options);

        return $form;
    }
}
