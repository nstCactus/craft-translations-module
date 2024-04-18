<?php

namespace nstcactus\craftcms\modules\translations\models;

use craft\db\ActiveRecord;

/**
 * @property int $translationItemId
 * @property string $siteId
 * @property string $translation
 *
 * @property TranslatableItem $id0
 */
class Translation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%frontend_translations}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['translationItemId', 'siteId'], 'required'],
            [['translationItemId'], 'integer'],
            [['translation'], 'string'],
            [['siteId'], 'integer'],
            [
                ['translationItemId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TranslatableItem::class,
                'targetAttribute' => ['translationItemId' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'translationItemId' => 'Translation item ID',
            'siteId' => 'Site ID',
            'translation' => 'Translation',
        ];
    }
}
