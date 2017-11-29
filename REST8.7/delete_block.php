<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = "block";
    $id    = "080f19658b7ffe83785cac8ab2d9e4a4";
    u\DebugUtility::dump( $service->delete( $service->createId( $type, $id ) ) );
/*//*/
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