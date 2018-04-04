<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/3/2018 Added code to test for NULL.
  * 12/15/2017 Updated documentation.
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
<p>The <code>FolderContainedAsset</code> class is an abstract sub-class of <code>ContainedAsset</code> and the superclass of all asset classes representing assets that can be found in folders.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "folder-contained-asset" ),
        array( "getComplexTypeXMLByName" => "containered-asset" ),
    ) );
return $doc_string;
?>
</description>
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
<documentation><description><p>Returns the parent folder or <code>NULL</code>.</p></description>
<example>u\DebugUtility::dump( $bf->getParentFolder() );</example>
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
<documentation><description><p>Returns <code>getParentFolderId</code> or NULL.</p></description>
<example>echo $dd->getParentFolderId(), BR,
     $dd->getParentFolderPath(), BR;</example>
<return-type>mixed</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function getParentFolderId()
    {
        if( isset( $this->getProperty()->parentFolderId ) )
            return $this->getProperty()->parentFolderId;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>parentFolderPath</code> or NULL.</p></description>
<example>echo $dd->getParentFolderId(), BR,
     $dd->getParentFoldPath(), BR;</example>
<return-type>mixed</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function getParentFolderPath()
    {
        if( isset( $this->getProperty()->parentFolderPath ) )
            return $this->getProperty()->parentFolderPath;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>tags</code> (an <code>stdClass</code> object).</p></description>
<example>u\DebugUtility::dump( $page->getTags() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getTags()
    {
        return $this->getProperty()->tags;
    }
    
    public function AddTag( string $t )
    {
    	$std = new \stdClass();
    	$std->name = $t;
    	
    	if( !in_array( $std, $this->getProperty()->tags ) )
    	{
    		$this->getProperty()->tags[] = $std;
    	}
    	
        return $this;
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