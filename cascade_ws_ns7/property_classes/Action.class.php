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

class Action extends Property
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
            $this->identifier  = $a->identifier;
            $this->label       = $a->label;
            $this->action_type = $a->actionType;
            $this->next_id     = $a->nextId;
        }
    }
    
    public function getActionType()
    {
        return $this->action_type;
    }
    
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function getNextId()
    {
        return $this->next_id;
    }
    
    public function toStdClass()
    {
        $obj             = new \stdClass();
        $obj->identifier = $this->identifier;
        $obj->label      = $this->label;
        $obj->actionType = $this->action_type;
        $obj->nextId     = $this->next_id;
        return $obj;
    }

    private $identifier;
    private $label;
    private $action_type;
    private $next_id;
}
?>