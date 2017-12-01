<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = "page";
    $id    = "c12eb9978b7ffe83129ed6d80132aa29";
    u\DebugUtility::dump(
        $service->checkIn( $service->createId( $type, $id ), "testing" ) );
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