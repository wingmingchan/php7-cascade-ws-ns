<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/22/2016 Added getStructuredDataStdClass.
  * 6/2/2016 Replaced most string literals with constants.
  * 5/5/2016 Added getStructuredDataStdClass, getStructuredDataObject.
  * 5/28/2015 Added namespaces.
  * 9/23/2014 Fixed a bug in isMultiple.
  * 7/1/2014 Added getStructuredData.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>A <code>DataDefinition</code> represents a data definition asset. This class is used by
both <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/data-definition-block.php"><code>DataDefinitionBlock</code></a> and <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/page.php"><code>Page</code></a>.</p>
<h2>Structure of <code>dataDefinition</code></h2>
<pre>dataDefinition
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  xml
</pre>
<h2>Definition-Data/Container Dichotomy</h2>
<p>When I build the classes related to <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/data-definition-block.php"><code>DataDefinitionBlock</code></a>,
I encounter the same issue of definition-data/container dichotomy again. The first time I run into this is when I build the <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/metadata-set.php"><code>MetadataSet</code></a>
and related classes. There, the definition-data/container dichotomy is much clearer.
On the definition side, I build three classes:
<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/possible-value.php"><code>p\PossibleValue</code></a>,
<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/dynamic-metadata-field-definition.php"><code>p\DynamicMetadataFieldDefinition</code></a>,
and <code>MetadataSet</code>. On the data/container side, I build three other classes:
<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/field-value.php"><code>p\FieldValue</code></a>,
<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/dynamic-field.php"><code>p\DynamicField</code></a>,
and <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/metadata.php"><code>p\Metadata</code></a>.
(See <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/field-value.php"><code>FieldValue</code></a>
for more discussion.)</p>
<p>Here when we look at data definition, we will have <code>DataDefinition</code> to
represent the definitions, and <a
href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/structured-data.php"><code>p\StructuredData</code></a>
and <a
href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/structured-data-node.php"><code>p\StructuredDataNode</code></a>
to encapsulate data/containers. To tell them apart, I will used the term <em>field</em>
to talk about elements defined in a data definition. For example, consider the following simple data definition:</p>
<pre>&lt;system-data-structure&gt;
  &lt;group identifier="test-group" label="Test Group" multiple="true"&gt;
    &lt;text identifier="test-text" label="Test Text" required="true" maxlength="100"
      size="20" help-text="Test Text"/&gt;
  &lt;/group&gt;
&lt;/system-data-structure&gt;
</pre>
<p>In this data definition, there are two fields: <code>test-group</code> and
<code>test-text</code>, the former containing the latter.</p>
<p>On the other hand, when we consider data/containers, I will use the term
<code>node</code>. When a data definition is associated with a data definition block,
there are nodes in the block. When we are adding an extra instance of a multiple field,
we are adding a node to the set of nodes bearing the same identifier.</p>
<h2>Fully Qualified Identifiers</h2>
<p>When we look at data definitions, we notice that each field has an identifier.
At first, it seems that all identifiers in a data definition must be unique.
But it turns out that this requirement only applies to siblings. That is to say,
fields of the same parent must have unique identifiers. Different fields of different
parents can have the same identifier. Therefore, identifiers themselves are not enough
to tell these fields apart. What really matters is the full path (in the sense of XPath)
of the each field. This is the reason why in the content type asset, Cascade has to use
group-path as well as the name to identify a field when dealing with inline editable
fields.</p>
<p>Therefore, when I design the three data definition-related classes, I decide to use
what I call <em>fully qualified identifiers</em>. A fully qualified identifier of a field
is in fact a string containing a set of identifiers, each pair separated by a semi-colon
(in Cascade, the group-path contains slashes instead). This identifier is the full XPath
from the top-level element all the way to the field. For example, the fully qualified
identifiers of the two fields above are <code>"test-group"</code> and
<code>"test-group;test-text"</code> respectively. Because fully qualified identifiers are
in a way XPath expressions, they are never ambiguous.</p>
<p>Back to the definition-data dichotomy. Fields in a <code>DataDefinition</code> object
have fully qualified field identifiers. Nodes in a <code>p\StructuredData</code> object
also have fully qualified identifiers. In most cases, the same fully qualified identifier
is used in a field and its corresponding part in a block or a page. The only time when
they differ is when we are dealing with a field with the <code>multiple</code> attribute
set to <code>true</code>. I will call such a field a <em>multiple field</em>. A multiple
field is defined only once in a data definition. But when the data definition is used in a
block or a page, the field can be instantiated multiple times and have multiple instances.
All of these instances will have the same field identifier from the data definition.
They are told apart only with the indexes of the array containing them. That is to say,
multiple instances of a field are defined in terms of their positions. To make things
worse, the array containing these nodes may contain nodes instantiated from a sibling
multiple field. When a multiple field is used, there will always be a first node. But this
first node may be the fifth one in the containing array.</p>
<p>I will use the positions of nodes of a multiple field to tell them apart. But instead
of using the indexes of the containing array, I use absolute positions within the set.
That is to say, the fully qualified identifier of the first node of a multiple field is
always the same no matter where the set of nodes appear, and always ends with
<code>;0</code>. For example, if we turn the <code>test-group;test-text</code> field
(the text, not the group) into a multiple field, <code>test-group;test-text</code> will
still be the fully qualified identifier of the field. But the fully qualified identifier
of the first node of this field is <code>test-group;test-text;0</code>. Therefore, when we
consider a multiple field and its corresponding nodes, a fully qualified identifier
without the digital part must be an identifier of a field, and an identifier of a node
must have this digital part. A multiple field in the definition does not have a position,
but its corresponding nodes do.</p>
<p>Instead of turning the text field into a multiple field, we can turn the group into
a multiple field. In this case, <code>test-group;test-text</code> will still be the fully
qualified identifier of the field, but the fully qualified identifier of the first node of
the group containing a text node will be <code>test-group;0;test-text</code>. If both the
group and text are multiple fields, then we can have fully qualifier identifiers like
<code>test-group;1;test-text;0</code> for nodes.</p>
<h2>Design Issues</h2>
<p>The <code>edit</code> method of this class is paired with the <code>setXML</code>
method. That is to say, only the definition XML of this object is editable. Here I adopt
the "garbage in, garbage out" principle and <code>setXML</code> will accept any data. If
the data is rejected by Cascade, no warnings will be issued from the class.</p>
<p>I translate every element in the data definition into an associative array.
Consider an example:</p>
<pre>&lt;text identifier="simpleslideshow-image-caption" label="Caption for the image"/&gt;
</pre>
<p>This element is represented as the following:</p>
<pre>["simpleslideshow-image-caption"]=&gt;
  array(3) {
    ["name"]=&gt;string(4) "text"
    ["identifier"]=&gt;string(29) "simpleslideshow-image-caption"
    ["label"]=&gt;string(21) "Caption for the image"
  }
</pre>
<p>This array includes all the information in the XML element. The value of
<code>name</code> is the element name. All other pairs represent the attributes of the
element. To retrieve this array, we just need to call the <code>getField</code> method and
pass in the fully qualified identifier like
<code>"simpleslideshow-image-caption"</code>.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/data_definition.php">data_definition.php</a></li></ul></postscript>
</documentation>
*/
class DataDefinition extends ContainedAsset
{
    const DEBUG     = false;
    const TYPE      = c\T::DATADEFINITION;
    const DELIMITER = ';';

/**
<documentation><description><p>The constructor, overriding the parent method to parse and
process the definition XML.</p></description>
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
<documentation><description><p>Displays <code>xml</code> and the attributes
array, and returns the calling object.</p></description>
<example>$dd->display();</example>
<return-type>Asset</return-type>
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
<documentation><description><p>Displays the attributes array, and returns the
calling object.</p></description>
<example>$dd->displayAttributes();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayAttributes() : Asset
    {
        echo S_H2 . "Attributes" . E_H2 . S_PRE;
        var_dump( $this->attributes );
        echo E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p>Displays <code>xml</code> and returns the calling object.
The flag <code>$formatted</code> controls whether the XML should be formatted for HTML output.</p></description>
<example>$dd->displayXML();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayXml( bool $formatted=true ) : Asset
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
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
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
<documentation><description><p>Returns the attribute array associated with the field
(identifier).</p></description>
<example>u\DebugUtility::dump( $dd->getField( $identifier ) );</example>
<return-type>array</return-type>
<exception>NoSuchFieldException</exception>
</documentation>
*/
    public function getField( $field_name ) : array
    {
        if( !in_array( $field_name, $this->identifiers ) )
            throw new e\NoSuchFieldException(
                S_SPAN . "The field name $field_name does not exist." . E_SPAN );

        return $this->attributes[ $field_name ];
    }
    
/**
<documentation><description><p>Returns the array of fully qualified identifiers.</p></description>
<example>u\DebugUtility::dump( $dd->getIdentifiers() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifiers() : array
    {
        return $this->identifiers;
    }
    
/**
<documentation><description><p>Returns an <code>stdClass</code> object, representing the
structured data associated with the data definition.</p></description>
<example>u\DebugUtility::dump( $dd->getStructuredData() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredData() : \stdClass
    {
        return $this->structured_data;
    }
    
/**
<documentation><description><p>Returns the <code>StructuredData</code> object.</p></description>
<example>u\DebugUtility::dump( $dd->getStructuredDataObject() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredDataObject() : p\Property
    {
        return new p\StructuredData(
            $this->structured_data, $this->getService(), $this->getId() );
    }
    
/**
<documentation><description><p>An alias of <code>getStructuredData</code>.</p></description>
<example></example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredDataStdClass() : \stdClass
    {
        return $this->structured_data;
    }
    
/**
<documentation><description><p>Returns <code>xml</code>.</p></description>
<example>$xml = $dd->getXml();</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getXml() : string
    {
        return $this->xml;
    }

/**
<documentation><description><p>An alias of <code>hasIdentifier</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasField( string $field_name ) : bool
    {
        return $this->hasIdentifier( $field_name );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether a field exists in the data definition.</p></description>
<example>if( $dd->hasIdentifier( $identifier ) )
{
    u\DebugUtility::dump( $dd->getField( $identifier ) );
    
    echo ( $dd->isMultiple( $identifier ) ? 
        'Multiple' : 'Not multiple' ) . BR;
}</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasIdentifier( string $field_name ) : bool
    {
        return ( in_array( $field_name, $this->identifiers ) );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field is a multiple field.</p></description>
<example>if( $dd->hasIdentifier( $identifier ) )
{
    u\DebugUtility::dump( $dd->getField( $identifier ) );
    
    echo ( $dd->isMultiple( $identifier ) ? 
        'Multiple' : 'Not multiple' ) . BR;
}</example>
<return-type>bool</return-type>
<exception>NoSuchFieldException</exception>
</documentation>
*/
    public function isMultiple( string $field_name ) : bool
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
<documentation><description><p>Sets <code>xml</code> and returns the calling
object.</p></description>
<example>$xml = $dd->getXml();
$dd->setXML( $xml )->edit();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setXml( string $xml )
    {
        $this->getProperty()->xml = $xml;
        $this->xml = $xml;
        $this->processSimpleXMLElement( new \SimpleXMLElement( $this->xml ) );

        return $this;
    }

    private function processSimpleXMLElement( 
        \SimpleXMLElement $xml_element, string $group_names='' )
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
    
    private function getStructuredDataNode( 
        \SimpleXMLElement $xml_element, string $type, string $identifier )
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
                        
                        $child_std = $this->createChildStd(
                            $child, $child_type, $child_identifier );
                    
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
    
    private function createStructuredData( \SimpleXMLElement $xml_element )
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
                    $child_std = $this->createChildStd(
                        $child, $child_type, $child_identifier );
                    $this->structured_data->structuredDataNodes->structuredDataNode[] =
                        $child_std;
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
                $this->structured_data->structuredDataNodes                     =
                    new \stdClass();
                $this->structured_data->structuredDataNodes->structuredDataNode =
                    new \stdClass();
                $this->structured_data->structuredDataNodes->structuredDataNode = 
                    $this->createChildStd( $child, $child_type, $child_identifier );
            }
        }
    }
    
    private function createChildStd(
        \SimpleXMLElement $child, string $child_type, string $child_identifier )
    {
        $child_std = $this->getStructuredDataNode( 
            $child, $child_type, $child_identifier );
        
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
