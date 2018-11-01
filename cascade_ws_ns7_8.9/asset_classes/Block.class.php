<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/3/2018 Updated with tests for NULL.
  * 11/28/2017 Changed parent class to DublinAwareAsset.
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/14/2017 Added WSDL.
  * 9/13/2016 Fixed bugs in setExpirationFolder.
  * 9/6/2016 Added expiration folder-related code.
  * 9/16/2015 Fixed a bug in setMetadata.
  * 5/28/2015 Added namespaces.
  * 9/29/2014 Added expiration folder-related methods.
  * 7/15/2014 Added getMetadataStdClass, setMetadata.
  * 7/1/2014 Removed copy.
 */
namespace cascade_ws_asset;

use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;
use cascade_ws_constants as c;

/**
<documentation>
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>The <code>Block</code> class is the superclass of <code>TextBlock</code>,
<code>DataDefinitionBlock</code> and so on.
It is an abstract class and defines most of the methods shared by all types of blocks.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "block" ),
        array( "getComplexTypeXMLByName" => "expiring-asset" ),
        array( "getComplexTypeXMLByName" => "dublin-aware-asset" ),
        array( "getComplexTypeXMLByName" => "folder-contained-asset" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/block.php">block.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/text_block.php">text_block.php</a></li>
</ul></postscript>
</documentation>
*/
abstract class Block extends DublinAwareAsset
{
    const DEBUG = false;

/**
<documentation><description><p>The constructor, overriding the parent method to process
metadata.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    protected function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->processMetadata();
    }

/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example>$tb->setText( $text )->edit();</example>
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
        $asset                           = new \stdClass();
        //$this->getProperty()->metadata   = $this->metadata->toStdClass();
        
        $asset->{ $p = $this->getPropertyName() }           = $this->getProperty();
        $asset->{ $p = $this->getPropertyName() }->metadata =
            $this->metadata->toStdClass();
            
        // patch for 8.9
        if( isset( $asset->reviewEvery ) )
        {
            $review_every = (int)$asset->reviewEvery;
        
            if( $review_every != 0 && $review_every != 30 && $review_every != 90 && 
                $review_every != 180 && $review_every != 365 )
            {
                $asset->reviewEvery = 0;
            }
        }

        if( self::DEBUG ){ u\DebugUtility::dump( $asset ); }

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
<example>echo $tb->getCreatedBy() . BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedBy() : string
    {
        return $this->getProperty()->createdBy;
    }
    
/**
<documentation><description><p>Returns <code>createdDate</code>.</p></description>
<example>echo $tb->getCreatedDate() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedDate() : string
    {
        return $this->getProperty()->createdDate;
    }
    
/**
<documentation><description><p>Returns the named
<a href="http://www.upstate.edu/web-services/api/property-classes/dynamic-field.php"><code>p\DynamicField</code></a> object.</p></description>
<example>if( $tb->hasDynamicField( $field_name ) )
{
    $df = $tb->getDynamicField( $field_name );
}</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getDynamicField( string $name ) : p\Property
    {
        return $this->metadata->getDynamicField( $name );
    }
    
/**
<documentation><description><p>Returns an array of <code>p\DynamicField</code> objects
or <code>NULL</code>.</p></description>
<example>if( $tb->hasDynamicFields() )
{
    u\DebugUtility::dump( $tb->getDynamicFields() );
}
else
{
    echo "There are no dynamic fields", BR;
}
</example>
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
<example>echo u\StringUtility::getCoalescedString(
    $tb->getExpirationFolderId() ), BR;</example>
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
<example>echo u\StringUtility::getCoalescedString(
    $tb->getExpirationFolderPath() ), BR;</example>
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
<example>echo u\StringUtility::boolToString(
    $tb->getExpirationFolderRecycled() ), BR;</example>
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
<example>echo $tb->getLastModifiedBy() . BR;</example>
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
<example>echo $tb->getLastModifiedDate() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p>Returns the <a href="http://www.upstate.edu/web-services/api/property-classes/metadata.php"><code>p\Metadata</code></a> object.</p></description>
<example>u\DebugUtility::dump( $tb->getMetadata()->toStdClass() );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadata() : p\Property
    {
        return $this->metadata;
    }
    
/**
<documentation><description><p>Returns the <code>MetadataSet</code> object.</p></description>
<example>$tb->getMetadataSet()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSet() : Asset
    {
        $service = $this->getService();
        
        return new MetadataSet( 
            $service, 
            $service->createId( MetadataSet::TYPE, 
                $this->getProperty()->metadataSetId ) );
    }
    
/**
<documentation><description><p>Returns <code>metadataSetId</code>.</p></description>
<example>echo $tb->getMetadataSetId(), BR;</example>
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
<example>echo $tb->getMetadataSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetPath() : string
    {
        return $this->getProperty()->metadataSetPath;
    }
    
/**
<documentation><description><p>Returns the metadata as an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $tb->getMetadataStdClass() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataStdClass() : \stdClass
    {
        return $this->metadata->toStdClass();
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named <code>p\DynamicField</code> exists.</p></description>
<example>if( $tb->hasDynamicField( $field_name ) )
{
    $df = $tb->getDynamicField( $field_name );
}</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasDynamicField( string $name ) : bool
    {
        return $this->metadata->hasDynamicField( $name );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named <code>p\DynamicField</code> exists.</p></description>
<example>if( $tb->hasDynamicFields() )
{
    u\DebugUtility::dump( $tb->getDynamicFields() );
}</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasDynamicFields() : bool
    {
        return $this->metadata->hasDynamicFields();
    }
    
/**
<documentation><description><p>Sets the <code>expirationFolderId</code> and <code>expirationFolderPath</code>, and returns the calling object.</p></description>
<example>$tb->setExpirationFolder(
    $cascade->getAsset( a\Folder::TYPE, "2401bc368b7ffe834c5fe91e0027a274" )
)->edit()->dump();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
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
<documentation><description><p>Sets the metadata, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$tb->setMetadata( $new_m );</example>
<return-type></return-type>
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
<documentation><description><p>Sets the metadata set, calls <code>edit</code>,
and returns the calling object.</p></description>
<example>$tb->setMetadataSet(
    $cascade->getAsset(
        a\MetadataSet::TYPE, "cc1e51068b7ffe8364375ac78eca378c" )
)->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setMetadataSet( MetadataSet $m ) : Asset
    {
        if( $m == NULL )
        {
            throw new e\NullAssetException(
                S_SPAN . c\M::NULL_ASSET . E_SPAN );
        }
    
        $this->getProperty()->metadataSetId   = $m->getId();
        $this->getProperty()->metadataSetPath = $m->getPath();
        $this->edit();
        $this->processMetadata();
        
        return $this;
    }
    
/**
<documentation><description><p>Returns a <code>Block</code> object bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a block.</p></description>
<example>$block = a\Block::getBlock( $service, $id )->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public static function getBlock( 
        aohs\AssetOperationHandlerService $service, string $id_string ) : Asset
    {
        return self::getAsset( $service, 
            self::getBlockType( $service, $id_string ), $id_string );
    }

/**
<documentation><description><p>Returns the type of the block bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a block.</p></description>
<example>echo a\Block::getBlockType( $service, $id ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getBlockType(
        aohs\AssetOperationHandlerService $service, string $id_string ) : string
    {
        $types      
            = array( 
                DataBlock::TYPE, FeedBlock::TYPE, IndexBlock::TYPE, 
                TextBlock::TYPE, XmlBlock::TYPE );
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
        $this->metadata = new p\Metadata( 
            $this->getProperty()->metadata, 
            $this->getService(), 
            $this->getProperty()->metadataSetId,
            $this );
    }    

    private $block;          // the property of asset
    private $metadata;
}
?>