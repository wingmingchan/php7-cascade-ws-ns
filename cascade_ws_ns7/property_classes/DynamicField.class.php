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
 
class DynamicField extends Property
{
    public function __construct( 
        \stdClass $f=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $f ) )
        {
            $this->name = $f->name;
            
            if( isset( $f->fieldValues ) && isset( $f->fieldValues->fieldValue ) )
            {
                // can be an object, one value or NULL
                // can be an array
                $this->processFieldValues( $f->fieldValues->fieldValue );
            }
            else
            {
                $this->field_values = new FieldValue( new \stdClass() );
            }
        }
    }
    
    public function getFieldValue()
    {
        return $this->field_values;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setValue( $values )
    {
        if( !is_array( $values ) )
        {
            $values = array( $values );
        }

        $this->field_values->setValues( $values );
    
        return $this;
    }
    
    public function toStdClass()
    {
        if( !isset( $this->name ) )
            return NULL;
            
        $obj = new \stdClass();
        $obj->name = $this->name;
        
        if( isset( $this->field_values ) )
        {
            $field_values = $this->field_values->toStdClass();
        }
        else
        {
            $field_values = new \stdClass();
        }
        
        $obj->fieldValues = $field_values;
        
        return $obj;
    }
    
    private function processFieldValues( $values )
    {
        if( is_array( $values ) )
        {
            $obj = new \stdClass();
            $obj->array = $values;
        }
        else
        {
            $obj = $values;
        }
        
        $this->field_values = new FieldValue( $obj );
    }
    
    private $name;
    private $field_values;
}
?>