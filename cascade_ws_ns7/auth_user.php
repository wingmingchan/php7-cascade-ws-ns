<?php 
require_once( 'cascade_ws_ns/ws_lib.php' );

use cascade_ws_AOHS as aohs;
use cascade_ws_asset as a;
use cascade_ws_exception as e;

$wsdl = "http://localhost:8080/ws/services/AssetOperationService?wsdl";
$auth           = new \stdClass();
$auth->username = $_SERVER['PHP_AUTH_USER'];
$auth->password = $_SERVER['PHP_AUTH_PW'];

$context = stream_context_create( [
    'ssl' => [
    	// set some SSL/TLS specific options
    	'verify_peer'       => false,
    	'verify_peer_name'  => false,
    	'allow_self_signed' => true
    ]
]);

try
{
    // set up global objects
    $service = new aohs\AssetOperationHandlerService( $wsdl, $auth );
    $cascade = new a\Cascade( $service );
    $report  = new a\Report( $cascade );
    $eval    = new u\EvalUtility();

    // create an asset for one-time use
    $asset = new \stdClass();
}
catch( e\ServerException $e )
{
    echo S_PRE . $e . E_PRE;
    throw $e;
}
?>
