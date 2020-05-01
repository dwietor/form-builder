<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\ImportExport\Configuration;

use DWietor\Bundle\CmsFormBundle\Entity\CmsFormResponse;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfiguration;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationInterface;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationProviderInterface;

class FormResponseImportExportConfigurationProvider implements ImportExportConfigurationProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function get(): ImportExportConfigurationInterface
    {
        return new ImportExportConfiguration([
            ImportExportConfiguration::FIELD_ENTITY_CLASS => CmsFormResponse::class,
            ImportExportConfiguration::FIELD_EXPORT_PROCESSOR_ALIAS => 'd_wietor_cms_form_response',
            ImportExportConfiguration::FIELD_EXPORT_JOB_NAME => 'd_wietor_cms_form_responses_export_to_csv',
        ]);
    }
}
