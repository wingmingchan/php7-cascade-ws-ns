<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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

JSON:
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
<pre>{ "asset":{
  "connectorContainer":{
    "children":[
    {
      "id":"086439518b7ffe8339ce5d13b34124b6",
      "path":{
        "path":"Test Connector Container/Google",
        "siteId":"1f2172088b7ffe834c5fe91e9596d028" },
      "type":"googleanalyticsconnector",
      "recycled":false } ],
    "parentContainerId":"1f2177b48b7ffe834c5fe91e1a7d31f4",
    "parentContainerPath":"/",
    "path":"Test Connector Container",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Test Connector Container",
    "id":"03dbe3628b7ffe8339ce5d132b740004" } },
  "success":true
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
