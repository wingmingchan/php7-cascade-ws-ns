<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/5/2018 Added phantom value-related code.
  * 12/28/2017 Added code to unset properties when NULL.
  * 12/27/2017 Added more REST code.
  * 12/21/2017 Added the $service object to constructor and processStructuredDataNodes so that isSoap and isRest can be called. Changed toStdClass so that it works with REST.
  * 9/19/2017 Fixed a bug in processStructuredDataNodes.
  * 8/1/2017 Added getBlock.
  * 7/18/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 10/17/2016 Bug fixes.
  * 6/2/2016 Added aliases. Replaced most string literals with constants.
  * 6/1/2016 Added isBlockChooser, isCalendarNode, isCheckboxNode, isDatetimeNode, isDropdownNode,
  * isFileChooser, isLinkableChooser, isMultiLineNode, isMultiSelectorNode, isPageChooser,
  * isRadioNode, isSymlinkChooser, isTextBox, and isWYSIWYGNode.
  * 12/23/2015 Fixed a bug in addChildNode.
  * 5/28/2015 Added namespaces.
  * 2/24/2015 Added getPossibleValues.
  * 2/24/2015 Fixed a bug in setText. Added tests of $this->required to deal with empty strings or NULL.
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
<p>A <code>StructuredDataNode</code> object represents a <code>structuredDataNode</code> property found in a <a href=\"http://www.upstate.edu/web-services/api/property-classes/structured-data.php\"><code>StructuredData</code></a> object inside a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/data-definition-block.php\"><code>a\DataDefinitionBlock</code></a> object. This property can also be found in a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/page.php\"><code>a\Page</code></a> object.</p>
<p>A <code>StructuredDataNode</code> object can have descendants of the same <code>StructuredDataNode</code> type. Therefore, there must be recursion in the constructor and the <code>toStdClass</code> method.</p>
<h2>Structure of <code>structureDataNode</code></h2>
<pre>structuredDataNode (stdClass or array of stdClass)
  type
  identifier
  structuredDataNodes
  text
  assetType
  blockId
  blockPath
  fileId
  filePath
  pageId
  pagePath
  symlinkId
  symlinkPath
  recycled
</pre>
<h2>About Identifiers</h2>
<p>Identifiers in a <code>structureDataNode</code> property can be considered in two different ways. First, these identifiers can be found in the corresponding data definition. Identifiers in a data definition has the following restriction: identifiers of siblings must be unique. The same identifier can be used in two different fields, provided they are not siblings.</p>
<p>When a data definition is associated with a page or a data definition block, then identifiers can have a different meaning. A field type with a <code>multiple</code> attribute (set to <code>true</code>), when instantiated as nodes, will have sibling instances sharing the same identifier. These nodes are distinguished by their positions in an array. To complicate things, these nodes can mingle with nodes corresponding to other fields in the same array.</p>
<p>Therefore, to identify a node, just using its identifier is not enough. All instances of a multiple field have the same identifier. The identifier must be combined with the positions. But the position of a multiple field can change, depending on what precedes it. For example, if there are two multiple fields at the same level, and the first field has two instances, then the first instance of the second multiple field will be the third node in the sequence. But if we add one more instance to the first field, then the first instance of the second field becomes the fourth node.</p>
<p>To be able to identify these nodes, I decide to use what I called <strong>fully qualified identifiers</strong>. A fully qualified identifier of a node looks like a full URL or file path. It contains all the identifiers of the nodes ancestors and its own identifier, each separated by a semi-colon. For example, a text field whose identifier is <code>test-text</code> at the root level will have a fully qualified identifier <code>test-text</code> because it has no parent. But if this field occurs in a group whose identifier is <code>test-group</code>, then the fully qualified identifier of the text field will be <code>test-group;test-text</code>, provided that the group has no parent.</p>
<p>For a field allowing multiple instances, the fully qualified identifier of the first instance is suffixed with '<code>;0</code>', the second instance is suffixed with '<code>;1</code>' and so on. Therefore, if our text field in the group <code>test-group</code> is a multiple text field, then the fully qualified identifier of the first instance of the text will be <code>test-group;test-text;0</code>. Note that this fully qualified identifier remain unchanged even if we add more instances to other fields preceding it. The '<code>;0</code>' part indicates that this is the first instance of this field. Now I can use these fully qualified identifiers as keys for quick look-up.</p>
<h2>Design Issues</h2>
<ul>
<li>A <code>StructuredDataNode</code> object contains a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/data-definition.php\"><code>a\DataDefinition</code></a> object. When a text value assigned to a node, the text value is checked against the definition of the field to make sure it is a valid value.</li>
<li>Possible values of a multiple-item field can be retrieved using the <code>getItems</code> method.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "structured-data-nodes" ),
        array( "getComplexTypeXMLByName" => "structured-data-node" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-type" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-asset-type" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/structured_data_node.php">structured_data_node.php</a></li></ul></postscript>
</documentation>
*/
class StructuredDataNode extends Property
{
    const DEBUG = false;

    const DELIMITER          = a\DataDefinition::DELIMITER;
    const CHECKBOX_PREFIX    = '::CONTENT-XML-CHECKBOX::';
    const SELECTOR_PREFIX    = '::CONTENT-XML-SELECTOR::';
    const TEXT_TYPE_CALENDAR = c\T::CALENDAR;
    const TEXT_TYPE_CHECKBOX = c\T::CHECKBOX;
    const TEXT_TYPE_DATETIME = c\T::DATETIME;
    const TEXT_TYPE_DROPDOWN = c\T::DROPDOWN;
    const TEXT_TYPE_RADIO    = c\T::RADIOBUTTON;
    const TEXT_TYPE_SELECTOR = c\T::MULTISELECTOR;
    
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $node=NULL,
        aohs\AssetOperationHandlerService $service=NULL,
        $dd=NULL, 
        $index=NULL, 
        $parent_id=NULL ) 
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
    
        if( isset( $node ) ) // $node always a single non-NULL object
        {
            $this->parent_id       = $parent_id;
            
            if( isset( $node->type ) )
                $this->type        = $node->type;
                
            $this->data_definition = $dd;
            $this->node_map        = array();
            
            // attach parent identifier to current node identifier
            // note that parent_id ends with a semi-colon
            $this->identifier = $parent_id . $node->identifier;
            
            $field_identifier = self::getFieldIdentifier( $this->identifier );
            $field            = $this->data_definition->getField( $field_identifier );
            
            // check if this is a multiple field
            if( isset( $field[ c\T::MULTIPLE ] ) )
            {
                $this->multiple = $field[ c\T::MULTIPLE ];
            }
            
            // check if this is a radio
            if( isset( $field[ c\T::TYPE ] ) )
            {
                $this->radio = ( $field[ c\T::TYPE ] == c\T::RADIOBUTTON );
            }
            
            // store the items for radio, multi-selectors, and so on
            if( isset( $field[ c\T::ITEMS ] ) )
            {
                $this->items = $field[ c\T::ITEMS ];
            }
            
            // is it required?
            if( isset( $field[ c\T::REQUIRED ] ) )
            {
                $this->required = $field[ c\T::REQUIRED ];
            }
            else
            {
                $this->required = false;
            }
            
            // type mostly for setText
            if( isset( $field[ c\T::TYPE ] ) )
            {
                $this->text_type = $field[ c\T::TYPE ];
            }
            
            // is it multi-line?
            if( isset( $field[ c\T::MULTILINE ] ) )
            {
                $this->multi_line = $field[ c\T::MULTILINE ];
            }
            else
            {
                $this->multi_line = false;
            }
            
            // is it wysiwyg?
            if( isset( $field[ c\T::WYSIWYG ] ) )
            {
                $this->wysiwyg = $field[ c\T::WYSIWYG ];
            }
            else
            {
                $this->wysiwyg = false;
            }
            
            // add the index if this is a multiple field
            if( $this->multiple == true )
            {
                $this->index       = $index;
                $this->identifier .= self::DELIMITER . $this->index;
            }
            
            if( $this->type != c\T::GROUP ) // text or asset
            {
                $this->structured_data_nodes = NULL;
                $this->text         = NULL;
                $this->asset_type   = NULL;
                $this->block_id     = NULL;
                $this->block_path   = NULL;
                $this->file_id      = NULL;
                $this->file_path    = NULL;
                $this->page_id      = NULL;
                $this->page_path    = NULL;
                $this->symlink_id   = NULL;
                $this->symlink_path = NULL;
                
                if( isset( $node->text ) )
                    $this->text         = $node->text;
                if( isset( $node->assetType ) )
                    $this->asset_type   = $node->assetType;
                if( isset( $node->blockId ) )
                    $this->block_id     = $node->blockId;
                if( isset( $node->blockPath ) )
                    $this->block_path   = $node->blockPath;
                if( isset( $node->fileId ) )
                    $this->file_id      = $node->fileId;
                if( isset( $node->filePath ) )
                    $this->file_path    = $node->filePath;
                if( isset( $node->pageId ) )
                    $this->page_id      = $node->pageId;
                if( isset( $node->pagePath ) )
                    $this->page_path    = $node->pagePath;
                if( isset( $node->symlinkId ) )
                    $this->symlink_id   = $node->symlinkId;
                if( isset( $node->symlinkPath ) )
                    $this->symlink_path = $node->symlinkPath;
                $this->node_map     = array( $this->identifier => $this );
            }
            else // group
            {
                $this->structured_data_nodes = array();
                $this->text         = NULL;
                $this->asset_type   = NULL;
                $this->block_id     = NULL;
                $this->block_path   = NULL;
                $this->file_id      = NULL;
                $this->file_path    = NULL;
                $this->page_id      = NULL;
                $this->page_path    = NULL;
                $this->symlink_id   = NULL;
                $this->symlink_path = NULL;
            
                $cur_identifier     = $this->identifier;
                // make sure there is exactly one trailing delimiter
                $cur_identifier = trim( $cur_identifier, self::DELIMITER );
                $cur_identifier .= self::DELIMITER;
                
                // recursively process the data
                if( $service->isSoap() )
                    self::processStructuredDataNodes( 
                        $cur_identifier, // the parent id
                        $this->structured_data_nodes, // array to store children
                        $node->structuredDataNodes->structuredDataNode, // stdClass
                        $this->data_definition,
                        $this->service
                    );
                elseif( $service->isRest() )
                    self::processStructuredDataNodes( 
                        $cur_identifier, // the parent id
                        $this->structured_data_nodes, // array to store children
                        $node->structuredDataNodes,   // array
                        $this->data_definition,
                        $this->service
                    );
                
                // for easy look-up
                $this->node_map[ $this->identifier ] = $this;
                
                foreach( $this->structured_data_nodes as $child_node )
                {
                    $this->node_map = array_merge( 
                        ( array )$this->node_map, ( array )$child_node->node_map );
                }
            }
        
            $this->recycled = $node->recycled;
        }
    }
    
/**
<documentation><description><p>Adds a node to a multiple field bearing the identifier and returns the calling object. Note that the identifier must be a fully qualified identifier in the data definition (without any <code>;digit</code> in it). This means that the field cannot have any ancestors of multiple type. This methods is used by the <code>StructuredData</code> class.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function addChildNode( string $node_id ) : Property
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $this->structured_data_nodes ); }
    
        if( $this->structured_data_nodes == NULL )
        {
            throw new e\NodeException(
                S_SPAN . "Cannot add a node to a node that has no children." . E_SPAN );
        }
        
        // remove digits and semi-colons, turning node id to field id
        $field_id = self::getFieldIdentifier( $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Field ID: " . $field_id ); }
        
        if( !$this->data_definition->isMultiple( $field_id ) )
        {
            throw new e\NodeException(
                S_SPAN . "Cannot add a node to a non-multiple field." . E_SPAN );
        }

        $last_pos    = self::getPositionOfLastNode( $this->structured_data_nodes, $field_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Last position: " . $last_pos ); }
        
        // create a copy of the last sibling
        $cloned_node = $this->structured_data_nodes[ $last_pos ]->cloneNode();
        if( self::DEBUG ) { u\DebugUtility::dump( $cloned_node->toStdClass() ); }

        $this->structured_data_nodes[] = $cloned_node;
        $this->node_map = array_merge( 
            $this->node_map, array( $cloned_node->getIdentifier() => $cloned_node ) );

        return $this;
    }
    
/**
<documentation><description><p>Returns a copy of the calling node. Since the identifier of the new node and all identifiers of the descendants of this new node must be recalculated, the node is created by the constructor, not by copying.</p></description>
<example>u\DebugUtility::dump( $node->cloneNode()->toStdClass() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function cloneNode() : Property
    {
        // clone the calling node
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $this->parent_id ); }
        
        $clone_obj = new StructuredDataNode( 
            $this->toStdClass(), $this->service, $this->data_definition, 0, 
            $this->parent_id );
            
        if( self::DEBUG ) { u\DebugUtility::dump( $clone_obj->toStdClass() ); }
        
        // work out the new identifier
        $this_identifier       = $this->identifier;
        if( self::DEBUG ) { u\DebugUtility::out( $this_identifier ); }
        $index                 = self::getLastIndex( $this->identifier ) + 1;
        $clone_identifier      = self::removeLastIndex( $this->identifier ) . 
                                 self::DELIMITER . $index;
        $clone_obj->identifier = $clone_identifier;
        
        if( self::DEBUG ) { u\DebugUtility::out( $clone_identifier ); }
        
        return $clone_obj;
    }
    
/**
<documentation><description><p>Displays some basic information and returns the calling object.</p></description>
<example>$node->display();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function display() : Property
    {
        switch( $this->type )
        {
            case c\T::ASSET:
                break;
                
            case c\T::GROUP:
                echo "Type: " . $this->type . BR .
                     "Identifier: " . $this->identifier . BR,
                     "Children size: " . count( $this->structured_data_nodes ) . BR;
                break;
                
            case c\T::TEXT:
                echo "Type: " . $this->type . BR .
                     "Identifier: " . $this->identifier . BR;
                break;
        }
        return $this;
    }
    
/**
<documentation><description><p>Dumps and returns the calling object.</p></description>
<example>$node->dump();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function dump() : Property
    {
        echo S_PRE;
        var_dump( $this );
        echo S_PRE;
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>assetType</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getAssetType() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getAssetType()
    {
        return $this->asset_type;
    }

/**
<documentation><description><p>Returns the block attached to this node or <code>null</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/

    public function getBlock( aohs\AssetOperationHandlerService $service )
    {
        if( !is_null( $this->block_id ) )
        {
            return a\Block::getBlock( $service, $this->block_id );
        }
        return null;
    }

/**
<documentation><description><p>Returns <code>blockId</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockId()
    {
        return $this->block_id;
    }
    
/**
<documentation><description><p>Returns <code>blockPath</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockPath()
    {
        return $this->block_path;
    }
    
/**
<documentation><description><p>An alias of <code>getStructuredDataNodes()</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getChildren()
    {
        return $this->getStructuredDataNodes();
    }
    
/**
<documentation><description><p>Returns the <code>a\DataDefinition</code> object.</p></description>
<example>u\DebugUtility::dump( $node->getDataDefinition() );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getDataDefinition()
    {
        return $this->data_definition;
    }
    
/**
<documentation><description><p>Returns <code>fileId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getFileId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFileId()
    {
        return $this->file_id;
    }
    
/**
<documentation><description><p>Returns <code>filePath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getFilePath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFilePath()
    {
        return $this->file_path;
    }
    
/**
<documentation><description><p>Returns the fully qualified identifier of this node.</p></description>
<example>echo $node->getIdentifier(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifier() : string
    {
        return $this->identifier;
    }
    
/**
<documentation><description><p>For a non-group node, the returned node map contains only one entry, namely, the identifier pointing to the object itself; for a group node, the returned node map contains the entry of the identifier of the group to itself, and all the entries of its children. The resulting node map facilitates easy and quick look-up of objects using their fully qualified identifiers.</p></description>
<example>u\DebugUtility::dump( $node->getIdentifierNodeMap() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifierNodeMap() : array
    {
        return $this->node_map;
    }
    
/**
<documentation><description><p>For a node that can have items (like checkboxes, selectors, radio buttons, and dropdowns), the method returns all the items (possible values) concatenated as a string.</p></description>
<example>u\DebugUtility::dump( $node->getItems() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getItems()
    {
        return $this->items;
    }
    
/**
<documentation><description><p>Returns the id of a <code>a\Linkable</code> node (a <code>a\Linkable</code> node is a chooser allowing users to choose either a page, a file, or a symlink; therefore, the id can be the <code>fileId</code>, <code>pageId</code>, or <code>symlinkId</code> of the node).</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getLinkableId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLinkableId()
    {
        if( isset( $this->file_id ) )
            return $this->file_id;
        else if( isset( $this->page_id ) )
            return $this->page_id;
        else // NULL or not 
            return $this->symlink_id;
    }
    
/**
<documentation><description><p>Returns the path of a <code>a\Linkable</code> node (a <code>a\Linkable</code> node is a chooser allowing users to choose either a page, a file, or a symlink; therefore, the path can be the <code>filePath</code>, <code>pagePath</code>, or <code>symlinkPath</code> of the node).</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getLinkablePath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLinkablePath()
    {
        if( isset( $this->file_path ) )
            return $this->file_path;
        else if( isset( $this->page_path ) )
            return $this->page_path;
        else // NULL or not
            return $this->symlink_path;
    }
/*/
	public function getNodeValue()
	{
		if( $this->isAsset() )
		{
			if( $this->isBlockChooser() )
			{
				return $this->getBlock();
			}
			elseif( $this->isFileChooser() )
		}
		
	}
/*/
/**
<documentation><description><p>Returns <code>pageId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getPageId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPageId()
    {
        return $this->page_id;
    }
    
/**
<documentation><description><p>Returns <code>pagePath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getPagePath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPagePath()
    {
        return $this->page_path;
    }
    
/**
<documentation><description><p>Returns the fully qualified identifier of the parent node, or the empty string if the node does not have a parent.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getParentId() ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getParentId() : string
    {
        return trim( $this->parent_id, self::DELIMITER );
    }
    
/**
<documentation><description><p>Returns an array of strings or NULL. Note that this method is meaningful only for text nodes of type radio buttons, dropdown, multiselect, and checkboxes.</p></description>
<example>u\DebugUtility::dump( $node->getPossibleValues() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPossibleValues()
    {
        if( isset( $this->items ) && strlen( $this->items ) > 0 )
            return explode( self::DELIMITER, $this->items );
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>recycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $node->getRecycled() );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getRecycled() : bool
    {
        return $this->recycled;
    }
    
/**
<documentation><description><p>Returns <code>structuredDataNodes</code>, which could be NULL or an array of <code>StructuredDataNode</code> objects.</p></description>
<example>u\DebugUtility::dump( $node->getStructuredDataNodes() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredDataNodes()
    {
        return $this->structured_data_nodes;
    }
    
/**
<documentation><description><p>Returns <code>symlinkId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getSymlinkId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkId()
    {
        return $this->symlink_id;
    }
    
/**
<documentation><description><p>Returns <code>symlinkPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getSymlinkPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkPath()
    {
        return $this->symlink_path;
    }
    
/**
<documentation><description><p>Returns <code>text</code>.</p></description>
<example>echo $node->getText(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getText()
    {
        return $this->text;
    }
    
/**
<documentation><description><p>Returns the type string of the current node if it is a text node (an text node is an instance of a normal text field (including multi-line and WYSIWYG), or a text field of type <code>datetime</code>, <code>calendar</code>, <code>multi-selector</code>, <code>dropdown</code>, or <code>checkbox</code>), or <code>NULL</code> if it is not. This method should be used together with <code>isTextNode()</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getTextNodeType() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTextNodeType()
    {
        return $this->text_type;
    }
    
/**
<documentation><description><p>An alias of <code>getTextNodeType()</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTextType()
    {
        return $this->getTextNodeType();
    }
    
/**
<documentation><description><p>Returns <code>type</code>. The returned value can be <code>text</code>, <code>asset</code>, or <code>group</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $node->getType() ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getType() : string
    {
        return $this->type;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the item exists in the <code>items</code> string.</p></description>
<example>echo u\StringUtility::boolToString( $node->hasItem( "Maybe" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasItem( string $item ) : bool
    {
        if( $this->items == '' )
            return false;
            
        $items = explode( self::DELIMITER, $this->items );
        return in_array( $item, $items );
    }
    
/**
<documentation><description><p>An alias of <code>isAssetNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isAsset() : bool
    {
        return $this->type == c\T::ASSET;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is an asset node, allowing users to choose an asset.</p></description>
<example>echo u\StringUtility::boolToString( $node->IsAsset() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isAssetNode() : bool
    {
        return $this->type == c\T::ASSET;
    }
    
/**
<documentation><description><p>An alias of <code>isBlockChooserNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isBlockChooser() : bool
    {
        return $this->asset_type == c\T::BLOCK;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the current node is a block chooser node, allowing users to choose a block.</p></description>
<example>echo u\StringUtility::boolToString( $node->isBlockChooser() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isBlockChooserNode() : bool
    {
        return $this->asset_type == c\T::BLOCK;
    }

/**
<documentation><description><p>An alias of <code>isCalendarNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isCalendar() : bool
    {
        return $this->text_type == c\T::CALENDAR;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is a calendar node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isCalendar() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isCalendarNode() : bool
    {
        return $this->text_type == c\T::CALENDAR;
    }
    
/**
<documentation><description><p>An alias of <code>isCheckboxNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isCheckbox() : bool
    {
        return $this->text_type == c\T::CHECKBOX;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is a checkbox node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isCheckbox() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isCheckboxNode() : bool
    {
        return $this->text_type == c\T::CHECKBOX;
    }
    
/**
<documentation><description><p>An alias of <code>isDatetimeNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isDatetime() : bool
    {
        return $this->text_type == c\T::DATETIME;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is a datatime node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isDatetime() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isDatetimeNode() : bool
    {
        return $this->text_type == c\T::DATETIME;
    }
    
/**
<documentation><description><p>An alias of <code>isDropdownNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isDropdown() : bool
    {
        return $this->text_type == c\T::DROPDOWN;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is a dropdown node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isDropdown() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isDropdownNode() : bool
    {
        return $this->text_type == c\T::DROPDOWN;
    }
    
/**
<documentation><description><p>An alias of <code>isFileChooserNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isFileChooser() : bool
    {
        return $this->asset_type == c\T::FILE;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the current node is a file chooser node, allowing users to choose a file.</p></description>
<example>echo u\StringUtility::boolToString( $node->isFileChooser() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isFileChooserNode() : bool
    {
        return $this->asset_type == c\T::FILE;
    }

/**
<documentation><description><p>An alias of <code>isGroupNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isGroup() : bool
    {
        return $this->type == c\T::GROUP;
    }
    
/**
<documentation><description><p>Returns returns a bool, indicating whether the current node is a group node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isGroup() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isGroupNode() : bool
    {
        return $this->type == c\T::GROUP;
    }
    
/**
<documentation><description><p>An alias of <code>isLinkableChooserNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isLinkableChooser() : bool
    {
        return $this->asset_type == c\T::PFS;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the current node is a linkable chooser node, allowing users to choose a file, a page, or a symlink.</p></description>
<example>echo u\StringUtility::boolToString( $node->isLinkableChooser() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isLinkableChooserNode() : bool
    {
        return $this->asset_type == c\T::PFS;
    }

/**
<documentation><description><p>An alias of <code>isMultiLineNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isMultiLine() : bool
    {
        return $this->multi_line;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is a multi-line node (i.e., textarea).</p></description>
<example>echo u\StringUtility::boolToString( $node->isMultiLine() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isMultiLineNode() : bool
    {
        return $this->multi_line;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is an instance of a multiple field.</p></description>
<example>echo u\StringUtility::boolToString( $node->isMultiple() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isMultiple() : bool
    {
        return isset( $this->multiple ) && $this->multiple ;
    }
    
/**
<documentation><description><p>An alias of <code>isMultiSelectorNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isMultiSelector() : bool
    {
        return $this->text_type == c\T::MULTISELECTOR;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the current node is a multi-selector node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isMultiSelector() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isMultiSelectorNode() : bool
    {
        return $this->text_type == c\T::MULTISELECTOR;
    }

/**
<documentation><description><p>An alias of <code>isPageChooserNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isPageChooser() : bool
    {
        return $this->asset_type == c\T::PAGE;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the current node is a page chooser node, allowing users to choose a page.</p></description>
<example>echo u\StringUtility::boolToString( $node->isPageChooser() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isPageChooserNode() : bool
    {
        return $this->asset_type == c\T::PAGE;
    }

/**
<documentation><description><p>An alias of <code>isRadioNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isRadio() : bool
    {
        return $this->radio;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is a radio node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isRadio() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isRadioNode() : bool
    {
        return $this->radio;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field of the data definition corresponding to the current node requires a value.</p></description>
<example>echo u\StringUtility::boolToString( $node->isRequired() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isRequired() : bool
    {
        return $this->required;
    }

/**
<documentation><description><p>An alias of <code>isSymlinkChooserNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isSymlinkChooser() : bool
    {
        return $this->asset_type == c\T::SYMLINK;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the current node is a symlink chooser node, allowing users to choose a symlink.</p></description>
<example>echo u\StringUtility::boolToString( $node->isSymlinkChooser() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isSymlinkChooserNode() : bool
    {
        return $this->asset_type == c\T::SYMLINK;
    }

/**
<documentation><description><p>An alias of <code>isTextNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isText() : bool
    {
        return $this->type == c\T::TEXT;
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isTextarea() : bool
    {
        return $this->multi_line;
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isTextareaNode() : bool
    {
        return $this->multi_line;
    }
    
/**
<documentation><description><p>An alias of <code>isTextBoxNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isTextBox() : bool
    {
        if( !$this->isTextNode() || $this->multi_line || $this->wysiwyg ||
            $this->text_type == c\T::DATETIME || $this->text_type == c\T::CALENDAR || 
            $this->text_type == c\T::MULTISELECTOR || $this->text_type == c\T::DROPDOWN ||
            $this->text_type == c\T::CHECKBOX || $this->radio
        )
            return false;
        return true;
    }
    
/**
<documentation><description><p>Returns returns a bool, indicating whether the current node is a simple text box node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isTextBox() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isTextBoxNode() : bool
    {
        return $this->isTextBox();
    }
    
/**
<documentation><description><p>Returns returns a bool, indicating whether the current node is a text node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isText() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isTextNode() : bool
    {
        return $this->type == c\T::TEXT;
    }
    
/**
<documentation><description><p>An alias of <code>isWYSIWYGNode()</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isWYSIWYG() : bool
    {
        return $this->wysiwyg;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the current node is a WYSIWYG node.</p></description>
<example>echo u\StringUtility::boolToString( $node->isWYSIWYG() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isWYSIWYGNode() : bool
    {
        return $this->wysiwyg;
    }
    
/**
<documentation><description><p>Removes the last instance of a multiple field bearing the identifier and returns the calling object. Note that the identifier must be a fully qualified identifier in the data definition (without any <code>;digit</code> in it). This means that the field cannot have any ancestors of multiple type. This methods is used by the <code>StructuredData</code> class.</p></description>
<example>$node->removeLastChildNode( "group;group-multiple-second" );</example>
<return-type>Property</return-type>
<exception>NodeException</exception>
</documentation>
*/
    public function removeLastChildNode( string $node_id ) : Property
    {
        if( $this->structured_data_nodes == NULL )
        {
            throw new e\NodeException( 
                S_SPAN . "Cannot remove a node from a node that has no children." . 
                E_SPAN );
        }
        
        // remove digits and semi-colons
        $field_id = self::getFieldIdentifier( $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Field ID: " . $field_id ); }
        if( !$this->data_definition->isMultiple( $field_id ) )
            throw new e\NodeException( 
                S_SPAN . "Cannot remove a node from a non-multiple field" . E_SPAN );

        $last_pos     = self::getPositionOfLastNode( 
            $this->structured_data_nodes, $node_id );
        $first_pos    = self::getPositionOfFirstNode( 
            $this->structured_data_nodes, $node_id );
            
        if( self::DEBUG ) { u\DebugUtility::out( 
            "First position: " . $first_pos . BR . "Last position: " . $last_pos ); }
            
        $last_node_id = $this->structured_data_nodes[ $last_pos ]->getIdentifier();
        
        if( self::DEBUG ) { u\DebugUtility::out( "Last node ID: " . $last_node_id ); }
            
        if( $first_pos == $last_pos ) // the only node
            throw new e\NodeException(
                S_SPAN . "Cannot remove the only node in the field." . E_SPAN );
        
        $child_count = count( $this->structured_data_nodes ); // total children
    
        // node to be deleted in the middle
        if( $child_count > $last_pos + 1 )
        {
            $before = array_slice( $this->structured_data_nodes, 0, $last_pos );
            $after = array_slice( $this->structured_data_nodes, $last_pos + 1 );
            $this->structured_data_nodes = array_merge( $before, $after );
        }
        else // at the end
        {
            array_pop( $this->structured_data_nodes );
        }
            
        unset( $this->node_map, $last_node_id );
        
        return $this;
    }
    
    public function removePhantomValues() : Property
    {
        // only relevant to four types of text nodes
        if( !$this->isTextNode() ||
            ( $this->text_type != self::TEXT_TYPE_CHECKBOX &&
              $this->text_type != self::TEXT_TYPE_DROPDOWN &&
              $this->text_type != self::TEXT_TYPE_RADIO &&
              $this->text_type != self::TEXT_TYPE_SELECTOR ) )
        {
            return $this;
        }
        else
        {
                $actual_values   =
                	u\StringUtility::getExplodedStringArray(
                		a\DataDefinition::DELIMITER,
                        str_replace(
                            StructuredDataNode::SELECTOR_PREFIX,
                            "",
                            str_replace(
                                structuredDataNode::CHECKBOX_PREFIX,
                                "",
                                $this->getText()
                            )
                        )
                    );

            $dd_id = u\StringUtility::getFullyQualifiedIdentifierWithoutPositions(
                $this->identifier );
        
            $possible_values = $this->data_definition->getPossibleValues( $dd_id );
            $valid_values    = array();
        
            foreach( $actual_values as $actual_value )
            {
                if( in_array( $actual_value, $possible_values ) )
                {
                    $valid_values[] = $actual_value;
                }
            }
        
            if( $this->data_definition->getRequired( $this->identifier ) &&
                count( $valid_values ) == 0 )
            {
                $valid_values[] =
                    $this->data_definition->getField( $this->identifier )[ "default" ];
            }
        
            $input_string = implode( a\DataDefinition::DELIMITER, $valid_values );
            $this->setText( $input_string );
        }
        
        return $this;
    }

/**
<documentation><description><p>Sets <code>blockId</code> and <code>blockPath</code> and returns the calling object.</p></description>
<example>$node->setBlock( 
    $cascade->getAsset( a\DataBlock::TYPE, "1f21ae208b7ffe834c5fe91e80fa13e6" ) );</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setBlock( a\Block $block=NULL ) : Property
    {
        if( self::DEBUG ) { u\DebugUtility::out( "setBlock called: " . $block->getId() ); }

        // required
        if( $this->required && $block == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_BLOCK . E_SPAN );
        }
        
        if( $this->asset_type != c\T::BLOCK )
        {
            throw new e\NodeException(
                S_SPAN . "The asset does not accept a block." . E_SPAN );
        }
        
        if( isset( $block ) )
        {
            $this->block_id   = $block->getId();
            $this->block_path = $block->getPath();
        }
        else
        {
            $this->block_id   = NULL;
            $this->block_path = NULL;
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>fileId</code> and <code>filePath</code> and returns the calling object.</p></description>
<example>$node->setFile();</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setFile( a\File $file=NULL ) : Property
    {
        // required
        if( $this->required && $file == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_FILE . E_SPAN );
        }
        
        if( $this->asset_type != c\T::FILE )
        {
            throw new e\NodeException(
                S_SPAN . "The asset does not accept a file." . E_SPAN );
        }
        
        if( isset( $file ) )
        {
            $this->file_id   = $file->getId();
            $this->file_path = $file->getPath();
        }
        else
        {
            $this->file_id   = NULL;
            $this->file_path = NULL;
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Sets either <code>fileId</code> and <code>filePath</code>, or <code>pageId</code> and <code>pagePath</code>, or <code>symlinkId</code> and <code>symlinkPath</code>, depending on the type of the linkable, and returns the calling object.</p></description>
<example>$node->setLinkable();</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setLinkable( a\Linkable $linkable=NULL ) : Property
    {
        // required
        if( $this->required && $linkable == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_LINKABLE . E_SPAN );
        }
        
        if( $this->asset_type != c\T::PFS )
        {
            throw new e\NodeException(
                S_SPAN . "The asset does not accept a linkable." . E_SPAN );
        }
        
        if( isset( $linkable ) )
        {
            $type = $linkable->getType();
            
            if( $type == c\T::FILE )
            {
                $this->file_id      = $linkable->getId();
                $this->file_path    = $linkable->getPath();
                $this->page_id      = NULL;
                $this->page_path    = NULL;
                $this->symlink_id   = NULL;
                $this->symlink_path = NULL;
            }
            else if( $type == c\T::PAGE )
            {
                $this->page_id      = $linkable->getId();
                $this->page_path    = $linkable->getPath();
                $this->file_id      = NULL;
                $this->file_path    = NULL;
                $this->symlink_id   = NULL;
                $this->symlink_path = NULL;
            }
            else if( $type == c\T::SYMLINK )
            {
                $this->symlink_id   = $linkable->getId();
                $this->symlink_path = $linkable->getPath();
                $this->file_id      = NULL;
                $this->file_path    = NULL;
                $this->page_id      = NULL;
                $this->page_path    = NULL;
            }
        }
        else
        {
            $this->file_id      = NULL;
            $this->file_path    = NULL;
            $this->page_id      = NULL;
            $this->page_path    = NULL;
            $this->symlink_id   = NULL;
            $this->symlink_path = NULL;
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>pageId</code> and <code>pagePath</code> and returns the calling object.</p></description>
<example>$node->setPage();</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setPage( a\Page $page=NULL ) : Property
    {
        // required
        if( $this->required && $page == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_PAGE . E_SPAN );
        }
        
        if( $this->asset_type != c\T::PAGE )
        {
            throw new e\NodeException(
                S_SPAN . "The asset does not accept a page." . E_SPAN );
        }
        
        if( isset( $page ) )
        {
            $this->page_id   = $page->getId();
            $this->page_path = $page->getPath();
        }
        else
        {
            $this->page_id   = NULL;
            $this->page_path = NULL;
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>symlinkId</code> and <code>symlinkPath</code> and returns the calling object.</p></description>
<example>$node->setSymlink();</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException</exception>
</documentation>
*/
    public function setSymlink( a\Symlink $symlink=NULL ) : Property
    {
        // required
        if( $this->required && $symlink == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_SYMLINK . E_SPAN );
        }
        
        if( $this->asset_type != c\T::SYMLINK )
        {
            throw new e\NodeException(
                S_SPAN . "The asset does not accept a symlink." . E_SPAN );
        }
        
        if( isset( $symlink ) )
        {
            $this->symlink_id   = $symlink->getId();
            $this->symlink_path = $symlink->getPath();
        }
        else
        {
            $this->symlink_id   = NULL;
            $this->symlink_path = NULL;
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>text</code> and returns the calling object.</p></description>
<example>$node->setText( "10-17-2016" );</example>
<return-type>Property</return-type>
<exception>NodeException, EmptyValueException, NoSuchValueException, UnacceptableValueException</exception>
</documentation>
*/
    public function setText( string $text=NULL ) : Property
    {
        $text = trim( $text );
        
        // required
        if( $this->required && $text == '' )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_TEXT . E_SPAN );
        }
        
        // no text to group
        if( $this->type == c\T::GROUP )
        {
            throw new e\NodeException(
                S_SPAN . "Group cannot have text." . E_SPAN );
        }
        else if( $this->items == '' ) // normal text, datetime, calendar
        {
            if( $this->text_type == self::TEXT_TYPE_DATETIME )
            {
                if( !is_null( $text ) && $text != "" && !is_numeric( $text ) )
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "$text is not an acceptable datetime value." . E_SPAN );
                    
                $this->text = $text;
            }
            else if( $this->text_type == self::TEXT_TYPE_CALENDAR ) // month-day-year
            {
                if( !is_null( $text ) && $text != "" )
                {
                    $date_array = explode( '-', $text );
                
                    // must have three parts
                    if( count( $date_array ) != 3 )
                    {
                        throw new e\UnacceptableValueException( 
                            S_SPAN . "$text is not an acceptable date value." . E_SPAN );
                    }
                
                    list( $month, $day, $year ) = $date_array;
                
                    // convert strings to integers
                    $month = intval( $month );
                    $day   = intval( $day );
                    $year  = intval( $year );
                
                    // check the date
                    if( !checkdate( $month, $day, $year ) )
                    {
                        throw new e\UnacceptableValueException( 
                            S_SPAN . "$text is not an acceptable date value." . E_SPAN );
                    }
                
                    // compare years, Cascade only has a range of 20 years
                    $today     = getdate();
                    $this_year = $today[ 'year' ];
                
                    if( abs( $this_year - $year ) > 10 )
                    {
                        throw new e\UnacceptableValueException( 
                            S_SPAN . "$text is not an acceptable date value." . E_SPAN );
                    }
                
                    // convert integers back to strings
                    if( $month < 10 )
                    {
                        $month_string = '0' . $month;
                    }
                    else
                    {
                        $month_string = $month;
                    }
                
                    if( $day < 10 )
                    {
                        $day_string = '0' . $day;
                    }
                    else
                    {
                        $day_string = $day;
                    }
                
                    $this->text = $month_string . '-' . $day_string . '-' . $year;
                }
                else
                    $this->text = $text;
            }
            else
            {
                $this->text = $text;
            }
            
            return $this;
        }
        else // checkbox, radio, select, dropdown
        {
            $item_array = explode( self::DELIMITER, $this->items ); // could be NULL
            
            if( strpos( $text, self::CHECKBOX_PREFIX ) !== false ) // no semi-colon
            {
                $input_array = explode( self::CHECKBOX_PREFIX, $text );
            }
            else if( strpos( $text, self::SELECTOR_PREFIX ) !== false ) // no semi-colon
            {
                $input_array = explode( self::SELECTOR_PREFIX, $text );
            }
            else // with semi-colon
            {
                $input_array = explode( self::DELIMITER, $text );
            }
            
            if( count( $item_array ) == 1 )  // single item checkbox or dropdown
            {
                if( $this->text_type == self::TEXT_TYPE_CHECKBOX )
                {
                    $text = str_replace( self::CHECKBOX_PREFIX, "", $text );
                    
                    // unacceptable input
                    if( $text != $this->items && $text != '' && 
                        $text != self::CHECKBOX_PREFIX )
                    {
                        throw new e\NoSuchValueException(
                            S_SPAN . "The value $text does not exist." . E_SPAN );
                    }
                    else if( $text == '' || $text == self::CHECKBOX_PREFIX )
                    {
                        $this->text = self::CHECKBOX_PREFIX;
                    }
                    else
                    {
                        $this->text = $text;
                    }
                }
                else if( $this->text_type == self::TEXT_TYPE_DROPDOWN )
                {
                    if( $text != "" && !in_array( $text, $item_array ) )
                    {
                        throw new e\NoSuchValueException(
                            S_SPAN . "The value $text does not exist." . E_SPAN );
                    }
                    $this->text = $text;
                }
                
                return $this;
            }
            else // multiple items
            {
                if( $this->text_type == self::TEXT_TYPE_CHECKBOX )
                {
                    if( $text == '' || $text == self::CHECKBOX_PREFIX )
                    {
                        $this->text = self::CHECKBOX_PREFIX;
                    }
                    else
                    {
                        $temp = '';
                        
                        foreach( $input_array as $input )
                        {
                            if( $input == '' )
                            {
                                continue;
                            }
                            else if( !in_array( $input, $item_array ) )
                            {
                                throw new e\NoSuchValueException( 
                                    S_SPAN . "The value $input does not exist." . 
                                    E_SPAN );
                            }
                            else
                            {
                                $temp .= self::CHECKBOX_PREFIX . $input;
                            }
                        }
                    
                        $this->text = $temp;
                    }
                    return $this;
                }
                else if( $this->text_type == self::TEXT_TYPE_RADIO )
                {
                    if( count( $input_array ) > 1 )
                    {
                        throw new e\UnacceptableValueException( 
                            S_SPAN . "Radio button does not allow more than one value." .
                            E_SPAN );
                    }
                
                    if( $text != "" && !in_array( $text, $item_array ) )
                    {
                        throw new e\NoSuchValueException( 
                            S_SPAN . "The value $text does not exist" . E_SPAN );
                    }
                    
                    if( $this->required && $text == "" )
                    {
                        throw new e\EmptyValueException( 
                            S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
                    }
                    
                    $this->text = $text;
                    
                    return $this;
                }
                else if( $this->text_type == self::TEXT_TYPE_SELECTOR )
                {
                    if( $text == '' || $text == self::SELECTOR_PREFIX )
                    {
                        $this->text = self::SELECTOR_PREFIX;
                    }
                    else
                    {
                        $temp = '';
                        
                        foreach( $input_array as $input )
                        {
                            // skip empty string
                            if( $input == '' )
                            {
                                continue;
                            }
                            else if( !in_array( $input, $item_array ) )
                            {
                                throw new e\UnacceptableValueException( 
                                    S_SPAN . "The value $input does not exist." . E_SPAN
                                );
                            }
                            else
                            {
                                $temp .= self::SELECTOR_PREFIX . $input;
                            }
                        }
                        
                        $this->text = $temp;
                    }
                    return $this;
                }
                else if( $this->text_type == self::TEXT_TYPE_DROPDOWN )
                {
                    $this->text = $text;
                }
            }
        }
        
        return $this;
    }

/**
<documentation><description><p>Swaps the two children, and returns the calling object. This method is used by <code>StructuredData</code>. <code>$node1</code> and <code>$node2</code> are actually new nodes created by the constructor of <code>structureDataNode</code>. There is not really any swapping. The two positions are in fact assigned two new nodes.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function swapChildren(
        int $pos1, StructuredDataNode $node1,
        int $pos2, StructuredDataNode$node2 ) : Property
    {
        $this->structured_data_nodes[ $pos1 ] = $node1;
        $this->structured_data_nodes[ $pos2 ] = $node2;
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $node->toStdClass() );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj       = new \stdClass();
        $obj->type = $this->type;
        $id_array  = explode( self::DELIMITER, $this->identifier );
        $id_count  = count( $id_array );
        
        // work out the identifier of the node
        $id = $id_array[ $id_count - 1 ];
        
        if( is_numeric( $id ) )
        {
            $obj->identifier = $id_array[ $id_count - 2 ];
        }
        else
        {
            $obj->identifier = $id_array[ $id_count - 1 ];
        }
    
        if( $this->type == c\T::GROUP )
        {
            $node_count = count( $this->structured_data_nodes );
        
            if( $node_count == 1 )
            {
                $obj->structuredDataNodes = new \stdClass();
                
                if( $this->service->isSoap() )
                    $obj->structuredDataNodes->structuredDataNode =
                        $this->structured_data_nodes[0]->toStdClass();
                elseif( $this->service->isRest() )
                    $obj->structuredDataNodes =
                        array( $this->structured_data_nodes[0]->toStdClass() );
            }
            else
            {
                $obj->structuredDataNodes = new \stdClass();
                
                if( $this->service->isSoap() )
                    $obj->structuredDataNodes->structuredDataNode = array();
                elseif( $this->service->isRest() )
                    $obj->structuredDataNodes = array();
        
                for( $i = 0; $i < $node_count; $i++ )
                {
                    if( $this->service->isSoap() )
                        $obj->structuredDataNodes->structuredDataNode[] = 
                            $this->structured_data_nodes[$i]->toStdClass();
                    elseif( $this->service->isRest() )
                        $obj->structuredDataNodes[] = 
                            $this->structured_data_nodes[$i]->toStdClass();
                }
            }
        }
        else
        {
            //$obj->structuredDataNodes = NULL;
            unset( $obj->structuredDataNodes );
        }
    
        if( isset( $this->text ) )
            $obj->text        = $this->text;
        else
            unset( $obj->text );
            
        if( isset( $this->asset_type ) )
            $obj->assetType   = $this->asset_type;
        else
            unset( $obj->assetType );
            
        if( isset( $this->block_id ) )
            $obj->blockId   = $this->block_id;
        else
            unset( $obj->blockId );
            
        if( isset( $this->block_path ) )
            $obj->blockPath   = $this->block_path;
        else
            unset( $obj->blockPath );
            
        if( isset( $this->file_id ) )
            $obj->fileId   = $this->file_id;
        else
            unset( $obj->fileId );
            
        if( isset( $this->file_path ) )
            $obj->filePath   = $this->file_path;
        else
            unset( $obj->filePath );
            
        if( isset( $this->page_id ) )
            $obj->pageId   = $this->page_id;
        else
            unset( $obj->pageId );
            
        if( isset( $this->page_path ) )
            $obj->pagePath   = $this->page_path;
        else
            unset( $obj->pagePath );
            
        if( isset( $this->symlink_id ) )
            $obj->symlinkId   = $this->symlink_id;
        else
            unset( $obj->symlinkId );
            
        if( isset( $this->symlink_path ) )
            $obj->symlinkPath   = $this->symlink_path;
        else
            unset( $obj->symlinkPath );
            
        $obj->recycled    = $this->recycled;
        
        return $obj;
    }
    
/**
<documentation><description><p>Returns the fully qualified identifier of corresponding field.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public static function getFieldIdentifier( string $node_id ) : string
    {
        /* this code looks unnecessarily long; just to make sure 
           only digits surrounded by ; are removed
           see StringUtility::getFullyQualifiedIdentifierWithoutPositions
        */
        // remove digit;
        $field_id = preg_replace( '/;(\d)+/', ';', $node_id );
        // remove any doubled-semi-colons
        $field_id = str_replace( ';;', ';', $field_id );
        // trim last semi-colon
        $field_id = trim( $field_id, ';' );
        return $field_id;
    }

/**
<documentation><description><p>Returns the last index.</p></description>
<example></example>
<return-type>int</return-type>
</documentation>
*/
    public static function getLastIndex( string $node_id ) : int
    {
        $matches = array();
        $result = preg_match( '/;(\d+)$/', $node_id, $matches );
        
        if( $result )
        {
            return intval( $matches[ 1 ] );
        }
        return -1;
    }
    
/**
<documentation><description><p>Returns the position of the first instance of a set of nodes.</p></description>
<example></example>
<return-type>int</return-type>
</documentation>
*/
    public static function getPositionOfFirstNode( array $array, string $field_id ) : int
    {
        $child_count = count( $array );
        
        for( $i = 0; $i < $child_count; $i++ )
        {
            if( strpos( $array[ $i ]->getIdentifier(), $field_id .
                a\DataDefinition::DELIMITER ) !== false )
                break;
        }
        return $i;
    }
    
/**
<documentation><description><p>Returns the position of the last instance of a set of nodes.</p></description>
<example></example>
<return-type>int</return-type>
</documentation>
*/
    public static function getPositionOfLastNode( array $array, string $node_id ) : int
    {
        $child_count = count( $array );
        if( self::DEBUG ) { u\DebugUtility::out( "Child count: " . $child_count ); }
        $shared_id   = self::removeLastIndex( $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Shared ID: " . $shared_id ); }
        
        for( $i = $child_count - 1; $i > 0; $i-- )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Child ID: " .
                $array[ $i ]->getIdentifier() ); }            
            if( strpos( $array[ $i ]->getIdentifier(), $shared_id ) !== false )
            {
                if( self::DEBUG ) { u\DebugUtility::out( "Found in $i" ); }  
                break;
            }
        }
        return $i;
    }
    
/**
<documentation><description><p>Processes the structured data nodes.</p></description>
<example></example>
<return-type>void</return-type>
</documentation>
*/
    public static function processStructuredDataNodes( 
        string $parent_id, 
        array &$node_array,
        $node_std, 
        a\DataDefinition $data_definition=NULL,
        aohs\AssetOperationHandlerService $service=NULL )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $parent_id ); }
        
        if( !is_array( $node_std ) )
        {
            $node_std = array( $node_std );
        }
        
        $node_count  = count( $node_std );
        
        if( self::DEBUG ) { u\DebugUtility::out( "Node count: " . $node_count ); }  
        
        // these are used to calculate the index
        $previous_identifier;
        $current_identifier;
        $cur_index = 0;
        $processed_mul_ids = array();
        
        // work out the id of the current node for the data definition
        // no digits in the fully qualified identifiers
        for( $i = 0; $i < $node_count; $i++ )
        {
            if( isset( $node_std[ $i ]->identifier ) )
            {
                $fq_identifier = $node_std[ $i ]->identifier;
            }
            
            if( $parent_id != '' )
            {
                $parent_id_array = explode( self::DELIMITER, $parent_id );
                $temp            = '';
                
                foreach( $parent_id_array as $part )
                {
                    if( !is_numeric( $part ) )
                    {
                        $temp .= $part . self::DELIMITER;
                    }
                }
                
                $temp          = trim( $temp, self::DELIMITER );
                $fq_identifier = 
                    $temp . self::DELIMITER . $node_std[$i]->identifier;
            }
        
            $is_multiple = $data_definition->isMultiple( $fq_identifier );
            
            if( $is_multiple )
            {
                if( !in_array( $fq_identifier, array_keys( $processed_mul_ids ) ) )
                {
                    $processed_mul_ids[ $fq_identifier ] = 0;
                }
                else
                {
                    $processed_mul_ids[ $fq_identifier ] += 1;
                }
            }
            
            // a multiple text or group, work out fully qualified identifier
            if( $is_multiple )
            {
                $cur_index = $processed_mul_ids[ $fq_identifier ];
            }
            else
            {
                $cur_index = 0;
            }
            
            if( $parent_id != '' )
            {
                $n = new StructuredDataNode(
                    $node_std[ $i ], $service, $data_definition, $cur_index, $parent_id );
            }
            else
            {
                $n = new StructuredDataNode(
                    $node_std[ $i ], $service, $data_definition, $cur_index );
            }
            
            $n->parent_id = $parent_id;
            $node_array[ $i ] = $n;
        }
    }
    
/**
<documentation><description><p>Returns the fully qualified identifier without the last index.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public static function removeLastIndex( string $node_id ) : string
    {
        return preg_replace( '/;(\d)+$/', '', $node_id );
    }

    private $type;                  // asset, group, text
    private $identifier;            // fully qualified identifier
    private $structured_data_nodes; // children of a group
    private $text;
    private $asset_type;
    private $block_id;
    private $block_path;
    private $file_id;
    private $file_path;
    private $multi_line;
    private $page_id;
    private $page_path;
    private $symlink_id;
    private $symlink_path;
    private $recycled;
    private $parent_id;
    private $multiple;  // whether this is a multiple node
    private $required;  // whether value is required
    private $text_type; // type of text, radiobutton, dropdown, and so on
    private $index;     // index of a multiple field
    private $items;     // items string of radio, checkbox, dropdown & selector
    private $radio;     // whether this is a radio
    private $wysiwyg;   // whether this is a wysiwyg
    private $data_definition;
    private $node_map;
    private $service;
}
?>