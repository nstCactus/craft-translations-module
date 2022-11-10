<?php

namespace lhs\craftcms\modules\translations\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use craft\web\Controller;
use lhs\craftcms\modules\translations\records\TranslatableItemRecord;
use lhs\craftcms\modules\translations\TranslationsModule;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class CpController extends Controller
{
    public function actionIndex()
    {
        $sites = Craft::$app->getSites()->getEditableSites();
        /** @var TranslatableItemRecord[] $translatableItems */
        $translatableItems = TranslatableItemRecord::find()->with(['translations'])->all();

        $translations = [];
        foreach ($translatableItems as $translatableItem) {
            // Create an array containing, for each site, all known translations keys in any site
            foreach ($sites as $site) {
                $translations[$site->id][$translatableItem->handle] = [
                    'id' => null,
                    'value' => null,
                    'translatableItemHandle' => $translatableItem->handle,
                    'translatableItemId' => $translatableItem->id,
                ];
            }

            // Set the actual translations
            foreach ($translatableItem->translations as $translationRecord) {
                $translations[$translationRecord->siteId][$translatableItem->handle] = [
                    'id' => $translationRecord->id,
                    'value' => $translationRecord->translation,
                    'translatableItemHandle' => $translatableItem->handle,
                    'translatableItemId' => $translatableItem->id,
                ];
            }
        }

        return $this->renderTemplate('translations-module/index', [
            'translations' => $translations,
            'sites' => $sites,
            'tabs' => ArrayHelper::map($sites, 'handle', static fn($site) => [
                'url'   => "#$site->handle",
                'label' => $site->name,
            ]),
        ]);
    }

    /**
     * Edge cases:
     *   - Adding a new translation with a handle that already exists
     *   - Adding a new translation in several languages at once
     *   - Saving with an empty handle
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     */
    public function actionSave(): Response
    {
        $bodyParams = $this->request->getBodyParams();

        if (!isset($bodyParams['translations']) || !is_array($bodyParams['translations'])) {
            throw new BadRequestHttpException();
        }

        $translationService = TranslationsModule::getInstance()->getTranslation();

        foreach ($bodyParams['translations'] as $siteId => $siteTranslations) {
            foreach ($siteTranslations as $translatableItemId => $translation) {
                $isNew = $translatableItemId === 'new';
                ['value' => $value, 'handle' => $handle] = $translation;

                if ($isNew) {
                    // Ignore new translations fields if they're left empty, to avoid creating empty items in the db which would then cause trouble on subsequent saves
                    if ($handle === '' && $value === '') {
                        continue;
                    }

                    $translatableItemRecord = $translationService->saveTranslationItem($handle);
                } else {
                    // Delete existing items if their handle field is emptied
                    if ($handle === '') {
                        TranslatableItemRecord::findOne(['id' => $translatableItemId])->delete();
                        continue;
                    }

                    $translatableItemRecord = TranslatableItemRecord::findOne(['id' => $translatableItemId]);

                    // If no translation record is found, it's because it was just deleted in another language.
                    if (!$translatableItemRecord) {
                        continue;
                    }

                    if ($translatableItemRecord->handle !== $handle) {
                        // FIXME: new handle gets overridden back to its original value when saving other languages
                        // A possible solution is to structure input data as follow:
                        // translations[translatableItemId][handle] = "header.hello"
                        // translations[translatableItemId][translations][siteId] = "Hello"
                        $translatableItemRecord->handle = $handle;
                        $translatableItemRecord->save();
                    }
                }

                $translationService->saveTranslation(
                    $siteId,
                    $translatableItemRecord->id,
                    $value,
                    $isNew,
                );
            }
        }

        return $this->redirectToPostedUrl();
    }
}
