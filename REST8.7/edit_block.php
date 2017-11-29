<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type  = "block";
    $id    = "c12da9c78b7ffe83129ed6d8411290fe";
    $asset = $service->read( $service->createId( $type, $id ) );

    if( $service->isSuccessful() )
    {
        $text = 
            $asset->xhtmlDataDefinitionBlock->structuredData->structuredDataNodes[ 1 ]->
            structuredDataNodes[ 0 ]->text;
        $text = $text . "<p>Text appended.</p>";
        $asset->xhtmlDataDefinitionBlock->structuredData->structuredDataNodes[ 1 ]->
            structuredDataNodes[ 0 ]->text = $text;
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