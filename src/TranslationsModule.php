<?php

namespace lhs\craftcms\modules\translations;

use Craft;
use craft\console\controllers\MigrateController;
use craft\db\MigrationManager;
use craft\events\RegisterMigratorEvent;
use lhs\craftcms\modules\translations\i18n\DbMessageSource;
use lhs\craftcms\modules\translations\services\TranslationService;
use nstcactus\CraftUtils\AbstractModule;
use yii\base\Event;

/**
 * @property TranslationService $translation
 */
class TranslationsModule extends AbstractModule
{
    public const TRANSLATIONS_GLOBAL_SET_HANDLE = 'translations';
    public const TRANSLATION_CATEGORY = 'translations-module';

    public string $translationCategory = 'dico';
    public string $translatableItemsTableName = '{{%frontend_translatable_items}}';
    public string $translationsTableName = '{{%frontend_translations}}';

    protected const MIGRATION_TRACK = 'translations-module';
    protected const SERVICE_TRANSLATION = 'translation';

    public function init()
    {
        parent::init();

        $this->set(self::SERVICE_TRANSLATION, TranslationService::class);
        $this->registerMigrator();
        $this->registerDynamicTranslationCategory();
    }

    protected function getCpRoutes(): ?array
    {
        return [
            'translations' => 'translations-module/cp/index',
        ];
    }

    protected function getCpNavItems(): ?array
    {
        return [
            'translations' => [
                'url' => 'translations',
                'label' => Craft::t('translations-module', 'cp-nav.title'),
                'icon' => '@modules/translations-module/resources/translations-icon.svg',
            ],
        ];
    }

    public function getTranslation(): TranslationService
    {
        return $this->get(self::SERVICE_TRANSLATION);
    }

    protected function registerMigrator()
    {
        Event::on(
            MigrateController::class,
            MigrateController::EVENT_REGISTER_MIGRATOR,
            function (RegisterMigratorEvent $event) {
                if ($event->track === self::MIGRATION_TRACK) {
                    $event->migrator = Craft::createObject([
                        'class' => MigrationManager::class,
                        'track' => self::MIGRATION_TRACK,
                        'migrationNamespace' => __NAMESPACE__ . '\\migrations',
                        'migrationPath' => "@modules/$this->id/migrations",
                    ]);
                    $event->handled = true;
                }
            }
        );
    }

    /**
     * Register a translation category for the module.
     */
    protected function registerDynamicTranslationCategory(): void
    {
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$this->translationCategory]) && !isset($i18n->translations[$this->translationCategory . '*'])) {
            $i18n->translations[$this->translationCategory] = [
                'class'              => DbMessageSource::class,
                'sourceLanguage'     => 'en',
                'forceTranslation'   => true,
                'sourceMessageTable' => $this->translatableItemsTableName,
                'messageTable'       => $this->translationsTableName,
            ];
        }
    }
}
