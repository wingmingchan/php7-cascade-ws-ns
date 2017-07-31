<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/13/2017 Added JSON structure and JSON dump.
  * 10/12/2016 Fixed errors in documentation.
  * 9/13/2016 Fixed bugs in setExpirationFolder.
  * 9/6/2016 Added expiration folder-related code.
  * 1/4/2016 Fixed a bug in publish.
  * 10/30/2015 Added unpublish.
  * 9/16/2015 Fixed a bug in setMetadata.
  * 6/23/2015 Modified getWorkflowSettings, passing in $service.
  * 5/28/2015 Added namespaces.
  * 9/29/2014 Added expiration folder-related methods.
  * 8/25/2014 Overrode edit.
  * 7/22/2014 Added isPublishable.
  * 7/14/2014 Added getMetadataStdClass, setMetadata.
  * 7/1/2014 Removed copy.
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
<p>A <code>Folder</code> object represents a folder asset. This class is a sub-class of <a href=\"/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>folder</code></h2>
<pre>SOAP:
folder
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
  expirationFolderRecycled
  shouldBePublished
  shouldBeIndexed
  lastPublishedDate
  lastPublishedBy
  children
    child (NULL, stdClass or array of stdClass)
      id
      path
      type
      recycled

JSON:
folder
  children (array)
    stdClass
      id
      path
        path
        siteId
        siteName
      type
      recycled
  shouldBePublished
  shouldBeIndexed
  lastPublishedDate
  lastPublishedBy
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
        array( "getComplexTypeXMLByName" => "folder" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/folder.php">folder.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{ "asset":{
    "folder":{
      "children":[ {
        "id":"c4482f1f7f0000014d70316543f95810",
        "path":{ "path":"suny-upstate/templates/RWD",
          "siteId":"9c8883d07f00000140b4daea7170b336" },
        "type":"template",
        "recycled":false },
      {
        "id":"c44960a07f0000014d70316501c2184a",
        "path":{ "path":"suny-upstate/templates/",
          "siteId":"9c8883d07f00000140b4daea7170b336" },
        "type":"template",
        "recycled":false },
      {
        "id":"c449b0ff7f0000014d703165d32ba6cd",
        "path":{ "path":"suny-upstate/templates/",
          "siteId":"9c8883d07f00000140b4daea7170b336" },
        "type":"template",
        "recycled":false },
      {
        "id":"c44a60887f0000014d7031654a242191",
        "path":{ "path":"suny-upstate/templates/RWD",
          "siteId":"9c8883d07f00000140b4daea7170b336" },
        "type":"template",
        "recycled":false } ],
      "shouldBePublished":true,
      "shouldBeIndexed":true,
      "expirationFolderRecycled":false,
      "metadataSetId":"9c8883aa7f00000140b4daeab7c5079c",
      "metadataSetPath":"Default",
      "metadata":{
        "displayName":"",
        "title":"",
        "summary":"",
        "teaser":"",
        "keywords":"",
        "metaDescription":"",
        "author":"" },
      "parentFolderId":"a226b81c7f0000011d450d2ac664948d",
      "parentFolderPath":"suny-upstate",
      "lastModifiedDate":"May 18, 2016 9:15:24 AM",
      "lastModifiedBy":"wing",
      "createdDate":"May 18, 2016 9:15:24 AM",
      "createdBy":"wing",
      "path":"suny-upstate/templates",
      "siteId":"9c8883d07f00000140b4daea7170b336",
      "siteName":"POPs",
      "name":"templates",
      "id":"c4389bc17f0000014d7031651f80292e" } },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class Folder extends Container
{
    const DEBUG = false;
    const DUMP  = false;
    const TYPE  = c\T::FOLDER;
    
/**
<documentation><description><p>The constructor, overriding the parent method to process metadata.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->processMetadata();
    }
    
/**
<documentation><description><p>An alias of <code>addWorkflowDefinition</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addWorkflow( WorkflowDefinition $wf ) : Asset
    {
        return $this->addWorkflowDefinition( $wf );
    }
    
/**
<documentation><description><p>Adds the workflow definition to the workflow settings,
and returns the calling object. Note that this method does not called <code>editWorkflowSettings</code>.</p></description>
<example>$f->addWorkflow( $wd )->editWorkflowSettings( true, true );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addWorkflowDefinition( WorkflowDefinition $wf ) : Asset
    {
        $this->getWorkflowSettings()->addWorkflowDefinition( $wf );
        return $this;
    }
    
/**
<documentation><description><p>Overrides the parent method to take care of metadata, and returns the calling object.</p></description>
<example>$folder->edit();</example>
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
        $asset  = new \stdClass();
        $folder = $this->getProperty();
        
        if( $folder->path == "/" )
        {
            $folder->parentFolderId = 'some dummy string';
        }
        $folder->metadata = $this->getMetadata()->toStdClass();
        
        $asset->{ $p = $this->getPropertyName() } = $folder;

        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                c\M::EDIT_ASSET_FAILURE . 
                "<span style='color:red;font-weight:bold;'>Path: " . 
                $folder->path . "</span>" .
                $service->getMessage() );
        }
        return $this->reloadProperty();
    }
    
/**
<documentation><description><p>Sets the two boolean flags and returns the object.
Note that the <code>edit</code> method is not called because the workflow settings
of a folder is a separate object.</p></description>
<example>$f->editWorkflowSettings( true, true );</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException, EditingFailureException</exception>
</documentation>
*/
    public function editWorkflowSettings( 
        bool $apply_inherit_workflows_to_children, 
        bool $apply_require_workflow_to_children ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $apply_inherit_workflows_to_children ) )
            throw new e\UnacceptableValueException( 
                "The value $apply_inherit_workflows_to_children must be a boolean." );
                
        if( !c\BooleanValues::isBoolean( $apply_require_workflow_to_children ) )
            throw new e\UnacceptableValueException( 
                "The value $apply_require_workflow_to_children must be a boolean." );
    
        $service = $this->getService();
        $service->editWorkflowSettings( $this->workflow_settings->toStdClass(),
            $apply_inherit_workflows_to_children, $apply_require_workflow_to_children );
            
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                c\M::EDIT_WORKFLOW_SETTINGS_FAILURE . $service->getMessage() );
        }
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>createdBy</code>.</p></description>
<example>echo $f->getCreatedBy() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedBy() : string
    {
        return $this->getProperty()->createdBy;
    }
    
/**
<documentation><description><p>Returns <code>createdDate</code>.</p></description>
<example>echo $f->getCreatedDate() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedDate() : string
    {
        return $this->getProperty()->createdDate;
    }
    
/**
<documentation><description><p>Returns the <a
href="http://www.upstate.edu/web-services/api/property-classes/dynamic-field.php"><code>p\DynamicField</code></a> object bearing that name.</p></description>
<example>$field_name = 'exclude-from-left';
echo "Dumping dynamic field $field_name" . S_PRE;

if( $f->hasDynamicField( $field_name ) )
    u\DebugUtility::dump( $f->getDynamicField( $field_name ) );
echo E_PRE . HR;</example>
<return-type>Property</return-type>
<exception>EmptyNameException, NoSuchFieldException</exception>
</documentation>
*/
    public function getDynamicField( string $name ) : p\Property
    {
        return $this->metadata->getDynamicField( $name );
    }
    
/**
<documentation><description><p>Returns <code>NULL</code> or an array of
<code>p\DynamicField</code> objects.</p></description>
<example>u\DebugUtility::dump( $f->getDynamicFields() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDynamicFields()
    {
        return $this->metadata->getDynamicFields();
    }
    
/**
<documentation><description><p>Returns <code>expirationFolderId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $f->getExpirationFolderId() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderId()
    {
        return $this->getProperty()->expirationFolderId;
    }
    
/**
<documentation><description><p>Returns <code>expirationFolderPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $f->getExpirationFolderPath() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderPath()
    {
        return $this->getProperty()->expirationFolderPath;
    }
    
/**
<documentation><description><p>Returns <code>expirationFolderRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $f->getExpirationFolderRecycled() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderRecycled() : bool
    {
        return $this->getProperty()->expirationFolderRecycled;
    }
    
/**
<documentation><description><p>Returns an array of <code>id</code> strings of folder children.</p></description>
<example>u\DebugUtility::dump( $f->getFolderChildrenIds() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getFolderChildrenIds() : array
    {
        return $this->getContainerChildrenIds();
    }

/**
<documentation><description><p>Returns <code>lastModifiedBy</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $f->getLastModifiedBy() ) .   BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedBy() : string
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedDate</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $f->getLastModifiedDate() ) .   BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p>Returns <code>lastPublishedBy</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $f->getLastPublishedBy() ) .   BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedBy()
    {
        return $this->getProperty()->lastPublishedBy;
    }
    
/**
<documentation><description><p>Returns <code>lastPublishedDate</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $f->getLastPublishedDate() ) .   BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedDate()
    {
        return $this->getProperty()->lastPublishedDate;
    }
    
/**
<documentation><description><p>Returns the <a
href="http://www.upstate.edu/web-services/api/property-classes/metadata.php"><code>Metadata</code></a> object.</p></description>
<example>u\DebugUtility::dump( $f->getMetadata()->toStdClass() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadata() : p\Property
    {
        return $this->metadata;
    }
    
/**
<documentation><description><p>Returns the associated <code>MetadataSet</code> object.</p></description>
<example>$f->getMetadataSet()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSet() : Asset
    {
        $service = $this->getService();
        //echo $this->metadataSetId . BR;
        
        return new MetadataSet( 
            $service, 
            $service->createId( MetadataSet::TYPE, 
                $this->getProperty()->metadataSetId ) );
    }
    
/**
<documentation><description><p>Returns <code>metadataSetId</code>.</p></description>
<example>echo $f->getMetadataSetId() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetId() : string
    {
        return $this->getProperty()->metadataSetId;
    }
    
/**
<documentation><description><p>Returns <code>metadataSetPath</code>.</p></description>
<example>echo $f->getMetadataSetPath() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetPath() : string
    {
        return $this->getProperty()->metadataSetPath;
    }
    
/**
<documentation><description><p>Returns the metadata property (an <code>\stdClass</code> object).</p></description>
<example>u\DebugUtility::dump( $f->getMetadataStdClass() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataStdClass() : \stdClass
    {
        return $this->metadata->toStdClass();
    }
    
/**
<documentation><description><p>Returns <code>parentFolderId</code>.</p></description>
<example>echo "Parent folder ID: ", u\StringUtility::getCoalescedString(
    $f->getParentFolderId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolderId()
    {
        return $this->getParentContainerId();
    }
    
/**
<documentation><description><p>Returns <code>parentFolderPath</code>.</p></description>
<example>echo "Parent folder path: ", u\StringUtility::getCoalescedString(
    $f->getParentFolderPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolderPath()
    {
        return $this->getParentContainerPath();
    }

/**
<documentation><description><p>Returns <code>shouldBeIndexed</code>.</p></description>
<example>echo u\StringUtility::boolToString( $f->getShouldBeIndexed() ) . BR;</example>
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
<example>echo u\StringUtility::boolToString( $f->getShouldBePublished() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBePublished() : bool
    {
        return $this->getProperty()->shouldBePublished;
    }

/**
<documentation><description><p>Returns the a <a
href="http://www.upstate.edu/web-services/api/property-classes/workflow-settings.php"><code>p\WorkflowSettings</code></a> object.</p></description>
<example>$ws = $f->getWorkflowSettings();</example>
<return-type>WorkflowSettings</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function getWorkflowSettings() : p\WorkflowSettings
    {
        if( $this->workflow_settings == NULL )
        {
            $service = $this->getService();
        
            $service->readWorkflowSettings( 
                $service->createId( self::TYPE, $this->getProperty()->id ) );
    
            if( $service->isSuccessful() )
            {
                $this->workflow_settings = new p\WorkflowSettings( 
                    $service->getReply()->readWorkflowSettingsReturn->workflowSettings,
                    $service );
            }
            else
            {
                throw new \Exception( $service->getMessage() );
            }
        }
        return $this->workflow_settings;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>DynamicField</code> bearing that name exists.</p></description>
<example>if( !$f->hasWorkflowDefinition( $wd ) )
{
    $f->addWorkflow( $wd )->editWorkflowSettings( true, true );
}</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasWorkflowDefinition( WorkflowDefinition $wf ) : bool
    {
        return $this->getWorkflowSettings()->hasWorkflowDefinition(
          $wf->getId()
        );
    }

/**
<documentation><description><p>Returns a bool, indicating whether the <code>DynamicField</code> bearing that name exists.</p></description>
<example>if( $f->hasDynamicField( $field_name ) )
    u\DebugUtility::dump( $f->getDynamicField( $field_name ) );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasDynamicField( string $name ) : bool
    {
        return $this->metadata->hasDynamicField( $name );
    }
   
/**
<documentation><description><p>Returns a bool, indicating whether the folder is publishable.</p></description>
<example>echo "Is folder publishable: ", u\StringUtility::boolToString(
    $f->isPublishable() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isPublishable() : bool
    {
        $path = $this->getPath();
        if( self::DEBUG ) { u\DebugUtility::out( $path ); }
        
        if( $this->getPath() == '/' )
        {
            return $this->getShouldBePublished();
        }
        else
        {
            $parent = $this->getAsset(
                $this->getService(), Folder::TYPE, $this->getParentContainerId() );
            return $parent->isPublishable() && $this->getShouldBePublished();
        }
    }
    
/**
<documentation><description><p>Publishes the folder and returns the
calling object.</p></description>
<example>$folder->publish();</example>
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
                    $service->createId( $this->getType(), $this->getId() ),
                    $destination_std );
            else
                $service->publish( 
                    $service->createId( $this->getType(), $this->getId() ) );
        }
        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>removeWorkflowDefinition</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeWorkflow( WorkflowDefinition $wf ) : Asset
    {
        return $this->removeWorkflowDefinition( $wf );
    }
    
/**
<documentation><description><p>Removes the workflow definition from the workflow settings,
and returns the calling object. Note that this method does not called <code>editWorkflowSettings</code>.</p></description>
<example>$f->removeWorkflow( $wd )->editWorkflowSettings( true, true );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeWorkflowDefinition( WorkflowDefinition $wf ) : Asset
    {
        $this->getWorkflowSettings()->removeWorkflowDefinition( $wf );
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>expirationFolderId</code> and <code>expirationFolderPath</code>, and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setExpirationFolder( Folder $f=NULL ) : Asset
    {
        $ms = $this->getMetadataSet();
        
        if( $ms->getExpirationFolderFieldRequired() && $f === NULL )
            throw new e\NullAssetException( c\M::NULL_FOLDER );
        
        if( $f === NULL )
        {
            $this->getProperty()->expirationFolderId  = NULL;
            $this->getProperty()->expirationFolderPath = NULL;
        }
        else
        {
            $this->getProperty()->expirationFolderId   = $f->getId();
            $this->getProperty()->expirationFolderPath = $f->getPath();
        }
        return $this;
    }
        
/**
<documentation><description><p>Sets the <code>metadata</code> property, calls
<code>edit</code>, and returns the calling object.</p></description>
<example>$folder->setMetadataSet( $new_ms )->setMetadata( $new_m );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setMetadata( p\Metadata $m ) : Asset
    {
        $this->metadata = $m;
        $this->edit();
        $this->processMetadata();
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>metadataSetId</code> and
<code>metadataSetPath</code>, calls <code>edit</code>, and returns
the calling object.</p></description>
<example>$folder->setMetadataSet( $new_ms )->setMetadata( $new_m );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setMetadataSet( MetadataSet $ms ) : Asset
    {
        if( $ms == NULL )
        {
            throw new e\NullAssetException( c\M::NULL_ASSET );
        }
    
        $this->getProperty()->metadataSetId   = $ms->getId();
        $this->getProperty()->metadataSetPath = $ms->getPath();
        $this->edit();
        $this->processMetadata();
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>shouldBeIndexed</code>
and returns the calling object.</p></description>
<example>$f->setShouldBeIndexed( true )->setShouldBePublished( true )->
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setShouldBeIndexed( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( "The value $bool must be a boolean" );
            
        $this->getProperty()->shouldBeIndexed = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>shouldBePublished</code> and returns the
calling object.</p></description>
<example>$f->setShouldBeIndexed( true )->setShouldBePublished( true )->
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setShouldBePublished( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( "The value $bool must be a boolean" );
            
        $this->getProperty()->shouldBePublished = $bool;
        return $this;
    }

/**
<documentation><description><p>Unpublishes the folder and returns the
calling object.</p></description>
<example>$f->unpublish();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function unpublish() : Asset
    {
        $this->getService()->unpublish( $this->getIdentifier() );
        return $this;
    }
    
    private function processMetadata()
    {
        $this->metadata = new p\Metadata( 
            $this->getProperty()->metadata, 
            $this->getService(), 
            $this->getProperty()->metadataSetId, $this );
    }

    private $metadata;
    private $children;
    private $workflow_settings;
}
?>
