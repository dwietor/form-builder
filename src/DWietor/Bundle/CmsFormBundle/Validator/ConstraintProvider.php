<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Validator;

use DWietor\Bundle\CmsFormBundle\Entity\CmsForm;
use DWietor\Bundle\CmsFormBundle\Event\ConstraintBuild;
use DWietor\Bundle\CmsFormBundle\Validator\Config\FormConstraintCollection;
use DWietor\Bundle\CmsFormBundle\Validator\Loader\ValidationRuleLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConstraintProvider implements ConstraintProviderInterface
{
    /** @var ValidationRuleLoader */
    protected $ruleLoader;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param ValidationRuleLoader     $ruleLoader
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ValidationRuleLoader $ruleLoader, EventDispatcherInterface $eventDispatcher)
    {
        $this->ruleLoader = $ruleLoader;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintsForForm(CmsForm $form): FormConstraintCollection
    {
        // @todo caching here instead of rule loader?
        $collection = new FormConstraintCollection($form);
        $configuration = $this->ruleLoader->getForForm($form->getAlias());

        if (array_key_exists('fields', $configuration)) {
            foreach ($configuration['fields'] as $fieldName => $fieldConfig) {
                foreach ($fieldConfig as $constraint) {
                    $constraintClass = key($constraint);
                    $constraintOptions = $constraint[$constraintClass];
                    $collection->addConstraintForField($fieldName, $constraintClass, $constraintOptions);
                }
            }
        }

        foreach ($form->getFields() as $field) {
            $options = $field->getOptions();
            if (array_key_exists('required', $options) && $options['required'] === true) {
                $collection->addConstraintForField($field->getName(), NotBlank::class);
            }
        }

        $event = new ConstraintBuild($collection, $form);
        $this->eventDispatcher->dispatch(ConstraintBuild::NAME, $event);

        return $event->getConstraintCollection();
    }
}
