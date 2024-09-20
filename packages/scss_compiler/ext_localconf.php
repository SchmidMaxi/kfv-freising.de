<?php
defined('TYPO3') || die();


// Register css processing parser
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/scss-compiler/css']['parser'][\Trion\ScssCompiler\Parser\ScssParser::class] =
    Trion\ScssCompiler\Parser\ScssParser::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][\Trion\ScssCompiler\Hooks\PageRenderer\PreProcessHook::class]
    = \Trion\ScssCompiler\Hooks\PageRenderer\PreProcessHook::class . '->execute';

