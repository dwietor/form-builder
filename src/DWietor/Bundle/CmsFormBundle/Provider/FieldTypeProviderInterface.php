<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Provider;

use DWietor\Bundle\CmsFormBundle\ValueObject\CmsFieldType;

interface FieldTypeProviderInterface
{
    /**
     * @return CmsFieldType[]
     */
    public function getAvailableTypes(): array;
}
