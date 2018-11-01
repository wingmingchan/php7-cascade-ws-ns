<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 1/3/2018 Added code to test for NULL.
  * 9/27/2017 Class created.
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
<p>A <code>CloudTransport</code> object represents a cloud transport asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/transport.php\"><code>Transport</code></a>.</p>
<h2>Structure of <code>cloudTransport</code></h2>
<pre>cloudTransport
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  key
  secret
  bucketName
  basePath
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "cloudTransport" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/cloud_transport.php">cloud_transport.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/transport_cloud/2428dfa38b7ffe8343b94c28b5616ed8

{
  "asset":{
    "cloudTransport":{
      "key":"blah",
      "secret":"kf4*IG_ds%^#^!we",
      "bucketName":"blah",
      "parentContainerId":"0fa6f7e98b7ffe8343b94c28a1414bed",
      "parentContainerPath":"/",
      "path":"blah",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"blah",
      "id":"2428dfa38b7ffe8343b94c28b5616ed8"
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
class CloudTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::TRANSPORTCLOUD;
    
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

/**
<documentation><description><p>Returns <code>basePath</code>.</p></description>
<example>echo $t->getBasePath(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBasePath()
    {
        if( isset( $this->getProperty()->basePath ) )
            return $this->getProperty()->basePath;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>bucketName</code>.</p></description>
<example>echo $t->getBucketName(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getBucketName() : string
    {
        return $this->getProperty()->bucketName;
    }
    
/**
<documentation><description><p>Returns <code>key</code>.</p></description>
<example>echo $t->getKey(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getKey() : string
    {
        return $this->getProperty()->key;
    }
    
/**
<documentation><description><p>Returns <code>secret</code>.</p></description>
<example>echo $t->getSecret(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSecret() : string
    {
        return $this->getProperty()->secret;
    }
    
/**
<documentation><description><p>Sets the <code>basePath</code> and returns the calling object.</p></description>
<example>$t->setBasePath( 'base-path' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setBasePath( string $bp ) : Asset
    {
        $this->getProperty()->basePath = $bp;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>key</code> and returns the calling object.</p></description>
<example>$t->setKey( 'key' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setKey( string $k ) : Asset
    {
        if( trim( $k ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_KEY . E_SPAN );
        $this->getProperty()->key = $k;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>secret</code> and returns the calling object.</p></description>
<example>$t->setPassword( $pw )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setSecret( string $s ) : Asset
    {
        if( trim( $s ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_SECRET . E_SPAN );
        $this->getProperty()->secret = $s;
        return $this;
    }
}
?>
