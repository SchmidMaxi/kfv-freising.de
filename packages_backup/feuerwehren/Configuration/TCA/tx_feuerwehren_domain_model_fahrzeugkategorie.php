<?php
return [
    'ctrl' => [
        'title' => 'Fahrzeugkategorie', 'label' => 'title',
        'tstamp' => 'tstamp','crdate' => 'crdate','cruser_id' => 'cruser_id',
        'delete' => 'deleted','enablecolumns' => ['disabled' => 'hidden'],
        'iconfile' => 'EXT:feuerwehren/Resources/Public/Icons/fahrzeug.svg',
    ],
    'columns' => [
        'hidden' => ['config' => ['type' => 'check']],
        'title' => ['label' => 'Titel', 'config' => ['type' => 'input', 'eval' => 'trim,required']],
    ],
    'types' => ['0' => ['showitem' => 'title, --div--;Access, hidden']],
];