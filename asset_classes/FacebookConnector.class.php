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

class FacebookConnector extends Connector
{
    const DEBUG    = false;
    const TYPE     = c\T::FACEBOOKCONNECTOR;
    const PREFIX   = "Prefix";
    const PAGENAME = "Page Name";
    
    public function getDestinationId()
    {
        return $this->getProperty()->destinationId;
    }
    
    public function getDestinationPath()
    {
        return $this->getProperty()->destinationPath;
    }
    
    public function getPageName()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PAGENAME )
            {
                return $param->getValue();
            }
        }
    }
    
    public function getPrefix()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PREFIX )
            {
                return $param->getValue();
            }
        }
    }
    
    public function setPageName( $value )
    {
        if( trim( $value) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . "The page name cannot be empty." . E_SPAN );
        }
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PAGENAME )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
    
    public function setPrefix( $value )
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PREFIX )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
}
?>
