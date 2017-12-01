<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $reply = $service->performWorkflowTransition(
        "1238fd1e8b7ffe83785cac8aa6c35877", "finished", "No comment" );
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