<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/22/2018 Class created.
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
<p>A <code>File</code> object represents a file asset. The <code>File</code> class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/linkable.php\"><code>Linkable</code></a>.</p>
<h2>Structure of <code>file</code></h2>
<pre>SOAP:
file
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
      dynamicField (an stdClass or array of stdClass)
        name
        fieldValues (NULL, stdClass or array of stdClass)
          fieldValue
            value
  metadataSetId
  metadataSetPath
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled (bool)
  shouldBePublished (bool)
  shouldBeIndexed (bool)
  lastPublishedDate
  lastPublishedBy
  text
  data
  rewriteLinks (bool)
  maintainAbsoluteLinks (bool)
  
REST:
file
  text (NULL)
  data
  rewriteLinks
  maintainAbsoluteLinks
  shouldBePublished
  shouldBeIndexed
  lastPublishedDate
  lastPublishedBy
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
    dynamicFields (array of stdClass)
      name
      fieldValues (array of stdClass)
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
<h2>Design Issues</h2>
<ul>
<li>The <code>setData</code> method accepts any data, binary or textual.</li>
<li>The <code>setText</code> method accepts only textual data.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "file" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/file.php">file.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/file/0fbcfbcb8b7ffe8343b94c28fe1f07e7

{
  "asset":{
    "file":{
      "text":"@media only print {\r\n\r\n@page{size: landscape}\r\n\r\nbody{
width:100%}",
      "data":[...],
      "rewriteLinks":false,
      "maintainAbsoluteLinks":false,
      "shouldBePublished":true,
      "shouldBeIndexed":true,
      "expirationFolderRecycled":false,
      "metadataSetId":"0fa6f6f08b7ffe8343b94c28a0eaa566",
      "metadataSetPath":"Default",
      "metadata":{},
      "reviewOnSchedule":false,
      "reviewEvery":0,
      "parentFolderId":"0fb201648b7ffe8343b94c28dc10ad7c",
      "parentFolderPath":"_extra",
      "lastModifiedDate":"Jan 23, 2018 9:42:13 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Jan 19, 2018 1:44:58 PM",
      "createdBy":"wing",
      "path":"_extra/style.css",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"style.css",
      "id":"0fbcfbcb8b7ffe8343b94c28fe1f07e7"
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
/*
5/22/2018
Since there is a bug in SOAP, there is not much I can do right now.
When the bug is fixed, look at Site and ContentType.

configuration:
{
"allowStyle":true,
"menuItems":["edit","format","insert","table","view","tools"],
"buttons":["|","undo","redo","|","bold","italic","underline","|","alignleft","aligncenter","alignright","alignjustify","|","styleselect","fontselect","|","fontsizeselect","|","forecolor","backcolor","|","bullist","numlist","outdent","indent","|","link","unlink","anchor","|","image","media","spectate","|","code","fullscreen","|","cut","copy","paste","pastetext","selectall","|","strikethrough","superscript","subscript","removeformat","|","charmap","hr","insertdatetime","|","visualaid","visualblocks","|","spellchecker"],
"customStyles":""
}
*/
class EditorConfiguration extends Asset
{
    const DEBUG = false;
    const TYPE  = c\T::EDITOR_CONFIGURATION;

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
<documentation><description><p>Edits and returns the calling object, overriding the parent
method to allow for workflow information. When working with workflows, the <code>WorkflowDefinition</code>
object cannot be <code>NULL</code>. The <code>p\Workflow</code> cannot be <code>NULL</code> if the file
is edited to advance the workflow. A non-empty comment is also required. When a
non-<code>NULL</code> <code>WorkflowDefinition</code> object is passed in, a <code>workflowConfiguration stdClass</code>
object will be created inside <code>edit</code>. If the <code>p\Workflow</code> object is
<code>NULL</code>, then the <code>$new_workflow_name</code> must be supplied. If the
<code>p\Workflow</code> object is not <code>NULL</code>, then its name will be used instead.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EmptyValueException, EditingFailureException</exception>
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

        // patch for 8.9
        if( isset( $this->getProperty()->reviewEvery ) )
        {
            $review_every = (int)$this->getProperty()->reviewEvery;
        
            if( $review_every != 0 && $review_every != 30 && $review_every != 00 && 
                $review_every != 180 && $review_every != 365 )
            {
                $this->getProperty()->reviewEvery = 0;
            }
        }

        $this->getProperty()->metadata  = $this->getMetadata()->toStdClass();
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
<documentation><description><p>Returns <code>data</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getData()
    {
        if( isset( $this->getProperty()->data ) )
            return $this->getProperty()->data;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>lastPublishedBy</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedBy()
    {
        if( isset( $this->getProperty()->lastPublishedBy ) )
            return $this->getProperty()->lastPublishedBy;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>lastPublishedDate</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedDate()
    {
        if( isset( $this->getProperty()->lastPublishedDate ) )
            return $this->getProperty()->lastPublishedDate;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>maintainAbsoluteLinks</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getMaintainAbsoluteLinks() : bool
    {
        return $this->getProperty()->maintainAbsoluteLinks;
    }
    
/**
<documentation><description><p>Returns <code>rewriteLinks</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getRewriteLinks() : bool
    {
        return $this->getProperty()->rewriteLinks;
    }
    
/**
<documentation><description><p>Returns <code>shouldBeIndexed</code>.</p></description>
<example></example>
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
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBePublished() : bool
    {
        return $this->getProperty()->shouldBePublished;
    }
    
/**
<documentation><description><p>Returns <code>text</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getText()
    {
        if( isset( $this->getProperty()->text ) )
            return $this->getProperty()->text;
        return NULL;
    }
    
/**
<documentation><description><p>Returns a <code>p\Workflow</code> object of <code>NULL</code>.</p></description>
<example></example>
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
                    $service->getReply()->
                        readWorkflowInformationReturn->workflow, $service );
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
<documentation><description><p>Returns a bool, indicating whether the file is publishable.</p></description>
<example></example>
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
<documentation><description><p>Publishes the file and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL ) : Asset
    {
        if( isset( $destination ) )
        {
            $destination_std           = new \stdClass();
            $destination_std->id       = $destination->getId();
            $destination_std->type     = $destination->getType();
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
<documentation><description><p>Sets <code>data</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setData( $data ) : Asset
    {
        $this->getProperty()->data = $data;
        return $this;
    }
       
/**
<documentation><description><p>Sets <code>maintainAbsoluteLinks</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setMaintainAbsoluteLinks( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        
        $this->getProperty()->maintainAbsoluteLinks = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>rewriteLinks</code> and returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setRewriteLinks( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        
        $this->getProperty()->rewriteLinks = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>shouldBeIndexed</code> returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setShouldBeIndexed( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->shouldBeIndexed = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>shouldBePublished</code> returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setShouldBePublished( bool $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->shouldBePublished = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>text</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setText( $text ) : Asset
    {
        if( !is_null( $text ) )
            $this->getProperty()->text = $text;
        
        return $this;
    }
    
/**
<documentation><description><p>Unpublishes the file and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function unpublish() : Asset
    {
        $this->getService()->unpublish( $this->getIdentifier() );
        
        return $this;
    }
}
?>