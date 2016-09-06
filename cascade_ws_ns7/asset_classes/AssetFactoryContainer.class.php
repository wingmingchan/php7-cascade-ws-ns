<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
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
<p>An <code>AssetFactoryContainer</code> object represents an asset factory container asset. This class is a sub-class of <a href="/web-services/api/asset-classes/container"><code>Container</code></a>.</p>
<h2>Structure of <code>assetFactoryContainer</code></h2>
<pre>assetFactoryContainer
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  applicableGroups
  children
    child
      id
      path
        path
        siteId
        siteName
      type
      recycled
</pre></description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/asset_factory_container.php">asset_factory_container.php</a></li></ul></postscript>
</documentation>
*/
class AssetFactoryContainer extends Container
{
    const DEBUG = false;
    const TYPE  = c\T::ASSETFACTORYCONTAINER;
    
/**
<documentation><description><p>Adds the group name to <code>applicableGroups</code> and returns the calling object.</p></description>
<example>$afc->addGroup( $group )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addGroup( Group $g ) : Asset
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }
        
        $group_name   = $g->getName();
        $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
        
        if( !in_array( $group_name, $group_array ) )
        {
            $group_array[] = $group_name;
        }
        
        $group_string = implode( ';', $group_array );
        $this->getProperty()->applicableGroups = $group_string;
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>applicableGroups</code>.</p></description>
<example>echo $afc->getApplicableGroups() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getApplicableGroups()
    {
        return $this->getProperty()->applicableGroups;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the asset factory container is applicable to the group.</p></description>
<example>if( $afc->isApplicableToGroup( $group ) )
    echo "Applicable to ", $group->getName(), BR;</example>
<return-type>bool</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function isApplicableToGroup( Group $g ) : bool
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }

        $group_name = $g->getName();
            $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
        return in_array( $group_name, $group_array );
    }

/**
<documentation><description><p>Removes the group name from <code>applicableGroups</code> and returns the calling object.</p></description>
<example>$afc->removeGroup( $group )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function removeGroup( Group $g ) : Asset
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }
        
        $group_name   = $g->getName();
        $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
            
        if( in_array( $group_name, $group_array ) )
        {
            $temp = array();
            foreach( $group_array as $group )
            {
                if( $group != $group_name )
                {
                    $temp[] = $group;
                }
            }
            $group_array = $temp;
        }
        $group_string = implode( ';', $group_array );
        $this->getProperty()->applicableGroups = $group_string;

        return $this;
    }
}
?>
