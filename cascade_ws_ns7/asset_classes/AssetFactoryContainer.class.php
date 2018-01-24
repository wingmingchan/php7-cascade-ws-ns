<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 1/2/2018 Added code to test for NULL.
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 2/22/2017 Added addGroupName.
  * 1/10/2017 Added JSON structure and JSON dump.
  * 9/7/2016 Added getDescription and setDescription.
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
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>An <code>AssetFactoryContainer</code> object represents an asset factory container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>assetFactoryContainer</code></h2>
<pre>SOAP:
assetFactoryContainer
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  applicableGroups
  description
  children
    child
      id
      path
        path
        siteId
        siteName
      type
      recycled
      
REST:
assetFactoryContainer
  applicableGroups
  children (array of stdClass)
    id
    path (stdClass)
      path
      siteId
    type
    recycled
  description
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  name
  id
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "assetFactoryContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/asset_factory_container.php">asset_factory_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/assetfactorycontainer/a14dd3958b7ffe830539acf004d370d7

{
  "asset":{
    "assetFactoryContainer":{
      "children":[
      {
        "id":"a14dd57d8b7ffe830539acf083f86623",
        "path":{
          "path":"Upstate/New Block",
          "siteId":"a14dbc498b7ffe830539acf0443910e3"
        },
        "type":"assetfactory",
        "recycled":false
      },
      {
        "id":"a14dd4b18b7ffe830539acf0f5859fed",
        "path":{
          "path":"Upstate/New Folder",
          "siteId":"a14dbc498b7ffe830539acf0443910e3"
        },
        "type":"assetfactory",
        "recycled":false
      } ],
      "parentContainerId":"a14dd32b8b7ffe830539acf0fb523e5b",
      "parentContainerPath":"/",
      "path":"Upstate",
      "siteId":"a14dbc498b7ffe830539acf0443910e3",
      "siteName":"velocity-test",
      "name":"Upstate",
      "id":"a14dd3958b7ffe830539acf004d370d7"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre>
</postscript>
</documentation>
*/
class AssetFactoryContainer extends Container
{
    const DEBUG = false;
    const TYPE  = c\T::ASSETFACTORYCONTAINER;
    
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

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
        
        if( isset( $this->getProperty()->applicableGroups ) )
            $group_string = $this->getProperty()->applicableGroups;
        else
            $group_string = "";
        
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
<documentation><description><p>Adds the group name to <code>applicableGroups</code> and returns the calling object.</p></description>
<example>$afc->addGroupName( "22q" )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addGroupName( string $group_name ) : Asset
    {
        // check the existence of the group
        $group = Asset::getAsset( $this->getService(), Group::TYPE, $group_name );
        
        if( isset( $this->getProperty()->applicableGroups ) )
            $group_string = $this->getProperty()->applicableGroups;
        else
            $group_string = "";

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
        if( isset( $this->getProperty()->applicableGroups ) )
            return $this->getProperty()->applicableGroups;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>description</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDescription()
    {
        if( isset( $this->getProperty()->description ) )
            return $this->getProperty()->description;
        return NULL;
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
        
        if( isset( $this->getProperty()->applicableGroups ) )
            $group_string = $this->getProperty()->applicableGroups;
        else
            $group_string = "";
            
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
        
        if( isset( $this->getProperty()->applicableGroups ) )
            $group_string = $this->getProperty()->applicableGroups;
        else
            $group_string = "";
            
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
/**
<documentation><description><p>Sets <code>description</code>,
and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDescription( string $desc=NULL ) : Asset
    {
        $this->getProperty()->description = $desc;
        return $this;
    }
}
?>
