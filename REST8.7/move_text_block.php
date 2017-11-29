<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type     = "block";
    $id       = "089c28d98b7ffe83785cac8a79fe2145";
    
    // move the block to a new folder
    $reply = $service->move( $service->createId( $type, $id ),
        $service->createId( "folder", "c12dce3c8b7ffe83129ed6d8f4f9b820" ) // new parent
    );
    u\DebugUtility::dump( $reply );
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