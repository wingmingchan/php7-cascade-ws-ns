<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/23/2015 Added getInheritedWorkflowDefinitions, setInheritWorkflows.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_asset as a;

class WorkflowSettings extends Property
{
    public function __construct( 
        \stdClass $wfs_std=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( $wfs_std == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . "The stdClass object cannot be NULL." . E_SPAN );
        }
        
        if( $service == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . "The service object cannot be NULL." . E_SPAN );
        }
        
        $this->identifier = new Identifier( $wfs_std->identifier );
        
        $this->workflow_definitions = array();
        
        if( isset( $wfs_std->workflowDefinitions ) && isset( $wfs_std->workflowDefinitions->assetIdentifier ) )
        {
            $asset_identifiers = $wfs_std->workflowDefinitions->assetIdentifier;
            
            if( !is_array( $asset_identifiers ) )
            {
                $asset_identifiers = array( $asset_identifiers );
            }
            
            foreach( $asset_identifiers as $asset_identifier )
            {
                $this->workflow_definitions[] = new Identifier( $asset_identifier );
            }
        }
        
        $this->inherit_workflows = $wfs_std->inheritWorkflows;
        $this->require_workflow  = $wfs_std->requireWorkflow;
        
        $this->inherited_workflow_definitions = array();
        
        if( isset( $wfs_std->inheritedWorkflowDefinitions ) && 
            isset( $wfs_std->inheritedWorkflowDefinitions->assetIdentifier ) )
        {
            $asset_identifiers = $wfs_std->inheritedWorkflowDefinitions->assetIdentifier;
            
            if( !is_array( $asset_identifiers ) )
            {
                $asset_identifiers = array( $asset_identifiers );
            }
            
            foreach( $asset_identifiers as $asset_identifier )
            {
                $this->inherited_workflow_definitions[] = new Identifier( $asset_identifier );
            }
        }
        $this->service = $service;
    }
    
    public function addWorkflowDefinition( Identifier $id )
    {
        if( $id->getType() != c\T::WORKFLOWDEFINITION )
        {
            throw new \Exception(
                S_SPAN . "The identifier is unacceptable." . E_SPAN );
        }
        if( $this->hasWorkflowDefinition( $id->getId() ) )
        {
            return $this;
        }
        
        $this->workflow_definitions[] = $id;
        return $this;
    }
    
    public function getInheritedWorkflowDefinitions()
    {
        return $this->inherited_workflow_definitions;
    }
    
    public function getInheritWorkflows()
    {
        return $this->inherit_workflows;
    }
    
    public function getRequireWorkflows()
    {
        return $this->require_workflow;
    }
    
    public function getWorkflowDefinitions()
    {
        return $this->workflow_definitions;
    }
    
    public function hasWorkflowDefinition( $id )
    {
        foreach( $this->workflow_definitions as $def )
        {
            if( $def->getId() == $id )
            {
                return true;
            }
        }
        return false;
    }
    
    // remove workflow?
    
    public function setInheritWorkflows( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        
        // already set
        if( $this->inherit_workflows == $bool )
            return $this;
        
        if( $bool )
        {
            $this->inherit_workflows = $bool;

            $folder_id = $this->identifier->getId();
            $folder    = a\Asset::getAsset( $this->service, a\Folder::TYPE, $folder_id );
            $parent    = $folder->getParentContainer();
            
            if( isset( $parent ) )
            {
                $parent_settings = $parent->getWorkflowSettings();
                $parent_wds      = $parent_settings->getWorkflowDefinitions();
                
                if( is_array( $parent_wds ) && count( $parent_wds ) > 0 )
                {
                    foreach( $parent_wds as $parent_wd )
                    {
                        $this->inherited_workflow_definitions[] = $parent_wd;
                    }
                }
            }
        }
        else
        {
            $this->unsetInheritWorkflows();
        }
        
        return $this;
    }
    
    public function setRequireWorkflow( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->require_workflow = $bool;
        return $this;
    }
    
    public function toStdClass()
    {
        $obj = new \stdClass();
        $obj->identifier = $this->identifier->toStdClass();
        
        $obj->workflowDefinitions = new \stdClass();
        
        if( count( $this->workflow_definitions ) > 0 )
        {
            if( count( $this->workflow_definitions ) == 1 )
            {
                $obj->workflowDefinitions->assetIdentifier =
                    $this->workflow_definitions[ 0 ]->toStdClass();
            }
            else
            {
                $obj->workflowDefinitions->assetIdentifier = array();
                
                foreach( $this->workflow_definitions as $def )
                {
                    $obj->workflowDefinitions->assetIdentifier[] = $def->toStdClass();
                }
            }
        }
        
        $obj->inheritWorkflows = $this->inherit_workflows;
        $obj->requireWorkflow  = $this->require_workflow;
        
        $obj->inheritedWorkflowDefinitions = new \stdClass();
        
        if( count( $this->inherited_workflow_definitions ) > 0 )
        {
            if( count( $this->inherited_workflow_definitions ) == 1 )
            {
                $obj->inheritedWorkflowDefinitions->assetIdentifier =
                    $this->inherited_workflow_definitions[ 0 ]->toStdClass();
            }
            else
            {
                $obj->inheritedWorkflowDefinitions->assetIdentifier = array();
                
                foreach( $this->inherited_workflow_definitions as $def )
                {
                    $obj->inheritedWorkflowDefinitions->assetIdentifier[] = $def->toStdClass();
                }
            }
        }
        
        return $obj;
    }

    public function unsetInheritWorkflows()
    {
        $this->inherit_workflows = false;
        $this->inherited_workflow_definitions = array();
        return $this;
    }

    private $identifier;
    private $workflow_definitions;
    private $inherit_workflows;
    private $require_workflow;
    private $inherited_workflow_definitions;
    private $service;
}
?>