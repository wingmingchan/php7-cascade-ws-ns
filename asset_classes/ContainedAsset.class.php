<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 4/25/2016 Added isDescendantOf.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;

abstract class ContainedAsset extends Asset
{
    const DEBUG = false;

    public function getParentContainer()
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        
        if( $this->getParentContainerId() != NULL )
        {
            $parent_id    = $this->getParentContainerId();
            $parent_type  = c\T::$type_parent_type_map[ $this->getType() ];
            
            return Asset::getAsset( $this->getService(), $parent_type, $parent_id );
        }
        return NULL;
    }
    
    public function getParentContainerId()
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        
        if( isset( $this->getProperty()->parentFolderId ) )
            return $this->getProperty()->parentFolderId;
        else
            return $this->getProperty()->parentContainerId;
    }
    
    public function getParentContainerPath()
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        
        if( isset( $this->getProperty()->parentFolderPath ) )
            return $this->getProperty()->parentFolderPath;
        elseif( isset( $this->getProperty()->parentContainerPath ) )
            return $this->getProperty()->parentContainerPath;
        else
            return NULL;
    }
    
    public function isDescendantOf( Container $container )
    {
        return $container->isAncestorOf( $this );
    }
    
    public function isInContainer( Container $c )
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        return $c->getId() == $this->getParentContainerId();
    }
    
    public function move( Container $new_parent, $doWorkflow=false )
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        
        if( $new_parent == NULL )
        {
            throw new e\NullAssetException( c\M::NULL_CONTAINER );
        }
        $this->moveRename( $new_parent, NULL, $doWorkflow );
        $this->reloadProperty();
        
        return $this;
    }
    
    public function rename( $new_name, $doWorkflow=false )
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }

        if( trim( $new_name ) == "" )
        {
            throw new e\EmptyValueException( c\M::EMPTY_NAME );
        }
        $this->moveRename( NULL, $new_name, $doWorkflow );
        $this->reloadProperty();
        
        return $this;
    }
    
    private function moveRename( $parent_container, $new_name, $doWorkflow=false )
    {
        if( !c\BooleanValues::isBoolean( $doWorkflow ) )
            throw new e\UnacceptableValueException( "The value $doWorkflow must be a boolean." );
            
        $parent_id = NULL;
        
        if( isset( $parent_container ) )
        {
            $parent_id = $parent_container->getIdentifier();
        }
    
        $identifier = $this->getIdentifier();

        $this->getService()->move( $identifier, $parent_id, $new_name, $doWorkflow );
        
        if( !$this->getService()->isSuccessful() )
        {
            throw new e\RenamingFailureException( 
                c\M::RENAME_ASSET_FAILURE . $this->getService()->getMessage() );
        }
    }
}
?>