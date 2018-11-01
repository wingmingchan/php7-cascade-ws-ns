<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 1/22/2018 Added code to pass in the $service object to the DynamicMetadataFieldDefinition class.
  * 1/3/2018 Added code to test for NULL.
  * 12/27/2017 Updated documentation.
  * 12/26/2017 Added REST code to edit.
  * 7/31/2017 Added help text related fields, get and set methods.
  * 6/26/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 9/14/2016 Added removeField.
  * 9/9/2016 Added $wired_fields. Added default values to getMetadata.
  * 9/6/2016 Added isDynamicMetadataFieldRequired.
  * 12/29/2015 Added expirationFolderFieldRequired and expirationFolderFieldVisibility for 8.
  * 9/14/2015 Added getMetaData.
  * 5/28/2015 Added namespaces.
  * 9/18/2014 Added a call to edit in a few methods.
  * 7/14/2014 Added getDynamicMetadataFieldDefinitionsStdClass and setDynamicMetadataFieldDefinitions.
  * 7/7/2014 Added addField and fixed some bugs.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>MetadataSet</code> object represents a metadata set asset. See <a href=\"http://www.upstate.edu/web-services/api/property-classes/dynamic-field.php\">DynamicField</a> for an important note on the use of default values. Use <code>MetadataSet::setSelectedByDefault</code> carefully.</p>
<h2>Structure of <code>metadataSet</code></h2>
<pre>SOAP:
metadataSet
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  authorFieldRequired
  authorFieldVisibility
  authorFieldHelpText (8.5)
  descriptionFieldRequired
  descriptionFieldVisibility
  descriptionFieldHelpText (8.5)
  displayNameFieldRequired
  displayNameFieldVisibility
  displayNameFieldHelpText (8.5)
  endDateFieldRequired
  endDateFieldVisibility
  endDateFieldHelpText (8.5)
  expirationFolderFieldRequired (8)
  expirationFolderFieldVisibility (8)
  expirationFolderFieldHelpText (8.5)
  keywordsFieldRequired
  keywordsFieldVisibility
  keywordsFieldHelpText (8.5)
  reviewDateFieldRequired
  reviewDateFieldVisibility
  reviewDateFieldHelpText (8.5)
  startDateFieldRequired
  startDateFieldVisibility
  startDateFieldHelpText (8.5)
  summaryFieldRequired
  summaryFieldVisibility (8.5)
  teaserFieldRequired
  teaserFieldVisibility
  teaserFieldHelpText (8.5)
  titleFieldRequired
  titleFieldVisibility
  titleFieldHelpText (8.5)
  dynamicMetadataFieldDefinitions
    dynamicMetadataFieldDefinition (NULL, stdClass or array of stdClass)
      name
      label
      fieldType
      required
      visibility
      possibleValues
        possibleValue (NULL, stdClass or array of stdClass)
          value
          selectedByDefault
      helpText

REST:
metadataSet
  authorFieldRequired
  authorFieldVisibility
  descriptionFieldRequired
  descriptionFieldVisibility
  displayNameFieldRequired
  displayNameFieldVisibility
  endDateFieldRequired
  endDateFieldVisibility
  expirationFolderFieldRequired (8)
  expirationFolderFieldVisibility (8)
  keywordsFieldRequired
  keywordsFieldVisibility
  reviewDateFieldRequired
  reviewDateFieldVisibility
  startDateFieldRequired
  startDateFieldVisibility
  summaryFieldRequired
  summaryFieldVisibility
  teaserFieldRequired
  teaserFieldVisibility
  titleFieldRequired
  titleFieldVisibility
  dynamicMetadataFieldDefinitions (array of stdClass)
    name
    label
    fieldType
    required
    visibility
    possibleValues (array of stdClass)
      value
      selectedByDefault
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  name
  id 
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "metadataSet" ),
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definitions" ),
        array( "getComplexTypeXMLByName" => "dynamicMetadataFieldDefinition" ),
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definition-values" ),
        array( "getComplexTypeXMLByName" => "dynamic-metadata-field-definition-value" ),
        array( "getSimpleTypeXMLByName"  => "metadata-field-visibility" ),
        array( "getSimpleTypeXMLByName"  => "dynamic-metadata-field-type" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/metadata_set.php">metadata_set.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/metadataset/647e77e18b7f085600ae2282629d7ea0

{
  "asset":{
    "metadataSet":{
      "authorFieldRequired":false,
      "authorFieldVisibility":"hidden",
      "descriptionFieldRequired":false,
      "descriptionFieldVisibility":"hidden",
      "displayNameFieldRequired":true,
      "displayNameFieldVisibility":"inline",
      "endDateFieldRequired":false,
      "endDateFieldVisibility":"hidden",
      "expirationFolderFieldRequired":false,
      "expirationFolderFieldVisibility":"visible",
      "keywordsFieldRequired":false,
      "keywordsFieldVisibility":"hidden",
      "reviewDateFieldRequired":false,
      "reviewDateFieldVisibility":"hidden",
      "startDateFieldRequired":false,
      "startDateFieldVisibility":"hidden",
      "summaryFieldRequired":false,
      "summaryFieldVisibility":"hidden",
      "teaserFieldRequired":false,
      "teaserFieldVisibility":"hidden",
      "titleFieldRequired":false,
      "titleFieldVisibility":"hidden",
      "dynamicMetadataFieldDefinitions":[
      {
        "name":"exclude-from-menu-bar",
        "label":"Exclude from Menu Bar",
        "fieldType":"checkbox",
        "required":false,
        "visibility":"inline",
        "possibleValues":[
        {
          "value":"Yes",
          "selectedByDefault":false
        } ]
      },
      {
        "name":"displayed-as-submenu",
        "label":"Display As Submenu",
        "fieldType":"checkbox",
        "required":false,
        "visibility":"inline",
        "possibleValues":[
        {
          "value":"Yes",
          "selectedByDefault":false
        } ]
      },
      {
        "name":"pdf-icon",
        "label":"PDF Icon",
        "fieldType":"checkbox",
        "required":false,
        "visibility":"inline",
        "possibleValues":[
        {
          "value":"Yes",
          "selectedByDefault":false
        } ]
      },
      {
        "name":"other-icon",
        "label":"Other Icon",
        "fieldType":"radio",
        "required":false,
        "visibility":"inline",
        "possibleValues":[
        {
          "value":"None",
          "selectedByDefault":true
        },
        {
          "value":"External",
          "selectedByDefault":false
        },
        {
          "value":"Intra",
          "selectedByDefault":false
        } ]
      } ],
      "parentContainerId":"647db3ab8b7f085600ae2282d55a5b6d",
      "parentContainerPath":"Test Metadata Set Container",
      "path":"Test Metadata Set Container/External Link",
      "siteId":"fd27691f8b7f08560159f3f02754e61d",
      "siteName":"_common",
      "name":"External Link",
      "id":"647e77e18b7f085600ae2282629d7ea0"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre>
</postscript>
</documentation>
*/
class MetadataSet extends ContainedAsset
{
    const DEBUG    = false;
    const DUMP     = false;
    const TYPE     = c\T::METADATASET;
    const HIDDEN   = c\T::HIDDEN;
    const INLINE   = c\T::INLINE;
    const VISIBLE  = c\T::VISIBLE;
    
    const AUTHOR      = "author";
    const DESCRIPTION = "description";
    const DISPLAYNAME = "display-name";
    const KEYWORDS    = "keywords";
    const SUMMARY     = "summary";
    const TEASER      = "teaser";
    const TITLE       = "title";
    
    public static $wired_fields = array(
        "author", "description", "displayName", "endDate", "expirationFolder",
        "keywords", "reviewDate", "startDate", "summary", "teaser", "title", 
    );
    
/**
<documentation><description><p>The constructor, overriding the parent method to process dynamic metadata field definition.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        if( isset( $this->getProperty()->dynamicMetadataFieldDefinitions ) )
        {
            if( $this->getService()->isSoap() &&
                isset( $this->getProperty()->dynamicMetadataFieldDefinitions->
                    dynamicMetadataFieldDefinition ) )
                $this->processDynamicMetadataFieldDefinition();
            elseif( $this->getService()->isRest() )
                $this->processDynamicMetadataFieldDefinition();
        }
    }
    
/**
<documentation><description><p>Adds a dynamic metadata set field definition , calls <code>edit</code>, and returns the calling object.
The <code>$possible_values</code> should be a string containing values, with semi-colons as delimiters.</p></description>
<example>$ms->
    addField( 
        'text',             // field name
        c\T::TEXT,          // type
        'Text',             // label
        false,              // required
        c\T::INLINE,        // visibility
        ""                  // possible value
    );
</example>
<return-type>Asset</return-type>
<exception>EmptyValueException, Exception</exception>
</documentation>
*/
    public function addDynamicFieldDefinition( string $field_name, string $type, string $label, 
        bool $required=false, string $visibility=c\T::VISIBLE, string $possible_values="" ) : Asset
    {
        if( $this->hasDynamicMetadataFieldDefinition( $field_name ) )
        {
            throw new \Exception( 
                S_SPAN . "The dynamic field definition $field_name already exists." . E_SPAN );
        }
        
        if( $type != c\T::TEXT && trim( $possible_values ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_POSSIBLE_VALUES . E_SPAN );
        }
        
        $dmfd = AssetTemplate::getDynamicMetadataFieldDefinition();
        $dmfd->dynamicMetadataFieldDefinition->name       = $field_name;
        $dmfd->dynamicMetadataFieldDefinition->label      = $label;
        $dmfd->dynamicMetadataFieldDefinition->fieldType  = $type;
        $dmfd->dynamicMetadataFieldDefinition->required   = $required;
        $dmfd->dynamicMetadataFieldDefinition->visibility = $visibility;
        
        if( $type != c\T::TEXT )
        {
            if( $this->getService()->isSoap() )
                $dmfd->dynamicMetadataFieldDefinition->possibleValues = new \stdClass();
            elseif( $this->getService()->isRest() )
                $dmfd->dynamicMetadataFieldDefinition->possibleValues = array();
            
            $values      = u\StringUtility::getExplodedStringArray( ";", $possible_values );
            $value_count = count( $values );
            
            if( $value_count == 1 )
            {
                $pv                    = new \stdClass();
                $pv->value             = $values[ 0 ];
                $pv->selectedByDefault = false;
                
                if( $this->getService()->isSoap() )
                {
                    $dmfd->dynamicMetadataFieldDefinition->possibleValues->
                        possibleValue = $pv;
                }
                elseif( $this->getService()->isRest() )
                {
                    $dmfd->dynamicMetadataFieldDefinition->possibleValues[] = $pv;
                }
                    
            }
            else
            {
                if( $this->getService()->isSoap() )
                {
                    $dmfd->dynamicMetadataFieldDefinition->possibleValues->
                        possibleValue = array();
                }
                
                foreach( $values as $value )
                {
                    if( self::DEBUG ) { u\DebugUtility::out( $value ); }
                
                    $pv                    = new \stdClass();
                    $pv->value             = $value;
                    $pv->selectedByDefault = false;
                    
                    if( $this->getService()->isSoap() )
                        $dmfd->dynamicMetadataFieldDefinition->possibleValues->
                            possibleValue[] = $pv;
                    elseif( $this->getService()->isRest() )
                        $dmfd->dynamicMetadataFieldDefinition->possibleValues[] = $pv;
                }
            }
        }
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $dmfd ); }
        
        $dmfd_obj = new p\DynamicMetadataFieldDefinition(
            $dmfd->dynamicMetadataFieldDefinition, $this->getService() );
        
        $this->dynamic_metadata_field_definitions[] = $dmfd_obj;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $dmfd_obj->toStdClass() ); }
        
        $this->edit();
        $this->processDynamicMetadataFieldDefinition();
        
        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>addDynamicFieldDefinition</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addField( string $field_name, string $type, string $label, 
        bool $required=false, string $visibility=c\T::VISIBLE, string $possible_values="" ) : Asset
    {
        return $this->addDynamicFieldDefinition(
            $field_name, $type, $label, $required, $visibility, $possible_values );
    }
    
    /**
     * Appends a value/item to the end of a field.
     */
/**
<documentation><description><p>Adds a value to the <code>dynamicMetadataFieldDefinition</code> bearing that name, calls <code>edit</code>,
and returns the calling object.</p></description>
<example>$ms->appendValue( $name, "No" );</example>
<return-type></return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function appendValue( string $name, string $value ) : Asset
    {
        $value = trim( $value );
        
        if( $value == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
            
        $def = $this->getDynamicMetadataFieldDefinition( $name );
        $def->appendValue( $value );
        $this->edit();
        $this->processDynamicMetadataFieldDefinition();

        return $this;
    }
       
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example>$ms->edit();</example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
</documentation>
*/
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $asset = new \stdClass();
        $metadata_set = $this->getProperty();
        
        if( $this->getService()->isSoap() )
            $metadata_set->dynamicMetadataFieldDefinitions->
                dynamicMetadataFieldDefinition = array();
        elseif( $this->getService()->isRest() )
            $metadata_set->dynamicMetadataFieldDefinitions = array();
            
        $count = $this->dynamic_metadata_field_definitions;
        
        if( $count > 0 )
            foreach( $this->dynamic_metadata_field_definitions as $definition )
            {
                if( $this->getService()->isSoap() )
                    $metadata_set->dynamicMetadataFieldDefinitions->
                        dynamicMetadataFieldDefinition[] = $definition->toStdClass();
                elseif( $this->getService()->isRest() )
                    $metadata_set->dynamicMetadataFieldDefinitions[] =
                        $definition->toStdClass();
            }
        
        //u\DebugUtility::dump( $metadata_set );
        $asset->{ $p = $this->getPropertyName() } = $metadata_set;
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }

        return $this->reloadProperty();
    }
    
/**
<documentation><description><p>Returns <code>authorFieldHelpText</code>.</p></description>
<example>echo $ms->getAuthorFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getAuthorFieldHelpText()
    {
        if( isset( $this->getProperty()->authorFieldHelpText ) )
            return $this->getProperty()->authorFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>authorFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getAuthorFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getAuthorFieldRequired() : bool
    {
        return $this->getProperty()->authorFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>authorFieldVisibility</code>.</p></description>
<example>echo $ms->getAuthorFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getAuthorFieldVisibility() : string
    {
        return $this->getProperty()->authorFieldVisibility;
    }
    
/**
<documentation><description><p>Returns <code>descriptionFieldHelpText</code>.</p></description>
<example>echo $ms->getDescriptionFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDescriptionFieldHelpText()
    {
        if( isset( $this->getProperty()->descriptionFieldHelpText ) )
            return $this->getProperty()->descriptionFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>descriptionFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getDescriptionFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getDescriptionFieldRequired() : bool
    {
        return $this->getProperty()->descriptionFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>descriptionFieldVisibility</code>.</p></description>
<example>echo $ms->getDescriptionFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDescriptionFieldVisibility() : string
    {
        return $this->getProperty()->descriptionFieldVisibility;
    }
    
/**
<documentation><description><p>Returns <code>displayNameFieldHelpText</code>.</p></description>
<example>echo $ms->getDisplayNameFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDisplayNameFieldHelpText()
    {
        if( isset( $this->getProperty()->displayNameFieldHelpText ) )
            return $this->getProperty()->displayNameFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>displayNameFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getDisplayNameFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getDisplayNameFieldRequired() : bool
    {
        return $this->getProperty()->displayNameFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>displayNameFieldVisibility</code>.</p></description>
<example>echo $ms->getDisplayNameFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDisplayNameFieldVisibility() : string
    {
        return $this->getProperty()->displayNameFieldVisibility;
    }
    
/**
<documentation><description><p>Returns the <a href="http://www.upstate.edu/web-services/api/property-classes/dynamic-metadata-field-definition.php"><code>DynamicMetadataFieldDefinition</code></a> object bearing that name.</p></description>
<example>$dmd = $ms->getDynamicMetadataFieldDefinition( $name );</example>
<return-type>DynamicMetadataFieldDefinition</return-type>
<exception>NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function getDynamicMetadataFieldDefinition( string $name ) : p\DynamicMetadataFieldDefinition
    {
        if( !$this->hasDynamicMetadataFieldDefinition( $name ) )
            throw new e\NoSuchMetadataFieldDefinitionException( 
                S_SPAN . "The definition $name does not exist." . E_SPAN );
        
        foreach( $this->dynamic_metadata_field_definitions as $definition )
        {
            if( $definition->getName() == $name )
                return $definition;
        }
    }
    
/**
<documentation><description><p>Returns an array of names of the dynamic fields.</p></description>
<example>u\DebugUtility::dump( $ms->getDynamicMetadataFieldDefinitionNames() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getDynamicMetadataFieldDefinitionNames() : array
    {
        return $this->field_names;
    }
    
/**
<documentation><description><p>Returns the <code>dynamicMetadataFieldDefinitions</code> property.</p></description>
<example>u\DebugUtility::dump( $ms->getDynamicMetadataFieldDefinitionsStdClass() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDynamicMetadataFieldDefinitionsStdClass()
    {
        return $this->getProperty()->dynamicMetadataFieldDefinitions;
    }
    
/**
<documentation><description><p>Returns an array of strings, each of which is a possible value (i.e., an item) of the field bearing the name.
If the field is a text field, this method returns an empty string.</p></description>
<example>u\DebugUtility::dump( $ms->getDynamicMetadataFieldPossibleValueStrings( "dropdown" ) );</example>
<return-type>mixed</return-type>
<exception>NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function getDynamicMetadataFieldPossibleValueStrings( string $name )
    {
        if( !$this->hasDynamicMetadataFieldDefinition( $name ) )
            throw new e\NoSuchMetadataFieldDefinitionException( 
                S_SPAN . "The definition $name does not exist." . E_SPAN );
                
        foreach( $this->dynamic_metadata_field_definitions as $definition )
        {
            if( $definition->getName() == $name )
                return $definition->getPossibleValueStrings();
        }
    }

/**
<documentation><description><p>Returns <code>endDateFieldHelpText</code>.</p></description>
<example>echo $ms->getEndDateFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getEndDateFieldHelpText()
    {
        if( isset( $this->getProperty()->endDateFieldHelpText ) )
            return $this->getProperty()->endDateFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>endDateFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getEndDateFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getEndDateFieldRequired() : bool
    {
        return $this->getProperty()->endDateFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>endDateFieldVisibility</code>.</p></description>
<example>echo $ms->getEndDateFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getEndDateFieldVisibility() : string
    {
        return $this->getProperty()->endDateFieldVisibility;
    }

/**
<documentation><description><p>Returns <code>expirationFolderFieldHelpText</code>.</p></description>
<example>echo $ms->getExpirationFolderFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderFieldHelpText()
    {
        if( isset( $this->getProperty()->expirationFolderFieldHelpText ) )
            return $this->getProperty()->expirationFolderFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>expirationFolderFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getExpirationFolderFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderFieldRequired() : bool
    {
        return $this->getProperty()->expirationFolderFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>expirationFolderFieldVisibility</code>.</p></description>
<example>echo $ms->getExpirationFolderFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderFieldVisibility() : string
    {
        return $this->getProperty()->expirationFolderFieldVisibility;
    }
   
/**
<documentation><description><p>Returns <code>keywordsFieldHelpText</code>.</p></description>
<example>echo $ms->getKeywordsFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getKeywordsFieldHelpText()
    {
        if( isset( $this->getProperty()->keywordsFieldHelpText ) )
            return $this->getProperty()->keywordsFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>keywordsFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getKeywordsFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getKeywordsFieldRequired() : bool
    {
        return $this->getProperty()->keywordsFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>keywordsFieldVisibility</code>.</p></description>
<example>echo $ms->getKeywordsFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getKeywordsFieldVisibility() : string
    {
        return $this->getProperty()->keywordsFieldVisibility;
    }
    
/**
<documentation><description><p>Returns a blank <a href="http://www.upstate.edu/web-services/api/property-classes/metadata.php"><code>Metadata</code></a> object.
Note that the object is populated with default values in dynamic fields.</p></description>
<example>u\DebugUtility::dump( $ms->getMetadata()->toStdClass() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadata() : p\Property
    {
        $m = AssetTemplate::getMetadata();
        
        if( isset( $this->getProperty()->dynamicMetadataFieldDefinitions->dynamicMetadataFieldDefinition ) &&
            is_array( $this->getProperty()->dynamicMetadataFieldDefinitions->dynamicMetadataFieldDefinition )
        )
        {
            $defs = $this->getProperty()->dynamicMetadataFieldDefinitions->dynamicMetadataFieldDefinition;
            $a    = array();
            
            foreach( $defs as $def )
            {
                $df              = new \stdClass();
                $df->name        = $def->name;
                $df->fieldValues = new \stdClass();
                $df->fieldValues->fieldValue = array();
                $a[] = $df;
            }
            $m->dynamicFields = new \stdClass();
            $m->dynamicFields->dynamicField = $a;
        }
        
        $metadata = new p\Metadata( $m, $this->getService(), $this->getId() );
        
        // default values
        if( $this->hasDynamicMetadataFieldDefinitions() )
        {
            $df_names = $this->getDynamicMetadataFieldDefinitionNames();
            
            foreach( $df_names as $df_name )
            {
                $df = $this->getDynamicMetadataFieldDefinition( $df_name );
                
                if( $df->hasDefaultValue() )
                {
                    $metadata->setDynamicFieldValue( $df_name, $df->getDefaultValueString() );
                }
            }
        }
        
        return $metadata;
    }
    
/**
<documentation><description><p>Returns an array of names of wired fields that are not hidden, used by the <code>WordPressConnector</code> class.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getNonHiddenWiredFieldNames() : array  // used by WordPressConnector
    {
        $fields = array();
        
        if( $this->getProperty()->authorFieldVisibility != self::HIDDEN )
            $fields[] = self::AUTHOR;
        if( $this->getProperty()->descriptionFieldVisibility != self::HIDDEN )
            $fields[] = self::DESCRIPTION;
        if( $this->getProperty()->displayNameFieldVisibility != self::HIDDEN )
            $fields[] = self::DISPLAYNAME;
        if( $this->getProperty()->keywordsFieldVisibility != self::HIDDEN )
            $fields[] = self::KEYWORDS;
        if( $this->getProperty()->summaryFieldVisibility != self::HIDDEN )
            $fields[] = self::SUMMARY;
        if( $this->getProperty()->teaserFieldVisibility != self::HIDDEN )
            $fields[] = self::TEASER;
        if( $this->getProperty()->titleFieldVisibility != self::HIDDEN )
            $fields[] = self::TITLE;
            
        return $fields;
    }
    
/**
<documentation><description><p>Returns <code>reviewDateFieldHelpText</code>.</p></description>
<example>echo $ms->getReviewDateFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getReviewDateFieldHelpText()
    {
        if( isset( $this->getProperty()->reviewDateFieldHelpText ) )
            return $this->getProperty()->reviewDateFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>reviewDateFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getReviewDateFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getReviewDateFieldRequired() : bool
    {
        return $this->getProperty()->reviewDateFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>reviewDateFieldVisibility</code>.</p></description>
<example>echo $ms->getReviewDateFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getReviewDateFieldVisibility() : string
    {
        return $this->getProperty()->reviewDateFieldVisibility;
    }
    
/**
<documentation><description><p>Returns <code>startDateFieldHelpText</code>.</p></description>
<example>echo $ms->getStartDateFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getStartDateFieldHelpText()
    {
        if( isset( $this->getProperty()->startDateFieldHelpText ) )
            return $this->getProperty()->startDateFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>startDateFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getStartDateFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getStartDateFieldRequired() : bool
    {
        return $this->getProperty()->startDateFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>startDateFieldVisibility</code>.</p></description>
<example>echo $ms->getStartDateFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getStartDateFieldVisibility() : string
    {
        return $this->getProperty()->startDateFieldVisibility;
    }
    
/**
<documentation><description><p>Returns <code>summaryFieldHelpText</code>.</p></description>
<example>echo $ms->getSummaryFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSummaryFieldHelpText()
    {
        if( isset( $this->getProperty()->summaryFieldHelpText ) )
            return $this->getProperty()->summaryFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>summaryFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getSummaryFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getSummaryFieldRequired() : bool
    {
        return $this->getProperty()->summaryFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>summaryFieldVisibility</code>.</p></description>
<example>echo $ms->getSummaryFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSummaryFieldVisibility() : string
    {
        return $this->getProperty()->summaryFieldVisibility;
    }
    
/**
<documentation><description><p>Returns <code>teaserFieldHelpText</code>.</p></description>
<example>echo $ms->getTeaserFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTeaserFieldHelpText()
    {
        if( isset( $this->getProperty()->teaserFieldHelpText ) )
            return $this->getProperty()->teaserFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>teaserFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getTeaserFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getTeaserFieldRequired() : bool
    {
        return $this->getProperty()->teaserFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>teaserFieldVisibility</code>.</p></description>
<example>echo $ms->getTeaserFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTeaserFieldVisibility() : string
    {
        return $this->getProperty()->teaserFieldVisibility;
    }
    
/**
<documentation><description><p>Returns <code>titleFieldHelpText</code>.</p></description>
<example>echo $ms->getTitleFieldHelpText() . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTitleFieldHelpText()
    {
        if( isset( $this->getProperty()->titleFieldHelpText ) )
            return $this->getProperty()->titleFieldHelpText;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>titleFieldRequired</code>.</p></description>
<example>echo u\StringUtility::boolToString( $ms->getTitleFieldRequired() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getTitleFieldRequired() : bool
    {
        return $this->getProperty()->titleFieldRequired;
    }
    
/**
<documentation><description><p>Returns <code>titleFieldVisibility</code>.</p></description>
<example>echo $ms->getTitleFieldVisibility() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTitleFieldVisibility() : string
    {
        return $this->getProperty()->titleFieldVisibility;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>dynamicMetadataFieldDefinition</code> bearing that name exists.</p></description>
<example>if( $ms->hasDynamicMetadataFieldDefinition( $name ) )
{
    echo "Definition found" . BR;
}</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasDynamicMetadataFieldDefinition( string $name ) : bool
    {
        if( !is_array( $this->field_names ) )
        {
            return false;
        }
        return in_array( $name, $this->field_names );
    }

/**
<documentation><description><p>Returns a bool, indicating whether the metadata set has one or more dynamic metadata field definitions.</p></description>
<example>if( $ms->hasDynamicMetadataFieldDefinitions() )
{
    // do something
}
</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasDynamicMetadataFieldDefinitions() : bool
    {
        return count( $this->dynamic_metadata_field_definitions ) != 0;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the named dynamic metadata field requires a value.</p></description>
<example>if( $ms->isDynamicMetadataFieldRequired( "text" ) )
{
    echo "The text field requires a value.", BR;
}</example>
<return-type>bool</return-type>
<exception>NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function isDynamicMetadataFieldRequired( string $name ) : bool
    {
        $dfd = $this->getDynamicMetadataFieldDefinition( $name );
        return $dfd->getRequired();
    }
    
/**
<documentation><description><p>Removes the field definition bearing that name, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$ms->removeDynamicMetadataFieldDefinition( $field );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeDynamicMetadataFieldDefinition( string $name ) : Asset
    {
        if( !in_array( $name, $this->field_names ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field $name does not exist." . E_SPAN );
        }
        
        $count = count( $this->dynamic_metadata_field_definitions );
        
        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->dynamic_metadata_field_definitions[ $i ]->getName() == $name )
            {
                $before       = array_slice( $this->dynamic_metadata_field_definitions, 0, $i );
                $names_before = array_slice( $this->field_names, 0, $i );
                $after        = array();
                $names_after  = array();
                
                if( $count - $i > 1 )
                {
                    $after       = array_slice( $this->dynamic_metadata_field_definitions, $i + 1 );
                    $names_after = array_slice( $this->field_names, $i + 1 );
                }
                $this->dynamic_metadata_field_definitions = array_merge( $before, $after );
                $this->field_names = array_merge( $names_before, $names_after );
                break;
            }
        }
        $this->edit();
        $this->processDynamicMetadataFieldDefinition();

        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>removeDynamicMetadataFieldDefinition</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeField( string $name ) : Asset
    {
        return $this->removeDynamicMetadataFieldDefinition( $name );
    }
    
/**
<documentation><description><p>Removes the value (i.e., item) from the named field definition, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$ms->removeValue( $name, "Maybe" );</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function removeValue( string $name, string $value ) : Asset
    {
        $value = trim( $value );
        
        if( $value == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
            
        $def = $this->getDynamicMetadataFieldDefinition( $name );
        $def->removeValue( $value );
        $this->edit();
        $this->processDynamicMetadataFieldDefinition();

        return $this;
    }
    
/**
<documentation><description><p>Sets <code>authorFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setAuthorFieldHelpText( "The author name" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setAuthorFieldHelpText( string $author_field_help_text="" ) : Asset
    {
        $this->getProperty()->authorFieldHelpText = $author_field_help_text;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>authorFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setAuthorFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAuthorFieldRequired( bool $author_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $author_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $author_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->authorFieldRequired = $author_field_required;
        return $this;
    }
    
/**
<documentation><description><p>sets <code>authorFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setAuthorFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAuthorFieldVisibility( string $author_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $author_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $author_field_visibility is not acceptable." . E_SPAN );

        $this->getProperty()->authorFieldVisibility = $author_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>descriptionFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setDescriptionFieldHelpText( "Description" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDescriptionFieldHelpText( string $description_field_help_text="" ) : Asset
    {
        $this->getProperty()->descriptionFieldHelpText = $description_field_help_text;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>descriptionFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setDescriptionFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDescriptionFieldRequired( bool $description_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $description_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $description_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->descriptionFieldRequired = $description_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>descriptionFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setDescriptionFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDescriptionFieldVisibility( string $description_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $description_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $description_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->descriptionFieldVisibility = $description_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>displayNameFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setDisplayNameFieldHelpText( "Display Name" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDisplayNameFieldHelpText( string $display_name_field_help_text="" ) : Asset
    {
        $this->getProperty()->displayNameFieldHelpText = $display_name_field_help_text;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>displayNameFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setDisplayNameFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDisplayNameFieldRequired( bool $display_name_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $display_name_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $display_name_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->displayNameFieldRequired = $display_name_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>displayNameFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setDisplayNameFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDisplayNameFieldVisibility( string $display_name_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $display_name_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $display_name_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->displayNameFieldVisibility = $display_name_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>dynamicMetadataFieldDefinitions</code> property, calls <code>edit</code>, and returns the calling object.
This method is used to replace the <code>dynamicMetadataFieldDefinitions</code> property.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDynamicMetadataFieldDefinitions( \stdClass $dmfd=NULL ) : Asset
    {
        if( $dmfd == NULL || !isset( $dmfd->dynamicMetadataFieldDefinition ) )
        {
            $this->getProperty()->dynamicMetadataFieldDefinitions = new \stdClass();
        }
        else
        {
            $this->dynamic_metadata_field_definitions = array();
            $this->field_names                        = array();

            $definitions = $dmfd->dynamicMetadataFieldDefinition;
            
            if( !is_array( $definitions ) )
            {
                $definitions = array( $definitions );
            }
        
            $count = count( $definitions );
        
            for( $i = 0; $i < $count; $i++ )
            {
                $this->dynamic_metadata_field_definitions[] = 
                    new p\DynamicMetadataFieldDefinition(
                        $definitions[ $i ], $this->getService() );
                $this->field_names[] = $definitions[ $i ]->name;
            }
        }
        return $this->edit();
    }
    
/**
<documentation><description><p>Sets <code>endDateFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setEndDateFieldHelpText( "End Date" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setEndDateFieldHelpText( string $end_date_field_help_text="" ) : Asset
    {
        $this->getProperty()->endDateFieldHelpText = $end_date_field_help_text;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>endDateFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setEndDateFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEndDateFieldRequired( bool $end_date_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $end_date_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $end_date_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->endDateFieldRequired = $end_date_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>endDateFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setEndDateFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEndDateFieldVisibility( string $end_date_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $end_date_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $end_date_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->endDateFieldVisibility = $end_date_field_visibility;
        return $this;
    }

/**
<documentation><description><p>Sets <code>expirationFolderFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setExpirationFolderFieldHelpText( "Expiration Folder" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setExpirationFolderFieldHelpText( string $expiration_folder_field_help_text="" ) : Asset
    {
        $this->getProperty()->expirationFolderFieldHelpText = $expiration_folder_field_help_text;
        return $this;
    }

/**
<documentation><description><p>Sets <code>expirationFolderFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setExpirationFolderFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setExpirationFolderFieldRequired( bool $expiration_folder_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $expiration_folder_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $expiration_folder_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->expirationFolderFieldRequired = $expiration_folder_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>expirationFolderFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setExpirationFolderFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setExpirationFolderFieldVisibility( string $expiration_folder_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $expiration_folder_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $expiration_folder_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->expirationFolderFieldVisibility = $expiration_folder_field_visibility;
        return $this;
    }
   
/**
<documentation><description><p>Sets <code>keywordsFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setKeywordsFieldHelpText( "Keywords" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setKeywordsFieldHelpText( string $keywords_field_help_text="" ) : Asset
    {
        $this->getProperty()->keywordsFieldHelpText = $keywords_field_help_text;
        return $this;
    }

/**
<documentation><description><p>Sets <code>keywordsFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setKeywordsFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setKeywordsFieldRequired( bool $keywords_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $keywords_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $keywords_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->keywordsFieldRequired = $keywords_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>keywordsFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setKeywordsFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setKeywordsFieldVisibility( string $keywords_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $keywords_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $keywords_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->keywordsFieldVisibility = $keywords_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets the <code>label</code> of the <code>dynamicMetadataFieldDefinition</code> bearing that name and returns the calling object.</p></description>
<example>$ms->setLabel( $name, "Exclude from Left Menu" )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function setLabel( string $name, string $label ) : Asset
    {
        $label = trim( $label );
        
        if( $label == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_LABEL . E_SPAN );
    
        if( $this->hasDynamicMetadataFieldDefinition( $name ) )
        {
            $d = $this->getDynamicMetadataFieldDefinition( $name );
            $d->setLabel( $label );
            
            return $this;
        }
        else
        {
            throw new e\NoSuchMetadataFieldDefinitionException( 
                S_SPAN . "The definition $name does not exist." . E_SPAN );
        }
    }
    
/**
<documentation><description><p>Sets the <code>required</code> of the <code>dynamicMetadataFieldDefinition</code> bearing that name and returns the calling object.</p></description>
<example>$ms->setRequired( $name, false )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function setRequired( string $name, bool $required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $required must be a boolean." . E_SPAN );
            
        if( $this->hasDynamicMetadataFieldDefinition( $name ) )
        {
            $d = $this->getDynamicMetadataFieldDefinition( $name );
            $d->setRequired( $required );
            
            return $this;
        }
        else
        {
            throw new e\NoSuchMetadataFieldDefinitionException( 
                S_SPAN . "The definition $name does not exist." . E_SPAN );
        }
    }
    
/**
<documentation><description><p>Sets <code>reviewDateFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setReviewDateFieldHelpText( "Review Date" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setReviewDateFieldHelpText( string $review_date_field_help_text="" ) : Asset
    {
        $this->getProperty()->reviewDateFieldHelpText = $review_date_field_help_text;
        return $this;
    }

/**
<documentation><description><p>Sets <code>reviewDateFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setReviewDateFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setReviewDateFieldRequired( bool $review_date_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $review_date_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $review_date_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->reviewDateFieldRequired = $review_date_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>reviewDateFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setReviewDateFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setReviewDateFieldVisibility( string $review_date_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $review_date_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $review_date_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->reviewDateFieldVisibility = $review_date_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>selectedByDefault</code> of the value (i.e., item) of the named field definition to <code>true</code> and returns the calling object.
For fields of type <code>radio</code> and <code>dropdown</code>,
the method also sets the <code>selectedByDefault</code> of all other values of the same field definition to <code>false</code>.</p></description>
<example>$ms->setSelectedByDefault( $name, "No" )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function setSelectedByDefault( string $name, string $value ) : Asset
    {
        $value = trim( $value );
        
        if( $value == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
    
        if( $this->hasDynamicMetadataFieldDefinition( $name ) )
        {
            $d = $this->getDynamicMetadataFieldDefinition( $name );
            
            if( $d->hasPossibleValue( $value ) )
            {
                $d->setSelectedByDefault( $value );
            }
        }
        else
        {
            throw new e\NoSuchMetadataFieldDefinitionException( 
                S_SPAN . "The definition $name does not exist." . E_SPAN );
        }
            
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>startDateFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setStartDateFieldHelpText( "Start Date" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setStartDateFieldHelpText( string $start_date_field_help_text="" ) : Asset
    {
        $this->getProperty()->startDateFieldHelpText = $start_date_field_help_text;
        return $this;
    }

/**
<documentation><description><p>Sets <code>startDateFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setStartDateFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setStartDateFieldRequired( bool $start_date_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $start_date_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $start_date_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->startDateFieldRequired = $start_date_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>startDateFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setStartDateFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setStartDateFieldVisibility( string $start_date_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $start_date_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $start_date_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->startDateFieldVisibility = $start_date_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>summaryFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setSummaryFieldHelpText( "Summary" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setSummaryFieldHelpText( string $summary_field_help_text="" ) : Asset
    {
        $this->getProperty()->summaryFieldHelpText = $summary_field_help_text;
        return $this;
    }

/**
<documentation><description><p>Sets <code>summaryFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setSummaryFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSummaryFieldRequired( bool $summary_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $summary_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $summary_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->summaryFieldRequired = $summary_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>summaryFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setSummaryFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSummaryFieldVisibility( string $summary_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $summary_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $summary_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->summaryFieldVisibility = $summary_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>teaserFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setTeaserFieldHelpText( "Teaser" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setTeaserFieldHelpText( string $teaser_field_help_text="" ) : Asset
    {
        $this->getProperty()->teaserFieldHelpText = $teaser_field_help_text;
        return $this;
    }

/**
<documentation><description><p>Sets <code>teaserFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setTeaserFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setTeaserFieldRequired( bool $teaser_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $teaser_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $teaser_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->teaserFieldRequired = $teaser_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>teaserFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setTeaserFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setTeaserFieldVisibility( string $teaser_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $teaser_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $teaser_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->teaserFieldVisibility = $teaser_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>titleFieldHelpText</code> and returns the calling object.</p></description>
<example>$ms->setTitleFieldHelpText( "Title" )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setTitleFieldHelpText( string $title_field_help_text="" ) : Asset
    {
        $this->getProperty()->titleFieldHelpText = $title_field_help_text;
        return $this;
    }

/**
<documentation><description><p>Sets <code>titleFieldRequired</code> and returns the calling object.</p></description>
<example>$ms->setTitleFieldRequired( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setTitleFieldRequired( bool $title_field_required=false ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $title_field_required ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $title_field_required must be a boolean." . E_SPAN );
            
        $this->getProperty()->titleFieldRequired = $title_field_required;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>titleFieldVisibility</code> and returns the calling object.</p></description>
<example>$ms->setTitleFieldVisibility( a\MetadataSet::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setTitleFieldVisibility( string $title_field_visibility=self::HIDDEN ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $title_field_visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $title_field_visibility is not acceptable." . E_SPAN );
        
        $this->getProperty()->titleFieldVisibility = $title_field_visibility;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>visibility</code> of the <code>dynamicMetadataFieldDefinition</code> bearing that name and returns the calling object.</p></description>
<example>$ms->setVisibility( $name, c\T::INLINE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException, NoSuchVisibilityException, NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function setVisibility( string $name, string $visibility ) : Asset
    {
        if( !c\VisibilityValues::isVisibility( $visibility ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $visibility is not acceptable." . E_SPAN );

        if( $this->hasDynamicMetadataFieldDefinition( $name ) )
        {
            $d = $this->getDynamicMetadataFieldDefinition( $name );
            
            if( $visibility == self::VISIBLE || $visibility == self::INLINE || $visibility == self::HIDDEN )
            {
                $d->setVisibility( $visibility );
                return $this;
            }
            else
            {
                throw new e\NoSuchVisibilityException( 
                    S_SPAN . "The definition $name does not exist." . E_SPAN );
            }
        }
        else
        {
            throw new e\NoSuchMetadataFieldDefinitionException( 
                S_SPAN . "The definition $name does not exist." . E_SPAN );
        }
    }
    
/**
<documentation><description><p>Swaps the two field definitions, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$ms->swapDynamicMetadataFieldDefinitions( $field1, $field2 )->
swapDynamicMetadataFieldDefinitions( $field1, $field3 );</example>
<return-type>Asset</return-type>
<exception>EmptyValueException, NoSuchFieldException</exception>
</documentation>
*/
    public function swapDynamicMetadataFieldDefinitions( string $def1, string $def2 ) : Asset
    {
        if( $def1 == '' || $def2 == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
            
        if( !in_array( $def1, $this->field_names ) )
            throw new e\NoSuchFieldException( 
                S_SPAN . "The definition $def1 does not exist." . E_SPAN );
        
        if( !in_array( $def1, $this->field_names ) )
            throw new e\NoSuchFieldException( 
                S_SPAN . "The definition $def2 does not exist." . E_SPAN );
            
        $first_def_pos  = -1;
        $second_def_pos = -1;
            
        $count = count( $this->dynamic_metadata_field_definitions );
    
        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->dynamic_metadata_field_definitions[ $i ]->getName() == $def1 )
            {
                $first_def_pos = $i;
            }
            
            if( $this->dynamic_metadata_field_definitions[ $i ]->getName() == $def2 )
            {
                $second_def_pos = $i;
            }
        }
        
        $temp = $this->dynamic_metadata_field_definitions[ $first_def_pos ];
        $this->dynamic_metadata_field_definitions[ $first_def_pos ] = 
            $this->dynamic_metadata_field_definitions[ $second_def_pos ];
        $this->dynamic_metadata_field_definitions[ $second_def_pos ] = $temp;
        
        $this->edit();
        $this->processDynamicMetadataFieldDefinition();
        
        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>swapDynamicMetadataFieldDefinitions</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function swapFields( string $def1, string $def2 ) : Asset
    {
        return $this->swapDynamicMetadataFieldDefinitions( $def1, $def2 );
    }
    
/**
<documentation><description><p>Swaps the two values of the named field definitions,
calls <code>edit</code>, and returns the calling object.</p></description>
<example>$ms->swapValues( $name, "Yes", "No" );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function swapValues( string $name, string $value1, string $value2 ) : Asset
    {
        $def = $this->getDynamicMetadataFieldDefinition( $name );
        $def->swapValues( $value1, $value2 );
        $this->edit();
        $this->processDynamicMetadataFieldDefinition();

        return $this;
    }
    
/**
<documentation><description><p>Sets <code>selectedByDefault</code> of the value of the named field definition to <code>false</code> and returns the calling object.</p></description>
<example>$ms->unsetSelectedByDefault( $name, "Maybe" )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchMetadataFieldDefinitionException</exception>
</documentation>
*/
    public function unsetSelectedByDefault( string $name, string $value ) : Asset
    {
        $value = trim( $value );
        
        if( $value == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
    
        if( $this->hasDynamicMetadataFieldDefinition( $name ) )
        {
            $d = $this->getDynamicMetadataFieldDefinition( $name );
            
            if( $d->hasPossibleValue( $value ) )
            {
                $d->unsetSelectedByDefault( $value );
            }
        }
        else
        {
            throw new e\NoSuchMetadataFieldDefinitionException( 
                S_SPAN . "The definition $name does not exist." . E_SPAN );
        }
            
        return $this;
    }
    
    private function processDynamicMetadataFieldDefinition()
    {
        $this->dynamic_metadata_field_definitions = array();
        $this->field_names                        = array();

        if( $this->getService()->isSoap() )
            $definitions = 
                $this->getProperty()->dynamicMetadataFieldDefinitions->
                dynamicMetadataFieldDefinition;
        elseif( $this->getService()->isRest() )
            $definitions = 
                $this->getProperty()->dynamicMetadataFieldDefinitions;
            
        if( !is_array( $definitions ) )
        {
            $definitions = array( $definitions );
        }
        
        $count = count( $definitions );
        
        for( $i = 0; $i < $count; $i++ )
        {
            $this->dynamic_metadata_field_definitions[] = 
                new p\DynamicMetadataFieldDefinition(
                    $definitions[ $i ], $this->getService() );
            $this->field_names[] = $definitions[ $i ]->name;
        }
    }

    private $dynamic_metadata_field_definitions;
    private $field_names;
}
?>
