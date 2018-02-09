<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/5/2018 Added removePhantomValues.
  * 1/24/2018 Updated documentation.
  * 12/28/2017 Added REST code and updated documentation.
  * 8/1/2017 Added getBlock.
  * 6/26/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 11/2/2016 Added hasPossibleValues, isMultipleField and isMultipleNode.
  * Rewrote code so that methods like getDataDefinitionId, getDataDefinitionPath do
  * return useful information.
  * 6/20/2016 Added searchTextByPattern and searchWYSIWYGByPattern.
  * 6/2/2016 Added aliases.
  * 6/1/2016 Added isBlockChooser, isCalendarNode, isCheckboxNode, isDatetimeNode, isDropdownNode,
  * isFileChooser, isLinkableChooser, isMultiLineNode, isMultiSelectorNode, isPageChooser,
  * isRadioNode, isSymlinkChooser, isTextBox, and isWYSIWYGNode.
  * 3/11/2016 Added call to checkStructuredData in hasPhantomNodes.
  * 3/10/2016 Added hasPhantomNodes.
  * 3/9/2016 Added mapData.
  * 1/8/2016 Added code to deal with host asset.
  * 1/4/2016 Fixed a bug in publish.
  * 12/11/2015 Added getPageRegions and getPageRegion.
  * 10/30/2015 Added unpublish.
  * 9/10/2015 Added the display of string id to checkStructuredData. 
  * 6/23/2015 Fixed a bug in edit.
  * 5/28/2015 Added namespaces.
  * 5/1/2015 Changed signature of edit and added editWithoutException.
  *   Reason: when changing the content type associated with a page,
  *           if a different data definition is used, phantom nodes will
  *           cause a lot of exceptions. The restriction must be loosened
  *           so that a page can be modified.
  * 4/9/2015 Added a flag to setContentType to avoid exception.
  * 2/24/2015 Added getPossibleValues.
  * 2/23/2015 Added the missing isMultiLineNode.
  * 10/2/2014 Fixed a bug in edit.
  * 9/18/2014 Added getMetadataSet, getMetadataSetId, getMetadataSetPath.
  * 8/29/2014 Fixed bugs in appendSibling and removeLastSibling.
  * 8/27/2014 Added getParentFolder, getParentFolderId, getParentFolderPath.
  * 8/20/2014 Added hasConfiguration.
  * 7/23/2014 Split getPageLevelRegionBlockFormat into getPageLevelRegionBlockFormat and getBlockFormatMap and
  * added no-block and no-format.
  * 7/22/2014 Added getMetadataStdClass, isPublishable, setMetadata.
  * 6/5/2014 Fixed a bug in getPageLevelRegionBlockFormat.
  * 5/13/2014 Added createNInstancesForMultipleField 
  *   and replaced all string literals with constants
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
<p>The <code>Page</code> class can be used to represent a page asset and is a sub-class of
<a href=\"http://www.upstate.edu/web-services/api/asset-classes/linkable.php\"><code>Linkable</code></a>.
This class can be used to manipulate pages with or without a data definition. The only
test available to tell them apart is the <code>Page::hasStructuredData</code> method. When
it returns true, the page is a page associated with a data definition; else it is not. We
cannot consider the <code>xhtml</code> property because it can be <code>NULL</code> for
both page sub-types. But note that a page that is associated with data definition and has absolutely no data, this method will return <code>false</code>. Then we have to try to retrieve the associated data definition by calling <code>getDataDefinition</code>, though this method may throw a <code>WrongPageTypeException</code>.</p>
<p>As I point out in <a href=\"http://www.upstate.edu/web-services/api/asset-classes/data-definition-block.php\"><code>DataDefinitionBlock</code></a>,
even though we are dealing with two sub-types of pages or blocks, it does not make sense
to split this class, or the <code>DataDefinitionBlock</code> class, into two. One class is
enough to deal with these sub-types.</p>
<p>Since this class can handle both pages associated with data definitions and pages that are not, the majority of the methods handle pages associated with data definitions and only a few handle pages that are not. If a method like <code>Page::setText</code>, which is used to set the text of a node, is called upon a page not associated with a data definition, an exception will be thrown. On the other hand, a method like <code>Page::setXHTML</code> is meaningful only for pages not associated with a data definition. When called upon a page associated with a data definition, this method throws an exception.</p>
<h2>About Metadata Set and Page Configuration Set</h2>
<p>Strange enough, a page does not have values for the following properties:
<code>metadataSetId</code>, <code>metadataSetPath</code>, <code>configurationSetId</code>,
and <code>configurationSetPath</code>. They all store <code>NULL</code>. To retrieve the metadata set and configuration set associated with a page, we must go through the content
type. To make methods, like <code>Page::getMetadataSetId</code>, <code>Page::getMetadataSetPath</code>, <code>Page::getConfigurationSetId</code>, and <code>Page::getConfigurationSetPath</code>, useful, instead of returning the <code>NULL</code> value, I go through the content type and retrieve the configuration set and metadata set. Therefore, the <code>Page::getMetadataSetId</code>, <code>Page::getMetadataSetPath</code>, <code>Page::getConfigurationSetId</code>, and <code>Page::getConfigurationSetPath</code> methods do return useful information.</p>
<h2>Structure of <code>page</code></h2>
<pre>SOAP:
page
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
  reviewOnSchedule (8.5)
  reviewEvery (8.5)
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
  expirationFolderRecycled
  shouldBePublished
  shouldBeIndexed
  lastPublishedDate
  lastPublishedBy
  configurationSetId
  configurationSetPath
  contentTypeId
  contentTypePath
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
  pageConfigurations
    pageConfiguration
      id
      name
      defaultConfiguration
      templateId
      templatePath
      formatId
      formatPath
      formatRecycled
      pageRegions
        pageRegion
        id
        name
        blockId
        blockPath
        blockRecycled
        noBlock
        formatId
        formatPath
        formatRecycled
        noFormat
      outputExtension
      serializationType
      includeXMLDeclaration
      publishable
  maintainAbsoluteLinks
  
REST:
page
  contentTypeId
  contentTypePath
  structuredData
    definitionId
    definitionPath
    structuredDataNodes (stdClass or array of stdClass)
      type
      identifier
      structuredDataNodes (stdClass or array of stdClass)
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
  pageConfigurations (array of stdClass)
      name
      defaultConfiguration
      templateId
      templatePath
      formatId
      formatPath
      formatRecycled
      pageRegions (array of stdClass)
        name
        blockId
        blockPath
        blockRecycled
        noBlock
        formatId
        formatPath
        formatRecycled
        noFormat
        id
      outputExtension
      serializationType
      includeXMLDeclaration
      publishable
      id
  maintainAbsoluteLinks
  shouldBePublished
  shouldBeIndexed
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled
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
  reviewOnSchedule (8.5)
  reviewEvery (8.5)
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
  xhtml
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "page" ),
        array( "getComplexTypeXMLByName" => "structured-data" ),
        array( "getComplexTypeXMLByName" => "structured-data-nodes" ),
        array( "getComplexTypeXMLByName" => "structured-data-node" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-type" ),
        array( "getSimpleTypeXMLByName"  => "structured-data-asset-type" ),
        array( "getComplexTypeXMLByName" => "pageConfigurations" ),
        array( "getComplexTypeXMLByName" => "page-configurations" ),
        array( "getComplexTypeXMLByName" => "pageConfiguration" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/page.php">page.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/page2.php">page2.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/tree/master/recipes/pages">Page Recipes</a></li>

</ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/page/9e19b89f8b7ffe8353cc17e9c1ab52bb

{
  "asset":
  {
    "page":
    {
      "contentTypeId":"61885ed98b7ffe8377b637e8eabc34b0",
      "contentTypePath":"_brisk:Page",
      "structuredData":
      {
        "structuredDataNodes":[
        {
          "type":"group",
          "identifier":"pre-main-group",
          "structuredDataNodes":[
          {
            "type":"asset",
            "identifier":"mul-pre-main-chooser",
            "assetType":"block",
            "recycled":false
          } ],
          "recycled":false
        },
        {
          "type":"group",
          "identifier":"main-group",
          "structuredDataNodes":[
          {
            "type":"asset",
            "identifier":"mul-pre-h1-chooser",
            "assetType":"block",
            "recycled":false
          },
          {
            "type":"text",
            "identifier":"h1",
            "text":"Formats",
            "recycled":false
          },
          {
            "type":"asset",
            "identifier":"mul-post-h1-chooser",
            "assetType":"block",
            "recycled":false
          },
          {
            "type":"text",
            "identifier":"float-pre-content-blocks-around-wysiwyg-content",
            "text":"::CONTENT-XML-CHECKBOX::",
            "recycled":false
          },
          {
            "type":"text",
            "identifier":"wysiwyg",
            "text":"\u003cp\u003eFormats, especially Velocity formats, play a central role in the Standard Model. The architecture of the multiple-design master site relies heavily on formats. Here I want to document the processes of building and using library code. I also provide tutorials and documentation on various Java packages.\u003c/p\u003e",
            "recycled":false
          },
          {
            "type":"asset",
            "identifier":"mul-post-wysiwyg-chooser",
            "assetType":"block",
            "recycled":false
          } ],
          "recycled":false
        },
        {
          "type":"group",
          "identifier":"post-main-group",
          "structuredDataNodes":[
          {
            "type":"asset",
            "identifier":"mul-post-main-chooser",
            "assetType":"block",
            "recycled":false
          } ],
          "recycled":false
        },
        {
          "type":"group",
          "identifier":"top-group",
          "structuredDataNodes":[
          {
            "type":"asset",
            "identifier":"mul-top-group-chooser",
            "assetType":"block",
            "recycled":false
          } ],
          "recycled":false
        },
        {
          "type":"group",
          "identifier":"bottom-group",
          "structuredDataNodes":[
          {
            "type":"asset",
            "identifier":"mul-bottom-group-chooser",
            "assetType":"block",
            "recycled":false
          } ],
          "recycled":false
        },
        {
          "type":"group",
          "identifier":"admin-group",
          "structuredDataNodes":[
          {
            "type":"asset",
            "identifier":"master-level-override",
            "assetType":"block",
            "recycled":false
          },
          {
            "type":"asset",
            "identifier":"page-level-override",
            "blockId":"be0493ed8b7ffe833b19adb83da1d76f",
        "blockPath":"_brisk:app/components/blocks/script/include-no-content-script",
            "assetType":"block",
            "recycled":false
          } ],
          "recycled":false
        } ]
      },
      "pageConfigurations":[
      {
        "name":"Page",
        "defaultConfiguration":true,
        "templateId":"618863fc8b7ffe8377b637e865012e5d",
        "templatePath":"_brisk:core/xml",
        "formatId":"618878dd8b7ffe8377b637e88e2153e9",
        "formatPath":"_brisk:core/page_template",
        "formatRecycled":false,
        "pageRegions":[
        {
          "name":"DEFAULT",
          "blockId":"61885d428b7ffe8377b637e8d0cc3dbe",
          "blockPath":"_brisk:core/calling-page",
          "blockRecycled":false,
          "noBlock":false,
          "formatId":"61886d138b7ffe8377b637e8b81d2135",
          "formatPath":"_brisk:core/startup",
          "formatRecycled":false,
          "noFormat":false,
          "id":"618862fe8b7ffe8377b637e8b8644e1f"
        } ],
        "includeXMLDeclaration":false,
        "publishable":false,
        "id":"9e19b8a08b7ffe8353cc17e92bd3d070"
      } ],
      "maintainAbsoluteLinks":false,
      "shouldBePublished":true,
      "shouldBeIndexed":true,
      "expirationFolderRecycled":false,
      "metadata":
      {
        "displayName":"Formats",
        "title":"Formats",
        "reviewDate":"Dec 28, 2017 8:51:00 AM",
        "dynamicFields":[
        {
          "name":"exclude-from-menu",
          "fieldValues":[]
        },
        {
          "name":"exclude-from-left-folder-nav",
          "fieldValues":[]
        },
        {
          "name":"exclude-from-mobile-menu",
          "fieldValues":[]
        },
        {
          "name":"tree-picker",
          "fieldValues":[
          {
            "value":"inherited"
          } ]
        } ]
      },
      "reviewOnSchedule":false,
      "reviewEvery":0,
      "parentFolderId":"c12d8d0d8b7ffe83129ed6d86dd9f853",
      "parentFolderPath":"/",
      "lastModifiedDate":"Jan 24, 2018 9:10:13 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Dec 28, 2017 12:09:33 PM",
      "createdBy":"wing",
      "path":"test2",
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "name":"test2",
      "id":"9e19b89f8b7ffe8353cc17e9c1ab52bb"
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
class PagePhantom extends Linkable
{
    const DEBUG = false;
    const DUMP  = false;
    const TYPE  = c\T::PAGE;

/**
<documentation><description><p>The constructor, overriding the parent method to process
page configurations and structured data.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->content_type = new ContentType( 
            $service, $service->createId( 
                ContentType::TYPE, 
                $this->getProperty()->contentTypeId ) );
        $this->page_configuration_set = $this->content_type->getPageConfigurationSet();
        parent::setPageContentType( $this->content_type );
            
        if( isset( $this->getProperty()->structuredData ) )
        {
            $this->data_definition_id = $this->content_type->getDataDefinitionId();
            $this->data_definition    = $this->content_type->getDataDefinition();

            // structuredDataNode could be empty for xml pages
            if( isset( $this->getProperty()->structuredData ) )
            {
                if( $this->getService()->isSoap() &&
                    isset( $this->getProperty()->
                    structuredData->structuredDataNodes->structuredDataNode ) )
                {
                    $this->processStructuredDataPhantom( $this->data_definition_id );
                }
                elseif( $this->getService()->isRest() )
                {
                    $this->processStructuredDataPhantom( $this->data_definition_id );
                }
            }
        }
        elseif( isset( $this->getProperty()->xhtml ) )
        {
            $this->xhtml = $this->getProperty()->xhtml;
        }
        
        if( $this->getService()->isSoap() )
            $this->processPageConfigurations(
                $this->getProperty()->pageConfigurations->pageConfiguration );
        elseif( $this->getService()->isRest() )
            $this->processPageConfigurations(
                $this->getProperty()->pageConfigurations );
    }

/**
<documentation><description><p>Appends a node to a multiple field, calls <code>edit</code>,
and returns the calling object. The identifier supplied must the the fully qualified
identifier of the first node in the set. The identifier of the first node is used because
a multiple field, when in use, always has a first node. Note that the new node is in fact
an exact copy of the last node (which can be the first node). Therefore, it contains all
the data contained in the node copied.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function appendSibling( string $identifier ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->appendSibling( $identifier );
        $this->edit();
        return $this;
    }

/**
<documentation><description><p>Creates exactly <code>$number</code> instances for the
multiple field whose first node is <code>$identifier</code> and returns the calling
object. This method ensures that a multiple field will have exactly N instances. If the
page has more or has less instances than <code>$number</code>, then instances are either
removed from or added to the field. This method calls <code>appendSibling</code> when new
nodes are needed.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
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
                S_SPAN . "The value $number is not a positive integer." . E_SPAN );
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
        $this->processStructuredDataPhantom( $this->data_definition_id );
        return $this;
    }
    
/**
<documentation><description><p>Displays <code>xml</code> of the data definition and returns the calling object.</p></description>
<example>$p->displayDataDefinition();</example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function displayDataDefinition() : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->getDataDefinition()->displayXML();
        return $this;
    }
    
/**
<documentation><description><p>Displays <code>xhtml</code> of the page, and returns
the calling object.</p></description>
<example></example>
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
method to allow for workflow information. When working with workflows, the
<code>WorkflowDefinition</code> object cannot be <code>NULL</code>. The
<code>p\Workflow</code> cannot be <code>NULL</code> if the page is edited to advance the
workflow. A non-empty comment is also required. When a non-<code>NULL</code>
<code>WorkflowDefinition</code> object is passed in, a <code>workflowConfiguration</code>
<code>stdClass</code> object will be created inside <code>edit</code>. If the <code>p\Workflow</code>
object is <code>NULL</code>, then the <code>$new_workflow_name</code> must be supplied.
If the <code>p\Workflow</code> object is not <code>NULL</code>, then its name will be used
instead. The last parameter <code>$exception</code>, when set to <code>false</code>, is
used to bypass the processing of the structured data. Note that this may lead to the
introduction of phantom nodes to a page. When the structured data is irrelevant, then set
this parameter to <code>false</code>. For example, when a page is edited so that it can be attached to a workflow definition that eventually unpublish and delete the page, the
processing of the structured data must be bypassed, because when the page is edited
successfully, it no longer exists and there will be no structured data to process.</p></description>
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
        $page  = $this->getProperty();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page->pageConfigurations ); }

        $page->metadata = $this->getMetadata()->toStdClass();
        
        if( isset( $this->structured_data ) )
        {
            $page->structuredData = $this->structured_data->toStdClass();
            $page->xhtml = NULL;
        }
        else
        {
            $page->structuredData = NULL;
            
            if( isset( $this->xhtml ) )
                $page->xhtml = $this->xhtml;
        }
        
        if( $this->getService()->isSoap() )
            $page->pageConfigurations->pageConfiguration = array();
        elseif( $this->getService()->isRest() )
            $page->pageConfigurations = array();
        
        foreach( $this->page_configurations as $config )
        {
            if( $this->getService()->isSoap() )
                $page->pageConfigurations->pageConfiguration[] = $config->toStdClass();
            elseif( $this->getService()->isRest() )
                $page->pageConfigurations[] = $config->toStdClass();
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page->pageConfigurations ); }
        
        if( isset( $wd ) )
        {
            $wf_config                       = new \stdClass();
            $wf_config->workflowDefinitionId = $wd->getId();
            $wf_config->workflowComments     = $comment;
            
            if( isset( $wf ) )
            {
                $wf_config->workflowName     = $wf->getName();
            }
            else
            {
                if( trim( $new_workflow_name ) == "" )
                    throw new e\EmptyValueException( c\M::EMPTY_WORKFLOW_NAME );
                    
                $wf_config->workflowName     = $new_workflow_name;
            }
            
            $asset->workflowConfiguration    = $wf_config;
        }
        
        //u\DebugUtility::dump( $page );
        
        $asset->{ $p = $this->getPropertyName() } = $page;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page ); }
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        
        if( $exception )
            $this->reloadProperty();
        
        if( isset( $this->data_definition_id ) && $exception )
            $this->processStructuredDataPhantom( $this->data_definition_id );
        return $this;
    }
    
/**
<documentation><description><p>Returns the type string of an asset node (an asset node is
an instance of an asset field of type <code>page</code>, <code>file</code>,
<code>block</code>, <code>symlink</code>, or <code>page,file,symlink</code>).</p></description>
<example>$asset_type = $p->getAssetNodeType( $node_name );</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getAssetNodeType( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getAssetNodeType( $identifier );
    }
    
/**
<documentation><description><p>Returns block attached to the node or <code>null</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception>WrongBlockTypeException</exception>
</documentation>
*/
    public function getBlock( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlock( $identifier );
    }
   
/**
<documentation><description><p>Returns an array storing information about blocks and
formats attached to regions of the named configuration at the page level.</p></description>
<example>$pc = $p->getPageConfigurationSet()->getPageConfiguration( "RWD" );
u\DebugUtility::dump( $p->getBlockFormatMap( $pc ) );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockFormatMap( p\PageConfiguration $configuration ) : array
    {
        $block_format_array  = array();
        $configuration_name  = $configuration->getName();
        $config_page_regions = $configuration->getPageRegions();
        $config_region_names = $configuration->getPageRegionNames();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $config_region_names ); }
        
        $page_level_config  = $this->page_configuration_map[ $configuration_name ];
        $page_level_regions = $page_level_config->getPageRegions();
        $page_region_names  = $page_level_config->getPageRegionNames();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page_region_names ); }
        
        $template = $this->getContentType()->getConfigurationSet()->
            getPageConfigurationTemplate( $configuration_name );
        $template_region_names = $template->getPageRegionNames();
        
        foreach( $page_region_names as $page_region_name )
        {
            // initialize id variables
            $block_id = NULL;
            $format_id = NULL;

            // for debugging
            if( self::DEBUG )
            {
                u\DebugUtility::out( $page_region_name );

                if( $template->hasPageRegion( $page_region_name ) )
                {
                    u\DebugUtility::out( "template block: " . 
                        $template->getPageRegion( $page_region_name )->getBlockId() );
                    u\DebugUtility::out( "template format: " . 
                        $template->getPageRegion( $page_region_name )->getFormatId() );
                }
            
                if( $configuration->hasPageRegion( $page_region_name ) )
                {
                    u\DebugUtility::out( "Config block: " . 
                        $configuration->getPageRegion( $page_region_name )->getBlockId() );
                    u\DebugUtility::out( "Config format: " . 
                        $configuration->getPageRegion( $page_region_name )->getFormatId() );
                }
                
                if( $page_level_config->hasPageRegion( $page_region_name ) )
                {
                    u\DebugUtility::out( "Page block: " . 
                        $page_level_config->getPageRegion( $page_region_name )->getBlockId() );
                    u\DebugUtility::out( "Page format: " . 
                        $page_level_config->getPageRegion( $page_region_name )->getFormatId() );
                } 
            }
            
            // template level
            if( $template->hasPageRegion( $page_region_name ) )
            {
                $template_block_id  = $template->
                    getPageRegion( $page_region_name )->getBlockId();
                $template_format_id = $template->
                    getPageRegion( $page_region_name )->getFormatId();
            }
            // config level
            if( $configuration->hasPageRegion( $page_region_name ) )
            {
                $config_block_id  = $configuration->
                    getPageRegion( $page_region_name )->getBlockId();
                $config_format_id = $configuration->
                    getPageRegion( $page_region_name )->getFormatId();
            }
            // page level
            else
            {
                $config_block_id  = NULL;
                $config_format_id = NULL;
            }
            
            if( $page_level_config->hasPageRegion( $page_region_name ) )
            {
                $page_block_id  = $page_level_config->
                    getPageRegion( $page_region_name )->getBlockId();
                $page_format_id = $page_level_config->
                    getPageRegion( $page_region_name )->getFormatId();
                $page_no_block  = $page_level_config->
                    getPageRegion( $page_region_name )->getNoBlock();
                $page_no_format = $page_level_config->
                    getPageRegion( $page_region_name )->getNoFormat();
            } 

            if( isset( $page_block_id ) )
            {
                $block_id = NULL;
                
                if( !isset( $config_block_id ) )
                {
                    if( $page_block_id != $template_block_id )
                    {
                        $block_id = $page_block_id;
                    }
                }
                else if( $config_block_id != $page_block_id )
                {
                    $block_id = $page_block_id;
                }
            }

            if( isset( $page_format_id ) )
            {
                $format_id = NULL;
                
                if( !isset( $config_format_id ) )
                {
                    if( $page_format_id != $template_format_id )
                    {
                        $format_id = $page_format_id;
                    }
                }
                else if( $config_format_id != $page_format_id )
                {
                    $format_id = $page_format_id;
                }
            }
            // store page-level block/format info
            if( isset( $block_id ) )
            {
                if( !isset( $block_format_array[ $page_region_name ] ) )
                {
                    $block_format_array[ $page_region_name ] = array();
                }
                
                $block_format_array[ $page_region_name ][ 'block' ] = $block_id;
            }
            
            if( isset( $format_id ) )
            {
                if( !isset( $block_format_array[ $page_region_name ] ) )
                {
                    $block_format_array[ $page_region_name ] = array();
                }
                
                $block_format_array[ $page_region_name ][ 'format' ] = $format_id;
            }
            
            if( $page_no_block )
            {
                $block_format_array[ $page_region_name ][ 'no-block' ] = true;
            }

            if( $page_no_format )
            {
                $block_format_array[ $page_region_name ]['no-format' ] = true;
            }
        }
        return $block_format_array;
    }
    
/**
<documentation><description><p>Returns <code>blockId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getBlockId( $node_name ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getBlockId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockId( $identifier );
    }
    
/**
<documentation><description><p>Returns <code>blockPath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getBlockPath( $node_name ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getBlockPath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockPath( $identifier );
    }
    
/**
<documentation><description><p>Returns the <code>PageConfigurationSet</code> object.</p></description>
<example>$p->getConfigurationSet()->display();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getConfigurationSet() : Asset
    {
        return $this->page_configuration_set;
    }
    
/**
<documentation><description><p>Returns the ID string of the configuration set.</p></description>
<example>echo $p->getConfigurationSetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getConfigurationSetId() : string
    {
        return $this->page_configuration_set->getId();
    }
    
/**
<documentation><description><p>Returns the path string of the configuration set.</p></description>
<example>echo $p->getConfigurationSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getConfigurationSetPath() : string
    {
        return $this->page_configuration_set->getPath();
    }
    
/**
<documentation><description><p>Returns the <code>ContentType</code> object.</p></description>
<example>$p->getContentType()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getContentType() : Asset
    {
        return $this->content_type;
    }

/**
<documentation><description><p>Returns <code>contentTypeId</code>.</p></description>
<example>echo $p->getContentTypeId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getContentTypeId() : string
    {
        return $this->content_type->getId();
    }
    
/**
<documentation><description><p>Returns <code>contentTypePath</code>.</p></description>
<example>echo $p->getContentTypePath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getContentTypePath() : string
    {
        return $this->content_type->getPath();
    }
    
/**
<documentation><description><p>Returns the associated <code>DataDefinition</code> object.</p></description>
<example>$p->getDataDefinition()->dump();</example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getDataDefinition() : Asset
    {
        $this->checkStructuredData();
        return $this->data_definition;
    }
    
/**
<documentation><description><p>Returns the ID string of the data definition.</p></description>
<example>echo $p->getDataDefinitionId(), BR;</example>
<return-type>string</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getDataDefinitionId() : string
    {
        $this->checkStructuredData();
        return $this->data_definition->getId();
    }
    
/**
<documentation><description><p>Returns the path string of the data definition.</p></description>
<example>echo $p->getDataDefinitionPath(), BR;</example>
<return-type>string</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getDataDefinitionPath() : string
    {
        $this->checkStructuredData();
        return $this->data_definition->getPath();
    }

/**
<documentation><description><p>Returns <code>fileId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getFileId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getFileId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFileId( $identifier );
    }
    
/**
<documentation><description><p>Returns <code>filePath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getFilePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getFilePath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFilePath( $identifier );
    }
    
/**
<documentation><description><p>Returns an array of fully qualified identifiers of all nodes.</p></description>
<example>u\DebugUtility::dump( $p->getIdentifiers() );</example>
<return-type>array</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getIdentifiers() : array
    {
        $this->checkStructuredData();
        return $this->structured_data->getIdentifiers();
    }
    
/**
<documentation><description><p>Returns <code>lastPublishedDate</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getLastPublishedDate() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedDate()
    {
        return $this->getProperty()->lastPublishedDate;
    }
    
/**
<documentation><description><p>Returns <code>lastPublishedBy</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getLastPublishedBy() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedBy()
    {
        return $this->getProperty()->lastPublishedBy;
    }
    
/**
<documentation><description><p>Returns the id of a <code>Linkable</code> node (a
<code>Linkable</code> node is a chooser allowing users to choose either a page, a file, or
a symlink; therefore, the id can be the <code>fileId</code>, <code>pageId</code>, or
<code>symlinkId</code> of the node).</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getLinkableId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
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
<example>echo u\StringUtility::getCoalescedString( $p->getLinkablePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getLinkablePath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkablePath( $identifier );
    }
    
/**
<documentation><description><p>Returns the associated <code>MetadataSet</code> object.</p></description>
<example>$page->getMetadataSet()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSet() : Asset
    {
        return $this->content_type->getMetadataSet();
    }

/**
<documentation><description><p>Returns <code>maintainAbsoluteLinks</code>.</p></description>
<example>echo u\StringUtility::boolToString( $p->getMaintainAbsoluteLinks() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getMaintainAbsoluteLinks() : bool
    {
        return $this->getProperty()->maintainAbsoluteLinks;
    }

/**
<documentation><description><p>Returns the type string of a node. The returned value is
one of the following: <code>group</code>, <code>asset</code>, and <code>text</code>.</p></description>
<example>echo $p->getNodeType( $id ), BR;</example>
<return-type>string</return-type>
<exception>WrongPageTypeException</exception>
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
<example></example>
<return-type>int</return-type>
<exception>WrongPageTypeException, EmptyValueException, NodeException</exception>
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
<documentation><description><p>Returns the <code>PageConfigurationSet</code> object.</p></description>
<example>$pc = $p->getPageConfigurationSet()->getPageConfiguration( "RWD" );
u\DebugUtility::dump( $p->getBlockFormatMap( $pc ) );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationSet() : Asset
    {
        // the page does not store page configuration set info
        return $this->page_configuration_set;
    }
    
/**
<documentation><description><p>Returns the ID of the configuration set. A page does not
store the ID of the configuration set. The information must be retrieved through the associated content type object.</p></description>
<example>echo $p->getConfigurationSetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationSetId() : string
    {
        return $this->page_configuration_set->getId();
    }
    
/**
<documentation><description><p>Returns the path of the configuration set. A page does not
store the path of the configuration set. The information must be retrieved through the associated content type object.</p></description>
<example>echo $p->getConfigurationSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationSetPath() : string
    {
        return $this->page_configuration_set->getPath();
    }
    
/**
<documentation><description><p>Returns <code>pageId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getPageId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getPageId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPageId( $identifier );
    }
    
/**
<documentation><description><p>Returns an array, mapping region names to string-id pairs.
This array gives the information about blocks and/or formats attached to page regions at
the page level. For example, if there is a page region named <code>SLIDESHOW</code> and if
at the page level, the region is associated with a block and a format, then in the
returned array there will be an entry like this:
<pre>["SLIDESHOW"]=&gt; array(2) { 
  ["block"]=&gt; string(32) "980c1af68b7f0856015997e4c3d37a21" 
  ["format"]=&gt; string(32) "470207d98b7f0856015997e487d78571" }
</pre>
Note that this method only retrieves information of the default page configuration and
ignores all other page configurations and is used mainly by <code>Page::setContentType</code>.</p></description>
<example>u\DebugUtility::dump( $p->getPageLevelRegionBlockFormat() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getPageLevelRegionBlockFormat() : array
    {
        $configuration = $this->page_configuration_set->
            getDefaultConfiguration();
        return $this->getBlockFormatMap( $configuration );
    }
    
/**
<documentation><description><p>Returns <code>pagePath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getPagePath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getPagePath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPagePath( $identifier );
    }
    
/**
<documentation><description><p>Returns the named <code>p\PageRegion</code> object of the named page configuration.</p></description>
<example>u\DebugUtility::dump( $p->getPageRegion( "RWD", "DEFAULT" )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function getPageRegion( string $config_name, string $region_name ) : p\Property
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
        
        return $this->page_configuration_map[ $config_name ]->getPageRegion( $region_name );
    }
    
/**
<documentation><description><p>Returns an array of <code>p\PageRegion</code> objects of the named page configuration.</p></description>
<example>u\DebugUtility::dump( $p->getPageRegions( "RWD" ) );</example>
<return-type>array</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function getPageRegions( string $config_name ) : array
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
        
        return $this->page_configuration_map[ $config_name ]->getPageRegions();
    }
    
/**
<documentation><description><p>Returns an array of region names of the named page configuration.</p></description>
<example>u\DebugUtility::dump( $p->getPageRegionNames( "RWD" ) );</example>
<return-type>array</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function getPageRegionNames( string $config_name ) : array
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
        
        return $this->page_configuration_map[ $config_name ]->getPageRegionNames();
    }
    
/**
<documentation><description><p>Returns the parent folder (a <a href="http://www.upstate.edu/web-services/api/asset-classes/folder.php"><code>Folder</code></a> object).</p></description>
<example>$p->getParentFolder()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolder() : Asset
    {
        return $this->getAsset(
            $this->getService(), Folder::TYPE, $this->getParentFolderId() );
    }

/**
<documentation><description><p>Returns <code>parentFolderId</code>.</p></description>
<example>echo $p->getParentFolderId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolderId() : string
    {
        return $this->getProperty()->parentFolderId;
    }

/**
<documentation><description><p>Returns <code>parentFolderPath</code>.</p></description>
<example>echo $p->getParentFolderPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolderPath() : string
    {
        return $this->getProperty()->parentFolderPath;
    }
    
/**
<documentation><description><p>Returns an array of strings (all possible values of a node like a dropdown or checkboxes) or <code>NULL</code>.</p></description>
<example>if( $p->hasPossibleValues( $id ) )
    u\DebugUtility::dump( $p->getPossibleValues( $id ) );
</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getPossibleValues( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPossibleValues( $identifier );
    }

/**
<documentation><description><p>Returns <code>shouldBeIndexed</code>.</p></description>
<example>echo u\StringUtility::boolToString( $p->getShouldBeIndexed() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBeIndexed() : bool
    {
        return $this->getProperty()->shouldBeIndexed;
    }
    
/**
<documentation><description><p>Returns <code>shouldBePublished</code>.</p></description>
<example>echo u\StringUtility::boolToString( $p->getShouldBePublished() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBePublished() : bool
    {
        return $this->getProperty()->shouldBePublished;
    }
    
/**
<documentation><description><p>Returns the <a href="http://www.upstate.edu/web-services/api/property-classes/structured-data.php"><code>p\StructuredData</code></a> object.</p></description>
<example>u\DebugUtility::dump( $p->getStructuredData()->toStdClass() );</example>
<return-type>StructuredData</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getStructuredDataPhantom() : p\StructuredDataPhantom
    {
        $this->checkStructuredData();
        return $this->structured_data;
    }
    
/**
<documentation><description><p>Returns <code>symlinkId</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getSymlinkId( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getSymlinkId( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkId( $identifier );
    }
    
/**
<documentation><description><p>Returns <code>symlinkPath</code> of the node.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getSymlinkPath( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getSymlinkPath( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkPath( $identifier );
    }
    
/**
<documentation><description><p>Returns the text of a node.</p></description>
<example>if( $p->isText( $id ) )
    echo $p->getText( $id ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getText( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getText( $identifier );
    }
    
/**
<documentation><description><p>Returns the type string of a text node. A text node is an
instance of a normal text field (including a text box, a multi-line and a WYSIWYG, all
three being associated with <code>NULL</code>), or a text field of type
<code>datetime</code>, <code>calendar</code>, <code>multi-selector</code>,
<code>dropdown</code>, or <code>checkbox</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $p->getTextNodeType( $id ) ), BR;</example>
<return-type>mixed</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function getTextNodeType( string $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getTextNodeType( $identifier );
    }

/**
<documentation><description><p>Returns the <a href="http://www.upstate.edu/web-services/api/property-classes/workflow.php"><code>p\Workflow</code></a> object currently associated with the page, or <code>NULL</code>.</p></description>
<example>u\DebugUtility::dump( $p->getWorkflow() );</example>
<return-type>mixed</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function getWorkflow()
    {
        $service = $this->getService();
        $service->readWorkflowInformation(
            $service->createId( self::TYPE, $this->getProperty()->id ) );
        
        if( $service->isSuccessful() )
        {
            if( isset( $service->getReply()->readWorkflowInformationReturn->workflow ) )
                return new p\Workflow(
                    $service->getReply()->readWorkflowInformationReturn->
                    workflow, $service );
            else
                return NULL; // no workflow
        }
        else
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::READ_WORKFLOW_FAILURE . E_SPAN );
        }
    }
    
/**
<documentation><description><p>Returns <code>xhtml</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getXhtml()
    {
        return $this->getProperty()->xhtml;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named configuration exists.</p></description>
<example>echo u\StringUtility::boolToString( $p->hasConfiguration( "RWD" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasConfiguration( string $config_name ) : bool
    {
        return isset( $this->page_configuration_map[ $config_name ] );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the node bearing that identifier exists.</p></description>
<example>if( $p->hasNode( $node_name ) )
{
    echo "The value is: " . $p->getText( $node_name ) . BR;
}</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function hasIdentifier( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->hasNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>hasIdentifier</code>.</p></description>
<example>echo u\StringUtility::boolToString( $p->hasNode( "left-column" ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function hasNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->hasNode( $identifier );
    }
    
/**
<documentation><description><p>An alias of <code>hasConfiguration</code>.</p></description>
<example>echo u\StringUtility::boolToString( $p->hasPageConfiguration( "RWD" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPageConfiguration( string $config_name ) : bool
    {
        return $this->hasConfiguration( $config_name );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named region exists in the named page configuration.</p></description>
<example>echo u\StringUtility::boolToString( $p->hasPageRegion( "XML", "DEFAULT" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPageRegion( string $config_name, string $region_name ) : bool
    {
        return $this->hasConfiguration( $config_name ) &&
            $this->page_configuration_map[ $config_name ]->
            hasPageRegion( $region_name );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether there are phantom nodes of type B in the page.</p></description>
<example>echo u\StringUtility::boolToString(
    $cascade->getAsset(
        a\Page::TYPE, "1e64191a8b7f08ee4bf6727368416cbe" )->hasPhantomNodes() );</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function hasPhantomNodes() : bool // detects phantom nodes of type B
    {
        $this->checkStructuredData();
        return $this->structured_data->hasPhantomNodes();
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether <code>structuredData</code> is defined (having nodes). Note that this method returns <code>false</code> when the page contains absolutely no data, even if it is associated with a data definition.</p></description>
<example></example>
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
<exception></exception>
</documentation>
*/
    public function isAsset( string $identifier ) : bool
    {
        return $this->isAssetNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is an
asset node, allowing users to choose an asset.</p></description>
<example>if( $p->hasIdentifier( $id ) &amp;&amp; $p->isAssetNode( $id ) )
    echo $p->getAssetNodeType( $id ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isBlockChooser( string $identifier ) : bool
    {
        return $this->isBlockChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
block chooser node, allowing users to choose a block.</p></description>
<example>if( $p->isBlockChooserNode( $id ) )
{
    echo u\StringUtility::getCoalescedString( $p->getBlockId( $id ) ), BR;
    echo u\StringUtility::getCoalescedString( $p->getBlockPath( $id ) ), BR;
}</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isCalendar( string $identifier ) : bool
    {
        return $this->isCalendarNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
calendar node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isCalendarNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isCheckbox( string $identifier ) : bool
    {
        return $this->isCheckboxNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a checkbox node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isCheckboxNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isDatetime( string $identifier ) : bool
    {
        return $this->isDatetimeNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a datetime node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isDatetimeNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isDropdown( string $identifier ) : bool
    {
        return $this->isDropdownNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a dropdown node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isDropdownNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isFileChooser( string $identifier ) : bool
    {
        return $this->isFileChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a file chooser node, allowing users to choose a file.</p></description>
<example>echo u\StringUtility::boolToString( $p->isFileChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isGroup( string $identifier ) : bool
    {
        return $this->isGroupNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a group node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isGroupNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isLinkableChooser( string $identifier ) : bool
    {
        return $this->isLinkableChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a
linkable chooser node, allowing users to choose a file, a page, or a symlink.</p></description>
<example>echo u\StringUtility::boolToString( $p->isLinkableChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isMultiLine( string $identifier ) : bool
    {
        return $this->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a multi-line node (i.e., a textarea).</p></description>
<example>echo u\StringUtility::boolToString( $p->isMultiLineNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function isMultiple( string $identifier ) : bool
    {
        return $this->isMultipleNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field name is associated with a multiple field. Note that the field name should be a fully qualified identifier of the associated data definition.</p></description>
<example>echo u\StringUtility::boolToString( $p->isMultipleField( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function isMultipleField( string $field_name ) : bool
    {
        $this->checkStructuredData();
        return $this->getDataDefinition()->isMultiple( $field_name );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a multiple node (an instance of a multiple field).</p></description>
<example>echo u\StringUtility::boolToString( $p->isMultipleNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isMultiSelector( string $identifier ) : bool
    {
        return $this->isMultiSelectorNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a multi-selector node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isMultiSelectorNode( $id ) ), BR;</example><return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isPageChooser( string $identifier ) : bool
    {
        return $this->isPageChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a page
chooser node, allowing users to choose a page.</p></description>
<example>echo u\StringUtility::boolToString( $p->isPageChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function isPageChooserNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isPageChooser( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the page is publishable. When the method returns <code>true</code>, it means that the parent folder of the page is publishable, and the page has a <code>true</code> value in <code>shouldBePublished</code>.</p></description>
<example>echo u\StringUtility::boolToString( $p->isPublishable() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isPublishable() : bool
    {
        $parent = $this->getAsset( 
            $this->getService(), Folder::TYPE, $this->getParentContainerId() );
        return $parent->isPublishable() && $this->getShouldBePublished();
    }
    
/**
<documentation><description><p>An alias of <code>isRadioNode</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isRadio( string $identifier ) : bool
    {
        return $this->isRadioNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a radio node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isRadioNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function isRadioNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isRadioNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field value is required by the named field.</p></description>
<example>echo u\StringUtility::boolToString( $p->isRequired( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isSymlinkChooser( string $identifier ) : bool
    {
        return $this->isSymlinkChooserNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a symlink chooser node, allowing users to choose a symlink.</p></description>
<example>echo u\StringUtility::boolToString( $p->isSymlinkChooserNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isTextBox( string $identifier ) : bool
    {
        return $this->isTextBoxNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a simple text box node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isTextBoxNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isText( string $identifier ) : bool
    {
        return $this->isTextNode( $identifier );
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
<example>if( $p->isTextNode( $id ) )
    echo $p->getText( $id ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
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
<exception></exception>
</documentation>
*/
    public function isWYSIWYG( string $identifier ) : bool
    {
        return $this->isWYSIWYGNode( $identifier );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named node is a WYSIWYG node.</p></description>
<example>echo u\StringUtility::boolToString( $p->isWYSIWYGNode( $id ) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function isWYSIWYGNode( string $identifier ) : bool
    {
        $this->checkStructuredData();
        return $this->structured_data->isWYSIWYGNode( $identifier );
    }
    
/**
<documentation><description><p>Removes all phantom nodes of type B, and returns the calling object.</p></description>
<example>$cascade->getAsset( a\Page::TYPE, "1e64131c8b7f08ee4bf67273f4e23681" )->mapData()->dump( true );</example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function mapData() : Asset
    {
        $this->checkStructuredData();
        $new_sd = $this->structured_data->mapData();
        return $this->setStructuredData( $new_sd );
    }
    
/**
<documentation><description><p>Publishes the page, and returns the calling
object.</p></description>
<example>$p->publish();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL ) : Asset
    {
        if( isset( $destination ) )
        {
            $destination_std       = new \stdClass();
            $destination_std->id   = $destination->getId();
            $destination_std->type = $destination->getType();
        }
        
        if( $this->getProperty()->shouldBePublished )
        {
            $service = $this->getService();
            
            if( isset( $destination ) )
                $service->publish( 
                    $service->createId(
                        $this->getType(), $this->getId() ), $destination_std );
            else
                $service->publish( 
                    $service->createId( $this->getType(), $this->getId() ) );
        }
        return $this;
    }

/**
<documentation><description><p>Removes the last node from a set of multiple nodes, calls
<code>edit</code>, and returns the calling object. The identifier supplied must the fully
qualified identifier of the first node of the set.</p></description>
<example>$p->removeLastSibling( $id );</example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
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
<documentation><description><p>Replaces the pattern with the replacement string for normal
text fields, and fields of type datetime and calendar, and returns the calling object.
Inside the method <code>preg_replace</code> is called. If an array of fully qualified
identifiers is also passed in, then only those nodes will be affected.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
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
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
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
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function replaceXhtmlByPattern(  string $pattern, string $replace ) : Asset
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_XHTML_PAGE . E_SPAN );
        }
        
        $this->xhtml = preg_replace( $pattern, $replace, $this->xhtml );
        
        return $this;
    }
    
/**
<documentation><description><p>Searches all text nodes, and returns an array of fully qualified identifiers of nodes where the string is found.</p></description>
<example></example>
<return-type>array</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function searchText( string $string ) : array
    {
        $this->checkStructuredData();
        return $this->structured_data->searchText( $string );
    }
    
/**
<documentation><description><p>Searches all text nodes, and returns an array of fully qualified identifiers of nodes where the pattern is found.</p></description>
<example></example>
<return-type>array</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function searchTextByPattern( string $pattern ) : array
    {
        $this->checkStructuredData();
        return $this->structured_data->searchTextByPattern( $pattern );
    }
    
/**
<documentation><description><p>Searches all WYSIWYG nodes, and returns an array of fully qualified identifiers of nodes where the pattern is found.</p></description>
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
<documentation><description><p>Returns a bool, indicating whether the string is found in the xhtml page.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function searchXhtml( string $string ) : bool
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_XHTML_PAGE . E_SPAN );
        }

        return strpos( $this->xhtml, $string ) !== false;
    }
    
/**
<documentation><description><p>Sets the node's <code>blockId</code> and <code>blockPath</code> properties, and returns the callling object.</p></description>
<example>$p->setBlock( $node_name, $text_block )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function setBlock( string $identifier, Block $block=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setBlock( $identifier, $block );
        return $this;
    }
    
/**
<documentation><description><p>Sets the content type for the page, calls <code>edit</code>,
and returns the calling object. Note that this method only takes care of blocks and
formats attached to regions at the page level of the default configuration. When no flag
is passed in for <code>$exception</code>, the default value <code>true</code> is passed
in. This should be the case if the old content type and new content type both are
associated with the same data definition. If the new content type is associated with a
different data definition, then a <code>false</code> should be passed in. In this case,
after the content type has been set, the structured data should be dealt with properly to
maintain consistency. Normally this means the method <code>Page::setStructuredData</code>
should be called as well.</p></description>
<example>$page->setContentType( $ct );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setContentType( ContentType $c, bool $exception=true ) : Asset
    {
        // nothing to do if already set
        if( $c->getId() == $this->getContentType()->getId() )
        {
            echo "Nothing to do" . BR;
            return $this;
        }
    
        // part 1: get the page level blocks and formats
        $block_format_array = $this->getPageLevelRegionBlockFormat();
        
        // just the default config, other config can be added
        $default_configuration       = $this->getContentType()->
            getConfigurationSet()->getDefaultConfiguration();
        $default_configuration_name  = $default_configuration->getName();
        $default_config_page_regions = 
            $default_configuration->getPageRegions();
        $default_region_names        = 
            $default_configuration->getPageRegionNames();
        
        $page_level_config  = 
            $this->page_configuration_map[ $default_configuration_name ];
        $page_level_regions = $page_level_config->getPageRegions();
        $page_region_names  = $page_level_config->getPageRegionNames();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $block_format_array ); }
        
        // part 2: switch content type
        if( $c == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET . E_SPAN );

        $page = $this->getProperty();
        $page->contentTypeId      = $c->getId();
        $page->contentTypePath    = $c->getPath();
        
        $configuration_array = array();
        $new_configurations = $c->getPageConfigurationSet()->
            getPageConfigurations();
        
        foreach( $new_configurations as $new_configuration )
        {
            $configuration_array[] = $new_configuration->toStdClass();
        }
        
        $page->pageConfigurations->pageConfiguration = $configuration_array;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page->pageConfigurations ); }
        
        $asset = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $page;
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );        
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . ": " . 
                $this->getName() . " " . $service->getMessage() );
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $this->getProperty()->pageConfigurations ); }
        
        $this->reloadProperty();
        $this->processPageConfigurations( 
            $this->getProperty()->pageConfigurations->pageConfiguration );
        
        $this->content_type = $c;
        parent::setPageContentType( $this->content_type );
        
            
        if( isset( $this->getProperty()->structuredData ) )
        {
            $this->data_definition_id = $this->content_type->getDataDefinitionId();
            

            // structuredDataNode could be empty for xml pages
            if( isset( $this->getProperty()->structuredData )  &&
                isset( $this->getProperty()->structuredData->structuredDataNodes ) &&
                isset( $this->getProperty()->structuredData->structuredDataNodes->
                    structuredDataNode ) 
            )
            {
                if( $exception ) // defaulted to true
                    $this->processStructuredDataPhantom( $this->data_definition_id );
            }
        }
        else
        {
            $this->xhtml = $this->getProperty()->xhtml;
        }

        // part 3: plug the blocks and formats back in
        $count = count( array_keys( $block_format_array) );
        
        if( $count > 0 )
        {
            $service = $this->getService();
            $page_level_config  = 
                $this->page_configuration_map[ $default_configuration_name ];
            $page_region_names  = $page_level_config->getPageRegionNames();
            
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page_region_names ); }
            
            foreach( $block_format_array as $region => $block_format )
            {
                // only if the region exists in the current config
                if( in_array( $region, $page_region_names ) )
                {
                    if( isset( $block_format[ 'block' ] ) )
                    {
                        $block_id = $block_format[ 'block' ];
                    }
                    if( isset( $block_format[ 'format' ] ) )
                    {
                        $format_id = $block_format[ 'format' ];
                    }
                
                    if( isset( $block_id ) )
                    {
                        $block = $this->getAsset( 
                            $service, $service->getType( $block_id ), $block_id );
                        $this->setRegionBlock( 
                            $default_configuration_name, $region, $block );
                    }
                    else if( isset( $block_format[ 'no-block' ] ) )
                    {
                        $this->setRegionNoBlock( 
                            $default_configuration_name, $region, true );
                    }
                
                    if( isset( $format_id ) )
                    {
                        $format = $this->getAsset( 
                            $service, $service->getType( $format_id ), $format_id );
                        $this->setRegionFormat( 
                            $default_configuration_name, $region, $format );
                    }
                    else if( isset( $block_format[ 'no-format' ] ) )
                    {
                        $this->setRegionNoFormat( 
                            $default_configuration_name, $region, true );
                    }
                }
            }
            
            if( $exception )
                $this->edit();
            else
                $this->editWithoutException();
        }
        
        if( self::DEBUG && self::DUMP ) { $page  = $this->getProperty(); u\DebugUtility::dump( $page->pageConfigurations ); }

        return $this;
    }

/**
<documentation><description><p>Sets the node's <code>fileId</code> and <code>filePath</code> properties, and returns the calling object.</p></description>
<example>$p->setFile( $node_name, $f )->edit();</example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
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
<example>$p->setLinkable( $node_name, $f )->edit();</example>
<return-type></return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function setLinkable( string $identifier, Linkable $linkable=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setLinkable( $identifier, $linkable );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>maintainAbsoluteLinks</code> and returns the
calling object.</p></description>
<example>$p->setMaintainAbsoluteLinks( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setMaintainAbsoluteLinks( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean" . E_SPAN );
        
        $this->getProperty()->maintainAbsoluteLinks = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>metadata</code> property, and returns the
calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
</documentation>
*/
/*
    public function setMetadata( p\Metadata $m ) : Asset
    {
        $page = $this->getProperty();
        $page->metadata = $m->toStdClass();
        
        $asset = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $page;
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
*/   
/**
<documentation><description><p>Sets the node's <code>pageId</code> and
<code>pathPath</code> properties, and returns the calling object.</p></description>
<example>$p->setPage( $node_name, $other_page )->edit();</example>
<return-type></return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function setPage( string $identifier, Page $page=NULL ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setPage( $identifier, $page );
        return $this;
    }
    
/**
<documentation><description><p>Attaches the named block to the named region of the named
page configuration, and returns the calling object. If <code>NULL</code> is passed in for
the block, then any existing page-level block will be detached.</p></description>
<example>$p->setRegionBlock( 'Desktop', "TOP GRAPHICS", NULL )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setRegionBlock(
        string $config_name, string $region_name, Block $block=NULL ) : Asset
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException(
                S_SPAN . "Path: " . $this->getPath() . E_SPAN . BR .
                "The page configuration $config_name does not exist." 
            );
        }
    
        if( self::DEBUG )
        {
            u\DebugUtility::out( "Setting block to region" . BR . "Region name: " . $region_name );
            if( isset( $block ) )
                u\DebugUtility::out( "Block ID: " . $block->getId() );
            else
                u\DebugUtility::out( "No block passed in." );
        }
        
        $this->page_configuration_map[ $config_name ]->setRegionBlock(
            $region_name, $block );
        
        return $this;
    }
    
/**
<documentation><description><p>Attaches the named format to the named region of the named
page configuration, and returns the calling object. If <code>NULL</code> is passed in for the format, then any existing page-level format will be detached.</p></description>
<example>$p->setRegionFormat( 'Desktop', "TOP GRAPHICS", $f )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setRegionFormat(
        string $config_name, string $region_name, Format $format=NULL ) : Asset
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        $this->page_configuration_map[ $config_name ]->setRegionFormat(
            $region_name, $format );
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>noBlock</code> of the named region of the named
page configuration to the supplied value, and returns the calling object.</p></description>
<example>$p->setRegionNoBlock( 'Desktop', "TOP GRAPHICS", true )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setRegionNoBlock(
        string $config_name, string $region_name, bool $no_block ) : Asset
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        $this->page_configuration_map[ $config_name ]->setRegionNoBlock(
            $region_name, $no_block );
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>noFormat</code> of the named region of the named
page configuration to the supplied value, and returns the calling object.</p></description>
<example>$p->setRegionNoFormat( 'Desktop', "TOP GRAPHICS", true )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setRegionNoFormat(
        string $config_name, string $region_name, bool $no_format ) : Asset
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        $this->page_configuration_map[ $config_name ]->setRegionNoFormat( $region_name, $no_format );
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>shouldBeIndexed</code> to the supplied value,
and returns the calling object.</p></description>
<example>$p->setShouldBeIndexed( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setShouldBeIndexed( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean" . E_SPAN );
            
        $this->getProperty()->shouldBeIndexed = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>shouldBePublished</code> to the supplied value,
and returns the calling object.</p></description>
<example>$p->setShouldBePublished( true )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setShouldBePublished( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean" . E_SPAN );
            
        $this->getProperty()->shouldBePublished = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>structuredData</code>, calls <code>edit</code>,
and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setStructuredData( p\StructuredData $structured_data ) : Asset
    {
        $this->structured_data = $structured_data;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $structured_data ); }
        
        $this->edit();
        $dd_id = $this->getDataDefinition()->getId();
        //$this->processStructuredDataPhantom( $dd_id );
        return $this;
    }

/**
<documentation><description><p>Sets the node's <code>symlinkId</code> and <code>symlinkPath</code> properties, and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
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
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function setText( string $identifier, string $text ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->setText( $identifier, $text );
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>xhtml</code> property of an xhtml page, and
returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
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
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_XHTML_PAGE . E_SPAN );
        }
        return $this;
    }
    
/**
<documentation><description><p>Swaps the data of two nodes, calls <code>edit</code>, and
returns the calling object. Since this method call can be chained, and all the fully
qualified identifiers must be recalculated after each swap, this method has to call
<code>edit</code> so that the change takes effect immediately.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongPageTypeException</exception>
</documentation>
*/
    public function swapData( string $identifier1, string $identifier2 ) : Asset
    {
        $this->checkStructuredData();
        $this->structured_data->swapData( $identifier1, $identifier2 );
        $this->edit()->processStructuredDataPhantom( $this->data_definition_id );

        return $this;
    }
    
/**
<documentation><description><p>Unpublishes the page and returns the calling
object.</p></description>
<example>$page->unpublish();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function unpublish() : Asset
    {
        $this->getService()->unpublish( $this->getIdentifier() );
        return $this;
    }
    
    private function checkStructuredData()
    {
        if( !$this->hasStructuredData() )
        {
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_DATA_DEFINITION_PAGE . " " . $this->getId() . E_SPAN );
        }
    }
    
    // to bypass processStructuredData
    private function editWithoutException()
    {
        return $this->edit( NULL, NULL, "", "", false );
    }

    private function processPageConfigurations( $page_config_std )
    {
        $this->page_configurations = array();
        
        if( !is_array( $page_config_std ) )
        {
            $page_config_std = array( $page_config_std );
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page_config_std ); }
        
        foreach( $page_config_std as $pc_std )
        {
            $pc = new p\PageConfiguration( $pc_std, $this->getService(), self::TYPE );
            $this->page_configurations[] = $pc;
            $this->page_configuration_map[ $pc->getName() ] = $pc;
        }
    }

    private function processStructuredDataPhantom( $data_definition_id )
    {
        $this->structured_data = new p\StructuredDataPhantom( 
            $this->getProperty()->structuredData, 
            $this->getService(),
            $data_definition_id,
            $this
        );
    }

    private $structured_data;
    private $page_configurations; // an array of objects
    private $page_configuration_map;
    private $data_definition_id;
    private $content_type;
    private $page_configuration_set;
    private $data_definition;
}