<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Controller;

use DWietor\Bundle\CmsFormBundle\Builder\FormBuilderInterface;
use DWietor\Bundle\CmsFormBundle\Entity\CmsForm;
use DWietor\Bundle\CmsFormBundle\Entity\CmsFormField;
use DWietor\Bundle\CmsFormBundle\Form\Type\FieldType;
use DWietor\Bundle\CmsFormBundle\Provider\GeneralFieldProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Component\HttpFoundation\Response;

class AjaxFormController extends Controller
{
    /**
     * @Route("/form-view}", name="d_wietor_cms_form_frontend_ajax_form_view")
     * @AclAncestor("d_wietor_cms_form_field_create")
     * @Template
     *
     * @param Request $request
     * @return array
     */
    public function formViewAction(Request $request)
    {
        $form = $this->createForm(FieldType::class, new CmsFormField());
        $form->handleRequest($request);

        $formView = $form->createView();
        $this->get(GeneralFieldProvider::class)->manipulate($formView);

        return [
            'form' => $formView,
        ];
    }

    /**
     * @Route("/form-preview}", name="d_wietor_cms_form_frontend_ajax_field_preview")
     * @AclAncestor("d_wietor_cms_form_field_create")
     * @Template
     * @param Request $request
     * @return array
     */
    public function fieldPreviewAction(Request $request)
    {
        $cmsField =  new CmsFormField();
        $form = $this->createForm(FieldType::class, $cmsField);
        $form->handleRequest($request);

        return [
            'form' => $this->get(FormBuilderInterface::class)->buildField($cmsField)->createView(),
            'entity' => $cmsField
        ];
    }

    /**
     * @Route("/{id}/reorder", name="d_wietor_cms_form_ajax_reorder")
     * @AclAncestor("d_wietor_cms_form_create")
     * @param Request $request
     * @param CmsForm $cmsForm
     * @return array|Response
     */
    public function reorderAction(Request $request, CmsForm $cmsForm)
    {
        $data = $request->request->get('cms_form_reorder');
        if (!array_key_exists('fields', $data)) {
            return new JsonResponse(['success' => false]);
        }

        foreach ($data['fields'] as $fieldName => $sortOrder) {
            if (!$cmsForm->getField($fieldName) || !array_key_exists('sortOrder', $sortOrder)) {
                continue;
            }

            $cmsForm->getField($fieldName)->setSortOrder((int)$sortOrder['sortOrder']);
        }

        $manager = $this->get('doctrine')->getManagerForClass(CmsForm::class);
        $manager->persist($cmsForm);
        $manager->flush();

        return new JsonResponse(['success' => true]);
    }
}
