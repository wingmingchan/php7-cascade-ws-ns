<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/2/2018 Added REST code.
  * 4/25/2016 Added isAncestorOf and contains.
  * 4/22/2016 Added isParentOf.
  * 5/28/2015 Added namespaces.
  * 6/10/2014 Added a dummy string to parentFolderId in edit.
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
<p>The <code>Container</code> class is the superclass of <code>AssetFactoryContainer</code>,
<code>ConnectorContainer</code>, <code>ContentTypeContainer</code>, <code>DataDefinitionContainer</code>, <code>Folder</code>,
<code>MetadataSetContainer</code>, <code>PageConfigurationSetContainer</code>, <code>PublishSetContainer</code>,
<code>SiteDestinationContainer</code>, <code>TransportContainer</code>, and <code>WorkflowDefinitionContainer</code>.
It is an abstract class and defines all methods commonly shared by its sub-classes.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/container.php">container.php</a></li></ul></postscript>
</documentation>
*/
abstract class Container extends ContainedAsset
{
    const DEBUG = false;

/**
<documentation><description><p>The constructor, overriding the parent method to process child assets.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    protected function __construct(
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->children               = array();
        $this->container_children_ids = array();
        
        
        if( isset( $this->getProperty()->children ) )
        {
            if( ( $this->getService()->isSoap() && 
                isset( $this->getProperty()->children->child ) ) ||
                ( $this->getService()->isRest() && 
                isset( $this->getProperty()->children ) ) )
                $this->processChildren();
        }
    }
    
/**
<documentation><description><p>An alias of <code>isAncestorOf( Asset $asset )</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function contains( Asset $asset ) : bool
    {
        return $this->isAncestorOf( $asset );
    }

/**
<documentation><description><p>Deletes all children in the container and returns the calling object.
This method uses the <code>aohs\AssetOperationHandlerService::batch</code> operation.
There is no need to call <code>Container::edit</code> because the container object itself is not modified.</p></description>
<example>$folder->deleteAllChildren();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function deleteAllChildren() : Asset
    {
        if( count( $this->children ) == 0 )
        {
            return $this;
        }
        
        $service = $this->getService();
        
        if( $service->isSoap() )
        {
            foreach( $this->children as $child )
            {
                $child_id              = $child->getId();
                $child_type            = $child->getType();
                $identifier            = $service->createId( $child_type, $child_id );
                $operation             = new \stdClass();
                $delete_op             = new \stdClass();
                $delete_op->identifier = $identifier;
                $operation->delete     = $delete_op;
                $operations[]          = $operation;
            }
        
            $service->batch( $operations );
        }
        // 8.7.1 no batch
        elseif( $service->isRest() )
        {
            foreach( $this->children as $child )
            {
                $service->delete(
                    $service->createId( $child->getType(), $child->getId() ) );
            }
        }
        
        $this->children               = array();
        $this->container_children_ids = array();
        
        return $this;
    }

/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example>$folder->getMetadata()->setDisplayName( "Test" )->getHostAsset()->edit();</example>
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
        $asset = new \stdClass();
        $property = $this->getProperty();
        
        if( $property->path == "/" )
        {
            if( $this->getType() == Folder::TYPE ) // type is NOT in property
                $property->parentFolderId = 'some dummy string';
            else
                $property->parentContainerId = 'some dummy string';
        }
        
        $asset->{ $p = $this->getPropertyName() } = $property;

        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                c\M::EDIT_ASSET_FAILURE . $service->getMessage() );
        }
        return $this->reloadProperty();
    }
    
/**
<documentation><description><p>Returns an <a href="http://www.upstate.edu/web-services/api/asset-tree/index.php"><code>AssetTree</code></a> object rooted at this container.</p></description>
<example>u\DebugUtility::dump( $folder->getAssetTree() );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getAssetTree() : AssetTree
    {
        return new AssetTree( $this );
    }
    
/**
<documentation><description><p>Returns an array of <a href="http://www.upstate.edu/web-services/api/property-classes/child.php"><code>p\Child</code></a> objects.</p></description>
<example>u\DebugUtility::dump( $folder->getChildren() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getChildren() : array
    {
        return $this->children;
    }

/**
<documentation><description><p>Returns an array of id strings of child containers of the same type.
For example, when this method is called through an <a href="http://www.upstate.edu/web-services/api/asset-classes/asset-factory-container.php"><code>AssetFactoryContainer</code></a> object,
then the id's returned are id's of all children of the type asset factory containers of the current container.</p></description>
<example>u\DebugUtility::dump( $folder->getContainerChildrenIds() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getContainerChildrenIds() : array
    {
        return $this->container_children_ids;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether this container is an ancestor of the named asset.</p></description>
<example>echo u\StringUtility::boolToString( $folder2->isAncestorOf( $folder1) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function isAncestorOf( Asset $asset ) : bool
    {
        if( !is_subclass_of( $asset, "cascade_ws_asset\ContainedAsset" ) )
            throw new e\WrongAssetTypeException( 
                "The asset is not a type of ContainedAsset object" );
            
        // case 1: is the parent
        if( $this->isParentOf( $asset ) )
            return true;
        // case 2: $asset is a root asset; use short-circuiting here
        elseif( $asset->getParentContainer()->getPath() == "/" )
            return false;
        // recursive call
        elseif( $this->isAncestorOf( $asset->getParentContainer() ) )
            return true;
        else
            return false;
    }
            
/**
<documentation><description><p>Returns a bool, indicating whether this container is the parent container of the named asset.</p></description>
<example>echo u\StringUtility::boolToString( $folder2->isParentOf( $folder1) ), BR;</example>
<return-type>bool</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function isParentOf( Asset $asset ) : bool
    {
        if( !is_subclass_of( $asset, "cascade_ws_asset\ContainedAsset" ) )
            throw new e\WrongAssetTypeException( 
                "The asset is not a type of ContainedAsset object" );
            
        return $asset->isChildOf( $this );
    }
            
/**
<documentation><description><p>Turns the <code>Container</code> object into a <code>p\Child</code> object and returns it.
This method is used by the <a href="http://www.upstate.edu/web-services/api/asset-tree/index.php"><code>AssetTree</code></a> class.</p></description>
<example>u\DebugUtility::dump( $folder2->toChild() );</example>
<return-type>Child</return-type>
<exception></exception>
</documentation>
*/
    public function toChild() : p\Child
    {
        $child_std       = new \stdClass();
        $child_std->id   = $this->getId();
        $child_std->type = $this->getType();
        
        $asset_path_std            = new \stdClass();
        $asset_path_std->path      = $this->getPath();
        $asset_path_std->siteId    = $this->getSiteId();
        $asset_path_std->siteName  = $this->getSiteName();
        
        $child_std->path     = $asset_path_std;
        $child_std->recycled = false;
        $child               = new p\Child( $child_std );
        return $child;
    }
    
    private function processChildren()
    {
        if( $this->getService()->isSoap() )
            $children = $this->getProperty()->children->child;
        elseif( $this->getService()->isRest() )
            $children = $this->getProperty()->children;
        
        if( !is_array( $children ) )
        {
            $children = array( $children );
        }
        
        foreach( $children as $child )
        {
        
            $this->children[] = new p\Child( $child );
            
            if( $child->type == $this->getType() )
            {
                $this->container_children_ids[] = $child->id;
            }
        }
    }
    
    private $children;
    private $container_children_ids;
}
?>