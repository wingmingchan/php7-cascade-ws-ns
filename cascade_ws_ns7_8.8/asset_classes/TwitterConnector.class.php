<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/30/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
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
<p>A <code>TwitterConnector</code> object represents a Twitter connector asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/connector.php\"><code>Connector</code></a>.</p>
<h2>Structure of <code>twitterConnector</code></h2>
<pre>SOAP:
twitterConnector
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  auth1
  auth2
  url
  verified
  verifiedDate
  connectorParameters
    connectorParameter
      name
      value
  connectorContentTypeLinks
    connectorContentTypeLink
      contentTypeId
      contentTypePath
      pageConfigurationId
      pageConfigurationName
      connectorContentTypeLinkParams
  destinationId
  destinationPath

REST:
twitterConnector
  destinationId
  destinationPath
  auth1
  auth2
  verified
  verifiedDate
  connectorParameters (array)
  	stdClass
      name
      value
  connectorContentTypeLinks (array)
  	stdClass
      contentTypeId
      contentTypePath
      pageConfigurationId
      pageConfigurationName
      connectorContentTypeLinkParams (array)
  parentFolderId
  parentFolderPath
  path
  siteId
  siteName
  name
  id
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "twitterConnector" ),
        array( "getComplexTypeXMLByName" => "statusUpdateConnector" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/twitter_connector.php">twitter_connector.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "twitterConnector":{
    "destinationId":"1f2239578b7ffe834c5fe91ee9cac23f",
    "destinationPath":"webapp",
    "auth2":"kf4*IG_ds%^#^!we",
    "verified":false,
    "connectorParameters":[ {
      "name":"Prefix",
      "value":"wing"},
      { "name":"Hash Tags",
      "value":"wing" } ],
    "connectorContentTypeLinks":[ {
      "contentTypeId":"783657f18b7ffe830539acf024a3423b",
      "contentTypePath":"_common_assets_wing:RWD",
      "pageConfigurationId":"78365bef8b7ffe830539acf0d7fb682e",
      "pageConfigurationName":"_common_assets_wing:RWD",
      "connectorContentTypeLinkParams":[] } ],
    "parentContainerId":"03dbe3628b7ffe8339ce5d132b740004",
    "parentContainerPath":"Test Connector Container",
    "path":"Test Connector Container/Twitter",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Twitter",
    "id":"17c737d88b7ffe834304cee25d9c0145" } },
  "success":true
}</pre>
</postscript>
</documentation>
*/
class TwitterConnector extends Connector
{
    const DEBUG    = false;
    const TYPE     = c\T::TWITTERCONNECTOR;
    const HASHTAGS = "Hash Tags";
    const PREFIX   = "Prefix";
    
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
<documentation><description><p>Returns <code>destinationId</code>.</p></description>
<example>echo $tc->getDestinationId(), BR;</example>
<return-type>string </return-type>
<exception></exception>
</documentation>
*/
    public function getDestinationId() : string
    {
        return $this->getProperty()->destinationId;
    }
    
/**
<documentation><description><p>Returns <code>destinationPath</code>.</p></description>
<example>echo $tc->getDestinationPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDestinationPath() : string
    {
        return $this->getProperty()->destinationPath;
    }
    
/**
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Hash Tags</code>.</p></description>
<example>echo $tc->getHashTags(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getHashTags()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::HASHTAGS )
            {
                return $param->getValue();
            }
        }
    }
    
/**
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Prefix</code>.</p></description>
<example>echo $tc->getPrefix(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPrefix()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PREFIX )
            {
                return $param->getValue();
            }
        }
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Hash Tags</code>, and returns the calling object.</p></description>
<example>$tc->setHashTags( '' )->setPrefix( '' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setHashTags( string $value ) : Asset
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::HASHTAGS )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Prefix</code>, and returns the calling object.</p></description>
<example>$tc->setHashTags( '' )->setPrefix( '' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setPrefix( string $value ) : Asset
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PREFIX )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
}
?>