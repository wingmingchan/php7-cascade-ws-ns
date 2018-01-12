<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/2/2018 Added REST code to toStdClass.
  * 12/26/2017 Added $service requirement in constructor.
  * 7/18/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 9/26/2016 Changed signatures of addWorkflowDefinition, added remove methods and aliases.
  * 6/23/2015 Added getInheritedWorkflowDefinitions, setInheritWorkflows.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>WorkflowSettings</code> object represents a <code>workflowSettings</code> property associated with an asset (e.g., a folder).</p>
<h2>Structure of <code>workflowSettings</code></h2>
<pre>SOAP:
workflowSettings
  identifier (identifier of the associated asset; e.g., a folder)
    id
    path
      path
      siteId
      siteName
    type (e.g., folder)
    recycled
  workflowDefinitions
    assetIdentifier
      id
      path
        path
        siteId
        siteName
      type (workflowdefinition)
      recycled
  inheritWorkflows
  requireWorkflow
  inheritedWorkflowDefinitions
    assetIdentifier
      id
      path
        path
        siteId
        siteName
      type (workflowdefinition)
      recycled
      
REST:
workflowSettings
  identifier
    id
    path
      path
      siteId
      siteName
    type (e.g., folder)
    recycled
  workflowDefinitions (array of stdClass)
    id
    path
      path
      siteId
      siteName
    type (workflowdefinition)
    recycled
  inheritedWorkflowDefinitions (array of stdClass)
    id
    path
      path
      siteId
      siteName
    type (workflowdefinition)
    recycled
  inheritWorkflows
  requireWorkflow
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "workflowSettings" ),
        array( "getComplexTypeXMLByName" => "assetIdentifiers" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/workflow_settings.php">workflow_settings.php</a></li></ul></postscript>
</documentation>
*/class WorkflowSettings extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/    public function __construct( 
        \stdClass $wfs_std=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
        
        if( $wfs_std == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . "The stdClass object cannot be NULL." . E_SPAN );
        }
        
        $this->identifier = new Identifier( $wfs_std->identifier );
        
        $this->workflow_definitions = array();
        
        if( isset( $wfs_std->workflowDefinitions ) )
        {
            if( $this->service->isSoap() &&
                isset( $wfs_std->workflowDefinitions->assetIdentifier ) )
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
            elseif( $this->service->isRest() )
            {
                foreach( $wfs_std->workflowDefinitions as $asset_identifier )
                {
                    $this->workflow_definitions[] = new Identifier( $asset_identifier );
                }
            }
        }    
        
        $this->inherit_workflows = $wfs_std->inheritWorkflows;
        $this->require_workflow  = $wfs_std->requireWorkflow;
        
        $this->inherited_workflow_definitions = array();
        
        if( isset( $wfs_std->inheritedWorkflowDefinitions ) )
        { 
            if( $this->service->isSoap() &&
                isset( $wfs_std->inheritedWorkflowDefinitions->assetIdentifier ) )
            {
                $asset_identifiers = $wfs_std->inheritedWorkflowDefinitions->
                    assetIdentifier;
            
                if( !is_array( $asset_identifiers ) )
                {
                    $asset_identifiers = array( $asset_identifiers );
                }
            
                foreach( $asset_identifiers as $asset_identifier )
                {
                    $this->inherited_workflow_definitions[] = 
                        new Identifier( $asset_identifier );
                }
            }
            elseif( $this->service->isRest() )
            {
                foreach( $wfs_std->inheritedWorkflowDefinitions as $asset_identifier )
                {
                    $this->inherited_workflow_definitions[] =
                        new Identifier( $asset_identifier );
                }
            }
        }
    }
    
/**
<documentation><description>Adds the workflow definition to the array of <code>workflowDefinitions</code>,
and returns the calling object.</description>
<example>if( !$ws->hasWorkflowDefinition( $wd_id ) )
    $ws->addWorkflowDefinition( $wd );</example>
<return-type>Property</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function addWorkflowDefinition( a\WorkflowDefinition $wd ) : Property
    {
        if( $this->hasWorkflowDefinition( $wd->getId() ) )
        {
            return $this;
        }
        
        $this->workflow_definitions[] = new Identifier(
            $wd->getIdentifier()
        );
        return $this;
    }
    
/**
<documentation><description>Returns an array of <code>Identifier</code> of inherited workflow defintions.</description>
<example>u\DebugUtility::dump( $ws->getInheritedWorkflowDefinitions() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getInheritedWorkflowDefinitions() : array
    {
        return $this->inherited_workflow_definitions;
    }
    
/**
<documentation><description>Returns <code>inheritWorkflows</code>.</description>
<example>u\DebugUtility::out( u\StringUtility::boolToString( $ws->getInheritWorkflows() ) );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getInheritWorkflows() : bool
    {
        return $this->inherit_workflows;
    }
    
/**
<documentation><description>Returns <code>requireWorkflow</code>.</description>
<example>u\DebugUtility::out( u\StringUtility::boolToString( $ws->getRequireWorkflow() ) );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getRequireWorkflow() : bool
    {
        return $this->require_workflow;
    }
    
/**
<documentation><description>Returns an array of <code>Identifier</code> objects.</description>
<example>u\DebugUtility::dump( $ws->getWorkflowDefinitions() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflowDefinitions() : array
    {
        return $this->workflow_definitions;
    }
    
/**
<documentation><description>Returns a bool, indicating whether the id (string) is a valid
id of a workflow definition in <code>workflowDefinitions</code>.</description>
<example>// toggle
if( $ws->hasWorkflowDefinition( $wd_id ) )
    $ws->removeWorkflowDefinition( $wd );
else
    $ws->addWorkflowDefinition( $wd );
</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasWorkflowDefinition( string $id ) : bool
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
    
/**
<documentation><description>Removes the workflow definition from the array of
<code>workflowDefinitions</code>, and returns the calling object.</description>
<example>$ws->removeWorkflowDefinition( $wd );</example>
<return-type>Property</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function removeWorkflowDefinition( a\WorkflowDefinition $wd ) : Property
    {
        $temp = array();
        
        foreach( $this->workflow_definitions as $def )
        {
            if( $def->getId() != $wd->getId() )
            {
                $temp[] = $def;
            }
        }
        
        $this->workflow_definitions = $temp;
        
        return $this;
    }
    
    
/**
<documentation><description>Sets <code>inheritWorkflows</code>, updates
<code>inheritedWorkflowDefinitions</code> if necessary, and returns the calling object.</description>
<example>$ws->setInheritWorkflows( false )->setRequireWorkflow( false );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setInheritWorkflows( bool $bool ) : Property
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
    
/**
<documentation><description>Sets <code>requireWorkflow</code>, and returns the
calling object.</description>
<example>$ws->setInheritWorkflows( false )->setRequireWorkflow( false );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setRequireWorkflow( bool $bool ) : Property
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->require_workflow = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $ws->toStdClass() );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj = new \stdClass();
        $obj->identifier = $this->identifier->toStdClass();
        
        if( $this->service->isSoap() )
            $obj->workflowDefinitions = new \stdClass();
        elseif( $this->service->isRest() )
            $obj->workflowDefinitions = array();
            
        
        if( count( $this->workflow_definitions ) > 0 )
        {
            if( count( $this->workflow_definitions ) == 1 )
            {
                if( $this->service->isSoap() )
                    $obj->workflowDefinitions->assetIdentifier =
                        $this->workflow_definitions[ 0 ]->toStdClass();
                elseif( $this->service->isRest() )
                    $obj->workflowDefinitions[] =
                        $this->workflow_definitions[ 0 ]->toStdClass();
            }
            else
            {
                if( $this->service->isSoap() )
                    $obj->workflowDefinitions->assetIdentifier = array();
                
                foreach( $this->workflow_definitions as $def )
                {
                    if( $this->service->isSoap() )
                        $obj->workflowDefinitions->assetIdentifier[] = $def->toStdClass();
                    elseif( $this->service->isRest() )
                        $obj->workflowDefinitions[] = $def->toStdClass();
                }
            }
        }
        
        $obj->inheritWorkflows = $this->inherit_workflows;
        $obj->requireWorkflow  = $this->require_workflow;
        
        if( $this->service->isSoap() )
            $obj->inheritedWorkflowDefinitions = new \stdClass();
        elseif( $this->service->isRest() )
            $obj->inheritedWorkflowDefinitions = array();
        
        if( count( $this->inherited_workflow_definitions ) > 0 )
        {
            if( count( $this->inherited_workflow_definitions ) == 1 )
            {
                if( $this->service->isSoap() )
                    $obj->inheritedWorkflowDefinitions->assetIdentifier =
                        $this->inherited_workflow_definitions[ 0 ]->toStdClass();
                elseif( $this->service->isRest() )
                    $obj->inheritedWorkflowDefinitions = array(
                        $this->inherited_workflow_definitions[ 0 ]->toStdClass()
                    );
            }
            else
            {
                if( $this->service->isSoap() )
                    $obj->inheritedWorkflowDefinitions->assetIdentifier = array();
                
                foreach( $this->inherited_workflow_definitions as $def )
                {
                    if( $this->service->isSoap() )
                        $obj->inheritedWorkflowDefinitions->assetIdentifier[] =
                            $def->toStdClass();
                    elseif( $this->service->isRest() )
                        $obj->inheritedWorkflowDefinitions[] = $def->toStdClass();
                }
            }
        }
        
        return $obj;
    }

/**
<documentation><description>Sets <code>inheritWorkflows</code> to <code>false</code>,
empties <code>inheritedWorkflowDefinitions</code>, and returns the calling object.</description>
<example>$ws->unsetInheritWorkflows();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function unsetInheritWorkflows() : Property
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