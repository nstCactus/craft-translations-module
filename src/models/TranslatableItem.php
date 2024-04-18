<?php

namespace nstcactus\craftcms\modules\translations\models;

use Craft;
use yii\db\ActiveQuery;
use craft\db\ActiveRecord;
use craft\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $handle
 * @property Translation[] $translations
 */
class TranslatableItem extends ActiveRecord
{
    public array $languages;

    public function __construct()
    {
        $this->languages = [];
        foreach (Craft::$app->i18n->getSiteLocales() as $language) {
            $this->languages[$language->id] = null;
        }
        parent::__construct();
    }

    public static function tableName(): string
    {
        return '{{%frontend_translatable_items}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['handle', 'string', 'max' => 255],
            ['languages', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'handle' => 'handle',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTranslations(): ActiveQuery
    {
        return $this->hasMany(Translation::class, ['translationItemId' => 'id']);
    }

    public function getTranslationsMap(): array
    {
        return ArrayHelper::map($this->translations, 'siteId', 'translation');
    }

    public function getTranslation($lang)
    {
        return $this->translationsMap[$lang] ?? null;
    }

    public function afterFind(): void
    {
        foreach ($this->languages as $siteId => $one) {
            $this->languages[$siteId] = $this->getTranslation($siteId);
        }
    }

    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            Translation::deleteAll(['translationItemId' => $this->id]);
            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if ($insert) {
            foreach ($this->languages as $siteId => $one) {
                $model = new Translation();
                $model->translationItemId = $this->id;
                $model->siteId = $siteId;
                if (empty($one)) {
                    $model->translation = null;
                } else {
                    $model->translation = $one;
                }
                $model->save();
            }
        } else {
            foreach ($this->translations as $translations) {
                $siteId = $translations->siteId;
                if (empty($this->languages[$siteId])) {
                    $translations->translation = null;
                } else {
                    $translations->translation = $this->languages[$siteId];
                }
                $translations->save();
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
