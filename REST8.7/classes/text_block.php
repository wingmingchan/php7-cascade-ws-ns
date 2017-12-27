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
        a\TextBlock::TYPE, "c12d973c8b7ffe83129ed6d886deba0f" );
    u\DebugUtility::out( $block->getText() );
    echo $block->getCreatedBy(), BR;
    
    $block->setText( "Some new text for REST" )->edit()->dump();
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