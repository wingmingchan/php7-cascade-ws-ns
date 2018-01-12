<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<h2>Introduction</h2>
<p>A <code>StepDefinition</code> object represents a step definition, an XML element in a <a href="http://www.upstate.edu/web-services/api/asset-classes/workflow-definition.php"><code>a\WorkflowDefinition</code></a> object. This class is not a sub-class of <code>Property</code> and does not implement the <code>toStdClass</code> method. Instead, it provides a <code>toXml</code> method, which converts the data of the object back to an XML string.</p>
</description>
<postscript></postscript>
</documentation>
*/
class StepDefinition
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function __construct( \SimpleXMLElement $step_xml )
    {
        if( $step_xml == NULL )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
        }

        $this->step_xml   = $step_xml;
        $step_attr        = $step_xml->attributes();
        $this->type       = $step_attr->type->__toString();
        $this->identifier = $step_attr->identifier->__toString();
        $this->label      = $step_attr->label->__toString();
        
        if( $step_attr->{ "default-user" } )
            $this->default_user = $step_attr->{ "default-user" };
        else
            $this->default_user = "";
        
        $this->actions    = array();
        
        if( $this->step_xml->actions )
        {
            foreach( $this->step_xml->actions->action as $action )
            {
                $this->actions[] = new ActionDefinition( $action );
            }
        }
    }
    
/**
<documentation><description><p>Returns an array of <code>ActionDefinition</code> objects of the step definition.</p></description>
<example></example>
<return-type>array</return-type>
</documentation>
*/
    public function getActions() : array
    {
        return $this->actions;
    }
    
/**
<documentation><description><p>Returns the value of the <code>default-user</code> attribute.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getDefaultUser() : string
    {
        return $this->default_user;
    }
    
/**
<documentation><description><p>Returns the value of the <code>identifier</code> attribute.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getIdentifier() : string
    {
        return $this->identifier;
    }
    
/**
<documentation><description><p>Returns the value of the <code>label</code> attribute.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getLabel() : string
    {
        return $this->label;
    }
    
/**
<documentation><description><p>Returns the value of the <code>type</code> attribute.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getType() : string
    {
        return $this->type;
    }
    
/**
<documentation><description><p>Converts the object back to an XML string.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function toXml() : string
    {
        $has_actions = count( $this->actions ) > 0 ;
    
        $xml_string = "    <step type=\"" . $this->type . "\" " . 
            "label=\"" . $this->label . "\" " .
            "identifier=\"" . $this->identifier . "\"";
            
        if( $this->default_user != "" )
            $xml_string .= " default-user=\"" . $this->default_user . "\"";
            
        if( !$has_actions )
        {
            $xml_string .= "/>\n";
        }
        else
        {
            $xml_string .= ">\n      <actions>\n";
            
            foreach( $this->actions as $action )
            {
                $xml_string .= $action->toXml();
            }
        
            $xml_string .= "      </actions>\n    </step>\n";
        }
        
        return $xml_string;
    }

    private $step_xml;
    private $type;
    private $identifier;
    private $label;
    private $default_user;
    private $actions;
}
?>