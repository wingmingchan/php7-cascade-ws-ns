<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/26/2017 Added REST code to toStdClass.
  * 7/31/2017 Added getHelpText and setHelpText.
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 9/9/2016 Added isCheckbox, isDropdown, isMultiselect, isRadio, isText.
  * 9/8/2016 Added getDefaultValue, getDefaultValueString, hasDefaultValue, 
  * getPossibleValue, getPossibleValues. Fixed a bug.
  * 5/28/2015 Added namespaces.
  * 9/18/2014 Fixed bugs in appendValue and swapValue.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>DynamicMetadataFieldDefinition</code> object represents a <code>dynamicMetadataFieldDefinition</code> property found in a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/metadata-set.php\"><code>a\MetadataSet</code></a> object. It can contain <a href=\"http://www.upstate.edu/web-services/api/property-classes/possible-value.php\"><code>PossibleValue</code></a> objects.</p>
<h2>Structure of <code>dynamicMetadataFieldDefinition</code></h2>
<pre>dynamicMetadataFieldDefinition (stdClass or array of stdClass)
  name
  label
  fieldType
  required
  visibility
  possibleValues
    possibleValue (NULL, stdClass or array of stdClass)
  helpText (8.5)
</pre>
<h2>Design Issues</h2>
<ul>
<li>If a field is required, then it should be visible or inline, except when it already has a default value.</li>
<li>If a field is hidden, then it should not be required; a required and hidden field containing no value is meaningless.</li>
<li>Within a group of <code>PossibleValue</code> objects of the same <code>DynamicMetadataFieldDefintion</code> object, all <code>value</code>s must be non-empty and unique.</li>
<li>For a single group of radio buttons and a dropdown, only one <code>PossibleValue</code> object can be selected by default.</li>
<li>A field can have no <code>PossibleValue</code> objects selected by default.</li>
<li>When appending a value to the set of possible values, if the value already exists, just echo a message without throwing an exception.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definitions" ),
        array( "getComplexTypeXMLByName" => "dynamicMetadataFieldDefinition" ),
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definition-values" ),
        array( "getSimpleTypeXMLByName"  => "dynamic-metadata-field-type" ),
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definition-value" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Note</h2>
<p>For a <code>dynamicMetadataFieldDefinition</code> property of type <code>text</code>,
it cannot have possible values. For other types, the property must have at least one possible value (which can be <code>NULL</code>).
Therefore, all methods related to <code>possibleValue</code> should not be invoked through an <code>DynamicMetadataFieldDefinition</code>
object of type <code>text</code>.</p>
<h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/dynamic_metadata_field_definition.php">dynamic_metadata_field_definition.php</a></li></ul></postscript>
</documentation>
*/class DynamicMetadataFieldDefinition extends Property
{
    const DEBUG = false;
    const DUMP  = false;
    
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $obj=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;

        if( isset( $obj ) )
        {
            if( isset( $obj->name ) )
                $this->name            = $obj->name;
            if( isset( $obj->label ) )
                $this->label           = $obj->label;
            if( isset( $obj->fieldType ) )
                $this->field_type      = $obj->fieldType;
            if( isset( $obj->required ) )
                $this->required        = $obj->required;
            if( isset( $obj->visibility ) )
                $this->visibility      = $obj->visibility;
            if( isset( $obj->helpText ) )
                $this->help_text       = $obj->helpText;
        
            // $obj->possibleValues->possibleValue can be NULL
            if( isset( $obj->possibleValues ) )
            {
                 if( $this->service->isSoap() &&
                     isset( $obj->possibleValues->possibleValue ) )
                    $this->processPossibleValues( $obj->possibleValues->possibleValue );
                elseif( $this->service->isRest() )
                    $this->processPossibleValues( $obj->possibleValues );
            }
        }
    }
    
/**
<documentation><description><p>Appends the value at the end of the array of <code>possibleValue</code>,
and returns the calling object.</p></description>
<example>$dmfd = $ms->getDynamicMetadataFieldDefinition( "languages" );
$dmfd->appendValue( "Chinese" );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function appendValue( string $value ) : Property
    {
        // type of text
        if( $this->possible_values == NULL )
        {
            echo c\M::TEXT_NO_POSSIBLE_VALUE . BR;
            return $this;
        }
    
        $value = trim( $value );
        
        if( $value == '' )
            throw new e\EmptyValueException(
                S_SPAN . "The value cannot be empty." . E_SPAN );
    
        if( !$this->hasPossibleValue( $value ) )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Adding " . $value ); }
            $obj = new \stdClass();
            $obj->value = $value;
            $obj->selectedByDefault  = false;
            $this->possible_values[] = new PossibleValue( $obj );
            $this->values[]          = $value;
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $this->values ); }
        }
        else
        {
            echo "The value $value already exists." . BR;
        }
        return $this;
    }
    
/**
<documentation><description><p>Returns the default value of a field, or <code>NULL</code> if undefined.</p></description>
<example>if( $dmfd->hasDefaultValue() )
    u\DebugUtility::dump( $dmfd->getDefaultValue()->toStdClass() );
</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getDefaultValue()
    {
        foreach( $this->possible_values as $ps )
        {
            if( $ps->isDefaultValue() )
                return $ps;
        }
        
        return NULL;
    }
    
/**
<documentation><description><p>Returns the default value string of a field, or <code>NULL</code> if undefined.</p></description>
<example>u\DebugUtility::out( $dmfd->getDefaultValueString() );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getDefaultValueString()
    {
        foreach( $this->possible_values as $ps )
        {
            if( $ps->isDefaultValue() )
                return $ps->getValue();
        }
        
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>fieldType</code>.</p></description>
<example>u\DebugUtility::out( $dmfd->getFieldType() );</example>
<return-type>string</return-type>
</documentation>
*/
    public function getFieldType() : string
    {
        return $this->field_type;
    }

/**
<documentation><description><p>Returns <code>helpText</code>.</p></description>
<example>u\DebugUtility::out( $dmfd->getHelpText() );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getHelpText()
    {
        return $this->help_text;
    }

/**
<documentation><description><p>Returns <code>label</code>.</p></description>
<example>u\DebugUtility::out( $dmfd->getLabel() );</example>
<return-type>string</return-type>
</documentation>
*/
    public function getLabel() : string
    {
        return $this->label;
    }

/**
<documentation><description><p>Returns <code>name</code>.</p></description>
<example>u\DebugUtility::out( $dmfd->getName() );</example>
<return-type>string</return-type>
</documentation>
*/
    public function getName() : string
    {
        return $this->name;
    }
    
/**
<documentation><description><p>Returns a <code>PossibleValue</code> object having the value.</p></description>
<example>$english = $dmfd->getPossibleValue( "English" );</example>
<return-type>PossibleValue</return-type>
<exception>NoSuchValueException</exception>
</documentation>
*/
    public function getPossibleValue( string $value ) : PossibleValue
    {
        foreach( $this->possible_values as $possible_value )
        {
            if( $possible_value->getValue() == $value )
                return $possible_value;
        }
        throw new e\NoSuchValueException( "The value $value does not exist. " );
    }

/**
<documentation><description><p>Returns an array of <code>PossibleValue</code> objects.</p></description>
<example>u\DebugUtility::dump( $dmfd->getPossibleValues() );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getPossibleValues() : array
    {
        return $this->possible_values;
    }

/**
<documentation><description><p>Returns the empty string or an array of value strings.</p></description>
<example>u\DebugUtility::dump( $dmfd->getPossibleValueStrings() );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getPossibleValueStrings()
    {
        if( $this->possible_values == NULL )
        {
            echo c\M::TEXT_NO_POSSIBLE_VALUE . BR;
            return "";
        }

        return $this->values;
    }
    
/**
<documentation><description><p>Returns <code>required</code>.</p></description>
<example>u\DebugUtility::dump( u\StringUtility::boolToString( $dmfd->getRequired() ) );</example>
<return-type>bool</return-type>
</documentation>
*/
    public function getRequired() : bool
    {
        return $this->required;
    }

/**
<documentation><description><p>Returns <code>visibility</code>.</p></description>
<example>u\DebugUtility::dump( $dmfd->getVisibility() );</example>
<return-type>string</return-type>
</documentation>
*/
    public function getVisibility() : string
    {
        return $this->visibility;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field has a default value.</p></description>
<example>if( $dmfd->hasDefaultValue() )
    u\DebugUtility::dump( $dmfd->getDefaultValue()->toStdClass() );
</example>
<return-type>bool</return-type>
</documentation>
*/
    public function hasDefaultValue() : bool
    {
        if( !$this->isText() )
        {
            foreach( $this->possible_values as $ps )
            {
                if( $ps->isDefaultValue() )
                    return true;
            }
        }
        
        return false;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the value exists as a possible value.</p></description>
<example>u\DebugUtility::dump( u\StringUtility::boolToString( $dmfd->hasPossibleValue( "Spanish") ) );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function hasPossibleValue( string $value ) : bool
    {
        if( $this->possible_values == NULL )
        {
            echo c\M::TEXT_NO_POSSIBLE_VALUE . BR;
            return false;
        }

        return in_array( $value, $this->values );
    }

/**
<documentation><description><p>Returns a bool, indicating whether the field is a checkbox field.</p></description>
<example>if( $dmfd->isCheckbox() )
    echo "A checkbox field", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isCheckbox() : bool
    {
        return $this->getFieldType() == c\T::CHECKBOX;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field is a dropdown field.</p></description>
<example>if( $dmfd->isDropdown() )
    echo "A dropdown field", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isDropdown() : bool
    {
        return $this->getFieldType() == c\T::DROPDOWN;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field is a multiselecct field.</p></description>
<example>if( $dmfd->isMultiselect() )
    echo "A multiselect field", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isMultiselect() : bool
    {
        return $this->getFieldType() == c\T::MULTISELECT;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field is a radio field.</p></description>
<example>if( $dmfd->isRadio() )
    echo "A radio field", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isRadio() : bool
    {
        return $this->getFieldType() == c\T::RADIO;
    }
    
/**
<documentation><description><p>An alias of <code>getRequired</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function isRequired() : bool
    {
        return $this->required;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field is a text field.</p></description>
<example>if( $dmfd->isText() )
    echo "A text field", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isText() : bool
    {
        return $this->getFieldType() == c\T::TEXT;
    }
    
/**
<documentation><description><p>Removes the <code>PossibleValue</code> object having that value, and returns the calling object.</p></description>
<example>$dmfd->removeValue( "Chinese" );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function removeValue( string $value ) : Property
    {
        // type of text
        if( $this->possible_values == NULL )
        {
            echo c\M::TEXT_NO_POSSIBLE_VALUE . BR;
            return $this;
        }
        
        if( $value == '' )
            throw new e\EmptyValueException(
                S_SPAN . "The value cannot be empty." . E_SPAN );
            
        if( !in_array( $value, $this->values ) )
            throw new e\NoSuchValueException(
                S_SPAN . "The value $value does not exist." . E_SPAN );
            
        $count = count( $this->possible_values );
    
        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->possible_values[ $i ]->getValue() == $value )
            {
                if( self::DEBUG ) { u\DebugUtility::out(  "Removing $value" ); }
                $before        = array_slice( $this->possible_values, 0, $i );
                $values_before = array_slice( $this->values, 0, $i );
                if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $values_before ); }
                
                $after         = array();
                $values_after  = array();
                
                if( $count - $i > 1 )
                {
                    $after  = array_slice( $this->possible_values, $i + 1 );
                    $values_after = array_slice( $this->values, $i + 1 );
                }
                
                $this->possible_values = array_merge( $before, $after );
                $this->values          = array_merge( $values_before, $values_after );
                break;
            }
        }

        return $this;
    }
    
/**
<documentation><description><p>Sets <code>helpText</code> and returns the calling object.</p></description>
<example>$dmfd->setHelpText( "Different Languages" );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function setHelpText( string $help_text="" ) : Property
    {
        $this->help_text = $help_text;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>label</code> and returns the calling object.</p></description>
<example>$dmfd->setLabel( "Different Languages" );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function setLabel( string $label ) : Property
    {
        $label = trim( $label );
        
        if( $label == '' )
            throw new e\EmptyValueException(
                S_SPAN . "The label cannot be empty." . E_SPAN );
        
        $this->label = $label;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>required</code> and returns the calling object.</p></description>
<example>$dmfd->setRequired( true );
$ms->edit()->dump();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function setRequired( $required ) : Property
    {
        if( !c\BooleanValues::isBoolean( $required ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $required must be a boolean." . E_SPAN );
        
        if( $required )
        {
            $this->visibility = c\T::VISIBLE;
        }
        $this->required = $required;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>selectedByDefault</code> of the <code>possibleValue</code> having
this value to <code>true</code>, and if the field is of type <code>radio</code> or <code>dropdown</code>,
sets <code>selectedByDefault</code> of all other <code>possibleValue</code>s to false, and returns the object.</p></description>
<example>$dmfd->setSelectedByDefault( "Male" );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function setSelectedByDefault( $value ) : Property
    {
        if( !in_array( $value, $this->values ) )
            throw new e\NoSuchValueException(
                S_SPAN . "The value $value does not exist." . E_SPAN );
    
        foreach( $this->possible_values as $item )
        {
            // the relevant item
            if( $item->getValue() == $value )
            {
                $item->setSelectedByDefault( true );
            }
            
            // radio and dropdown
            if( $this->field_type == c\T::RADIO || $this->field_type == c\T::DROPDOWN )
            {
                if( $item->getValue() != $value )
                    $item->setSelectedByDefault( false );
            }
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>visibility</code> and returns the calling object.</p></description>
<example>$dmfd->setVisibility( c\T::VISIBLE );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function setVisibility( $visibility ) : Property
    {
        if( !c\VisibilityValues::isVisibility( $visibility ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $visibility is not acceptable." . E_SPAN );
    
        if( $visibility == c\T::HIDDEN )
        {
            $this->required = false;
            $this->visibility = $visibility;
        }
        else
        {
            $this->visibility = $visibility;
        }
        return $this;
    }
    
/**
<documentation><description><p>Swaps the two <code>PossibleValue</code> objects and returns the object.
This method can be used to change the order of the items.</p></description>
<example>$dmfd->swapValues( "Chinese", "Japanese" );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function swapValues( string $value1, string $value2 ) : Property
    {
        // type of text
        if( $this->possible_values == NULL )
        {
            echo c\M::TEXT_NO_POSSIBLE_VALUE . BR;
            return $this;
        }
    
        if( $value1 == '' || $value2 == '' )
            throw new e\EmptyValueException(
                S_SPAN . "The value cannot be empty." . E_SPAN );
            
        if( !in_array( $value1, $this->values ) )
            throw new e\NoSuchValueException(
                S_SPAN . "The value $value1 does not exist." . E_SPAN );
        
        if( !in_array( $value2, $this->values ) )
            throw new e\NoSuchValueException(
                S_SPAN . "The value $value2 does not exist." . E_SPAN );
            
        $first_pv_pos  = -1;
        $second_pv_pos = -1;
            
        $count = count( $this->possible_values );
    
        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->possible_values[ $i ]->getValue() == $value1 )
            {
                $first_pv_pos = $i;
            }
            
            if( $this->possible_values[ $i ]->getValue() == $value2 )
            {
                $second_pv_pos = $i;
            }
        }
        
        $temp_value = $this->values[ $first_pv_pos ];
        $this->values[ $first_pv_pos ]  = $value2;
        $this->values[ $second_pv_pos ] = $value1;
        
        $temp = $this->possible_values[ $first_pv_pos ];
        $this->possible_values[ $first_pv_pos ] = $this->possible_values[ $second_pv_pos ];
        $this->possible_values[ $second_pv_pos ] = $temp;
        
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $dmfd->toStdClass() );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj                                = new \stdClass();
        $obj->name                          = $this->name;
        $obj->label                         = $this->label;
        $obj->fieldType                     = $this->field_type;
        $obj->required                      = $this->required;
        $obj->visibility                    = $this->visibility;
        $obj->helpText                      = $this->help_text;
        
        if( $this->service->isSoap() )
        {
            $obj->possibleValues                = new \stdClass();
            $obj->possibleValues->possibleValue = array();
        }
        if( $this->service->isRest() )
            $obj->possibleValues = array();
            
        if( isset( $this->possible_values ) )
        {
            $count = count( $this->possible_values );
            
            if( $count == 1 )
            {
                if( $this->service->isSoap() )
                    $obj->possibleValues->possibleValue = 
                        $this->possible_values[0]->toStdClass();
                elseif( $this->service->isRest() )
                    $obj->possibleValues[] = $this->possible_values[0]->toStdClass();
            }
            else
            {
                $v_array        = array();
                $selected_count = 0;
                
                for( $i = 0; $i < $count; $i++ )
                {
                    $cur_value = $this->possible_values[ $i ]->getValue();
                    
                    if( $this->possible_values[ $i ]->getSelectedByDefault() )
                    {
                        $selected_count++;
                        
                        if( $selected_count > 1 && 
                            ( $this->field_type == c\T::RADIO || $this->field_type == c\T::DROPDOWN )
                        )
                        {
                            throw new e\MultipleSelectedByDefaultException( 
                                S_SPAN . "Multiple values have been selected by default." . E_SPAN );
                        }
                    }
                    
                    if( in_array( $cur_value, $v_array ) )
                    {
                        throw new e\NonUniqueValueException(
                            S_SPAN . "Repeated value found." . E_SPAN );
                    }
                    else
                    {
                        $v_array[] = $cur_value;
                    }
                
                    if( $this->service->isSoap() )
                        $obj->possibleValues->possibleValue[] = 
                            $this->possible_values[ $i ]->toStdClass();
                    elseif( $this->service->isRest() )
                        $obj->possibleValues[] =
                            $this->possible_values[ $i ]->toStdClass();
                }
            }
        }
        else
        {
            if( $this->service->isSoap() )
                $obj->possibleValues = new \stdClass();
            elseif( $this->service->isRest() )
                $obj->possibleValues = array();
        }
        return $obj;
    }
    
/**
<documentation><description><p>Sets the <code>selectedByDefault</code> of the <code>possibleValue</code>
having this value to <code>false</code> and returns the object.</p></description>
<example>$dmfd->unsetSelectedByDefault( "Japanese" );
$ms->edit();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function unsetSelectedByDefault( $value ) : Property
    {
        if( !$this->hasPossibleValue( $value ) )
            throw new e\NoSuchValueException(
                S_SPAN . "The value $value does not exist." . E_SPAN );
    
        foreach( $this->possible_values as $item )
        {
            // the relevant item
            if( $item->getValue() == $value )
            {
                $item->setSelectedByDefault( false );
            }
        }
        return $this;
    }
    
    private function processPossibleValues( $values )
    {
        $this->possible_values = array();
        $this->values          = array();

        if( $values == NULL ) // text
        {
            $this->possible_values = NULL;
            return;
        }
        
        if( !is_array( $values ) )
        {
            $values = array( $values );
        }
        
        $count = count( $values );
        
        for( $i = 0; $i < $count; $i++ )
        {
            $this->possible_values[] = new PossibleValue( $values[ $i ] );
            $this->values[] = $values[ $i ]->value;
        }
    }
    
    private $name;
    private $label;
    private $field_type;
    private $required;
    private $visibility;
    private $possible_values; // PossibleValue objects
    private $values;          // array of strings
    private $help_text;
    private $service;
}
?>
