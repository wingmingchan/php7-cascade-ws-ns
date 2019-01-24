<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 7/14/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 9/8/2016 Added isDefaultValue.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>PossibleValue</code> object represents a <code>possibleValue</code> property found in
a <a href=\"http://www.upstate.edu/web-services/api/property-classes/dynamic-metadata-field-definition.php\"><code>DynamicMetadataFieldDefintion</code></a> object
inside a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/metadata-set.php\"><code>a\MetadataSet</code></a> object.</p>
<p>To understand what this class represents, let us look at a small part of the dump of a metadata set:</p>
<pre>      object(stdClass)#56 (6) {
        [\"name\"]=&gt;
        string(17) \"exclude-from-left\"
        [\"label\"]=&gt;
        string(22) \"Exclude from Left Menu\"
        [\"fieldType\"]=&gt;
        string(8) \"checkbox\"
        [\"required\"]=&gt;
        bool(false)
        [\"visibility\"]=&gt;
        string(6) \"inline\"
        [\"possibleValues\"]=&gt;
        object(stdClass)#55 (1) {
          [\"possibleValue\"]=&gt;
          array(3) {
            [0]=&gt;
            object(stdClass)#54 (2) {
              [\"value\"]=&gt;
              string(3) \"Yes\"
              [\"selectedByDefault\"]=&gt;
              bool(false)
            }
            [1]=&gt;
            object(stdClass)#50 (2) {
              [\"value\"]=&gt;
              string(5) \"Maybe\"
              [\"selectedByDefault\"]=&gt;
              bool(false)
            }
            [2]=&gt;
            object(stdClass)#52 (2) {
              [\"value\"]=&gt;
              string(2) \"No\"
              [\"selectedByDefault\"]=&gt;
              bool(true)
            }
          }
        }
      }
</pre>
<p>This object is the definition of the following dynamic field:</p>
<pre>&lt;checkbox&gt;
    &lt;item&gt;Yes&lt;/item&gt;
    &lt;item&gt;Maybe&lt;/item&gt;
    &lt;item default=\"true\"&gt;No&lt;/item&gt;
&lt;/checkbox&gt;
</pre>
<p>When the metadata set is associated with a page, for example, this inlined dynamic field will provide three checkboxes,
labeled \"<code>Yes</code>\", \"<code>Maybe</code>\", and \"<code>No</code>\" respectively, with \"<code>No</code>\" selected by default.
So a possible value represents an item or a possible choice, in a dynamic field (like a checkbox or group of checkboxes,
a group of radio buttons, a dropdown, or a multiselect) of a metadata set. Note that a value like \"<code>No</code>\" is in fact
the unique identifier of an item. That is to say, we cannot have two identical values in the same field. Also note that for a group of radio buttons and a dropdown, at most only one item can be selected as the default value of the field.</p>
<h2>Structure of <code>possibleValue</code></h2>
<pre>possibleValue (stdClass or array of stdClass)
  value (string)
  selectedByDefault (bool)
</pre>
<h2>Design Issues</h2>
<ul>
<li>The <code>value</code> of the object is the object's identifier. Therefore, it cannot be modified, nor can it be empty.</li>
<li>The <code>selectedByDefault</code> must be a bool value.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definition-values" ),
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definition-value" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/possible_value.php">possible_value.php</a></li></ul></postscript>
</documentation>
*/
class PossibleValue extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>EmptyValueException, UnacceptableValueException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Returns <code>selectedByDefault</code>.</p></description>
<example>echo u\StringUtility::boolToString( $english->getSelectedByDefault() );</example>
<return-type>bool</return-type>
</documentation>
*/
    public function getSelectedByDefault() : bool
    {
        return $this->selected_by_default;
    }
    
/**
<documentation><description><p>Returns <code>value</code>.</p></description>
<example>echo $pv->getValue(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getValue() : string
    {
        return $this->value;
    }
    
/**
<documentation><description><p>An alias of <code>getSelectedByDefault</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function isDefaultValue() : bool
    {
        return $this->selected_by_default;
    }
    
/**
<documentation><description><p>Sets <code>selectedByDefault</code> and returns the object.</p></description>
<example>$dmfd = $ms->getDynamicMetadataFieldDefinition( "gender" );
$dmfd->getPossibleValue( "Female" )->setSelectedByDefault( false );
$dmfd->getPossibleValue( "Male" )->setSelectedByDefault( true );
</example>
<return-type></return-type>
</documentation>
*/
    public function setSelectedByDefault( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->selected_by_default = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $dmfd->getPossibleValue( "Female" )->toStdClass() );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
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
