<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 12/27/2017 Updated documentation.
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 10/24/2016 Added construtor.
  * 9/22/2016 Added default param values to all set methods.
  * 5/28/2015 Added namespaces.
  * 7/15/2014 Added getContentType and getFolder.
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
<p>A <code>IndexBlock</code> object represents an index block asset. This class is a
sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/block.php\"><code>Block</code></a>.</p>
<p>There are two types of index blocks: \"folder\" and \"content-type\". Use the <code>IndexBlock::getIndexBlockType()</code> method
to get the type, or use <code>IndexBlock::isFolder()</code> or <code>IndexBlock::isContent()</code> to test the type.</p>
<h2>Structure of <code>indexBlock</code></h2>
<pre>SOAP:
indexBlock
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
  reviewOnSchedule
  reviewEvery
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled
  indexBlockType
  indexedFolderId
  indexedFolderPath
  indexedContentTypeId
  indexedContentTypePath
  indexedFolderRecycled
  maxRenderedAssets
  depthOfIndex
  renderingBehavior
  indexPages
  indexBlocks
  indexLinks
  indexFiles
  indexRegularContent
  indexSystemMetadata
  indexUserMetadata
  indexAccessRights
  indexUserInfo
  indexWorkflowInfo
  appendCallingPageData
  sortMethod
  sortOrder
  pageXML
  
REST:
indexBlock
  indexedFolderId
  indexedFolderPath
  indexedContentTypeId
  indexedContentTypePath
  indexedFolderRecycled
  indexBlockType
  maxRenderedAssets
  depthOfIndex
  indexPages
  indexBlocks
  indexLinks
  indexFiles
  indexRegularContent
  indexSystemMetadata
  indexUserMetadata
  indexAccessRights
  indexUserInfo
  indexWorkflowInfo
  appendCallingPageData
  sortMethod
  sortOrder
  pageXML
  renderingBehavior
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled
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
    dynamicFields (array of stdClass)
      name
      fieldValues (array of stdClass)
        value
  reviewOnSchedule
  reviewEvery
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
<p>Note that there is a <code>pageXML</code> property but no <code>blockXML</code> property. This is a bug in Cascade, and I cannot provide any methods to access this missing property.</p>
<h2>Design Issues</h2>
<ul>
<li>Although it is not hard to implement, I decide not to provide a <code>setType</code> method.
Switching between folder indexing and content type indexing may not be a good idea.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "indexBlock" ),
        array( "getSimpleTypeXMLByName"  => "index-block-sort-method" ),
        array( "getSimpleTypeXMLByName"  => "index-block-type" ),
        array( "getSimpleTypeXMLByName"  => "index-block-sort-order" ),
        array( "getSimpleTypeXMLByName"  => "index-block-page-xml" ),
        array( "getSimpleTypeXMLByName"  => "index-block-rendering-behavior" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/index_block_folder.php">index_block_folder.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/index_block_content.php">index_block_content.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/block_INDEX/987203c88b7ffe8353cc17e9ed0bab6c

{
  "asset":{
    "indexBlock":{
      "indexedFolderId":"c12dce028b7ffe83129ed6d8fdc88b47",
      "indexedFolderPath":"_cascade",
      "indexedFolderRecycled":false,
      "indexBlockType":"folder",
      "maxRenderedAssets":50,
      "depthOfIndex":3,
      "indexPages":true,
      "indexBlocks":true,
      "indexLinks":true,
      "indexFiles":true,
      "indexRegularContent":true,
      "indexSystemMetadata":true,
      "indexUserMetadata":true,
      "indexAccessRights":true,
      "indexUserInfo":true,
      "indexWorkflowInfo":true,
      "appendCallingPageData":true,
      "sortMethod":"last-modified-date",
      "sortOrder":"descending",
      "pageXML":"render-current-page-only",
      "renderingBehavior":"hierarchy",
      "expirationFolderRecycled":false,
      "metadataSetId":"c12dd0738b7ffe83129ed6d86580d804",
      "metadataSetPath":"Default",
      "metadata":{},
      "reviewOnSchedule":false,
      "reviewEvery":0,
      "parentFolderId":"c12dcef18b7ffe83129ed6d85960d93d",
      "parentFolderPath":"_cascade/blocks/code",
      "lastModifiedDate":"Jan 23, 2018 8:54:15 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Dec 27, 2017 9:48:17 AM",
      "createdBy":"wing",
      "path":"_cascade/blocks/code/test-folder-index",
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "name":"test-folder-index",
      "id":"987203c88b7ffe8353cc17e9ed0bab6c"
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
class IndexBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::INDEXBLOCK;
 
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

/**
<documentation><description><p>Returns <code>appendCallingPageData</code>.</p></description>
<example>echo "Append calling page data: " . 
    u\StringUtility::boolToString( $ifb->getAppendCallingPageData() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getAppendCallingPageData() : bool
    {
        return $this->getProperty()->appendCallingPageData;
    }
    
/**
<documentation><description><p>Returns the <code>ContentType</code> object associated
with the block, or <code>NULL</code> if the block is not of type content type.</p></description>
<example>$ifb->getContentType()->dump();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getContentType()
    {
        if( $this->isContent() && $this->getIndexedContentTypeId() != NULL )
        {
            $service = $this->getService();
            return new ContentType( 
                $service, $service->createId( 
                    ContentType::TYPE, $this->getIndexedContentTypeId() ) );
        }
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>depthOfIndex</code>.</p></description>
<example>echo "Depth of index: " . $ifb->getDepthOfIndex() . BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getDepthOfIndex() : int
    {
        return $this->getProperty()->depthOfIndex;
    }
    
/**
<documentation><description><p>Returns the <code>Folder</code> object associated with
the block, or <code>NULL</code> if the block is not of type folder.</p></description>
<example>$ifb->getFolder()->dump();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFolder()
    {
        if( $this->isFolder() && $this->getIndexedFolderId() != NULL )
        {
            $service = $this->getService();
            if( self::DEBUG ) { 
                u\DebugUtility::out( "Returning folder" . "ID " .
                    $this->getIndexedFolderPath() ); }
            return new Folder( 
                $service, $service->createId( Folder::TYPE, $this->getIndexedFolderId() ) );
        }
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>indexAccessRights</code>.</p></description>
<example>echo "Index access rights: " . 
    u\StringUtility::boolToString( $ifb->getIndexAccessRights() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexAccessRights() : bool
    {
        return $this->getProperty()->indexAccessRights;
    }
    
/**
<documentation><description><p>Returns <code>indexBlocks</code>.</p></description>
<example>echo "Index blocks: " . u\StringUtility::boolToString(
    $ifb->getIndexBlocks() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexBlocks() : bool
    {
        return $this->getProperty()->indexBlocks;
    }
    
    // no setter
/**
<documentation><description><p>Returns <code>indexBlockType</code>.</p></description>
<example>echo "Index type: " . $ifb->getIndexBlockType() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexBlockType() : string
    {
        return $this->getProperty()->indexBlockType;
    }
    
/**
<documentation><description><p>Returns
<code>indexedContentTypeId</code>.</p></description>
<example>echo "Indexed content type ID: " . u\StringUtility::getCoalescedString(
    $ifb->getIndexedContentTypeId() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedContentTypeId()
    {
        if( isset( $this->getProperty()->indexedContentTypeId ) )
            return $this->getProperty()->indexedContentTypeId;
        return NULL;
    }
    
/**
<documentation><description><p>Returns
<code>indexedContentTypePath</code>.</p></description>
<example>echo "Indexed content type path: " . u\StringUtility::getCoalescedString(
    $ifb->getIndexedContentTypePath() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedContentTypePath()
    {
        if( isset( $this->getProperty()->indexedContentTypePath ) )
            return $this->getProperty()->indexedContentTypePath;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>indexedFolderId</code>.</p></description>
<example>echo "Indexed folder ID: " . u\StringUtility::getCoalescedString(
    $ifb->getIndexedFolderId() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedFolderId()
    {
        if( isset( $this->getProperty()->indexedFolderId ) )
            return $this->getProperty()->indexedFolderId;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>indexedFolderPath</code>.</p></description>
<example>echo "Indexed folder path: " . u\StringUtility::getCoalescedString(
    $ifb->getIndexedFolderPath() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedFolderPath()
    {
        if( isset( $this->getProperty()->indexedFolderPath ) )
            return $this->getProperty()->indexedFolderPath;
        return NULL;
    }
    
/**
<documentation><description><p>Returns
<code>indexedFolderRecycled</code>.</p></description>
<example>echo "Indexed folder recycled: " . u\StringUtility::boolToString(
    $ifb->getIndexedFolderRecycled() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedFolderRecycled() : bool
    {
        return $this->getProperty()->indexedFolderRecycled;
    }
    
/**
<documentation><description><p>Returns <code>indexFiles</code>.</p></description>
<example>echo "Index files: " . u\StringUtility::boolToString(
    $ifb->getIndexFiles() ) . BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexFiles() : bool
    {
        return $this->getProperty()->indexFiles;
    }
    
/**
<documentation><description><p>Returns <code>indexLinks</code>.</p></description>
<example>echo "Index links: " . u\StringUtility::boolToString(
    $ifb->getIndexLinks() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexLinks() : bool
    {
        return $this->getProperty()->indexLinks;
    }
    
/**
<documentation><description><p>Returns <code>indexPages</code>.</p></description>
<example>echo "Index pages: " . u\StringUtility::boolToString(
    $ifb->getIndexPages() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexPages() : bool
    {
        return $this->getProperty()->indexPages;
    }
    
/**
<documentation><description><p>Returns <code>indexRegularContent</code>.</p></description>
<example>echo "Index regular content: " . u\StringUtility::boolToString(
    $ifb->getIndexRegularContent() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexRegularContent() : bool
    {
        return $this->getProperty()->indexRegularContent;
    }
    
/**
<documentation><description><p>Returns <code>indexSystemMetadata</code>.</p></description>
<example>echo "Index system metadata: " . u\StringUtility::boolToString(
    $ifb->getIndexSystemMetadata() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexSystemMetadata() : bool
    {
        return $this->getProperty()->indexSystemMetadata;
    }
    
/**
<documentation><description><p>Returns <code>indexUserInfo</code>.</p></description>
<example>echo "Index user info: " . u\StringUtility::boolToString(
    $ifb->getIndexUserInfo() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexUserInfo() : bool
    {
        return $this->getProperty()->indexUserInfo;
    }
    
/**
<documentation><description><p>Returns <code>indexUserMetadata</code>.</p></description>
<example>echo "Index user metadata: " . u\StringUtility::boolToString(
    $ifb->getIndexUserMetadata() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexUserMetadata() : bool
    {
        return $this->getProperty()->indexUserMetadata;
    }
    
/**
<documentation><description><p>Returns <code>indexWorkflowInfo</code>.</p></description>
<example>echo "Index workflow info: " . u\StringUtility::boolToString(
    $ifb->getIndexWorkflowInfo() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIndexWorkflowInfo() : bool
    {
        return $this->getProperty()->indexWorkflowInfo;
    }
    
/**
<documentation><description><p>Returns <code>maxRenderedAssets</code>.</p></description>
<example>echo "Max rendered assets: " . $ifb->getMaxRenderedAssets() . BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getMaxRenderedAssets() : int
    {
        return $this->getProperty()->maxRenderedAssets;
    }
    
/**
<documentation><description><p>Returns <code>pageXML</code>. Possible values for this
property are: <code>c\T::NORENDER</code>, <code>c\T::RENDER</code>, and
<code>c\T::RENDERCURRENTPAGEONLY</code>.</p></description>
<example>echo "Page xml: " . $ifb->getPageXML() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPageXML() : string
    {
        return $this->getProperty()->pageXML;
    }
    
/**
<documentation><description><p>Returns <code>renderingBehavior</code>. Possible values
for this property are: <code>c\T::RENDERNORMALLY</code>, <code>c\T::HIERARCHY</code>,
<code>c\T::HIERARCHYWITHSIBLINGS</code>, and
<code>c\T::HIERARCHYSIBLINGSFORWARD</code>.</p></description>
<example>echo "Rendering behavior: " . $ifb->getRenderingBehavior() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRenderingBehavior() : string
    {
        return $this->getProperty()->renderingBehavior;
    }
    
/**
<documentation><description><p>Returns <code>sortMethod</code>. Possible values for this
property are: <code>c\T::FOLDERORDER</code>, <code>c\T::ALPHABETICAL</code>,
<code>c\T::LASTMODIFIEDDATE</code>, and
<code>c\T::CREATEDDATE</code>.</p></description>
<example>echo "Sort method: " . $ifb->getSortMethod() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSortMethod() : string
    {
        return $this->getProperty()->sortMethod;
    }
    
/**
<documentation><description><p>Returns <code>sortOrder</code>. Possible values for this
property are: <code>c\T::ASCENDING</code>, and
<code>c\T::DESCENDING</code>.</p></description>
<example>echo "Sort order: " . $ifb->getSortOrder() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSortOrder() : string
    {
        return $this->getProperty()->sortOrder;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the type of the block is
"content-type".</p></description>
<example>echo "Is content: " . u\StringUtility::boolToString(
    $ifb->isContent() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isContent() : bool
    {
        return $this->getProperty()->indexBlockType == c\T::CONTENTTYPEINDEX;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the type of the block is
"folder".</p></description>
<example>echo "Is folder: " . u\StringUtility::boolToString(
    $ifb->isFolder() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isFolder() : bool
    {
        return $this->getProperty()->indexBlockType == c\T::FOLDER;
    }
    
/**
<documentation><description><p>Sets <code>appendCallingPageData</code> and returns the
calling object.</p></description>
<example>$ifb->setAppendCallingPageData( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAppendCallingPageData( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->appendCallingPageData = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexedContentTypeId</code> and
<code>indexedContentTypePath</code>, and returns the calling object.</p></description>
<example>$icb->setContentType( 
    $cascade->getAsset( a\ContentType::TYPE, '9bdfd8928b7f0856002a5e11732284e6' ) )->
    edit();</example>
<return-type>Asset</return-type>
<exception>WrongTypeException</exception>
</documentation>
*/
    public function setContentType( ContentType $content_type ) : Asset
    {
        if( $this->getIndexBlockType() != c\T::CONTENTTYPEINDEX )
        {
            throw new e\WrongTypeException( 
                S_SPAN . "This block is not a content type index block." . E_SPAN );
        }
    
        $this->getProperty()->indexedContentTypeId   = $content_type->getId();
        $this->getProperty()->indexedContentTypePath = $content_type->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>depthOfIndex</code> and returns the calling
object.</p></description>
<example>$ifb->setDepthOfIndex( 3 )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException, WrongTypeException</exception>
</documentation>
*/
    public function setDepthOfIndex( int $num=2 ) : Asset
    {
        if( intval( $num ) < 1 )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $num is unacceptable." . E_SPAN );
        }
        
        if( $this->getIndexBlockType() != Folder::TYPE )
        {
            throw new e\WrongTypeException( 
                S_SPAN . "This block is not a folder index block." . E_SPAN );
        }
        
        $this->getProperty()->depthOfIndex = $num;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexedFolderId</code> and
<code>indexedFolderPath</code>, and returns the calling object.</p></description>
<example>$ifb->setFolder( $folder )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongTypeException</exception>
</documentation>
*/
    public function setFolder( Folder $folder ) : Asset
    {
        if( $this->getIndexBlockType() != Folder::TYPE )
        {
            throw new e\WrongTypeException( 
                S_SPAN . "This block is not a folder index block." . E_SPAN );
        }
    
        $this->getProperty()->indexedFolderId = $folder->getId();
        $this->getProperty()->indexedFolderPath = $folder->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexAccessRights</code> and returns the
calling object.</p></description>
<example>$ifb->setIndexAccessRights( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexAccessRights( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexAccessRights = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexBlocks</code> and returns the calling
object.</p></description>
<example>$ifb->setIndexBlocks( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexBlocks( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexBlocks = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexFiles</code> and returns the
calling object.</p></description>
<example>$ifb->setIndexFiles( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexFiles( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexFiles = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexedFolderRecycled</code> and returns the
calling object.</p></description>
<example>$ifb->setIndexedFolderRecycled( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexedFolderRecycled( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexedFolderRecycled = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexLinks</code> and returns the calling
object.</p></description>
<example>$ifb->setIndexLinks( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexLinks( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexLinks = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexPages</code> and returns the calling
object.</p></description>
<example>$ifb->setIndexPages( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexPages( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexPages = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexRegularContent</code> and returns the
calling object.</p></description>
<example>$ifb->setIndexRegularContent( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexRegularContent( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexRegularContent = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexSystemMetadata</code> and returns the
calling object.</p></description>
<example>$ifb->setIndexSystemMetadata( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexSystemMetadata( bool $b=true ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexSystemMetadata = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexUserInfo</code> and returns the calling
object.</p></description>
<example>$ifb->setIndexUserInfo( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexUserInfo( bool $b ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexUserInfo = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexUserMetadata</code> and returns the
calling object.</p></description>
<example>$ifb->setIndexUserMetadata( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexUserMetadata( bool $b=true ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexUserMetadata = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>indexWorkflowInfo</code> and returns the
calling object.</p></description>
<example>$ifb->setIndexWorkflowInfo( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIndexWorkflowInfo( bool $b=false ) : Asset
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexWorkflowInfo = $b;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>maxRenderedAssets</code> and returns the
calling object.</p></description>
<example>$ifb->setMaxRenderedAssets( 50 )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setMaxRenderedAssets( int $num=0 ) : Asset
    {
        if( intval( $num ) < 0 )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $num is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->maxRenderedAssets = $num;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>pageXML</code> and returns the calling
object. Possible values for this property are: <code>c\T::NORENDER</code>,
<code>c\T::RENDER</code>, and <code>c\T::RENDERCURRENTPAGEONLY</code>.</p></description>
<example>$ifb->setPageXML( c\T::NORENDER )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setPageXML( string $page_xml=c\T::NORENDER ) : Asset
    {
        if( $page_xml != c\T::NORENDER &&
            $page_xml != c\T::RENDER &&
            $page_xml != c\T::RENDERCURRENTPAGEONLY
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The pageXML $page_xml is unacceptable." . E_SPAN );
        }
    
        $this->getProperty()->pageXML = $page_xml;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>renderingBehavior</code> and returns the
calling object. Possible values for this property are: <code>c\T::RENDERNORMALLY</code>,
<code>c\T::HIERARCHY</code>, <code>c\T::HIERARCHYWITHSIBLINGS</code>, and
<code>c\T::HIERARCHYSIBLINGSFORWARD</code>.</p></description>
<example>$ifb->setRenderingBehavior( c\T::RENDERNORMALLY )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setRenderingBehavior( string $behavior=c\T::RENDERNORMALLY ) : Asset
    {
        if( $behavior != c\T::RENDERNORMALLY &&
            $behavior != c\T::HIERARCHY &&
            $behavior != c\T::HIERARCHYWITHSIBLINGS &&
            $behavior != c\T::HIERARCHYSIBLINGSFORWARD
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The behavior $behavior is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->renderingBehavior = $behavior;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>sortMethod</code> and returns the calling
object. Possible values for this property are: <code>c\T::FOLDERORDER</code>,
<code>c\T::ALPHABETICAL</code>, <code>c\T::LASTMODIFIEDDATE</code>, and
<code>c\T::CREATEDDATE</code>.</p></description>
<example>$ifb->setSortMethod( c\T::FOLDERORDER )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSortMethod( string $method=c\T::FOLDERORDER ) : Asset
    {
        if( $method != c\T::FOLDERORDER &&
            $method != c\T::ALPHABETICAL &&
            $method != c\T::LASTMODIFIEDDATE &&
            $method != c\T::CREATEDDATE
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The method $method is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->sortMethod = $method;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>sortOrder</code> and returns the calling
object. Possible values for this property are: <code>c\T::ASCENDING</code>, and
<code>c\T::DESCENDING</code>.</p></description>
<example>$ifb->setSortOrder( c\T::ASCENDING )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSortOrder( string $order=c\T::ASCENDING ) : Asset
    {
        if( $order != c\T::ASCENDING &&
            $order != c\T::DESCENDING
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The order $order is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->sortOrder = $order;
        return $this;
    }
    
    private function checkBoolean( bool $b )
    {
        if( !c\BooleanValues::isBoolean( $b ) )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $b is not a boolean." . E_SPAN );
        }
    }
}
?>