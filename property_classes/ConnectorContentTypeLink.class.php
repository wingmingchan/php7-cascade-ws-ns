<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_asset as a;

class ConnectorContentTypeLink extends Property
{
    public function __construct( 
        \stdClass $cctl=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $cctl ) )
        {
            $this->content_type_id                    = $cctl->contentTypeId;
            $this->content_type_path                  = $cctl->contentTypePath;
            $this->page_configuration_id              = $cctl->pageConfigurationId;
            $this->page_configuration_name            = $cctl->pageConfigurationName;

            if( isset( $service ) )
            {
                $this->metadata_set = 
                    a\Asset::getAsset( $service,
                        a\ContentType::TYPE,
                        $this->content_type_id )->getMetadataSet();
            }
            
            $this->connector_content_type_link_params = array();
            
            if( isset( $cctl->connectorContentTypeLinkParams ) && 
                isset( $cctl->connectorContentTypeLinkParams->connectorContentTypeLinkParam ) )
            {
                $params = $cctl->connectorContentTypeLinkParams->connectorContentTypeLinkParam;
                
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
    
    public function getContentTypeId()
    {
        return $this->content_type_id;
    }
    
    public function getContentTypePath()
    {
        return $this->content_type_path;
    }
    
    public function getPageConfigurationId()
    {
        return $this->page_configuration_id;
    }
    
    public function getPageConfigurationName()
    {
        return $this->page_configuration_name;
    }
    
    public function setMetadataMapping( $name, $value )
    {
        $wired = $this->metadata_set->getNonHiddenWiredFieldNames();
        $dynamic = $this->metadata_set->getDynamicMetadataFieldDefinitionNames();
        
        if( !in_array( $value, $wired ) && !in_array( $value, $dynamic ) && isset( $value ) && $value != "" )
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
    
    public function setPageConfiguration( PageConfiguration $pc )
    {
        $this->page_configuration_id   = $pc->getId();
        $this->page_configuration_name = $pc->getName();
        return $this;
    }
    
    public function toStdClass()
    {
        $obj                                 = new \stdClass();
        $obj->contentTypeId                  = $this->content_type_id;
        $obj->contentTypePath                = $this->content_type_path;
        $obj->pageConfigurationId            = $this->page_configuration_id;
        $obj->pageConfigurationName          = $this->page_configuration_name;
        $obj->connectorContentTypeLinkParams = new \stdClass();
        
        $count = count( $this->connector_content_type_link_params );
        
        if( $count > 0 )
        {
            if( $count == 1 )
            {
                $obj->connectorContentTypeLinkParams->connectorContentTypeLinkParam =
                    $this->connector_content_type_link_params[ 0 ]->toStdClass();
            }
            else
            {
                $obj->connectorContentTypeLinkParams->connectorContentTypeLinkParam = array();

                for( $i = 0; $i < $count; $i++ )
                {
                    $obj->connectorContentTypeLinkParams->connectorContentTypeLinkParam[] =
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
}
?>
