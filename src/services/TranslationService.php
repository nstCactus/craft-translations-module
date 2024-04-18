<?php

namespace nstcactus\craftcms\modules\translations\services;

use Craft;
use craft\base\Component;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use nstcactus\craftcms\modules\translations\records\TranslatableItemRecord;
use nstcactus\craftcms\modules\translations\records\TranslationRecord;
use RuntimeException;

class TranslationService extends Component
{
    public function saveTranslationItem(string $handle): TranslatableItemRecord
    {
        $record = TranslatableItemRecord::findOne(['handle' => $handle]);
        if ($record) {
            return $record;
        }

        $record = new TranslatableItemRecord();
        $record->handle = $handle;

        if (!$record->save()) {
            throw new \RuntimeException(sprintf(
                'Couldn\'t save translatable item "%s": %s',
                $handle,
                implode(', ', $record->getFirstErrors())
            ));
        }

        return $record;
    }

    public function saveTranslation(
        int $siteId,
        int $translationItemId,
        string $value,
        bool $propagate = false
    ): bool
    {
        $siteIds = $propagate ? Craft::$app->getSites()->getAllSiteIds(true) : [$siteId];
        foreach ($siteIds as $currentSiteId) {
            $record = TranslationRecord::findOne(['siteId' => $currentSiteId, 'translationItemId' => $translationItemId]);
            if (!$record) {
                $record = new TranslationRecord();
                $record->translationItemId = $translationItemId;
                $record->siteId = $currentSiteId;
            }

            $record->translation = $value;

            if (!$record->save()) {
                throw new \RuntimeException(sprintf(
                    'Couldn\'t save translation for item #%s on site #%s: %s',
                    $translationItemId,
                    $currentSiteId,
                    implode(', ', $record->getFirstErrors())
                ));
            }
        }

        return true;
    }
}
