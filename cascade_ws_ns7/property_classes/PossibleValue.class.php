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

class PossibleValue extends Property
{
    public function __construct( 
        \stdClass $v=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        // could be NULL for text
        if( isset( $v ) )
        {
            if( $v->value == NULL || 
                $v->value == '' )
            {
                throw new e\EmptyValueException(
                    S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
            }
                
            if( !c\BooleanValues::isBoolean( $v->selectedByDefault ) )
            {
                throw new e\UnacceptableValueException( 
                    S_SPAN . "The value " . $v->selectedByDefault .
                    " must be a boolean." . E_SPAN );
            }
            
            $this->value               = $v->value;
            $this->selected_by_default = $v->selectedByDefault;
        }
    }
    
    public function getSelectedByDefault()
    {
        return $this->selected_by_default;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setSelectedByDefault( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->selected_by_default = $bool;
        return $this;
    }
    
    public function toStdClass()
    {
        if( $this->value == NULL || $this->value == '' )
            throw new e\EmptyValueException(
                    S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
            
        $obj                    = new \stdClass();
        $obj->value             = $this->value;
        $obj->selectedByDefault = $this->selected_by_default;
        return $obj;
    }

    private $selected_by_default;
    private $value;
}
?>
