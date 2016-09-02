<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;

class TriggerDefinition
{
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
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
    public function hasParameter( $param_name )
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
    
    public function toXml()
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