<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/2/2018 Class created.
 */

namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

class Administration
{
    const DEBUG      = false;
    const DUMP       = false;
    const NAME_SPACE = "cascade_ws_asset";
    
    public function __construct( Cascade $cascade )
    {
        if( $cascade == NULL )
        {
            throw new e\NullServiceException( c\M::NULL_CASCADE );
        }
            
        $this->cascade = $cascade;
        $this->service = $cascade->getService();
    }
    
    public function associateAssetsWithMetadataSets( array $array ) : Administration
    {
        $type_array  = $this->service->getTypes();
        
        // check type strings
        foreach( $array as $key => $param )
        {
            if( !in_array( $key, $type_array ) )
            {
                throw new e\NoSuchTypeException( "The type $key does not exist." );
            }
            
            foreach( $param as $param_key => $param_value )
            {
                if( !in_array( $param_key, $type_array ) )
                {
                    throw new e\NoSuchTypeException(
                        "The type $param_key does not exist." );
                }
            }
        }
        
        foreach( $array as $key => $param )
        {
            $folder = $this->getAssetWithParam( Folder::TYPE, $param[ Folder::TYPE ] );
        
            if( !$folder instanceof Folder )
            {
                throw new e\WrongAssetTypeException(
                    $folder->getName() . " is not a folder." );
            }
        
            $ms = $this->getAssetWithParam(
                MetadataSet::TYPE, $param[ MetadataSet::TYPE ] );
        
            if( !$ms instanceof MetadataSet )
            {
                throw new e\WrongAssetTypeException(
                    $ms->getName() . " is not a metadata set." );
            }
            
            $folder->getAssetTree()->traverse(
                array( $key => array( "assetTreeAssociateWithMetadataSet" ) ),
                array( $key => array( MetadataSet::TYPE => $ms ) )
            );
        }
        
        return $this;
    }
    
    public function publishAllFiles(
    	string $site_name, string $folder_path=NULL ) : Administration
    {
    	if( !is_null( $folder_path ) )
    	{
    		$folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
    	}
    	else
    	{
    		$folder = $this->cascade->getSite( $site_name )->getBaseFolder();
    	}
    
        $folder->getAssetTree()->traverse(
            array( File::TYPE => array( "assetTreePublish" ) )
        );
    
        return $this;
    }
    
    public function publishAllFilesPages(
    	string $site_name, string $folder_path=NULL ) : Administration
    {
    	if( !is_null( $folder_path ) )
    	{
    		$folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
    	}
    	else
    	{
    		$folder = $this->cascade->getSite( $site_name )->getBaseFolder();
    	}
    
        $folder->getAssetTree()->traverse(
            array(
                File::TYPE => array( "assetTreePublish" ),
                Page::TYPE => array( "assetTreePublish" )
            )
        );
    
        return $this;
    }
    
    public function publishAllPages(
    	string $site_name, string $folder_path=NULL ) : Administration
    {
    	if( !is_null( $folder_path ) )
    	{
    		$folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
    	}
    	else
    	{
    		$folder = $this->cascade->getSite( $site_name )->getBaseFolder();
    	}
    
        $folder->getAssetTree()->traverse(
            array( Page::TYPE => array( "assetTreePublish" ) )
        );
    
        return $this;
    }
    
    // remove phantom nodes and values NoSuchFieldException
    public function removePhantomNodes(
        string $site_name, string $folder_path=NULL,
        array &$results=NULL ) : Administration
    {
    	if( !is_null( $folder_path ) )
    	{
    		$folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
    	}
    	else
    	{
    		$folder = $this->cascade->getSite( $site_name )->getBaseFolder();
    	}
    
        $folder->getAssetTree()->traverse(
            array(
                Page::TYPE => array( "assetTreeRemovePhantomNodes" ),
                DataDefinitionBlock::TYPE => array( "assetTreeRemovePhantomNodes" ),
            ),
            NULL,
            $results
        );
    
        return $this;
    }
    
    
    public function removePhantomValues(
        string $site_name, string $folder_path=NULL,
        array &$results=NULL ) : Administration
    {
    	if( !is_null( $folder_path ) )
    	{
    		$folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
    	}
    	else
    	{
    		$folder = $this->cascade->getSite( $site_name )->getBaseFolder();
    	}
    
        $folder->getAssetTree()->traverse(
            array(
                Page::TYPE => array( "assetTreeRemovePhantomValues" ),
                DataDefinitionBlock::TYPE => array( "assetTreeRemovePhantomValues" ),
            ),
            NULL,
            $results
        );
    
        return $this;
    }
    
    // report phantom nodes and values NoSuchFieldException
    public function reportPhantomNodes(
        string $site_name, string $folder_path=NULL, array &$results ) : Administration
    {
    	if( !is_null( $folder_path ) )
    	{
    		$folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
    	}
    	else
    	{
    		$folder = $this->cascade->getSite( $site_name )->getBaseFolder();
    	}
    
        $folder->getAssetTree()->traverse(
            array(
                Page::TYPE => array( "assetTreeReportPhantomNodes" ),
                DataDefinitionBlock::TYPE => array( "assetTreeReportPhantomNodes" ),
            ),
            NULL,
            $results
        );
    
        return $this;
    }
    
    public function reportPhantomValues(
    	string $site_name, string $folder_path=NULL, array &$results  ) : array
    {
    	if( !is_null( $folder_path ) )
    	{
    		$folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
    	}
    	else
    	{
    		$folder = $this->cascade->getSite( $site_name )->getBaseFolder();
    	}
    
        $folder->getAssetTree()->traverse(
            array(
                Page::TYPE => array( "assetTreeReportPhantomValues" ),
                DataDefinitionBlock::TYPE => array( "assetTreeReportPhantomValues" ),
            ),
            NULL,
            $results
        );
    
        return $results;
    }
    
    private function getAssetWithParam( string $type, $param ) : Asset
    {
        if( is_string( $param ) && $this->service->isHexString( $param ) )
        {
            u\DebugUtility::out( $type );
            u\DebugUtility::out( $param );
        
            $asset = $this->cascade->getAsset( $type, $param );
        }
        elseif( is_array( $param ) &&
            is_string( $param[ 0 ] ) && is_string( $param[ 1 ] ) &&
            trim( $param[ 0 ], " " ) != "" && trim( $param[ 1 ], " " ) != "" )
        {
            $asset_path = trim( $param[ 0 ], " " );
            $site_name  = trim( $param[ 1 ], " " );
            $asset =
                $this->cascade->getAsset( $type, $asset_path, $site_name );
        }
        elseif( $param instanceof Asset )
        {
            $asset = $param;
        }
        else
        {
            throw new e\NullAssetException( "No $type is supplied." );
        }
        return $asset;
    }
    
    private $cascade;
    private $service;
}
?>