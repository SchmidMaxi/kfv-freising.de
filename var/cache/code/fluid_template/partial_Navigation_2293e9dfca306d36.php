<?php
class partial_Navigation_2293e9dfca306d36 extends \TYPO3Fluid\Fluid\Core\Compiler\AbstractCompiledTemplate {
    public function getLayoutName(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
        return (string)'';
    }
    public function hasLayout() {
        return false;
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
 * section Primary
 */
public function section_a531e4ecc92ea537(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output0 = '';

$output0 .= '
  <nav class="navbar navbar-expand-lg bg-body">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure2 = function() use ($renderingContext) {
$output3 = '';

$output3 .= '
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array5 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
];

$expression6 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments4 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression6(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array5)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output7 = '';

$output7 .= '
                <li class="nav-item dropdown">
                  ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure9 = function() use ($renderingContext) {
$output18 = '';

$output18 .= '
                    ';

$output18 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output18 .= '
                  ';
return $output18;
};
$output10 = '';

$output10 .= 'nav-link dropdown-toggle ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array12 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression13 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments11 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression13(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array12)),
    $renderingContext
),
];

$output10 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments11, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array16 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression17 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments15 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression17(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array16)),
    $renderingContext
),
];

$array14 = [
'role' => 'button',
'aria-expanded' => 'false',
'data-bs-toggle' => 'dropdown',
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments15, static fn() => '', $renderingContext)
,
];

$arguments8 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => '#',
'class' => $output10,
'additionalAttributes' => $array14,
];

$output7 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments8, $renderChildrenClosure9, $renderingContext);

$output7 .= '
                  <ul class="dropdown-menu">
                    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure20 = function() use ($renderingContext) {
$output21 = '';

$output21 .= '
                      <li>
                        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure23 = function() use ($renderingContext) {
$output32 = '';

$output32 .= '
                          ';

$output32 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('child.title')]);

$output32 .= '
                        ';
return $output32;
};
$output24 = '';

$output24 .= 'dropdown-item';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array26 = [
'0' => $renderingContext->getVariableProvider()->getByPath('child.active'),
];

$expression27 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments25 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression27(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array26)),
    $renderingContext
),
];

$output24 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments25, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array30 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression31 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments29 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression31(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array30)),
    $renderingContext
),
];

$array28 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments29, static fn() => '', $renderingContext)
,
];

$arguments22 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('child.link'),
'class' => $output24,
'additionalAttributes' => $array28,
];

$output21 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments22, $renderChildrenClosure23, $renderingContext);

$output21 .= '
                      </li>
                    ';
return $output21;
};

$arguments19 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
'as' => 'child',
'iteration' => 'i',
];

$output7 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments19, $renderChildrenClosure20, $renderingContext);

$output7 .= '
                  </ul>
                </li>
              ';
return $output7;
},
'__else' => function() use ($renderingContext) {
$output33 = '';

$output33 .= '
                <li class="nav-item">
                  ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure35 = function() use ($renderingContext) {
$output44 = '';

$output44 .= '
                    ';

$output44 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output44 .= '
                  ';
return $output44;
};
$output36 = '';

$output36 .= 'nav-link';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array38 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression39 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments37 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression39(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array38)),
    $renderingContext
),
];

$output36 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments37, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array42 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression43 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments41 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression43(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array42)),
    $renderingContext
),
];

$array40 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments41, static fn() => '', $renderingContext)
,
];

$arguments34 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'class' => $output36,
'additionalAttributes' => $array40,
];

$output33 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments34, $renderChildrenClosure35, $renderingContext);

$output33 .= '
                </li>
              ';
return $output33;
},
];

$output3 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments4, static fn() => '', $renderingContext)
;

$output3 .= '
          ';
return $output3;
};

$arguments1 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('primaryNavigation'),
'as' => 'navItem',
'iteration' => 'i',
];

$output0 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments1, $renderChildrenClosure2, $renderingContext);

$output0 .= '
        </ul>
        <ul class="nav d-lg-none">
          ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure46 = function() use ($renderingContext) {
$output47 = '';

$output47 .= '
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array49 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
];

$expression50 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments48 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression50(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array49)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output51 = '';

$output51 .= '
                <li class="nav-item dropdown">
                  ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure53 = function() use ($renderingContext) {
$output62 = '';

$output62 .= '
                    ';

$output62 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output62 .= '
                  ';
return $output62;
};
$output54 = '';

$output54 .= 'nav-link dropdown-toggle ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array56 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression57 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments55 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression57(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array56)),
    $renderingContext
),
];

$output54 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments55, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array60 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression61 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments59 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression61(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array60)),
    $renderingContext
),
];

$array58 = [
'role' => 'button',
'aria-expanded' => 'false',
'data-bs-toggle' => 'dropdown',
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments59, static fn() => '', $renderingContext)
,
];

$arguments52 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => '#',
'class' => $output54,
'additionalAttributes' => $array58,
];

$output51 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments52, $renderChildrenClosure53, $renderingContext);

$output51 .= '
                  <ul class="dropdown-menu">
                    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure64 = function() use ($renderingContext) {
$output65 = '';

$output65 .= '
                      <li>
                        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure67 = function() use ($renderingContext) {
$output76 = '';

$output76 .= '
                          ';

$output76 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('child.title')]);

$output76 .= '
                        ';
return $output76;
};
$output68 = '';

$output68 .= 'dropdown-item';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array70 = [
'0' => $renderingContext->getVariableProvider()->getByPath('child.active'),
];

$expression71 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments69 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression71(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array70)),
    $renderingContext
),
];

$output68 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments69, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array74 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression75 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments73 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression75(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array74)),
    $renderingContext
),
];

$array72 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments73, static fn() => '', $renderingContext)
,
];

$arguments66 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('child.link'),
'class' => $output68,
'additionalAttributes' => $array72,
];

$output65 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments66, $renderChildrenClosure67, $renderingContext);

$output65 .= '
                      </li>
                    ';
return $output65;
};

$arguments63 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
'as' => 'child',
'iteration' => 'i',
];

$output51 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments63, $renderChildrenClosure64, $renderingContext);

$output51 .= '
                  </ul>
                </li>
              ';
return $output51;
},
'__else' => function() use ($renderingContext) {
$output77 = '';

$output77 .= '
                <li class="nav-item">
                  ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure79 = function() use ($renderingContext) {
$output88 = '';

$output88 .= '
                    ';

$output88 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output88 .= '
                  ';
return $output88;
};
$output80 = '';

$output80 .= 'nav-link';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array82 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression83 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments81 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression83(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array82)),
    $renderingContext
),
];

$output80 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments81, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array86 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression87 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments85 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression87(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array86)),
    $renderingContext
),
];

$array84 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments85, static fn() => '', $renderingContext)
,
];

$arguments78 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'class' => $output80,
'additionalAttributes' => $array84,
];

$output77 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments78, $renderChildrenClosure79, $renderingContext);

$output77 .= '
                </li>
              ';
return $output77;
},
];

$output47 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments48, static fn() => '', $renderingContext)
;

$output47 .= '
          ';
return $output47;
};

$arguments45 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('secondaryNavigation'),
'as' => 'navItem',
'iteration' => 'i',
];

$output0 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments45, $renderChildrenClosure46, $renderingContext);

$output0 .= '
        </ul>
      </div>
    </div>
  </nav>
';

    return $output0;
}
/**
 * section Secondary
 */
public function section_c9611f3e99277ff0(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output89 = '';

$output89 .= '
  <nav>
    <ul class="nav justify-content-end">
      ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure91 = function() use ($renderingContext) {
$output92 = '';

$output92 .= '
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array94 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
];

$expression95 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments93 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression95(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array94)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output96 = '';

$output96 .= '
            <li class="nav-item dropdown">
              ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure98 = function() use ($renderingContext) {
$output107 = '';

$output107 .= '
                ';

$output107 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output107 .= '
              ';
return $output107;
};
$output99 = '';

$output99 .= 'nav-link dropdown-toggle ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array101 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression102 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments100 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression102(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array101)),
    $renderingContext
),
];

$output99 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments100, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array105 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression106 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments104 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression106(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array105)),
    $renderingContext
),
];

$array103 = [
'role' => 'button',
'aria-expanded' => 'false',
'data-bs-toggle' => 'dropdown',
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments104, static fn() => '', $renderingContext)
,
];

$arguments97 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => '#',
'class' => $output99,
'additionalAttributes' => $array103,
];

$output96 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments97, $renderChildrenClosure98, $renderingContext);

$output96 .= '
              <ul class="dropdown-menu">
                ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure109 = function() use ($renderingContext) {
$output110 = '';

$output110 .= '
                  <li>
                    ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure112 = function() use ($renderingContext) {
$output121 = '';

$output121 .= '
                      ';

$output121 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('child.title')]);

$output121 .= '
                    ';
return $output121;
};
$output113 = '';

$output113 .= 'dropdown-item';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array115 = [
'0' => $renderingContext->getVariableProvider()->getByPath('child.active'),
];

$expression116 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments114 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression116(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array115)),
    $renderingContext
),
];

$output113 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments114, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array119 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression120 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments118 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression120(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array119)),
    $renderingContext
),
];

$array117 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments118, static fn() => '', $renderingContext)
,
];

$arguments111 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('child.link'),
'class' => $output113,
'additionalAttributes' => $array117,
];

$output110 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments111, $renderChildrenClosure112, $renderingContext);

$output110 .= '
                  </li>
                ';
return $output110;
};

$arguments108 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
'as' => 'child',
'iteration' => 'i',
];

$output96 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments108, $renderChildrenClosure109, $renderingContext);

$output96 .= '
              </ul>
            </li>
          ';
return $output96;
},
'__else' => function() use ($renderingContext) {
$output122 = '';

$output122 .= '
            <li class="nav-item">
              ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure124 = function() use ($renderingContext) {
$output133 = '';

$output133 .= '
                ';

$output133 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output133 .= '
              ';
return $output133;
};
$output125 = '';

$output125 .= 'nav-link';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array127 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression128 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments126 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression128(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array127)),
    $renderingContext
),
];

$output125 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments126, static fn() => '', $renderingContext)
;
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array131 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression132 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments130 = [
'then' => 'page',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression132(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array131)),
    $renderingContext
),
];

$array129 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments130, static fn() => '', $renderingContext)
,
];

$arguments123 = [
'target' => '',
'title' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'class' => $output125,
'additionalAttributes' => $array129,
];

$output122 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments123, $renderChildrenClosure124, $renderingContext);

$output122 .= '
            </li>
          ';
return $output122;
},
];

$output92 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments93, static fn() => '', $renderingContext)
;

$output92 .= '
      ';
return $output92;
};

$arguments90 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('secondaryNavigation'),
'as' => 'navItem',
'iteration' => 'i',
];

$output89 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments90, $renderChildrenClosure91, $renderingContext);

$output89 .= '
    </ul>
  </nav>
';

    return $output89;
}
/**
 * section Social
 */
public function section_9251ca0817813dad(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output134 = '';

$output134 .= '
  <ul class="nav gap-3 socialNavigation';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper
$output136 = '';

$output136 .= ' justify-content-';

$output136 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('alignment')]);

$array137 = [
'0' => $renderingContext->getVariableProvider()->getByPath('alignment'),
];

$expression138 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments135 = [
'then' => $output136,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression138(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array137)),
    $renderingContext
),
];

$output134 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments135, static fn() => '', $renderingContext)
;

$output134 .= '">
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure140 = function() use ($renderingContext) {
$output141 = '';

$output141 .= '
      <li class="nav-item">
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure143 = function() use ($renderingContext) {
$output147 = '';

$output147 .= '
          ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array149 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.media.0'),
];

$expression150 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments148 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression150(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array149)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output151 = '';

$output151 .= '
              ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\ImageViewHelper
$renderChildrenClosure153 = function() use ($renderingContext) {
return NULL;
};

$arguments152 = [
'additionalAttributes' => NULL,
'data' => NULL,
'aria' => NULL,
'dir' => NULL,
'id' => NULL,
'lang' => NULL,
'style' => NULL,
'title' => NULL,
'accesskey' => NULL,
'tabindex' => NULL,
'onclick' => NULL,
'ismap' => NULL,
'longdesc' => NULL,
'usemap' => NULL,
'loading' => NULL,
'decoding' => NULL,
'src' => '',
'treatIdAsReference' => false,
'crop' => NULL,
'cropVariant' => 'default',
'fileExtension' => NULL,
'width' => NULL,
'minWidth' => NULL,
'minHeight' => NULL,
'maxWidth' => NULL,
'maxHeight' => NULL,
'absolute' => false,
'image' => $renderingContext->getVariableProvider()->getByPath('navItem.media.0'),
'class' => '',
'alt' => $renderingContext->getVariableProvider()->getByPath('navItem.title'),
'height' => 43,
];

$output151 .= TYPO3\CMS\Fluid\ViewHelpers\ImageViewHelper::renderStatic($arguments152, $renderChildrenClosure153, $renderingContext);

$output151 .= '
            ';
return $output151;
},
'__else' => function() use ($renderingContext) {
$output154 = '';

$output154 .= '
              ';

$output154 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output154 .= '
            ';
return $output154;
},
];

$output147 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments148, static fn() => '', $renderingContext)
;

$output147 .= '
        ';
return $output147;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array145 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.target'),
];

$expression146 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments144 = [
'then' => $renderingContext->getVariableProvider()->getByPath('navItem.target'),
'else' => '_blank',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression146(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array145)),
    $renderingContext
),
];

$arguments142 = [
'language' => NULL,
'additionalParams' => '',
'additionalAttributes' => [],
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'title' => $renderingContext->getVariableProvider()->getByPath('navItem.title'),
'target' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments144, static fn() => '', $renderingContext)
,
'class' => 'nav-link px-0',
];

$output141 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments142, $renderChildrenClosure143, $renderingContext);

$output141 .= '
      </li>
    ';
return $output141;
};

$arguments139 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('socialNavigation'),
'as' => 'navItem',
'iteration' => 'i',
];

$output134 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments139, $renderChildrenClosure140, $renderingContext);

$output134 .= '
  </ul>
';

    return $output134;
}
/**
 * section Meta
 */
public function section_29cb9ed501a840ba(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output155 = '';

$output155 .= '
  <ul class="nav justify-content-center border-bottom pb-3 mb-3" style="--bs-nav-link-color: var(--bs-gray-400);--bs-nav-link-hover-color:var(--bs-white)">
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure157 = function() use ($renderingContext) {
$output158 = '';

$output158 .= '
      <li class="nav-item">
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure160 = function() use ($renderingContext) {
$output165 = '';

$output165 .= '
          ';

$output165 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output165 .= '
        ';
return $output165;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array163 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression164 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments162 = [
'then' => 'page',
'else' => '',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression164(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array163)),
    $renderingContext
),
];

$array161 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments162, static fn() => '', $renderingContext)
,
];

$arguments159 = [
'target' => '',
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'title' => $renderingContext->getVariableProvider()->getByPath('navItem.title'),
'class' => 'nav-link px-2',
'additionalAttributes' => $array161,
];

$output158 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments159, $renderChildrenClosure160, $renderingContext);

$output158 .= '
      </li>
      ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array167 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.data.uid'),
'1' => ' == ',
'2' => $renderingContext->getVariableProvider()->getByPath('dataProtectionDeclaration'),
];

$expression168 = function($context) {return (TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]) == TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node2"]));};

$arguments166 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression168(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array167)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output169 = '';

$output169 .= '
        <li class="nav-item">
          <div id="cookieBtn" class="nav-link px-2" role="button" tabindex="0" >
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\Format\RawViewHelper
$renderChildrenClosure171 = function() use ($renderingContext) {
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure173 = function() use ($renderingContext) {
return NULL;
};

$arguments172 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'PrivacySettings',
'extensionName' => 'Template',
];
return TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments172, $renderChildrenClosure173, $renderingContext);
};

$arguments170 = [
'value' => NULL,
];

$output169 .= isset($arguments170['value']) ? $arguments170['value'] : $renderChildrenClosure171();

$output169 .= '
          </div>
        </li>
      ';
return $output169;
},
];

$output158 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments166, static fn() => '', $renderingContext)
;

$output158 .= '
    ';
return $output158;
};

$arguments156 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('metaNavigation'),
'as' => 'navItem',
'iteration' => 'position',
];

$output155 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments156, $renderChildrenClosure157, $renderingContext);

$output155 .= '
  </ul>
';

    return $output155;
}
/**
 * section Breadcrumb
 */
public function section_57776c41e9c42df5(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output174 = '';

$output174 .= '
  ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array176 = [
'0' => $renderingContext->getVariableProvider()->getByPath('breadcrumbNavigation'),
];

$expression177 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments175 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression177(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array176)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output178 = '';

$output178 .= '
      <nav aria-label="breadcrumb" id="breadcrumbNav" class="';

$output178 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('firstelementBgColor')]);

$output178 .= '">
        <div class="container">
          <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure180 = function() use ($renderingContext) {
$output181 = '';

$output181 .= '
              ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array183 = [
'0' => $renderingContext->getVariableProvider()->getByPath('breadcrumbItem.current'),
];

$expression184 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments182 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression184(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array183)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output185 = '';

$output185 .= '
                  <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <span itemprop="name">';

$output185 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.title')]);

$output185 .= '</span>
                    <meta itemprop="position" content="';

$output185 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('iteration.cycle')]);

$output185 .= '" />
                  </li>
                ';
return $output185;
},
'__else' => function() use ($renderingContext) {
$output186 = '';

$output186 .= '
                  <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a class="text-decoration-none" itemscope itemtype="https://schema.org/WebPage" itemprop="item" itemid="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper
$renderChildrenClosure188 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('breadcrumbItem.link');
};

$array189 = [
'0' => 1,
];

$expression190 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments187 = [
'additionalParams' => '',
'language' => NULL,
'addQueryString' => false,
'addQueryStringExclude' => '',
'parameter' => 1,
'absolute' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression190(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array189)),
    $renderingContext
),
];

$output186 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper::renderStatic($arguments187, $renderChildrenClosure188, $renderingContext)]);

$output186 .= '" href="';

$output186 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.link')]);

$output186 .= '" title="';

$output186 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.title')]);

$output186 .= '">
                      <span itemprop="name">';

$output186 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.title')]);

$output186 .= '</span>
                    </a>
                    <meta itemprop="position" content="';

$output186 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('iteration.cycle')]);

$output186 .= '" />
                  </li>
                ';
return $output186;
},
];

$output181 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments182, static fn() => '', $renderingContext)
;

$output181 .= '
            ';
return $output181;
};

$arguments179 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('breadcrumbNavigation'),
'as' => 'breadcrumbItem',
'iteration' => 'iteration',
];

$output178 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments179, $renderChildrenClosure180, $renderingContext);

$output178 .= '
          </ol>
        </div>
      </nav>
    ';
return $output178;
},
'__else' => function() use ($renderingContext) {
return '
      <div id="breadcrumbNav"></div>
    ';
},
];

$output174 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments175, static fn() => '', $renderingContext)
;

$output174 .= '
';

    return $output174;
}
/**
 * Main Render function
 */
public function render(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output191 = '';

$output191 .= '';

$output191 .= '
';

$output191 .= '';

$output191 .= '

';

$output191 .= '';

$output191 .= '


';

$output191 .= '';

$output191 .= '


';

$output191 .= '';

$output191 .= '

';

    return $output191;
}

}

#