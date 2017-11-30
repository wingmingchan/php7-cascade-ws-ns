<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $searchInfo = new \stdClass();

    $searchInfo->searchTerms = "group";
    $searchInfo->siteId      = "61885ac08b7ffe8377b637e83a86cca5";
    $searchInfo->searchTypes = array( "format" );
    
    $reply = $service->search( $searchInfo );
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