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

class DatabaseTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::DATABASETRANSPORT;
    
    public function getDatabaseName()
    {
        return $this->getProperty()->databaseName;
    }
    
    public function getPassword()
    {
        return $this->getProperty()->password;
    }
    
    public function getServerName()
    {
        return $this->getProperty()->serverName;
    }
    
    public function getServerPort()
    {
        return $this->getProperty()->serverPort;
    }
    
    public function getTransportSiteId()
    {
        return $this->getProperty()->transportSiteId;
    }
    
    public function getUsername()
    {
        return $this->getProperty()->username;
    }
    
    public function setDatabaseName( $d )
    {
        if( trim( $d ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The database name cannot be empty." . E_SPAN );
        $this->getProperty()->databaseName = $d;
        return $this;
    }
    
    public function setPassword( $pw="" )
    {
        $this->getProperty()->password = $pw;
        return $this;
    }
    
    public function setServerName( $s )
    {
        if( trim( $s ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . "The host name cannot be empty." . E_SPAN );
        $this->getProperty()->serverName = $s;
        return $this;
    }
    
    public function setServerPort( $p )
    {
        if( !is_numeric( $p ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The server port must be numeric." . E_SPAN );
        $this->getProperty()->serverPort = $p;
        return $this;
    }
    
    public function setTransportSiteId( $t )
    {
        if( !is_numeric( $t ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The transport site ID must be numeric." . E_SPAN );
        $this->getProperty()->transportSiteId = $t;
        return $this;
    }
    
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
