<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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

abstract class Block extends ContainedAsset
{
    const DEBUG = false;

    protected function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->processMetadata();
    }

/**
<documentation><description><p></p></description>
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
        $asset                           = new \stdClass();
        //$this->getProperty()->metadata   = $this->metadata->toStdClass();
        
        $asset->{ $p = $this->getPropertyName() }           = $this->getProperty();
        $asset->{ $p = $this->getPropertyName() }->metadata = $this->metadata->toStdClass();

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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedBy()
    {
        return $this->getProperty()->createdBy;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedDate()
    {
        return $this->getProperty()->createdDate;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDynamicField( $name )
    {
        return $this->metadata->getDynamicField( $name );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDynamicFields()
    {
        return $this->metadata->getDynamicFields();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderId()
    {
        return $this->getProperty()->expirationFolderId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderPath()
    {
        return $this->getProperty()->expirationFolderPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderRecycled() : bool
    {
        return $this->getProperty()->expirationFolderRecycled;
    }
        
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedBy() : string
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadata()
    {
        return $this->metadata;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSet()
    {
        $service = $this->getService();
        
        return new MetadataSet( 
            $service, 
            $service->createId( MetadataSet::TYPE, 
                $this->getProperty()->metadataSetId ) );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetId()
    {
        return $this->getProperty()->metadataSetId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetPath()
    {
        return $this->getProperty()->metadataSetPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataStdClass()
    {
        return $this->metadata->toStdClass();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasDynamicField( $name ) : bool
    {
        return $this->metadata->hasDynamicField( $name );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setExpirationFolder( Folder $f ) : Asset
    {
        $this->getProperty()->expirationFolderId   = $f->getId();
        $this->getProperty()->expirationFolderPath = $f->getPath();
        return $this;
    }
        
/**
<documentation><description><p></p></description>
<example></example>
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
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
    
    public static function getBlock( aohs\AssetOperationHandlerService $service, $id_string ) : Asset
    {
        return self::getAsset( $service, 
            self::getBlockType( $service, $id_string ), $id_string );
    }

    public static function getBlockType( aohs\AssetOperationHandlerService $service, $id_string ) : string
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
