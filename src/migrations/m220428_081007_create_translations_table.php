<?php

namespace nstcactus\craftcms\modules\translations\migrations;

use craft\db\Migration;
use craft\db\Table;
use nstcactus\craftcms\modules\translations\TranslationsModule;

/**
 * m220428_081007_create_translations_table migration.
 */
class m220428_081007_create_translations_table extends Migration
{
    protected TranslationsModule $module;

    public function init()
    {
        parent::init();

        $this->module = TranslationsModule::getInstance();
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable($this->module->translatableItemsTableName, [
            'id' => $this->primaryKey(),
            'handle' => $this->string(255),
            'dateCreated' => $this->dateTime(),
            'dateUpdated' => $this->dateTime(),
            'uid' => $this->uid(),
        ]);
        $this->createIndex(
            'idx_unique_handle',
            $this->module->translatableItemsTableName,
            'handle',
            true
        );

        $translationItemIdColumnName = 'translationItemId';
        $this->createTable($this->module->translationsTableName, [
            'id' => $this->primaryKey(),
            'siteId' => $this->integer(),
            $translationItemIdColumnName => $this->integer(),
            'translation' => $this->text(),
            'dateCreated' => $this->dateTime(),
            'dateUpdated' => $this->dateTime(),
            'uid' => $this->uid(),
        ]);

        $this->addForeignKey(
            'fk_siteId',
            $this->module->translationsTableName,
            'siteId',
            Table::SITES,
            'id',
            'cascade',
            'cascade',
        );
        $this->addForeignKey(
            'fk_' . $translationItemIdColumnName,
            $this->module->translationsTableName,
            $translationItemIdColumnName,
            $this->module->translatableItemsTableName,
            'id',
            'cascade',
            'cascade',
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->module->translationsTableName);
        $this->dropTable($this->module->translatableItemsTableName);
    }
}
