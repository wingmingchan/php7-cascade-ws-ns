<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<p>An <code>AssetFactoryContainer</code> object represents an asset factory container asset. This class is a sub-class of <a href=\"/web-services/api/asset-classes/container\"><code>Container</code></a>.</p>
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
      
JSON:
assetFactoryContainer
  applicableGroups
  children (array)
    stdClass
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
<pre>{ "asset":{
  "assetFactoryContainer":{
    "applicableGroups":"22q",
    "children":[
    { "id":"1f217d608b7ffe834c5fe91ed245a520",
      "path":{
        "path":"Upload/test",
        "siteId":"1f2172088b7ffe834c5fe91e9596d028" },
      "type":"assetfactory",
      "recycled":false },
    { "id":"1f217b0b8b7ffe834c5fe91e4a8615fd",
      "path":{
        "path":"Upload/Upload Word and Other Documents",
        "siteId":"1f2172088b7ffe834c5fe91e9596d028"},
      "type":"assetfactory",
      "recycled":false } ],
    "description":"Upload",
    "parentContainerId":"1f2174298b7ffe834c5fe91e544ee758",
    "parentContainerPath":"/",
    "path":"Upload",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Upload",
    "id":"1f217d838b7ffe834c5fe91e9832f910"}},
  "success":true
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
<documentation><description><p>Returns <code>description</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDescription()
    {
        return $this->getProperty()->description;
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
