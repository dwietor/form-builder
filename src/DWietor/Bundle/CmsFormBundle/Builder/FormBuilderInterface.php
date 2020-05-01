<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Builder;

use DWietor\Bundle\CmsFormBundle\Exception\CmsFormNotFound;
use Symfony\Component\Form\FormInterface;

interface FormBuilderInterface
{
    /**
     * @param string $alias
     * @param array  $options
     * @throws CmsFormNotFound
     * @return mixed
     */
    public function getForm(string $alias, array $options = []): FormInterface;
}
