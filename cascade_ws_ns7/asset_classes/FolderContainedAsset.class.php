<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 11/28/2017 Class created.
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
abstract class FolderContainedAsset extends ContainedAsset
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
    public function getParentFolder()
    {
        if( $this->getParentFolderId() != NULL )
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
    public function getParentFolderId()
    {
        if( isset( $this->getProperty()->parentFolderId ) )
            return $this->getProperty()->parentFolderId;
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
    public function getParentFolderPath()
    {
        if( isset( $this->getProperty()->parentFolderPath ) )
            return $this->getProperty()->parentFolderPath;
        else
            return NULL;
    }
    
/**
<documentation><description><p>An alias of <code>isDescendantOf</code>.</p></description>
<example>if( $page->isInContainer( $test2 ) )
    $page->move( $test1, false );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isInFolder( Folder $folder ) : bool
    {
        return $this->isDescendantOf( $folder );
    }
}
?>