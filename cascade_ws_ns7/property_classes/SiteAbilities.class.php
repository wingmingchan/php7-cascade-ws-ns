<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;

class SiteAbilities extends Abilities
{
    public function __construct( 
        \stdClass $a=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $a ) )
        {
            parent::__construct( $a );
        }
        
        $this->access_connectors       = $a->accessConnectors;
        $this->access_destinations     = $a->accessDestinations;
        //$this->access_manage_site_area = $a->accessManageSiteArea;
    }
        
    public function getAccessConnectors()
    {
        return $this->access_connectors;
    }
    
    public function getAccessDestinations()
    {
        return $this->access_destinations;
    }

    public function setAccessConnectors( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->access_connectors = $bool;
        return $this;
    }
    
    public function setAccessDestinations( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->access_destinations = $bool;
        return $this;
    }

    public function toStdClass()
    {
        $obj = parent::toStdClass();
        $obj->accessDestinations   = $this->access_destinations;
        $obj->accessConnectors     = $this->access_connectors;
        //$obj->accessManageSiteArea = $this->access_manage_site_area;
        
        return $obj;
    }
    
    private $access_destinations;
    private $access_connectors;
}
?>