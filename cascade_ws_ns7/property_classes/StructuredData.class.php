<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/14/2018 Fixed a bug in removePhantomNodes.
  * 2/5/2018 Added phantom value-related code.
  * 12/21/2017 Added the $service object to constructor and pass it into processStructuredDataNodes so that isSoap and isRest can be called. Changed toStdClass so that it works with REST.
  * 12/19/2017 Added throwException with asset id and path information in messages,
  and added calls to throwException in setX methods.
  * 8/1/2017 Added getBlock.
  * 7/18/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 11/3/2016 Added code to copyData to bypass phantom values.
  * 10/24/2016 Added hasPossibleValues; multiple bug fixes.
  * 8/11/2016 Added getService in both this class and StructuredDataPhantom to work with phantom nodes.
  * 6/20/2016 Changed the name searchWYSIWYG to searchWYSIWYGByPattern, added searchTextByPattern.
  * 6/2/2016 Added aliases. Replaced most string literals with constants.
  * 6/1/2016 Added isBlockChooser, isCalendarNode, isCheckboxNode, isDatetimeNode, isDropdownNode,
  * isFileChooser, isLinkableChooser, isMultiLineNode, isMultiSelectorNode, isPageChooser,
  * isRadioNode, isSymlinkChooser, isTextBox, and isWYSIWYGNode.
  * 3/10/2016 Added hasPhantomNodes.
  * 3/9/2016 Added removePhantomNodes and copyData.
  * 2/26/2016 Added mapData.
  * 1/8/2016 Added code to deal with host asset.
  * 9/15/2015 Added createNInstancesForMultipleField.
  * 5/28/2015 Added namespaces.
  * 5/1/2015 Added if statements to various get and set methods.
  *   Reason: to loosen the restriction related to phantom nodes.
  * 2/24/2015 Added getPossibleValues.
  * 9/25/2014 Added isMultiLineNode.
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
<p>A <code>StructuredData</code> object represents a <code>structuredData</code> property found in a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/data-definition-block.php\"><code>a\DataDefinitionBlock</code></a> object and a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/page.php\"><code>a\Page</code></a> object.</p>
<h2>Structure of <code>structuredData</code></h2>
<pre>structuredData
  definitionId
  definitionPath
  structuredDataNodes
    structuredDataNode
</pre>
<h2>Design Issues</h2>
<ul>
<li>A <code>StructuredData</code> object contains a <code>a\DataDefinition</code> object so that it can pass it along to its children.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "structured-data" ),
        array( "getComplexTypeXMLByName" => "structured-data-nodes" ),
        array( "getComplexTypeXMLByName" => "structured-data-node" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-type" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-asset-type" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/structured_data.php">structured_data.php</a></li></ul></postscript>
</documentation>
*/
class StructuredData extends Property
{
    const DEBUG = false;

/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $sd=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data_definition_id=NULL,
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
        
        // a data definition block will have a data definition id in the sd object
        // a page will need to pass in the data definition id
        if( isset( $sd ) )
        {
            // store the data
            if( isset( $sd->definitionId ) )
            {
                $this->definition_id = $sd->definitionId;
                $this->type = a\DataDefinitionBlock::TYPE;
            }
            else if( isset( $data_definition_id ) )
            {
                $this->definition_id = $data_definition_id;
                $this->type = a\Page::TYPE;
            }
                
            if( isset( $sd->definitionPath ) )
                $this->definition_path = $sd->definitionPath;
            // initialize the arrays
            $this->children        = array();
            $this->node_map        = array();
            
            // store the data definition
            $this->data_definition = new a\DataDefinition( 
                $service, $service->createId(
                    a\DataDefinition::TYPE, $this->definition_id ) );
            // turn structuredDataNode into an array
            if( $this->service->isSoap() )
            {
                if( isset( $sd->structuredDataNodes->structuredDataNode ) && 
                    !is_array( $sd->structuredDataNodes->structuredDataNode ) )
                {
                    $child_nodes = array( $sd->structuredDataNodes->structuredDataNode );
                }
                elseif( isset( $sd->structuredDataNodes->structuredDataNode ) )
                {
                    $child_nodes = $sd->structuredDataNodes->structuredDataNode;
                
                    if( self::DEBUG ) { u\DebugUtility::out(
                        "Number of nodes in std: " . count( $child_nodes ) ); }
                }
            }
            elseif( $this->service->isRest() )
            {
                if( isset( $sd->structuredDataNodes ) && 
                    !is_array( $sd->structuredDataNodes ) )
                {
                    $child_nodes = array( $sd->structuredDataNodes );
                }
                elseif( isset( $sd->structuredDataNodes ) )
                {
                    $child_nodes = $sd->structuredDataNodes;
                }
            }
            // convert stdClass to objects
            StructuredDataNode::processStructuredDataNodes( 
                '', $this->children, $child_nodes, $this->data_definition, $service );
        }
        
        $this->node_map    = $this->getIdentifierNodeMap();
        $this->identifiers = array_keys( $this->node_map );
        $this->host_asset  = $data2;
        
        if( self::DEBUG ) { u\DebugUtility::out( "First node ID: " . $first_node_id ); }
    }
    
/**
<documentation><description><p>Appends a node to a set of nodes consisting a first node
identified by the identifier, and returns the calling object. Note that node instances are in fact copies of the last instance.
Therefore, if the last node contains data, then copies created will contain exactly the
same data.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function appendSibling( string $first_node_id ) : Property
    {
        if( self::DEBUG ) { u\DebugUtility::out( "First node ID: " . $first_node_id ); }
        
        // if this is a multiple field, there must be a first node
        if( !$this->hasIdentifier( $first_node_id ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $first_node_id does not exist." . E_SPAN );
        }
        
        $first_node = $this->node_map[ $first_node_id ];
        $field_id   = StructuredDataNode::getFieldIdentifier( $first_node_id );
        
        if( self::DEBUG ) { u\DebugUtility::out( "Field ID: " . $field_id ); }
        
        // non-ambiguous path, no multipled-parent
        // no ;digits in the identifier
        if( strpos( $first_node_id, $field_id ) !== false )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "non_ambiguous" ); }
            return $this->appendNodeToField( $field_id );
        }
        
        // ambiguous, with multiple ancestors
        $parent_id   = $first_node->getParentId();
        $parent_node = $this->getNode( $parent_id );
        
        if( self::DEBUG ) 
        { 
            u\DebugUtility::out( "Parent ID: " . $parent_id );
            $shared_id = StructuredDataNode::removeLastIndex( $first_node_id );
            u\DebugUtility::out( "Shared ID: " . $shared_id ); 
        }

        $parent_node->addChildNode( $first_node_id );
        $temp = $this->node_map;
        asort( $temp );
        $this->identifiers = array_keys( $temp );

        return $this;
    }
    
/**
<documentation><description><p>Creates exactly <code>$number</code> instances for the
multiple field whose first node is <code>$identifier</code> and returns the object. This
method ensures that a multiple field will have exactly N instances. If the object has more
or has less instances than <code>$number</code>, then instances are either removed from or
added to the field. Note that node instances are in fact copies of the last instance.
Therefore, if the last node contains data, then copies created will contain exactly the
same data.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException, NodeException</exception>
</documentation>
*/
    public function createNInstancesForMultipleField(
        int $number, string $identifier ) : Property
    {
        $number = intval( $number );
        
        if( !$number > 0 )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $number is not a number." . E_SPAN );
        }
        
        if( !$this->hasNode( $identifier ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist." . E_SPAN );
        }
        
        $num_of_instances  = $this->getNumberOfSiblings( $identifier );
    
        if( $num_of_instances < $number ) // more needed
        {
            while( $this->getNumberOfSiblings( $identifier ) != $number )
            {
                $this->appendSibling( $identifier );
            }
        }
        else if( $num_of_instances > $number )
        {
            while( $this->getNumberOfSiblings( $identifier ) != $number )
            {
                $this->removeLastSibling( $identifier );
            }
        }
        
        // update map and identifiers
        if( $this->service->isSoap() )
            StructuredDataNode::processStructuredDataNodes( 
                '', $this->children, 
                $this->toStdClass()->structuredDataNodes->structuredDataNode, 
                $this->data_definition, $this->service );
        elseif( $this->service->isRest() )
            StructuredDataNode::processStructuredDataNodes( 
                '', $this->children, 
                $this->toStdClass()->structuredDataNodes, 
                $this->data_definition, $this->service );
            
        $this->node_map    = $this->getIdentifierNodeMap();
        $this->identifiers = array_keys( $this->node_map );
        return $this;
    }
    
/**
<documentation><description><p>Returns the type string of an asset node (an asset node is
an instance of an asset field of type <code>page</code>, <code>file</code>,
<code>block</code>, <code>symlink</code>, or
<code>page,file,symlink</code>).</p></description>
<example>echo $sd->getAssetNodeType( "group;block-chooser" ), BR;</example>
<return-type>mixed</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function getAssetNodeType( string $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        $node = $this->node_map[ $identifier ];
        
        if( $node->getType() != c\T::ASSET )
        {
            throw new e\NodeException(
                S_SPAN . "This node is not an asset node." . E_SPAN );
        }

        return $node->getAssetType();
    }
    
/**
<documentation><description><p>Returns the block attached to the named node or <code>null</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlock( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getBlock( $this->service );
    }
    
/**
<documentation><description><p>Returns <code>blockId</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getBlockId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockId( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getBlockId();
    }
    
/**
<documentation><description><p>Returns <code>blockPath</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getBlockPath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockPath( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getBlockPath();
    }
    
/**
<documentation><description><p>Returns the <code>a\DataDefinition</code> object.</p></description>
<example>$sd->getDataDefinition()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getDataDefinition() : a\Asset
    {
        return $this->data_definition;
    }
    
/**
<documentation><description><p>Returns <code>definitionId</code>.</p></description>
<example>echo $sd->getDefinitionId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDefinitionId() : string
    {
        return $this->definition_id;
    }
    
/**
<documentation><description><p>Returns <code>definitionPath</code>.</p></description>
<example>echo $sd->getDefinitionPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDefinitionPath() : string
    {
        return $this->definition_path;
    }
    
/**
<documentation><description><p>Returns <code>fieldId</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getFileId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFileId( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getFileId();
    }
    
/**
<documentation><description><p>Returns <code>fieldPath</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getFilePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFilePath( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getFilePath();
    }
    
/**
<documentation><description><p>Returns the host asset.</p></description>
<example>$sd->getHostAsset()->dump();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getHostAsset()
    {
        return $this->host_asset;
    }
    
/**
<documentation><description><p>Returns the map of identifiers pointing to <code>StructuredDataNode</code> objects.</p></description>
<example>u\DebugUtility::dump( $sd->getIdentifierNodeMap() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifierNodeMap() : array
    {
        foreach( $this->children as $child )
        {
            $this->node_map = array_merge( 
                $this->node_map, $child->getIdentifierNodeMap() );
        }
        
        return $this->node_map;
    }
    
/**
<documentation><description><p>Returns the array of all fully qualified identifiers.</p></description>
<example>u\DebugUtility::dump( $sd->getIdentifiers() )</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifiers() : array
    {
        return $this->identifiers;
    }
    
/**
<documentation><description><p>Returns the id of a <code>a\Linkable</code> node (a
<code>Linkable</code> node is a chooser allowing users to choose either a page, a file, or
a symlink; therefore, the id can be the <code>fileId</code>, <code>pageId</code>, or
<code>symlinkId</code> of the node).</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getLinkableId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLinkableId( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getLinkableId();
    }
    
/**
<documentation><description><p>Returns the path of a <code>a\Linkable</code> node (a
<code>a\Linkable</code> node is a chooser allowing users to choose either a page, a file,
or a symlink; therefore, the path can be the <code>filePath</code>, <code>pagePath</code>,
or <code>symlinkPath</code> of the node).</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getLinkablePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLinkablePath( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getLinkablePath();
    }
    
/**
<documentation><description><p>Returns <code>StructuredDataNode</code> object bearing this
fully qualified identifier.</p></description>
<example>u\DebugUtility::dump( $sd->getNode( $id )->toStdClass() );</example>
<return-type>StructuredDataNode</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function getNode( string $identifier ) : StructuredDataNode
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }

        return $this->node_map[ $identifier ];
    }
    
/**
<documentation><description><p>Returns the type string of a node. The returned value is
one of the following: <code>group</code>, <code>asset</code>, and <code>text</code>.</p></description>
<example>echo $sd->getNodeType( $id ), BR;</example>
<return-type>string</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function getNodeType( string $identifier ) : string
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }

        return $this->node_map[ $identifier ]->getType();
    }
    
/**
<documentation><description><p>Returns the number of children.</p></description>
<example>echo $sd->getNumberOfChildren(), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getNumberOfChildren() : int
    {
        return count( $this->children );
    }
    
/**
<documentation><description><p>Returns the number of instances of a multiple field, the
supplied identifier being the one of the first instance.</p></description>
<example>echo $sd->getNumberOfSiblings( "multiple-first;0" ), BR;</example>
<return-type>int</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function getNumberOfSiblings( string $node_name ) : int
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Node ID: " . $node_name ); }
        $par_id     = $this->node_map[ $node_name ]->getParentId();
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $par_id ); }

        if( !in_array( $node_name, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $node_name does not exist" . E_SPAN );
        }
        
        if( $par_id != '' )
        {
            $siblings = $this->node_map[ $par_id ]->getChildren();
        }
        else
        {
            $siblings = $this->children;
        }
        
        // remove ;0
        $field_id = StructuredDataNode::removeLastIndex( $node_name );
        if( self::DEBUG ) { u\DebugUtility::out( "Field ID: " . $field_id ); }
        
        $last_sibling_index = StructuredDataNode::getPositionOfLastNode(
            $siblings, $field_id );
        $last_id  = $siblings[ $last_sibling_index ]->getIdentifier();
        
        return StructuredDataNode::getLastIndex( $last_id ) + 1;
    }
    
/**
<documentation><description><p>Returns <code>pageId</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getPageId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPageId( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getPageId();
    }
    
/**
<documentation><description><p>Returns <code>pagePath</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getPagePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPagePath( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getPagePath();
    }
    
/**
<documentation><description><p>Returns an array of strings or NULL.</p></description>
<example>u\DebugUtility::dump( $sd->getPossibleValues( "group;multiselect" ) );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPossibleValues( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getPossibleValues();
    }
    
/**
<documentation><description><p>Returns the <code>$service</code> object.</p></description>
<example>u\DebugUtility::dump( $sd->getService() );</example>
<return-type>AssetOperationHandlerService</return-type>
<exception></exception>
</documentation>
*/
    public function getService() : aohs\AssetOperationHandlerService
    {
        return $this->service;
    }
    
/**
<documentation><description><p>Returns <code>symlinkId</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getSymlinkId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkId( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getSymlinkId();
    }
    
/**
<documentation><description><p>Returns <code>symlinkPath</code> of the named node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getSymlinkPath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkPath( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getSymlinkPath();
    }
    
/**
<documentation><description><p>An alias of <code>getNode( $identifier )</code>.</p></description>
<example></example>
<return-type>StructuredDataNode</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function getStructuredDataNode( string $identifier ) : StructuredDataNode
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ];
    }
/*/
    public function getStructuredDataValues() : array
    {
        $values = array();
        
        foreach( $this->getIdentifiers() as $fqi )
        {
        	$node = $this->getStructuredDataNode( $fqi );
        	
        	if( $node->isGroupNode() )
        	{
        		continue;  // skip group nodes
        	}
        	
            $values[ $fqi ] = $node->getNodeValue();
        }
        
        return $values;
    }
/*/
/**
<documentation><description><p>Returns the text of a node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getText( "group;calendar" ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getText( string $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getText();
    }
    
/**
<documentation><description><p>Returns the type string of an text node (an text node is an
instance of a normal text field (including multi-line and WYSIWYG), which are
associated with <code>NULL</code>, or a text field of
type <code>datetime</code>, <code>calendar</code>, <code>multi-selector</code>,
<code>dropdown</code>, or <code>checkbox</code>).</p></description>
<example>echo u\StringUtility::getCoalescedString( $sd->getTextNodeType( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function getTextNodeType( string $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        $node = $this->node_map[ $identifier ];
        
        if( $node->getType() != c\T::TEXT )
        {
            throw new e\NodeException( 
                S_SPAN . "This node is not a text node." . E_SPAN );
        }

        return $node->getTextType();
    }
    
/**
<documentation><description><p>Returns the type string. The returned value is either <code>Page::TYPE</code> or <code>DataDefinitionBlock::TYPE</code>.</p></description>
<example>echo $sd->getType(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getType() : string
    {
        return $this->type;
    }
    
/**
<documentation><description><p>An alias of <code>hasNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasIdentifier( string $identifier ) : bool
    {
        return $this->hasNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the identifier exists.</p></description>
<example>if( $sd->hasNode( $id ) )
{
    echo $sd->getAssetNodeType( $id ), BR;
}</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasNode( string $identifier ) : bool
    {
        return in_array( $identifier, $this->identifiers );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether there are phantom nodes
of type B in the structured data.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPhantomNodes() : bool // detects phantom nodes of type B
    {
        $dd_ids   = $this->data_definition->getIdentifiers();
        $sd_ids   = $this->getIdentifiers();
        $temp_ids = array();
        
        foreach( $sd_ids as $id )
        {
            $temp_ids[] = 
                u\StringUtility::getFullyQualifiedIdentifierWithoutPositions( $id );
        }
        
        foreach( $dd_ids as $id )
        {
            if( !in_array( $id, $temp_ids ) )
            {
                //echo "Phantom node identifier: ", $id, BR;
                return true;
            }
        }
        return false;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether there are phantom values
in the structured data.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPhantomValues() : bool // detects phantom values
    {
        $dd_ids   = $this->data_definition->getIdentifiers();
        $sd_ids   = $this->getIdentifiers();
        $temp_ids = array();
        
        foreach( $sd_ids as $id )
        {
            if( $this->hasPossibleValues( $id ) )
            {
                $possible_values = $this->data_definition->getPossibleValues( $id );
                $actual_values   =
                    u\StringUtility::getExplodedStringArray(
                        a\DataDefinition::DELIMITER,
                        str_replace(
                            StructuredDataNode::SELECTOR_PREFIX,
                            "",
                            str_replace(
                                structuredDataNode::CHECKBOX_PREFIX,
                                "",
                                $this->getText( $id )
                            )
                        )
                    );
                
                if( is_array( $actual_values ) )
                {
                    foreach( $actual_values as $value )
                    {
                        if( !in_array( $value, $possible_values ) )
                        {
                            //echo $identifier, BR;
                            return true;
                        }
                    }
                }
                elseif( !in_array( $actual_values, $possible_values ) )
                {
                    //echo $identifier, BR;
                    return true;
                }
            }
        }
        return false;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether this node has possible values.</p></description>
<example>if( $sd->hasPossibleValues( $id ) )
    u\DebugUtility::dump( $sd->getPossibleValues( $id ) );
</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPossibleValues( string $identifier ) : bool
    {
        if( isset( $this->node_map[ $identifier ] ) && 
            $this->node_map[ $identifier ]->getType() == "text" )
        {
            $type = $this->node_map[ $identifier ]->getTextNodeType();
            
            return $type == c\T::CHECKBOX || $type == c\T::DROPDOWN ||
                $type == c\T::RADIOBUTTON || $type == c\T::MULTISELECTOR;
        }
        return false;
    }
    
/**
<documentation><description><p>An alias of <code>isAssetNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isAsset( string $identifier ) : bool
    {
        return $this->isAssetNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is an
asset node, allowing users to choose an asset.</p></description>
<example>if( $sd->isAsset( "group;block-chooser" ) )
{
    echo $sd->getAssetNodeType( "group;block-chooser" ), BR;
}</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isAssetNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isAssetNode();
    }
    
/**
<documentation><description><p>An alias of <code>isBlockChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isBlockChooser( string $identifier ) : bool
    {
        return $this->isBlockChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
block chooser node, allowing users to choose a block.</p></description>
<example>if( $sd->isBlockChooserNode( $id ) )
{
    echo u\StringUtility::getCoalescedString( $sd->getBlockId( $id ) ), BR;
    echo u\StringUtility::getCoalescedString( $sd->getBlockPath( $id ) ), BR;
}</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isBlockChooserNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isBlockChooser();
    }
    
/**
<documentation><description><p>An alias of <code>isCalendarNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isCalendar( string $identifier ) : bool
    {
        return $this->isCalendarNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
calendar node.</p></description>
<example>if( $sd->isCalendarNode( $id ) )
    echo u\StringUtility::getCoalescedString( $sd->getText( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isCalendarNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isCalendarNode();
    }
    
/**
<documentation><description><p>An alias of <code>isCheckboxNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isCheckbox( string $identifier ) : bool
    {
        return $this->isCheckboxNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
checkbox node.</p></description>
<example>if( $sd->isCheckboxNode( $id ) )
    echo u\StringUtility::getCoalescedString( $sd->getText( $id ) ), BR;
</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isCheckboxNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isCheckboxNode();
    }
    
/**
<documentation><description><p>An alias of <code>isDatetimeNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isDatetime( string $identifier ) : bool
    {
        return $this->isDatetimeNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
datetime node.</p></description>
<example>if( $sd->isDatetimeNode( $id ) )
    echo u\StringUtility::getCoalescedString( $sd->getText( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isDatetimeNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isDatetimeNode();
    }
    
/**
<documentation><description><p>An alias of <code>isDropdownNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isDropdown( string $identifier ) : bool
    {
        return $this->isDropdownNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
dropdown node.</p></description>
<example>if( $sd->isDropdownNode( $id ) )
    echo u\StringUtility::getCoalescedString( $sd->getText( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isDropdownNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isDropdownNode();
    }
    
/**
<documentation><description><p>An alias of <code>isFileChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isFileChooser( string $identifier ) : bool
    {
        return $this->isFileChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a file
chooser node, allowing users to choose a file.</p></description>
<example>if( $sd->isFileChooserNode( $id ) )
{
    echo u\StringUtility::getCoalescedString( $sd->getFileId( $id ) ), BR;
}</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isFileChooserNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isFileChooser();
    }
    
/**
<documentation><description><p>An alias of <code>isGroupNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isGroup( string $identifier ) : bool
    {
        return $this->isGroupNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
group node.</p></description>
<example>if( $sd->isGroupNode( $id ) )
{
    echo "A group", BR;
}</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isGroupNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isGroupNode();
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is the
first node of a set of multiple nodes.</p></description>
<example>echo u\StringUtility::boolToString(
    $sd->isIdentifierOfFirstNode( "multiple-second;1" ) );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isIdentifierOfFirstNode( string $identifier ) : bool
    {
        if( $this->isMultiple( $identifier ) )
        {
            return u\StringUtility::endsWith( $identifier, ";0" );
        }
        return false;
    }
    
/**
<documentation><description><p>An alias of <code>isLinkableChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isLinkableChooser( string $identifier ) : bool
    {
        return $this->isLinkableChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
linkable chooser node, allowing users to choose a file, a page, or a symlink.</p></description>
<example>if( $sd->isLinkableChooserNode( $id ) )
{
    echo u\StringUtility::getCoalescedString( $sd->getLinkableId( $id ) ), BR;
}</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isLinkableChooserNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isLinkableChooser();
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isMultiLine( string $identifier ) : bool
    {
        return $this->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the node is a multi-line
node (i.e., textarea).</p></description>
<example>if( $sd->isMultiLineNode( $id ) )
    echo u\StringUtility::getCoalescedString( $sd->getText( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isMultiLineNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isMultiLineNode();
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the node is a multiple
node.</p></description>
<example>if( $sd->isMultiple( $id ) )
    echo "Multiple", BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isMultiple( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isMultiple();
    }
    
/**
<documentation><description><p>An alias of <code>isMultiSelectorNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isMultiSelector( string $identifier ) : bool
    {
        return $this->isMultiSelectorNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the node is a multi-selector node.</p></description>
<example>if( $sd->isMultiSelectorNode( $id ) )
    echo "Multi selector", BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isMultiSelectorNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isMultiSelectorNode();
    }
    
/**
<documentation><description><p>An alias of <code>isPageChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isPageChooser( string $identifier ) : bool
    {
        return $this->isPageChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a page
chooser node, allowing users to choose a page.</p></description>
<example>if( $sd->isPageChooserNode( $id ) )
    echo u\StringUtility::getCoalescedString( $sd->getPageId( $id ) ), BR;
</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isPageChooserNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isPageChooser();
    }
    
/**
<documentation><description><p>An alias of <code>isRadioNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isRadio( string $identifier ) : bool
    {
        return $this->isRadioNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the node is a radio node.</p></description>
<example>if( $sd->isRadioNode( $id ) )
    echo "Radio", BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isRadioNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isRadioNode();
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field value is required by the named field.</p></description>
<example>if( $sd->isRequired( $id ) )
    echo "Required", BR;
</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isRequired( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isRequired();
    }

/**
<documentation><description><p>An alias of <code>isSymlinkChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isSymlinkChooser( string $identifier ) : bool
    {
        return $this->isSymlinkChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a symlink chooser node, allowing users to choose a symlink.</p></description>
<example>if( $sd->isSymlinkChooserNode( $id ) )
    echo u\StringUtility::getCoalescedString( $sd->getSymlinkId( $id ) ), BR;
</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isSymlinkChooserNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isSymlinkChooser();
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isTextarea( string $identifier ) : bool
    {
        return $this->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isTextareaNode( string $identifier ) : bool
    {
        return $this->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isTextBoxNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isTextBox( string $identifier ) : bool
    {
        return $this->isTextBoxNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the node is a simple text box node.</p></description>
<example>if( $sd->isTextBoxNode( $id ) )
    echo "Text box", BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isTextBoxNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isTextBox();
    }
    
/**
<documentation><description><p>An alias of <code>isTextNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isText( string $identifier ) : bool
    {
        return $this->isTextNode( $identifier );
    }
    
/**
<documentation><description><p>Returns returns a bool, indicating whether the named node is a text node (vs. group and asset).</p></description>
<example>if( $sd->isTextNode( $id ) )
    echo "Text node", BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isTextNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isTextNode();
    }
    
/**
<documentation><description><p>An alias of <code>isWYSIWYGNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isWYSIWYG( string $identifier ) : bool
    {
        return $this->isWYSIWYGNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named field is a WYSIWYG text field.</p></description>
<example>if( $sd->isWYSIWYGNode( $id ) )
    echo "WYSIWYG node", BR;</example>
<return-type>bool</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function isWYSIWYGNode( string $identifier ) : bool
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isWYSIWYGNode();
    }
    
/**
<documentation><description><p>Returns a new <code>StructuredData</code> object, containing all the data licensed by the data definition. The method can be used to remove phantom nodes of type B.</p></description>
<example>u\DebugUtility::dump( $sd->mapData()->toStdClass() );</example>
<return-type>StructuredData</return-type>
<exception></exception>
</documentation>
*/
    public function mapData() : StructuredData
    {
        $new_sd  = new StructuredData( 
            $this->data_definition->getStructuredData(), 
            $this->getService(), $this->data_definition->getId() );
        $cur_ids = $this->getIdentifiers();
        
        foreach( $cur_ids as $id )
        {
            if( $this->isIdentifierOfFirstNode( $id ) )
            {
                $num_of_instances = $this->getNumberOfSiblings( $id );
                
                if( $num_of_instances > 1 )
                {
                    $new_sd->createNInstancesForMultipleField( $num_of_instances, $id );
                }
            }
        }
        
        foreach( $cur_ids as $id )
        {
            self::copyData( $this, $new_sd, $id );
        }
        
        return $new_sd;
    }
    
/**
<documentation><description><p>Removes the last node from a set of nodes, and returns the object. The identifier supplied must the the fully qualified identifier of the first node of the set.</p></description>
<example>$sd->removeLastSibling( "multiple-first;0" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function removeLastSibling( string $first_node_id ) : Property
    {
        if( !$this->hasIdentifier( $first_node_id ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $first_node_id does not exist." . E_SPAN );
        }
        
        $first_node = $this->node_map[ $first_node_id ];
        $field_id   = StructuredDataNode::getFieldIdentifier( $first_node_id );
        
        // non-ambiguous path, no multiple ancestor
        if( strpos( $first_node_id, $field_id ) !== false )
        {
            return $this->removeLastNodeFromField( $field_id );
        }
        // with multiple ancestor
        $parent_id   = $first_node->getParentId();
        $parent_node = $this->node_map[ $parent_id ];
        
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $parent_id ); }
        $shared_id = StructuredDataNode::removeLastIndex( $first_node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Shared ID: " . $shared_id ); }
        
        $shared_id = StructuredDataNode::removeLastIndex( $first_node_id );
        $parent_node->removeLastChildNode( $shared_id );
        $this->identifiers = array_keys( $this->node_map );

        return $this;
    }
    
/**
<documentation><description><p>Accepts a <code>p\StructuredDataPhantom</code> object, removes all the phantom nodes of type A, and returns the resulting <code>StructuredData</code> object.</p></description>
<example></example>
<return-type>StructuredData</return-type>
<exception></exception>
</documentation>
*/
    public function removePhantomNodes( StructuredDataPhantom $sdp ) : StructuredData
    {
        $new_sd  = new StructuredData( 
            $this->data_definition->getStructuredData(), $this->getService(),
            $this->data_definition->getId() );
        $sdp_ids = $sdp->getIdentifiers();
        
        foreach( $sdp_ids as $id )
        {
            try
            {
                if( $this->isIdentifierOfFirstNode( $id ) )
                {
                    $num_of_instances = $sdp->getNumberOfSiblings( $id );
                    
                    if( $num_of_instances > 1 )
                    {
                        $new_sd->createNInstancesForMultipleField( $num_of_instances, $id );
                    }
                }
            }
            catch( e\NodeException $e )
            {
                continue; // skip phantom nodes
            }
        }

        foreach( $sdp_ids as $id )
        {
            try
            {
                self::copyData( $sdp, $new_sd, $id );
            }
            catch( e\NodeException $e )
            {
                continue; // skip phantom nodes
            }
            catch( e\NoSuchFieldException $e )
            {
                continue; // skip phantom nodes
            }
        }
        return $new_sd;
    }
    
    public function removePhantomValues() : Property
    {
        $ids = $this->identifiers;
    
        foreach( $this->identifiers as $identifier )
        {
            $this->node_map[ $identifier ]->removePhantomValues();
        }
        return $this;
    }

/**
<documentation><description><p>Replaces the pattern with the replacement string for normal
text fields, and fields of type datetime and calendar, and returns the calling object.
Inside the method <code>preg_replace</code> is called. If an array of fully qualified
identifiers is also passed in, then only those nodes will be affected.</p></description>
<example>if( $sd->isWYSIWYGNode( $id ) )
{
    $sd->replaceByPattern(
        "/" . "&lt;" . "p&gt;([^&lt;]+)&lt;\/p&gt;/", 
        "&lt;div class='text_red'&gt;$1&lt;/div&gt;", 
        array( $id )
    )->getHostAsset()->edit();
}</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function replaceByPattern(
        string $pattern, string $replace, array $include=NULL ) : Property
    {
        $check = false;
        
        if( is_array( $include ) )
        {
            $check = true;
        }
        
        foreach( $this->identifiers as $identifier )
        {
            if( $check && !in_array( $identifier, $include ) )
            {
                continue; // skip this one
            }
            
            $cur_node = $this->node_map[ $identifier ];
        
            $current_text = $cur_node->getText();
        
            // including WYSIWYG
            if( $cur_node->getType() == c\T::TEXT &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_CHECKBOX &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_DROPDOWN &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_RADIO &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_SELECTOR
            )
            {
                $new_text = preg_replace( $pattern, $replace, $current_text );
                
                $this->setText( $identifier, $new_text );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Replaces the string found with the replacement string for
normal text fields, and fields of type datetime and calendar, and returns the calling
object. Inside the method <code>str_replace</code> is called. If an array of fully
qualified identifiers is also passed in, then only those nodes will be affected.</p></description>
<example>// affects all text nodes    
$sd->replaceText( "Wonderful", "Amazing" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function replaceText(
        string $search, string $replace, array $include=NULL ) : Property
    {
        $check = false;
        
        if( is_array( $include ) )
        {
            $check = true;
        }
        
        foreach( $this->identifiers as $identifier )
        {
            if( $check && !in_array( $identifier, $include ) )
            {
                continue; // skip this one
            }
            
            $cur_node = $this->node_map[ $identifier ];
        
            $current_text = $cur_node->getText();
        
            // including WYSIWYG
            if( $cur_node->getType() == c\T::TEXT &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_CHECKBOX &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_DROPDOWN &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_RADIO &&
                $cur_node->getTextType() != StructuredDataNode::TEXT_TYPE_SELECTOR &&
                strpos( $current_text, $search ) !== false )
            {
                $new_text = str_replace( $search, $replace, $current_text );
                
                $this->setText( 
                    $identifier,
                    $new_text
                );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Searches all the nodes of type <code>text</code> (excluding
<code>asset</code> and <code>group</code>) for the string, and returns an array of fully
qualified identifiers of nodes where the string is found. Inside the method <code>strpos</code> is used.</p></description>
<example>u\DebugUtility::dump( $sd->searchText( "Amazing" ) );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function searchText( string $string ) : array
    {
        $identifiers = array();
        
        foreach( $this->identifiers as $identifier )
        {
            $cur_node = $this->node_map[ $identifier ];
        
            if( $cur_node->getType() == c\T::TEXT && 
                strpos( $cur_node->getText(), $string ) !== false )
            {
                $identifiers[] = $identifier;
            }
        }
        return $identifiers;
    }
    
/**
<documentation><description><p>Searches all the nodes of type <code>text</code> (excluding
<code>asset</code> and <code>group</code>) for the pattern, and returns an array of fully
qualified identifiers of nodes where the pattern is found. Inside the method <code>preg_match</code> is used.</p></description>
<example>u\DebugUtility::dump( $sd->searchTextByPattern( "/" . "&lt;" . "p&gt;([^&lt;]+)&lt;\/p&gt;/" ) );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function searchTextByPattern( string $pattern ) : array
    {
        $identifiers = array();
        
        foreach( $this->identifiers as $identifier )
        {
            $cur_node = $this->node_map[ $identifier ];
        
            // only one instance is enough, hence preg_match, not preg_match_all
            if( $cur_node->getType() == c\T::TEXT &&
                preg_match( $pattern, $cur_node->getText() ) == 1 )
            {
                $identifiers[] = $identifier;
            }
        }
        return $identifiers;
    }
    
/**
<documentation><description><p>Searches all the WYSIWYG nodes for the pattern, and returns an array of fully qualified identifiers of nodes where the pattern is found. Inside the method <code>preg_match</code> is used.</p></description>
<example>u\DebugUtility::dump( $sd->searchWYSIWYGByPattern( "/" . "&lt;" . 
"p&gt;([^&lt;]+)&lt;\/p>/" ) );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function searchWYSIWYGByPattern( string $pattern ) : array
    {
        $identifiers = array();
        
        foreach( $this->identifiers as $identifier )
        {
            $cur_node = $this->node_map[ $identifier ];
        
            // only one instance is enough, hence preg_match, not preg_match_all
            if( $cur_node->getType() == c\T::TEXT &&
                $cur_node->isWYSIWYG() &&
                preg_match( $pattern, $cur_node->getText() ) == 1 )
            {
                $identifiers[] = $identifier;
            }
        }
        return $identifiers;
    }
    
/**
<documentation><description><p>Sets the node's <code>blockId</code> and <code>blockPath</code> properties, and returns the calling object.</p></description>
<example>$sd->setBlock(
    "group;block-chooser",
    $cascade->getAsset(
        a\DataBlock::TYPE, "1f21e3268b7ffe834c5fe91e2e0a7b2d" ) )->
    getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setBlock( string $node_name, a\Block $block=NULL ) : Property
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $block ); }
        if( self::DEBUG ) { u\DebugUtility::out( $node_name ); }
        
        try
        {
            if( isset( $this->node_map[ $node_name ] ) )
                $this->node_map[ $node_name ]->setBlock( $block );
            return $this;
        }
        catch( \Exception $e )
        {
            $this->throwException( $e );
        }
    }
    
/**
<documentation><description><p>Sets <code>definitionId</code>, and <code>definitionPath</code>, and returns the calling object. Note that this method is meaningless unless the current structured data is replaced by a new structured data if a different data definition is involved.</p></description>
<example>u\DebugUtility::dump( $sd->setDataDefinition(
    $cascade->getAsset(
        a\DataDefinition::TYPE, "1f24065d8b7ffe834c5fe91e95372ce1" ) )->
    toStdClass() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setDataDefinition( a\DataDefinition $dd ) : Property
    {
        $this->definition_id   = $dd->getId();
        $this->definition_path = $dd->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets the node's <code>fileId</code> and <code>filePath</code> properties, and returns the calling object.</p></description>
<example>$sd->setBlock(
    "group;block-chooser",
    $cascade->getAsset(
        a\DataBlock::TYPE, "1f21e3268b7ffe834c5fe91e2e0a7b2d" ) )->
    setFile( "group;file-chooser" )-> 
    setPage( "group;page-chooser" )-> 
    setLinkable( "group;linkable-chooser" )-> 
    setSymlink( "group;symlink-chooser" )->
    getHostAsset()->edit();
</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setFile( string $node_name, a\File $file=NULL ) : Property
    {
        try
        {
            if( isset( $this->node_map[ $node_name ] ) )
                $this->node_map[ $node_name ]->setFile( $file );
            return $this;
        }
        catch( \Exception $e )
        {
            $this->throwException( $e );
        }
    }
    
/**
<documentation><description><p>Sets the node's <code>fileId</code> and <code>filePath</code>, or <code>pageId</code> and <code>pagePath</code>,
or <code>symlinkId</code> and <code>symlinkPath</code> properties, depending on what is passed in, and returns the calling object.</p></description>
<example>$sd->setBlock(
    "group;block-chooser",
    $cascade->getAsset(
        a\DataBlock::TYPE, "1f21e3268b7ffe834c5fe91e2e0a7b2d" ) )->
    setFile( "group;file-chooser" )-> 
    setPage( "group;page-chooser" )-> 
    setLinkable( "group;linkable-chooser" )-> 
    setSymlink( "group;symlink-chooser" )->
    getHostAsset()->edit();
</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setLinkable( string $node_name, a\Linkable $linkable=NULL ) : Property
    {
        try
        {
            if( isset( $this->node_map[ $node_name ] ) )
                $this->node_map[ $node_name ]->setLinkable( $linkable );
            return $this;
        }
        catch( \Exception $e )
        {
            $this->throwException( $e );
        }
    }
    
/**
<documentation><description><p>Sets the node's <code>pageId</code> and <code>pathPath</code> properties, and returns the calling object.</p></description>
<example>$sd->setBlock(
    "group;block-chooser",
    $cascade->getAsset(
        a\DataBlock::TYPE, "1f21e3268b7ffe834c5fe91e2e0a7b2d" ) )->
    setFile( "group;file-chooser" )-> 
    setPage( "group;page-chooser" )-> 
    setLinkable( "group;linkable-chooser" )-> 
    setSymlink( "group;symlink-chooser" )->
    getHostAsset()->edit();
</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setPage( string $node_name, a\Page $page=NULL ) : Property
    {
        try
        {
            if( isset( $this->node_map[ $node_name ] ) )
                $this->node_map[ $node_name ]->setPage( $page );
            return $this;
        }
        catch( \Exception $e )
        {
            $this->throwException( $e );
        }
    }
    
/**
<documentation><description><p>Sets the node's <code>symlinkId</code> and <code>symlinkPath</code> properties, and returns the calling object.</p></description>
<example>$sd->setBlock(
    "group;block-chooser",
    $cascade->getAsset(
        a\DataBlock::TYPE, "1f21e3268b7ffe834c5fe91e2e0a7b2d" ) )->
    setFile( "group;file-chooser" )-> 
    setPage( "group;page-chooser" )-> 
    setLinkable( "group;linkable-chooser" )-> 
    setSymlink( "group;symlink-chooser" )->
    getHostAsset()->edit();
</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setSymlink( string $node_name, a\Symlink $symlink=NULL ) : Property
    {
        try
        {
            if( isset( $this->node_map[ $node_name ] ) )
                $this->node_map[ $node_name ]->setSymlink( $symlink );
            return $this;
        }
        catch( \Exception $e )
        {
            $this->throwException( $e );
        }
    }
    
/**
<documentation><description><p>Sets the node's <code>text</code>, and returns the calling object.</p></description>
<example>$sd->setText( "group;text-box", "Some new text" )->
    getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException, NoSuchValueException, UnacceptableValueException</exception>
</documentation>
*/
    public function setText( string $node_name, string $text=NULL ) : Property
    {
        try
        {
            if( isset( $this->node_map[ $node_name ] ) )
                $this->node_map[ $node_name ]->setText( $text );
            return $this;
        }
        catch( \Exception $e )
        {
            $this->throwException( $e );
        }
    }
    
/**
<documentation><description><p>Swaps the data of two nodes, and returns the calling object.</p></description>
<example>$sd->swapData( "multiple-first;0", "multiple-first;1" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function swapData( string $node_name1, string $node_name2 ) : Property
    {
        if( !$this->hasIdentifier( $node_name1 ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $node_name1 does not exists." . E_SPAN );
        }
        
        if( !$this->hasIdentifier( $node_name2 ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $node_name2 does not exists." . E_SPAN );
        }
        
        // must be siblings
        if( StructuredDataNode::removeLastIndex( $node_name1 ) != 
            StructuredDataNode::removeLastIndex( $node_name2 ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The nodes $node_name1 and $node_name2 are not siblings." . E_SPAN );
        }
        
        $par_id     = $this->node_map[ $node_name1 ]->getParentId();
        // get the data
        $node1_data = $this->node_map[ $node_name1 ]->toStdClass();
        $node2_data = $this->node_map[ $node_name2 ]->toStdClass();
        
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: $par_id" . 
            BR . "Node 1: $node_name1" . BR . "Node 2: $node_name2" ); }
        
        if( $par_id != '' )
            $siblings = $this->node_map[ $par_id ]->getChildren();
        else
            $siblings = $this->children;
            
        $sibling_count = count( $siblings );
        
        if( self::DEBUG ) { u\DebugUtility::out( "Sibling count: $sibling_count" ); }
        
        for( $i = 0; $i < $sibling_count; $i++ )
        {
            if( self::DEBUG ) { u\DebugUtility::out( $siblings[ $i ]->getIdentifier() ); }
            
            // find the two positions
            if( $siblings[ $i ]->getIdentifier() == $node_name1 )
            {
                $node_pos1 = $i;
                if( self::DEBUG ) { u\DebugUtility::out( "Node 1 position: $node_pos1" ); }
            }
            if( $siblings[ $i ]->getIdentifier() == $node_name2 )
            {
                $node_pos2 = $i;
                if( self::DEBUG ) { u\DebugUtility::out( "Node 2 position: $node_pos2" . BR ); }
            }
        }
        
        // create new nodes
        $new_node1 = new StructuredDataNode( 
            $node2_data, $this->service, $this->data_definition, $node_pos1, 
            $par_id . structuredDataNode::DELIMITER );
        $new_node2 = new StructuredDataNode( 
            $node1_data, $this->service, $this->data_definition, $node_pos2, 
            $par_id . structuredDataNode::DELIMITER );
        
        // insert new nodes
        // must assign new nodes to the original arrays, not $siblings
        if( $par_id != '' )
        {
            $this->node_map[ $par_id ]->swapChildren( 
                $node_pos1, $new_node1, $node_pos2, $new_node2 );
        }
        else
        {
            $this->children[ $node_pos1 ] = $new_node1;
            $this->children[ $node_pos2 ] = $new_node2;
        }
        
        $this->node_map[ $node_name1 ] = $new_node1;
        $this->node_map[ $node_name2 ] = $new_node2;
        
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $sd->toStdClass() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj = new \stdClass();
        
        if( $this->type == a\DataDefinitionBlock::TYPE )
        {
            $obj->definitionId   = $this->definition_id;
            $obj->definitionPath = $this->definition_path;
        }
        
        $child_count = count( $this->children );
        
        if( self::DEBUG ) { u\DebugUtility::out( "child count: $child_count" ); }
        
        if( $child_count == 1 )
        {
            $obj->structuredDataNodes = new \stdClass();
            
            if( $this->service->isSoap() )
                $obj->structuredDataNodes->structuredDataNode =
                    $this->children[0]->toStdClass();
            elseif( $this->service->isRest() )
                $obj->structuredDataNodes =
                    array( $this->children[0]->toStdClass() );
        }
        else
        {
            $obj->structuredDataNodes = new \stdClass();
            
            if( $this->service->isSoap() )
                $obj->structuredDataNodes->structuredDataNode = array();
            elseif( $this->service->isRest() )
                $obj->structuredDataNodes = array();
            
            for( $i = 0; $i < $child_count; $i++ )
            {
                if( $this->service->isSoap() )
                    $obj->structuredDataNodes->structuredDataNode[] =
                        $this->children[$i]->toStdClass();
                elseif( $this->service->isRest() )
                    $obj->structuredDataNodes[] =
                        $this->children[$i]->toStdClass();
            }
        }
        return $obj;
    }
    
    private function appendNodeToField( string $field_name ) : Property
    {
        
        if( self::DEBUG ) { u\DebugUtility::out( $field_name ); }

        if( !$this->data_definition->hasIdentifier( $field_name ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field name $field_name does not exist." . E_SPAN );
        }
        
        if( !$this->data_definition->isMultiple( $field_name ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The field $field_name is not multiple." . E_SPAN );
        }
        
        // get the parent id through the first node
        // alternative: use the field name to work out the parent id
        $first_node = $this->getNode( $field_name . a\DataDefinition::DELIMITER . '0' );
        $par_id     = $first_node->getParentId();
        
        if( $par_id == '' ) // top level
        {
            $child_count = count( $this->children );
            //$first_pos   = StructuredDataNode::getPositionOfFirstNode( $this->children, $field_name );
            $last_pos    = StructuredDataNode::getPositionOfLastNode(
                $this->children, $field_name );
            $cloned_node = $this->children[ $last_pos ]->cloneNode();

            if( $child_count > $last_pos + 1 ) // in the middle
            {
                $before = array_slice( $this->children, 0, $last_pos + 1 );
                $after  = array_slice( $this->children, $last_pos + 1 );
                $this->children = array_merge( $before, array( $cloned_node ), $after );
            }
            else // the last one
            {
                $this->children[] = $cloned_node;
            }
            
            // add new node to map
            $this->node_map = array_merge( 
                $this->node_map, array( $cloned_node->getIdentifier() => $cloned_node ) );
        }
        else
        {
            $this->getNode( $par_id )->addChildNode( $field_name );
        }
        
        // add new identifier to identifiers
        $temp = $this->node_map;
        asort( $temp );
        $this->identifiers = array_keys( $temp );
        
        return $this;
    }
    
    private function removeLastNodeFromField( string $field_name ) : Property
    {
        if( !$this->data_definition->hasIdentifier( $field_name ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field name $field_name does not exist." . E_SPAN );
        }
        
        if( !$this->data_definition->isMultiple( $field_name ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The field $field_name is not multiple." . E_SPAN );
        }
    
        $first_node = $this->getNode( $field_name . a\DataDefinition::DELIMITER . '0' );
        $par_id     = $first_node->getParentId();

        if( $par_id == '' ) // top level
        {
            $last_pos  = StructuredDataNode::getPositionOfLastNode(
                $this->children, $field_name );
            $first_pos = StructuredDataNode::getPositionOfFirstNode(
                $this->children, $field_name );
            $last_id   = $this->children[ $last_pos ]->getIdentifier();
            
            if( $first_pos == $last_pos ) // the only node
            {
                throw new e\NodeException( 
                    S_SPAN . "Cannot remove the only node in the field." . E_SPAN );
            }
            
            $child_count = count( $this->children );
            
            if( $child_count > $last_pos )
            {
                $before = array_slice( $this->children, 0, $last_pos );
                $after = array_slice( $this->children, $last_pos + 1 );
                $this->children = array_merge( $before, $after );
            }
        }
        else
        {
            $this->getNode( $par_id )->removeLastChildNode( $field_name );
        }
        
        if( isset( $last_id ) && isset( $this->node_map[ $last_id ] ) )
            unset( $this->node_map[ $last_id ] );
        $this->identifiers = array_keys( $this->node_map );

        return $this;
    }
    
    private static function copyData( $source, StructuredData $target, string $id )
    {
        if( !$source instanceof StructuredData && 
            !$source instanceof StructuredDataPhantom )
            throw new \Exception( "Wrong source type" );
        
        if( $source->isTextNode( $id ) || $source->isWYSIWYG( $id ) )
        {
            try
            {
                $target->setText( $id, $source->getText( $id ) );
                
                if( $target->getText( $id ) == NULL )
                    $target->setText( $id, "" );
            }
            catch( e\NoSuchValueException $e )
            {
                // do nothing to skip phantom values
            }
        }
        elseif( $source->isAssetNode( $id ) )
        {
            $asset_type = $source->getAssetNodeType( $id );
            
            try
            {
                switch( $asset_type )
                {
                    case c\T::PAGE:
                        $page_id = $source->getPageId( $id );
                    
                        if( isset( $page_id ) )
                        {
                            $target->setPage( $id, 
                                $source->getService()->getAsset(
                                    a\Page::TYPE, $page_id ) );
                        }
                        break;
                    case c\T::FILE:
                        $file_id = $source->getFileId( $id );
                    
                        if( isset( $file_id ) )
                        {
                            $target->setFile( $id, 
                                $source->getService()->getAsset(
                                    a\File::TYPE, $file_id ) );
                        }
                        break;
                    case c\T::BLOCK:
                        $block_id = $source->getBlockId( $id );
                    
                        if( isset( $block_id ) )
                        {
                            $target->setBlock( $id, a\Block::getBlock(
                                $target->getService(), $block_id ) );
                        }
                        break;
                    case c\T::SYMLINK:
                        $symlink_id = $source->getSymlinkId( $id );
                    
                        if( isset( $symlink_id ) )
                        {
                            $target->setSymlink( $id,
                                $source->getService()->getAsset(
                                    a\Symlink::TYPE, $symlink_id ) );
                        }
                        break;
                    case c\T::PFS:
                        $linkable_id = $source->getLinkableId( $id );
                    
                        if( isset( $linkable_id ) )
                        {
                            $target->setLinkable(
                                $id, 
                                a\Linkable::getLinkable(
                                    $source->getService(), $linkable_id ) );
                        }
                        break;
                }
            }
            catch( e\NoSuchTypeException $e )
            {
                // do nothing to skip deleted blocks
            }
        }
    }
    
    private function throwException( \Exception $e )
    {
        if( !is_null( $this->getHostAsset() ) )
        {
            u\DebugUtility::throwException( $this->getHostAsset(), $e );
        }
        else
        {
            u\DebugUtility::throwException( NULL, $e );
        }
    }

    private $definition_id;
    private $definition_path;
    private $children;
    private $identifiers;
    private $data_definition;
    private $node_map;
    private $type; // block or page
    private $host_asset;
    private $service;
}
?>