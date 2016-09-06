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

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class DatabaseTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::DATABASETRANSPORT;
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDatabaseName()
    {
        return $this->getProperty()->databaseName;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPassword()
    {
        return $this->getProperty()->password;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getServerName()
    {
        return $this->getProperty()->serverName;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getServerPort()
    {
        return $this->getProperty()->serverPort;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getTransportSiteId()
    {
        return $this->getProperty()->transportSiteId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getUsername()
    {
        return $this->getProperty()->username;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setDatabaseName( $d )
    {
        if( trim( $d ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The database name cannot be empty." . E_SPAN );
        $this->getProperty()->databaseName = $d;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPassword( $pw="" )
    {
        $this->getProperty()->password = $pw;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setServerName( $s )
    {
        if( trim( $s ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The host name cannot be empty." . E_SPAN );
        $this->getProperty()->serverName = $s;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setServerPort( $p )
    {
        if( !is_numeric( $p ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The server port must be numeric." . E_SPAN );
        $this->getProperty()->serverPort = $p;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setTransportSiteId( $t )
    {
        if( !is_numeric( $t ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The transport site ID must be numeric." . E_SPAN );
        $this->getProperty()->transportSiteId = $t;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setUsername( $u )
    {
        if( trim( $u ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The username cannot be empty." . E_SPAN );
        $this->getProperty()->username = $u;
        return $this;
    }
}
?>
