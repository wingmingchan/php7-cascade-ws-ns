<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 1/3/2018 Added code to test for NULL.
  * 11/27/2017 Added move-related methods.
  * 6/30/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
  * 5/28/2015 Added namespaces.
  * 7/17/2014 Fixed a bug in setXml.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>WorkflowDefinition</code> object represents a workflow definition asset.</p>
<h2>Structure of <code>workflowDefinition</code></h2>
<pre>workflowDefinition
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  applicableGroups
  copy
  create
  delete
  edit
  namingBehavior
  xml
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "workflowDefinition" ),
        array( "getSimpleTypeXMLByName"  => "workflowNamingBehavior" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/workflow_definition.php">workflow_definition.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/workflowdefinition/b408830e8b7ffe835446afac3b3923ba

{
  "asset":{
    "workflowDefinition":{
      "copy":false,
      "create":false,
      "delete":true,
      "edit":false,
      "move":false,
      "namingBehavior":"auto-name",
      "xml":"\u003csystem-workflow-definition name\u003d\"Unpublish and Delete\" initial-step\u003d\"initialize\" \u003e\n    \u003ctriggers\u003e\n        \u003ctrigger name\u003d\"AssignStepIfUser\" class\u003d\"com.cms.workflow.function.AssignStepIfUser\" /\u003e\n        \u003ctrigger name\u003d\"AssignToGroupOwningAsset\" class\u003d\"com.cms.workflow.function.AssignToGroupOwningAsset\" /\u003e\n        \u003ctrigger name\u003d\"AssignToSpecifiedGroup\" class\u003d\"com.cms.workflow.function.AssignToSpecifiedGroup\" /\u003e\n        \u003ctrigger name\u003d\"AssignToWorkflowOwner\" class\u003d\"com.cms.workflow.function.AssignToWorkflowOwner\" /\u003e\n        \u003ctrigger name\u003d\"CopyFolder\" class\u003d\"com.cms.workflow.function.CopyFolder\" /\u003e\n        \u003ctrigger name\u003d\"com.cms.workflow.function.CreateNewWorkflowTrigger\" class\u003d\"com.cms.workflow.function.CreateNewWorkflowTrigger\" /\u003e\n        \u003ctrigger name\u003d\"Delete\" class\u003d\"com.cms.workflow.function.Delete\" /\u003e\n        \u003ctrigger name\u003d\"UnpublishAndDelete\" class\u003d\"com.cms.workflow.function.DeleteAndUnpublish\" /\u003e\n        \u003ctrigger name\u003d\"DeleteParentFolder\" class\u003d\"com.cms.workflow.function.DeleteParentFolderTrigger\" /\u003e\n        \u003ctrigger name\u003d\"Email\" class\u003d\"com.cms.workflow.function.EmailProvider\" /\u003e\n        \u003ctrigger name\u003d\"Merge\" class\u003d\"com.cms.workflow.function.Merge\" /\u003e\n        \u003ctrigger name\u003d\"PreserveCurrentUser\" class\u003d\"com.cms.workflow.function.PreserveCurrentUser\" /\u003e\n        \u003ctrigger name\u003d\"PublishContainingPublishSet\" class\u003d\"com.cms.workflow.function.PublishContainingPublishSetTrigger\" /\u003e\n        \u003ctrigger name\u003d\"PublishParentFolder\" class\u003d\"com.cms.workflow.function.PublishParentFolderTrigger\" /\u003e\n        \u003ctrigger name\u003d\"PublishSet\" class\u003d\"com.cms.workflow.function.PublishSetTrigger\" /\u003e\n        \u003ctrigger name\u003d\"Publish\" class\u003d\"com.cms.workflow.function.Publisher\" /\u003e\n        \u003ctrigger name\u003d\"Version\" class\u003d\"com.cms.workflow.function.Version\" /\u003e\n        \u003ctrigger name\u003d\"CreateNewWorkflow\" class\u003d\"com.cms.workflow.function.CreateNewWorkflowsTrigger\" /\u003e\n    \u003c/triggers\u003e\n    \u003csteps\u003e\n        \u003cstep type\u003d\"system\" identifier\u003d\"initialize\" label\u003d\"Initialization\" \u003e\n            \u003cactions\u003e\n                \u003caction identifier\u003d\"publish\" label\u003d\"Publish\" move\u003d\"forward\" \u003e\n                    \u003ctrigger name\u003d\"UnpublishAndDelete\" /\u003e\n                \u003c/action\u003e\n            \u003c/actions\u003e\n        \u003c/step\u003e\n        \u003cstep type\u003d\"system\" identifier\u003d\"finished\" label\u003d\"Finished\" /\u003e\n    \u003c/steps\u003e\n    \u003cnon-ordered-steps/\u003e\n\u003c/system-workflow-definition\u003e",
      "parentContainerId":"fd2770ba8b7f08560159f3f03223b508",
      "parentContainerPath":"/",
      "path":"Unpublish and Delete",
      "siteId":"fd27691f8b7f08560159f3f02754e61d",
      "siteName":"_common",
      "name":"Unpublish and Delete",
      "id":"b408830e8b7ffe835446afac3b3923ba"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre>
</postscript>
</documentation>
*/
class WorkflowDefinition extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::WORKFLOWDEFINITION;
    
    const NAMING_BEHAVIOR_AUTO       = c\T::AUTO_NAME;
    const NAMING_BEHAVIOR_DEFINITION = c\T::NAMEOFDEFINITION;
    const NAMING_BEHAVIOR_BLANK      = 'empty';
    
/**
<documentation><description><p>The constructor, overriding the parent method to parse the XML definition.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->ordered_steps        = array();
        $this->non_ordered_steps    = array();
        $this->ordered_step_map     = array();
        $this->non_ordered_step_map = array();
        $this->triggers             = array();
        
        $simple_xml         = simplexml_load_string( $this->getProperty()->xml );
        $def_attr           = $simple_xml->attributes();
        $this->name         = $def_attr->name;
        $this->initial_step = $def_attr->{ "initial-step" };
        
        $count = count( $simple_xml->triggers->trigger );
        
        if( $count > 0 )
        {
            foreach( $simple_xml->triggers->trigger as $trigger )
            {
                $this->triggers[] = new p\TriggerDefinition( $trigger );
            }
        }
        
        if( $simple_xml->steps )
        {
            foreach( $simple_xml->steps->step as $step )
            {
                $new_step = new p\StepDefinition( $step );
                $this->ordered_steps[] = $new_step;
                $this->ordered_step_map[ $new_step->getIdentifier() ] = $new_step;
            }
        }
        
        if( $simple_xml->{ "non-ordered-steps" } )
        {
            foreach( $simple_xml->{ "non-ordered-steps" }->step as $step )
            {
                $new_step = new p\StepDefinition( $step );
                $this->non_ordered_steps[] = $new_step;
                $this->non_ordered_step_map[ $new_step->getIdentifier() ] = $new_step;
            }
        }
    }
    
/**
<documentation><description><p>Adds a group name to <code>applicableGroups</code> and returns the calling object.</p></description>
<example>$wfd->addGroup( $cascade->getAsset( a\Group::TYPE, 'demo' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addGroup( Group $g ) : Asset
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }
    
        $group_name   = $g->getName();
        
        if( isset( $this->getProperty()->applicableGroups ) )
            $group_string = $this->getProperty()->applicableGroups;
        else
            $group_string = "";
        
        $group_array  = explode( ';', $group_string );
        
        if( !in_array( $group_name, $group_array ) )
        {
            $group_array[] = $group_name;
        }
        
        $group_string = implode( ';', $group_array );
        $this->getProperty()->applicableGroups = $group_string;
        return $this;
    }
    
/**
<documentation><description><p>Displays <code>xml</code> and returns the calling object.</p></description>
<example>$wfd->displayXml();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayXml() : Asset
    {
        $xml_string = u\XMLUtility::replaceBrackets( 
            $this->getProperty()->xml );
        
        echo S_H2 . "XML" . E_H2 .
             S_PRE . $xml_string . E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p>Overriding the parent method, dumps the <code>workflowDefinition</code> property,
displays the XML definition, and returns the calling object.</p></description>
<example>$wfd->dump();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function dump( bool $formatted=true ) : Asset
    {
        parent::dump( $formatted );        
        $this->displayXml();
        return $this;
    }
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
</documentation>
*/
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $asset                                    = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
        
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        return $this->reloadProperty();
    }

/**
<documentation><description><p>Returns <code>applicableGroups</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $wfd->getApplicableGroups() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getApplicableGroups() 
    {
        if( isset( $this->getProperty()->applicableGroups ) )
            return $this->getProperty()->applicableGroups;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>copy</code>.</p></description>
<example>echo $wfd->u\StringUtility::boolToString( $wfd->getCopy() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getCopy() : bool
    {
        return $this->getProperty()->copy;
    }
    
/**
<documentation><description><p>Returns <code>create</code>.</p></description>
<example>echo $wfd->u\StringUtility::boolToString( $wfd->getCreate() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getCreate() : bool
    {
        return $this->getProperty()->create;
    }
    
/**
<documentation><description><p>Returns <code>delete</code>.</p></description>
<example>echo $wfd->u\StringUtility::boolToString( $wfd->getDelete() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getDelete() : bool
    {
        return $this->getProperty()->delete;
    }
    
/**
<documentation><description><p>Returns <code>edit</code>.</p></description>
<example>echo $wfd->u\StringUtility::boolToString( $wfd->getEdit() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getEdit() : bool
    {
        return $this->getProperty()->edit;
    }
    
/**
<documentation><description><p>Returns <code>move</code>.</p></description>
<example>echo $wfd->u\StringUtility::boolToString( $wfd->getMove() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getMove() : bool
    {
        return $this->getProperty()->move;
    }
    
/**
<documentation><description><p>Overriding the parent method, returns an <code>Identifier</code> object,
representing the workflow definition. This method is used by <code>WorkflowSettings</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifier() : \stdClass
    {
        $obj                 = new \stdClass();
        $obj->id             = $this->getProperty()->id;
        $obj->path           = new \stdClass();
        $obj->path->path     = $this->getProperty()->path;
        $obj->path->siteId   = $this->getProperty()->siteId;
        $obj->path->siteName = $this->getProperty()->siteName;
        $obj->type           = c\T::WORKFLOWDEFINITION;
        $obj->recycled       = false;
        return $obj;
    }
    
/**
<documentation><description><p>Returns <code>namingBehavior</code>.</p></description>
<example>echo $wfd->getNamingBehavior(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getNamingBehavior() : string
    {
        return $this->getProperty()->namingBehavior;
    }
    
/**
<documentation><description><p>Returns a <code>StepDefinition</code> object bearing that id, listed under non-ordered steps.</p></description>
<example>u\DebugUtility::dump( $wfd->getNonOrderedStep( 'edit' ) );</example>
<return-type>StepDefinition</return-type>
<exception></exception>
</documentation>
*/
    public function getNonOrderedStep( string $step_id ) : p\StepDefinition
    {
        if( !isset( $this->non_ordered_step_map[ $step_id ] ) )
            throw new e\NoSuchStepException(
                S_SPAN . "The step does not exist." . E_SPAN );
            
        return $this->non_ordered_step_map[ $step_id ];
    }
    
/**
<documentation><description><p>Returns an array of <code>StepDefinition</code> objects listed under non-ordered steps.</p></description>
<example>u\DebugUtility::dump( $wfd->getNonOrderedSteps() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getNonOrderedSteps() : array
    {
        return $this->non_ordered_steps;
    }
    
/**
<documentation><description><p>Returns a <code>StepDefinition</code> object bearing that id, listed under ordered steps.</p></description>
<example>u\DebugUtility::dump( $wfd->getOrderedStep( 'initialize' ) );</example>
<return-type>StepDefinition</return-type>
<exception>NoSuchStepException</exception>
</documentation>
*/
    public function getOrderedStep( string $step_id ) : p\StepDefinition
    {
        if( !isset( $this->ordered_step_map[ $step_id ] ) )
            throw new e\NoSuchStepException( 
                S_SPAN . "The step does not exist." . E_SPAN );
            
        return $this->ordered_step_map[ $step_id ];
    }
    
/**
<documentation><description><p>Returns an array of <code>StepDefinition</code> objects listed under ordered steps.</p></description>
<example>u\DebugUtility::dump( $wfd->getOrderedSteps() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getOrderedSteps() : array
    {
        return $this->ordered_steps;
    }
    
/**
<documentation><description><p>Returns <code>xml</code>.</p></description>
<example>u\DebugUtility::dump( u\XmlUtility::replaceBrackets( $wfd->getXml() ) );</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getXml() : string
    {
        return $this->getProperty()->xml;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named step exists under non-ordered steps.</p></description>
<example>echo u\StringUtility::boolToString( $wfd->hasNonOrderedStep( 'edit' ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasNonOrderedStep( string $step_id ) : bool
    {
        return isset( $this->non_ordered_step_map[ $step_id ] );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named step exists under ordered steps.</p></description>
<example>echo u\StringUtility::boolToString( $wfd->hasOrderedStep( 'initialize' ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasOrderedStep( string $step_id ) : bool
    {
        return isset( $this->ordered_step_map[ $step_id ] );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the workflow definition is applicable to the group.</p></description>
<example>echo u\StringUtility::boolToString( $wfd->isApplicableToGroup(
    $cascade->getAsset( a\Group::TYPE, "Administrators" )
) ), BR;</example>
<return-type>bool</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function isApplicableToGroup( Group $g ) : bool
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException(
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }

        $group_name = $g->getName();
        $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
        return in_array( $group_name, $group_array );
    }
    
/**
<documentation><description><p>Removes the group name from <code>applicableGroups</code>,
and returns the calling object.</p></description>
<example>$wfd->removeGroup( $group )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function removeGroup( Group $g ) : Asset
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException(
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }
        
        $group_name   = $g->getName();
        $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
            
        if( in_array( $group_name, $group_array ) )
        {
            $temp = array();
            foreach( $group_array as $group )
            {
                if( $group != $group_name )
                {
                    $temp[] = $group;
                }
            }
            $group_array = $temp;
        }
        $group_string = implode( ';', $group_array );
        $this->getProperty()->applicableGroups = $group_string;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>copy</code> and returns the calling object.</p></description>
<example>$wfd->setCopy( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setCopy( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->copy = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>create</code> and returns the calling object.</p></description>
<example>$wfd->setCreate( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setCreate( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->create = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>delete</code> and returns the calling object.</p></description>
<example>$wfd->setDelete( true )->edit();</example>
<return-type>UnacceptableValueException</return-type>
<exception></exception>
</documentation>
*/
    public function setDelete( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->delete = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>edit</code> and returns the calling object.</p></description>
<example>$wfd->setEdit( true )->edit();</example>
<return-type>UnacceptableValueException</return-type>
<exception></exception>
</documentation>
*/
    public function setEdit( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->edit = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>move</code> and returns the calling object.</p></description>
<example>$wfd->setMove( true )->edit();</example>
<return-type>UnacceptableValueException</return-type>
<exception></exception>
</documentation>
*/
    public function setMove( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->move = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>namingBehavior</code> and returns the calling object.</p></description>
<example>$wfd->setNamingBehavior( 
    a\WorkflowDefinition::NAMING_BEHAVIOR_DEFINITION )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setNamingBehavior( string $nb ) : Asset
    {
        if( $nb != self::NAMING_BEHAVIOR_AUTO && 
            $nb != self::NAMING_BEHAVIOR_DEFINITION &&
            $nb != self::NAMING_BEHAVIOR_BLANK
        )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $nb is unacceptable." . E_SPAN );

        $this->getProperty()->namingBehavior = $nb;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>xml</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setXml( string $xml ) : Asset
    {
        if( trim( $xml ) == "" )
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
            
        $this->getProperty()->xml = $xml;
        return $this;
    }
    
/**
<documentation><description><p>Returns an XML string (for debugging purposes).</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function toXml() : string
    {
        $xml_string = "<system-workflow-definition name=\"" . $this->name .
            "\" initial-step=\"" . $this->initial_step . "\">\n";
        $xml_string .= "  <triggers>\n";
        
        foreach( $this->triggers as $trigger )
        {
            $xml_string .= $trigger->toXml();
        }
    
        $xml_string .= "  <steps>\n";
        
        foreach( $this->ordered_steps as $step )
        {
            $xml_string .= $step->toXml();
        }
        
        $xml_string .= "  </steps>\n";
        
        if( count( $this->non_ordered_steps ) > 0 )
        {
            $xml_string .= "  <non-ordered-steps>\n";
        
            foreach( $this->non_ordered_steps as $step )
            {
                $xml_string .= $step->toXml();
            }
        
            $xml_string .= "  </non-ordered-steps>\n";
        }
        $xml_string .= "</system-workflow-definition>\n";
        
        return $xml_string;
    }
    
    private $name;
    private $initial_step;
    private $ordered_steps;
    private $non_ordered_steps;
    private $ordered_step_map;
    private $non_ordered_step_map;
    private $triggers;
}
?>
