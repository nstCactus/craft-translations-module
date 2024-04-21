<?php

namespace nstcactus\craftcms\modules\translations\i18n;

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
     */
    protected function loadMessagesFromDb($category, $language): array
    {;
        $mainQuery = (new Query())->select(['message' => 't1.handle', 'translation' => 't2.translation'])
            ->from(['t1' => $this->sourceMessageTable])
            ->leftJoin(['t2' => $this->messageTable], 't1.id = t2.translationItemId')
            ->where([
                't2.siteId' => \Craft::$app->getSites()->getCurrentSite()->id ?? -1,
            ]);

        $messages = $mainQuery->createCommand($this->db)->queryAll();

        return ArrayHelper::map($messages, 'message', 'translation');
    }
}
