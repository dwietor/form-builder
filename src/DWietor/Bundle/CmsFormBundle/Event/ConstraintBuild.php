<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Event;

use DWietor\Bundle\CmsFormBundle\Entity\CmsForm;
use DWietor\Bundle\CmsFormBundle\Validator\Config\FormConstraintCollection;
use Symfony\Component\EventDispatcher\Event;

class ConstraintBuild extends Event
{
    public const NAME = 'd_wietor_cms_form.constraints.build';

    /** @var FormConstraintCollection */
    protected $constraintCollection;

    /** @var CmsForm */
    protected $form;

    /**
     * @param FormConstraintCollection $constraintCollection
     * @param CmsForm                  $form
     */
    public function __construct(FormConstraintCollection $constraintCollection, CmsForm $form)
    {
        $this->constraintCollection = $constraintCollection;
        $this->form = $form;
    }

    /**
     * @return FormConstraintCollection
     */
    public function getConstraintCollection(): FormConstraintCollection
    {
        return $this->constraintCollection;
    }

    /**
     * @return CmsForm
     */
    public function getForm(): CmsForm
    {
        return $this->form;
    }
}
