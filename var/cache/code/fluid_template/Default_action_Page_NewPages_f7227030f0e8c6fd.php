<?php
class Default_action_Page_NewPages_f7227030f0e8c6fd extends \TYPO3Fluid\Fluid\Core\Compiler\AbstractCompiledTemplate {
    public function getLayoutName(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
        return (string)'Module';
    }
    public function hasLayout() {
        return true;
    }
    public function addCompiledNamespaces(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
        $renderingContext->getViewHelperResolver()->addNamespaces(array (
  'core' => 
  array (
    0 => 'TYPO3\\CMS\\Core\\ViewHelpers',
  ),
  'f' => 
  array (
    0 => 'TYPO3Fluid\\Fluid\\ViewHelpers',
    1 => 'TYPO3\\CMS\\Fluid\\ViewHelpers',
  ),
  'formvh' => 
  array (
    0 => 'TYPO3\\CMS\\Form\\ViewHelpers',
  ),
  'cb' => 
  array (
    0 => 'TYPO3\\CMS\\ContentBlocks\\ViewHelpers',
  ),
  'be' => 
  array (
    0 => 'TYPO3\\CMS\\Backend\\ViewHelpers',
  ),
));
    }
    /**
 * section Content
 */
public function section_26298499e77d870c(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output0 = '';

$output0 .= '

    ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Be\PageRendererViewHelper
$renderChildrenClosure2 = function() use ($renderingContext) {
return NULL;
};

$array3 = [
'0' => '@typo3/backend/context-menu.js',
'1' => '@typo3/backend/new-multiple-pages.js',
];

$arguments1 = [
'pageTitle' => '',
'includeCssFiles' => NULL,
'includeJsFiles' => NULL,
'addJsInlineLabels' => NULL,
'includeRequireJsModules' => NULL,
'addInlineSettings' => NULL,
'includeJavaScriptModules' => $array3,
];

$output0 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\Be\PageRendererViewHelper::renderStatic($arguments1, $renderChildrenClosure2, $renderingContext)]);

$output0 .= '

    <h1>
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure5 = function() use ($renderingContext) {
return NULL;
};

$arguments4 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:title',
];

$output0 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments4, $renderChildrenClosure5, $renderingContext)]);

$output0 .= '
    </h1>

    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array7 = [
'0' => '!',
'1' => $renderingContext->getVariableProvider()->getByPath('canCreateNew'),
];

$expression8 = function($context) {return !(TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node1"]));};

$arguments6 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression8(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array7)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output9 = '';

$output9 .= '
            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper
$renderChildrenClosure11 = function() use ($renderingContext) {
return NULL;
};
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure13 = function() use ($renderingContext) {
return NULL;
};

$arguments12 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:canNotCreateNew.title',
];
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure15 = function() use ($renderingContext) {
return NULL;
};

$arguments14 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:canNotCreateNew.message',
];

$arguments10 = [
'iconName' => NULL,
'disableIcon' => false,
'title' => TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments12, $renderChildrenClosure13, $renderingContext),
'message' => call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments14, $renderChildrenClosure15, $renderingContext)]),
'state' => -1,
];
$renderChildrenClosure11 = ($arguments10['message'] !== null) ? function() use ($arguments10) { return $arguments10['message']; } : $renderChildrenClosure11;
$output9 .= TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::renderStatic($arguments10, $renderChildrenClosure11, $renderingContext);

$output9 .= '
        ';
return $output9;
},
'__else' => function() use ($renderingContext) {
$output16 = '';

$output16 .= '
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array18 = [
'0' => $renderingContext->getVariableProvider()->getByPath('hasNewPagesData'),
];

$expression19 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments17 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression19(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array18)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output20 = '';

$output20 .= '
                    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array22 = [
'0' => $renderingContext->getVariableProvider()->getByPath('pagesCreated'),
];

$expression23 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments21 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression23(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array22)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output24 = '';

$output24 .= '
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper
$renderChildrenClosure26 = function() use ($renderingContext) {
return NULL;
};
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure28 = function() use ($renderingContext) {
return NULL;
};

$arguments27 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:pagesCreated.title',
];
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure30 = function() use ($renderingContext) {
return NULL;
};

$arguments29 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:pagesCreated.message',
];

$arguments25 = [
'iconName' => NULL,
'disableIcon' => false,
'title' => TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments27, $renderChildrenClosure28, $renderingContext),
'message' => call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments29, $renderChildrenClosure30, $renderingContext)]),
'state' => -1,
];
$renderChildrenClosure26 = ($arguments25['message'] !== null) ? function() use ($arguments25) { return $arguments25['message']; } : $renderChildrenClosure26;
$output24 .= TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::renderStatic($arguments25, $renderChildrenClosure26, $renderingContext);

$output24 .= '
                        ';
return $output24;
},
'__else' => function() use ($renderingContext) {
$output31 = '';

$output31 .= '
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper
$renderChildrenClosure33 = function() use ($renderingContext) {
return NULL;
};
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure35 = function() use ($renderingContext) {
return NULL;
};

$arguments34 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:noPagesCreated.title',
];
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure37 = function() use ($renderingContext) {
return NULL;
};

$arguments36 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:noPagesCreated.message',
];

$arguments32 = [
'iconName' => NULL,
'disableIcon' => false,
'title' => TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments34, $renderChildrenClosure35, $renderingContext),
'message' => call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments36, $renderChildrenClosure37, $renderingContext)]),
'state' => 1,
];
$renderChildrenClosure33 = ($arguments32['message'] !== null) ? function() use ($arguments32) { return $arguments32['message']; } : $renderChildrenClosure33;
$output31 .= TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::renderStatic($arguments32, $renderChildrenClosure33, $renderingContext);

$output31 .= '
                        ';
return $output31;
},
];

$output20 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments21, static fn() => '', $renderingContext)
;

$output20 .= '

                    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array39 = [
'0' => $renderingContext->getVariableProvider()->getByPath('visiblePages'),
];

$expression40 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments38 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression40(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array39)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output41 = '';

$output41 .= '
                        <h2 class="mt-4">
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure43 = function() use ($renderingContext) {
return NULL;
};

$arguments42 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:newSubPages',
];

$output41 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments42, $renderChildrenClosure43, $renderingContext)]);

$output41 .= '
                        </h2>
                        <div class="table-fit">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure45 = function() use ($renderingContext) {
return NULL;
};

$arguments44 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:newSubPages.pageTitle',
];

$output41 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments44, $renderChildrenClosure45, $renderingContext)]);

$output41 .= '</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure47 = function() use ($renderingContext) {
$output48 = '';

$output48 .= '
                                        <tr>
                                            <td>
                                                <span class="treeline-icon treeline-icon-join';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array50 = [
'0' => $renderingContext->getVariableProvider()->getByPath('pageIterator.isLast'),
];

$expression51 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments49 = [
'then' => 'bottom',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression51(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array50)),
    $renderingContext
),
];

$output48 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments49, static fn() => '', $renderingContext)
;

$output48 .= '"></span>
                                                <span title="id=';

$output48 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('page.uid')]);

$output48 .= '">';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\SpacelessViewHelper
$renderChildrenClosure53 = function() use ($renderingContext) {
$output54 = '';

$output54 .= '
                                                    <button type="button"
                                                        class="btn btn-link p-0"
                                                        data-contextmenu-trigger="click"
                                                        data-contextmenu-table="pages"
                                                        data-contextmenu-uid="';

$output54 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('page.uid')]);

$output54 .= '"
                                                        aria-label="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure56 = function() use ($renderingContext) {
return NULL;
};

$arguments55 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.contextMenu.open',
];

$output54 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments55, $renderChildrenClosure56, $renderingContext)]);

$output54 .= '"
                                                    >
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconForRecordViewHelper
$renderChildrenClosure58 = function() use ($renderingContext) {
return NULL;
};

$arguments57 = [
'size' => 'small',
'alternativeMarkupIdentifier' => NULL,
'table' => 'pages',
'row' => $renderingContext->getVariableProvider()->getByPath('page'),
];

$output54 .= TYPO3\CMS\Core\ViewHelpers\IconForRecordViewHelper::renderStatic($arguments57, $renderChildrenClosure58, $renderingContext);

$output54 .= '
                                                    </button>
                                                    ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Format\CropViewHelper
$renderChildrenClosure60 = function() use ($renderingContext) {
return call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('page.title')]);
};

$arguments59 = [
'append' => '&hellip;',
'respectWordBoundaries' => true,
'respectHtml' => true,
'maxCharacters' => $renderingContext->getVariableProvider()->getByPath('maxTitleLength'),
];

$output54 .= TYPO3\CMS\Fluid\ViewHelpers\Format\CropViewHelper::renderStatic($arguments59, $renderChildrenClosure60, $renderingContext);

$output54 .= '
                                                ';
return $output54;
};

$arguments52 = [
];

$output48 .= TYPO3Fluid\Fluid\ViewHelpers\SpacelessViewHelper::renderStatic($arguments52, $renderChildrenClosure53, $renderingContext);

$output48 .= '</span>
                                            </td>
                                            <td class="col-control">
                                                <div class="btn-group">
                                                    ';
// Rendering ViewHelper TYPO3\CMS\Backend\ViewHelpers\Link\EditRecordViewHelper
$renderChildrenClosure62 = function() use ($renderingContext) {
$output65 = '';

$output65 .= '
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure67 = function() use ($renderingContext) {
return NULL;
};

$arguments66 = [
'size' => 'small',
'overlay' => NULL,
'state' => 'default',
'alternativeMarkupIdentifier' => NULL,
'title' => NULL,
'identifier' => 'actions-page-open',
];

$output65 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments66, $renderChildrenClosure67, $renderingContext);

$output65 .= '
                                                    ';
return $output65;
};
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure64 = function() use ($renderingContext) {
return NULL;
};

$arguments63 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:core/Resources/Private/Language/locallang_mod_web_list.xlf:edit',
];

$arguments61 = [
'additionalAttributes' => NULL,
'data' => NULL,
'aria' => NULL,
'dir' => NULL,
'id' => NULL,
'lang' => NULL,
'style' => NULL,
'accesskey' => NULL,
'tabindex' => NULL,
'onclick' => NULL,
'fields' => NULL,
'returnUrl' => '',
'table' => 'pages',
'uid' => $renderingContext->getVariableProvider()->getByPath('page.uid'),
'title' => TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments63, $renderChildrenClosure64, $renderingContext),
'class' => 'btn btn-default',
];

$output48 .= TYPO3\CMS\Backend\ViewHelpers\Link\EditRecordViewHelper::renderStatic($arguments61, $renderChildrenClosure62, $renderingContext);

$output48 .= '
                                                </div>
                                            </td>
                                        </tr>
                                    ';
return $output48;
};

$arguments46 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('visiblePages'),
'as' => 'page',
'iteration' => 'pageIterator',
];

$output41 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments46, $renderChildrenClosure47, $renderingContext);

$output41 .= '
                                </tbody>
                            </table>
                        </div>
                    ';
return $output41;
},
];

$output20 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments38, static fn() => '', $renderingContext)
;

$output20 .= '
                ';
return $output20;
},
'__else' => function() use ($renderingContext) {
$output68 = '';

$output68 .= '
                    <div class="t3js-newmultiplepages-newlinetemplate" style="display:none">
                        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure70 = function() use ($renderingContext) {
return NULL;
};

$array72 = [
'index' => '[0]',
'label' => '[1]',
'pageTypes' => $renderingContext->getVariableProvider()->getByPath('pageTypes'),
];

$array71 = [
'line' => $array72,
];

$arguments69 = [
'partial' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'debug' => true,
'section' => 'FormLine',
'arguments' => $array71,
];

$output68 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments69, $renderChildrenClosure70, $renderingContext);

$output68 .= '
                    </div>

                    <form action="';
// Rendering ViewHelper TYPO3\CMS\Backend\ViewHelpers\ModuleLinkViewHelper
$renderChildrenClosure74 = function() use ($renderingContext) {
return NULL;
};

$array75 = [
'id' => $renderingContext->getVariableProvider()->getByPath('pageUid'),
];

$arguments73 = [
'query' => NULL,
'currentUrlParameterName' => NULL,
'route' => 'pages_new',
'arguments' => $array75,
];

$output68 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Backend\ViewHelpers\ModuleLinkViewHelper::renderStatic($arguments73, $renderChildrenClosure74, $renderingContext)]);

$output68 .= '" method="post">
                        <div class="form-group t3js-newmultiplepages-container">
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure77 = function() use ($renderingContext) {
return NULL;
};

$array79 = [
'index' => 0,
'label' => 1,
'pageTypes' => $renderingContext->getVariableProvider()->getByPath('pageTypes'),
];

$array78 = [
'line' => $array79,
];

$arguments76 = [
'partial' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'debug' => true,
'section' => 'FormLine',
'arguments' => $array78,
];

$output68 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments76, $renderChildrenClosure77, $renderingContext);

$output68 .= '
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure81 = function() use ($renderingContext) {
return NULL;
};

$array83 = [
'index' => 1,
'label' => 2,
'pageTypes' => $renderingContext->getVariableProvider()->getByPath('pageTypes'),
];

$array82 = [
'line' => $array83,
];

$arguments80 = [
'partial' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'debug' => true,
'section' => 'FormLine',
'arguments' => $array82,
];

$output68 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments80, $renderChildrenClosure81, $renderingContext);

$output68 .= '
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure85 = function() use ($renderingContext) {
return NULL;
};

$array87 = [
'index' => 2,
'label' => 3,
'pageTypes' => $renderingContext->getVariableProvider()->getByPath('pageTypes'),
];

$array86 = [
'line' => $array87,
];

$arguments84 = [
'partial' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'debug' => true,
'section' => 'FormLine',
'arguments' => $array86,
];

$output68 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments84, $renderChildrenClosure85, $renderingContext);

$output68 .= '
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure89 = function() use ($renderingContext) {
return NULL;
};

$array91 = [
'index' => 3,
'label' => 4,
'pageTypes' => $renderingContext->getVariableProvider()->getByPath('pageTypes'),
];

$array90 = [
'line' => $array91,
];

$arguments88 = [
'partial' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'debug' => true,
'section' => 'FormLine',
'arguments' => $array90,
];

$output68 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments88, $renderChildrenClosure89, $renderingContext);

$output68 .= '
                            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure93 = function() use ($renderingContext) {
return NULL;
};

$array95 = [
'index' => 4,
'label' => 5,
'pageTypes' => $renderingContext->getVariableProvider()->getByPath('pageTypes'),
];

$array94 = [
'line' => $array95,
];

$arguments92 = [
'partial' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'debug' => true,
'section' => 'FormLine',
'arguments' => $array94,
];

$output68 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments92, $renderChildrenClosure93, $renderingContext);

$output68 .= '
                        </div>

                        <div class="form-group">
                            <input
                                class="btn btn-default t3js-newmultiplepages-createnewfields"
                                type="button"
                                value="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure97 = function() use ($renderingContext) {
return NULL;
};

$arguments96 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:addMoreLines',
];

$output68 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments96, $renderChildrenClosure97, $renderingContext)]);

$output68 .= '"
                            />
                        </div>

                        <div class="form-group">
                            <div class="formengine-field-item">
                                <div class="form-wizards-wrap">
                                    <div class="form-wizards-element">
                                        <div class="form-check form-check-type-icon-toggle">
                                            <input type="checkbox" class="form-check-input" name="createInListEnd" id="createInListEnd" value="1" />
                                            <label for="createInListEnd" class="form-check-label">
                                                <span class="form-check-label-icon">
                                                    <span class="form-check-label-icon-checked">
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure99 = function() use ($renderingContext) {
return NULL;
};

$arguments98 = [
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'actions-check',
'size' => 'small',
'alternativeMarkupIdentifier' => 'inline',
];

$output68 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments98, $renderChildrenClosure99, $renderingContext);

$output68 .= '
                                                    </span>
                                                    <span class="form-check-label-icon-unchecked">
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure101 = function() use ($renderingContext) {
return NULL;
};

$arguments100 = [
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'miscellaneous-placeholder',
'size' => 'small',
'alternativeMarkupIdentifier' => 'inline',
];

$output68 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments100, $renderChildrenClosure101, $renderingContext);

$output68 .= '
                                                    </span>
                                                </span>
                                                ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure103 = function() use ($renderingContext) {
return NULL;
};

$arguments102 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:listEnd',
];

$output68 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments102, $renderChildrenClosure103, $renderingContext)]);

$output68 .= '
                                            </label>
                                        </div>
                                        <div class="form-check form-check-type-icon-toggle">
                                            <input type="checkbox" class="form-check-input" name="hidePages" id="hidePages" value="1" />
                                            <label for="hidePages" class="form-check-label">
                                                <span class="form-check-label-icon">
                                                    <span class="form-check-label-icon-checked">
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure105 = function() use ($renderingContext) {
return NULL;
};

$arguments104 = [
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'actions-check',
'size' => 'small',
'alternativeMarkupIdentifier' => 'inline',
];

$output68 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments104, $renderChildrenClosure105, $renderingContext);

$output68 .= '
                                                    </span>
                                                    <span class="form-check-label-icon-unchecked">
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure107 = function() use ($renderingContext) {
return NULL;
};

$arguments106 = [
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'miscellaneous-placeholder',
'size' => 'small',
'alternativeMarkupIdentifier' => 'inline',
];

$output68 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments106, $renderChildrenClosure107, $renderingContext);

$output68 .= '
                                                    </span>
                                                </span>
                                                ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure109 = function() use ($renderingContext) {
return NULL;
};

$arguments108 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:hidePages',
];

$output68 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments108, $renderChildrenClosure109, $renderingContext)]);

$output68 .= '
                                            </label>
                                        </div>
                                        <div class="form-check form-check-type-icon-toggle">
                                            <input type="checkbox" class="form-check-input" name="hidePagesInMenus" id="hidePagesInMenus" value="1" />
                                            <label for="hidePagesInMenus" class="form-check-label">
                                                <span class="form-check-label-icon">
                                                    <span class="form-check-label-icon-checked">
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure111 = function() use ($renderingContext) {
return NULL;
};

$arguments110 = [
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'actions-check',
'size' => 'small',
'alternativeMarkupIdentifier' => 'inline',
];

$output68 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments110, $renderChildrenClosure111, $renderingContext);

$output68 .= '
                                                    </span>
                                                    <span class="form-check-label-icon-unchecked">
                                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure113 = function() use ($renderingContext) {
return NULL;
};

$arguments112 = [
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'miscellaneous-placeholder',
'size' => 'small',
'alternativeMarkupIdentifier' => 'inline',
];

$output68 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments112, $renderChildrenClosure113, $renderingContext);

$output68 .= '
                                                    </span>
                                                </span>
                                                ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure115 = function() use ($renderingContext) {
return NULL;
};

$arguments114 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:hidePagesInMenus',
];

$output68 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments114, $renderChildrenClosure115, $renderingContext)]);

$output68 .= '
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input
                                class="btn btn-primary"
                                type="submit"
                                name="create"
                                value="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure117 = function() use ($renderingContext) {
return NULL;
};

$arguments116 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:create',
];

$output68 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments116, $renderChildrenClosure117, $renderingContext)]);

$output68 .= '"
                            />
                            <input
                                class="btn btn-default t3js-newmultiplepages-reset-fields"
                                type="reset"
                                value="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure119 = function() use ($renderingContext) {
return NULL;
};

$arguments118 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:reset',
];

$output68 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments118, $renderChildrenClosure119, $renderingContext)]);

$output68 .= '"
                            />
                        </div>
                    </form>
                ';
return $output68;
},
];

$output16 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments17, static fn() => '', $renderingContext)
;

$output16 .= '
        ';
return $output16;
},
];

$output0 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments6, static fn() => '', $renderingContext)
;

$output0 .= '
';

    return $output0;
}
/**
 * section FormLine
 */
public function section_03018a135b7b5690(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output120 = '';

$output120 .= '
    <fieldset class="form-section" id="form-line-';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '">
        <div class="row">
            <div class="form-group col-8">
                <label class="form-label" for="page_new_';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '">
                    ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure122 = function() use ($renderingContext) {
return NULL;
};

$arguments121 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:backend/Resources/Private/Language/locallang_pages_new.xlf:newPage',
];

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments121, $renderChildrenClosure122, $renderingContext)]);

$output120 .= ' ';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.label')]);

$output120 .= '
                </label>
                <div class="formengine-field-item t3js-formengine-field-item">
                    <div class="form-control-wrap">
                        <div class="form-wizards-wrap">
                            <input
                                class="form-control t3js-newmultiplepages-page-title"
                                type="text"
                                id="page_new_';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '"
                                name="pages[NEW';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '][title]"
                                placeholder="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure124 = function() use ($renderingContext) {
return NULL;
};

$arguments123 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.title',
];

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments123, $renderChildrenClosure124, $renderingContext)]);

$output120 .= '"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-4">
                <label class="form-label" for="page_new_select_';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '">
                    ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure126 = function() use ($renderingContext) {
return NULL;
};

$arguments125 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.type',
];

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments125, $renderChildrenClosure126, $renderingContext)]);

$output120 .= '
                </label>
                <div class="formengine-field-item t3js-formengine-field-item">
                    <div class="form-control-wrap">
                        <div class="form-wizards-wrap">
                            <div class="form-wizards-element">
                                <div class="input-group">
                                    <span class="input-group-addon input-group-icon" id="page_new_icon_';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '">
                                        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconForRecordViewHelper
$renderChildrenClosure128 = function() use ($renderingContext) {
return NULL;
};

$array129 = [
'id' => 0,
];

$arguments127 = [
'size' => 'small',
'alternativeMarkupIdentifier' => NULL,
'table' => 'pages',
'row' => $array129,
];

$output120 .= TYPO3\CMS\Core\ViewHelpers\IconForRecordViewHelper::renderStatic($arguments127, $renderChildrenClosure128, $renderingContext);

$output120 .= '
                                    </span>
                                    <select id="page_new_select_';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '" class="form-select t3js-newmultiplepages-select-doktype" name="pages[NEW';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '][doktype]" data-target="#page_new_icon_';

$output120 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('line.index')]);

$output120 .= '">
                                        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure131 = function() use ($renderingContext) {
$output132 = '';

$output132 .= '
                                            <optgroup label="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure134 = function() use ($renderingContext) {
return NULL;
};

$arguments133 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => $renderingContext->getVariableProvider()->getByPath('group'),
];

$output132 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments133, $renderChildrenClosure134, $renderingContext)]);

$output132 .= '">
                                                ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure136 = function() use ($renderingContext) {
$output137 = '';

$output137 .= '
                                                    <option data-icon=\'';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure139 = function() use ($renderingContext) {
return NULL;
};

$arguments138 = [
'size' => 'small',
'overlay' => NULL,
'state' => 'default',
'alternativeMarkupIdentifier' => NULL,
'title' => NULL,
'identifier' => $renderingContext->getVariableProvider()->getByPath('type.icon'),
];

$output137 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments138, $renderChildrenClosure139, $renderingContext);

$output137 .= '\' value="';

$output137 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('type.value')]);

$output137 .= '">';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure141 = function() use ($renderingContext) {
return NULL;
};

$arguments140 = [
'id' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => $renderingContext->getVariableProvider()->getByPath('type.label'),
'default' => $renderingContext->getVariableProvider()->getByPath('type.label'),
];

$output137 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments140, $renderChildrenClosure141, $renderingContext)]);

$output137 .= '</option>
                                                ';
return $output137;
};

$arguments135 = [
'key' => NULL,
'reverse' => false,
'iteration' => NULL,
'each' => $renderingContext->getVariableProvider()->getByPath('typegroup'),
'as' => 'type',
];

$output132 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments135, $renderChildrenClosure136, $renderingContext);

$output132 .= '
                                            </optgroup>
                                        ';
return $output132;
};

$arguments130 = [
'reverse' => false,
'iteration' => NULL,
'each' => $renderingContext->getVariableProvider()->getByPath('line.pageTypes'),
'as' => 'typegroup',
'key' => 'group',
];

$output120 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments130, $renderChildrenClosure131, $renderingContext);

$output120 .= '
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
';

    return $output120;
}
/**
 * Main Render function
 */
public function render(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output142 = '';

$output142 .= '

';

$output142 .= '';

$output142 .= '

';

$output142 .= '';

$output142 .= '

';

$output142 .= '';

$output142 .= '


';

    return $output142;
}

}

#