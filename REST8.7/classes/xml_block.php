<?php
require_once( 'auth_test.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    $block = $cascade->getAsset(
        a\XmlBlock::TYPE, "985da4158b7ffe8353cc17e9ffaa5315" )->dump();
    echo u\XmlUtility::replaceBrackets( $block->getXML() ) . BR;
    
    $block->setXML( "<code>#set( \$x = 1 )</code>" )->edit()->dump();
}
catch( \Exception $e ) 
{
    echo S_PRE . $e . E_PRE; 
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE; 
}
?>