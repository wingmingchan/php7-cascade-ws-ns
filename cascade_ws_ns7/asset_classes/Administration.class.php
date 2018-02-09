<?php
/**
  * Author: Wing Ming Chan, German Drulyk
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
  *                    German Drulyk <drulykg@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/6/2018 Added publishAll.
  * 2/2/2018 Class created.
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
<h2>Introduction</h2>
<p>The <code>Administration</code> class is a high-level class that can be used to manage sites.
The class provides methods to modify assets at the site/folder level using asset tree traversal.
An object named <code>$admin</code> should be initialized in an authentication file:</p>
<pre>
$admin = new a\Administration( $cascade );
</pre>
</description>
<todo>
clearPermissions
grantSiteReadAccessToUserGroup
grantSiteWriteAccessToUserGroup
grantFolderReadAccessToUserGroup
grantFolderWriteAccessToUserGroup
grantSiteReadAccessToAll
grantFolderReadAccessToAll
grantSiteWriteAccessToAll
grantFolderWriteAccessToAll
grantSiteNoAccessToAll
grantFolderNoAccessToAll
</todo>
<postscript></postscript>
</documentation>
*/
class Administration
{
    const DEBUG      = false;
    const DUMP       = false;
    const NAME_SPACE = "cascade_ws_asset";
    
/**
<documentation><description><p>The constructor. Note that the <code>$cascade</code> object is passed into the constructor.</p></description>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( Cascade $cascade )
    {
        if( $cascade == NULL )
        {
            throw new e\NullServiceException( c\M::NULL_CASCADE );
        }
            
        $this->cascade = $cascade;
        $this->service = $cascade->getService();
    }
/*/
	public function clearPermissions(
		string $site_name, string $folder_path=NULL,
		bool $applied_to_children=true ) : Administration
	{
		$this
	}
/*/
/**
<documentation><description><p>Associates types of assets with the corresponding metadata set.
An entry in the param array should be a string (asset type) as the key and an array as its value.
The inner array should contain information about the folder containing assets of the desired type and information about the metadata set.
Information can be <code>Asset</code> objects, or a single ID string, or an array of two strings (the site name and the folder path).</p></description>
<example>
    $admin->associateAssetsWithMetadataSets( array(
        // the type of assets to be associated with the metadata set
        a\Folder::TYPE => array(
            // the folder containing the assets
            a\Folder::TYPE      => array( "_cascade/blocks", $site_name ),
            // the metadata set
            a\MetadataSet::TYPE => $cascade->getAsset(
                a\MetadataSet::TYPE, "6188622e8b7ffe8377b637e84e639b54" )
        ),
        a\XmlBlock::TYPE => array(
            a\Folder::TYPE      => "48a581eb8b7ffe834bd90c8f2ef6b1bb",
            a\MetadataSet::TYPE => "618861da8b7ffe8377b637e8ad3dd499"
        )
    ) );
</example>
<return-type>Administration</return-type>
<exception>NoSuchTypeException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Publishes all publishable assets by issuing a publish command on each asset. If <code>$folder_path</code> is provided, then only assets in the corresponding folder will be published. There should be at least one type string supplied.</p></description>
<example>$admin->publishAllFiles( "about" ); // all files in the site

$admin->publishAllFiles( "about", "images" ); // all files in the images folder
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function applyToAssets(
        string $site_name, string $folder_path=NULL,
        $global_function_names, // string or array of strings
        array &$params=NULL,
        array &$results=NULL,
        string $type, string ...$more_types
    ) : Administration
    {
    	if( !is_array( $global_function_names ) )
    	{
    		$global_function_names = array( $global_function_names );
    	}
    
        foreach( array_merge( array( $type ), $more_types ) as $v )
        {
            $type_array[ $v ] = $global_function_names;
        }
        
        $this->getFolder( $site_name, $folder_path )->getAssetTree()->
        	traverse( $type_array, $params, $results );
    
        return $this;
    }
    
/**
<documentation><description><p>Publishes all files by issuing a publish command on each individual file. If <code>$folder_path</code> is provided, then only files in the corresponding folder will be published.</p></description>
<example>$admin->publishAllFiles( "about" ); // all files in the site

$admin->publishAllFiles( "about", "images" ); // all files in the images folder
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function publishAllFiles(
        string $site_name, string $folder_path=NULL ) : Administration
    {
        return $this->applyToAssets(
        	$site_name, $folder_path, "assetTreePublish", $null, $null, File::TYPE );
    }
    
/**
<documentation><description><p>Publishes all files and pages by issuing a publish command on each individual file/page. If <code>$folder_path</code> is provided, then only files/pages in the corresponding folder will be published.</p></description>
<example>$admin->publishAllFilesPages( "about" ); // all files in the site
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function publishAllFilesPages(
        string $site_name, string $folder_path=NULL ) : Administration
    {
        return $this->applyToAssets(
        	$site_name, $folder_path, "assetTreePublish", 
        	$null, $null, File::TYPE, Page::TYPE );
    }
    
/**
<documentation><description><p>Publishes all pages by issuing a publish command on each individual page. If <code>$folder_path</code> is provided, then only pages in the corresponding folder will be published.</p></description>
<example>$admin->publishAllPages( "about" ); // all files in the site
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function publishAllPages(
        string $site_name, string $folder_path=NULL ) : Administration
    {
        return $this->applyToAssets(
        	$site_name, $folder_path, "assetTreePublish", $null, $null, Page::TYPE );
    }
    
/**
<documentation><description><p>Removes phantom nodes of both type A and B from pages and data definition blocks. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be affected. If an array is also supplied, then the array will store paths of all pages/blocks that are modified.</p></description>
<example>$admin->removePhantomNodes( "about" );
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function removePhantomNodes(
        string $site_name, string $folder_path=NULL,
        array &$results=NULL ) : Administration
    {
        return $this->applyToAssets(
        	$site_name, $folder_path, "assetTreeRemovePhantomNodes", 
        	$null, $results, DataDefinitionBlock::TYPE, Page::TYPE );
    }
    
/**
<documentation><description><p>Removes phantom values from pages and data definition blocks. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be affected. If an array is also supplied, then the array will store paths of all pages/blocks that are modified.</p></description>
<example>$admin->removePhantomValues( "about" );
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function removePhantomValues(
        string $site_name, string $folder_path=NULL,
        array &$results=NULL ) : Administration
    {
        return $this->applyToAssets(
        	$site_name, $folder_path, "assetTreeRemovePhantomValues", 
        	$null, $results, DataDefinitionBlock::TYPE, Page::TYPE );
    }
    
/**
<documentation><description><p>Reports phantom nodes in pages and data definition blocks. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be examined. The array for storing the report must be supplied.</p></description>
<example>
$admin->reportPhantomNodes( "about" );
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function reportPhantomNodes(
        string $site_name, string $folder_path=NULL, array &$results ) : Administration
    {
    	return $this->applyToAssets(
        	$site_name, $folder_path, "assetTreeReportPhantomNodes", 
        	$null, $results, DataDefinitionBlock::TYPE, Page::TYPE );
    }
    
/**
<documentation><description><p>Reports phantom values in pages and data definition blocks. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be examined. The array for storing the report must be supplied.</p></description>
<example>
$admin->reportPhantomNodes( "about" );
</example>
<return-type>Administration</return-type>
<exception></exception>
</documentation>
*/
    public function reportPhantomValues(
        string $site_name, string $folder_path=NULL, array &$results  ) : array
    {
    	return $this->applyToAssets(
        	$site_name, $folder_path, "assetTreeReportPhantomValues", 
        	$null, $results, DataDefinitionBlock::TYPE, Page::TYPE );
    }
    
    private function getAssetWithParam( string $type, $param ) : Asset
    {
        if( is_string( $param ) && $this->service->isHexString( $param ) )
        {
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
    
    private function getFolder( string $site_name, string $folder_path=NULL ) : Folder
    {
    	if( !is_null( $folder_path ) )
        {
            $folder = $this->cascade->getAsset( Folder::TYPE, $folder_path, $site_name );
        }
        else
        {
            $folder = $this->cascade->getSite( $site_name )->getBaseFolder();
        }
        return $folder;
    }
    
    private $cascade;
    private $service;
}
?>