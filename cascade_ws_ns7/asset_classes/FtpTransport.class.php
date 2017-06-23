<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
  * 5/28/2015 Added namespaces.
  * 7/8/2014 Fixed some bugs.
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
<p>An <code>FtpTransport</code> object represents a database transport asset. This class is a sub-class of <a href=\"/web-services/api/asset-classes/transport\"><code>Transport</code></a>.</p>
<h2>Structure of <code>ftpTransport</code></h2>
<pre>ftpTransport
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  hostName
  port
  username
  password
  directory
  doSFTP
  doPASV
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "ftpTransport" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/ftp_transport.php">ftp_transport.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "ftpTransport":{
    "hostName":"www.upstate.edu",
    "port":50,
    "username":"Cascade",
    "password":"kf4*IG_ds%^#^!we",
    "doSFTP":true,
    "doPASV":false,
    "parentContainerId":"042b48d78b7ffe8339ce5d13f348500d",
    "parentContainerPath":"Transport Container",
    "path":"Transport Container/Test FTP",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"Test FTP",
    "id":"085ee3dd8b7ffe8339ce5d13c4b8bd85" } },
  "success":true
}</pre>
</postscript>
</documentation>
*/
class FtpTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::FTPTRANSPORT;
    
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
<documentation><description><p>Returns <code>directory</code>.</p></description>
<example>echo $t->getDirectory(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDirectory() : string
    {
        return $this->getProperty()->directory;
    }
    
/**
<documentation><description><p>Returns <code>doPASV</code>.</p></description>
<example>echo u\StringUtility::boolToString( $t->getDoPASV() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getDoPASV() : bool
    {
        return $this->getProperty()->doPASV;
    }
    
/**
<documentation><description><p>Returns <code>doSFTP</code>.</p></description>
<example>echo u\StringUtility::boolToString( $t->getDoSFTP() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getDoSFTP() : bool
    {
        return $this->getProperty()->doSFTP;
    }
    
/**
<documentation><description><p>Returns <code>hostName</code>.</p></description>
<example>echo $t->getHostName(), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getHostName()
    {
        return $this->getProperty()->hostName;
    }
    
/**
<documentation><description><p>Returns <code>password</code>. Since the password is
encrypted, the returned string is useless.</p></description>
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
<documentation><description><p>Returns <code>port</code>.</p></description>
<example>echo $t->getPort(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPort() : string
    {
        return $this->getProperty()->port;
    }
    
/**
<documentation><description><p>Returns <code>username</code>.</p></description>
<example>echo $t->getUsername(), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getUsername()
    {
        return $this->getProperty()->username;
    }
    
/**
<documentation><description><p>Sets the <code>directory</code> and returns the calling object.</p></description>
<example>$t->setDirectory( 'about' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDirectory( string $d ) : Asset
    {
        $this->getProperty()->directory = $d;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>doPASV</code> and returns the calling object.</p></description>
<example>$t->setDoPASV( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDoPASV( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        if( self::DEBUG ) { u\DebugUtility::out( $bool ? 'true' : 'false' ); }
        $this->getProperty()->doPASV = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>doSFTP</code> and returns the calling object.</p></description>
<example>$t->setDoSFTP( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDoSFTP( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        $this->getProperty()->doSFTP = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>hostName</code> and returns the calling object.</p></description>
<example>$t->setHostName( 'www.upstate.edu' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setHostName( string $h ) : Asset
    {
        if( trim( $h ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_HOST_NAME . E_SPAN );
        $this->getProperty()->hostName = $h;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>port</code> and returns the calling object.</p></description>
<example>$t->setPort( 50 )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setPort( string $p ) : Asset
    {
        if( !is_numeric( $p ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The port must be numeric." . E_SPAN );
        $this->getProperty()->port = $p;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>password</code> and returns the calling object.</p></description>
<example>$t->setPassword( $pw )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setPassword( string $pw ) : Asset
    {
        if( trim( $pw ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );
        $this->getProperty()->password = $pw;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>username</code> and returns the calling object.</p></description>
<example>$t->setUsername( $name )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setUsername( string $u ) : Asset
    {
        if( trim( $u ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_USER_NAME . E_SPAN );
        $this->getProperty()->username = $u;
        return $this;
    }
}
?>
