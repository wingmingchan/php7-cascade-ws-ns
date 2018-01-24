<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/21/2017 Changed toStdClass so that it works with REST.
  * 12/5/2017 Added more tests to constructor.
  * 9/8/2017 Fixed a bug in the initialization.
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/31/2017 Fixed return type of getAuthor.
  * 1/20/2017 Added default value to setDynamicFieldValue.
  * 9/16/2016 Added $wired_fields and copyWiredFields.
  * 9/15/2016 Added hasDynamicFields.
  * 9/6/2016 Added all isXRequired methods.
  * 1/8/2016 Added code to deal with host asset.
  * 5/28/2015 Added namespaces.
  * 9/21/2014 Fixed a bug in toStdClass.
  * 7/30/2014 Fixed bugs in setEndDate, setReviewDate, setStartDate.
  * 7/28/2014 Added isWiredField and getWiredFieldMethodName.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_asset     as a;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
 
/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>Metadata</code> object represents the <code>metadata</code> property of an asset that can be associated with a metadata set.
It contains zero or more <code>DynamicField</code> objects.</p>
<p>As mentioned in <code>FieldValue</code>, we need to take care of the two sides of metadata, i.e., definition and data/container,
when we deal with an asset that can have metadata. When the <code>metadata</code> property is modified, this class must guarantee
that all modifications are allowed by the definition; i.e., that the containers are defined, and that the data are allowed in these containers.
Therefore, besides storing the <code>metadata</code> property, a <code>Metadata</code> object can also store the global <code>\$service</code> object
and the id of its corresponding metadata set. When a <code>setX</code> method is called, it will retrieve the corresponding <code>a\MetadataSet</code> object
and look at the definition. For example, if a value is added to a <code>dynamicField</code>, this value must be defined
in the corresponding <code>DynamicMetadataFieldDefinition</code> object.</p>
<h2>Structure of <code>metadata</code></h2>
<pre>SOAP:
metadata
  author
  displayName
  endDate
  keywords
  metaDescription
  reviewDate
  startDate
  summary
  teaser
  title
  dynamicFields (NULL or an stClass)
    dynamicField
      name
      fieldValues
        fieldValue
          value

REST:
metadata
  author
  displayName
  endDate
  keywords
  metaDescription
  reviewDate
  startDate
  summary
  teaser
  title
  dynamicFields (array of stClass)
    name
    fieldValues (array of stClass)
      value
</pre>
<p>Note that although the properties <code>expirationFolderFieldRequired</code> and <code>expirationFolderFieldVisibility</code>
are defined in a metadata set, the metadata <code>stdClass</code> object does not include information about expiration folder. Instead,
<code>expirationFolderId</code> and <code>expirationFolderPath</code> are properties of assets.</p>
<h2>Design Issues</h2>
<ul>
<li>The <code>\$service</code> object and the corresponding <code>a\MetadataSet</code> object are needed only when a <code>setX</code> method is called.</li>
<li>The <code>stdClass</code> object passed into the constructor cannot be NULL.</li>
<li>If a field requires a value, then the corresponding <code>setX</code> method cannot be called with an empty value.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "metadata" ),
        array( "getComplexTypeXMLByName" => "dynamicMetadataFields" ),
        array( "getComplexTypeXMLByName" => "dynamicMetadataField" ),
        array( "getComplexTypeXMLByName" => "fieldValues" ),
        array( "getComplexTypeXMLByName" => "fieldValue" ),
        array( "getSimpleTypeXMLByName"  => "metadata-field-visibility" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/metadata.php">metadata.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/metadata_wired_field.php">metadata_wired_field.php</a></li>
<li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/metadata_dynamic_field.php">metadata_dynamic_field.php</a></li>
</ul></postscript>
</documentation>
*/
class Metadata extends Property
{
    const DEBUG = false;
    const DUMP  = false;
    
    public static $wired_fields = array(
        "author", "displayName", "endDate", "keywords", "metaDescription", 
        "reviewDate","startDate", "summary", "teaser", "title"
    );

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
        $metadata_set_id=NULL,
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;

        if( isset( $obj ) )
        {
            if( isset( $obj->author ) )
                $this->author                 = $obj->author;
            if( isset( $obj->displayName ) )
                $this->display_name           = $obj->displayName;
            if( isset( $obj->endDate ) )
                $this->end_date               = $obj->endDate;
            if( isset( $obj->keywords ) )
                $this->keywords               = $obj->keywords;
            if( isset( $obj->metaDescription ) )
                $this->meta_description       = $obj->metaDescription;
            if( isset( $obj->reviewDate ) )
                $this->review_date            = $obj->reviewDate;
            if( isset( $obj->startDate ) )
                $this->start_date             = $obj->startDate;
            if( isset( $obj->summary ) )
                $this->summary                = $obj->summary;
            if( isset( $obj->teaser ) )
                $this->teaser                 = $obj->teaser;
            if( isset( $obj->title ) )
                $this->title                  = $obj->title;
            $this->metadata_set        = NULL;
            
            if( isset( $metadata_set_id ) )
                $this->metadata_set_id = $metadata_set_id;
            $this->dynamic_field_names = array();
            
            if( isset( $obj->dynamicFields ) )
            {
                if( $this->service->isSoap() &&
                    isset( $obj->dynamicFields->dynamicField ) ) // could be NULL
                    $this->processDynamicFields( 
                        $obj->dynamicFields->dynamicField, $this->service );
                elseif( $this->service->isRest() )
                    $this->processDynamicFields( $obj->dynamicFields, $this->service );
            }
            
            $this->host_asset = $data2; // could be null
        }
    }
    
/**
<documentation><description><p>Returns <code>author</code>.</p></description>
<example>echo "Author: ", $m->getAuthor(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getAuthor()
    {
        return $this->author;
    }
    
/**
<documentation><description><p>Returns <code>displayName</code>.</p></description>
<example>echo "Display name: ", $m->getDisplayName(), BR</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getDisplayName()
    {
        return $this->display_name;
    }
    
/**
<documentation><description><p>Returns the <code>DynamicField</code> object bearing the name.</p></description>
<example>u\DebugUtility::dump( $m->getDynamicField( $checkbox_name )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>EmptyNameException, NoSuchFieldException</exception>
</documentation>
*/
    public function getDynamicField( string $name ) : Property
    {
        $name = trim( $name );
        
        if( $name == '' )
            throw new e\EmptyNameException(
                S_SPAN . c\M::EMPTY_NAME . E_SPAN );
    
        foreach( $this->dynamic_fields as $field )
        {
            if( $field->getName() == $name )
                return $field;
        }
        
        throw new e\NoSuchFieldException(
            S_SPAN . "The dynamic field $name does not exist" . E_SPAN );
    }

/**
<documentation><description><p>Returns an array of names of <code>DynamicField</code> objects.</p></description>
<example>$field_names = $m->getDynamicFieldNames();</example>
<return-type>array</return-type>
</documentation>
*/
    public function getDynamicFieldNames() : array
    {
        return $this->dynamic_field_names;
    }
    
/**
<documentation><description><p>Returns an array of possible values (strings) of the named dynamic field from the corresponding <code>a\MetadataSet</code> object.</p></description>
<example>u\DebugUtility::dump( $m->getDynamicFieldPossibleValues( $dropdown_name ) );</example>
<return-type>array</return-type>
</documentation>
*/
    public function getDynamicFieldPossibleValues( string $name ) : array
    {
        return $this->getMetadataSet()->getDynamicMetadataFieldPossibleValueStrings( $name );
    }
    
/**
<documentation><description><p>Returns an array of all <code>DynamicField</code> objects contained in the <code>Metadata</code> object.</p></description>
<example>u\DebugUtility::dump( $m->getDynamicFields() );</example>
<return-type></return-type>
</documentation>
*/
    public function getDynamicFields()
    {
        return $this->dynamic_fields;
    }
    
/**
<documentation><description><p>Returns an array of values (strings) of the named field.</p></description>
<example>u\DebugUtility::dump( $m->getDynamicFieldValues( $multiselect_name ) );</example>
<return-type>array</return-type>
</documentation>
*/
    public function getDynamicFieldValues( string $name ) : array
    {
        $name = trim( $name );
        
        if( $name == '' )
            throw new e\EmptyNameException(
                S_SPAN . c\M::EMPTY_NAME . E_SPAN );
    
        $field = $this->getDynamicField( $name );
        
        return $field->getFieldValue()->getValues();
    }
    
/**
<documentation><description><p>Returns <code>endDate</code>.</p></description>
<example>echo "End date: ", $m->getEndDate(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getEndDate()
    {
        return $this->end_date;
    }
    
/**
<documentation><description><p>Returns the host asset.</p></description>
<example>$m->getHostAsset()->edit()->dump();</example>
<return-type>Asset</return-type>
</documentation>
*/
    public function getHostAsset() : a\Asset
    {
        return $this->host_asset;
    }
    
/**
<documentation><description><p>Returns <code>keywords</code>.</p></description>
<example>echo "Keywords: ", $m->getKeywords(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getKeywords()
    {
        return $this->keywords;
    }
    
/**
<documentation><description><p>Returns the <code>a\MetadataSet</code> object.</p></description>
<example>$ms = $m->getMetadataSet();</example>
<return-type>Asset</return-type>
</documentation>
*/
    public function getMetadataSet() : a\Asset
    {
        if( $this->metadata_set == NULL )
        {
            $this->metadata_set = new a\MetadataSet( 
                $this->service, $this->service->createId( 
                    a\MetadataSet::TYPE, $this->metadata_set_id ) );
        }

        return $this->metadata_set;
    }
    
/**
<documentation><description><p>Returns <code>metaDescription</code>.</p></description>
<example>echo "Description: ", $m->getMetaDescription(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getMetaDescription()
    {
        return $this->meta_description;
    }
    
/**
<documentation><description><p>Returns <code>reviewDate</code>.</p></description>
<example>echo "Review date: ", $m->getReviewDate(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getReviewDate()
    {
        return $this->review_date;
    }
    
/**
<documentation><description><p>Returns <code>startDate</code>.</p></description>
<example>echo "Start date: ", $m->getStartDate(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getStartDate()
    {
        return $this->start_date;
    }
    
/**
<documentation><description><p>Returns <code>summary</code>.</p></description>
<example>echo "Summary: ", $m->getSummary(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getSummary()
    {
        return $this->summary;
    }
    
/**
<documentation><description><p>Returns <code>teaser</code>.</p></description>
<example>echo "Teaser: ", $m->getTeaser(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getTeaser()
    {
        return $this->teaser;
    }
    
/**
<documentation><description><p>Returns <code>title</code>.</p></description>
<example>echo "Title: ", $m->getTitle(), BR;</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getTitle()
    {
        return $this->title;
    }
    
/**
<documentation><description><p>An alias of <code>getDynamicFieldValues</code>.</p></description>
<example></example>
<return-type>array</return-type>
<exception>EmptyNameException</exception>
</documentation>
*/
    public function getValues( string $name ) : array
    {
        return $this->getDynamicFieldValues( $name );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>DynamicField</code> bearing that name exists.</p></description>
<example>if( $m->hasDynamicField( $text_name ) )
{
    // do something
}
</example>
<return-type>bool</return-type>
<exception>EmptyNameException</exception>
</documentation>
*/
    public function hasDynamicField( string $name ) : bool
    {
        if( $name == '' )
            throw new e\EmptyNameException(
                S_SPAN . c\M::EMPTY_NAME . E_SPAN );
                
        if( !isset( $this->dynamic_field_names ) )
            return false;
    
        return in_array( $name, $this->dynamic_field_names );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>MetadataSet</code> object contains
<code>DynamicMetadataFieldDefinition</code> objects.</p></description>
<example>if( $m->hasDynamicFields() )
{
    // do something
}
</example>
<return-type>bool</return-type>
<exception>EmptyNameException</exception>
</documentation>
*/
    public function hasDynamicFields() : bool
    {
        return count( $this->dynamic_field_names ) > 0;
    }
    
/**
<documentation><description><p>An alias of <code>isPossibleValue</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function hasPossibleValue( string $field_name, string $value ) : bool
    {
        return $this->isPossibleValue( $field_name, $value );      
    }

/**
<documentation><description><p>Returns a bool, indicating whether the <code>author</code> field is required.</p></description>
<example>echo "Author: ", $m->isAuthorFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isAuthorFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getAuthorFieldRequired();      
    }
    
/**
<documentation><description><p>An alias of <code>isMetaDescriptionFieldRequired</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function isDescriptionFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getDescriptionFieldRequired();      
    }
    
/**
<documentation><description><p>An alias of <code>isDynamicMetadataFieldRequired</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function isDynamicFieldRequired( string $name ) : bool
    {
        return $this->isDynamicMetadataFieldRequired( $name );      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named <code>dynamicField</code> is required.</p></description>
<example>if( !$m->isDynamicMetadataFieldRequired( $text_name ) )
    $text_df->setValue( NULL );</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isDynamicMetadataFieldRequired( string $name ) : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->isDynamicMetadataFieldRequired( $name );      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>displayName</code> field is required.</p></description>
<example>echo "Display name: ", $m->isDisplayNameFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isDisplayNameFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getDisplayNameFieldRequired();      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>endDate</code> field is required.</p></description>
<example>echo "End date: ", $m->isEndDateFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isEndDateFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getEndDateFieldRequired();      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>endDate</code> field is required.</p></description>
<example>echo "Expiration folder: ", $m->isExpirationFolderFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isExpirationFolderFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getExpirationFolderFieldRequired();      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>keywords</code> field is required.</p></description>
<example>echo "Keywords: ", $m->isKeywordsFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isKeywordsFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getKeywordsFieldRequired();      
    }

/**
<documentation><description><p>Returns a bool, indicating whether the <code>metaDescription</code> field is required.</p></description>
<example>echo "Description: ", $m->isMetaDescriptionFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isMetaDescriptionFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getDescriptionFieldRequired();      
    }

/**
<documentation><description><p>Returns a bool, indicating whether the <code>$value</code> is a possible value of the named field.</p></description>
<example>if( $m->isPossibleValue( "exclude-from-menu", "Yes" ) )
{
    $m->setDynamicFieldValue( "exclude-from-menu", "Yes" );
    $page->edit();
}</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isPossibleValue( string $field_name, string $value ) : bool
    {
        $values = $this->getDynamicFieldValues( $field_name );
        return in_array( $value, $values );      
    }

/**
<documentation><description><p>Returns a bool, indicating whether the <code>reviewDate</code> field is required.</p></description>
<example>echo "Review date: ", $m->isReviewDateFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isReviewDateFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getReviewDateFieldRequired();      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>startDate</code> field is required.</p></description>
<example>echo "Start date: ", $m->isStartDateFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isStartDateFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getStartDateFieldRequired();      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>summary</code> field is required.</p></description>
<example>echo "Summary: ", $m->isSummaryFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isSummaryFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getSummaryFieldRequired();      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>teaser</code> field is required.</p></description>
<example>echo "Teaser: ", $m->isTeaserFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isTeaserFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getTeaserFieldRequired();      
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>title</code> field is required.</p></description>
<example>echo "Title: ", $m->isTitleFieldRequired() ? "required" : "not required", BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function isTitleFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getTitleFieldRequired();      
    }
    
/**
<documentation><description><p>Sets <code>author</code> and returns the calling object.</p></description>
<example>$m->setAuthor( "Wing" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setAuthor( string $author=NULL ) : Property
    {
        $author = trim( $author );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getAuthorFieldRequired() && $author == '' )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The author field is required." . E_SPAN );
        }

        $this->author = $author;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>displayName</code> and returns the calling object.</p></description>
<example>$m->setDisplayName( "Block" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setDisplayName( string $display_name=NULL ) : Property
    {
        $display_name = trim( $display_name );
    
        $this->checkMetadataSet();

        if( $this->metadata_set->getDisplayNameFieldRequired() && $display_name == '' )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The displayName field is required." . E_SPAN );
        }

        $this->display_name = $display_name;
        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>setDynamicFieldValue</code>.</p></description>
<example></example>
<return-type>Property</return-type>
</documentation>
*/
    public function setDynamicField( string $field, $values=NULL ) : Property
    {
        return $this->setDynamicFieldValue( $field, $values );
    }
    
/**
<documentation><description><p>Uses the set of values, sets the <code>DynamicField</code> object bearing that name, and returns the calling object.
<code>$values</code> can be a string (containing a single value) or an array of strings.</p></description>
<example>$values = array( "Swimming", "Reading" );
$m->setDynamicFieldValue( $checkbox_name, $values );</example>
<return-type>Property</return-type>
<exception>RequiredFieldException, NoSuchValueException</exception>
</documentation>
*/
    public function setDynamicFieldValue( string $field, $values=NULL ) : Property
    {
        if( $values == "" )
            $values = NULL;
            
        if( !is_array( $values ) )
        {
            $values = array( $values );
        }
        
        $v_count = count( $values );
        
        $this->checkMetadataSet();
        
        $df_def     = $this->metadata_set->getDynamicMetadataFieldDefinition( $field );
        $field_type = $df_def->getFieldType();
        $required   = $df_def->getRequired();
        $df         = $this->getDynamicField( $field );
        
        // text can accept anything
        if( $field_type == c\T::TEXT && $v_count == 1 )
        {
            $value = $values[0];
            
            if( $value == NULL ) // turn NULL to empty string
                $value = '';
            
            if( $required && $value == '' )
            {
                throw new e\RequiredFieldException(
                    S_SPAN . "The $field_type requires non-empty value" . E_SPAN );
            }
            
            $v = new \stdClass();
            $v->value = $value;
            $df->setValue( array( $v ) );
        }
        // radio and dropdown can accept only one value
        else if( ( $field_type == c\T::RADIO || $field_type == c\T::DROPDOWN ) &&
            $v_count == 1 )
        {
            $value = $values[0]; // read first value
            
            if( $value == '' ) // turn empty string to NULL
                $value = NULL;
            
            if( $required && $value == NULL ) // cannot be empty if required
                throw new e\RequiredFieldException(
                    S_SPAN . "The $field_type requires non-empty value." . E_SPAN );
            
            $possible_values = $df_def->getPossibleValueStrings(); // read from metadataSet
            
            if( !in_array( $value, $possible_values ) && isset( $value ) ) // undefined value
                throw new e\NoSuchValueException(
                    S_SPAN . "The value $value does not exist." . E_SPAN );
            
            $v = new \stdClass();
            
            if( $value != '' )
                $v->value = $value;
        
            $df->setValue( array( $v ) );
        }
        else if( ( $field_type == c\T::CHECKBOX || $field_type == c\T::MULTISELECT ) &&
            $v_count > 0 )
        {
            if( self::DEBUG ){ u\DebugUtility::out( 'Setting values for checkbox or multiselect' ); }

            if( $required && ( in_array( NULL, $values) || in_array( '', $values ) ) )
            {
                throw new e\RequiredFieldException(
                    S_SPAN . "The $field_type requires non-empty value." . E_SPAN );
            }
        
            $possible_values = $df_def->getPossibleValueStrings();
            
            foreach( $values as $value )
            {
                if( self::DEBUG ){ u\DebugUtility::out( "Value: $value" ); }

                if( !in_array( $value, $possible_values ) && isset( $value ) )
                {
                    throw new e\NoSuchValueException(
                        S_SPAN . "The value $value does not exist." . E_SPAN );
                }
            }
            
            $v_array = array();
            
            foreach( $values as $value )
            {
                $v = new \stdClass();
                $v->value = $value;
                $v_array[] = $v;
            }
            
            $df->setValue( $v_array );
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $df->toStdClass() ); }

        }
        
        //if( self::DEBUG && self::DUMP ){ u\DebugUtility::dump( $this ); }

        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>setDynamicFieldValue</code>.</p></description>
<example></example>
<return-type>Property</return-type>
</documentation>
*/
    public function setDynamicFieldValues( string $field, $values ) : Property
    {
        return $this->setDynamicField( $field, $values );
    }
    
/**
<documentation><description><p>Sets <code>endDate</code> and returns the calling object.
The input string should have the following format: <code>"yyyy-mm-ddThh:mm:ss"</code>. For example, <code>"2014-02-21T12:00:00"</code>.</p></description>
<example>$m->setEndDate( "2017-12-31T12:00:00" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setEndDate( string $end_date=NULL ) : Property
    {
        $end_date = trim( $end_date );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getEndDateFieldRequired() && ( $end_date == '' || $end_date == NULL ) )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The endDate field is required." . E_SPAN );
        }

        if( $end_date == "" )
            $end_date = NULL;
            
        $this->end_date = $end_date;
        return $this;
    }
   
/**
<documentation><description><p>Sets <code>keywords</code> and returns the calling object.</p></description>
<example>$m->setKeywords( "Test, More Test" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setKeywords( string $keywords=NULL ) : Property
    {
        $keywords = trim( $keywords );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getKeywordsFieldRequired() && $keywords == '' )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The keywords field is required." . E_SPAN );
        }

        $this->keywords = $keywords;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>metaDescription</code> and returns the calling object.</p></description>
<example>$m->setMetaDescription( "This is just a test" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setMetaDescription( string $meta_description=NULL ) : Property
    {
        $meta_description = trim( $meta_description );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getDescriptionFieldRequired() && $meta_description == '' )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The metaDescription field is required." . E_SPAN );
        }

        $this->meta_description = $meta_description;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>reviewDate</code> and returns the calling object.
The input string should have the following format: <code>"yyyy-mm-ddThh:mm:ss"</code>. For example, <code>"2014-02-21T12:00:00"</code>.</p></description>
<example>$m->setReviewDate( "2016-12-31T12:00:00" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setReviewDate( string $review_date=NULL ) : Property
    {
        $review_date = trim( $review_date );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getReviewDateFieldRequired() && 
            ( $review_date == '' || $review_date == NULL ) )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The reviewDate field is required." . E_SPAN );
        }

        if( $review_date == "" )
            $review_date = NULL;
            
        $this->review_date = $review_date;
        return $this;
    }
    
/**
<documentation><description><p>ets <code>startDate</code> and returns the calling object.
The input string should have the following format: <code>"yyyy-mm-ddThh:mm:ss"</code>. For example, <code>"2014-02-21T12:00:00"</code>.</p></description>
<example>$m->setStartDate( "2016-01-01T00:00:00" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setStartDate( string $start_date=NULL ) : Property
    {
        $start_date = trim( $start_date );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getStartDateFieldRequired()
            && ( $start_date == '' || $start_date == NULL ) )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The startDate field is required." . E_SPAN );
        }
        
        if( $start_date == "" )
            $start_date = NULL;

        $this->start_date = $start_date;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>summary</code> and returns the calling object.</p></description>
<example>$m->setSummary( "This is just a test" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setSummary( string $summary=NULL ) : Property
    {
        $summary = trim( $summary );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getSummaryFieldRequired() && $summary == '' )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The summary field is required." . E_SPAN );
        }

        $this->summary = $summary;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>teaser</code> and returns the calling object.</p></description>
<example>$m->setTeaser( "This is just a test" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setTeaser( string $teaser=NULL ) : Property
    {
        $teaser = trim( $teaser );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getTeaserFieldRequired() && $teaser == '' )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The teaser field is required." . E_SPAN );
        }

        $this->teaser = $teaser;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>title</code> and returns the calling object.</p></description>
<example>$m->setTitle( "This is just a test" )->getHostAsset()->edit();</example>
<return-type>Property</return-type>
<exception>RequiredFieldException</exception>
</documentation>
*/
    public function setTitle( string $title=NULL ) : Property
    {
        $title = trim( $title );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getTitleFieldRequired() && $title == '' )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The title field is required." . E_SPAN );
        }

        $this->title = $title;
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>u\DebugUtility::dump( $m->toStdClass() );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj                       = new \stdClass();
        $obj->author               = $this->author;
        $obj->displayName          = $this->display_name;
        $obj->endDate              = $this->end_date;
        $obj->keywords             = $this->keywords;
        $obj->metaDescription      = $this->meta_description;
        $obj->reviewDate           = $this->review_date;
        $obj->startDate            = $this->start_date;
        $obj->summary              = $this->summary;
        $obj->teaser               = $this->teaser;
        $obj->title                = $this->title;

        $count = 0;
        
        if( isset( $this->dynamic_fields ) )
            $count = count( $this->dynamic_fields );
        
        if( $count == 0 )
        {
            $obj->dynamicFields = NULL;
        }
        else if( $count == 1 )
        {
            if( $this->service->isSoap() )
            {
                $obj->dynamicFields = new \stdClass();
                $obj->dynamicFields->dynamicField =
                    $this->dynamic_fields[0]->toStdClass();
            }
            elseif( $this->service->isRest() )
            {
                $obj->dynamicFields = array( $this->dynamic_fields[0]->toStdClass() );
            }
        }
        else
        {
            if( $this->service->isSoap() )
            {
                $obj->dynamicFields = new \stdClass();
                $obj->dynamicFields->dynamicField = array();
            }
            elseif( $this->service->isRest() )
                $obj->dynamicFields = array();
            
            for( $i = 0; $i < $count; $i++ )
            {
                if( $this->service->isSoap() )
                    $obj->dynamicFields->dynamicField[] = 
                        $this->dynamic_fields[ $i ]->toStdClass();
                elseif( $this->service->isRest() )
                    $obj->dynamicFields[] = $this->dynamic_fields[ $i ]->toStdClass();
            }
        }
        
        return $obj;
    }
    
/**
<documentation><description><p>Copies all the values of the wired fields
from the old metadata to the new metadata. Note that if a field is required in the new
metadata and there is no value in the corresponding field of the old metadata, then the
string "NULL" will be used as the value.</p></description>
<example>p\Metadata::copyWiredFields( $old_m, $new_m );</example>
<return-type>void</return-type>
</documentation>
*/
    public static function copyWiredFields( Metadata $old_m, Metadata $new_m )
    {
        foreach( self::$wired_fields as $field )
        {
            $get_method_name = u\StringUtility::getMethodName( $field );
            $set_method_name = u\StringUtility::getMethodName( $field, "set" );
            $is_method_name  = u\StringUtility::getMethodName( $field, "is" ) .
                "FieldRequired";
            
            $field_value    = $old_m->$get_method_name();
            $field_required = $new_m->$is_method_name();
            
            if( $field_required && ( is_null( $field_value ) || $field_value == "" ) )
            {
                $field_value = u\StringUtility::getCoalescedString( NULL );
            }
                
            $new_m->$set_method_name( $field_value );
        }
    }

/**
<documentation><description><p>Returns the <code>get</code> method name corresponding to the supplied wired field.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public static function getWiredFieldMethodName( string $field_name ) : string
    {
        if( self::isWiredField( $field_name ) )
        {
            return u\StringUtility::getMethodName( $field_name );
        }
        return NULL;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the supplied name is associated with a wired field.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public static function isWiredField( string $field_name ) : bool
    {
        // check the array in MetadataSet
        $result = in_array( $field_name, a\MetadataSet::$wired_fields );
        
        // check the array in this class
        if( !$result )
            $result = in_array( $field_name, self::$wired_fields );
            
        return $result;
    }
    
    private function checkMetadataSet()
    {
        if( $this->metadata_set == NULL )
        {
            $this->metadata_set = new a\MetadataSet( 
                $this->service, $this->service->createId( 
                    a\MetadataSet::TYPE, $this->metadata_set_id ) );
        }
    }
    
    private function processDynamicFields( $fields )
    {
        $this->dynamic_fields = array();

        if( !is_array( $fields ) )
        {
            $fields = array( $fields );
        }
        
        foreach( $fields as $field )
        {
            $df = new DynamicField( $field, $this->service );
            $this->dynamic_fields[] = $df;
            $this->dynamic_field_names[] = $field->name;
        }
    }
    
/*    
    private static $wired_fields = array(
        'author', 'displayName', 'endDate', 'keywords', 'metaDescription',
        'reviewDate', 'startDate', 'summary', 'teaser', 'title'
    );
*/    
    private $author;
    private $display_name;
    private $end_date;
    private $keywords;
    private $meta_description;
    private $review_date;
    private $start_date;
    private $summary;
    private $teaser;
    private $title;
    private $dynamic_fields;
    private $dynamic_field_names;
    private $service;
    private $metadata_set_id;
    private $host_asset;
}
?>
