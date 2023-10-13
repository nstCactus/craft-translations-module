<?php

namespace lhs\craftcms\modules\translations\i18n;

use craft\db\Query;
use craft\helpers\ArrayHelper;
use yii\db\Exception;
use yii\i18n\DbMessageSource as BaseDbMessageSource;

class DbMessageSource extends BaseDbMessageSource
{
    /**
     * Loads the messages from database.
     * @param string $category the message category.
     * @param string $language the target language.
     * @return array the messages loaded from database.
     * @throws Exception
     * TODO: Refactor this method (and the DB) to use $language instead of $siteId
     */
    protected function loadMessagesFromDb($category, $language): array
    {
        $sites = ArrayHelper::map(\Craft::$app->getSites()->getAllSites(true), 'language', 'id');

        $mainQuery = (new Query())->select(['message' => 't1.handle', 'translation' => 't2.translation'])
            ->from(['t1' => $this->sourceMessageTable])
            ->leftJoin(['t2' => $this->messageTable], 't1.id = t2.translationItemId')
            ->where([
                't2.siteId' => $sites[$language] ?? -1,
            ]);

        $messages = $mainQuery->createCommand($this->db)->queryAll();

        return ArrayHelper::map($messages, 'message', 'translation');
    }
}