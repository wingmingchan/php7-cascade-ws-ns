<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<description><h2>Introduction</h2>
<p>A <code>MetadataSetContainer</code> object represents a metadata set container asset. This class is a sub-class of <a href="/web-services/api/asset-classes/container"><code>Container</code></a>.</p>
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

JSON:
metadataSetContainer
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
<pre>&lt;complexType name="metadataSetContainer">
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
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/metadata_set_container.php">metadata_set_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{"asset":{
  "metadataSetContainer":{
    "children":[],
    "parentContainerId":"1f2176d58b7ffe834c5fe91ee998459c",
    "parentContainerPath":"/",
    "path":"Test Metadata Set Container",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Test Metadata Set Container",
    "id":"1f22acb08b7ffe834c5fe91eaf922b7d" } },
  "success":true
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