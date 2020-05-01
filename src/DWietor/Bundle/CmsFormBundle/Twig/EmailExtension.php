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

use DWietor\Bundle\CmsFormBundle\Entity\CmsFormResponse;

class EmailExtension extends \Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('d_wietor_form_response_array', [$this, 'getResponse'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param CmsFormResponse $formResponse
     * @return array
     */
    public function getResponse(CmsFormResponse $formResponse): array
    {
        return $formResponse->toArray();
    }
}
