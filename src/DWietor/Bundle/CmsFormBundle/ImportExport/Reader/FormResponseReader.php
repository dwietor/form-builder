<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\ImportExport\Reader;

use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Reader\EntityReader;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class FormResponseReader extends EntityReader
{
    /** @var int */
    protected $formId;

    /**
     * {@inheritdoc}
     */
    protected function createSourceEntityQueryBuilder($entityName, Organization $organization = null, array $ids = [])
    {
        $qb = parent::createSourceEntityQueryBuilder($entityName, $organization);

        if ($this->formId > 0) {
            $aliases = $qb->getRootAliases();
            $rootAlias = reset($aliases);
            $qb
                ->andWhere(
                    $qb->expr()->eq(sprintf('IDENTITY(%s.form)', $rootAlias), ':form')
                )
                ->setParameter('form', $this->formId);
        }

        $this->formId = null;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeFromContext(ContextInterface $context)
    {
        $this->formId = (int)$context->getOption('form_id');

        parent::initializeFromContext($context);
    }
}
