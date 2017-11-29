<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = "block";
    $id    = "089c28d98b7ffe83785cac8a79fe2145";
    
    $reply = $service->copy(
      $service->createId( $type, $id ), // block
      $service->createId( "folder", "c12dcef18b7ffe83129ed6d85960d93d" ), // folder
      "new-hello" // new name
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