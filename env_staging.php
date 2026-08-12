<?php
  $variables = [
    'TYPO3_CONTEXT' => 'Production/Staging',
    'applicationContext' => 'Production/Staging',
    'dbname' => 'kfv_t3_13_stg',
    'host' =>  'ix4c.your-database.de',
    'user' => 'kfv_t3_13_stg',
    'password' => 'wjCHvPprK9tfJqhn',
  ];

  foreach ($variables as $key => $value) {
      putenv("$key=$value");
  }
?>
