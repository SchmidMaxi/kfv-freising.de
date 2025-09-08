<?php
return [
    'ctrl' => [
        'title' => 'Rolle',
        'label' => 'title',
        'tstamp' => 'tstamp', 'crdate' => 'crdate', 'cruser_id' => 'cruser_id',
        'delete' => 'deleted', 'hideTable' => false, 'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile' => 'EXT:feuerwehren/Resources/Public/Icons/rolle.svg',
    ],
    'columns' => [
        'hidden' => ['config' => ['type' => 'check']],
        'title' => ['label' => 'Titel', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim,required']],
    ],
    'types' => ['0' => ['showitem' => 'title, --div--;Access, hidden']],
];