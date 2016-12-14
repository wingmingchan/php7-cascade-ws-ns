<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>A <code>DataDefinitionContainer</code> object represents a data definition container asset. This class is a sub-class of <a href="/web-services/api/asset-classes/container"><code>Container</code></a>.</p>
<h2>Structure of <code>dataDefinitionContainer</code></h2>
<pre>dataDefinitionContainer
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
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/data_definition_container.php">data_definition_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{ "asset":{
    "dataDefinitionContainer":{
    "children":[ {
      "id":"e3aceb867f00000118d3acfcaabcc1f4",
      "path":{
        "path":"DD Container/DD",
        "siteId":"f7a963087f0000012693e3d9932e44ba"},
        "type":"datadefinition",
        "recycled":false } ],
    "parentContainerId":"f7a9632d7f0000012693e3d9809faca9",
    "parentContainerPath":"/",
    "path":"DD Container",
    "siteId":"f7a963087f0000012693e3d9932e44ba",
    "siteName":"SUNY Upstate",
    "name":"DD Container",
    "id":"e3a9224c7f00000118d3acfc495173e2" } },
  "success":true
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