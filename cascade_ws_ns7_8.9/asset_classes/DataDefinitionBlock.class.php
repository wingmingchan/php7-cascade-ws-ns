<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/6/2018 Added removePhantomNodes (of type B).
  * 2/5/2018 Added removePhantomValues.
  * 1/24/2018 Updated documentation.
  * 8/1/2017 Added getNodeBlock.
  * 6/20/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/11/2017 Added JSON structure and JSON dump.
  * 11/2/2016 Added searchTextByPattern and searchWYSIWYGByPattern.
  * 10/25/2016 Added hasPossibleValues, isMultipleField and isMultipleNode.
  * 6/2/2016 Added aliases.
  * 6/1/2016 Added isBlockChooser, isCalendarNode, isCheckboxNode, isDatetimeNode, isDropdownNode,
  * isFileChooser, isLinkableChooser, isMultiLineNode, isMultiSelectorNode, isPageChooser,
  * isRadioNode, isSymlinkChooser, isTextBox, and isWYSIWYGNode.
  * 3/10/2016 Added hasPhantomNodes.
  * 3/9/2016 Added mapData.
  * 1/8/2016 Added code to deal with host asset.
  * 5/28/2015 Added namespaces.
  * 2/24/2015 Added getPossibleValues.
  * 9/29/2014 Fixed in bug in edit.
  * 9/25/2014 Added isMultiLineNode.
  * 8/14/2014 Added style to error messages.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>DataDefinitionBlock</code> object represents an asset of type <code>xhtmlDataDefinitionBlock</code>. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/block.php\"><code>Block</code></a>.</p>
<p>This class can be used to manipulate both data definition blocks and xhtml blocks. The only test available to tell them apart is the <code>DataDefinitionBlock::hasStructuredData</code> method. When it returns true, the block is a data definition block; else it is an xhtml block. We cannot consider the <code>xhtml</code> property because it can be <code>NULL</code> for both block sub-types.</p>
<p>I could have split this class into two: <code>DataDefinitionBlock</code> and <code>XhtmlBlock</code>. But this difference between with and without using a data definition also exists in a page, and I do not think I should split the <code>Page</code> class into two. Therefore, I use the same class to deal with these two types of <code>xhtmlDataDefinitionBlock</code>.</p>
<h2>Structure of <code>xhtmlDataDefinitionBlock</code></h2>
<pre>SOAP:
xhtmlDataDefinitionBlock
  id
  name
  parentFolderId
  parentFolderPath
  path
  lastModifiedDate
  lastModifiedBy
  createdDate
  createdBy
  siteId
  siteName
  metadata
    author
    displayName
    endDate
    keywords
    metaDescription
    reviewDate
    startDate
    summary
    teaser
    title
    dynamicFields (NULL or an stdClass)
      dynamicField (an stdClass or or array of stdClass)
        name
        fieldValues (NULL, stdClass or array of stdClass)
          fieldValue
            value
  metadataSetId
  metadataSetPath
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled (bool)
  structuredData
    definitionId
    definitionPath
    structuredDataNodes
      structuredDataNode (stdClass or array of stdClass)
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
  xhtml
  
REST:
xhtmlDataDefinitionBlock
  structuredData
  structuredData
    definitionId
    definitionPath
    structuredDataNodes (array)
      stdClass
        type
        identifier
        structuredDataNodes (array)
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled (bool)
  metadataSetId
  metadataSetPath
  metadata
    author
    displayName
    endDate
    keywords
    metaDescription
    reviewDate
    startDate
    summary
    teaser
    title
    dynamicFields (array)
      stdClass
        name
        fieldValues (array)
          stdClass
            value
  parentFolderId
  parentFolderPath
  lastModifiedDate
  lastModifiedBy
  createdDate
  createdBy
  path
  siteId
  siteName
  name
  id
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "xhtmlDataDefinitionBlock" ),
        array( "getComplexTypeXMLByName" => "structured-data" ),
        array( "getComplexTypeXMLByName" => "structured-data-nodes" ),
        array( "getComplexTypeXMLByName" => "structured-data-node" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-type" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-asset-type" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/data_block.php">data_block.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/dd_block_multiple_fields.php">dd_block_multiple_fields.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/dd_block_multiple_text.php">dd_block_multiple_text.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/xhtml_block.php">xhtml_block.php</a></li>
</ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/block_XHTML_DATADEFINITION/c12da9c78b7ffe83129ed6d8411290fe

{
  "asset":{
    "xhtmlDataDefinitionBlock":{
      "structuredData":{
        "definitionId":"618863658b7ffe8377b637e8ee4f3e42",
        "definitionPath":"_brisk:Wysiwyg",
        "structuredDataNodes":[ {
          "type":"text",
          "identifier":"display",
          "text":"yes",
          "recycled":false
        },
        {
          "type":"group",
          "identifier":"wysiwyg-group",
          "structuredDataNodes":[ {
            "type":"text",
            "identifier":"wysiwyg-content",
            "text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vitae arcu diam.",
            "recycled":false
          },
          {
            "type":"text",
            "identifier":"admin-options",
            "text":"::CONTENT-XML-CHECKBOX::",
            "recycled":false
          } ],
          "recycled":false
        } ]
      },
      "expirationFolderRecycled":false,
      "metadataSetId":"618861da8b7ffe8377b637e8ad3dd499",
      "metadataSetPath":"_brisk:Block",
      "metadata":{
        "dynamicFields":[ {
          "name":"macro",
          "fieldValues":[ {
            "value":"processWysiwygMacro"
          } ]
        } ]
      },
      "reviewOnSchedule":false,
      "reviewEvery":0,
      "parentFolderId":"c12dceb28b7ffe83129ed6d8535fb721",
      "parentFolderPath":"_cascade/blocks/data",
      "lastModifiedDate":"Jan 23, 2018 8:33:48 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Nov 15, 2017 2:35:16 PM",
      "createdBy":"wing",
      "path":"_cascade/blocks/data/latin-wysiwyg",
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "name":"latin-wysiwyg",
      "id":"c12da9c78b7ffe83129ed6d8411290fe"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }  
}

http://mydomain.edu:1234/api/v1/read/block_XHTML_DATADEFINITION/9d9336e18b7ffe8353cc17e99daf87e1

{
  "asset":{
    "xhtmlDataDefinitionBlock":
    {
      "xhtml":"\u003cdiv class\u003d\"text_red\"\u003eThis is meaningless!\u003c/div\u003e",
      "expirationFolderRecycled":false,
      "metadataSetId":"c12dd0738b7ffe83129ed6d86580d804",
      "metadataSetPath":"Default",
      "metadata":{},
      "reviewOnSchedule":false,
      "reviewEvery":0,
      "parentFolderId":"c12dceb28b7ffe83129ed6d8535fb721",
      "parentFolderPath":"_cascade/blocks/data",
      "lastModifiedDate":"Jan 23, 2018 9:11:29 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Dec 28, 2017 9:42:38 AM",
      "createdBy":"wing",
      "path":"_cascade/blocks/data/test-xhtml",
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "name":"test-xhtml",
      "id":"9d9336e18b7ffe8353cc17e99daf87e1"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre>
</postscript>
</documentation>
*/
class DataDefinitionBlock extends Block
{
    const DEBUG = false;
    const DUMP  = false;
    const TYPE  = c\T::DATABLOCK;

/**
<documentation><description>
<p>Since this class can handle both data definition blocks and xhtml blocks, the majority of the methods handle data definition blocks and only a few handle xhtml blocks. If a method like <code>DataDefinitionBlock::setText</code>, which is used to set the text of a node, is called upon a xhtml block, an exception will be thrown. On the other hand, a method like <code>DataDefinitionBlock::setXHTML</code> is meaningful only for xhtml blocks. When called upon a data definition block, this method throws an exception.</p>
<p>A note about cases in method names. Method names in PHP are not case-sensitive. Therefore, <code>getXHTML</code> is the same as <code>getXhtml</code>, and there is no way (and no need) to define aliases just by differing cases.</p>
<p>The constructor, overriding the parent method to process <code>structuredData</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        if( isset( $this->getProperty()->structuredData ) )
        {
            $this->processStructuredData();
        }
        else
        {
            $this->xhtml = $this->getProperty()->xhtml;
        }
    }

/**
<documentation><description><p>Appends a node to a multiple field, calls
<code>edit</code>, and returns the calling object. The identifier supplied must the fully
qualified identifier of the first node in the set. The identifier of the first node is
used because a multiple field, when in use, always has a first node. Note that the new
node is in fact an exact copy of the last node (which can be the first node). Therefore,
it contains all the data contained in the node copied.</p></description>
<example>$block->appendSibling( "multiple-first;0" );</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function appendSibling( string $identifier ) : Asset
    {
        $this->checkStructuredData();
        
        if( self::DEBUG ) { u\DebugUtility::out( "Calling appendSibling" ); }
        $this->structured_data->appendSibling( $identifier );
        $this->edit();
        return $this;
    }
    
/**
<documentation><description><p>Copies the data from the calling object to
<code>$block</code>. Note that this method only works if the calling object is a block
associated with a data definition, and that it ignores the data definition associated with
<code>$block</code>. If <code>$block</code> is associated with a data definition different
than the one associated with the calling object, then the one associated with the calling
object will still be associated with <code>$block</code>, overwriting the old information.
Also note that if <code>$block</code> is an xhtml block (one that is not associated with a
data definition), it will be turned into a data definition block.</p></description>
<example>$block->copyDataTo( $block2 );</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function copyDataTo( DataDefinitionBlock $block ) : Asset
    {
        $this->checkStructuredData();
        $block->setStructuredData( $this->getStructuredData() );
        return $this;
    }
    
/**
<documentation><description><p>Creates exactly <code>$number</code> instances for the
multiple field whose first node is <code>$identifier</code>, and returns the calling object. This method ensures that a multiple field will have exactly N instances. If the
block has moreor has less instances than <code>$number</code>, then instances are either
removed from or added to the field.</p></description>
<example>$block->createNInstancesForMultipleField( 4, "multiple-first;0" );</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException, UnacceptableValueException, NodeException</exception>
</documentation>
*/
    public function createNInstancesForMultipleField(
        int $number, string $identifier ) : Asset
    {
        $this->checkStructuredData();
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
        $this->reloadProperty();
        $this->processStructuredData();
        return $this;
    }
    
/**
<documentation><description><p>Displays <code>xml</code> of the data definition, and
returns the calling object.</p></description>
<example>$block->displayDataDefinition();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function displayDataDefinition() : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->getDataDefinition()->displayXML();
        return $this;
    }
    
/**
<documentation><description><p>Displays <code>xhtml</code> of the block, and returns the
calling object. Note that the <code>xhtml</code> property is defined even in a block
associated with a data definition. When this method is called through a data definition block, the method does not do anything.</p></description>
<example>$xhtml->displayXhtml();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayXhtml() : Asset
    {
        if( !$this->hasStructuredData() )
        {
            $xhtml_string = u\XMLUtility::replaceBrackets( $this->xhtml );
            echo S_H2 . 'XHTML' . E_H2;
            echo $xhtml_string . HR;
        }
        return $this;
    }
    
/**
<documentation><description><p>Edits and returns the calling object, overriding the parent
method to edit the <code>structuredData</code> property.</p></description>
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
        // edit the asset
        $asset = new \stdClass();
        $block = $this->getProperty();
        
        // patch for 8.9
        if( isset( $block->reviewEvery ) )
        {
            $review_every = (int)$block->reviewEvery;
        
            if( $review_every != 0 && $review_every != 30 && $review_every != 90 && 
                $review_every != 180 && $review_every != 365 )
            {
                $block->reviewEvery = 0;
            }
        }
        
        $block->metadata = $this->getMetadata()->toStdClass();
        
        if( isset( $this->structured_data ) )
        {
            $block->structuredData = $this->structured_data->toStdClass();
            $block->xhtml          = NULL;
        }
        else
        {
            $block->structuredData = NULL;
            $block->xhtml          = $this->xhtml;
        }

        $asset->{ $p = $this->getPropertyName() } = $block;
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $asset ); }
            throw new e\EditingFailureException(
                S_SPAN . "Block: " . $this->getPath() . E_SPAN . BR .
                c\M::EDIT_ASSET_FAILURE . $service->getMessage() );
        }
        $this->reloadProperty();
        
        if( isset( $this->getProperty()->structuredData ) )
            $this->processStructuredData();
        return $this;
    }
    
/**
<documentation><description><p>Returns the type string of an asset node (an asset node is
an instance of an asset field of type <code>page</code>, <code>file</code>,
<code>block</code>, <code>symlink</code>, or <code>page,file,symlink</code>).</p></description>
<example>echo $block->getAssetNodeType( $id ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getAssetNodeType( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getAssetNodeType( $identifier );
    }

/**
<documentation><description><p>Returns block attached to the node or <code>null</code>.
Since there is a <code>static</code> method <code>getBlock</code> defined in
<code>Block</code>, this method must have a different name.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getNodeBlock( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlock( $identifier );
    }

/**
<documentation><description><p>Returns <code>blockId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getBlockId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getBlockId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockId( $identifier );
    }
   
/**
<documentation><description><p>Returns <code>blockPath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getBlockPath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getBlockPath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockPath( $identifier );
    }
    
/**
<documentation><description><p>Returns the associated <code>DataDefinition</code> object.</p></description>
<example>$block->getDataDefinition()->dump();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getDataDefinition() : Asset
    {
        $this->checkStructuredData();
        return $this->structured_data->getDataDefinition();
    }
    
/**
<documentation><description><p>Returns <code>fileId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getFileId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getFileId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFileId( $identifier );
    }
    
/**
<documentation><description><p>Returns <code>filePath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getFilePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getFilePath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFilePath( $identifier );
    }
    
/**
<documentation><description><p>Returns an array of fully qualified identifiers of all nodes.</p></description>
<example>u\DebugUtility::dump( $block->getIdentifiers() );</example>
<return-type>array</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getIdentifiers() : array
    {
        $this->checkStructuredData();
        return $this->structured_data->getIdentifiers();
    }
    
/**
<documentation><description><p>Returns the id of a <code>Linkable</code> node (a
<code>Linkable</code> node is a chooser allowing users to choose either a page, a file, or
a symlink; therefore, the id can be the <code>fileId</code>, <code>pageId</code>, or
<code>symlinkId</code> of the node).</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getLinkableId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getLinkableId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkableId( $identifier );
    }
    
/**
<documentation><description><p>Returns the path of a <code>Linkable</code> node (a
<code>Linkable</code> node is a chooser allowing users to choose either a page, a file, or
a symlink; therefore, the path can be the <code>filePath</code>, <code>pagePath</code>, or
<code>symlinkPath</code> of the node).</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getLinkablePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getLinkablePath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkablePath( $identifier );
    }
    
/**
<documentation><description><p>Returns the type string of a node. The returned value is
one of the following: <code>group</code>, <code>asset</code>, and <code>text</code>.</p></description>
<example>echo $block->getNodeType( $id ), BR;</example>
<return-type>string</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getNodeType( string $identifier ) : string
    {
        $this->checkStructuredData();
        return $this->structured_data->getNodeType( $identifier );
    }
    
/**
<documentation><description><p>Returns of the number of instances (including the first
node) of a multiple field. The <code>$identifier</code> must be the fully qualified identifier of the first node.</p></description>
<example>echo $block->getNumberOfSiblings( "multiple-first;0" ), BR;</example>
<return-type>int</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getNumberOfSiblings( string $identifier ) : int
    {
        $this->checkStructuredData();
        
        if( trim( $identifier ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_IDENTIFIER . E_SPAN );
        }
        
        if( !$this->hasIdentifier( $identifier ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist." . E_SPAN );
        }
        return $this->structured_data->getNumberOfSiblings( $identifier );
    }

/**
<documentation><description><p>Returns <code>pageId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getPageId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getPageId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPageId( $identifier );
    }
    
/**
<documentation><description><p>Returns <code>pagePath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getPagePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getPagePath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPagePath( $identifier );
    }
    
/**
<documentation><description><p>Returns an array of strings (all possible values of a node like a dropdown or checkboxes) or NULL.</p></description>
<example>if( $block->hasPossibleValues( $id ) )
    u\DebugUtility::dump( $block->getPossibleValues( $id ) );
</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getPossibleValues( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPossibleValues( $identifier );
    }
    
/**
<documentation><description><p>Returns the <code>p\StructuredData</code> object.</p></description>
<example>u\DebugUtility::dump( $block->getStructuredData()->toStdClass() );</example>
<return-type>StructuredData</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getStructuredData() : p\StructuredData
    {
        $this->checkStructuredData();
        return $this->structured_data;
    }
    
/**
<documentation><description><p>Returns <code>symlinkId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getSymlinkId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getSymlinkId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkId( $identifier );
    }
    
/**
<documentation><description><p>Returns <code>symlinkPath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getSymlinkPath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getSymlinkPath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkPath( $identifier );
    }
    
/**
<documentation><description><p>Returns the text of a node.</p></description>
<example>if( $block->isText( $id ) )
    echo $block->getText( $id ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getText( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getText( $identifier );
    }
    
/**
<documentation><description><p>Returns the type string of a text node. A text node is an instance of a normal text field (including a text box, a multi-line and a WYSIWYG, all three being associated with <code>NULL</code>), or a text field of type <code>datetime</code>, <code>calendar</code>, <code>multi-selector</code>, <code>dropdown</code>, or <code>checkbox</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getTextNodeType( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getTextNodeType( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getTextNodeType( $identifier );
    }

/**
<documentation><description><p>Returns <code>xhtml</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $block->getXhtml() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getXhtml()
    {
        return $this->xhtml;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the node bearing that identifier exists.</p></description>
<example>if( $block->hasIdentifier( $id ) &amp;&amp; $block->isAssetNode( $id ) )
    echo $block->getAssetNodeType( $id ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function hasIdentifier( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->hasNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>hasIdentifier</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function hasNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->hasNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether there are phantom nodes of type B in the block.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function hasPhantomNodes() : bool // detects phantom nodes of type B
    {
        $this->checkStructuredData();
        return $this->structured_data->hasPhantomNodes();
    }

/**
<documentation><description><p>Returns a bool, indicating whether the named node has
possible values.</p></description>
<example>if( $block->hasPossibleValues( $id ) )
    u\DebugUtility::dump( $block->getPossibleValues( $id ) );
</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPossibleValues( string $identifier ) : bool
    {
        return $this->structured_data->hasPossibleValues( $identifier );
    }
   
/**
<documentation><description><p>Returns a bool, indicating whether
<code>structuredData</code> is defined (having nodes). <code>true</code> means that the
block is a data definition block. <code>false</code> means that the block is an xhtml block.</p></description>
<example>echo u\StringUtility::boolToString( $xhtml->hasStructuredData() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasStructuredData() : bool
    {
        return $this->structured_data != NULL;
    }
    
/**
<documentation><description><p>An alias of <code>isAssetNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isAsset( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isAssetNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is an
asset node, allowing users to choose an asset.</p></description>
<example>if( $block->hasIdentifier( $id ) &amp;&amp; $block->isAssetNode( $id ) )
    echo $block->getAssetNodeType( $id ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isAssetNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isAssetNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isBlockChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isBlockChooser( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isBlockChooser( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
block chooser node, allowing users to choose a block.</p></description>
<example>if( $block->isBlockChooserNode( $id ) )
{
    echo u\StringUtility::getCoalescedString( $block->getBlockId( $id ) ), BR;
    echo u\StringUtility::getCoalescedString( $block->getBlockPath( $id ) ), BR;
}</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isBlockChooserNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isBlockChooser( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isCalendarNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isCalendar( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isCalendarNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
calendar node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isCalendarNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isCalendarNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isCalendarNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isCheckboxNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isCheckbox( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isCheckboxNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a checkbox node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isCheckboxNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isCheckboxNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isCheckboxNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isDatetimeNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isDatetime( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isDatetimeNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a datetime node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isDatetimeNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isDatetimeNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isDatetimeNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isDropdownNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isDropdown( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isDropdownNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a dropdown node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isDropdownNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isDropdownNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isDropdownNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isFileChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isFileChooser( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isFileChooser( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a file chooser node, allowing users to choose a file.</p></description>
<example>echo u\StringUtility::boolToString( $block->isFileChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isFileChooserNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isFileChooser( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isGroupNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isGroup( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isGroupNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a group node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isGroupNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isGroupNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isGroupNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isLinkableChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isLinkableChooser( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isLinkableChooser( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a linkable chooser node, allowing users to choose a file, a page, or a symlink.</p></description>
<example>echo u\StringUtility::boolToString( $block->isLinkableChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isLinkableChooserNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isLinkableChooser( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isMultiLine( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a multi-line node (i.e., a textarea).</p></description>
<example>echo u\StringUtility::boolToString( $block->isMultiLineNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isMultiLineNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isMultipleNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isMultiple( string $identifier ) : bool
    {
        return $this->isMultipleNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field name is associated with a multiple field. Note that the field name should be a fully qualified identifier of the associated data definition.</p></description>
<example>echo u\StringUtility::boolToString( $block->isMultipleField( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isMultipleField( string $field_name ) : bool
    {
        $this->checkStructuredData();
        return $this->getDataDefinition()->isMultiple( $field_name );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a multiple node (an instance of a multiple field).</p></description>
<example>echo u\StringUtility::boolToString( $block->isMultipleNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isMultipleNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiple( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isMultiSelectorNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isMultiSelector( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiSelectorNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a multi-selector node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isMultiSelectorNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isMultiSelectorNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiSelectorNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isPageChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isPageChooser( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isPageChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a page
chooser node, allowing users to choose a page.</p></description>
<example>echo u\StringUtility::boolToString( $block->isPageChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isPageChooserNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isPageChooserNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isRadioNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isRadio( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isRadioNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a radio node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isRadioNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isRadioNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isRadioNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field value is required by the named field.</p></description>
<example>echo u\StringUtility::boolToString( $block->isRequired( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isRequired( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isRequired( $identifier );
    }

/**
<documentation><description><p>An alias of <code>isSymlinkChooserNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isSymlinkChooser( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isSymlinkChooser( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a symlink chooser node, allowing users to choose a symlink.</p></description>
<example>echo u\StringUtility::boolToString( $block->isSymlinkChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isSymlinkChooserNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isSymlinkChooser( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isTextBoxNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isTextBox( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextBox( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a simple text box node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isTextBoxNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isTextBoxNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextBox( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isTextNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isText( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isTextarea( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isMultiLineNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isTextareaNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>Returns returns a bool, indicating whether the named node is a text node.</p></description>
<example>if( $block->isTextNode( $id ) )
    echo $block->getText( $id ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isTextNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>isWYSIWYGNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isWYSIWYG( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isWYSIWYG( $identifier );
    }

/**
<documentation><description><p>Returns a bool, indicating whether the named node is a WYSIWYG node.</p></description>
<example>echo u\StringUtility::boolToString( $block->isWYSIWYGNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function isWYSIWYGNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isWYSIWYGNode( $identifier );
    }
    
/**
<documentation><description><p>Removes all phantom nodes of type B, and returns the calling object. Because this method relies on the <code>batch</code> operation, which is not defined for REST (Cascade 8.7.1), it only works for SOAP.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function mapData() : Asset
    {
        if( $this->getService()->isSoap() )
        {
            $this->checkStructuredData();
            $new_sd = $this->structured_data->mapData();
            
            return $this->setStructuredData( $new_sd );
        }
        u\DebugUtility::out( "Soap is required for this to work" );
        return $this;
    }

/**
<documentation><description><p>Removes the last node from a set of multiple nodes, calls
<code>edit</code>, and returns the calling object. The identifier supplied must the fully
qualified identifier of the first node of the set.</p></description>
<example>$block->removeLastSibling( "multiple-first;0" );</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function removeLastSibling( string $identifier ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->removeLastSibling( $identifier );
        $this->edit();
        return $this;
    }
    
/**
<documentation><description><p>Removes phantom nodes of type B in the block.</p></description>
<example>$block->removePhantomNodes();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function removePhantomNodes( array &$results=NULL ) : Asset
    {
        $this->checkStructuredData();
        // type B
        if( $this->structured_data->hasPhantomNodes() )
        {
            $this->mapData();
            
            if( isset( $results ) )
            {
            	$results[ self::TYPE ][ "B" ][] = $this->getPath();
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Removes phantom values in the block.</p></description>
<example>$block->removePhantomValues();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function removePhantomValues( array &$results=NULL ) : Asset
    {
        $this->checkStructuredData();
        
        if( $this->structured_data->hasPhantomValues() )
        {
            $this->structured_data->removePhantomValues();
            
            if( isset( $results ) )
            {
            	$results[ self::TYPE ][] = $this->getPath();
            }
            
            return $this->edit();
        }
        return $this;
    }
    
/**
<documentation><description><p>Replaces the pattern with the replacement string for normal
text fields, and fields of type datetime and calendar, and returns the calling object.
Inside the method <code>preg_replace</code> is called. If an array of fully qualified
identifiers is also passed in, then only those nodes will be affected.</p></description>
<example>if( $block->isWYSIWYGNode( $id ) )
{
    $block->replaceByPattern(
        "/" . "&lt;" . "p&gt;([^&lt;]+)&lt;\/p&gt;/", 
        "&lt;div class='text_red'&gt;$1&lt;/div&gt;", 
        array( $id )
    )->edit();
}</example><return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function replaceByPattern(
       string $pattern, string $replace, array $include=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->replaceByPattern( $pattern, $replace, $include );
        return $this;
    }
    
/**
<documentation><description><p>Replaces the string found with the replacement string for
normal text fields, and fields of type datetime and calendar, and returns the calling
object. Inside the method <code>str_replace</code> is called. If an array of fully
qualified identifiers is also passed in, then only those nodes will be affected.</p></description>
<example>$block->replaceText( "Wonderful", "Amazing" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function replaceText(
        string $search, string $replace, array $include=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->replaceText( $search, $replace, $include );
        return $this;
    }
    
/**
<documentation><description><p>Replaces the pattern with the replacement string in the
<code>xhtml</code> and returns the calling object. Inside the method <code>preg_replace</code> is called.</p></description>
<example>$xhtml->replaceXhtmlByPattern(
    "/" . "&lt;" . "p>([^&lt;]+)&lt;\/p&gt;/", 
    "&lt;div class='text_red'&gt;$1&lt;/div&gt;" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function replaceXhtmlByPattern( string $pattern, string $replace ) : Asset
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_XHTML_BLOCK . E_SPAN );
        }
        
        $this->xhtml = preg_replace( $pattern, $replace, $this->xhtml );
        
        return $this;
    }
    
/**
<documentation><description><p>Searches all text nodes for the string, and returns an
array of fully qualified identifiers of nodes where the string is found.</p></description>
<example>u\DebugUtility::dump( $block->searchText( "Amazing" ) );</example>
<return-type>array</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function searchText( string $string ) : array
    {
        $this->checkStructuredData();
        return $this->structured_data->searchText( $string );
    }
    
/**
<documentation><description><p>Searches all text nodes for the pattern, and returns an
array of fully qualified identifiers of nodes where the pattern is found.</p></description>
<example></example>
<return-type>array</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function searchTextByPattern( string $pattern ) : array
    {
        $this->checkStructuredData();
        return $this->structured_data->searchTextByPattern( $pattern );
    }
    
/**
<documentation><description><p>Searches all WYSIWYG nodes for the pattern, and returns an
array of fully qualified identifiers of nodes where the pattern is found.</p></description>
<example></example>
<return-type>array</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function searchWYSIWYGByPattern( string $pattern ) : array
    {
        $this->checkStructuredData();
        return $this->structured_data->searchWYSIWYGByPattern( $pattern );
    }

/**
<documentation><description><p>Returns a bool, indicating whether the string is found in the xhtml block.</p></description>
<example>echo u\StringUtility::boolToString( $xhtml->searchXhtml( "hello" ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function searchXhtml( string $string ) : bool
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_XHTML_BLOCK . E_SPAN );
        }

        return strpos( $this->xhtml, $string ) !== false;
    }

/**
<documentation><description><p>Sets the node's <code>blockId</code> and <code>blockPath</code> properties, and returns the callling object.</p></description>
<example>$block->setBlock(
    "group;block-chooser",
    $cascade->getAsset(
        a\DataBlock::TYPE, "1f21e3268b7ffe834c5fe91e2e0a7b2d" ) )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setBlock( string $identifier, Block $block=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setBlock( $identifier, $block );
        return $this;
    }
    
/**
<documentation><description><p>Sets the node's <code>fileId</code> and <code>filePath</code> properties, and returns the calling object.</p></description>
<example>$block->setFile( "group;file-chooser" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setFile( string $identifier, File $file=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setFile( $identifier, $file );
        return $this;
    }

/**
<documentation><description><p>Sets the node's <code>fileId</code> and
<code>filePath</code>, or <code>pageId</code> and <code>pagePath</code>, or
<code>symlinkId</code> and <code>symlinkPath</code> properties, depending on what is
passed in, and returns the calling object.</p></description>
<example>$block->setLinkable( "group;linkable-chooser" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setLinkable( string $identifier, Linkable $linkable=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setLinkable( $identifier, $linkable );
        return $this;
    }

/**
<documentation><description><p>Sets the node's <code>pageId</code> and
<code>pathPath</code> properties, and returns the calling object.</p></description>
<example>$block->setPage( "group;page-chooser" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setPage( string $identifier, Page $page=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setPage( $identifier, $page );
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>structuredData</code>, calls
<code>edit</code>, and returns the calling object. Note that the calling object must be a data definition block associated with a data definition.</p></description>
<example>$block->setStructuredData( $block2->getStructuredData() );</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setStructuredData( p\StructuredData $structured_data ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data = $structured_data;
        $this->edit();
        $this->processStructuredData();
        return $this;
    }

/**
<documentation><description><p>Sets the node's <code>symlinkId</code> and <code>symlinkPath</code> properties, and returns the calling object.</p></description>
<example>$block->setSymlink( "group;symlink-chooser" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setSymlink( string $identifier, Symlink $symlink=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setSymlink( $identifier, $symlink );
        return $this;
    }

/**
<documentation><description><p>Sets the node's <code>text</code>, and returns the calling
object.</p></description>
<example>$block->setText( "group;text-box", "Good News" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setText( string $identifier, string $text ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setText( $identifier, $text );
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>xhtml</code> property of an xhtml block, and
returns the calling object.</p></description>
<example>$xhtml->setXhtml( "<span class='italic'>This is meaningless!</span>" )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function setXhtml( string $xhtml ) : Asset
    {
        if( !$this->hasStructuredData() )
        {
            $this->xhtml = $xhtml;
        }
        else
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_XHTML_BLOCK . E_SPAN );
        }
        return $this;
    }

/**
<documentation><description><p>Swaps the data of two nodes, calls <code>edit</code>, and
returns the calling object. Since this method call can be chained, and all the fully qualified identifiers must be recalculated after each swap, this method has to call
<code>edit</code> so that the change takes effect immediately.</p></description>
<example>$block->swapData( "multiple-first;0", "multiple-first;2" );</example>
<return-type>Asset</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function swapData( string $identifier1, string $identifier2 ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->swapData( $identifier1, $identifier2 );
        $this->edit()->processStructuredData();
        
        return $this;
    }

    private function checkStructuredData()
    {
        if( !$this->hasStructuredData() )
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_DATA_BLOCK . E_SPAN );
        }
    }
    
    private function processStructuredData()
    {
        $this->structured_data = new p\StructuredData( 
            $this->getProperty()->structuredData, 
            $this->getService(), 
            $this->getProperty()->structuredData->definitionId,
            $this );
    }

    private $structured_data;
    private $xhtml;
}
?>