<?php

namespace nstcactus\craftcms\modules\translations\records;

use yii\db\ActiveQuery;
use craft\db\ActiveRecord;
use nstcactus\craftcms\modules\translations\TranslationsModule;

/**
 * @property int $id
 * @property int $siteId
 * @property int $translationItemId
 * @property string $translation
 */
class TranslationRecord extends ActiveRecord
{
    public static function tableName()
    {
        return TranslationsModule::getInstance()->translationsTableName;
    }

    public function getTranslatableItem(): ActiveQuery
    {
        return $this->hasOne(TranslatableItemRecord::class, ['id' => 'translationItemId']);
    }
}
