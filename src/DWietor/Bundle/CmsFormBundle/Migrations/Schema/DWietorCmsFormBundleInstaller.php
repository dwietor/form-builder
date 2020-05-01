<?php

/*
 * This file is part of the DWietor CMS Form Builder.
 *
 * (c) David Wietor <davidwietor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWietor\Bundle\CmsFormBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @todo indexes, notnull, onDelete, etc.!
 */
class DWietorCmsFormBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_1';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createDWietorCmsFormResponseTable($schema);
        $this->createDWietorCmsFieldResponseTable($schema);
        $this->createDWietorCmsFormFieldTable($schema);
        $this->createDWietorCmsFormNotificationTable($schema);
        $this->createDWietorCmsFormTable($schema);

        /** Foreign keys generation **/
        $this->addDWietorCmsFormResponseForeignKeys($schema);
        $this->addDWietorCmsFieldResponseForeignKeys($schema);
        $this->addDWietorCmsFormFieldForeignKeys($schema);
        $this->addDWietorCmsFormNotificationForeignKeys($schema);
    }

    /**
     * Create d_wietor_cms_form_response table
     *
     * @param Schema $schema
     */
    protected function createDWietorCmsFormResponseTable(Schema $schema)
    {
        $table = $schema->createTable('d_wietor_cms_form_response');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('form_id', 'integer', []);
        $table->addColumn('visitor_id', 'integer', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('is_resolved', 'boolean', ['notnull' => false]);
        $table->addColumn('serialized_data', 'array', ['notnull' => false, 'comment' => '(DC2Type:array)']);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['visitor_id'], 'idx_eab5a5270bee6d', []);
        $table->addIndex(['form_id'], 'idx_eab5a525ff69b7d', []);
    }

    /**
     * Create d_wietor_cms_field_response table
     *
     * @param Schema $schema
     */
    protected function createDWietorCmsFieldResponseTable(Schema $schema)
    {
        $table = $schema->createTable('d_wietor_cms_field_response');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('field_id', 'integer', []);
        $table->addColumn('form_response_id', 'integer', []);
        $table->addColumn('value', 'text', ['notnull' => false]);
        $table->addColumn('serialized_data', 'array', ['notnull' => false, 'comment' => '(DC2Type:array)']);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['form_response_id'], 'idx_3679f827c98b851', []);
        $table->addIndex(['field_id'], 'idx_3679f827443707b0', []);
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
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('sort_order', 'smallint', []);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('options', 'array', ['notnull' => false, 'comment' => '(DC2Type:array)']);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('serialized_data', 'array', ['notnull' => false, 'comment' => '(DC2Type:array)']);
        $table->addIndex(['form_id'], 'idx_c32e75ca5ff69b7d', []);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['form_id', 'name'], 'uidx_d_wietor_field_form_name');
    }

    /**
     * Create d_wietor_cms_form_notification table
     *
     * @param Schema $schema
     */
    protected function createDWietorCmsFormNotificationTable(Schema $schema)
    {
        $table = $schema->createTable('d_wietor_cms_form_notification');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('form_id', 'integer', []);
        $table->addColumn('template_id', 'integer', ['notnull' => false]);
        $table->addColumn('email', 'string', ['notnull' => false, 'length' => 255]);
        $table->addIndex(['form_id'], 'idx_45a891715ff69b7d', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['template_id'], 'idx_45a891715da0fb8', []);
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
        $table->addColumn('alias', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('serialized_data', 'array', ['notnull' => false, 'comment' => '(DC2Type:array)']);
        $table->addColumn('uuid', 'string', ['length' => 255]);
        $table->addColumn('preview_enabled', 'boolean', ['notnull' => false]);
        $table->addColumn('notifications_enabled', 'boolean', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['alias'], 'uniq_e042b376e16c6b94');
        $table->addUniqueIndex(['uuid'], 'uniq_e042b376d17f50a6');
    }

    /**
     * Add d_wietor_cms_form_response foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDWietorCmsFormResponseForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('d_wietor_cms_form_response');
        $table->addForeignKeyConstraint(
            $schema->getTable('d_wietor_cms_form'),
            ['form_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_customer_visitor'),
            ['visitor_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
    }

    /**
     * Add d_wietor_cms_field_response foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDWietorCmsFieldResponseForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('d_wietor_cms_field_response');
        $table->addForeignKeyConstraint(
            $schema->getTable('d_wietor_cms_form_field'),
            ['field_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('d_wietor_cms_form_response'),
            ['form_response_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
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

    /**
     * Add d_wietor_cms_form_notification foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDWietorCmsFormNotificationForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('d_wietor_cms_form_notification');
        $table->addForeignKeyConstraint(
            $schema->getTable('d_wietor_cms_form'),
            ['form_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_email_template'),
            ['template_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
    }
}
