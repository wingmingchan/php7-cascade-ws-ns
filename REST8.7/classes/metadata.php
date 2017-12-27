<?php
require_once('auth_test.php');

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    $block = $cascade->getAsset(
        a\TextBlock::TYPE, "c12d973c8b7ffe83129ed6d886deba0f" );
    $m = $block->getMetadata();
    u\DebugUtility::out( u\StringUtility::boolToString( 
        $m->isDynamicMetadataFieldRequired( "languages" ) ) );
    
    u\DebugUtility::out( u\StringUtility::boolToString( $m->isAuthorFieldRequired() ) );    
    u\DebugUtility::out( u\StringUtility::boolToString( $m->isDescriptionFieldRequired() ) );
    
    // corresponding metadata set for possible values of fields
    $ms = $block->getMetadataSet();
    
    // wired fields
    echo "Author: ", $m->getAuthor(), BR,
         "Display name: ", $m->getDisplayName(), BR,
         "End date: ", u\StringUtility::getCoalescedString( $m->getEndDate() ), BR . HR;
         
    // dynamic fields
    // text: macro
    $field_name = "macro";
    echo "Testing $field_name", BR;
    
    if( $m->hasDynamicField( $field_name ) )
    {
        $text = $m->getDynamicField( $field_name );
        u\DebugUtility::dump( $text->getFieldValue()->getValues() );
        
        // passing in a single string as value
        $m->setDynamicFieldValue( $field_name, "processUnknown" );
        // submit the change
        $m->getHostAsset()->edit();
    }
    else
    {
        echo "The field $field_name does not exit", BR;
    }
    
    // radio
/*/
<radio>
    <item default="true">Female</item>
    <item>Male</item>
    <item>Undetermined</item>
</radio>
/*/
    $field_name = "gender";
    echo "Testing $field_name", BR;
    
    if( $m->hasDynamicField( $field_name ) )
    {
        $radio = $m->getDynamicField( $field_name );
        u\DebugUtility::dump( $radio->getFieldValue()->getValues() );
        
        $new_value = "Male";
        
        if( $ms->hasDynamicMetadataFieldDefinition( $field_name ) )
        {
            $dmfd = $ms->getDynamicMetadataFieldDefinition( $field_name );
            
            if( $dmfd->hasPossibleValue( $new_value ) )
            {
                // an array as values, OK
                $m->setDynamicFieldValues( $field_name, array( $new_value ) );
                // a single string as value, OK too
                $m->setDynamicFieldValues( $field_name, $new_value );
                $m->getHostAsset()->edit();
            }
        }
    }
    else
    {
        echo "The field $field_name does not exit", BR;
    }
    
    // dropdown
/*/
<dropdown>
    <item>0-20</item>
    <item>21-30</item>
    <item>31-40</item>
    <item>41 and Above</item>
</dropdown>
/*/
    $field_name = "age";
    echo "Testing $field_name", BR;
    
    if( $m->hasDynamicField( $field_name ) )
    {
        $dropdown = $m->getDynamicField( $field_name );
        u\DebugUtility::dump( $dropdown->getFieldValue()->getValues() );
        
        $new_value = "21-30";
        
        if( $ms->hasDynamicMetadataFieldDefinition( $field_name ) )
        {
            $dmfd = $ms->getDynamicMetadataFieldDefinition( $field_name );
            
            if( $dmfd->hasPossibleValue( $new_value ) )
            {
                $m->setDynamicFieldValues( $field_name, array( $new_value ) )->
                    getHostAsset()->edit();
            }
        }
    }
    else
    {
        echo "The field $field_name does not exit", BR;
    }
    
    // checkbox
/*/
<checkbox>
    <item>Swimming</item>
    <item>Jogging</item>
    <item>Reading</item>
</checkbox>
/*/
    $field_name = "hobbies";
    echo "Testing $field_name", BR;
    
    if( $m->hasDynamicField( $field_name ) )
    {
        $checkboxes = $m->getDynamicField( $field_name );
        u\DebugUtility::dump( $checkboxes->toStdClass() );
        
        $new_values = array( "Swimming", "Jogging", "Reading" );
        
        if( $ms->hasDynamicMetadataFieldDefinition( $field_name ) )
        {
            $dmfd  = $ms->getDynamicMetadataFieldDefinition( $field_name );
            $valid = true;
            
            foreach( $new_values as $new_value )
            {
                if( !$dmfd->hasPossibleValue( $new_value ) )
                {
                    $valid = false;
                    break;
                }
            }
            
            if( $valid )
            {
                $m->setDynamicFieldValues( $field_name, $new_values )->
                    getHostAsset()->edit();
            }
        }
    }
    else
    {
        echo "The field $field_name does not exit", BR;
    }
    
    // multiselect
/*/
<multiselect>
    <item>English</item>
    <item>Chinese</item>
    <item>Spanish</item>
</multiselect>
/*/
    $field_name = "languages";
    echo "Testing $field_name", BR;
    
    if( $m->hasDynamicField( $field_name ) )
    {
        $multiselect = $m->getDynamicField( $field_name );
        u\DebugUtility::dump( $multiselect->toStdClass() );
        
        $new_values = array( "Chinese", "English" );
        
        if( $ms->hasDynamicMetadataFieldDefinition( $field_name ) )
        {
            $dmfd  = $ms->getDynamicMetadataFieldDefinition( $field_name );
            $valid = true;
            
            foreach( $new_values as $new_value )
            {
                if( !$dmfd->hasPossibleValue( $new_value ) )
                {
                    $valid = false;
                    break;
                }
            }
            
            if( $valid )
            {
                $m->setDynamicFieldValues( $field_name, $new_values )->
                    getHostAsset()->edit();
            }
        }
    }
    else
    {
        echo "The field $field_name does not exit", BR;
    }
}
catch( \Exception $e )
{
    echo S_PRE . $e . E_PRE;
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE;
}
?>