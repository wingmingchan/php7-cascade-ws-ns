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
        a\FeedBlock::TYPE, "c12d93f48b7ffe83129ed6d8b74902e1" )->dump();
    //echo u\XmlUtility::replaceBrackets( $block->getFeedXML() ) . BR;
    
    $block->setFeedURL( "http://www.upstate.edu/web-services/_extra/internal-nav.php" )-> 
        edit()->dump();
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