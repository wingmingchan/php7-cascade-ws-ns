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

class GoogleAnalyticsConnector extends Connector
{
    const DEBUG     = false;
    const TYPE      = c\T::GOOGLEANALYTICSCONNECTOR;
    const BASEPATH  = "Base Path";
    const PROFILEID = "Google Analytics Profile Id";
    
    public function getBasePath()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::BASEPATH )
            {
                return $param->getValue();
            }
        }
    }
    
    public function getProfileId()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PROFILEID )
            {
                return $param->getValue();
            }
        }
    }
    
    public function setBasePath( $value )
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::BASEPATH )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
    
    public function setProfileId( $value )
    {
        if( trim( $value) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . "The profile ID cannot be empty." . E_SPAN );
        }
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PROFILEID )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
}
?>
