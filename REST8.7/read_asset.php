<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = c\T::ASSET_FACTORY_CONTAINER;
    $id    = "ec1c8bc98b7f0856002a5e119ce4b1e4";
    $asset = $service->read( $service->createId( $type, $id ) );
    u\DebugUtility::dump( $asset );
    u\DebugUtility::dump( json_encode( $asset ) );
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

