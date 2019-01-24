<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/26/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
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
<p>A <code>MetadataSetContainer</code> object represents a metadata set container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>metadataSetContainer</code></h2>
<pre>SOAP:
metadataSetContainer
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
metadataSetContainer
  children (array of stdClass)
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
        array( "getComplexTypeXMLByName" => "metadataSetContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/metadata_set_container.php">metadata_set_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/metadatasetcontainer/647db3ab8b7f085600ae2282d55a5b6d

{
  "asset":{
    "metadataSetContainer":{
      "children":[
      {
        "id":"647e291f8b7f085600ae22824372da97",
        "path":{
          "path":"Test Metadata Set Container/Test Child Container",
          "siteId":"fd27691f8b7f08560159f3f02754e61d"
        },
        "type":"metadatasetcontainer",
        "recycled":false
      },
      {
        "id":"647e77e18b7f085600ae2282629d7ea0",
        "path":{
          "path":"Test Metadata Set Container/External Link",
          "siteId":"fd27691f8b7f08560159f3f02754e61d"
        },
        "type":"metadataset",
        "recycled":false
      } ],
      "parentContainerId":"fd276a9e8b7f08560159f3f0d0b72bac",
      "parentContainerPath":"/",
      "path":"Test Metadata Set Container",
      "siteId":"fd27691f8b7f08560159f3f02754e61d",
      "siteName":"_common",
      "name":"Test Metadata Set Container",
      "id":"647db3ab8b7f085600ae2282d55a5b6d"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre></postscript>
</documentation>
*/
class MetadataSetContainer extends Container
{
    const TYPE = c\T::METADATASETCONTAINER;

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