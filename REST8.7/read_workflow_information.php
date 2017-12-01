<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type   = "page";
    $id     = "c12deeb18b7ffe83129ed6d85a5d3d95";
    $reply  = 
        $service->readWorkflowInformation( $service->createId( $type, $id ) );
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