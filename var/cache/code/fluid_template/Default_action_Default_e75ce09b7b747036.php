<?php
class Default_action_Default_e75ce09b7b747036 extends \TYPO3Fluid\Fluid\Core\Compiler\AbstractCompiledTemplate {
    public function getLayoutName(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
        return (string)'Default';
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
));
    }
    /**
 * section Module
 */
public function section_df951f992ac6a5ed(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output0 = '';

$output0 .= '
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure2 = function() use ($renderingContext) {
$output3 = '';

$output3 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure5 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('iteration.index');
};

$arguments4 = [
'value' => NULL,
'name' => 'uid',
];
$renderChildrenClosure5 = ($arguments4['value'] !== null) ? function() use ($arguments4) { return $arguments4['value']; } : $renderChildrenClosure5;
$output3 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments4, $renderChildrenClosure5, $renderingContext)]);

$output3 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure7 = function() use ($renderingContext) {
$output8 = '';

$output8 .= '
            ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure10 = function() use ($renderingContext) {
return NULL;
};

$arguments9 = [
'size' => 'small',
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => $renderingContext->getVariableProvider()->getByPath('module.iconIdentifier'),
'alternativeMarkupIdentifier' => 'inline',
];

$output8 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments9, $renderChildrenClosure10, $renderingContext);

$output8 .= '
        ';
return $output8;
};

$arguments6 = [
'value' => NULL,
'name' => 'icon',
];
$renderChildrenClosure7 = ($arguments6['value'] !== null) ? function() use ($arguments6) { return $arguments6['value']; } : $renderChildrenClosure7;
$output3 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments6, $renderChildrenClosure7, $renderingContext)]);

$output3 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure12 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('module.label');
};

$arguments11 = [
'value' => NULL,
'name' => 'label',
];
$renderChildrenClosure12 = ($arguments11['value'] !== null) ? function() use ($arguments11) { return $arguments11['value']; } : $renderChildrenClosure12;
$output3 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments11, $renderChildrenClosure12, $renderingContext)]);

$output3 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure14 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('module.shortInfo');
};

$arguments13 = [
'value' => NULL,
'name' => 'information',
];
$renderChildrenClosure14 = ($arguments13['value'] !== null) ? function() use ($arguments13) { return $arguments13['value']; } : $renderChildrenClosure14;
$output3 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments13, $renderChildrenClosure14, $renderingContext)]);

$output3 .= '
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure16 = function() use ($renderingContext) {
return NULL;
};

$array17 = [
'module' => $renderingContext->getVariableProvider()->getByPath('module'),
'uid' => $renderingContext->getVariableProvider()->getByPath('uid'),
'icon' => $renderingContext->getVariableProvider()->getByPath('icon'),
'label' => $renderingContext->getVariableProvider()->getByPath('label'),
'information' => $renderingContext->getVariableProvider()->getByPath('information'),
'data' => $renderingContext->getVariableProvider()->getByPath('data'),
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$array18 = [
'0' => 'false',
];

$expression19 = function($context) {return FALSE;};

$arguments15 = [
'section' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'partial' => 'Modules/Item',
'arguments' => $array17,
'debug' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression19(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array18)),
    $renderingContext
),
];

$output3 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments15, $renderChildrenClosure16, $renderingContext);

$output3 .= '
    ';
return $output3;
};

$arguments1 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('parentModules'),
'as' => 'module',
'iteration' => 'iteration',
];

$output0 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments1, $renderChildrenClosure2, $renderingContext);

$output0 .= '

    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array21 = [
'0' => $renderingContext->getVariableProvider()->getByPath('hookObjectModuleContent'),
];

$expression22 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments20 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression22(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array21)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output23 = '';

$output23 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure25 = function() use ($renderingContext) {
return 'hook';
};

$arguments24 = [
'value' => NULL,
'name' => 'uid',
];
$renderChildrenClosure25 = ($arguments24['value'] !== null) ? function() use ($arguments24) { return $arguments24['value']; } : $renderChildrenClosure25;
$output23 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments24, $renderChildrenClosure25, $renderingContext)]);

$output23 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure27 = function() use ($renderingContext) {
$output28 = '';

$output28 .= '
            ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure30 = function() use ($renderingContext) {
return NULL;
};

$arguments29 = [
'size' => 'small',
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'actions-window-open',
'alternativeMarkupIdentifier' => 'inline',
];

$output28 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments29, $renderChildrenClosure30, $renderingContext);

$output28 .= '
        ';
return $output28;
};

$arguments26 = [
'value' => NULL,
'name' => 'icon',
];
$renderChildrenClosure27 = ($arguments26['value'] !== null) ? function() use ($arguments26) { return $arguments26['value']; } : $renderChildrenClosure27;
$output23 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments26, $renderChildrenClosure27, $renderingContext)]);

$output23 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure32 = function() use ($renderingContext) {
$output33 = '';

$output33 .= '
            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure35 = function() use ($renderingContext) {
return NULL;
};

$arguments34 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:adminpanel/Resources/Private/Language/locallang.xlf:deprecatedModuleLabel',
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$output33 .= TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments34, $renderChildrenClosure35, $renderingContext);

$output33 .= '
        ';
return $output33;
};

$arguments31 = [
'value' => NULL,
'name' => 'label',
];
$renderChildrenClosure32 = ($arguments31['value'] !== null) ? function() use ($arguments31) { return $arguments31['value']; } : $renderChildrenClosure32;
$output23 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments31, $renderChildrenClosure32, $renderingContext)]);

$output23 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure37 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('hookObjectModuleContent');
};

$arguments36 = [
'value' => NULL,
'name' => 'mainContent',
];
$renderChildrenClosure37 = ($arguments36['value'] !== null) ? function() use ($arguments36) { return $arguments36['value']; } : $renderChildrenClosure37;
$output23 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments36, $renderChildrenClosure37, $renderingContext)]);

$output23 .= '
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure39 = function() use ($renderingContext) {
return NULL;
};
$output40 = '';

$array41 = [
'uid' => $renderingContext->getVariableProvider()->getByPath('uid'),
'icon' => $renderingContext->getVariableProvider()->getByPath('icon'),
'label' => $renderingContext->getVariableProvider()->getByPath('label'),
'mainContent' => $renderingContext->getVariableProvider()->getByPath('mainContent'),
'data' => $renderingContext->getVariableProvider()->getByPath('data'),
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$output40 .= $array41;

$output40 .= '}';

$array42 = [
'0' => 'false',
];

$expression43 = function($context) {return FALSE;};

$arguments38 = [
'section' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'partial' => 'Modules/Item',
'arguments' => $output40,
'debug' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression43(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array42)),
    $renderingContext
),
];

$output23 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments38, $renderChildrenClosure39, $renderingContext);

$output23 .= '
    ';
return $output23;
},
];

$output0 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments20, static fn() => '', $renderingContext)
;

$output0 .= '

';

    return $output0;
}
/**
 * section Settings
 */
public function section_87df8b759d277b20(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output44 = '';

$output44 .= '

    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure46 = function() use ($renderingContext) {
return 'settings';
};

$arguments45 = [
'value' => NULL,
'name' => 'uid',
];
$renderChildrenClosure46 = ($arguments45['value'] !== null) ? function() use ($arguments45) { return $arguments45['value']; } : $renderChildrenClosure46;
$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments45, $renderChildrenClosure46, $renderingContext)]);

$output44 .= '
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure48 = function() use ($renderingContext) {
$output49 = '';

$output49 .= '
        ';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure51 = function() use ($renderingContext) {
return NULL;
};

$arguments50 = [
'size' => 'small',
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'actions-system-extension-configure',
'alternativeMarkupIdentifier' => 'inline',
];

$output49 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments50, $renderChildrenClosure51, $renderingContext);

$output49 .= '
    ';
return $output49;
};

$arguments47 = [
'value' => NULL,
'name' => 'icon',
];
$renderChildrenClosure48 = ($arguments47['value'] !== null) ? function() use ($arguments47) { return $arguments47['value']; } : $renderChildrenClosure48;
$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments47, $renderChildrenClosure48, $renderingContext)]);

$output44 .= '
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure53 = function() use ($renderingContext) {
$output54 = '';

$output54 .= '
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure56 = function() use ($renderingContext) {
return NULL;
};

$arguments55 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:adminpanel/Resources/Private/Language/locallang.xlf:settings.label',
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$output54 .= TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments55, $renderChildrenClosure56, $renderingContext);

$output54 .= '
    ';
return $output54;
};

$arguments52 = [
'value' => NULL,
'name' => 'label',
];
$renderChildrenClosure53 = ($arguments52['value'] !== null) ? function() use ($arguments52) { return $arguments52['value']; } : $renderChildrenClosure53;
$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments52, $renderChildrenClosure53, $renderingContext)]);

$output44 .= '
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure58 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('module.shortInfo');
};

$arguments57 = [
'value' => NULL,
'name' => 'information',
];
$renderChildrenClosure58 = ($arguments57['value'] !== null) ? function() use ($arguments57) { return $arguments57['value']; } : $renderChildrenClosure58;
$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments57, $renderChildrenClosure58, $renderingContext)]);

$output44 .= '
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure60 = function() use ($renderingContext) {
$output61 = '';

$output61 .= '
        <h1 class="typo3-adminPanel-headline">
            ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure63 = function() use ($renderingContext) {
return NULL;
};

$arguments62 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:adminpanel/Resources/Private/Language/locallang.xlf:settings.label',
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$output61 .= TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments62, $renderChildrenClosure63, $renderingContext);

$output61 .= '
        </h1>
        <div class="typo3-adminPanel-card-group">
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure65 = function() use ($renderingContext) {
$output66 = '';

$output66 .= '
                ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array68 = [
'0' => $renderingContext->getVariableProvider()->getByPath('module.pageSettings'),
];

$expression69 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments67 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression69(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array68)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output70 = '';

$output70 .= '
                    <div class="typo3-adminPanel-card">
                        <div class="typo3-adminPanel-card-header">
                            <h2 class="typo3-adminPanel-card-header-headline">
                                <span class="typo3-adminPanel-card-header-icon">';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure72 = function() use ($renderingContext) {
return NULL;
};

$arguments71 = [
'size' => 'small',
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => $renderingContext->getVariableProvider()->getByPath('module.iconIdentifier'),
'alternativeMarkupIdentifier' => 'inline',
];

$output70 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments71, $renderChildrenClosure72, $renderingContext);

$output70 .= '</span>
                                <span class="typo3-adminPanel-card-header-text">';

$output70 .= $renderingContext->getVariableProvider()->getByPath('module.label');

$output70 .= '</span>
                            </h2>
                        </div>
                        <div class="typo3-adminPanel-card-body">
                            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\Format\RawViewHelper
$renderChildrenClosure74 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('module.pageSettings');
};

$arguments73 = [
'value' => NULL,
];

$output70 .= isset($arguments73['value']) ? $arguments73['value'] : $renderChildrenClosure74();

$output70 .= '
                        </div>
                    </div>
                ';
return $output70;
},
];

$output66 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments67, static fn() => '', $renderingContext)
;

$output66 .= '
            ';
return $output66;
};

$arguments64 = [
'key' => NULL,
'reverse' => false,
'iteration' => NULL,
'each' => $renderingContext->getVariableProvider()->getByPath('settingsModules'),
'as' => 'module',
];

$output61 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments64, $renderChildrenClosure65, $renderingContext);

$output61 .= '
        </div>
        <div class="typo3-adminPanel-form-group">
            <button data-typo3-role="typo3-adminPanel-saveButton" class="typo3-adminPanel-btn typo3-adminPanel-btn-primary">
                ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure76 = function() use ($renderingContext) {
return NULL;
};

$arguments75 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:adminpanel/Resources/Private/Language/locallang.xlf:settings.updateButtonLabel',
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$output61 .= TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments75, $renderChildrenClosure76, $renderingContext);

$output61 .= '
            </button>
        </div>
    ';
return $output61;
};

$arguments59 = [
'value' => NULL,
'name' => 'mainContent',
];
$renderChildrenClosure60 = ($arguments59['value'] !== null) ? function() use ($arguments59) { return $arguments59['value']; } : $renderChildrenClosure60;
$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments59, $renderChildrenClosure60, $renderingContext)]);

$output44 .= '
    <div class="typo3-adminPanel-module-group">
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure78 = function() use ($renderingContext) {
return NULL;
};

$array79 = [
'uid' => $renderingContext->getVariableProvider()->getByPath('uid'),
'icon' => $renderingContext->getVariableProvider()->getByPath('icon'),
'label' => $renderingContext->getVariableProvider()->getByPath('label'),
'information' => $renderingContext->getVariableProvider()->getByPath('information'),
'mainContent' => $renderingContext->getVariableProvider()->getByPath('mainContent'),
'data' => $renderingContext->getVariableProvider()->getByPath('data'),
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$array80 = [
'0' => 'false',
];

$expression81 = function($context) {return FALSE;};

$arguments77 = [
'section' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'partial' => 'Modules/Item',
'arguments' => $array79,
'debug' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression81(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array80)),
    $renderingContext
),
];

$output44 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments77, $renderChildrenClosure78, $renderingContext);

$output44 .= '
        <div class="typo3-adminPanel-module">
            <div class="typo3-adminPanel-module-trigger">
                <a href="';

$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('backendUrl')]);

$output44 .= '" class="typo3-adminPanel-backend-url" target="typo3-backend">
                    <span class="typo3-adminPanel-module-trigger-icon">';
// Rendering ViewHelper TYPO3\CMS\Core\ViewHelpers\IconViewHelper
$renderChildrenClosure83 = function() use ($renderingContext) {
return NULL;
};

$arguments82 = [
'size' => 'small',
'overlay' => NULL,
'state' => 'default',
'title' => NULL,
'identifier' => 'actions-window-open',
'alternativeMarkupIdentifier' => 'inline',
];

$output44 .= TYPO3\CMS\Core\ViewHelpers\IconViewHelper::renderStatic($arguments82, $renderChildrenClosure83, $renderingContext);

$output44 .= '</span>
                    <span class="typo3-adminPanel-module-trigger-label">';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure85 = function() use ($renderingContext) {
return NULL;
};

$arguments84 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'extensionName' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'LLL:EXT:adminpanel/Resources/Private/Language/locallang.xlf:settings.openBackendUrlLabel',
'languageKey' => $renderingContext->getVariableProvider()->getByPath('languageKey'),
];

$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments84, $renderChildrenClosure85, $renderingContext)]);

$output44 .= '</span>
                    <span class="typo3-adminPanel-module-trigger-information">';

$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('information')]);

$output44 .= '</span>
                </a>
            </div>
        </div>
    </div>
';

    return $output44;
}
/**
 * Main Render function
 */
public function render(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output86 = '';

$output86 .= '
';

$output86 .= '';

$output86 .= '
';

$output86 .= '';

$output86 .= '
';

$output86 .= '';

$output86 .= '

';

    return $output86;
}

}

#