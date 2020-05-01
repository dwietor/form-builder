<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Notification;

use DWietor\Bundle\CmsFormBundle\Entity\CmsFormResponse;

interface NotificationInterface
{
    /**
     * @param CmsFormResponse $formResponse
     * @param array           $context
     *
     * @return mixed
     */
    public function process(CmsFormResponse $formResponse, array $context = []);
}
