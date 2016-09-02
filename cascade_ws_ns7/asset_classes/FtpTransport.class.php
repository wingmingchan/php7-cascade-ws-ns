<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/8/2014 Fixed some bugs.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class FtpTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::FTPTRANSPORT;
    
    public function getDirectory()
    {
        return $this->getProperty()->directory;
    }
    
    public function getDoPASV()
    {
        return $this->getProperty()->doPASV;
    }
    
    public function getDoSFTP()
    {
        return $this->getProperty()->doSFTP;
    }
    
    public function getHostName()
    {
        return $this->getProperty()->hostName;
    }
    
    public function getPassword()
    {
        return $this->getProperty()->password;
    }
    
    public function getPort()
    {
        return $this->getProperty()->port;
    }
    
    public function getUsername()
    {
        return $this->getProperty()->username;
    }
    
    public function setDirectory( $d )
    {
        $this->getProperty()->directory = $d;
        return $this;
    }
    
    public function setDoPASV( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        if( self::DEBUG ) { u\DebugUtility::out( $bool ? 'true' : 'false' ); }
        $this->getProperty()->doPASV = $bool;
        return $this;
    }
    
    public function setDoSFTP( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        $this->getProperty()->doSFTP = $bool;
        return $this;
    }
    
    public function setHostName( $h )
    {
        if( trim( $h ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_HOST_NAME . E_SPAN );
        $this->getProperty()->hostName = $h;
        return $this;
    }
    
    public function setPort( $p )
    {
        if( !is_numeric( $p ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The port must be numeric." . E_SPAN );
        $this->getProperty()->port = $p;
        return $this;
    }
    
    public function setPassword( $pw )
    {
        if( trim( $pw ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );
        $this->getProperty()->password = $pw;
        return $this;
    }
    
    public function setUsername( $u )
    {
        if( trim( $u ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_USER_NAME . E_SPAN );
        $this->getProperty()->username = $u;
        return $this;
    }
}
?>
