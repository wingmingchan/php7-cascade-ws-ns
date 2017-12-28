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
    $block = $service->getAsset(
        a\DataBlock::TYPE, "9d9336e18b7ffe8353cc17e99daf87e1" );
        
    $block->setXhtml( "<p>This is meaningless!</p>" )->
        edit()->dump();
    $block->replaceXhtmlByPattern(
        "/" . "<" . "p>([^<]+)<\/p>/", 
        "<div class='text_red'>$1</div>" )->edit()->dump();
    
    echo u\StringUtility::boolToString( $block->searchXhtml( "hello" ) ), BR;
    echo u\StringUtility::getCoalescedString( $block->getXhtml() ), BR;
    $block->displayXhtml();
    echo u\StringUtility::boolToString( $block->hasStructuredData() ), BR;
/*/ /*/  
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