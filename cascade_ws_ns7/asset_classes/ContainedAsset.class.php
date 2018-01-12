<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/3/2018 Added code to test for NULL.
  * 11/28/2017 Added getSiteId and getSiteName.
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/12/2017 Added WSDL.
  * 9/24/2016 Changed isInContainer to isChildOf.
    Turned isInContainer to an alias of isDescendantOf.
    Added isContainedBy.
  * 8/30/2016 Fixed a bug in getParentContainerId.
  * 4/25/2016 Added isDescendantOf.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>The <code>ContainedAsset</code> class is an abstract sub-class of <code>Asset</code> and the superclass of all asset classes except the following three classes:</p>
<ul>
<li><code>Group</code></li>
<li><code>Role</code></li>
<li><code>User</code></li>
</ul>
<p>These three classes do not have parent containers. Note that although <code>Site</code> is a sub-class of <code>ScheduledPublishing</code>,
which is a sub-class of <code>ContainedAsset</code>, and hence inherits all methods defined in <code>ContainedAsset</code>, a site does not have a parent container.
Calling any method defined in <code>ContainedAsset</code> on a <code>Site</code> object will cause an exception to be thrown from <code>ContainedAsset</code>.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "folder-contained-asset" ),
        array( "getComplexTypeXMLByName" => "containered-asset" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/contained_asset.php">contained_asset.php</a></li></ul></postscript>
</documentation>
*/
abstract class ContainedAsset extends Asset
{
    const DEBUG = false;

/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    protected function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

/**
<documentation><description><p>Returns the parent container or <code>NULL</code>.</p></description>
<example>u\DebugUtility::dump( $bf->getParentContainer() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getParentContainer()
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        
        if( !is_null( $this->getParentContainerId() ) )
        {
            $parent_id    = $this->getParentContainerId();
            $parent_type  = c\T::$type_parent_type_map[ $this->getType() ];
            
            return Asset::getAsset( $this->getService(), $parent_type, $parent_id );
        }
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>parentContainerId</code> or <code>parentFolderId</code>.</p></description>
<example>echo $dd->getParentContainerId(), BR,
     $dd->getParentContainerPath(), BR;</example>
<return-type>mixed</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function getParentContainerId()
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        
        if( isset( $this->getProperty()->parentFolderId ) )
            return $this->getProperty()->parentFolderId;
        elseif( isset( $this->getProperty()->parentContainerId ) )
            return $this->getProperty()->parentContainerId;
        else
            return NULL;
    }
    
/**
<documentation><description><p>Returns <code>parentContainerPath</code> or <code>parentFolderPath</code>.</p></description>
<example>echo $dd->getParentContainerId(), BR,
     $dd->getParentContainerPath(), BR;</example>
<return-type>mixed</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Returns <code>siteId</code>.</p></description>
<example>echo $page->getSiteId(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteId() : string
    {
        if( $this->getType() == c\T::SITE )
        {
            return $this->getId();
        }
        
        if( isset( $this->getProperty()->siteId ) )
            return $this->getProperty()->siteId;
        return NULL;
    }
  
/**
<documentation><description><p>Returns <code>siteName</code>.</p></description>
<example>echo $page->getSiteName(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteName()
    {
        if( $this->getType() == c\T::SITE )
        {
            return $this->getName();
        }
        
        if( isset( $this->getProperty()->siteName ) )
            return $this->getProperty()->siteName;
        return NULL;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the asset is a direct child of the named container.</p></description>
<example>if( $page->isInContainer( $test2 ) )
    $page->move( $test1, false );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isChildOf( Container $c ) : bool
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        return $c->getId() == $this->getParentContainerId();
    }
    
/**
<documentation><description><p>An alias of <code>isDescendantOf</code>.</p></description>
<example>if( $page->isInContainer( $test2 ) )
    $page->move( $test1, false );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isContainedBy( Container $container ) : bool
    {
        return $this->isDescendantOf( $container );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether this asset is a descendant of the named container.</p></description>
<example>echo u\StringUtility::boolToString( $dd->isDescendantOf( $msc ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isDescendantOf( Container $container ) : bool
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        return $container->isAncestorOf( $this );
    }
    
/**
<documentation><description><p>An alias of <code>isDescendantOf</code>.</p></description>
<example>if( $page->isInContainer( $test2 ) )
    $page->move( $test1, false );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isInContainer( Container $container ) : bool
    {
        return $this->isDescendantOf( $container );
    }
    
/**
<documentation><description><p>Moves the asset to the destination container, calls <code>reloadProperty</code>, and returns the calling object.</p></description>
<example>if( $page->isInContainer( $test2 ) )
    $page->move( $test1, false );</example>
<return-type>Asset</return-type>
<exception>WrongAssetTypeException, NullAssetException</exception>
</documentation>
*/
    public function move( Container $new_parent, $doWorkflow=false ) : Asset
    {
        if( $this->getType() == c\T::SITE )
        {
            throw new e\WrongAssetTypeException( c\M::SITE_NO_PARENT_CONTAINER );
        }
        
        if( $new_parent == NULL )
        {
            throw new e\NullAssetException( c\M::NULL_CONTAINER );
        }
        $this->moveRename( $new_parent, "", $doWorkflow );
        $this->reloadProperty();
        
        return $this;
    }
    
/**
<documentation><description><p>Renames the asset to the new name, calls <code>reloadProperty</code>, and returns the calling object.</p></description>
<example>$p = $cascade->getAsset( a\Page::TYPE, $id );
$p->rename( 'test3' );
</example>
<return-type>Asset</return-type>
<exception>WrongAssetTypeException, EmptyValueException</exception>
</documentation>
*/
    public function rename( string $new_name, bool $doWorkflow=false ) : Asset
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
    
    private function moveRename( Container $parent_container=NULL, string $new_name="", bool $doWorkflow=false )
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