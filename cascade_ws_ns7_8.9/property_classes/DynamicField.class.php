<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/22/2018 Added code to pass into the $service object to FieldValue.
  * 12/27/2017 Fixed a bug in toStdClass.
  * 12/21/2017 Changed toStdClass so that it works with REST.
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 5/28/2015 Added namespaces.
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
<p>Just like a <a href=\"http://www.upstate.edu/web-services/api/property-classes/field-value.php\"><code>FieldValue</code></a> object that
contains data and corresponds to a <a href=\"http://www.upstate.edu/web-services/api/property-classes/possible-value.php\"><code>PossibleValue</code></a> object in the definition,
a <code>DynamicField</code> object contains <code>FieldValue</code> objects and corresponds to
a <a href=\"http://www.upstate.edu/web-services/api/property-classes/dynamic-metadata-field-definition.php\"><code>DynamicMetadataFieldDefinition</code></a> object
(see <a href=\"http://www.upstate.edu/web-services/api/property-classes/field-value.php\"><code>FieldValue</code></a>).
A <code>DynamicField</code> object represents the <code>dynamicField</code> property of a <a href=\"http://www.upstate.edu/web-services/api/property-classes/metadata.php\"><code>Metadata</code></a>
object inside an asset that can be associated with a metadata set.</p>
<h2>Important Note About Default</h2>
<p>When using checkboxes and multi-selectors, pay attention to default values assigned to them. If there is one or more items in a field that are marked as \"Default\",
then at all times at least one item in the field must be selected. If all items are unselected manually, then the one(s) marked as \"Default\" will be selected,
no matter what. Web service code used to unselect all items of such a field will of course fail. But this will be the case even when working with them in the back-end of Cascade.
Therefore, it may not be a good idea to use \"Default\" for checkboxes and multi-selectors.</p>
<h2>Structure of <code>dynamicField</code></h2>
<pre>dynamicField (NULL, stdClass or array of stdClass)
  name
  fieldValues
    fieldValue
</pre>
<h2>Design Issues</h2>
<ul>
<li>A <code>dynamicField</code> object can be <code>NULL</code>, or it can contain one, or more (i.e., an array of) <code>fieldValue</code> objects.
The <code>toStdClass</code> method must generate the correct <code>\stdClass</code> object corresponding to these three cases.</li>
<li>The <code>\stdClass</code> object passed into the constructor can be NULL.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "dynamicMetadataFields" ),
        array( "getComplexTypeXMLByName" => "dynamicMetadataField" ),
        array( "getComplexTypeXMLByName" => "fieldValues" ),
        array( "getComplexTypeXMLByName" => "fieldValue" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/metadata_dynamic_field.php">metadata_dynamic_field.php</a></li></ul></postscript>
</documentation>
*/
class DynamicField extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $f=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;

        if( isset( $f ) )
        {
            if( isset( $f->name ) )
                $this->name = $f->name;
            
            if( isset( $f->fieldValues ) )
            {
                // can be an object, one value or NULL
                // can be an array
                if( $this->service->isSoap() )
                {
                    if( isset( $f->fieldValues->fieldValue ) )
                    {
                        $this->processFieldValues( $f->fieldValues->fieldValue );
                    }
                    else
                    {
                         $this->field_values =
                             new FieldValue( new \stdClass(), $this->service );
                    }
                }
                elseif( $this->service->isRest() )
                {
                    if( isset( $f->fieldValues ) )
                    {
                        $this->processFieldValues( $f->fieldValues );
                    }
                    else
                    {
                        $this->field_values =
                            new FieldValue( new \stdClass(), $this->service );
                    }
                }
            }
            else
            {
                $this->field_values = 
                    new FieldValue( new \stdClass(), $this->service );
            }
        }
    }
    
/**
<documentation><description><p>Returns a <a href="http://www.upstate.edu/web-services/api/property-classes/field-value.php"><code>FieldValue</code></a> object.</p></description>
<example>$text_fv = $text_df->getFieldValue();</example>
<return-type>Property</return-type>
</documentation>
*/
    public function getFieldValue() : Property
    {
        if( !isset( $this->field_values ) )
        {
            u\DebugUtility::out( "null field value" );
        }
        return $this->field_values;
    }
    
/**
<documentation><description><p>Returns <code>name</code>.</p></description>
<example>echo $text_df->getName(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getName() : string
    {
        return $this->name;
    }
    
/**
<documentation><description><p>Uses the set of <code>\stdClass</code> objects
or <code>NULL</code> to set <code>fieldValues</code> and return the object. The method must guarantee that no repeated values are allowed.</p></description>
<example>$text_df->setValue( NULL );</example>
<return-type>Property</return-type>
</documentation>
*/
    public function setValue( $values ) : Property
    {
        if( !is_array( $values ) )
        {
            $values = array( $values );
        }

        $this->field_values->setValues( $values );
    
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $text_df->toStdClass() );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass()
    {
        if( !isset( $this->name ) )
            return NULL;
            
        $obj = new \stdClass();
        $obj->name = $this->name;
        
        if( isset( $this->field_values ) )
        {
            //echo "============", BR;
            //u\DebugUtility::dump( $this->field_values );
            //echo "============", BR;
            $field_values = $this->field_values->toStdClass();
        }
        else
        {
            if( $this->service->isSoap() )
                $field_values = new \stdClass();
            elseif( $this->service->isRest() )
                $field_values = array();
        }
        
        //if( isset( $field_values ) )
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
        
        $this->field_values = new FieldValue( $obj, $this->service );
    }
    
    private $name;
    private $field_values;
    private $service;
}
?>