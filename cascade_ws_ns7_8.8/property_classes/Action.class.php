<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
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
<p>An <code>Action</code> object represents an action in a step of a workflow instance.</p>
<h2>Structure of <code>action</code></h2>
<pre>action
  identifier
  label
  actionType
  nextId
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "workflowActions" ),
        array( "getComplexTypeXMLByName" => "workflowAction" ),
    ) );
return $doc_string;
?>
</description>
<postscript></postscript>
</documentation>
*/
class Action extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct(
        \stdClass $a=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $a ) )
        {
            if( isset( $a->identifier ) )
                $this->identifier  = $a->identifier;
            if( isset( $a->label ) )
                $this->label       = $a->label;
            if( isset( $a->actionType ) )
                $this->action_type = $a->actionType;
            if( isset( $a->nextId ) )
                $this->next_id     = $a->nextId;
        }
    }
    
/**
<documentation><description><p>Returns <code>actionType</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getActionType()
    {
        return $this->action_type;
    }
    
/**
<documentation><description><p>Returns <code>identifier</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
/**
<documentation><description><p>Returns <code>label</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLabel()
    {
        return $this->label;
    }
    
/**
<documentation><description><p>Returns <code>nextId</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getNextId()
    {
        return $this->next_id;
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
        $obj->actionType = $this->action_type;
        $obj->nextId     = $this->next_id;
        return $obj;
    }

    private $identifier;
    private $label;
    private $action_type;
    private $next_id;
}
?>