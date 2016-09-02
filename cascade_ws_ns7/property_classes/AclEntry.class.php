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

class AclEntry extends Property
{
    public function __construct( 
        \stdClass $ae=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $ae ) )
        {
            $this->level = $ae->level;
            $this->type  = $ae->type;
            $this->name  = $ae->name;
        }
    }
    
    public function display()
    {
        echo "Level: " . $this->level . BR .
             "Type: "  . $this->type . BR .
             "Name: "  . $this->name . BR . BR;
        return $this;
    }
    
    public function getLevel()
    {
        return $this->level;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setLevel( $level )
    {
        if( !c\LevelValues::isLevel( $level ) )
            throw new e\UnacceptableValueException( "The level $level is unacceptable." );
            
        $this->level = $level;
        return $this;
    }

    public function toStdClass()
    {
        $obj        = new \stdClass();
        $obj->level = $this->level;
        $obj->type  = $this->type;
        $obj->name  = $this->name;
        return $obj;
    }
    
    private $level;
    private $type;
    private $name;
}
?>
