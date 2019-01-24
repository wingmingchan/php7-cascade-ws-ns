<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/21/2017 Changed toStdClass so that it works with REST.
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 9/13/2016 Fixed a bug in setValues.
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
<p>A <code>FieldValue</code> object represents a <code>fieldValue</code> property found in a
<a href=\"http://www.upstate.edu/web-services/api/property-classes/dynamic-field.php\"><code>DynamicField</code></a> object inside
a <a href=\"http://www.upstate.edu/web-services/api/property-classes/metadata.php\"><code>Metadata</code></a> object
inside an asset that can be associated with a metadata set.</p>
<h2>Definition-Data/Container Dichotomy</h2>
<p>When working with metadata, we need to deal with its two sides at the same time. On the one hand, we are dealing with definitions of containers and types of data.
We have a metadata set which define what wired and dynamic fields an asset can have. For a wired field, it has two properties: required and visibility.
For a dynamic field, it has a name, a type, a label, and a set of possible values and so on. On the other hand, we are dealing with data.
For each wired field, it has a setting for required (a boolean) and a setting for visibility. For dynamic fields, it has a name, a value for required,
with zero or more values, each of which must be from the set of possible values in the definition, and so on.</p>
<p>If we look at metadata this way, we have two parallel hierarchies:</p>
<pre>Definition:
metadataSet
  dynamicMetadataFieldDefinition
    possibleValue
    
Data:
metadata
  dynamicField
    fieldValue
</pre>
<p>When the data, especially of a dynamic field, is altered, we need to look at the corresponding definition to make sure that the alteration is allowed.
For example, when we add a value to a dynamic field, we need to make sure that the value added is one of those possible values defined in the corresponding
dynamic metadata field definition, and that the value does not already exist in the dynamic field.</p>
<p>When we are changing the metadata of an asset, we also need to check if the metadata container is defined in the corresponding metadata set.
If the container does not exist, it is pointless to assign it a value.</p>
<p>Since a few asset types (file, block, page, folder, and symlink) can be associated with metadata, classes related to metadata must be built outside
these asset-related classes so that the same set of metadata-related classes can be reused.</p>
<h2>Structure of <code>fieldValue</code></h2>
<pre>fieldValue (NULL, stdClass or array of stdClass)
  value
</pre>
<h2>Design Issues</h2>
<ul>
<li>A <code>FieldValue</code> object can contain zero, one, or more <code>value</code>s.
The <code>toStdClass</code> method must generate the correct <code>\stdClass</code> object corresponding to these three cases.</li>
<li>The <code>stdClass</code> object passed into the constructor cannot be NULL. But the object can contain nothing.</li>
<li>To avoid empty values mixing with non-empty values, <code>setValues</code> should not allow <code>\stdClass</code> objects
with no values mixed with <code>\stdClass</code> objects with a value property. But if a single object is passed in, then the object can have no value.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "fieldValues" ),
        array( "getComplexTypeXMLByName" => "fieldValue" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/metadata_dynamic_field.php">metadata_dynamic_field.php</a></li></ul></postscript>
</documentation>
*/
class FieldValue extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $fv=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
        $this->values  = array();
        
        if( isset( $fv ) )
        {
            if( isset( $fv->array ) && count( $fv->array ) > 0 )
            {
                $this->processValues( $fv );
            }
            else
            {
                if( isset( $fv->value ) )
                    $this->values[] = $fv->value;
            }
        }
    }
    
/**
<documentation><description><p>Returns <code>NULL</code>, or an array of strings.</p></description>
<example>u\DebugUtility::dump( $radio_fv->getValues() );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getValues()
    {
        return $this->values; // can be NULL
    }
    
/**
<documentation><description><p>Uses the set of <code>\stdClass</code> objects (each has a non-NULL and non-empty <code>value</code> property)
to set the values and returns the calling object. The method must guarantee that no repeated values are allowed.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>EmptyValueException, NonUniqueValueException</exception>
</documentation>
*/
    public function setValues( $values ) : Property // an array of stdClass objects
    {
        $this->values = array();
        
        $count = count( $values );
        
        if( $count == 1 ) // NULL or object
        {
            if( is_null( $values[ 0 ] ) )
                $this->values[] = NULL;
            else
                $this->values[] = $values[ 0 ]->value;
        }
        else
        {
            foreach( $values as $value )
            {
                if( $value->value == NULL || $value->value == '' )
                {
                    throw new e\EmptyValueException(
                        S_SPAN . "The value cannot be empty." . E_SPAN );
                }
            
                if( in_array( $value->value, $this->values ) )
                {
                    throw new e\NonUniqueValueException(
                        S_SPAN . "The value " . $value->value . " already exist." . E_SPAN );
                }
                else
                {
                    $this->values[] = $value->value;
                }
            }
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $text_fv->toStdClass() );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass()
    {
        $obj   = new \stdClass();
        $count = count( $this->values );
        
        if( $count == 0 ) ## REST
        {
            $obj = array();
        }
        elseif( $count == 1 ) // NULL or 1 value, possibly empty string
        {
            $value = new \stdClass();
            
            if( $this->values[ 0 ] != '' )
            {
                if( $this->service->isSoap() )
                {
                    $value->value = $this->values[ 0 ];
                    $obj->fieldValue = $value;
                }
                elseif( $this->service->isRest() )
                {
                    $value->value = $this->values[ 0 ];
                    $obj = array( $value );
                }
            }
            elseif( $this->values[ 0 ] == '' || is_null( $this->values[ 0 ] ) )
            {
                $obj = array();
            }
        }
        else // one or more
        {
            if( $this->service->isSoap() )
                $obj->fieldValue = array();
            elseif( $this->service->isRest() )
                $obj = array();

            for( $i = 0; $i < $count; $i++ )
            {
                $value        = new \stdClass();
                $value->value = $this->values[ $i ];

                if( $this->service->isSoap() )
                {
                    $obj->fieldValue[] = $value;
                }
                elseif( $this->service->isRest() )
                {
                    $obj[] = $value;
                }
            }
        }

        return $obj;
    }
    
    // $values: 'array'=>an array of stdClass
    private function processValues( $values ) 
    {
        $values = $values->array; // now an array of stdClass
        
        foreach( $values as $value )
        {
            if( isset( $value->value ) )
                $this->values[] = $value->value;
        }
    }

    private $values;
    private $service;
}
?>
