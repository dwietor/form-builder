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

use DWietor\Bundle\CmsFormBundle\Entity\CmsForm;
use Oro\Bundle\LayoutBundle\Annotation\Layout;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FormController extends Controller
{
    /**
     * @Route("/preview/{uuid}", name="d_wietor_cms_form_frontend_form_preview")
     * @Layout
     * @param CmsForm $form
     * @return array
     */
    public function formViewAction(CmsForm $form)
    {
        return [
            'data' => [
                'entity' => $form
            ]
        ];
    }
}
