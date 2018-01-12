<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/17/2014 Fixed a bug related to label.
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
<p>An <code>ActionDefinition</code> object represents an action definition, an XML element in the a step definition of a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/workflow-definition.php\"><code>a\WorkflowDefinition</code></a> object. This class is not a sub-class of <code>Property</code> and does not implement the <code>toStdClass</code> method. Instead, it provides a <code>toXml</code> method, which converts the data of the object back to an XML string.</p>";
return $doc_string;
?>
</description>
<postscript></postscript>
</documentation>
*/
class ActionDefinition
{
    const DEBUG = false;
    const DUMP  = false;
    
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Converts the object back to an XML string.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function toXml() : string
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