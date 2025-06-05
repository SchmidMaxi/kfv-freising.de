<?php
defined('TYPO3') or die('Access denied.');

$additionalColumns = [
    'color' => [
        'exclude' => true,
        'label' => 'Hintergrundfarbe',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'label' => 'Weiß',
                    'value' => 'white',
                ],
                [
                    'label' => 'Dunkelgrau',
                    'value' => 'dark',
                ],
                [
                    'label' => 'Hellgrau',
                    'value' => 'light',
                ],
            ]
        ],
    ],
    'breakpoint_desktop' => [
        'exclude' => true,
        'label' => 'Desktop',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'default' => 3,
            'items' => [
                [
                    'label' => '1',
                    'value' => 1,
                ],
                [
                    'label' => '2',
                    'value' => 2,
                ],
                [
                    'label' => '3',
                    'value' => 3,
                ],
                [
                    'label' => '4',
                    'value' => 4,
                ],
                [
                    'label' => '5',
                    'value' => 5,
                ],
            ]
        ],
    ],
    'breakpoint_tablet' => [
        'exclude' => true,
        'label' => 'Tablet',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'default' => '2',
            'items' => [
                [
                    'label' => '1',
                    'value' => '1',
                ],
                [
                    'label' => '2',
                    'value' => '2',
                ],
                [
                    'label' => '3',
                    'value' => '3',
                ],
                [
                    'label' => '4',
                    'value' => '4',
                ],
                [
                    'label' => '5',
                    'value' => '5',
                ],
            ]
        ],
    ],
    'breakpoint_mobile' => [
        'exclude' => true,
        'label' => 'Mobil',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'default' => '1',
            'items' => [
                [
                    'label' => '1',
                    'value' => '1',
                ],
                [
                    'label' => '2',
                    'value' => '2',
                ],
                [
                    'label' => '3',
                    'value' => '3',
                ],
                [
                    'label' => '4',
                    'value' => '4',
                ],
                [
                    'label' => '5',
                    'value' => '5',
                ],
            ]
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $additionalColumns);
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'section', 'color', 'before:image');

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
  (
    new \B13\Container\Tca\ContainerConfiguration(
      'section', // CType
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:section_title', // label
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:section_description', // description
      [
        [
          ['name' => 'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:section.content', 'colPos' => 200],
        ]
      ] // grid configuration
    )
  )
  ->setSaveAndCloseInNewContentElementWizard(false)
  ->setIcon('EXT:container/Resources/Public/Icons/container-1col.svg')
);
$GLOBALS['TCA']['tt_content']['types']['section']['showitem'] = '
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
       --palette--;;general,
       --palette--;;headers,
       color,
       image;Hintergrundbild,
   --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
       --palette--;;frames,
       --palette--;;appearanceLinks,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
       --palette--;;language,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
       --palette--;;hidden,
       --palette--;;access,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
       categories,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
       rowDescription,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';

// Two Column Container
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
  (
    new \B13\Container\Tca\ContainerConfiguration(
      '2cols', // CType
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:twoColumn_title', // label
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:twoColumn_description', // description
      [
        [
          ['name' => '1. Spalte', 'colPos' => 200],
          ['name' => '2. Spalte', 'colPos' => 201]
        ]
      ] // grid configuration
    )
  )
  ->setSaveAndCloseInNewContentElementWizard(false)
  ->setIcon('EXT:container/Resources/Public/Icons/container-2col.svg')
);
// Add Flexform to Container
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
  '*',
  'FILE:EXT:sitepackage/Configuration/FlexForms/container_2col.xml',
  '2cols'
);
$GLOBALS['TCA']['tt_content']['types']['2cols']['showitem'] = '
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
       --palette--;;general,
       --palette--;;headers,pi_flexform;Grid Einstellungen,
   --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
       --palette--;;frames,
       --palette--;;appearanceLinks,
       media;Background,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
       --palette--;;language,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
       --palette--;;hidden,
       --palette--;;access,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
       categories,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
       rowDescription,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';


// Three Column Container
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
  (
    new \B13\Container\Tca\ContainerConfiguration(
      '3cols', // CType
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:threeColumn_title', // label
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:threeColumn_description', // description
      [
        [
          ['name' => '1. Spalte', 'colPos' => 200],
          ['name' => '2. Spalte', 'colPos' => 201],
          ['name' => '3. Spalte', 'colPos' => 202]
        ]
      ] // grid configuration
    )
  )
  ->setSaveAndCloseInNewContentElementWizard(false)
  ->setIcon('EXT:container/Resources/Public/Icons/container-3col.svg')
);
// Add Flexform to Container
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
  '*',
  'FILE:EXT:sitepackage/Configuration/FlexForms/container_3col.xml',
  '3cols'
);
$GLOBALS['TCA']['tt_content']['types']['3cols']['showitem'] = '
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
       --palette--;;general,
       --palette--;;headers,pi_flexform;Grid Einstellungen,
   --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
       --palette--;;frames,
       --palette--;;appearanceLinks,
       media;Background,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
       --palette--;;language,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
       --palette--;;hidden,
       --palette--;;access,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
       categories,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
       rowDescription,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';


// Four Column Container
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
  (
    new \B13\Container\Tca\ContainerConfiguration(
      '4cols', // CType
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:fourColumn_title', // label
      'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:fourColumn_description', // description
      [
        [
          ['name' => '1. Spalte', 'colPos' => 200],
          ['name' => '2. Spalte', 'colPos' => 201],
          ['name' => '3. Spalte', 'colPos' => 202],
          ['name' => '4. Spalte', 'colPos' => 203]
        ]
      ] // grid configuration
    )
  )
  ->setSaveAndCloseInNewContentElementWizard(false)
  ->setIcon('EXT:container/Resources/Public/Icons/container-4col.svg')
);
// Add Flexform to Container
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
  '*',
  'FILE:EXT:sitepackage/Configuration/FlexForms/container_4col.xml',
  '4cols'
);
$GLOBALS['TCA']['tt_content']['types']['4cols']['showitem'] = '
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
       --palette--;;general,
       --palette--;;headers,pi_flexform;Grid Einstellungen,
   --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
       --palette--;;frames,
       --palette--;;appearanceLinks,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
       --palette--;;language,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
       --palette--;;hidden,
       --palette--;;access,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
       categories,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
       rowDescription,
   --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';
