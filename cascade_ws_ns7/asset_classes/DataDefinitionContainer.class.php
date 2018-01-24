<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/22/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/11/2017 Added JSON structure and JSON dump.
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
<p>A <code>DataDefinitionContainer</code> object represents a data definition container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>dataDefinitionContainer</code></h2>
<pre>SOAP:
dataDefinitionContainer
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
dataDefinitionContainer
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
        array( "getComplexTypeXMLByName" => "dataDefinitionContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/data_definition_container.php">data_definition_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/datadefinitioncontainer/5501cc048b7f085600ae2282a4d548b7

{
  "asset":{
    "dataDefinitionContainer":{
      "children":[
      {
        "id":"647808af8b7f085600ae2282bbfcfedd",
        "path":{
          "path":"Test Data Definition Container/Test Child Container",
          "siteId":"fd27691f8b7f08560159f3f02754e61d"
        },
        "type":"datadefinitioncontainer",
        "recycled":false
      } ],
      "parentContainerId":"fd276f068b7f08560159f3f0bf02df08",
      "parentContainerPath":"/",
      "path":"Test Data Definition Container",
      "siteId":"fd27691f8b7f08560159f3f02754e61d",
      "siteName":"_common",
      "name":"Test Data Definition Container",
      "id":"5501cc048b7f085600ae2282a4d548b7"
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
class DataDefinitionContainer extends Container
{
    const TYPE = c\T::DATADEFINITIONCONTAINER;
    
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