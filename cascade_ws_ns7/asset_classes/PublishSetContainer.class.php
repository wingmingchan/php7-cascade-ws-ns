<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<description><h2>Introduction</h2>
<p>A <code>PublishSetContainer</code> object represents a publish set container asset. This class is a sub-class of <a href="/web-services/api/asset-classes/container"><code>Container</code></a>.</p>
<h2>Structure of <code>publishSetContainer</code></h2>
<pre>SOAP:
publishSetContainer
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
publishSetContainer
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
<p>WSDL:</p>
<pre>&lt;complexType name="publishSetContainer">
  &lt;complexContent>
    &lt;extension base="impl:containered-asset">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="0" name="children" nillable="true" type="impl:container-children"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/publish_set_container.php">publish_set_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "publishSetContainer":{
    "children":[],
    "parentContainerId":"1f21774c8b7ffe834c5fe91e130d561a",
    "parentContainerPath":"/",
    "path":"Test Publish Set Container",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Test Publish Set Container",
    "id":"ad6ebf308b7ffe83664b9bd4760644e6" } },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class PublishSetContainer extends Container
{
    const TYPE = c\T::PUBLISHSETCONTAINER;

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