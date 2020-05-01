<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Controller\Frontend;

use DWietor\Bundle\CmsFormBundle\Builder\FormBuilderInterface;
use DWietor\Bundle\CmsFormBundle\Entity\CmsFieldResponse;
use DWietor\Bundle\CmsFormBundle\Entity\CmsForm;
use DWietor\Bundle\CmsFormBundle\Entity\CmsFormResponse;
use DWietor\Bundle\CmsFormBundle\Notification\NotificationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Component\HttpFoundation\Response;

class AjaxFormController extends Controller
{
    /**
     * @Route("/respond/{uuid}", name="d_wietor_cms_frontend_ajax_respond")
     * @AclAncestor("d_wietor_cms_frontend_form_respond")
     * @param Request $request
     * @param CmsForm $cmsForm
     * @return array|Response
     */
    public function respondAction(Request $request, CmsForm $cmsForm)
    {
        // build CmsFormType and map it to CmsFieldResponse
        // @todo daniel extract
        $form = $this->get(FormBuilderInterface::class)->getForm($cmsForm->getAlias());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fieldsSubmitted = $form->getData();
            $formResponse = new CmsFormResponse();
            $formResponse->setForm($cmsForm);

            foreach ($fieldsSubmitted as $fieldName => $fieldValue) {
                if ($cmsField = $cmsForm->getField($fieldName)) {
                    $fieldResponse = new CmsFieldResponse();
                    $value = null;
                    if (is_scalar($fieldValue)) {
                        $value = trim($fieldValue);
                    } elseif (is_array($fieldValue)) {
                        $value = json_encode($fieldValue);
                    }
                    $fieldResponse->setField($cmsField)->setValue($value);

                    $formResponse->addFieldResponse($fieldResponse);
                }
            }

            $manager = $this->get('doctrine')->getManagerForClass(CmsFormResponse::class);
            $manager->persist($formResponse);
            $manager->flush();

            // @todo extract
            $this->get(NotificationInterface::class)->process($formResponse);

            return new JsonResponse(['success' => true, 'message' => '@todo']);
        }

        $errors = [];
        foreach ($form->getErrors(true, true) as $formError) {
            $errors[$formError->getOrigin()->getName()] = $formError->getMessage();
        }

        return new JsonResponse([
            'success' => false,
            'errors'    => $errors
        ]);
    }
}
