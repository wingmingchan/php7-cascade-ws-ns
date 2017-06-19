<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
    Added getEditorConfigurationId and getEditorConfigurationPath.
  * 2/1/2017 Changed signature of addInlineEditableField, allowing NULL.
  * 1/10/2017 Added JSON structure and JSON dump.
  * 6/24/2016 Minor bug fix.
  * 5/28/2015 Added namespaces.
  * 9/22/2014 Added setDataDefinition, setMetadataSet, and setPageConfigurationSet.
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
<p>A <code>ContentType</code> object represents a content type asset.</p>
<h2>Structure of <code>contentType</code></h2>
<pre>SOAP:
contentType
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  pageConfigurationSetId
  pageConfigurationSetPath
  metadataSetId
  metadataSetPath
  dataDefinitionId
  dataDefinitionPath
  editorConfigurationId (8.4)
  editorConfigurationPath (8.4)
  contentTypePageConfigurations
    contentTypePageConfiguration (NULL, stdClass or array of stdClass)
      pageConfigurationId
      pageConfigurationName
      publishMode
      destinations (7.12.2 impl:destination-list)
  inlineEditableFields
    inlineEditableField (NULL, stdClass or array of stdClass)
      pageConfigurationName
      pageRegionName
      dataDefinitionGroupPath
      type
      name
      
JSON:
contentType
  pageConfigurationSetId
  pageConfigurationSetPath
  dataDefinitionId
  dataDefinitionPath
  metadataSetId
  metadataSetPath
  contentTypePageConfigurations (array)
    stdClass
      pageConfigurationId
      pageConfigurationName
      publishMode
  inlineEditableFields (array)
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  name
  id
</pre>
<h2>Design Issues</h2>
<ul>
<li>As of December, 2016, there is a <a href=\"https://hannonhill.jira.com/browse/CSI-626\">bug</a> related to adding/removing a data definition field to the set of inline editable fields. Although the <code>addInlineEditableField</code> and <code>removeInlineEditableField</code> methods are implemented and work for metadata set, do not use them for data definition fields.</li>
<li>Here again I need to work with fully qualified identifiers for inline editable fields. Cascade uses the slashes in the path. This is actually good for me because I use semi-colons. Since different delimiters are used, the group path information from Cascade will be kept intact. Whenever needed, a translation between slashes and semi-colons can be performed.</li>
<li>When dealing with data definitions, the result of concatenating group path and the name of the field, with all the slashes turned into semi-colons, is equivalent to my fully qualified identifier of the field in the data definition.</li>
</ul>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "contentType" ),
        array( "getComplexTypeXMLByName" => "contentTypePageConfigurations" ),
        array( "getComplexTypeXMLByName" => "contentTypePageConfiguration" ),
        array( "getSimpleTypeXMLByName"  => "contentTypePageConfigurationPublishMode" ),
        array( "getComplexTypeXMLByName" => "destination-list" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
        array( "getComplexTypeXMLByName" => "inlineEditableFields" ),
        array( "getComplexTypeXMLByName" => "inlineEditableField" ),
        array( "getSimpleTypeXMLByName"  => "inlineEditableFieldType" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/content_type.php">content_type.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "contentType":{
    "pageConfigurationSetId":"d7b67e638b7f085600a0fcdc2ef6d531",
    "pageConfigurationSetPath":"_common:3 Columns",
    "dataDefinitionId":"1f24084f8b7ffe834c5fe91e2e4d7950",
    "dataDefinitionPath":"article_new",
    "metadataSetId":"1f22ac6a8b7ffe834c5fe91ec00145cb",
    "metadataSetPath":"My Metadata Set",
    "contentTypePageConfigurations":[ { 
      "pageConfigurationId":"d7b67e658b7f085600a0fcdc6767c5fe",
      "pageConfigurationName":"Desktop",
      "publishMode":"all-destinations" } ],
    "inlineEditableFields":[],
    "parentContainerId":"1f2175d28b7ffe834c5fe91e3eb6485d",
    "parentContainerPath":"/",
    "path":"article_new",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"article_new",
    "id":"1f2239118b7ffe834c5fe91e560a90e0"}},
  "success":true
}</pre></postscript>
</documentation>
*/
class ContentType extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::CONTENTTYPE;
    
    const PUBLISH_MODE_ALL_DESTINATIONS = c\T::ALLDESTINATIONS;
    const PUBLISH_MODE_DO_NOT_PUBLISH   = c\T::DONOTPUBLISH;
    
    // metadata set wired-fields
    const AUTHOR           = "author";
    const DISPLAY_NAME     = "displayName";
    const END_DATE         = "endDate";
    const KEYWORDS         = "keywords";
    const META_DESCRIPTION = "metaDescription";
    const REVIEW_DATE      = "reviewDate";
    const START_DATE       = "startDate";
    const SUMMARY          = "summary";
    const TEASER           = "teaser";
    const TITLE            = "title";
    
/**
<documentation><description><p>The constructor, overriding the parent method to process
metadata set, configuration set, and so on.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        if( isset( $this->getProperty()->contentTypePageConfigurations ) )
        {
            $this->processContentTypePageConfigurations(
                $this->getProperty()->contentTypePageConfigurations->
                contentTypePageConfiguration );
        }
        
        if( isset( $this->getProperty()->inlineEditableFields ) &&
            isset( $this->getProperty()->inlineEditableFields->inlineEditableField ))
            $this->processInlineEditableFields(
                $this->getProperty()->inlineEditableFields->inlineEditableField );
        
        if( isset( $this->getProperty()->dataDefinitionId ) )
        {
            $this->data_definition = new DataDefinition(
                $service, $service->createId( 
                    DataDefinition::TYPE, 
                    $this->getProperty()->dataDefinitionId )
            );
        }
        
        $this->metadata_set = new MetadataSet(
            $service, $service->createId( 
                MetadataSet::TYPE, 
                $this->getProperty()->metadataSetId )
        );
        
        $this->configuration_set = new PageConfigurationSet(
            $service, $service->createId( 
                PageConfigurationSet::TYPE, 
                $this->getProperty()->pageConfigurationSetId )
        );
        
        $this->wired_field_types = array(
            self::AUTHOR, self::DISPLAY_NAME, self::END_DATE,
            self::KEYWORDS, self::META_DESCRIPTION, self::REVIEW_DATE, self::START_DATE,
            self::SUMMARY, self::TEASER, self::TITLE );
    }
    
/**
<documentation><description><p>Adds an inline editable field, and returns the calling
object. Due to a bug in Cascade, do not use this method to add data definition fields.</p></description>
<example>$ct->addInlineEditableField( 
    $config_name, $region_name, $group_path, 
    $type, $name )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException, NoSuchPageRegionException, NoSuchFieldException, Exception</exception>
</documentation>
*/
    public function addInlineEditableField( 
        string $config, string $region, string $group_path=NULL,
        string $type=NULL, string $name=NULL ) : Asset
    {
        $identifier = $config . DataDefinition::DELIMITER .
            $region . DataDefinition::DELIMITER .
            ( isset( $group_path ) && $group_path != 'NULL' ? 
                str_replace( '/', DataDefinition::DELIMITER, $group_path ) :
                'NULL'
            ) . DataDefinition::DELIMITER .
            $type . DataDefinition::DELIMITER . $name;
            
        if( $this->hasInlineEditableField( $identifier ) )
        {
            echo "The field already exists." . BR;
            return $this;
        }
    
        if( !$this->hasPageConfiguration( $config ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config does not exist." . E_SPAN );
        }
        
        if( !$this->hasRegion( $config, $region ) )
        {
            throw new e\NoSuchPageRegionException( 
                S_SPAN . "The page region $region does not exist." . E_SPAN );
        }
        
        if( $type == c\T::WIRED_METADATA && !in_array( $name, $this->wired_field_types ) )
        {
            throw new \Exception( 
                S_SPAN . "The name $name is not acceptable." . E_SPAN );
        }
        else if( $type == c\T::DYNAMIC_METADATA && !in_array( 
            $name, $this->metadata_set->getDynamicMetadataFieldDefinitionNames() ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field $name does not exist." . E_SPAN );
        }
        
        if( isset( $group_path ) && $group_path != 'NULL' )
        {
            $group_path = str_replace( '/', DataDefinition::DELIMITER, $group_path );
            $field_name = $group_path . DataDefinition::DELIMITER . $name;
            $field_name = trim( $field_name, DataDefinition::DELIMITER );
            
            if( !$this->data_definition->hasField( $field_name ) )
            {
                throw new e\NoSuchFieldException( 
                    S_SPAN . "The field $name does not exist." . E_SPAN );
            }
        }
        
        $field_std                          = new \stdClass();
        $field_std->pageConfigurationName   = $config;
        $field_std->pageRegionName          = $region;
        $field_std->dataDefinitionGroupPath = ( $group_path == NULL ||
            $group_path == 'NULL' ? NULL : $group_path );
        $field_std->type                    = $type;
        $field_std->name                    = ( $name == NULL ||
            $name == 'NULL' ? NULL : $name );
        $field = new p\InlineEditableField( $field_std );
        
        $this->inline_editable_fields[] = $field;
        $this->inline_editable_field_map[ $field->getIdentifier() ] = $field;
        $this->inline_editable_field_names = array_keys(
            $this->inline_editable_field_map );
        
        if( self::DEBUG ) { u\DebugUtility::dump( $this->inline_editable_fields ); }
        
        return $this;
    }
    
/**
<documentation><description><p>Displays some information and returns the calling object,
overriding the parent method to display the configuration set as well.</p></description>
<example>$ct->display();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function display() : Asset
    {
        parent::display();
             
        foreach( $this->content_type_page_configurations as $config )
        {
            $config->display();
        }
             
        return $this;
    }
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example>$ct->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
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
        $this->getProperty()->contentTypePageConfigurations->
            contentTypePageConfiguration = array();
        
        foreach( $this->content_type_page_configurations as $config )
        {
            $this->getProperty()->contentTypePageConfigurations->
                contentTypePageConfiguration[] = $config->toStdClass();
        }

        $editable_count = count( $this->inline_editable_fields );
        
        $this->getProperty()->inlineEditableFields = new \stdClass();
        
        if( $editable_count == 1 )
        {
            $this->getProperty()->inlineEditableFields->inlineEditableField =
                $this->inline_editable_fields[0]->toStdClass();
        }
        else if( $editable_count > 1 )
        {
            $this->getProperty()->inlineEditableFields->inlineEditableField = array();
            
            foreach( $this->inline_editable_fields as $field )
            {
                $this->getProperty()->inlineEditableFields->inlineEditableField[] =
                    $field->toStdClass();
            }
        }
        
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
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
<documentation><description><p>An alias of <code>getPageConfigurationSet</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getConfigurationSet() : Asset
    {
        return $this->getPageConfigurationSet();
    }
    
/**
<documentation><description><p>Returns an array of page configuration names.</p></description>
<example>u\DebugUtility::dump( $ct->getContentTypePageConfigurationNames() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getContentTypePageConfigurationNames() : array
    {
        return $this->content_type_page_configuration_names;
    }
    
/**
<documentation><description><p>Returns the <code>DataDefinition</code> object or <code>NULL</code>.</p></description>
<example>$dd = $ct->getDataDefinition();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDataDefinition()
    {
        return $this->data_definition;
    }
    
/**
<documentation><description><p>Returns <code>dataDefinitionId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $ct->getDataDefinitionId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDataDefinitionId()
    {
        return $this->getProperty()->dataDefinitionId;
    }
    
/**
<documentation><description><p>Returns <code>dataDefinitionPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $ct->getDataDefinitionPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDataDefinitionPath()
    {
        return $this->getProperty()->dataDefinitionPath;
    }

/**
<documentation><description><p>Returns <code>editorConfigurationId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $ct->getEditorConfigurationId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
	public function getEditorConfigurationId()
	{
		return $this->getProperty()->editorConfigurationId;
	}
	
/**
<documentation><description><p>Returns <code>editorConfigurationPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $ct->getEditorConfigurationPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
	public function getEditorConfigurationPath()
	{
		return $this->getProperty()->editorConfigurationPath;
	}
/**
<documentation><description><p>Returns an array of inline editable field names. An inline editable field name consists of five parts: <code>pageConfigurationName;pageRegionName;dataDefinitionGroupPath;type;name</code>. For example, <code>RWD;DEFAULT;NULL;data-definition;post-title-chooser</code>.</p></description>
<example>u\DebugUtility::dump( $ct->getInlineEditableFieldNames() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getInlineEditableFieldNames()
    {
        return $this->inline_editable_field_names;
    }
    
/**
<documentation><description><p>Returns <code>inlineEditableFields</code>.</p></description>
<example></example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getInlineEditableFields() : \stdClass
    {
        return $this->getProperty()->inlineEditableFields;
    }
    
/**
<documentation><description><p>Returns the <code>MetadataSet</code> object.</p></description>
<example>$ms = $ct->getMetadataSet();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSet() : Asset
    {
        return $this->metadata_set;
    }
    
/**
<documentation><description><p>Returns <code>metadataSetId</code>.</p></description>
<example>echo $ct->getMetadataSetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetId() : string
    {
        return $this->getProperty()->metadataSetId;
    }
    
/**
<documentation><description><p>Returns <code>metadataSetPath</code>.</p></description>
<example>echo $ct->getMetadataSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetPath()
    {
        return $this->getProperty()->metadataSetPath;
    }
    
/**
<documentation><description><p>Returns the <code>PageConfigurationSet</code> object.</p></description>
<example>$cs = $ct->getPageConfigurationSet();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationSet() : Asset
    {
        return $this->configuration_set;
    }

/**
<documentation><description><p>Returns <code>pageConfigurationSetId</code>.</p></description>
<example>echo $ct->getPageConfigurationSetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationSetId() : string
    {
        return $this->getProperty()->pageConfigurationSetId;
    }
    
/**
<documentation><description><p>Returns <code>pageConfigurationSetPath</code>.</p></description>
<example>echo $ct->getPageConfigurationSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationSetPath() : string
    {
        return $this->getProperty()->pageConfigurationSetPath;
    }
    
/**
<documentation><description><p>Returns the published mode of the named configuration.</p></description>
<example>echo $ct->getPublishMode( "RWD" ), BR;</example>
<return-type>string</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function getPublishMode( string $config_name ) : string
    {
        if( !in_array( $config_name, $this->content_type_page_configuration_names ) )
        {
            throw new \Exception( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        foreach( $this->content_type_page_configurations as $config )
        {
            if( $config->getPageConfigurationName() == $config_name )
            {
                return $config->getPublishMode();
            }
        }
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the group path, when all slashes replaced by semi-colons, exists in the corresponding data definition as a fully qualified field identifier.</p></description>
<example></example>
<return-type>bool</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function hasDataDefinitionGroupPath( string $name ) : bool
    {
    	if( !isset( $this->data_definition ) )
    		throw new \Exception( 
    			"The content type is not associated with a data definition" );
    			
        $name = str_replace( '/', DataDefinition::DELIMITER, $name );
        return in_array( $name, $this->data_definition->getIdentifiers() );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the field bearing the name exists.</p></description>
<example>echo u\StringUtility::boolToString(
    $ct->hasInlineEditableField(
        "RWD;DEFAULT;NULL;wired-metadata;title" ) ), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasInlineEditableField( string $name ) : bool
    {
        if( isset( $this->inline_editable_field_names ) && 
            is_array( $this->inline_editable_field_names ) )
            return in_array( $name, $this->inline_editable_field_names );
        return false;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the page configuration bearing that name exists.</p></description>
<example>echo u\StringUtility::boolToString(
    $ct->hasPageConfiguration( "RWD" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPageConfiguration( string $name ) : bool
    {
        return in_array( $name, $this->content_type_page_configuration_names );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named region exists in the named page configuration. Note that this method calls <code>Template::getPageRegionNames</code> instead of <code>PageConfiguration::getPageRegionNames</code>.</p></description>
<example>echo u\StringUtility::boolToString(
    $ct->hasRegion( "RWD", "BANNER 12 COLUMNS" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasRegion( string $config_name, string $region_name ) : bool
    {
        return in_array( $region_name, 
            $this->configuration_set->getPageConfiguration( $config_name )->
                getTemplate()->getPageRegionNames() );
    }
    
/**
<documentation><description><p>Removes the field bearing the fully qualified identifier, 
and returns the calling object. Due to a bug in Cascade, do not use this method to remove
data definition fields.</p></description>
<example>$ct->removeInlineEditableField( $field_name )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchFieldException</exception>
</documentation>
*/
    public function removeInlineEditableField( string $identifier ) : Asset
    {
        if( !$this->hasInlineEditableField( $identifier ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field $identifier does not exist." . E_SPAN );
        }
        
        $count = count( $this->inline_editable_fields );
        
        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->inline_editable_fields[ $i ]->getIdentifier() == $identifier )
            {
                $field_before = array_splice( $this->inline_editable_fields, 0, $i );
                
                $field_after = array();
                
                if( $count > $i + 1 )
                {
                    $field_after  = array_splice( $this->inline_editable_fields, $i + 1 );
                }
                
                $this->inline_editable_fields = array_merge( $field_before, $field_after );
                break;
            }
        }
        
        unset( $this->inline_editable_field_map[ $identifier ] );
        $this->inline_editable_field_names = array_keys( $this->inline_editable_field_map );
        
        return $this;
    }

/**
<documentation><description><p>Sets <code>dataDefinitionId</code> and <code>dataDefinitionPath</code>, and returns the calling object.</p></description>
<example>$ct->setDataDefinition( $dd )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDataDefinition( DataDefinition $dd=NULL ) : Asset
    {
        if( isset( $dd ) )
        {
            $this->getProperty()->dataDefinitionId   = $dd->getId();
            $this->getProperty()->dataDefinitionPath = $dd->getPath();
        }
        else
        {
            $this->getProperty()->dataDefinitionId   = NULL;
            $this->getProperty()->dataDefinitionPath = NULL;
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>metadataSetId</code> and <code>metadataSetPath</code>, and returns the calling object.</p></description>
<example>$ct->setMetadataSet( $ms )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setMetadataSet( MetadataSet $ms ) : Asset
    {
        $this->getProperty()->metadataSetId   = $ms->getId();
        $this->getProperty()->metadataSetPath = $ms->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>pageConfigurationSetId</code> and <code>pageConfigurationSetPath</code>, and returns the calling object.</p></description>
<example>$ct->setPageConfigurationSet( $pcs )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setPageConfigurationSet( PageConfigurationSet $pcs ) : Asset
    {
        $this->getProperty()->pageConfigurationSetId   = $pcs->getId();
        $this->getProperty()->pageConfigurationSetPath = $pcs->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets the publish mode for the named page configuration and returns the object. Currently only two modes are supported: <code>all-destinations</code> and <code>do-not-publish</code>.</p></description>
<example>$ct->setPublishMode( 
    $config_name, 
    a\ContentType::PUBLISH_MODE_ALL_DESTINATIONS )->
    edit();</example>
<return-type>Asset</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function setPublishMode( string $config_name, string $mode ) : Asset
    {
        if( !in_array( $config_name, $this->content_type_page_configuration_names ) )
        {
            throw new \Exception( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        if( $mode != self::PUBLISH_MODE_ALL_DESTINATIONS && 
            $mode != self::PUBLISH_MODE_DO_NOT_PUBLISH )
        {
            throw new \Exception( 
                S_SPAN . "The mode $mode is not supported." . E_SPAN );
        }
        
        foreach( $this->content_type_page_configurations as $config )
        {
            if( $config->getPageConfigurationName() == $config_name )
            {
                $config->setPublishMode( $mode );
            }
        }
        
        return $this;
    }
    
    private function processContentTypePageConfigurations( $configs )
    {
        $this->content_type_page_configurations = array();
        
        // store the names of page configs
        $this->content_type_page_configuration_names = array();

        if( !is_array( $configs ) )
        {
            $configs = array( $configs );
        }
        
        foreach( $configs as $config )
        {
            $this->content_type_page_configurations[] = 
                new p\ContentTypePageConfiguration( $config );
                
            $this->content_type_page_configuration_names[] = 
                $config->pageConfigurationName;    
        }
    }
    
    private function processInlineEditableFields( $fields )
    {
        $this->inline_editable_fields      = array();
        $this->inline_editable_field_map   = array();
        $this->inline_editable_field_names = array();

        if( isset( $fields ) )
        {
            if( !is_array( $fields ) )
            {
                $fields = array( $fields );
            }
            
            foreach( $fields as $field )
            {
                $ief = new p\InlineEditableField( $field );
                $this->inline_editable_fields[] = $ief;
                //echo $ief->getIdentifier() . BR;
                $this->inline_editable_field_map[ $ief->getIdentifier() ] = $ief;
            }
            
            $this->inline_editable_field_names = 
                array_keys( $this->inline_editable_field_map );
        }
    }
    
    private $content_type_page_configurations;
    private $content_type_page_configuration_names;
    private $inline_editable_fields;
    private $inline_editable_field_map;
    private $inline_editable_field_names;
    
    private $data_definition;
    private $metadata_set;
    private $configuration_set;
    private $wired_field_types;
}