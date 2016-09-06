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
class FacebookConnector extends Connector
{
    const DEBUG    = false;
    const TYPE     = c\T::FACEBOOKCONNECTOR;
    const PREFIX   = "Prefix";
    const PAGENAME = "Page Name";
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDestinationId()
    {
        return $this->getProperty()->destinationId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDestinationPath()
    {
        return $this->getProperty()->destinationPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
