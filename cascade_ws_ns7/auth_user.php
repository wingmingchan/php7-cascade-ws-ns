<?php 
require_once( 'cascade_ws_ns/ws_lib.php' );

use cascade_ws_AOHS as aohs;
use cascade_ws_asset as a;
use cascade_ws_exception as e;

$wsdl = "http://localhost:8080/ws/services/AssetOperationService?wsdl";
$auth           = new \stdClass();
$auth->username = $_SERVER[ 'PHP_AUTH_USER' ];
$auth->password = $_SERVER[ 'PHP_AUTH_PW' ];

/*
// uncomment this if needed; also uncomment ", $context" below
$context =
	array( 'trace' => 1,
    	'proxy_host' => "111.222.33.44",
    	'proxy_port' => "80",
    	'stream_context' => stream_context_create(
      		array( 'https' =>
        		array( 'proxy' => "tcp:// 111.222.33.44:80", 'request_fulluri' => true )
    		)
    	)
	);
*/
try
{
    // set up global objects
    $service = new aohs\AssetOperationHandlerService( $wsdl, $auth /*, $context */ );
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
