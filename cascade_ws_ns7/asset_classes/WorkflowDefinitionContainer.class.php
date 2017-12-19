<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<p>A <code>WorkflowDefinitionContainer</code> object represents a workflow definition container asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/container.php\"><code>Container</code></a>.</p>
<h2>Structure of <code>workflowDefinitionContainer</code></h2>
<pre>SOAP:
workflowDefinitionContainer
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
workflowDefinitionContainer
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
        array( "getComplexTypeXMLByName" => "workflowDefinitionContainer" ),
        array( "getComplexTypeXMLByName" => "container-children" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/workflow_definition_container.php">workflow_definition_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "workflowDefinitionContainer":{
    "children":[ 
    { "id":"1f2421c78b7ffe834c5fe91e9d7913b5",
      "path":{
        "path":"Test Workflow Container/Test Child Container",
        "siteId":"1f2172088b7ffe834c5fe91e9596d028"},
      "type":"workflowdefinitioncontainer",
      "recycled":false } ],
    "parentContainerId":"1f2172958b7ffe834c5fe91edc1f2412",
    "parentContainerPath":"/",
    "path":"Test Workflow Container",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Test Workflow Container",
    "id":"1f2421b28b7ffe834c5fe91effa66c81"}},
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class WorkflowDefinitionContainer extends Container
{
    const TYPE = c\T::WORKFLOWDEFINITIONCONTAINER;

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