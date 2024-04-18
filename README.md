# Craft translations module

## Getting started

1. Install the module via composer: 
    
    ```shell
    composer require nstcactus/craft-translations-module
    ```

2. Load the module in the Craft `config/app.php` file:

    ````php
    <?php return [
       'modules' => [
         'translations-module' => [ 
           'class' => nstcactus\craftcms\modules\translations\TranslationsModule::class,
   
           // Optional properties
           'translationCategory' => 'dico',
           'translatableItemsTableName' => '{{%frontend_translatable_items}}',
           'translationsTableName' => '{{%frontend_translations}}',
         ],
       ],
   ];
    ````

3. Execute the migration to create the DB tables:

    ````shell
   ./craft migrate/up --track translations-module
    ````

4. Add some translations in the Craft control panel: `<craftUrl>/admin/translations`

5. Use translations in your templates:

    ````twig
    Voici une chaîne traduite : {{ 'app.translated.string'|t('dico') }}
    ````

## Roadmap

- change the database schema to be as close as possible to `\yii\i18n\DbMessageSource` 
  (ideally use it instead of `\nstcactus\craftcms\modules\translations\i18n\DbMessageSource`)
- refactor the module to link translations to a language (`<lang>_<country>`) instead of a site ID
- improve the UI:
  - make it easier to remove translations
  - add the ability to add several translations at once
- add the ability to import/export translations in CSV format

