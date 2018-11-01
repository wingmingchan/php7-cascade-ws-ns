<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/10/2017 Added JSON structure and JSON dump.
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
<p>A <code>ConnectorContainer</code> object represents a connector container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>connectorContainer</code></h2>
<pre>SOAP:
connectorContainer
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
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
connectorContainer
  children (array)
    stdClass
      id
      path (stdClass)
        path
        siteId
      type
      recycled
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
        array( "getComplexTypeXMLByName" => "connectorContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/connector_container.php">connector_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/connectorcontainer/2436012e8b7ffe8343b94c2803783fb1

{
  "asset":{
    "connectorContainer":{
      "children":[
      {
        "id":"243d93498b7ffe8343b94c28de034795",
        "path":{
          "path":"Test Connector Container/ga",
          "siteId":"0fa6f6f18b7ffe8343b94c28251e132e"
        },
        "type":"googleanalyticsconnector",
        "recycled":false
      },
      {
        "id":"244081a88b7ffe8343b94c28b1197a9d",
        "path":{
          "path":"Test Connector Container/tw",
          "siteId":"0fa6f6f18b7ffe8343b94c28251e132e"
        },
        "type":"twitterconnector",
        "recycled":false
      },
      {
        "id":"243c73ba8b7ffe8343b94c28ebf34eb1",
        "path":{
          "path":"Test Connector Container/wp",
          "siteId":"0fa6f6f18b7ffe8343b94c28251e132e"
        },
        "type":"wordpressconnector",
        "recycled":false
      },
      {
        "id":"243fb6bc8b7ffe8343b94c2812745ebf",
        "path":{
          "path":"Test Connector Container/sp",
          "siteId":"0fa6f6f18b7ffe8343b94c28251e132e"
        },
        "type":"spectateconnector",
        "recycled":false
      } ],
      "parentContainerId":"0fa6f7878b7ffe8343b94c28eecf4668",
      "parentContainerPath":"/",
      "path":"Test Connector Container",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"Test Connector Container",
      "id":"2436012e8b7ffe8343b94c2803783fb1"
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
class ConnectorContainer extends Container
{
    const TYPE = c\T::CONNECTORCONTAINER;
    
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }
}
?>
