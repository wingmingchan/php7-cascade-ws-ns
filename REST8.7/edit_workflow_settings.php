<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $type   = "folder";
    $id     = "c12dcf268b7ffe83129ed6d81d964c24";
    $reply  = 
        $service->readWorkflowSettings( $service->createId( $type, $id ) );
    $workflowDefinitions = $reply->workflowSettings->workflowDefinitions;
    $inheritedWorkflowDefinitions =
        $reply->workflowSettings->inheritedWorkflowDefinitions;
    $reply = $service->editWorkflowSettings(
        $service->createId( $type, $id ),
        $workflowDefinitions,
        $inheritedWorkflowDefinitions,
        false, // inheritWorkflows
        false // requireWorkflow
        // false and false by default for applyInheritWorkflowsToChildren and
        // applyRequireWorkflowToChildren
    );    
        
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