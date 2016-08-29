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

class Parameter extends Property
{
    public function __construct( 
        \stdClass $p=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $p ) )
        {
            $this->name  = $p->name;
            $this->value = $p->value;
        }
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setValue( $value )
    {
        $this->value = $value;
        return $this;
    }
    
    public function toStdClass()
    {
        $obj        = new \stdClass();
        $obj->name  = $this->name;
        $obj->value = $this->value;
        return $obj;
    }

    private $name;
    private $value;
}
?>
