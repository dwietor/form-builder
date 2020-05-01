<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Validator;

use DWietor\Bundle\CmsFormBundle\Entity\CmsForm;
use DWietor\Bundle\CmsFormBundle\Validator\Config\FormConstraintCollection;

interface ConstraintProviderInterface
{
    /**
     * @param CmsForm $form
     * @return FormConstraintCollection
     */
    public function getConstraintsForForm(CmsForm $form): FormConstraintCollection;
}
