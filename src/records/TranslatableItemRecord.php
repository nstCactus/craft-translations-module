<?php

namespace nstcactus\craftcms\modules\translations\records;

use craft\db\ActiveRecord;
use nstcactus\craftcms\modules\translations\TranslationsModule;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property string $handle
 * @property TranslationRecord[] $translations
 */
class TranslatableItemRecord extends ActiveRecord
{
    public static function tableName()
    {
        return TranslationsModule::getInstance()->translatableItemsTableName;
    }

    public function getTranslations(): ActiveQuery
    {
        return $this->hasMany(TranslationRecord::class, [
            'translationItemId' => 'id',
        ]);
    }
}
