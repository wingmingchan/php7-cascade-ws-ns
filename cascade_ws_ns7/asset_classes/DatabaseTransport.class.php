<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/20/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/10/2017 Added JSON dump.
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
<p>A <code>DatabaseTransport</code> object represents a database transport asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/transport.php\"><code>Transport</code></a>.</p>
<h2>Structure of <code>databaseTransport</code></h2>
<pre>databaseTransport
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  transportSiteId
  serverName
  serverPort
  databaseName
  username
  password
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "databaseTransport" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/database_transport.php">database_transport.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/transport_db/24af01918b7ffe8343b94c28de598720

{
  "asset":{
    "databaseTransport":{
      "transportSiteId":0,
      "serverName":"blah",
      "serverPort":1,
      "databaseName":"blah",
      "username":"blah",
      "password":"",
      "parentContainerId":"0fa6f7e98b7ffe8343b94c28a1414bed",
      "parentContainerPath":"/",
      "path":"db",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"db",
      "id":"24af01918b7ffe8343b94c28de598720"
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
class DatabaseTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::DATABASETRANSPORT;
    
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
<documentation><description><p>Returns <code>databaseName</code>.</p></description>
<example>echo $t->getDatabaseName();</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDatabaseName() : string
    {
        return $this->getProperty()->databaseName;
    }
    
/**
<documentation><description><p>Returns <code>password</code>. Since the password is encrypted, the returned string is useless.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPassword() : string
    {
        return $this->getProperty()->password;
    }
    
/**
<documentation><description><p>Returns <code>serverName</code>.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getServerName() : string
    {
        return $this->getProperty()->serverName;
    }
    
/**
<documentation><description><p>Returns <code>serverPort</code>.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getServerPort() : string
    {
        return $this->getProperty()->serverPort;
    }
    
/**
<documentation><description><p>Returns <code>transportSiteId</code>.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTransportSiteId() : string
    {
        return $this->getProperty()->transportSiteId;
    }
    
/**
<documentation><description><p>Returns <code>username</code>.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getUsername() : string
    {
        return $this->getProperty()->username;
    }
    
/**
<documentation><description><p>Sets the <code>databaseName</code> and returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setDatabaseName( string $d ) : Asset
    {
        if( trim( $d ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The database name cannot be empty." . E_SPAN );
        $this->getProperty()->databaseName = $d;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>password</code> and returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setPassword( string $pw="" ) : Asset
    {
        $this->getProperty()->password = $pw;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>serverName</code> and returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setServerName( string $s ) : Asset
    {
        if( trim( $s ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The host name cannot be empty." . E_SPAN );
        $this->getProperty()->serverName = $s;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>serverPort</code> and returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setServerPort( string $p ) : Asset
    {
        if( !is_numeric( $p ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The server port must be numeric." . E_SPAN );
        $this->getProperty()->serverPort = $p;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>transportSiteId</code> and returns the
calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setTransportSiteId( string $t ) : Asset
    {
        if( !is_numeric( $t ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The transport site ID must be numeric." . E_SPAN );
        $this->getProperty()->transportSiteId = $t;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>username</code> and returns the calling
object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setUsername( string $u ) : Asset
    {
        if( trim( $u ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The username cannot be empty." . E_SPAN );
        $this->getProperty()->username = $u;
        return $this;
    }
}
?>
