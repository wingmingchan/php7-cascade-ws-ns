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

class Step
{
    public function __construct( 
        \stdClass $s=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {        
        $this->identifier         = $s->identifier;
        $this->label              = $s->label;
        $this->step_type          = $s->stepType;
        $this->owner              = $s->owner;
        $this->actions            = array();
        $this->action_identifiers = array();
        
        if( isset( $s->actions ) && isset( $s->actions->action ) )
        {
            $actions = $s->actions->action;
            
            if( !is_array( $actions ) )
            {
                $actions = array( $actions );
            }
            
            foreach( $actions as $action )
            {
                $a                          = new Action( $action );
                $this->actions[]            = $a;
                $this->action_identifiers[] = $a->getIdentifier();
            }
        }
    }
    
    public function getActionIdentifiers()
    {
        return $this->action_identifiers;
    }
    
    public function getActions()
    {
        return $this->actions;
    }
    
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function getStepType()
    {
        return $this->step_type;
    }
    
    public function toStdClass()
    {
        $obj             = new \stdClass();
        $obj->identifier = $this->identifier;
        $obj->label      = $this->label;
        $obj->stepType   = $this->step_type;
        $obj->owner      = $this->owner;
        $obj->actions    = new \stdClass();
        $count           = count( $this->actions );
        
        if( $count > 0 )
        {
            $obj->actions = new \stdClass();
            
            if( $count == 1 )
            {
                $obj->actions->action = $this->actions[ 0 ]->toStdClass();
            }
            else
            {
                $obj->actions->action = array();
                
                foreach( $this->actions as $action )
                {
                    $obj->actions->action[] = $action->toStdClass();
                }
            }
        }
        return $obj;
    }

    private $identifier;
    private $label;
    private $step_type;
    private $owner;
    private $actions;
    private $action_identifiers;
}
?>