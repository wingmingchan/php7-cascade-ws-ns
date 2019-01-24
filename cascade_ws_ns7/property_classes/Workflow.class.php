<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 7/18/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 12/22/2016 Minor bug fixes.
  * 2/13/2016 Added start_date, end_date, and the get methods.
  * 5/28/2015 Added namespaces.
  * 10/1/2014 Fixed a bug in getRelatedEntity.
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
<p>A <code>Workflow</code> object represents a workflow instance. It contains <a href=\"http://www.upstate.edu/web-services/api/property-classes/step.php\"><code>Step</code></a> objects, which in turn contain <a href=\"http://www.upstate.edu/web-services/api/property-classes/action.php\"><code>Action</code></a> objects. All properties in this class are read-only. There are no <code>set</code> methods implemented.</p>
<p>A <code>workflow</code> object can be obtained by calling the <code>getWorkflow</code> method through an <code>Asset</code> object (e.g., a <code>Page</code> object).</p>
<h2>Structure of <code>workflow</code></h2>
<pre>workflow
  id
  name
  relatedEntity (an Identifier object)
    id
    path
      path
      siteId
      siteName
    type
    recycled
  currentStep (a string)
  orderedSteps
    step
      identifier
      label
      stepType
      owner
      actions
        action
          identifier
          label
          actionType
          nextId
  unorderedSteps
    step
      identifier
      label
      stepType
      owner
      actions
        action
          identifier
          label
          actionType
          nextId
  startDate
  endDate
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "workflow" ),
        array( "getComplexTypeXMLByName" => "workflowSteps" ),
        array( "getComplexTypeXMLByName" => "workflowStep" ),
        array( "getComplexTypeXMLByName" => "workflowActions" ),
        array( "getComplexTypeXMLByName" => "workflowAction" ),
        array( "getComplexTypeXMLByName" => "workflowTransitionInformation" ),
        array( "getComplexTypeXMLByName" => "workflow-configuration" ),
        array( "getComplexTypeXMLByName" => "workflow-step-configurations" ),
        array( "getComplexTypeXMLByName" => "workflow-step-configuration" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/workflow.php">workflow.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/recipes/workflow/transition.php">transition.php</a></li></ul></postscript>
</documentation>
*/
class Workflow extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        \stdClass $wf=NULL, 
        aohs\AssetOperationHandlerService $service=NULL,
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
    /*
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
    */
        
        if( isset( $wf ) )
        {
            $this->workflow        = $wf;
            $this->related_entity  = new Identifier( $wf->relatedEntity );
            $this->ordered_steps   = array();
            $this->unordered_steps = array();
        
            if( isset( $wf->orderedSteps ) && isset( $wf->orderedSteps->step ) )
            {
                $steps = $wf->orderedSteps->step;
                
                if( !is_array( $steps ) )
                {
                    $steps = array( $steps );
                }
                
                foreach( $steps as $step )
                {
                    $s                     = new Step( $step );
                    $this->ordered_steps[] = $s;
                    
                    $this->ordered_step_possible_action_map[ $s->getIdentifier() ]  =
                        $s->getActionIdentifiers();
                }
            }
        
            if( isset( $wf->unorderedSteps ) && isset( $wf->unorderedSteps->step ) )
            {
                $steps = $wf->unorderedSteps->step;
                
                if( !is_array( $steps ) )
                {
                    $steps = array( $steps );
                }
                
                foreach( $steps as $step )
                {
                    $s                       = new Step( $step );
                    $this->unordered_steps[] = $s;
                    
                    $this->unordered_step_possible_action_map[ $s->getIdentifier() ]  =
                        $s->getActionIdentifiers();
                }
            }
            $this->start_date = $wf->startDate;
            $this->end_date   = $wf->endDate;
        }
    }
    
/**
<documentation><description><p>Returns <code>currentStep</code>.</p></description>
<example>echo $wf->getCurrentStep(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getCurrentStep() : string
    {
        return $this->workflow->currentStep;
    }
    
/**
<documentation><description><p>Returns a string array of possible actions of the current step, if the current step has possible actions; otherwise <code>NULL</code>.</p></description>
<example>u\DebugUtility::dump( $wf->getCurrentStepPossibleActions() );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getCurrentStepPossibleActions()
    {
        if( isset(
            $this->ordered_step_possible_action_map[ $this->workflow->currentStep ] ) )
            return $this->ordered_step_possible_action_map[ $this->workflow->currentStep ];
        else
            return NULL;
    }
    
/**
<documentation><description><p>Returns <code>endDate</code>.</p></description>
<example>echo $wf->getEndDate(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getEndDate() : string
    {
        return $this->end_date;
    }

/**
<documentation><description><p>Returns <code>id</code>.</p></description>
<example>echo $wf->getId(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getId() : string
    {
        return $this->workflow->id;
    }
    
/**
<documentation><description><p>Returns <code>name</code>.</p></description>
<example>echo $wf->getName(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getName() : string
    {
        return $this->workflow->name;
    }
    
/**
<documentation><description><p>Returns <code>relatedEntity</code>, which is an <code>Identifier</code> object.</p></description>
<example>u\DebugUtility::dump( $wf->getRelatedEntity() );</example>
<return-type>Property</return-type>
</documentation>
*/
    public function getRelatedEntity() : Property
    {
        return $this->related_entity;
    }
    
/**
<documentation><description><p>Returns <code>startDate</code></p></description>
<example>echo $wf->getStartDate(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getStartDate() : string
    {
        return $this->start_date;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named action is possible in the current step.</p></description>
<example>echo u\StringUtility::boolToString( $wf->isPossibleAction( $action ) );</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isPossibleAction( string $a_name ) : bool
    {
        if( isset( $this->ordered_step_possible_action_map[
                $this->workflow->currentStep ] ) &&
            is_array( $this->ordered_step_possible_action_map[
                $this->workflow->currentStep ] ) &&
            in_array( $a_name,
                $this->ordered_step_possible_action_map[ $this->workflow->currentStep ] ) )
            return true;
            
        if( isset( $this->unordered_step_possible_action_map[
                $this->workflow->currentStep ] ) &&
            is_array( $this->unordered_step_possible_action_map[
                $this->workflow->currentStep ] ) &&
            in_array( $a_name,
                $this->unordered_step_possible_action_map[ $this->workflow->currentStep ] ) )
            return true;
            
        return false;
    }
    
/**
<documentation><description><p>Advances the workflow to the named action of the current step. Note that the advancement is possible only for ordered steps.</p></description>
<example>if( $action != "" )
    $wf->performWorkflowTransition( $action, "Performing $action" );</example>
<return-type></return-type>
<exception>NoSuchActionException, WorkflowTransitionFailureException</exception>
</documentation>
*/
    public function performWorkflowTransition(
        string $a_name, string $comment="" ) : Property
    {
        if( !$this->isPossibleAction( $a_name ) )
            throw new e\NoSuchActionException(
                S_SPAN . "The action $a_name is not defined in the workflow." . E_SPAN );
            
        $this->service->performWorkflowTransition( $this->workflow->id, $a_name, $comment );
        
        if( $this->service->isSuccessful() )
        {
            return $this;
        }
        else
        {
            throw new e\WorkflowTransitionFailureException(
                S_SPAN . "The transition cannot be performed." . E_SPAN .
                $this->service->getMessage() );
        }
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj                = new \stdClass();
        $obj->id            = $this->workflow->id;
        $obj->name          = $this->workflow->name;
        $obj->relatedEntity = $this->related_entity->toStdClass();
        $obj->currentStep   = $this->workflow->currentStep;
        $obj->orderedSteps  = new \stdClass();
        $os_count           = count( $this->ordered_steps );
        
        if( $os_count > 0 )
        {
            $obj->orderedSteps = new \stdClass();

            if( $os_count == 1 )
            {
                $obj->orderedSteps->step = $this->ordered_steps[ 0 ]->toStdClass();
            }
            else
            {
                $obj->orderedSteps->step = array();
                
                foreach( $this->ordered_steps as $step )
                {
                    $obj->orderedSteps->step[] = $step->toStdClass();
                }
            }
        }
        
        $us_count = count( $this->unordered_steps );
        
        if( $us_count > 0 )
        {
            if( $us_count == 1 )
            {
                $obj->unorderedSteps->step = $this->unordered_steps[ 0 ]->toStdClass();
            }
            else
            {
                $obj->unorderedSteps->step = array();
                
                foreach( $this->unordered_steps as $step )
                {
                    $obj->unorderedSteps->step[] = $step->toStdClass();
                }
            }
        }
        
        $obj->startDate = $this->workflow->startDate;
        $obj->endDate   = $this->workflow->endDate;
    
        return $obj;
    }
    
    private $workflow;
    private $related_entity;
    private $ordered_steps;
    private $unordered_steps;
    private $ordered_step_possible_action_map;
    private $unordered_step_possible_action_map;
    private $action_id_identifier_map;
    private $start_date;
    private $end_date;
    //private $service;
}
?>