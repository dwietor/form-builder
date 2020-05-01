<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Twig;

use DWietor\Bundle\CmsFormBundle\Builder\FormBuilderInterface;
use DWietor\Bundle\CmsFormBundle\Provider\GeneralFieldProvider;
use Symfony\Component\Form\FormRendererInterface;

class FormExtension extends \Twig_Extension
{
    /** @var FormBuilderInterface */
    protected $formBuilder;

    /** @var FormRendererInterface */
    protected $formRenderer;

    /** @var GeneralFieldProvider */
    protected $generalFieldProvider;

    /**
     * @param FormBuilderInterface  $formBuilder
     * @param FormRendererInterface $formRenderer
     * @param GeneralFieldProvider  $generalFieldProvider
     */
    public function __construct(
        FormBuilderInterface $formBuilder,
        FormRendererInterface $formRenderer,
        GeneralFieldProvider $generalFieldProvider
    ) {
        $this->formBuilder = $formBuilder;
        $this->formRenderer = $formRenderer;
        $this->generalFieldProvider = $generalFieldProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('d_wietor_form', [$this, 'renderForm'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('d_wietor_form_updateable_fields', [$this, 'getUpdateableFields']),
        ];
    }

    /**
     * @param string      $alias
     * @param string|null $actionUrl
     * @return string
     */
    public function renderForm(string $alias, ?string $actionUrl = null)
    {
        $options = [];
        if ($actionUrl) {
            $options['action'] = $actionUrl;
        }

        $formView = $this->formBuilder->getForm($alias, $options)->createView();
        // @todo evaluate this approach
        $this->formRenderer->setTheme($formView, 'DWietorCmsFormBundle:layouts:blank/cms_form.html.twig');

        // @todo evaluate this approach
        return $this->formRenderer->renderBlock($formView, 'cms_form_widget');
    }

    /**
     * @return array
     */
    public function getUpdateableFields(): array
    {
        return $this->generalFieldProvider->getUpdateableFields();
    }
}
