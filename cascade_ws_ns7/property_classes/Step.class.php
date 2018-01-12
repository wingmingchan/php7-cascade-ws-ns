<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/26/2017 Changed constructor so that it works with REST.
  * 6/12/2017 Added WSDL.
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
<p>A <code>Step</code> object represents a step in a workflow instance.</p>
<h2>Structure of <code>step</code></h2>
<pre>step
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
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "workflowSteps" ),
        array( "getComplexTypeXMLByName" => "workflowStep" ),
        array( "getComplexTypeXMLByName" => "workflowActions" ),
        array( "getComplexTypeXMLByName" => "workflowAction" ),
    ) );
return $doc_string;
?>
</description>
<postscript></postscript>
</documentation>
*/
class Step
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $s=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {        
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
        
        if( isset( $s ) )
        {
            if( isset( $s->identifier ) )
                $this->identifier = $s->identifier;
            if( isset( $s->label ) )
                $this->label      = $s->label;
            if( isset( $s->stepType ) )
                $this->step_type  = $s->stepType;
            if( isset( $s->owner ) )
                $this->owner      = $s->owner;
                
            $this->actions            = array();
            $this->action_identifiers = array();
        
            if( isset( $s->actions ) )
            {
                if( $this->service->isSoap() && isset( $s->actions->action ) )
                    $actions = $s->actions->action;
                elseif( $this->service->isRest() )
                    $actions = $s->actions;
            
                if( !is_array( $actions ) )
                {
                    $actions = array( $actions );
                }
            
                foreach( $actions as $action )
                {
                    $a                          = new Action( $action );
                    $this->actions[]            = $a;
                    $this->action_identifiers[] = $a->getIdentifier();
                }
            }
        }
    }
    
/**
<documentation><description><p>Returns an array of identifiers of the <code>Action</code> objects of the step.</p></description>
<example></example>
<return-type>array</return-type>
</documentation>
*/
    public function getActionIdentifiers() : array
    {
        return $this->action_identifiers;
    }
    
/**
<documentation><description><p>Returns an array of <code>Action</code> objects.</p></description>
<example></example>
<return-type>array</return-type>
</documentation>
*/
    public function getActions() : array
    {
        return $this->actions;
    }
    
/**
<documentation><description><p>Returns <code>identifier</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getIdentifier() : string
    {
        return $this->identifier;
    }
    
/**
<documentation><description><p>Returns <code>label</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getLabel() : string
    {
        return $this->label;
    }
    
/**
<documentation><description><p>Returns <code>owner</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getOwner() : string
    {
        return $this->owner;
    }
    
/**
<documentation><description><p>Returns <code>stepType</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getStepType() : string
    {
        return $this->step_type;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj             = new \stdClass();
        $obj->identifier = $this->identifier;
        $obj->label      = $this->label;
        $obj->stepType   = $this->step_type;
        $obj->owner      = $this->owner;
        $obj->actions    = new \stdClass();
        $count           = count( $this->actions );
        
        if( $count > 0 )
        {
            $obj->actions = new \stdClass();
            
            if( $count == 1 )
            {
                $obj->actions->action = $this->actions[ 0 ]->toStdClass();
            }
            else
            {
                $obj->actions->action = array();
                
                foreach( $this->actions as $action )
                {
                    $obj->actions->action[] = $action->toStdClass();
                }
            }
        }
        return $obj;
    }

    private $identifier;
    private $label;
    private $step_type;
    private $owner;
    private $actions;
    private $action_identifiers;
    private $service;
}
?>