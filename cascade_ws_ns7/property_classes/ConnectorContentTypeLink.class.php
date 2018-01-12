<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/26/2017 Updated for REST.
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
<p>A <code>ConnectorContentTypeLink</code> object represents a <code>connectorContentTypeLink</code> property found in a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/connector.php\"><code>Connector</code></a> asset.</p>
<h2>Structure of <code>connectorContentTypeLink</code></h2>
<pre>connectorContentTypeLink
  contentTypeId
  contentTypePath
  pageConfigurationId
  pageConfigurationName
  connectorContentTypeLinkParams (empty except for WordPressConnector)
    connectorContentTypeLinkParam
      name
      value
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "connector-content-type-link-list" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-param-list" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-param" ),
    ) );
return $doc_string;
?>
</description>
<postscript></postscript>
</documentation>
*/
class ConnectorContentTypeLink extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $cctl=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;

        if( isset( $cctl ) )
        {
            if( isset( $cctl->contentTypeId ) )
                $this->content_type_id         = $cctl->contentTypeId;
            if( isset( $cctl->contentTypePath ) )
                $this->content_type_path       = $cctl->contentTypePath;
            if( isset( $cctl->pageConfigurationId ) )
                $this->page_configuration_id   = $cctl->pageConfigurationId;
            if( isset( $cctl->pageConfigurationName ) )
                $this->page_configuration_name = $cctl->pageConfigurationName;

            $this->metadata_set = 
                a\Asset::getAsset( $this->service,
                    a\ContentType::TYPE,
                    $this->content_type_id )->getMetadataSet();
            
            $this->connector_content_type_link_params = array();
            
            if( isset( $cctl->connectorContentTypeLinkParams ) )
            {
                if( $this->service->isSoap() &&
                    isset( $cctl->connectorContentTypeLinkParams->
                        connectorContentTypeLinkParam ) )
                {
                    $params = $cctl->connectorContentTypeLinkParams->
                        connectorContentTypeLinkParam;
                
                    if( !is_array( $params ) )
                    {
                        $params = array( $params );
                    }
                
                    foreach( $params as $param )
                    {
                        $this->connector_content_type_link_params[] = 
                            new ConnectorContentTypeLinkParameter( $param );
                    }
                }
                elseif( $this->service->isRest() )
                {
                    $params = $cctl->connectorContentTypeLinkParams;
                    
                    if( !is_array( $params ) )
                    {
                        $params = array( $params );
                    }
                
                    foreach( $params as $param )
                    {
                        $this->connector_content_type_link_params[] = 
                            new ConnectorContentTypeLinkParameter( $param );
                    }
                }
            }
        }
    }
    
/**
<documentation><description><p>Returns <code>contentTypeId</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getContentTypeId()
    {
        return $this->content_type_id;
    }
    
/**
<documentation><description><p>Returns <code>contentTypePath</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getContentTypePath()
    {
        return $this->content_type_path;
    }
    
/**
<documentation><description><p>Returns <code>pageConfigurationId</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationId()
    {
        return $this->page_configuration_id;
    }
    
/**
<documentation><description><p>Returns <code>pageConfigurationName</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationName()
    {
        return $this->page_configuration_name;
    }
    
/**
<documentation><description><p>A method used by <code>a\WordPressConnector</code>, which returns the object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setMetadataMapping( string $name, string $value ) : Property
    {
        $wired = $this->metadata_set->getNonHiddenWiredFieldNames();
        $dynamic = $this->metadata_set->getDynamicMetadataFieldDefinitionNames();
        
        if( !in_array( $value, $wired ) && !in_array( $value, $dynamic ) && 
            isset( $value ) && $value != "" )
        {
            throw new e\UnacceptableValueException( "The value $value is unacceptable." );
        }
        
        if( $value == "" )
            $value = NULL;
            
        if( count( $this->connector_content_type_link_params ) == 0 )
        {
            $std1 = new \stdClass();
            $std1->name = a\WordPressConnector::TAGS;
            $std1->value = NULL;
            
            $std2 = new \stdClass();
            $std2->name = a\WordPressConnector::CATEGORIES;
            $std2->value = NULL;
            
            $this->connector_content_type_link_params[ 0 ] = 
                new ConnectorContentTypeLinkParameter( $std1 );
            $this->connector_content_type_link_params[ 1 ] = 
                new ConnectorContentTypeLinkParameter( $std2 );
        }
        
        foreach( $this->connector_content_type_link_params as $param )
        {
            if( $param->getName() == $name )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>pageConfigurationId</code> and
<code>pageConfigurationName</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPageConfiguration( PageConfiguration $pc ) : Property
    {
        $this->page_configuration_id   = $pc->getId();
        $this->page_configuration_name = $pc->getName();
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj                                 = new \stdClass();
        $obj->contentTypeId                  = $this->content_type_id;
        $obj->contentTypePath                = $this->content_type_path;
        $obj->pageConfigurationId            = $this->page_configuration_id;
        $obj->pageConfigurationName          = $this->page_configuration_name;
        
        if( $this->service->isSoap() )
            $obj->connectorContentTypeLinkParams = new \stdClass();
        elseif( $this->service->isRest() )
            $obj->connectorContentTypeLinkParams = array();
        
        $count = count( $this->connector_content_type_link_params );
        
        if( $count > 0 )
        {
            if( $count == 1 )
            {
                if( $this->service->isSoap() )
                    $obj->connectorContentTypeLinkParams->
                        connectorContentTypeLinkParam =
                            $this->connector_content_type_link_params[ 0 ]->toStdClass();
                elseif( $this->service->isRest() )
                {
                    $obj->connectorContentTypeLinkParams =
                        array( $this->connector_content_type_link_params[ 0 ]->toStdClass() );
                }
            }
            else
            {
                if( $this->service->isSoap() )
                    $obj->connectorContentTypeLinkParams->connectorContentTypeLinkParam =
                    array();

                for( $i = 0; $i < $count; $i++ )
                {
                    if( $this->service->isSoap() )
                        $obj->connectorContentTypeLinkParams->
                            connectorContentTypeLinkParam[] =
                            $this->connector_content_type_link_params[ $i ]->toStdClass();
                    elseif( $this->service->isRest() )
                        $obj->connectorContentTypeLinkParams[] =
                        $this->connector_content_type_link_params[ $i ]->toStdClass();
                }
            }
        }
        return $obj;
    }

    private $content_type_id;
    private $content_type_path;
    private $metadata_set;
    private $page_configuration_id;
    private $page_configuration_name;
    private $connector_content_type_link_params;
    private $service;
}
?>