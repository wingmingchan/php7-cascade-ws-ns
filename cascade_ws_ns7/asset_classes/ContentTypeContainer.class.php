<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<description><h2>Introduction</h2>
<p>A <code>ContentTypeContainer</code> object represents a content type container asset. This class is a sub-class of <a href="/web-services/api/asset-classes/container"><code>Container</code></a>.</p>
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

JSON:
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
<h2>WSDL</h2>
<pre>&lt;complexType name="contentTypeContainer">
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
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/content_type_container.php">content_type_container.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "contentTypeContainer":{
    "children":[
    {
      "id":"1f2239118b7ffe834c5fe91e560a90e0",
      "path":{
        "path":"article_new",
        "siteId":"1f2172088b7ffe834c5fe91e9596d028" } ],
    "path":"/",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Content Types",
    "id":"1f2175d28b7ffe834c5fe91e3eb6485d"}},
  "success":true
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
