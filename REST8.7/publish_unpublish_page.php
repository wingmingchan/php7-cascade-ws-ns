<?php
require_once( 'auth_rest_web.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = "page";
    $id    = "9a1416488b7f08ee5d439b31921d08b6";
    //$reply = $service->publish( $service->createId( $type, $id ) );
    //u\DebugUtility::dump( $reply );
    
    $reply = $service->publish(
    	$service->createId( $type, $id ), // page
    	array(
    		$service->createId( "destination", "c34b58ca8b7f08ee4fe76bb83ba1613b" ),
    		$service->createId( "destination", "c34d2a868b7f08ee4fe76bb87c352c01" )
    	)
    );
    /*
    $reply = $service->unpublish(
    	$service->createId( $type, $id ), // page
    	array(
    		$service->createId( "destination", "c34b58ca8b7f08ee4fe76bb83ba1613b" ),
    		$service->createId( "destination", "c34d2a868b7f08ee4fe76bb87c352c01" )
    	)
    );
    */
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