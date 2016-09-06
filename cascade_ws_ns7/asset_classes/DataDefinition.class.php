<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/2/2016 Replaced most string literals with constants.
  * 5/5/2016 Added getStructuredDataStdClass, getStructuredDataObject.
  * 5/28/2015 Added namespaces.
  * 9/23/2014 Fixed a bug in isMultiple.
  * 7/1/2014 Added getStructuredData.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class DataDefinition extends ContainedAsset
{
    const DEBUG     = false;
    const TYPE      = c\T::DATADEFINITION;
    const DELIMITER = ';';

    /**
    * The constructor
    * @param $service the AssetOperationHandlerService object
    * @param $identifier the identifier object
    */
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->xml             = $this->getProperty()->xml;
        $this->attributes      = array();
        $this->structured_data = new \stdClass();
        
        // process the xml
        $this->processSimpleXMLElement( new \SimpleXMLElement( $this->xml ) );
        // fully qualified identifiers
        $this->identifiers = array_keys( $this->attributes );
        
        // create the structured data
        $this->createStructuredData( new \SimpleXMLElement( $this->xml ) );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function display() : Asset
    {
        $xml_string = u\XMLUtility::replaceBrackets( $this->xml );
        
        echo S_H2 . "XML" . E_H2 .
             S_PRE . $xml_string . E_PRE . HR;
        echo S_H2 . "Attributes" . E_H2 . S_PRE;
        var_dump( $this->attributes );
        echo E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function displayAttributes()
    {
        echo S_H2 . "Attributes" . E_H2 . S_PRE;
        var_dump( $this->attributes );
        echo E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function displayXml( $formatted=true )
    {
        if( $formatted )
        {
            $xml_string = u\XMLUtility::replaceBrackets( $this->xml );
            echo S_H2 . "XML" . E_H2 . S_PRE;
        }

        echo $xml_string;
        
        if( $formatted )
             echo E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
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
        $asset = new \stdClass();
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getField( $field_name )
    {
        if( !in_array( $field_name, $this->identifiers ) )
            throw new e\NoSuchFieldException(
                S_SPAN . "The field name $field_name does not exist." . E_SPAN );

        return $this->attributes[ $field_name ];
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifiers()
    {
        return $this->identifiers;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredData()
    {
        return $this->structured_data;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredDataObject()
    {
        return new p\StructuredData( $this->structured_data, $this->getService(), $this->getId() );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredDataStdClass()
    {
        return $this->structured_data;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getXml()
    {
        return $this->xml;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasField( $field_name )
    {
        return $this->hasIdentifier( $field_name );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasIdentifier( $field_name )
    {
        return ( in_array( $field_name, $this->identifiers ) );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isMultiple( $field_name )
    {
        if( !in_array( $field_name, $this->identifiers ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field name $field_name does not exist." . E_SPAN );
        }
        
        if( isset( $this->attributes[ $field_name ][ c\T::MULTIPLE ] ) ) 
        {
            return true;
        }
        else if( isset( $this->attributes[ $field_name ][ 0 ][ c\T::MULTIPLE ] ) )
        {
            return true;
        }
        
        return false;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setXml( $xml )
    {
        $this->getProperty()->xml = $xml;
        $this->xml = $xml;
        $this->processSimpleXMLElement( new \SimpleXMLElement( $this->xml ) );

        return $this;
    }

    private function processSimpleXMLElement( $xml_element, $group_names='' )
    {
        foreach( $xml_element->children() as $child )
        {
            $type       = trim( $child->attributes()->{ $a = c\T::TYPE } );
            $name       = $child->getName();
            $identifier = $child[ c\T::IDENTIFIER ]->__toString();
            $old_group  = $group_names;
            
            if( $name == c\T::GROUP )
            {
                // fully qualified identifier
                // if a field/group belongs to a group,
                // add the group name to the identifier
                $group_names    .= $identifier;
                $group_names    .= self::DELIMITER;
                $attributes      = $child->attributes();
                $attribute_array = array();
                // add the name
                $attribute_array[ c\T::NAME ] = $name;

                // create the attribute array
                foreach( $attributes as $key => $value )
                {
                    $attribute_array[$key] = $value->__toString();
                }
                // store attributes
                $this->attributes[ trim( $group_names, self::DELIMITER ) ] = 
                    $attribute_array;
                // recursively process children
                $this->processSimpleXMLElement( $child, $group_names );
                
                // reset parent name for siblings
                $group_names = $old_group;
            }
            else
            {
                $value_string = '';
                
                // process checkbox, dropdown, radio, selector
                if( $name == c\T::TEXT && isset( $type ) && 
                    $type != c\T::DATETIME && $type != c\T::CALENDAR )
                {
                    $item_name = '';
                
                    // if type is not defined, then normal, multi-line, wysiwyg
                    switch( $type )
                    {
                        case c\T::CHECKBOX:
                        case c\T::DROPDOWN:
                            $item_name = $type;
                            break;
                        case c\T::RADIOBUTTON:
                            $item_name = c\T::RADIO;
                            break;
                        case c\T::MULTISELECTOR:
                            $item_name = c\T::SELECTOR;
                            break;
                    }
                
                    $text = array();
                
                    foreach( $child->{$p = "$item_name-item"} as $item )
                    {
                        $text[] = $item->attributes()->{ $a = c\T::VALUE };
                    }
                    
                    $value_string = implode( self::DELIMITER, $text );
                }
            
                $attributes      = $child->attributes();
                $attribute_array = array();
                // add the name
                $attribute_array[ c\T::NAME ] = $name;
                
                // attach items for checkbox, dropdown, radio, selector
                if( $value_string != '' )
                {
                    $attribute_array[ c\T::ITEMS ] = $value_string;
                }
                // create the attribute array
                foreach( $attributes as $key => $value )
                {
                    $attribute_array[$key] = $value->__toString();
                }
                
                // add identifier/attribute array to $this->attributes
                // add the first item
                $this->attributes[ $group_names . $identifier ] = 
                    $attribute_array;
            }
        }
    }
    
    private function getStructuredDataNode( $xml_element, $type, $identifier )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "$type, $identifier" ); }
        
        $obj = AssetTemplate::getStructuredDataNode();
        
        if( $type == c\T::GROUP )
        {
            $obj->type       = $type;
            $obj->identifier = $identifier;
            $obj->structuredDataNodes = new \stdClass();
            
            $child_count = count( $xml_element->children() );
            $more_than_one = ( $child_count > 1 ? true : false );
            
            if( $more_than_one )
            {
                $obj->structuredDataNodes->structuredDataNode = array();
                
                foreach( $xml_element->children() as $child )
                {
                    $child_type = $child->getName();
                    
                    if( self::DEBUG ) { u\DebugUtility::out( "Child type in group: $child_type" ); }
                    
                    if( isset( $child[ c\T::IDENTIFIER ] ) )
                    {
                        $child_identifier = $child[ c\T::IDENTIFIER ]->__toString();
                        
                        $child_std = $this->createChildStd( $child, $child_type, $child_identifier );
                    
                        $obj->structuredDataNodes->structuredDataNode[] = $child_std;
                    }
                }
            }
            else
            {
                $xml_array  = $xml_element->children();
                
                //var_dump( $xml_array );
                
                $child      = $xml_array[ 0 ];
                $child_type = $child->getName();
                
                if( self::DEBUG ) { u\DebugUtility::out( "Child type in group: $child_type" ); }
                
                $child_identifier = $child[ c\T::IDENTIFIER ]->__toString();
                $child_std = $this->createChildStd( $child, $child_type, $child_identifier );
                $obj->structuredDataNodes->structuredDataNode = $child_std;
            }
        }
        else
        {
            $obj->type       = $type;
            $obj->identifier = $identifier;
        }
        
        return $obj;
    }
    
    private function createStructuredData( $xml_element )
    {
        $this->structured_data->definitionId   = $this->getId();
        $this->structured_data->definitionPath = $this->getPath();
        
        $count = count( $xml_element->children() );
        
        if( $count > 1 )
        {
            $this->structured_data->structuredDataNodes = new \stdClass();
            $this->structured_data->structuredDataNodes->structuredDataNode = array();
            
            foreach( $xml_element->children() as $child )
            {
                $child_type = $child->getName();
                
                if( isset( $child[ c\T::IDENTIFIER ] ) )
                {
                    $child_identifier = $child[ c\T::IDENTIFIER ]->__toString();
                    $child_std = $this->createChildStd( $child, $child_type, $child_identifier );
                    $this->structured_data->structuredDataNodes->structuredDataNode[] = $child_std;
                }
            }
        }
        else
        {
            $child      = $xml_element->children();
            $child_type = $child->getName();
            $attributes = $child->attributes();
            
            if( isset( $attributes[ c\T::IDENTIFIER ] ) )
            {
                $child_identifier = $attributes[ c\T::IDENTIFIER ]->__toString();
                $this->structured_data->structuredDataNodes                     = new \stdClass();
                $this->structured_data->structuredDataNodes->structuredDataNode = new \stdClass();
                $this->structured_data->structuredDataNodes->structuredDataNode = 
                    $this->createChildStd( $child, $child_type, $child_identifier );
            }
        }
    }
    
    private function createChildStd( $child, $child_type, $child_identifier )
    {
        $child_std = $this->getStructuredDataNode( $child, $child_type, $child_identifier );
        
        $grandchild = $child->children();
        
        if( isset( $grandchild ) )
        {
            $grandchild_type = $grandchild->getName();
            
            if( $grandchild_type == c\T::CHECKBOXITEM )
            {
                $child_std->text = p\StructuredDataNode::CHECKBOX_PREFIX;
            }
            else if( $grandchild_type == c\T::SELECTORITEM )
            {
                $child_std->text = p\StructuredDataNode::SELECTOR_PREFIX;
            }
        }
        
        if( $child_type == c\T::ASSET )
        {
            $child_attributes     = $child->attributes();
            $asset_type           = $child_attributes[ c\T::TYPE ]->__toString();
            $child_std->assetType = $asset_type;
        }
        return $child_std;
    }
    
    private $attributes;      // all attributes of each field
    private $identifiers;     // all identifiers of fields
    private $xml;             // the definition xml
    private $structured_data; // the corresponding structured data
}
?>
