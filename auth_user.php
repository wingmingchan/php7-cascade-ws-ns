<?php
require_once( 'cascade_ws_ns7/ws_lib.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$type = $techName;
$url  = "$server/api/v1/";

if( $soap )
{
    $url = "$server/ws/services/AssetOperationService?wsdl";
}

$username = "admin";
$password = "admin";

if( $sandbox )
{
    $username = "wing";
    $password = "secret";
}

try
{
    $service = aohs\ServiceFactory::getService( $type, $url, $username, $password );
    $cascade = new a\Cascade( $service );
    $report  = new a\Report( $cascade );
    $admin   = new a\Administration( $cascade );
}
catch( e\ServerException $e )
{
    echo S_PRE . $e . E_PRE;
    throw $e;
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