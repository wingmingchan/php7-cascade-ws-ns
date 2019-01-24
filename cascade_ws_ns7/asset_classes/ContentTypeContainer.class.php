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
<p>A <code>ContentTypeContainer</code> object represents a content type container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>contentTypeContainer</code></h2>
<pre>SOAP:
contentTypeContainer
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
contentTypeContainer
  children (array)
    stdClass
      id
      path
        path
        siteId
        siteName
      type
      recycled
  path
  siteId
  siteName
  name
  id
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "contentTypeContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/content_type_container.php">content_type_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/contenttypecontainer/54b505c28b7f085600ae2282a4b7ed71

{
  "asset":{
    "contentTypeContainer":{
      "children":[
      {
        "id":"5526403c8b7f085600ae228268382668",
        "path":{
          "path":"Test Content Type Container/Test Child Content Type Container",
          "siteId":"fd27691f8b7f08560159f3f02754e61d"
        },
        "type":"contenttypecontainer",
        "recycled":false
      },
      {
        "id":"54bc2efd8b7f085600ae22823d5ac8da",
        "path":{
          "path":"Test Content Type Container/Test XML",
          "siteId":"fd27691f8b7f08560159f3f02754e61d"
        },
        "type":"contenttype",
        "recycled":false
      },
      {
        "id":"54bbcd228b7f085600ae22828a8082de",
        "path":{
          "path":"Test Content Type Container/Test",
          "siteId":"fd27691f8b7f08560159f3f02754e61d"
        },
        "type":"contenttype",
        "recycled":false
      } ],
      "parentContainerId":"fd276cfc8b7f08560159f3f0db454558",
      "parentContainerPath":"/",
      "path":"Test Content Type Container",
      "siteId":"fd27691f8b7f08560159f3f02754e61d",
      "siteName":"_common",
      "name":"Test Content Type Container",
      "id":"54b505c28b7f085600ae2282a4b7ed71"
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
class ContentTypeContainer extends Container
{
    const TYPE = c\T::CONTENTTYPECONTAINER;
    
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
