<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type     = "block";
    $id       = "089c28d98b7ffe83785cac8a79fe2145";
    
    // rename the block to hello
    $reply = $service->move( $service->createId( $type, $id ), NULL, "hello" );
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