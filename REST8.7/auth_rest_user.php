<?php
require_once( 'constants.php' );
require_once( 'AssetOperationHandlerService.class.php' );
require_once( 'DebugUtility.class.php' );

use cascade_ws_AOHS    as aohs;

$url     = "http://www.mydomain.edu:1234/api/v1/";
$auth    = new \stdClass();
$auth->u = "username";
$auth->p = "password";

try
{
    $service = new aohs\AssetOperationHandlerService( $url, $auth );
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