<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>An <code>InlineEditableField</code> object represents an <code>inlineEditableField</code> property that can be found in a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/content-type.php\"><code>a\ContentType</code></a> object.</p>
<h2>Structure of <code>inlineEditableField</code></h2>
<pre>inlineEditableField
  pageConfigurationName
  pageRegionName
  dataDefinitionGroupPath
  type
  name
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "inlineEditableFields" ),
        array( "getComplexTypeXMLByName" => "inlineEditableField" ),
        array( "getSimpleTypeXMLByName"  => "inlineEditableFieldType" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class InlineEditableField extends Property
{
    const INLINE_WIRED_METADATA   = c\T::WIRED_METADATA;
    const INLINE_DYNAMIC_METADATA = c\T::DYNAMIC_METADATA;
    const INLINE_DATA_DEFINITION  = c\T::INLINE_DATA_DEFINITION;
    const INLINE_XHTML            = c\T::XHTML;
    
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        \stdClass $ief=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $ief ) )
        {
            if( isset( $ief->pageConfigurationName ) )
                $this->page_configuration_name    = $ief->pageConfigurationName;
            if( isset( $ief->pageRegionName ) )
                $this->page_region_name           = $ief->pageRegionName;
            if( isset( $ief->dataDefinitionGroupPath ) )
                $this->data_definition_group_path = $ief->dataDefinitionGroupPath;
            if( isset( $ief->type ) )
                $this->type                       = $ief->type;
            if( isset( $ief->name ) )
                $this->name                       = $ief->name;
        }
    }
    
/**
<documentation><description><p>Returns <code>dataDefinitionGroupPath</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getDataDefinitionGroupPath() : string
    {
        return $this->data_definition_group_path;
    }
    
/**
<documentation><description><p>Returns a fully qualified identifier of the field.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getIdentifier() : string
    {
        return $this->page_configuration_name . 
               a\DataDefinition::DELIMITER .
               $this->page_region_name . 
               a\DataDefinition::DELIMITER .
               ( $this->data_definition_group_path == NULL ? 
                   'NULL' : $this->data_definition_group_path ) . 
               a\DataDefinition::DELIMITER .
               $this->type . 
               a\DataDefinition::DELIMITER .
               ( $this->name == NULL ? 'NULL' : $this->name );
    }
    
/**
<documentation><description><p>Returns <code>name</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getName() : string
    {
        return $this->name;
    }
    
/**
<documentation><description><p>Returns <code>pageConfigurationName</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getPageConfigurationName() : string
    {
        return $this->pageConfigurationName;
    }
    
/**
<documentation><description><p>Returns <code>pageRegionName</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getPageRegionName() : string
    {
        return $this->pageRegionName;
    }
    
/**
<documentation><description><p>Returns <code>type</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getType() : string
    {
        return $this->type;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj                          = new \stdClass();
        $obj->pageConfigurationName   = $this->page_configuration_name;
        $obj->pageRegionName          = $this->page_region_name;
        $obj->dataDefinitionGroupPath = $this->data_definition_group_path;
        $obj->type                    = $this->type;
        $obj->name                    = $this->name;        

        return $obj;
    }
    
    private $page_configuration_name;
    private $page_region_name;
    private $data_definition_group_path;
    private $type;
    private $name;
}
?>