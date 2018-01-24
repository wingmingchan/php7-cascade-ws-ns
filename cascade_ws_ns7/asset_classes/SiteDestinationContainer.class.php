<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/29/2017 Replaced static WSDL code with call to getXMLFragments.
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
<p>A <code>SiteDestinationContainer</code> object represents a site destination container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>siteDestinationContainer</code></h2>
<pre>SOAP:
siteDestinationContainer
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
siteDestinationContainer
  children (array)
    stdClass
      id
      path
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
        array( "getComplexTypeXMLByName" => "siteDestinationContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/site_destination_container.php">site_destination_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/sitedestinationcontainer/0fbc5d5c8b7ffe8343b94c289aaf4adc

{
  "asset":{
    "siteDestinationContainer":{
      "children":[
      {
        "id":"0fbc6d3c8b7ffe8343b94c28323b18e1",
        "path":
        {
          "path":"Test Destination Container/upstate",
          "siteId":"0fa6f6f18b7ffe8343b94c28251e132e"
        },
        "type":"destination",
        "recycled":false
      } ],
      "parentContainerId":"0fa6f7f58b7ffe8343b94c28d64b3e7e",
      "parentContainerPath":"/",
      "path":"Test Destination Container",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"Test Destination Container",
      "id":"0fbc5d5c8b7ffe8343b94c289aaf4adc"
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
class SiteDestinationContainer extends Container
{
    const TYPE = c\T::SITEDESTINATIONCONTAINER;

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