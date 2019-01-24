<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 11/2/2018 Class created.
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
<p>A <code>SharedFieldContainer</code> object represents a shared field container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>sharedFieldContainer</code></h2>
<pre>SOAP:
SharedFieldContainer
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
SharedFieldContainer
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
        array( "getComplexTypeXMLByName" => "sharedFieldContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/shared_field_container.php">shared_field_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/sharedfieldcontainer/cf6cf8ccac1e001b36b86cdab4d7d792

{
  "asset":
  {
    "sharedFieldContainer":
    {
      "children":
      [ {
        "id":"cf4d8095ac1e001b36b86cdaa44d34fd",
        "path":{"path":"wysiwyg-group","siteId":"f7a963087f0000012693e3d9932e44ba"},
        "type":"sharedfield_GROUP",
        "recycled":false
      },
      {
        "id":"cf4cc0adac1e001b36b86cda3fb840cf",
        "path":{"path":"text-group","siteId":"f7a963087f0000012693e3d9932e44ba"},
        "type":"sharedfield_GROUP","recycled":false
      } ],
      "path":"/",
      "siteId":"f7a963087f0000012693e3d9932e44ba",
      "siteName":"upstate",
      "name":"Shared Fields",
      "id":"580ec1ebac1e001b1730aec6861b78c8"
    }
  },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class SharedFieldContainer extends Container
{
    const TYPE = c\T::SHAREDFIELDCONTAINER;
    
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