<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/26/2017 Replaced static WSDL code with call to getXMLFragments.
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
<p>A <code>PageConfigurationSetContainer</code> object represents a page configuration set container asset. This class is a sub-class of <a href=\"/web-services/api/asset-classes/container\"><code>Container</code></a>.</p>
<h2>Structure of <code>pageConfigurationSetContainer</code></h2>
<pre>SOAP:
pageConfigurationSetContainer
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
pageConfigurationSetContainer
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
        array( "getComplexTypeXMLByName" => "pageConfigurationSetContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/page_configuration_set_container.php">page_configuration_set_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "pageConfigurationSetContainer":{
    "children":[],
    "parentContainerId":"1f2175268b7ffe834c5fe91ea94519a0",
    "parentContainerPath":"/",
    "path":"Test Configuration Set Container",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Test Configuration Set Container",
    "id":"ad2b3c068b7ffe83664b9bd40724496c"}},
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class PageConfigurationSetContainer extends Container
{
    const TYPE = c\T::PAGECONFIGURATIONSETCONTAINER;

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