<?php
defined('TYPO3') or die();

call_user_func(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Schmid\IcsImporter\Task\ImportWithCategoryTask::class] = [
        'extension'        => 'icsimporter',
        'title'            => 'ICS Import (Calendarize) mit Kategorie',
        'description'      => 'Importiert einen ICS-Feed in Calendarize und weist eine sys_category zu.',
        'additionalFields' => \Schmid\IcsImporter\Task\ImportWithCategoryAdditionalFieldProvider::class,
    ];
});
