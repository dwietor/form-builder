<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Migrations\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Version 1_0 doesn't need to be present. Left just for BC.
 */
class CreateTables implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        return;
    }

    /**
     * Create d_wietor_cms_form table
     *
     * @param Schema $schema
     */
    protected function createDWietorCmsFormTable(Schema $schema)
    {
        $table = $schema->createTable('d_wietor_cms_form');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create d_wietor_cms_form_field table
     *
     * @param Schema $schema
     */
    protected function createDWietorCmsFormFieldTable(Schema $schema)
    {
        $table = $schema->createTable('d_wietor_cms_form_field');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('form_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('order', 'smallint', []);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['form_id'], 'idx_c32e75ca5ff69b7d', []);
    }

    /**
     * Add d_wietor_cms_form_field foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDWietorCmsFormFieldForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('d_wietor_cms_form_field');
        $table->addForeignKeyConstraint(
            $schema->getTable('d_wietor_cms_form'),
            ['form_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }
}
