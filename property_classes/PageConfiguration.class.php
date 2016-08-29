<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/29/2015 Added three more constants.
  * 5/28/2015 Added namespaces.
  * Swapped the last two arguments of the constructor.
  * 12/29/2015 Added more constants.
  * 6/5/2014 Added getTemplate. Rewrote setPageRegionBlock and setPageRegionFormat.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

class PageConfiguration extends Property
{
    const DEBUG = false;
    
    const DATA_TYPE_HTML = 'HTML';
    const DATA_TYPE_XML  = 'XML';
    const DATA_TYPE_PDF  = 'PDF';
    const DATA_TYPE_RTF  = 'RTF';
    const DATA_TYPE_JSON = 'JSON';
    const DATA_TYPE_JS   = 'JS';
    const DATA_TYPE_CSS  = 'CSS';
    
    public function __construct( 
        \stdClass $configuration=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $type=NULL,
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $configuration ) )
        {
            $this->id                      = $configuration->id;
            $this->name                    = $configuration->name;
            $this->default_configuration   = $configuration->defaultConfiguration;
            $this->template_id             = $configuration->templateId;
            $this->template_path           = $configuration->templatePath;
            $this->format_id               = $configuration->formatId;
            $this->format_path             = $configuration->formatPath;
            $this->format_recycled         = $configuration->formatRecycled;
            $this->output_extension        = $configuration->outputExtension;
            $this->serialization_type      = $configuration->serializationType;
            $this->include_xml_declaration = $configuration->includeXMLDeclaration;
            $this->publishable             = $configuration->publishable;
            $this->service                 = $service;
        
            $this->page_regions            = array(); // order page regions
            $this->page_region_map         = array(); // name->page region map

            // test added 8/16/2014
            if( isset( $configuration->pageRegions ) && isset( $configuration->pageRegions->pageRegion ) )
                a\Template::processPageRegions( 
                    $configuration->pageRegions->pageRegion, 
                    $this->page_regions, 
                    $this->page_region_map,
                    $service );
            
            if( isset( $type ) && $type == c\T::PAGE )
            {
                $this->type = c\T::PAGE;
            }
        }
    }
    
    public function addPageRegion( $page_region_name )
    {
        if( !$this->getTemplate()->hasPageRegion( $page_region_name ) )
        {
            throw new e\NoSuchPageRegionException( 
                S_SPAN . "The page region $page_region_name does not exist.". E_SPAN );
        }
        
        // exists
        if( $this->hasPageRegion( $page_region_name ) )
        {
            return $this;
        }
        // does not exist
        $pr_std                  = new \stdClass();
        $pr_std->name            = $page_region_name;
        $pr_std->block_recycled  = false;
        $pr_std->no_block        = false;
        $pr_std->format_recycled = false;
        $pr_std->no_format       = false;
        
        $pr = new PageRegion( $pr_std, $this->service );
        $this->page_regions[]                       = $pr;
        $this->page_region_map[ $page_region_name ] = $pr;
        
        return $this;
    }
    
    public function display()
    {
        echo "ID: " . $this->id . BR .
             "Name: " . $this->name . BR;
        return $this;
    }
    
    public function dump( $formatted=false )
    {
        if( $formatted ) echo S_H2 . c\L::READ_DUMP . E_H2 . S_PRE;
        var_dump( $this->toStdClass() );
        if( $formatted ) echo E_PRE . HR;
        
        return $this;
    }

    public function getDefaultConfiguration()
    {
        return $this->default_configuration;
    }
    
    public function getFormatId()
    {
        return $this->format_id;
    }
    
    public function getFormatPath()
    {
        return $this->format_path;
    }
    
    public function getFormatRecycled()
    {
        return $this->format_recycled;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getIncludeXMLDeclaration()
    {
        return $this->include_xml_declaration;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getOutputExtension()
    {
        return $this->output_extension;
    }
    
    public function getPageRegion( $name )
    {
        $this->checkPageRegion( $name );
        return $this->page_region_map[ $name ];
    }
    
    public function getPageRegionNames()
    {
        return array_keys( $this->page_region_map );
    }
    
    public function getPageRegions()
    {
        return $this->page_regions;
    }
    
    public function getPageRegionBlock( $name )
    {
        $this->checkPageRegion( $name );
        $page_region = $this->page_region_map[ $name ];
        
        return $page_region->getBlock();
    }
    
    public function getPageRegionFormat( $name )
    {
        $this->checkPageRegion( $name );
        $page_region = $this->page_region_map[ $name ];
        
        return $page_region->getFormat();
    }
    
    public function getPublishable()
    {
        return $this->publishable;
    }
    
    public function getSerializationType()
    {
        return $this->serialization_type;
    }
    
    public function getTemplate()
    {
        if( $this->service == NULL )
            throw new e\NullServiceException( 
                S_SPAN . c\M::NULL_SERVICE . E_SPAN );
            
        return a\Asset::getAsset( $this->service, a\Template::TYPE, $this->template_id );
    }
    
    public function getTemplateId()
    {
        return $this->template_id;
    }

    public function getTemplatePath()
    {
        return $this->template_path;
    }

    public function hasPageRegion( $region_name )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Region name fed in: " . $region_name ); }
    
        return isset( $this->page_region_map[ $region_name ] );
    }
    
    public function setDefaultConfiguration( $v )
    {
        if( !c\BooleanValues::isBoolean( $v ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $v is not a boolean." . E_SPAN );
        $this->default_configuration = $v;
        return $this;
    }
    
    public function setFormat( a\Format $format=NULL )
    {
        if( isset( $format ) )
        {
            if( $this->type != c\T::PAGE && $format->getType() != c\T::XSLTFORMAT )
            {
                throw new \Exception( S_SPAN . "Wrong type of format." . E_SPAN );
            }
        
            $this->format_id   = $format->getId();
            $this->format_path = $format->getPath();
        }
        else
        {
            $this->format_id   = NULL;
            $this->format_path = NULL;
        }
        
        return $this;
    }
    
    public function setIncludeXMLDeclaration( $include_xml_declaration )
    {
        if( !c\BooleanValues::isBoolean( $include_xml_declaration ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $include_xml_declaration is not a boolean." . E_SPAN );
                
        $this->include_xml_declaration = $include_xml_declaration;
        return $this;
    }

    public function setOutputExtension( $ext )
    {
        $ext = trim( $ext );
        
        if( $ext == '' )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_FILE_EXTENSION . E_SPAN );
        }
        // garbage in, garbage out
        $this->output_extension = $ext;
        return $this;
    }
    
    public function setPageRegionBlock( $page_region_name, a\Block $block=NULL )
    {
        $regions = $this->getTemplate()->getRegionNames();
        
        if( !in_array( $page_region_name, $regions ) )
        {
            throw new e\NoSuchPageRegionException(
                S_SPAN . "The page region $page_region_name does not exist." . E_SPAN );
        }
        
        if( !isset( $this->page_region_map[ $page_region_name ] ) )
        {
            $this->addPageRegion( $page_region_name );
        }
        
        $this->page_region_map[ $page_region_name ]->setBlock( $block );
    }
    
    public function setPageRegionFormat( $page_region_name, a\Format $format=NULL )
    {
        $regions = $this->getTemplate()->getRegionNames();
        
        if( !in_array( $page_region_name, $regions ) )
        {
            throw new e\NoSuchPageRegionException(
                S_SPAN . "The page region $page_region_name does not exist." . E_SPAN );
        }
        
        if( !isset( $this->page_region_map[ $page_region_name ] ) )
        {
            $this->addPageRegion( $page_region_name );
        }
        
        $this->page_region_map[ $page_region_name ]->setFormat( $format );
    }
    
    public function setPublishable( $publishable )
    {
        if( !c\BooleanValues::isBoolean( $publishable ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $publishable is not a boolean." . E_SPAN );
            
        $this->publishable = $publishable;
        return $this;
    }
    
    public function setRegionBlock( $region_name, a\Block $block=NULL )
    {
        return $this->setPageRegionBlock( $region_name, $block );
    }
    
    public function setRegionFormat( $region_name, a\Format $format=NULL )
    {
        return $this->setPageRegionFormat( $region_name, $format );
    }
    
    public function setRegionNoBlock( $name, $no_block )
    {
        $this->checkPageRegion( $name );
        $region = $this->page_region_map[ $name ];
        $region->setNoBlock( $no_block );        
        
        return $this;
    }
    
    public function setRegionNoFormat( $name, $no_format )
    {
        $this->checkPageRegion( $name );
        $region = $this->page_region_map[ $name ];
        $region->setNoFormat( $no_format );
        
        return $this;
    }
    
    public function setSerializationType( $serialization_type )
    {
        if( $serialization_type != self::DATA_TYPE_HTML &&
            $serialization_type != self::DATA_TYPE_XML &&
            $serialization_type != self::DATA_TYPE_PDF &&
            $serialization_type != self::DATA_TYPE_RTF )
            throw new e\UnacceptableValueException(
                S_SPAN . "The serialization type $serialization_type is unacceptable. " . E_SPAN );
    
        $this->serialization_type = $serialization_type;
        return $this;
    }

    // template cannot be NULL
    public function setTemplate( a\Template $template )
    {
        $this->template_id   = $template->getId();
        $this->template_path = $template->getPath();
        return $this;
    }
    
    public function toStdClass()
    {
        $obj                       = new \stdClass();
        $obj->id                   = $this->id;
        $obj->name                 = $this->name;
        $obj->defaultConfiguration = $this->default_configuration;
        $obj->templateId           = $this->template_id;
        $obj->templatePath         = $this->template_path;
        $obj->formatId             = $this->format_id;
        $obj->formatPath           = $this->format_path;
        $obj->formatRecycled       = $this->format_recycled;
        
        $region_count = count( $this->page_regions );
        
        if( $region_count > 0 )
        {
            if( $region_count == 1 )
            {
                $obj->pageRegions = new \stdClass();
                $obj->pageRegions->pageRegion = $this->page_regions[0]->toStdClass();
            }
            else
            {
                $obj->pageRegions = new \stdClass();
                $obj->pageRegions->pageRegion = array();
        
                foreach( $this->page_regions as $region )
                {
                    $obj->pageRegions->pageRegion[] = $region->toStdClass();
                }
            }
        }
        else
        {
            $obj->pageRegions = new \stdClass();
        }
        
        $obj->outputExtension       = $this->output_extension;
        $obj->serializationType     = $this->serialization_type;
        $obj->includeXMLDeclaration = $this->include_xml_declaration;
        $obj->publishable           = $this->publishable;
        
        return $obj;
    }
    
    private function checkPageRegion( $name )
    {
        if( !isset( $this->page_region_map[ $name ] ) )
        {
            throw new e\NoSuchPageRegionException(
                S_SPAN . "The page region $name does not exist." . E_SPAN );
        }
    }

    private $id;
    private $name;
    private $default_configuration;
    private $template_id;
    private $template_path;
    private $format_id;
    private $format_path;
    private $format_recycled;
    private $output_extension;
    private $serialization_type;
    private $include_xml_declaration;
    private $publishable;
    private $page_regions;
    private $page_region_map;
    private $service;
    private $type;
}
?>