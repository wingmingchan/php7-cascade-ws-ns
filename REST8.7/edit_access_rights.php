<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
	$type   = "folder";
    $id     = "c12d8d0d8b7ffe83129ed6d86dd9f853";
    $reply  = $service->readAccessRights( $service->createId( $type, $id ) );
    $params = $reply->accessRightsInformation;
    unset( $params->identifier );
    
    $entry        = new \stdClass();
    $entry->level = "read";
    $entry->type  = "group";
    $entry->name  = "CWT-Designers";
    
    $params->aclEntries[] = $entry;
    $params->allLevel     = "none";
    $reply = $service->editAccessRights( $service->createId( $type, $id ), $params );
    
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