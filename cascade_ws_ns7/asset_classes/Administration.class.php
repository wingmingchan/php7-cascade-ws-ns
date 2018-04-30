<?php
/**
  * Author: Wing Ming Chan, German Drulyk
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
  *                    German Drulyk <drulykg@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 4/30/2018 Used the splat operator to replace call_user_func_array.
  * 3/30/2018 Added copyGroup, copyGroupWriteAccess, copyAFGroupAccess.
  * 2/13/2018 Added __call.
  * 2/12/2018 Added access-related methods.
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

/**
<documentation><description><p>This single method generates all methods defined in the <code>Cascade</code> class. That is to say, any method call of the form <code>$obj = $cascade->method( $params )</code> can now be called using <code>$obj = $admin->method( $params )</code>.</p></description>
<example>$dd = $admin->getDataDefinition( $dd_id );</example>
<return-type>mixed</return-type>
<exception>NoSuchMethodException</exception>
</documentation>
*/
    function __call( string $func, array $params )
    {
        return $this->cascade->$func( ...$params );
    }

/**
<documentation><description><p>Associates types of assets with the corresponding metadata set and returns the calling object.
An entry in the param array should be a string (asset type) as the key and an array as its value.
The inner array should contain information about the folder containing assets of the desired type and information about the metadata set.
Information can be <code>Asset</code> objects, or a single ID string, or an array of two strings (the site name and the folder path).</p></description>
<example>   $admin->associateAssetsWithMetadataSets( array(
    // the type of assets to be associated with the metadata set
    a\Folder::TYPE => array(
        // the folder containing the assets
        a\Folder::TYPE      => array( "_cascade/blocks", $site_name ),
        // the metadata set
        a\MetadataSet::TYPE => $admin->getAsset(
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
<documentation><description><p>Applies the named global function(s) to all assets of the specified types and returns the calling object. If <code>$folder_path</code> is provided, then only assets in the corresponding folder will be affected. The third parameter, name(s) of global function(s), can a single string or an array of strings. The fourth parameter, when present, can store paramters to be passed into the global function(s). The fifth parameter, when present, should be array used to stored returned values. The last parameters should be type strings. There should be at least one type string supplied.</p></description>
<example>$admin->applyToAssets(
    $site_name,          // the site name
    $folder_path,        // the folder to be traversed, can be NULL
    "assetTreePublish",  // global function name
    $null,               // $params to be passed into global functions
    $null,               // the $results array to store return values
    File::TYPE           // types of assets
);
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
<documentation><description><p>Clears group, user, and all access from the folder and returns the calling object.</p></description>
<example>$admin->clearFolderAccess(
    $site_name, "xslt/building-library/java-extension", true );
</example>
<exception></exception>
</documentation>
*/
    public function clearFolderAccess(
        string $site_name, string $folder_path, 
        bool $applied_to_children=true ) : Administration
    {
        $this->cascade->clearPermissions(
            Folder::TYPE, $folder_path, $site_name, $applied_to_children );
        return $this;
    }

/**
<documentation><description><p>Copies the access of asset factory containers and
asset factories from the old group to the new group by traversing a site,
and returns the calling object.</p></description>
<example>$admin->copyAFGroupAccess( $old_group, $new_group, "cancer" );
</example>
<exception></exception>
</documentation>
*/
    public function copyAFGroupAccess(
        Group $old_group, Group $new_group, string $site_name ) : Administration
    {
        $params = array(
            "old-group" => $old_group,
            "new-group" => $new_group
        );
        
        $this->getSite( $site_name )->
            getRootAssetFactoryContainerAssetTree( $site_name )->traverse(
            array(
                AssetFactoryContainer::TYPE => array( "assetTreeCopyAFGroupAccess" ),
                AssetFactory::TYPE =>          array( "assetTreeCopyAFGroupAccess" )
            ), 
            $params );

        return $this;
    }

/**
<documentation><description><p>Creates a new group bearing the new name,
copies all settings of the existing group to the new group, and returns the calling object.</p></description>
<example>$admin->copyGroup( $old_group, $new_group_name );
</example>
<exception>EmptyNameException</exception>
</documentation>
*/
    public function copyGroup(
        Group $current_group, string $new_group_name ) : Administration
    {
        if( trim( $new_group_name ) == "" )
        {
            throw new e\EmptyNameException( c\M::EMPTY_GROUP_NAME );
        }
        
        $group = $this->cascade->getGroup( $new_group_name );
        
        if( !is_null( $group ) )
        {
            echo "The group $new_group_name already exists", BR;
        }
        else
        {
            $current_group->copyGroup( $new_group_name );
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Copies the read access of assets from the old group to the
new group by traversing a site (or a subfolder of the site), and returns the calling object. Ten types of assets are affected by this method: DataDefinitionBlock,
FeedBlock, IndexBlock, TextBlock, XmlBlock, File, Folder, Page, Reference, and Symlink.</p></description>
<example>$admin->copyGroupReadAccess( $old_group, $new_group, "cancer" );
</example>
<exception></exception>
</documentation>
*/
    public function copyGroupReadAccess(
        Group $old_group, Group $new_group,
        string $site_name, string $folder_path=NULL ) : Administration
    {
        $this->copyGroupAccess( $old_group, $new_group,
            $site_name, $folder_path, c\T::READ );
    
        return $this;
    }
    
/**
<documentation><description><p>Copies the write access of assets from the old group to the
new group by traversing a site (or a subfolder of the site), and returns the calling object. Ten types of assets are affected by this method: DataDefinitionBlock,
FeedBlock, IndexBlock, TextBlock, XmlBlock, File, Folder, Page, Reference, and Symlink.</p></description>
<example>$admin->copyGroupWriteAccess( $old_group, $new_group, "cancer" );
</example>
<exception></exception>
</documentation>
*/
    public function copyGroupWriteAccess(
        Group $old_group, Group $new_group,
        string $site_name, string $folder_path=NULL ) : Administration
    {
        $this->copyGroupAccess( $old_group, $new_group,
            $site_name, $folder_path, c\T::WRITE );
            
        return $this;
    }

/**
<documentation><description><p>Grants access to a user or group. <code>$a</code> must be either a <code>User</code> or <code>Group</code> object.</p></description>
<example>$admin->grantAccess( $ug, Folder::TYPE, $folder_path, $site_name,
    $applied_to_children, c\T::READ );
</example>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function grantAccess( Asset $a, string $type, string $path, 
        string $site_name, bool $applied_to_children=true, 
        string $level=c\T::READ ) : Administration
    {
        $this->cascade->grantAccess( $a, $type, $path, $site_name,
            $applied_to_children, $level );
        return $this;
    }

/**
<documentation><description><p>Grants folder no access (none) to all.</p></description>
<example>$admin->grantFolderNoneAccessToAll( $site_name, $folder_path, true );
</example>
<exception></exception>
</documentation>
*/
    public function grantFolderNoneAccessToAll( string $site_name, $folder_path,
        bool $applied_to_children=true ) : Administration
    {
        $this->setAllLevel(
            Folder::TYPE, $site_name, $folder_path, c\T::NONE, $applied_to_children );
        return $this;
    }

/**
<documentation><description><p>Grants folder read access to all.</p></description>
<example>$admin->grantFolderReadAccessToAll( $site_name, $folder_path, true );
</example>
<exception></exception>
</documentation>
*/
    public function grantFolderReadAccessToAll( string $site_name, $folder_path,
        bool $applied_to_children=true ) : Administration
    {
        $this->setAllLevel(
            Folder::TYPE, $site_name, $folder_path, c\T::READ, $applied_to_children );
        return $this;
    }

/**
<documentation><description><p>Grants folder read access to a user or group. <code>$ug</code> must be either a <code>User</code> or <code>Group</code> object.</p></description>
<example>$admin->grantFolderReadAccessToUserGroup( $site_name, $folder_path, true, $group );
</example>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function grantFolderReadAccessToUserGroup(
        string $site_name, string $folder_path, 
        bool $applied_to_children=true, Asset $ug ) : Administration
    {
        $this->cascade->grantAccess( $ug, Folder::TYPE, $folder_path, $site_name,
            $applied_to_children, c\T::READ );
        return $this;
    }

/**
<documentation><description><p>Grants folder write access to all.</p></description>
<example>$admin->grantFolderWriteAccessToAll( $site_name, $folder_path, true );
</example>
<exception></exception>
</documentation>
*/
    public function grantFolderWriteAccessToAll( string $site_name, $folder_path,
        bool $applied_to_children=true ) : Administration
    {
        $this->setAllLevel(
            Folder::TYPE, $site_name, $folder_path, c\T::WRITE, $applied_to_children );
        return $this;
    }

/**
<documentation><description><p>Grants folder write access to a user or group. <code>$ug</code> must be either a <code>User</code> or <code>Group</code> object.</p></description>
<example>$admin->grantFolderWriteAccessToUserGroup(
    $site_name, $folder_path, true, $group );
</example>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function grantFolderWriteAccessToUserGroup(
        string $site_name, string $folder_path, 
        bool $applied_to_children=true, Asset $ug ) : Administration
    {
        $this->cascade->grantAccess( $ug, Folder::TYPE, $folder_path, $site_name,
            $applied_to_children, c\T::WRITE );
        return $this;
    }

/**
<documentation><description><p>Grants site no access (none) to all.</p></description>
<example>$admin->grantSiteNoneAccessToAll( $site_name, true );
</example>
<exception></exception>
</documentation>
*/
    public function grantSiteNoneAccessToAll( string $site_name, 
        bool $applied_to_children=true ) : Administration
    {
        $this->setAllLevel(
            Folder::TYPE, $site_name, "/", c\T::NONE, $applied_to_children );
        return $this;
    }

/**
<documentation><description><p>Grants site read access to all.</p></description>
<example>$admin->grantSiteReadAccessToAll( $site_name, true );
</example>
<exception></exception>
</documentation>
*/
    public function grantSiteReadAccessToAll( string $site_name, 
        bool $applied_to_children=true ) : Administration
    {
        $this->setAllLevel(
            Folder::TYPE, $site_name, "/", c\T::READ, $applied_to_children );
        return $this;
    }

/**
<documentation><description><p>Grants site read access to a user or group. <code>$ug</code> must be either a <code>User</code> or <code>Group</code> object.</p></description>
<example>$admin->grantSiteReadAccessToUserGroup( $site_name, true, $group );
</example>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function grantSiteReadAccessToUserGroup(
        string $site_name, 
        bool $applied_to_children=true, Asset $ug ) : Administration
    {
        $this->cascade->grantAccess( $ug, Folder::TYPE, "/", $site_name,
            $applied_to_children, c\T::READ );
        return $this;
    }

/**
<documentation><description><p>Grants site write access to all.</p></description>
<example>$admin->grantSiteWriteAccessToAll( $site_name, true );
</example>
<exception></exception>
</documentation>
*/
    public function grantSiteWriteAccessToAll( string $site_name, 
        bool $applied_to_children=true ) : Administration
    {
        $this->setAllLevel(
            Folder::TYPE, $site_name, "/", c\T::WRITE, $applied_to_children );
        return $this;
    }

/**
<documentation><description><p>Grants site write access to a user or group. <code>$ug</code> must be either a <code>User</code> or <code>Group</code> object.</p></description>
<example>$admin->grantSiteWriteAccessToUserGroup( $site_name, true, $group );
</example>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function grantSiteWriteAccessToUserGroup(
        string $site_name, 
        bool $applied_to_children=true, Asset $ug ) : Administration
    {
        $this->cascade->grantAccess( $ug, Folder::TYPE, "/", $site_name,
            $applied_to_children, c\T::WRITE );
        return $this;
    }

/**
<documentation><description><p>Publishes all files by issuing a publish command on each individual file and returns the calling object. If <code>$folder_path</code> is provided, then only files in the corresponding folder will be published.</p></description>
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
<documentation><description><p>Publishes all files and pages by issuing a publish command on each individual file/page and returns the calling object. If <code>$folder_path</code> is provided, then only files/pages in the corresponding folder will be published.</p></description>
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
<documentation><description><p>Publishes all pages by issuing a publish command on each individual page and returns the calling object. If <code>$folder_path</code> is provided, then only pages in the corresponding folder will be published.</p></description>
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
<documentation><description><p>Removes phantom nodes of both type A and B from pages and data definition blocks and returns the calling object. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be affected. If an array is also supplied, then the array will store paths of all pages/blocks that are modified.</p></description>
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
<documentation><description><p>Removes phantom values from pages and data definition blocks and returns the calling object. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be affected. If an array is also supplied, then the array will store paths of all pages/blocks that are modified.</p></description>
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
<documentation><description><p>Reports phantom nodes in pages and data definition blocks and returns the calling object. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be examined. The array for storing the report must be supplied.</p></description>
<example>$admin->reportPhantomNodes( "about" );</example>
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
<documentation><description><p>Reports phantom values in pages and data definition blocks and returns the calling object. If <code>$folder_path</code> is provided, then only pages/blocks in the corresponding folder will be examined. The array for storing the report must be supplied.</p></description>
<example>$admin->reportPhantomNodes( "about" );</example>
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

/**
<documentation><description><p>Sets the access for all to an asset. <code>$type</code> is the asset type.</p></description>
<example>$admin->setAllLevel( Folder::TYPE, $site_name, $folder_path, c\T::READ, true );
</example>
<exception></exception>
</documentation>
*/
    public function setAllLevel( 
        string $type, string $site_name, string $path, 
        string $level=c\T::NONE, bool $applied_to_children=true ) : Administration
    {
        $ari = $this->cascade->getAccessRights( $type, $path, $site_name );
        $ari->setAllLevel( $level );
        $this->cascade->setAccessRights( $ari, $applied_to_children );
        return $this;
    }

    private function copyGroupAccess(
        Group $old_group, Group $new_group,
        string $site_name, string $folder_path=NULL,
        string $level
    )
    {
        // read by default
        $global_function_name = "assetTreeCopyGroupReadAccess";
        
        if( $level == c\T::WRITE )
        {
            $global_function_name = "assetTreeCopyGroupWriteAccess";
        }
        
        $params = array(
            "cascade"   => $this->cascade,
            "old-group" => $old_group,
            "new-group" => $new_group
        );
        return $this->applyToAssets(
            $site_name, $folder_path,
            $global_function_name, 
            $params, $null, 
            DataDefinitionBlock::TYPE, 
            FeedBlock::TYPE, 
            IndexBlock::TYPE, 
            TextBlock::TYPE, 
            XmlBlock::TYPE, 
            File::TYPE,
            Folder::TYPE,
            Page::TYPE,
            Reference::TYPE,
            Symlink::TYPE
        );
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