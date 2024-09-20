<?php
class partial_Navigation_d72955bd8cdfaf3f extends \TYPO3Fluid\Fluid\Core\Compiler\AbstractCompiledTemplate {
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
  'v' => 
  array (
    0 => 'FluidTYPO3\\Vhs\\ViewHelpers',
  ),
));
    }
    /**
 * section Primary
 */
public function section_a531e4ecc92ea537(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output0 = '';

$output0 .= '
  <div class="desktopNav d-none d-lg-block border-top border-bottom border-1 position-relative z-3">
    <div class="container">
      <ul id="nav-main" class="nav nav-justified">
        <li class="nav-item">
          ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure2 = function() use ($renderingContext) {
return NULL;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array4 = [
'0' => $renderingContext->getVariableProvider()->getByPath('data.uid'),
'1' => ' == ',
'2' => $renderingContext->getVariableProvider()->getByPath('home'),
];

$expression5 = function($context) {return (TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]) == TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node2"]));};

$arguments3 = [
'then' => 'page',
'else' => '',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression5(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array4)),
    $renderingContext
),
];

$arguments1 = [
'name' => 'aria-current',
'value' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments3, static fn() => '', $renderingContext)
,
];
$renderChildrenClosure2 = ($arguments1['value'] !== null) ? function() use ($arguments1) { return $arguments1['value']; } : $renderChildrenClosure2;
$output0 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments1, $renderChildrenClosure2, $renderingContext)]);

$output0 .= '
          ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure7 = function() use ($renderingContext) {
return '
              Startseite
          ';
};
$output8 = '';

$output8 .= 'nav-link border-top border-1';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array10 = [
'0' => $renderingContext->getVariableProvider()->getByPath('data.uid'),
'1' => ' == ',
'2' => $renderingContext->getVariableProvider()->getByPath('home'),
];

$expression11 = function($context) {return (TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]) == TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node2"]));};

$arguments9 = [
'then' => ' active border-primary',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression11(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array10)),
    $renderingContext
),
];

$output8 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments9, static fn() => '', $renderingContext)
;

$array12 = [
'aria-current' => $renderingContext->getVariableProvider()->getByPath('aria-current'),
];

$arguments6 = [
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('home'),
'target' => '_top',
'title' => 'Startseite',
'class' => $output8,
'additionalAttributes' => $array12,
];

$output0 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments6, $renderChildrenClosure7, $renderingContext);

$output0 .= '
        </li>
        ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure14 = function() use ($renderingContext) {
$output15 = '';

$output15 .= '
          <li class="nav-item">
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure17 = function() use ($renderingContext) {
return NULL;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array19 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression20 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments18 = [
'then' => 'page',
'else' => '',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression20(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array19)),
    $renderingContext
),
];

$arguments16 = [
'name' => 'aria-current',
'value' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments18, static fn() => '', $renderingContext)
,
];
$renderChildrenClosure17 = ($arguments16['value'] !== null) ? function() use ($arguments16) { return $arguments16['value']; } : $renderChildrenClosure17;
$output15 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments16, $renderChildrenClosure17, $renderingContext)]);

$output15 .= '
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array22 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
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
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure26 = function() use ($renderingContext) {
$output32 = '';

$output32 .= '
                    ';

$output32 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output32 .= '
                ';
return $output32;
};
$output27 = '';

$output27 .= 'nav-link border-top border-1';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array29 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression30 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments28 = [
'then' => ' active border-primary',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression30(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array29)),
    $renderingContext
),
];

$output27 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments28, static fn() => '', $renderingContext)
;

$array31 = [
'aria-current' => $renderingContext->getVariableProvider()->getByPath('aria-current'),
];

$arguments25 = [
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'target' => $renderingContext->getVariableProvider()->getByPath('navItem.target'),
'title' => $renderingContext->getVariableProvider()->getByPath('navItem.title'),
'class' => $output27,
'additionalAttributes' => $array31,
];

$output24 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments25, $renderChildrenClosure26, $renderingContext);

$output24 .= '

                <div class="dropdown">
                  ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure34 = function() use ($renderingContext) {
$output35 = '';

$output35 .= '
                    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper
$renderChildrenClosure37 = function() use ($renderingContext) {
return NULL;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array39 = [
'0' => $renderingContext->getVariableProvider()->getByPath('child.current'),
];

$expression40 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments38 = [
'then' => 'page',
'else' => '',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression40(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array39)),
    $renderingContext
),
];

$arguments36 = [
'name' => 'aria-current',
'value' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments38, static fn() => '', $renderingContext)
,
];
$renderChildrenClosure37 = ($arguments36['value'] !== null) ? function() use ($arguments36) { return $arguments36['value']; } : $renderChildrenClosure37;
$output35 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3Fluid\Fluid\ViewHelpers\VariableViewHelper::renderStatic($arguments36, $renderChildrenClosure37, $renderingContext)]);

$output35 .= '
                    ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure42 = function() use ($renderingContext) {
$output47 = '';

$output47 .= '
                      ';

$output47 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('child.title')]);

$output47 .= '
                    ';
return $output47;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array44 = [
'0' => $renderingContext->getVariableProvider()->getByPath('child.active'),
];

$expression45 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments43 = [
'then' => 'active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression45(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array44)),
    $renderingContext
),
];

$array46 = [
'aria-current' => $renderingContext->getVariableProvider()->getByPath('aria-current'),
];

$arguments41 = [
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('child.link'),
'target' => $renderingContext->getVariableProvider()->getByPath('child.target'),
'title' => $renderingContext->getVariableProvider()->getByPath('child.title'),
'class' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments43, static fn() => '', $renderingContext)
,
'additionalAttributes' => $array46,
];

$output35 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments41, $renderChildrenClosure42, $renderingContext);

$output35 .= '
                  ';
return $output35;
};

$arguments33 = [
'key' => NULL,
'reverse' => false,
'iteration' => NULL,
'each' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
'as' => 'child',
];

$output24 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments33, $renderChildrenClosure34, $renderingContext);

$output24 .= '
                </div>
              ';
return $output24;
},
'__else' => function() use ($renderingContext) {
$output48 = '';

$output48 .= '
                ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure50 = function() use ($renderingContext) {
$output56 = '';

$output56 .= '
                    ';

$output56 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output56 .= '
                ';
return $output56;
};
$output51 = '';

$output51 .= 'nav-link border-top border-1';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array53 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression54 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments52 = [
'then' => ' active border-primary',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression54(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array53)),
    $renderingContext
),
];

$output51 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments52, static fn() => '', $renderingContext)
;

$array55 = [
'aria-current' => $renderingContext->getVariableProvider()->getByPath('aria-current'),
];

$arguments49 = [
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'target' => $renderingContext->getVariableProvider()->getByPath('navItem.target'),
'title' => $renderingContext->getVariableProvider()->getByPath('navItem.title'),
'class' => $output51,
'additionalAttributes' => $array55,
];

$output48 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments49, $renderChildrenClosure50, $renderingContext);

$output48 .= '
              ';
return $output48;
},
];

$output15 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments21, static fn() => '', $renderingContext)
;

$output15 .= '
          </li>
        ';
return $output15;
};

$arguments13 = [
'key' => NULL,
'reverse' => false,
'iteration' => NULL,
'each' => $renderingContext->getVariableProvider()->getByPath('primaryNavigation'),
'as' => 'navItem',
];

$output0 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments13, $renderChildrenClosure14, $renderingContext);

$output0 .= '

        <div class="container">

          <div class="collapse navbar-collapse bg-body" id="navbarContent">

            <div class="btn-group dropstart">
              <button class="btn btn-pink" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-target="#headerSearch" aria-expanded="false" aria-controls="headerSearch">
                <span class="search-icon"></span>
              </button>
      
              <div class="dropdown-menu m-0 p-0" id="headerSearch">
                ';

$output0 .= '';

$output0 .= '
              </div>
            </div>
                

          </div>

        </div>
      </ul>
    </div>
  </div>
';

    return $output0;
}
/**
 * section Mobile
 */
public function section_606ae10221352756(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output57 = '';

$output57 .= '
  <div class="modal fade" id="navModal" tabindex="-1" aria-labelledby="navModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content text-white" style="--bs-modal-bg: #e4473f; --bs-link-color-rgb: #ffffff;">
        <div class="modal-header"></div>
        <div class="modal-body">
          <div class="container">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 gy-5">
              ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure59 = function() use ($renderingContext) {
$output60 = '';

$output60 .= '
                <div class="col">
                  ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure62 = function() use ($renderingContext) {
$output72 = '';

$output72 .= '
                    ';

$output72 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output72 .= '
                  ';
return $output72;
};
$output63 = '';

$output63 .= 'h3 d-block ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array65 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.active'),
];

$expression66 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments64 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression66(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array65)),
    $renderingContext
),
];

$output63 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments64, static fn() => '', $renderingContext)
;
$output68 = '';

$output68 .= 'page-';

$output68 .= $renderingContext->getVariableProvider()->getByPath('navItem.data.uid');
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array70 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression71 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments69 = [
'then' => 'page',
'else' => '',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression71(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array70)),
    $renderingContext
),
];

$array67 = [
'id' => $output68,
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments69, static fn() => '', $renderingContext)
,
];

$arguments61 = [
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('navItem.link'),
'target' => $renderingContext->getVariableProvider()->getByPath('navItem.target'),
'title' => $renderingContext->getVariableProvider()->getByPath('navItem.title'),
'class' => $output63,
'additionalAttributes' => $array67,
];

$output60 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments61, $renderChildrenClosure62, $renderingContext);

$output60 .= '
                  ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array74 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
];

$expression75 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments73 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression75(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array74)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output76 = '';

$output76 .= '
                    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure78 = function() use ($renderingContext) {
$output79 = '';

$output79 .= '
                      ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure81 = function() use ($renderingContext) {
$output91 = '';

$output91 .= '
                        ';

$output91 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('child.title')]);

$output91 .= '
                      ';
return $output91;
};
$output82 = '';

$output82 .= 'py-1 d-block text-uppercase';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array84 = [
'0' => $renderingContext->getVariableProvider()->getByPath('child.active'),
];

$expression85 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments83 = [
'then' => ' active',
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression85(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array84)),
    $renderingContext
),
];

$output82 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments83, static fn() => '', $renderingContext)
;
$output87 = '';

$output87 .= 'page-';

$output87 .= $renderingContext->getVariableProvider()->getByPath('child.data.uid');
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array89 = [
'0' => $renderingContext->getVariableProvider()->getByPath('child.current'),
];

$expression90 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments88 = [
'then' => 'page',
'else' => '',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression90(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array89)),
    $renderingContext
),
];

$array86 = [
'id' => $output87,
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments88, static fn() => '', $renderingContext)
,
];

$arguments80 = [
'language' => NULL,
'additionalParams' => '',
'addQueryString' => false,
'addQueryStringExclude' => '',
'absolute' => false,
'parts-as' => 'typoLinkParts',
'textWrap' => '',
'parameter' => $renderingContext->getVariableProvider()->getByPath('child.link'),
'target' => $renderingContext->getVariableProvider()->getByPath('child.target'),
'title' => $renderingContext->getVariableProvider()->getByPath('child.title'),
'class' => $output82,
'additionalAttributes' => $array86,
];

$output79 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments80, $renderChildrenClosure81, $renderingContext);

$output79 .= '
                    ';
return $output79;
};

$arguments77 = [
'key' => NULL,
'reverse' => false,
'iteration' => NULL,
'each' => $renderingContext->getVariableProvider()->getByPath('navItem.children'),
'as' => 'child',
];

$output76 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments77, $renderChildrenClosure78, $renderingContext);

$output76 .= '
                  ';
return $output76;
},
];

$output60 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments73, static fn() => '', $renderingContext)
;

$output60 .= '
                </div>
              ';
return $output60;
};

$arguments58 = [
'key' => NULL,
'reverse' => false,
'iteration' => NULL,
'each' => $renderingContext->getVariableProvider()->getByPath('primaryNavigation'),
'as' => 'navItem',
];

$output57 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments58, $renderChildrenClosure59, $renderingContext);

$output57 .= '
            </div>
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array93 = [
'0' => $renderingContext->getVariableProvider()->getByPath('socialNavigation'),
];

$expression94 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments92 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression94(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array93)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output95 = '';

$output95 .= '
              <p class="mt-5 mb-1 text-center">Folgen Sie uns:</p>
              ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
$renderChildrenClosure97 = function() use ($renderingContext) {
return NULL;
};

$array98 = [
'socialNavigation' => $renderingContext->getVariableProvider()->getByPath('socialNavigation'),
'alignment' => 'center',
];

$arguments96 = [
'partial' => NULL,
'delegate' => NULL,
'optional' => false,
'default' => NULL,
'contentAs' => NULL,
'debug' => true,
'section' => 'Social',
'arguments' => $array98,
];

$output95 .= TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper::renderStatic($arguments96, $renderChildrenClosure97, $renderingContext);

$output95 .= '
            ';
return $output95;
},
];

$output57 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments92, static fn() => '', $renderingContext)
;

$output57 .= '
          </div>
        </div>
      </div>
    </div>
  </div>
';

    return $output57;
}
/**
 * section Social
 */
public function section_9251ca0817813dad(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output99 = '';

$output99 .= '
  <ul class="nav gap-3 socialNavigation';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper
$output101 = '';

$output101 .= ' justify-content-';

$output101 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('alignment')]);

$array102 = [
'0' => $renderingContext->getVariableProvider()->getByPath('alignment'),
];

$expression103 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments100 = [
'then' => $output101,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression103(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array102)),
    $renderingContext
),
];

$output99 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments100, static fn() => '', $renderingContext)
;

$output99 .= '">
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure105 = function() use ($renderingContext) {
$output106 = '';

$output106 .= '
      <li class="nav-item">
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure108 = function() use ($renderingContext) {
$output112 = '';

$output112 .= '
          ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array114 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.media.0'),
];

$expression115 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments113 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression115(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array114)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output116 = '';

$output116 .= '
              ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\ImageViewHelper
$renderChildrenClosure118 = function() use ($renderingContext) {
return NULL;
};

$arguments117 = [
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

$output116 .= TYPO3\CMS\Fluid\ViewHelpers\ImageViewHelper::renderStatic($arguments117, $renderChildrenClosure118, $renderingContext);

$output116 .= '
            ';
return $output116;
},
'__else' => function() use ($renderingContext) {
$output119 = '';

$output119 .= '
              ';

$output119 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output119 .= '
            ';
return $output119;
},
];

$output112 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments113, static fn() => '', $renderingContext)
;

$output112 .= '
        ';
return $output112;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array110 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.target'),
];

$expression111 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments109 = [
'then' => $renderingContext->getVariableProvider()->getByPath('navItem.target'),
'else' => '_blank',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression111(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array110)),
    $renderingContext
),
];

$arguments107 = [
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
'target' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments109, static fn() => '', $renderingContext)
,
'class' => 'nav-link px-0',
];

$output106 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments107, $renderChildrenClosure108, $renderingContext);

$output106 .= '
      </li>
    ';
return $output106;
};

$arguments104 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('socialNavigation'),
'as' => 'navItem',
'iteration' => 'i',
];

$output99 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments104, $renderChildrenClosure105, $renderingContext);

$output99 .= '
  </ul>
';

    return $output99;
}
/**
 * section Meta
 */
public function section_29cb9ed501a840ba(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output120 = '';

$output120 .= '
  <ul class="nav justify-content-center border-bottom pb-3 mb-3" style="--bs-nav-link-color: var(--bs-gray-400);--bs-nav-link-hover-color:var(--bs-white)">
    ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure122 = function() use ($renderingContext) {
$output123 = '';

$output123 .= '
      <li class="nav-item">
        ';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper
$renderChildrenClosure125 = function() use ($renderingContext) {
$output130 = '';

$output130 .= '
          ';

$output130 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('navItem.title')]);

$output130 .= '
        ';
return $output130;
};
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array128 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.current'),
];

$expression129 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments127 = [
'then' => 'page',
'else' => '',
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression129(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array128)),
    $renderingContext
),
];

$array126 = [
'aria-current' => TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments127, static fn() => '', $renderingContext)
,
];

$arguments124 = [
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
'additionalAttributes' => $array126,
];

$output123 .= TYPO3\CMS\Fluid\ViewHelpers\Link\TypolinkViewHelper::renderStatic($arguments124, $renderChildrenClosure125, $renderingContext);

$output123 .= '
      </li>
      ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array132 = [
'0' => $renderingContext->getVariableProvider()->getByPath('navItem.data.uid'),
'1' => ' == ',
'2' => $renderingContext->getVariableProvider()->getByPath('dataProtectionDeclaration'),
];

$expression133 = function($context) {return (TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]) == TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node2"]));};

$arguments131 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression133(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array132)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output134 = '';

$output134 .= '
        <li class="nav-item">
          <div id="cookieBtn" class="nav-link px-2" role="button" tabindex="0" >
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\Format\RawViewHelper
$renderChildrenClosure136 = function() use ($renderingContext) {
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
$renderChildrenClosure138 = function() use ($renderingContext) {
return NULL;
};

$arguments137 = [
'id' => NULL,
'default' => NULL,
'arguments' => NULL,
'languageKey' => NULL,
'alternativeLanguageKeys' => NULL,
'key' => 'PrivacySettings',
'extensionName' => 'Template',
];
return TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper::renderStatic($arguments137, $renderChildrenClosure138, $renderingContext);
};

$arguments135 = [
'value' => NULL,
];

$output134 .= isset($arguments135['value']) ? $arguments135['value'] : $renderChildrenClosure136();

$output134 .= '
          </div>
        </li>
      ';
return $output134;
},
];

$output123 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments131, static fn() => '', $renderingContext)
;

$output123 .= '
    ';
return $output123;
};

$arguments121 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('metaNavigation'),
'as' => 'navItem',
'iteration' => 'position',
];

$output120 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments121, $renderChildrenClosure122, $renderingContext);

$output120 .= '
  </ul>
';

    return $output120;
}
/**
 * section Breadcrumb
 */
public function section_57776c41e9c42df5(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output139 = '';

$output139 .= '
  ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array141 = [
'0' => $renderingContext->getVariableProvider()->getByPath('breadcrumbNavigation'),
];

$expression142 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments140 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression142(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array141)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output143 = '';

$output143 .= '
      <nav aria-label="breadcrumb" id="breadcrumbNav" class="';

$output143 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('firstelementBgColor')]);

$output143 .= '">
        <div class="container">
          <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper
$renderChildrenClosure145 = function() use ($renderingContext) {
$output146 = '';

$output146 .= '
              ';
// Rendering ViewHelper TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper

$array148 = [
'0' => $renderingContext->getVariableProvider()->getByPath('breadcrumbItem.current'),
];

$expression149 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments147 = [
'then' => NULL,
'else' => NULL,
'condition' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression149(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array148)),
    $renderingContext
),
'__then' => function() use ($renderingContext) {
$output150 = '';

$output150 .= '
                  <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <span itemprop="name">';

$output150 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.title')]);

$output150 .= '</span>
                    <meta itemprop="position" content="';

$output150 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('iteration.cycle')]);

$output150 .= '" />
                  </li>
                ';
return $output150;
},
'__else' => function() use ($renderingContext) {
$output151 = '';

$output151 .= '
                  <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a class="text-decoration-none" itemscope itemtype="https://schema.org/WebPage" itemprop="item" itemid="';
// Rendering ViewHelper TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper
$renderChildrenClosure153 = function() use ($renderingContext) {
return $renderingContext->getVariableProvider()->getByPath('breadcrumbItem.link');
};

$array154 = [
'0' => 1,
];

$expression155 = function($context) {return TYPO3Fluid\Fluid\Core\Parser\BooleanParser::convertNodeToBoolean($context["node0"]);};

$arguments152 = [
'additionalParams' => '',
'language' => NULL,
'addQueryString' => false,
'addQueryStringExclude' => '',
'parameter' => 1,
'absolute' => TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::convertToBoolean(
    $expression155(TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode::gatherContext($renderingContext, $array154)),
    $renderingContext
),
];

$output151 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper::renderStatic($arguments152, $renderChildrenClosure153, $renderingContext)]);

$output151 .= '" href="';

$output151 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.link')]);

$output151 .= '" title="';

$output151 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.title')]);

$output151 .= '">
                      <span itemprop="name">';

$output151 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('breadcrumbItem.title')]);

$output151 .= '</span>
                    </a>
                    <meta itemprop="position" content="';

$output151 .= call_user_func_array( function ($var) { return (is_string($var) || (is_object($var) && method_exists($var, '__toString')) ? htmlspecialchars((string) $var, ENT_QUOTES) : $var); }, [$renderingContext->getVariableProvider()->getByPath('iteration.cycle')]);

$output151 .= '" />
                  </li>
                ';
return $output151;
},
];

$output146 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments147, static fn() => '', $renderingContext)
;

$output146 .= '
            ';
return $output146;
};

$arguments144 = [
'key' => NULL,
'reverse' => false,
'each' => $renderingContext->getVariableProvider()->getByPath('breadcrumbNavigation'),
'as' => 'breadcrumbItem',
'iteration' => 'iteration',
];

$output143 .= TYPO3Fluid\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments144, $renderChildrenClosure145, $renderingContext);

$output143 .= '
          </ol>
        </div>
      </nav>
    ';
return $output143;
},
'__else' => function() use ($renderingContext) {
return '
      <div id="breadcrumbNav"></div>
    ';
},
];

$output139 .= TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::renderStatic($arguments140, static fn() => '', $renderingContext)
;

$output139 .= '
';

    return $output139;
}
/**
 * Main Render function
 */
public function render(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
    $output156 = '';

$output156 .= '';

$output156 .= '


';

$output156 .= '';

$output156 .= '


';

$output156 .= '';

$output156 .= '


';

$output156 .= '';

$output156 .= '


';

$output156 .= '';

$output156 .= '

';

    return $output156;
}

}

#