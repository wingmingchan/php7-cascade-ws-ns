<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/17/2014 Fixed a bug related to label.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;

class ActionDefinition
{
    const DEBUG = false;
    const DUMP  = false;
    
    public function __construct( \SimpleXMLElement $action_xml )
    {
        if( $action_xml == NULL )
        {
            throw new e\EmptyValueException( "The xml cannot be empty." );
        }
        
        if( self::DEBUG && self::DUMP ) { 
            DebugUtility::dump( u\XMLUtility::replaceBrackets( $action_xml->asXML() ) ) ; }
        $this->action_xml = $action_xml;
        $action_attr      = $action_xml->attributes();
        $this->identifier = $action_attr->identifier->__toString();
        if( $action_attr->label )
            $this->label  = $action_attr->label->__toString();
        
        if( $action_attr->move )
        {
            $this->move = $action_attr->move->__toString();
        }
        else
        {
            $this->move = "";
        }
        
        if( $action_attr->{ "next-id" } )
        {
            $this->next_id = $action_attr->{ "next-id" }->__toString();
        }
        else
        {
            $this->next_id = "";
        }

        $this->triggers  = array();
        
        if( $this->action_xml->trigger )
        {
            foreach( $this->action_xml->trigger as $trigger )
            {
                $this->triggers[] = new TriggerDefinition( $trigger );
            }
        }
    }
    
    public function toXml()
    {
        $has_trigger = count( $this->triggers ) > 0 ;
        $xml_string  = "";
        
        $xml_string = "        <action identifier=\"" . $this->identifier . "\" " .
            ( isset( $this->label ) ? "label=\"" . $this->label . "\"" : "" );
        
        if( $this->move != "" )
        {
            $xml_string .= " move=\"" . $this->move . "\"";
        }
            
        if( $this->next_id != "" )
        {
            $xml_string .= " next-id=\"" . $this->next_id . "\"";
        }
            
        if( !$has_trigger )
        {
               
            $xml_string .= "/>\n"; 
        }
        else
        {
            $xml_string .= ">\n";
            
            foreach( $this->triggers as $trigger )
            {
                $xml_string .= $trigger->toXml();
            }
            
            $xml_string .=  "        </action>\n";
        }
        
        return $xml_string;
    }

    private $action_xml;
    private $identifier;
    private $label;
    private $move;
    private $next_id;
    private $triggers;
}
?>