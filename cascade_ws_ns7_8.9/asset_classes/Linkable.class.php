<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 11/28/2017 Changed parent class to DublinAwareAsset.
  * 11/1/2016 Added exception in setMetadataSet for pages; turned setPageContentType to protected.
  * 9/13/2016 Fixed bugs in setExpirationFolder.
  * 9/16/2015 Fixed a bug in setMetadata.
  * 5/28/2015 Added namespaces.
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
<description><h2>Introduction</h2>
<p>The abstract <code>Linkable</code> class is the superclass of <a href="http://www.upstate.edu/web-services/api/asset-classes/page.php"><code>Page</code></a>, <a href="http://www.upstate.edu/web-services/api/asset-classes/file.php"><code>File</code></a>, and <a href="http://www.upstate.edu/web-services/api/asset-classes/symlink.php"><code>Symlink</code></a>.</p>
<p>This class is my creation to deal with data definitions. A linkable chooser can be used in a data definition, allowing users to choose either a file, a page, or a symlink. Therefore, there should be a <code>setLinkable</code> method in both <a href="http://www.upstate.edu/web-services/api/asset-classes/data-definition-block.php"><code>DataDefinitionBlock</code></a> and <a href="http://www.upstate.edu/web-services/api/asset-classes/page.php"><code>Page</code></a>. The signature of the method will be like this:</p>
<pre class="code">public function setLinkable( $node_name, Linkable $linkable=NULL )
{
}
</pre>
<p>The <code>$linkable</code> variable can point to a file, a page, or a symlink.</p>
<h2>Design Issues</h2>
<p>Currently, the metadata set ID can be read from a file or symlink property. But it is not possible for a page. Instead, the ID can only be read through the associated content type. Before this class processes the metadata, the <code>ContentType</code> object associated with a page must be passed into this class from <code>Page</code>. Therefore, I have to provide a <code>setPageContentType</code> method in this class to allow that. This method only works for a <code>Page</code> object, or a <code>WrongAssetTypeException</code> will be thrown.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/linkable.php">linkable.php</a></li></ul></postscript>
</documentation>
*/
abstract class Linkable extends DublinAwareAsset
{
    const DEBUG = false;

/**
<documentation><description><p>The constructor, overriding the parent method to process metadata.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    protected function __construct( 
        aohs\AssetOperationHandlerService $service, 
        \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        // Skip page for content type to be set
        if( $this->getType() == File::TYPE || $this->getType() == Symlink::TYPE )
        {
            $this->processMetadata();
        }
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
        $asset                          = new \stdClass();
        $this->getProperty()->metadata  = $this->metadata->toStdClass();
        
        // patch for 8.9
        if( isset( $this->getProperty()->reviewEvery ) )
        {
            $review_every = (int)$this->getProperty()->reviewEvery;
        
            if( $review_every != 0 && $review_every != 30 && $review_every != 90 && 
                $review_every != 180 && $review_every != 365 )
            {
                $this->getProperty()->reviewEvery = 0;
            }
        }
        
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
<documentation><description><p>Returns <code>createdBy</code>.</p></description>
<example>echo $page->getCreatedBy(), BR;</example>
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
<example>echo $page->getCreatedDate(), BR;</example>
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
<example>u\DebugUtility::dump( $page->getDynamicField( "exclude-from-menu" ) );</example>
<return-type>Property</return-type>
<exception>EmptyNameException, NoSuchFieldException</exception>
</documentation>
*/
    public function getDynamicField( string $name ) : p\Property
    {
        return $this->metadata->getDynamicField( $name );
    }
    
/**
<documentation><description><p><p>Returns <code>NULL</code> or an array of
<code>p\DynamicField</code> objects.</p></p></description>
<example>u\DebugUtility::dump( $page->getDynamicFields() );</example>
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
<example>echo u\StringUtility::getCoalescedString( $page->getExpirationFolderId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderId()
    {
        if( isset( $this->getProperty()->expirationFolderId ) )
            return $this->getProperty()->expirationFolderId;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>expirationFolderPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $page->getExpirationFolderPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderPath()
    {
        if( isset( $this->getProperty()->expirationFolderPath ) )
            return $this->getProperty()->expirationFolderPath;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>expirationFolderRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $page->getExpirationFolderRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderRecycled() : bool
    {
        return $this->getProperty()->expirationFolderRecycled;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedBy</code>.</p></description>
<example>echo $page->getLastModifiedBy(), BR;</example>
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
<example>echo $page->getLastModifiedDate(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p>Returns the <a
href="http://www.upstate.edu/web-services/api/property-classes/metadata.php"><code>Metadata</code></a> object.</p></description>
<example>u\DebugUtility::dump( $page->getMetadata() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadata() : p\Property
    {
        return $this->metadata;
    }
    
/**
<documentation><description><p>Returns the metadata property (an <code>\stdClass</code> object).</p></description>
<example>u\DebugUtility::dump( $page->getMetadataStdClass() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataStdClass() : \stdClass
    {
        return $this->metadata->toStdClass();
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>DynamicField</code> bearing that name exists.</p></description>
<example>echo u\StringUtility::boolToString( $page->hasDynamicField( "exclude-from-menu" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasDynamicField( string $name ) : bool
    {
        return $this->metadata->hasDynamicField( $name );
    }
    
/**
<documentation><description><p>Sets the <code>expirationFolderId</code> and <code>expirationFolderPath</code>, and returns the calling object.</p></description>
<example>$page->setExpirationFolder(
    $cascade->getAsset(
        a\Folder::TYPE, "39d53a118b7ffe834c5fe91e7e2e0cd9" )
)->edit();
</example>
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
<example>$page->setMetadata( $new_m );</example>
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
<example></example>
<return-type>Asset</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function setMetadataSet( MetadataSet $m ) : Asset
    {
        if( $m == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET . E_SPAN );
        }
        
        if( $this->getType() == Page::TYPE )
        {
            throw new e\WrongAssetTypeException(
                S_SPAN . c\M::PAGE_METADATA_SET . E_SPAN );
        }
    
        $this->getProperty()->metadataSetId   = $m->getId();
        $this->getProperty()->metadataSetPath = $m->getPath();
        $this->edit();
        $this->processMetadata();

        return $this;
    }
    
/**
<documentation><description><p>Sets the content type for a page and returns the calling object. Do not use this method on a <code>File</code> or <code>Symlink</code> object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>WrongAssetTypeException, NullAssetException</exception>
</documentation>
*/
    protected function setPageContentType( ContentType $c ) : Asset
    {
        if( $this->getType() != Page::TYPE )
        {
            throw new e\WrongAssetTypeException(
                S_SPAN . "This is not a page." . E_SPAN );
        }
        if( $c == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET . E_SPAN );
        }
        $this->page_content_type = $c;
        $this->processMetadata();
        return $this;
    }
    
/**
<documentation><description><p>Returns a <code>Linkable</code> object bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a linkable.</p></description>
<example>a\Linkable::getLinkable( $service, "06a23e5b8b7ffe830820c9fac501387b" )->dump();</example>
<return-type>Asset</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public static function getLinkable( 
        aohs\AssetOperationHandlerService $service, string $id_string ) : Asset
    {
        return self::getAsset( $service, 
            self::getLinkableType( $service, $id_string ), $id_string );
    }

/**
<documentation><description><p>eturns the type of the linkable bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a linkable. Note that if the supplied id does not match any asset, the string "The id does not match any asset type." will be returned.</p></description>
<example>echo a\Linkable::getLinkableType( $service, "06a23e5b8b7ffe830820c9fac501387b" ), BR;</example>
<return-type>string</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public static function getLinkableType(
        aohs\AssetOperationHandlerService $service, string $id_string ) : string
    {
        $types      = array( Page::TYPE, File::TYPE, Symlink::TYPE );
        $type_count = count( $types );
        
        for( $i = 0; $i < $type_count; $i++ )
        {
            $id = $service->createId( $types[ $i ], $id_string );
            $operation = new \stdClass();
            $read_op   = new \stdClass();
    
            $read_op->identifier = $id;
            $operation->read     = $read_op;
            $operations[]        = $operation;
        }
        
        $service->batch( $operations );
        
        $reply_array = $service->getReply()->batchReturn;
        
        for( $j = 0; $j < $type_count; $j++ )
        {
            if( $reply_array[ $j ]->readResult->success == 'true' )
            {
                foreach( c\T::$type_property_name_map as $type => $property )
                {
                    if( isset( $reply_array[ $j ]->readResult->asset->$property ) )
                        return $type;
                }
            }
        }
        
        return "The id does not match any asset type.";
    }

    private function processMetadata()
    {
        if( $this->getType() == Page::TYPE && isset( $this->page_content_type ) )
        {
            $metadata_set_id = $this->page_content_type->getMetadataSetId();
        }
        else
        {
            $metadata_set_id = $this->getProperty()->metadataSetId;
        }
        
        $this->metadata = new p\Metadata( 
            $this->getProperty()->metadata, 
            $this->getService(), $metadata_set_id, $this
        );
    }

    private $metadata;
    private $page_content_type;
}
?>