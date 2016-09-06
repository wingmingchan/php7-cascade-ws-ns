<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/6/2016 Added all isXRequired methods.
  * 1/8/2016 Added code to deal with host asset.
  * 5/28/2015 Added namespaces.
  * 9/21/2014 Fixed a bug in toStdClass.
  * 7/30/2014 Fixed bugs in setEndDate, setReviewDate, setStartDate.
  * 7/28/2014 Added isWiredField and getWiredFieldMethodName.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_asset as a;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
 
class Metadata extends Property
{
    const DEBUG = false;
    const DUMP  = false;

    public function __construct( 
        \stdClass $obj=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $metadata_set_id=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $obj ) )
        {
            $this->author              = $obj->author;
            $this->display_name        = $obj->displayName;
            $this->end_date            = $obj->endDate;
            $this->keywords            = $obj->keywords;
            $this->meta_description    = $obj->metaDescription;
            $this->review_date         = $obj->reviewDate;
            $this->start_date          = $obj->startDate;
            $this->summary             = $obj->summary;
            $this->teaser              = $obj->teaser;
            $this->title               = $obj->title;
            $this->service             = $service;
            $this->metadata_set        = NULL;
            $this->metadata_set_id     = $metadata_set_id;
        
            if( isset( $obj->dynamicFields ) ) // could be NULL
            {
                $this->processDynamicFields( $obj->dynamicFields->dynamicField );
            }
            
            $this->host_asset = $data2; // could be null
        }
    }
    
    public function getAuthor()
    {
        return $this->author;
    }
    
    public function getDisplayName()
    {
        return $this->display_name;
    }
    
    public function getDynamicField( $name )
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

    public function getDynamicFieldNames()
    {
        return $this->dynamic_field_names;
    }
    
    public function getDynamicFieldPossibleValues( $name )
    {
        return $this->getMetadataSet()->getDynamicMetadataFieldPossibleValueStrings( $name );
    }
    
    public function getDynamicFields()
    {
        return $this->dynamic_fields;
    }
    
    public function getDynamicFieldValues( $name )
    {
        $name = trim( $name );
        
        if( $name == '' )
            throw new e\EmptyNameException(
                S_SPAN . c\M::EMPTY_NAME . E_SPAN );
    
        $field = $this->getDynamicField( $name );
        
        return $field->getFieldValue()->getValues();
    }
    
    public function getEndDate()
    {
        return $this->end_date;
    }
    
    public function getHostAsset()
    {
        return $this->host_asset;
    }
    
    public function getKeywords()
    {
        return $this->keywords;
    }
    
    public function getMetadataSet()
    {
        if( $this->metadata_set == NULL )
        {
            $this->metadata_set = new a\MetadataSet( 
                $this->service, $this->service->createId( 
                    a\MetadataSet::TYPE, $this->metadata_set_id ) );
        }

        return $this->metadata_set;
    }
    
    public function getMetaDescription()
    {
        return $this->meta_description;
    }
    
    public function getReviewDate()
    {
        return $this->review_date;
    }
    
    public function getStartDate()
    {
        return $this->start_date;
    }
    
    public function getSummary()
    {
        return $this->summary;
    }
    
    public function getTeaser()
    {
        return $this->teaser;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function hasDynamicField( $name )
    {
        if( $name == '' )
            throw new e\EmptyNameException(
                S_SPAN . c\M::EMPTY_NAME . E_SPAN );
                
        if( !isset( $this->dynamic_field_names ) )
            return false;
    
        return in_array( $name, $this->dynamic_field_names );
    }
    
    public function isAuthorFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getAuthorFieldRequired();      
    }
    
    public function isDescriptionFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getDescriptionFieldRequired();      
    }
    
    public function isDynamicFieldRequired( string $name ) : bool
    {
        return isDynamicMetadataFieldRequired( $name );      
    }
    
    public function isDynamicMetadataFieldRequired( string $name ) : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->isDynamicMetadataFieldRequired( $name );      
    }
    
    public function isDisplayNameFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getDisplayNameFieldRequired();      
    }
    
    public function isEndDateFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getEndDateFieldRequired();      
    }
    
    public function isExpirationFolderFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getExpirationFolderFieldRequired();      
    }
    
    public function isKeywordsFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getKeywordsFieldRequired();      
    }
    
    public function isReviewDateFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getReviewDateFieldRequired();      
    }
    
    public function isStartDateFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getStartDateFieldRequired();      
    }
    
    public function isSummaryFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getSummaryFieldRequired();      
    }
    
    public function isTeaserFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getTeaserFieldRequired();      
    }
    
    public function isTitleFieldRequired() : bool
    {
        $this->checkMetadataSet();
        return $this->metadata_set->getTitleFieldRequired();      
    }
    
    public function setAuthor( $author )
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
    
    public function setDisplayName( $display_name )
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
    
    public function setDynamicField( $field, $values )
    {
        return $this->setDynamicFieldValue( $field, $values );
    }
    
    public function setDynamicFieldValue( $field, $values ) // string or string array
    {
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
    
    public function setDynamicFieldValues( $field, $values )
    {
        return $this->setDynamicField( $field, $values );
    }
    
    public function setEndDate( $end_date )
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
    
    public function setKeywords( $keywords )
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
    
    public function setMetaDescription( $meta_description )
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
    
    public function setReviewDate( $review_date )
    {
        $review_date = trim( $review_date );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getReviewDateFieldRequired() && ( $review_date == '' || $review_date == NULL ) )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The reviewDate field is required." . E_SPAN );
        }

        if( $review_date == "" )
            $review_date = NULL;
            
        $this->review_date = $review_date;
        return $this;
    }
    
    public function setStartDate( $start_date )
    {
        $start_date = trim( $start_date );
    
        $this->checkMetadataSet();
                
        if( $this->metadata_set->getStartDateFieldRequired() && ( $start_date == '' || $start_date == NULL ) )
        {
            throw new e\RequiredFieldException(
                S_SPAN . "The startDate field is required." . E_SPAN );
        }
        
        if( $start_date == "" )
            $start_date = NULL;

        $this->start_date = $start_date;
        return $this;
    }
    
    public function setSummary( $summary )
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
    
    public function setTeaser( $teaser )
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
    
    public function setTitle( $title )
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
    
    public function toStdClass()
    {
        $obj                  = new \stdClass();
        $obj->author          = $this->author;
        $obj->displayName     = $this->display_name;
        $obj->endDate         = $this->end_date;
        $obj->keywords        = $this->keywords;
        $obj->metaDescription = $this->meta_description;
        $obj->reviewDate      = $this->review_date;
        $obj->startDate       = $this->start_date;
        $obj->summary         = $this->summary;
        $obj->teaser          = $this->teaser;
        $obj->title           = $this->title;

        $count = count( $this->dynamic_fields );
        
        if( $count == 0 )
        {
            $obj->dynamicFields = NULL;
        }
        else if( $count == 1 )
        {
            $obj->dynamicFields = new \stdClass();
            $obj->dynamicFields->dynamicField = $this->dynamic_fields[0]->toStdClass();
        }
        else
        {
            $obj->dynamicFields = new \stdClass();
            $obj->dynamicFields->dynamicField = array();
            
            for( $i = 0; $i < $count; $i++ )
            {
                $obj->dynamicFields->dynamicField[] = 
                    $this->dynamic_fields[$i]->toStdClass();
            }
        }
        
        return $obj;
    }
    
    public static function getWiredFieldMethodName( $field_name )
    {
        if( self::isWiredField( $field_name ) )
        {
            return u\StringUtility::getMethodName( $field_name );
        }
        return NULL;
    }

    public static function isWiredField( $field_name )
    {
        return in_array( $field_name, self::$wired_fields );
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
        $this->dynamic_fields      = array();
        $this->dynamic_field_names = array();

        if( !is_array( $fields ) )
        {
            $fields = array( $fields );
        }
        
        foreach( $fields as $field )
        {
            $df = new DynamicField( $field );
            $this->dynamic_fields[] = $df;
            $this->dynamic_field_names[] = $field->name;
        }
    }
    
    private static $wired_fields = array(
        'author', 'displayName', 'endDate', 'keywords', 'metaDescription',
        'reviewDate', 'startDate', 'summary', 'teaser', 'title'
    );
    
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
