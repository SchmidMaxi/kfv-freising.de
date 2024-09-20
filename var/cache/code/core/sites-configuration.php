<?php
return array (
  'kfv' => 
  array (
    'base' => 'https://www.kfv-freising.de/',
    'baseVariants' => 
    array (
      0 => 
      array (
        'base' => 'https://staging.kfv-freising.de/',
        'condition' => 'applicationContext == "Production/Staging"',
      ),
      1 => 
      array (
        'base' => 'https://kfv-freising.ddev.site/',
        'condition' => 'applicationContext == "Development"',
      ),
    ),
    'languages' => 
    array (
      0 => 
      array (
        'title' => 'Deutsch',
        'enabled' => true,
        'languageId' => 0,
        'base' => '/',
        'locale' => 'de_DE',
        'navigationTitle' => 'Deutsch',
        'flag' => 'de',
        'hreflang' => '',
        'websiteTitle' => '',
      ),
    ),
    'rootPageId' => 1,
    'websiteTitle' => '',
  ),
);
#