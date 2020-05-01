<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Form\Extension;

use DWietor\Bundle\CmsFormBundle\Entity\CmsFormField;
use DWietor\Bundle\CmsFormBundle\Form\Type\FieldType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

class FieldOptionsExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FieldType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addGeneralOptionsToForm($builder, $options);

        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $this->incrementSortOrder($event);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function addGeneralOptionsToForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'required',
                CheckboxType::class,
                [
                    'required'      => false,
                    'label'         => 'dwietor.cmsform.cmsformfield.options.required.label',
                    'property_path' => 'options[required]'
                ]
            )
            // attr options below
            ->add(
                'placeholder',
                TextType::class,
                [
                    'required'      => false,
                    'label'         => 'dwietor.cmsform.cmsformfield.options.placeholder.label',
                    'tooltip'       => 'dwietor.cmsform.cmsformfield.options.placeholder.tooltip',
                    'property_path' => 'options[attr][placeholder]'
                ]
            )
            ->add(
                'css_class',
                TextType::class,
                [
                    'required'      => false,
                    'label'         => 'dwietor.cmsform.cmsformfield.options.css_class.label',
                    'tooltip'       => 'dwietor.cmsform.cmsformfield.options.css_class.tooltip',
                    'property_path' => 'options[attr][class]'
                ]
            )
            ->add(
                'size',
                ChoiceType::class,
                [
                    'required'      => true,
                    'label'         => 'dwietor.cmsform.cmsformfield.options.size.label',
                    'tooltip'       => 'dwietor.cmsform.cmsformfield.options.size.tooltip',
                    'choices'       => [
                        'dwietor.cmsform.cmsformfield.options.size.choices.small'  => 'small',
                        'dwietor.cmsform.cmsformfield.options.size.choices.medium' => 'medium',
                        'dwietor.cmsform.cmsformfield.options.size.choices.large'  => 'large',
                    ],
                    'property_path' => 'options[attr][data-size]',
                    'constraints'   => [new NotBlank()],
                ]
            );
    }

    /**
     * @param FormEvent $event
     */
    protected function incrementSortOrder(FormEvent $event): void
    {
        /** @var CmsFormField $cmsField */
        $cmsField = $event->getData();
        if ($cmsField->getSortOrder() === null) {
            $cmsField->incrementSortOrder();
        }
    }
}
