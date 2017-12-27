<?php
require_once( 'cascade_ws_ns7/ws_lib.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$url     = "http://www.mydomain.edu:1234/api/v1/";
$auth    = new \stdClass();
$auth->u = "username";
$auth->p = "password";

try
{
    $service = new aohs\AssetOperationHandlerService( $url, $auth );
    $cascade = new a\Cascade( $service );
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