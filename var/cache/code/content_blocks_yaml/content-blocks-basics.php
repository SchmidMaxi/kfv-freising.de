<?php
return array (
  'TYPO3/Links' => 
  array (
    'identifier' => 'TYPO3/Links',
    'fields' => 
    array (
      0 => 
      array (
        'identifier' => 'links_palette',
        'type' => 'Palette',
        'fields' => 
        array (
          0 => 
          array (
            'identifier' => 'sectionIndex',
            'useExistingField' => true,
          ),
          1 => 
          array (
            'identifier' => 'linkToTop',
            'useExistingField' => true,
          ),
        ),
      ),
    ),
    'hostExtension' => 'content_blocks',
  ),
  'TYPO3/Appearance' => 
  array (
    'identifier' => 'TYPO3/Appearance',
    'fields' => 
    array (
      0 => 
      array (
        'identifier' => 'appearance_tab',
        'type' => 'Tab',
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance',
      ),
      1 => 
      array (
        'identifier' => 'frames_palette',
        'type' => 'Palette',
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames',
        'fields' => 
        array (
          0 => 
          array (
            'identifier' => 'layout',
            'useExistingField' => true,
          ),
          1 => 
          array (
            'identifier' => 'frame_class',
            'useExistingField' => true,
          ),
          2 => 
          array (
            'identifier' => 'space_before_class',
            'useExistingField' => true,
          ),
          3 => 
          array (
            'identifier' => 'space_after_class',
            'useExistingField' => true,
          ),
        ),
      ),
    ),
    'hostExtension' => 'content_blocks',
  ),
  'TYPO3/Header' => 
  array (
    'identifier' => 'TYPO3/Header',
    'fields' => 
    array (
      0 => 
      array (
        'identifier' => 'header_palette',
        'type' => 'Palette',
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header',
        'fields' => 
        array (
          0 => 
          array (
            'identifier' => 'header',
            'useExistingField' => true,
          ),
          1 => 
          array (
            'type' => 'Linebreak',
          ),
          2 => 
          array (
            'identifier' => 'header_layout',
            'useExistingField' => true,
          ),
          3 => 
          array (
            'identifier' => 'header_position',
            'useExistingField' => true,
          ),
        ),
      ),
    ),
    'hostExtension' => 'content_blocks',
  ),
  'TYPO3/Categories' => 
  array (
    'identifier' => 'TYPO3/Categories',
    'fields' => 
    array (
      0 => 
      array (
        'identifier' => 'categories_tab',
        'type' => 'Tab',
        'label' => 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories',
      ),
      1 => 
      array (
        'identifier' => 'categories',
        'useExistingField' => true,
      ),
    ),
    'hostExtension' => 'content_blocks',
  ),
);
#