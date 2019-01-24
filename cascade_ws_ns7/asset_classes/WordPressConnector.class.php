<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
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
<p>A <code>WordPressConnector</code> object represents a WordPress connector asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/connector.php\"><code>Connector</code></a>.</p>
<h2>Structure of <code>wordPressConnector</code></h2>
<pre>SOAP:
wordPressConnector
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
  connectorParameters (empty)
  connectorContentTypeLinks
    connectorContentTypeLink
      contentTypeId
      contentTypePath
      pageConfigurationId
      pageConfigurationName
      connectorContentTypeLinkParams
        name
        value

REST:
wordPressConnector
  auth1
  auth2
  url
  verified
  verifiedDate
  connectorParameters (array)
  connectorContentTypeLinks (array)
      stdClass
      contentTypeId
      contentTypePath
      pageConfigurationId
      pageConfigurationName
      connectorContentTypeLinkParams (array)
        stdClass
          name
          value
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
        array( "getComplexTypeXMLByName" => "wordPressConnector" ),
        array( "getComplexTypeXMLByName" => "connector" ),
        array( "getComplexTypeXMLByName" => "connector-parameter-list" ),
        array( "getComplexTypeXMLByName" => "connector-parameter" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-list" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-param-list" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-param" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/wp_connector.php">wp_connector.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/wordpressconnector/243c73ba8b7ffe8343b94c28ebf34eb1

{
  "asset":{
    "wordPressConnector":{
      "auth2":"kf4*IG_ds%^#^!we",
      "url":"http://www.upstate.edu/cardiacsurgery-dev",
      "verified":false,
      "connectorParameters":[],
      "connectorContentTypeLinks":[
      {
        "contentTypeId":"d7b9363f8b7f085600a0fcdc8f82a7a6",
        "contentTypePath":"_common:3 Columns",
        "pageConfigurationId":"d7b67e658b7f085600a0fcdc6767c5fe",
        "pageConfigurationName":"_common:Desktop",
        "connectorContentTypeLinkParams":[
          {
            "name":"Metadata mapping for categories"
          },
          {
            "name":"Metadata mapping for tags"
          } ]
      } ],
      "parentContainerId":"2436012e8b7ffe8343b94c2803783fb1",
      "parentContainerPath":"Test Connector Container",
      "path":"Test Connector Container/wp",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"wp",
      "id":"243c73ba8b7ffe8343b94c28ebf34eb1"
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
class WordPressConnector extends Connector
{
    const DEBUG      = false;
    const TYPE       = c\T::WORDPRESSCONNECTOR;
    const CATEGORIES = "Metadata mapping for categories";
    const TAGS       = "Metadata mapping for tags";
    
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
<documentation><description><p>Sets <code>auth1</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setAuth1( $value ) : Asset
    {
        $this->getProperty()->auth1 = $value;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>auth2</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setAuth2( string $value ) : Asset
    {
        $this->getProperty()->auth2 = $value;
        return $this;
    }
    
/**
<documentation><description><p>Uses the name (either <code>Metadata mapping for
tags</code> or <code>Metadata mapping for categories</code>) and sets its value, both of
which are listed under the content type, and returns the calling object. Possible values
are drawn from the metadata set associated with the content type. They include seven
textual wired fields (like 'author', 'summary' and so on) and all the dynamic field names.</p></description>
<example></example>
<return-type></return-type>
<exception>NullAssetException, UnacceptableValueException, Exception</exception>
</documentation>
*/
    public function setMetadataMapping(
        ContentType $ct, string $name, string $value ) : Asset
    {
        if( $ct == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_CONTENT_TYPE . E_SPAN );
            
        if( $name != self::TAGS && $name != self::CATEGORIES )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The name $name is not acceptable." . E_SPAN );
            
        $links = $this->getConnectorContentTypeLinks();
        
        foreach( $links as $link )
        {
            if( $link->getContentTypeId() == $ct->getId() )
            {
                $link->setMetadataMapping( $name, $value );
                return $this;
            }
        }
        
        throw new \Exception( 
            S_SPAN . "The content does not exist in the connector." . E_SPAN );
    }
    
/**
<documentation><description><p>Sets <code>url</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setUrl( string $u ) : Asset
    {
        if( trim( $u ) == "" )
            throw e\EmptyValueException(
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
            
        $this->getProperty()->url = $u;
        return $this;
    }
}
?>