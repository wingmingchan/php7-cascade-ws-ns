<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = "block";
    $id    = "c12d973c8b7ffe83129ed6d886deba0f";
    $asset = $service->read( $service->createId( $type, $id ) );
    u\DebugUtility::dump( $asset );
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