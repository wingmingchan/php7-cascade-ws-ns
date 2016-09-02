<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
      Swapped the last two arguments of PageConfiguration.
  * 9/22/2014 Fixed a bug in addPageConfiguration.
  * 9/8/2014 Fixed a bug in deletePageConfiguration.
  * 7/3/2014 Added addConfiguration.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class PageConfigurationSet extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::CONFIGURATIONSET;
    
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->processPageConfigurations();
    }
    
    public function addConfiguration( $name, Template $t, $extension, $type )
    {
        return $this->addPageConfiguration( $name, $t, $extension, $type );
    }
    
    public function addPageConfiguration( $name, Template $t, $extension, $type )
    {
        if( trim( $extension ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_FILE_EXTENSION . E_SPAN );
            
        if( !c\SerializationTypeValues::isSerializationTypeValue( $type ) )
            throw new e\WrongSerializationTypeException( 
                S_SPAN . "The serialization type $type is not acceptable. " . E_SPAN );
        
        $config                    = AssetTemplate::getPageConfiguration();
        $config->name              = $name;
        $config->templateId        = $t->getId();
        $config->templatePath      = $t->getPath();
        $config->pageRegions       = $t->getPageRegionStdForPageConfiguration();
        $config->outputExtension   = $extension;
        $config->serializationType = $type;

        $p = new p\PageConfiguration( $config, $this->getService(), NULL );
        $this->page_configurations[] = $p;
        $this->edit();
            
        $this->processPageConfigurations( 
            $this->getProperty()->pageConfigurations->pageConfiguration );
        return $this;
    }
    
    public function deleteConfiguration( $name )
    {
        return $this->deletePageConfiguration( $name );        
    }
    
    public function deletePageConfiguration( $name )
    {
        if( $this->getDefaultConfiguration() == $name )
        {
            throw new \Exception( 
                S_SPAN . "Cannot delete the default configuration." . E_SPAN );
        }
        
        if( !$this->hasConfiguration( $name ) )
            return $this;
            
        $id = $this->page_configuration_map[ $name ]->getId();
        $service = $this->getService();
        $service->delete( $service->createId( c\T::CONFIGURATION, $id ) );
        
        $this->reloadProperty();
            
        $this->processPageConfigurations( 
            $this->getProperty()->pageConfigurations->pageConfiguration );

        return $this;        
    }
    
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $asset        = new \stdClass();
        $config_array = array();
        $config_count = count( $this->page_configurations );
        
        // convert PageConfiguration objects back to stdClass objects
        for( $i = 0; $i < $config_count; $i++ )
        {
            $config_array[ $i ] = $this->page_configurations[ $i ]->toStdClass();
        }
        
        $this->getProperty()->pageConfigurations->pageConfiguration = $config_array;
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
    
    public function getConfiguration( $name )
    {
        return $this->getPageConfiguration( $name );
    }

    
    public function getDefaultConfiguration()
    {
        foreach( $this->page_configurations as $page_configuration )
        {
            if( $page_configuration->getDefaultConfiguration() )
            {
                return $page_configuration;
            }
        }
    }
    
    public function getIncludeXMLDeclaration( $config )
    {
        return $this->page_configuration_map[ $config ]->getIncludeXMLDeclaration();
    }
    
    public function getOutputExtension( $config )
    {
        return $this->page_configuration_map[ $config ]->getOutputExtension();
    }
    
    public function getPageConfiguration( $name )
    {
        $this->checkPageConfiguration( $name );
        $count = $this->page_configurations;
        
        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->page_configurations[ $i ]->getName() == $name )
                return $this->page_configurations[ $i ];
        }
    }
    
    public function getPageConfigurationNames()
    {
        return $this->page_configuration_names;
    }
    
    public function getPageConfigurations()
    {
        $config_array = array();
        $config_count = count( $this->page_configurations );
        
        for( $i = 0; $i < $config_count; $i++ )
        {
            $config_array[ $i ] = $this->page_configurations[ $i ];
        }
        
        return $config_array;
    }
    
    public function getPageConfigurationTemplate( $name )
    {
        $this->checkPageConfiguration( $name );
        $count = $this->page_configurations;

        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->page_configurations[ $i ]->getName() == $name )
            {
                $id = $this->page_configurations[ $i ]->getTemplateId();
                return Asset::getAsset( $this->getService(), Template::TYPE, $id );
            }
        }
    }
    
    public function getPageRegionNames( $name )
    {
        $this->checkPageConfiguration( $name );
        return $this->page_configuration_map[ $name ]->getPageRegionNames();
    }
    
    public function getPageRegion( $name, $region_name )
    {
        $this->checkPageConfiguration( $name );
        return $this->page_configuration_map[ $name ]->getPageRegion( $region_name );
    }
    
    public function getPublishable( $name )
    {
        return $this->page_configuration_map[ $name ]->getPublishable();
    }
    
    public function getSerializationType( $name )
    {
        return $this->page_configuration_map[ $name ]->getSerializationType();
    }
    
    public function hasConfiguration( $name )
    {
        return $this->hasPageConfiguration( $name );
    }

    public function hasPageConfiguration( $name )
    {
        return in_array( $name, $this->page_configuration_names );
    }
    
    public function hasPageRegion( $name, $region_name )
    {
        $this->checkPageConfiguration( $name );
        return $this->page_configuration_map[ $name ]->hasPageRegion( $region_name );
    }
    
    public function setConfigurationPageRegionBlock( $name, $region_name, $block )
    {
        $this->checkPageConfiguration( $name );
        $config = $this->page_configuration_map[ $name ];
        $config->setPageRegionBlock( $region_name, $block );
        return $this;
    }
    
    public function setConfigurationPageRegionFormat( $name, $region_name, $format )
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setPageRegionFormat( $region_name, $format );
        return $this;
    }
    
    public function setDefaultConfiguration( $name )
    {
        $this->checkPageConfiguration( $name );
        
        foreach( $this->page_configurations as $page_configuration )
        {
            if( $page_configuration->getName() != $name )
            {
                $page_configuration->setDefaultConfiguration( false );
            }
            else
            {
                $page_configuration->setDefaultConfiguration( true );
            }
        }
        return $this;
    }
    
    public function setFormat( $name, Format $format )
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setFormat( $format );
        return $this;
    }
    
    public function setIncludeXMLDeclaration( $name, $i )
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setIncludeXMLDeclaration( $i );
        return $this;
    }
    
    public function setOutputExtension( $name, $ext )
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setOutputExtension( $ext );
        return $this;
    }
    
    public function setPublishable( $name, $p )
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setPublishable( $p );
        return $this;
    }
    
    public function setSerializationType( $name, $type )
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setSerializationType( $type );
        return $this;
    }
    
    private function checkPageConfiguration( $name )
    {
        if( !in_array( $name, $this->page_configuration_names ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $name does not exist." . E_SPAN );
        }
    }
    
    private function processPageConfigurations()
    {
        $this->page_configurations      = array();
        $this->page_configuration_names = array();
        $this->page_configuration_map   = array();

        $array = $this->getProperty()->pageConfigurations->pageConfiguration;
        
        if( isset( $array ) )
        {
            // stdClass object
            if( !is_array( $array ) )
            {
                $array = array( $array );
            }
        }
        
        $service = $this->getService();
        
        foreach( $array as $page_configuration )
        {
            $p = new p\PageConfiguration( $page_configuration, $this->getService(), NULL );
            $this->page_configurations[] = $p;
            $this->page_configuration_names[] = $page_configuration->name;
            $this->page_configuration_map[ $page_configuration->name ] = $p;
        }
    }

    private $page_configurations;
    private $page_configuration_names;
    private $page_configuration_map;
}
?>