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

class StepDefinition
{
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
    
    public function getActions()
    {
        return $this->actions;
    }
    
    public function getDefaultUser()
    {
        return $this->default_user;
    }
    
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function toXml()
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