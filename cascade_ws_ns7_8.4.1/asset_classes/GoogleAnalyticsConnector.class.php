<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
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
<p>A <code>GoogleAnalyticsConnector</code> object represents a Google analytics connector asset. This class is a sub-class of <a href=\"/cascade-admin/web-services/api/asset-classes/connector.php\"><code>Connector</code></a>.</p>
<h2>Structure of <code>googleAnalyticsConnector</code></h2>
<pre>SOAP:
googleAnalyticsConnector
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
  connectorContentTypeLinks (empty)
  
JSON:
googleAnalyticsConnector
  auth1
  auth2
  url
  verified
  verifiedDate
  connectorParameters (array)
    stdClass
      name
      value
  connectorContentTypeLinks (array)
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
        array( "getComplexTypeXMLByName" => "googleAnalyticsConnector" ),
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
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/google_connector.php">google_connector.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "googleAnalyticsConnector":{
    "auth2":"kf4*IG_ds%^#^!we",
    "verified":false,
    "connectorParameters":[ {
      "name":"Google Analytics Profile Id",
      "value":"two"},
    {
      "name":"Base Path",
      "value":"/all" } ],
    "connectorContentTypeLinks":[],
    "parentContainerId":"03dbe3628b7ffe8339ce5d132b740004",
    "parentContainerPath":"Test Connector Container",
    "path":"Test Connector Container/Google",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Google",
    "id":"086439518b7ffe8339ce5d13b34124b6" } },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class GoogleAnalyticsConnector extends Connector
{
    const DEBUG     = false;
    const TYPE      = c\T::GOOGLEANALYTICSCONNECTOR;
    const BASEPATH  = "Base Path";
    const PROFILEID = "Google Analytics Profile Id";
    
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
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Base Path</code>.</p></description>
<example>echo $gc->getBasePath(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBasePath() : string
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::BASEPATH )
            {
                return $param->getValue();
            }
        }
        return NULL;
    }
    
/**
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Google Analytics Profile Id</code>.</p></description>
<example>echo $gc->getProfileId(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getProfileId()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PROFILEID )
            {
                return $param->getValue();
            }
        }
        return NULL;
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Base Path</code>, and returns the calling object.</p></description>
<example>$gc->setBasePath( 'all' )->setProfileId( 'two' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setBasePath( string $value ) : Asset
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::BASEPATH )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Google Analytics Profile Id</code>, and returns the calling object. The <code>$value</code> cannot be empty.</p></description>
<example>$gc->setBasePath( 'all' )->setProfileId( 'two' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setProfileId( string $value ) : Asset
    {
        if( trim( $value) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . "The profile ID cannot be empty." . E_SPAN );
        }
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PROFILEID )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
}
?>