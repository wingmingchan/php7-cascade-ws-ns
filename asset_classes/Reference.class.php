<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/30/2014 Added setAsset.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class Reference extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::REFERENCE;
    
    public function getCreatedBy()
    {
        return $this->getProperty()->createdBy;
    }
    
    public function getCreatedDate()
    {
        return $this->getProperty()->createdDate;
    }
    
    public function getLastModifiedBy()
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
    public function getLastModifiedDate()
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
    public function getReferencedAsset()
    {
        return Asset::getAsset( 
            $this->getService(),
            $this->getProperty()->referencedAssetType,
            $this->getProperty()->referencedAssetId );
    }
    
    public function getReferencedAssetId()
    {
        return $this->getProperty()->referencedAssetId;
    }
    
    public function getReferencedAssetPath()
    {
        return $this->getProperty()->referencedAssetPath;
    }
    
    public function getReferencedAssetType()
    {
        return $this->getProperty()->referencedAssetType;
    }
    
    public function setAsset( Asset $asset )
    {
        $property = $this->getProperty();
        $property->referencedAssetId   = $asset->getId();
        $property->referencedAssetPath = $asset->getPath();
        $property->referencedAssetType = $asset->getType();
        
        $asset                          = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $property;
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
}
?>