<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type     = "block";
    //$id       = "c12da9c78b7ffe83129ed6d8411290fe";
    $path     = "_cascade/blocks/data/latin-wysiwyg";
    $siteName = "formats";
    //$asset = $service->read( $service->createId( $type, $id ) );
    $asset = $service->read( $service->createId( $type, $path, $siteName ) );
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