<?php

namespace DWietor\Bundle\CmsFormBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddResolvedField implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        if (!$schema->hasTable('d_wietor_cms_form_response')) {
            return;
        }

        $table = $schema->getTable('d_wietor_cms_form_response');

        $table->addColumn('is_resolved', 'boolean', ['notnull' => false]);

        $queries->addPostQuery('UPDATE d_wietor_cms_form_response SET is_resolved = false');
    }
}
