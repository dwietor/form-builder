<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Form\Type;

use DWietor\Bundle\CmsFormBundle\Entity\CmsFormField;
use DWietor\Bundle\CmsFormBundle\Provider\FieldTypeRegistry;
use Oro\Bundle\FormBundle\Form\Type\Select2ChoiceType;
use Oro\Bundle\RedirectBundle\Form\Type\SlugType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class FieldType extends AbstractType
{
    /** @var FieldTypeRegistry */
    protected $fieldTypeRegistry;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param FieldTypeRegistry   $fieldTypeRegistry
     * @param TranslatorInterface $translator
     */
    public function __construct(FieldTypeRegistry $fieldTypeRegistry, TranslatorInterface $translator)
    {
        $this->fieldTypeRegistry = $fieldTypeRegistry;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'label',
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'dwietor.cmsform.cmsformfield.label.label',
                ]
            )
            ->add(
                'name',
                SlugType::class,
                [
                    'required'     => true,
                    'label'        => 'dwietor.cmsform.cmsformfield.name.label',
                    'tooltip'      => 'dwietor.cmsform.cmsformfield.name.tooltip',
                    'source_field' => 'label',
                ]
            );

        $typesChoices = [];
        foreach ($this->fieldTypeRegistry->getAvailableTypes() as $type) {
            $label = $this->translator->trans(sprintf('dwietor.cmsform.field_type.%s.label', $type->getName()));
            $typesChoices[$label] = $type->getName();
        }

        $builder
            ->add(
                'type',
                Select2ChoiceType::class,
                [
                    'required'    => true,
                    'label'       => 'dwietor.cmsform.cmsformfield.type.label',
                    'choices'     => $typesChoices,
                    'placeholder' => 'dwietor.cmsform.cmsformfield.type.placeholder'
                ]
            );

        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        /** @var CmsFormField $cmsField */
        $cmsField = $event->getData();

        if ($cmsField->getLabel() !== null) {
            $cmsField->addOption('label', $cmsField->getLabel());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => CmsFormField::class
            ]
        );
    }
}
