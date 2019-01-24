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
<documentation><description><h2>Introduction</h2>
<p>A <code>TriggerDefinition</code> object represents a trigger definition, an XML element in a <a href="http://www.upstate.edu/web-services/api/asset-classes/workflow-definition.php"><code>a\WorkflowDefinition</code></a> object. This class is not a sub-class of <code>Property</code> and does not implement the <code>toStdClass</code> method. Instead, it provides a <code>toXml</code> method, which converts the data of the object back to an XML string.</p>
<p>A trigger element can be a child of the triggers element, or it can be a child of an action element. This class can represent both. When a trigger element appears in the triggers element, it will have a class attribute. If it is a child of an action element, then it has no class attribute. But it can have parameter children.</p>
</description>
<postscript></postscript>
</documentation>
*/
class TriggerDefinition
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function __construct( \SimpleXMLElement $trigger_xml )
    {
        if( $trigger_xml == NULL )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
        }

        $this->trigger_xml = $trigger_xml;
        $trigger_attr      = $trigger_xml->attributes();
        $this->name        = $trigger_attr->name->__toString();
        
        if( isset( $trigger_attr ) && isset( $trigger_attr->class ) )
        {
            $this->class       = $trigger_attr->class->__toString();
            $this->in_triggers = true;
        }
        else
        {
            $this->parameters  = array();
        
            if( $this->trigger_xml->parameter )
            {
            
                foreach( $this->trigger_xml->parameter as $parameter )
                {
                    $param_std        = new \stdClass();
                    $param_std->name  = $parameter->name->__toString();
                    $param_std->value = $parameter->value->__toString();
                
                    $this->parameters[] = new Parameter( $param_std );
                }
            }
        }
    }
    
/**
<documentation><description><p>Returns the value of the <code>class</code> attribute.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getClass() : string
    {
        return $this->class;
    }
    
/**
<documentation><description><p>Returns the value of the <code>name</code> attribute.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getName() : string
    {
        return $this->name;
    }
    
/**
<documentation><description><p>Returns an array of <code>Parameter</code> objects.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getParameters()
    {
        return $this->parameters;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named parameter exists in the trigger.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function hasParameter( string $param_name ) : bool
    {
        foreach( $this->parameters as $parameter )
        {
            if( $parameter->getName() == $param_name )
            {
                return true;
            }
        }
        return false;
    }
    
/**
<documentation><description><p>Converts the object back to an XML string.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function toXml() : string
    {
        $xml_string = "";
        
        if( $this->in_triggers )
        {
            $xml_string = "    <trigger name=\"" . $this->name . "\" class=\"" .
                $this->class . "\"/>\n";
        }
        else
        {
            $has_param  = count( $this->parameters ) > 0 ;
        
            if( !$has_param )
            {
                $xml_string = "          <trigger name=\"" . $this->name . "\"/>\n";
            }
            else
            {
                $xml_string = "          <trigger name=\"" . $this->name . "\">\n";
            
                foreach( $this->parameters as $parameter )
                {
                    $xml_string .= 
                         "            <parameter>\n" .
                         "              <name>" . $parameter->getName() . "</name>\n" .
                         "              <value>" . $parameter->getValue() . "</value>\n" .
                         "            </parameter>\n";
                }
            
                $xml_string .=  "          </trigger>\n";
            }
        }
        
        //echo u\XMLUtility::replaceBrackets( $xml_string ) . BR;
        return $xml_string;
    }

    private $trigger_xml;
    private $in_triggers;
    private $name;
    private $class;
    private $parameters;
}
?>