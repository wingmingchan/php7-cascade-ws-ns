<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 9/18/2017 Added setProtocolAuthentication and removed other related methods for 8.6.
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
<p>An <code>FtpTransport</code> object represents a database transport asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/transport.php\"><code>Transport</code></a>.</p>
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
  doPASV
  username
  authMode
  privateKey
  password
  directory
  ftpProtocolType
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "ftpTransport" ),
        array( "getSimpleTypeXMLByName"  => "ftpProtocolType" ),
        array( "getSimpleTypeXMLByName"  => "authMode" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/ftp_transport.php">ftp_transport.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/transport/2844531b8b7ffe8343b94c28ccbfc7f7

{
  "asset":{
    "ftpTransport":{
      "hostName":"server",
      "port":1234,
      "username":"wing",
      "password":"kf4*IG_ds%^#^!we",
      "privateKey":"kf4*IG_ds%^#^!we",
      "directory":"about",
      "doPASV":false,
      "ftpProtocolType":"FTP",
      "parentContainerId":"0fa6f7e98b7ffe8343b94c28a1414bed",
      "parentContainerPath":"/",
      "path":"ftp",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"ftp",
      "id":"2844531b8b7ffe8343b94c28ccbfc7f7"
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
class FtpTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::FTPTRANSPORT;
    
    const PROTOCOL_TYPE_FTP    = "FTP";
    const PROTOCOL_TYPE_SFTP   = "SFTP";
    const PROTOCOL_TYPE_FTPS   = "FTPS";
    const AUTH_TYPE_PASSWORD   = "PASSWORD";
    const AUTH_TYPE_PUBLIC_KEY = "PUBLIC_KEY";
    
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
<example>echo $t->getAuthMode(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getAuthMode() : string
    {
        return $this->getProperty()->authMode;
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
<documentation><description><p>Returns <code>privateKey</code>. Since the password is
encrypted, the returned string is useless.</p></description>
<example>echo $t->getPrivateKey(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPrivateKey() : string
    {
        return $this->getProperty()->privateKey;
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
<documentation><description><p>Sets all properties related to authentication and returns
the calling object. Note that no type info is provided for <code>$do_pasv</code>, and
hence no casting is performed.</p></description>
<example>
// FTP
$t->setProtocolAuthentication(
    a\FtpTransport::PROTOCOL_TYPE_FTP,
    "",       // mode
    "1234",   // password
    false
)->edit();

// FTPS
$t->setProtocolAuthentication(
    a\FtpTransport::PROTOCOL_TYPE_FTPS,
    "",       // mode
    "1234"    // password
)->edit();

// SFTP
$t->setProtocolAuthentication(
    a\FtpTransport::PROTOCOL_TYPE_SFTP,
    a\FtpTransport::AUTH_TYPE_PUBLIC_KEY, // mode
    "",    // password, not used
    false, // doPASV, not used
    "privatekey" // optional
)->edit();
</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException, EmptyValueException</exception>
</documentation>
*/
    public function setProtocolAuthentication(
        string $protocol_type=self::PROTOCOL_TYPE_FTP,
        string $auth_mode="", // required by SFTP
        string $password="",  // required by FTP & FTPS
        $do_pasv=false, // FTP only, optional
        string $private_key=""
    ) : Asset
    {
        if( trim( $protocol_type ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_PROTOCOL_TYPE . E_SPAN );
    
        if( trim( $protocol_type ) != self::PROTOCOL_TYPE_FTP && 
            trim( $protocol_type ) != self::PROTOCOL_TYPE_FTPS &&
            trim( $protocol_type ) != self::PROTOCOL_TYPE_SFTP )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The protocol type $protocol_type is undefined." . E_SPAN );
        
        // FTP: password required, doPASV optioinal
        if( trim( $protocol_type ) == self::PROTOCOL_TYPE_FTP )
        {
            if( trim( $password ) == "" )
                throw new e\EmptyValueException( 
                    S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );
            if( !c\BooleanValues::isBoolean( $do_pasv ) )
                throw new e\UnacceptableValueException( 
                    "The value $do_pasv must be a boolean." );
                    
            $this->getProperty()->ftpProtocolType = self::PROTOCOL_TYPE_FTP;
            $this->getProperty()->authMode = self::AUTH_TYPE_PASSWORD;
            $this->getProperty()->password = trim( $password );
            $this->getProperty()->doPASV   = $do_pasv;
        }
        // FTPS: 
        elseif( trim( $protocol_type ) == self::PROTOCOL_TYPE_FTPS )
        {
            if( trim( $password ) == "" )
                throw new e\EmptyValueException( 
                    S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );
            
            $this->getProperty()->ftpProtocolType = self::PROTOCOL_TYPE_FTPS;
            $this->getProperty()->authMode = self::AUTH_TYPE_PASSWORD;
            $this->getProperty()->password = trim( $password );
        }
        // SFTP
        else
        {
            if( trim( $auth_mode ) != self::AUTH_TYPE_PASSWORD && 
                trim( $auth_mode ) != self::AUTH_TYPE_PUBLIC_KEY )
            {
                throw new e\UnacceptableValueException( 
                    S_SPAN . "The auth mode $auth_mode is undefined." . E_SPAN );
            }
                    
            if( trim( $auth_mode ) == self::AUTH_TYPE_PASSWORD )
            {
                if( trim( $password ) == "" )
                    throw new e\EmptyValueException( 
                        S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );
                $this->getProperty()->ftpProtocolType = self::PROTOCOL_TYPE_SFTP;
                $this->getProperty()->authMode = self::AUTH_TYPE_PASSWORD;
                $this->getProperty()->password = trim( $password );
            }
            else
            {
                $this->getProperty()->ftpProtocolType = self::PROTOCOL_TYPE_SFTP;
                $this->getProperty()->authMode = self::AUTH_TYPE_PUBLIC_KEY;
                    
                if( trim( $private_key ) != "" )
                {
                    $this->getProperty()->privateKey = trim( $private_key );
                }
            }
        }
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
