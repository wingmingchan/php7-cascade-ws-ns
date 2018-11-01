<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 10/12/2018 Fixed bugs in addTag, isInTags and removeTag.
  * 5/18/2018 Added addTag, isInTags, hasTag and removeTag.
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
<documentation><description><p>Adds a tag, if it does not exist, and returns the calling object.</p></description>
<example>$page->->addTag( "education" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addTag( string $t ) : Asset
    {
        $std = new \stdClass();
        $std->name = $t;
        
        if( $this->getService()->isSoap() )
        {
            // empty
            if( !isset( $this->getProperty()->tags->tag ) )
            {
                $this->getProperty()->tags->tag = $std;
            }
            // one other tag
            elseif( !is_array( $this->getProperty()->tags->tag ) )
            {
                if( $this->getProperty()->tags->tag->name != $t )
                {
                	$cur_tag = $this->getProperty()->tags->tag;
                	$this->getProperty()->tags->tag = array();
                	$this->getProperty()->tags->tag[] = $cur_tag;
                	$this->getProperty()->tags->tag[] = $std;
                }
            }
            // tag not in array
            elseif( !in_array( $std, $this->getProperty()->tags->tag ) )
            {
                $this->getProperty()->tags->tag[] = $std;
            }
        }
        elseif( $this->getService()->isRest() )
        {
            if( !in_array( $std, $this->getProperty()->tags ) )
            {
                $this->getProperty()->tags[] = $std;
            }
        }
        
        return $this;
    }
    
    public function isInTags( string $t ) : bool
    {
    	if( $this->getService()->isSoap() )
    	{
    		if( !isset( $this->getProperty()->tags->tag ) )
    		{
    			return false;
    		}
    		elseif( !is_array( $this->getProperty()->tags->tag ) )
            {
            	if( $this->getProperty()->tags->tag->name != $t )
            		return false;
            	else
            		return true;
            }
            else
            {
            	foreach( $this->getProperty()->tags->tag as $tag )
            	{
            		if( $tag->name == $t )
            		{
            			return true;
            		}
            	}
            	return false;
            }
        }
        elseif( $this->getService()->isRest() )
        {
        	$std = new \stdClass();
        	$std->name = $t;
        	
        	if( in_array( $std, $this->getProperty()->tags ) )
        	{
        		return true;
        	}
        	return false;
        }
    }
    
    public function hasTag( string $t ) : bool
    {
    	return $this->isInTags( $t );
    }
    
    public function removeTag( string $t ) : Asset
    {
    	if( $this->isInTags( $t ) )
    	{
    		if( $this->getService()->isSoap() )
        	{
        		if( isset( $this->getProperty()->tags->tag ) )
        		{
        			// the only tag
        			if( !is_array( $this->getProperty()->tags->tag ) &&
        				$this->getProperty()->tags->tag->name == $t )
        			{
        				$this->getProperty()->tags = new \stdClass();
        			}
        			else
        			{
        				$temp = array();
        				
        				foreach( $this->getProperty()->tags->tag as $tag )
        				{
        					if( $tag->name != $t )
        					{
        						$temp[] = $tag;
        					}
        				}
        				$this->getProperty()->tags->tag = $temp;
        			}
        		}
        	}
    		elseif( $this->getService()->isRest() )
        	{
        		$temp = array();
        		
        		foreach( $this->getProperty()->tags as $tag )
        		{
        			if( $tag->name != $t )
        			{
        				$temp[] = $tag;
        			}
        		}
        		$this->getProperty()->tags = $temp;
        	}
    	}
    	
    	return $this;
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
<documentation><description><p>Returns <code>tags</code> (an <code>stdClass</code> object for SOAP, and an array for REST).</p></description>
<example>u\DebugUtility::dump( $page->getTags() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTags()
    {
        return $this->getProperty()->tags;
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