<?php
require_once( 'auth_test.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    $ms = $cascade->getAsset(
        a\MetadataSet::TYPE, "4624645d8b7ffe831131a667a82cb3b5" );
    $ms->dump();
    echo u\StringUtility::boolToString( $ms->getAuthorFieldRequired() ), BR;
    u\DebugUtility::dump( $ms->getDynamicMetadataFieldDefinitionNames() );
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