<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = "site";
    $id    = "c12d8c498b7ffe83129ed6d81ea4076a";
    $asset = $service->read( $service->createId( $type, $id ) );

    if( $service->isSuccessful() )
    {
        $asset->$type->namingRuleAssets = array( "file", "template", "page" );
        $service->edit( $asset );
    }
    u\DebugUtility::dump( $service->read( $service->createId( $type, $id ) ) );
/*//*/
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