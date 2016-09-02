<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 3/14/2016 Minor bug fix.
  * 5/28/2015 Added namespaces.
  * 7/3/2014 Added getPageRegionStdForPageConfiguration.
  * 6/4/2014 Added getPageRegionNames.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class Template extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::TEMPLATE;
    
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->page_regions     = array();
        $this->page_region_map  = array();
        
        if( !is_null( $this->getProperty()->pageRegions ) && !is_null( $this->getProperty()->pageRegions->pageRegion ) )
            self::processPageRegions( $this->getProperty()->pageRegions->pageRegion, 
                $this->page_regions, $this->page_region_map, $this->getService() );
            
        $this->xml = $this->getProperty()->xml;
    }
    
    public function displayXml()
    {
        $xml_string = u\XMLUtility::replaceBrackets( $this->xml );
        
        echo S_H2 . "XML" . E_H2 .
             S_PRE . $xml_string . E_PRE . HR;
        
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
        $region_array = array();
        $region_count = count( $this->page_regions );
        
        // convert PageRegion objects back to stdClass objects
        for( $i = 0; $i < $region_count; $i++ )
        {
            $region_array[ $i ] = $this->page_regions[ $i ]->toStdClass();
        }

        $this->getProperty()->pageRegions->pageRegion = $region_array;
        $asset->{ $p = $this->getPropertyName() }     = $this->getProperty();

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
    
    public function getCreatedBy()
    {
        return $this->getProperty()->createdBy;
    }
    
    public function getCreatedDate()
    {
        return $this->getProperty()->createdDate;
    }
    
    public function getFormat()
    {
        if( isset( $this->getProperty()->formatId ) )
        {
            return Asset::getAsset( $this->getService(),
                XsltFormat::TYPE,
                $this->getProperty()->formatId );
        }
        
        return NULL;
    }
    
    public function getFormatId()
    {
        return $this->getProperty()->formatId;
    }
    
    public function getFormatPath()
    {
        return $this->getProperty()->formatPath;
    }
    
    public function getFormatRecycled()
    {
        return $this->getProperty()->formatRecycled;
    }
    
    public function getLastModifiedBy()
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
    public function getLastModifiedDate()
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
    public function getPageRegion( $name )
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $this->page_region_map ); }
        
        if( !isset( $this->page_region_map[ $name ] ) )
            throw new e\NoSuchPageRegionException( 
                S_SPAN . "The region $name does not exist." . E_SPAN );
            
        return $this->page_region_map[ $name ];
    }
    
    public function getPageRegionBlock( $region_name )
    {
        return $this->getPageRegion( $region_name )->getBlock();
    }
    
    public function getPageRegionFormat( $region_name )
    {
        return $this->getPageRegion( $region_name )->getFormat();
    }
    
    public function getPageRegionNames()
    {
        return array_keys( $this->page_region_map );
    }
    
    public function getPageRegions()
    {
        return $this->page_regions;
    }
    
    public function getPageRegionStdForPageConfiguration()
    {
        $temp = array();
        
        // there is at least 1
        foreach( $this->page_regions as $region )
        {
            // only returns regions with block and/or format
            if( $region->getBlockId() != NULL || $region->getFormatId() != NULL )
            {
                $temp[] = $region;
            }
        }
        
        $std          = new \stdClass();
         $region_count = count( $temp );
       
           if( $region_count == 0 )
           {
               // do nothing
           }
        else if( $region_count == 1 )
        {
            $std->pageRegions->pageRegion = $temp[ 0 ]->toStdClass();
        }
        else
        {
            $std->pageRegions->pageRegion = array();
            
            for( $i = 0; $i < $region_count; $i++ )
            {
                $std->pageRegions->pageRegion[] = $temp[ $i ]->toStdClass();
            }
        }
        
        return $std;
    }
    
    public function getRegionNames()
    {
        return $this->getPageRegionNames();
    }
    
    public function getTargetId()
    {
        return $this->getProperty()->targetId;
    }
    
    public function getTargetPath()
    {
        return $this->getProperty()->targetPath;
    }
    
    public function getXml()
    {
        return $this->xml;
    }
    
    public function hasPageRegion( $name )
    {
        return isset( $this->page_region_map[ $name ] );
    }
    
    public function setFormat( Format $format=NULL )
    {
        if( isset( $format ) )
        {
            // only XSLT format for templates
            if( $format->getType() != c\T::XSLTFORMAT )
            {
                throw new \Exception( 
                    S_SPAN . "Wrong type of format." . E_SPAN );
            }
            $this->getProperty()->formatId   = $format->getId();
            $this->getProperty()->formatPath = $format->getPath();
        }
        else
        {
            $this->getProperty()->formatId   = NULL;
            $this->getProperty()->formatPath = NULL;
        }
        
        return $this;
    }
    
    public function setPageRegion( $name, p\PageRegion $page_region )
    {
        if( !isset( $this->page_region_map[ $name ] ) )
        {
            throw new e\NoSuchPageRegionException( 
                S_SPAN . "The region $name does not exist." . E_SPAN );
        }
        
        $this->page_region_map[ $name ] = $page_region;
        
        $region_count = count( $this->page_regions );
        
        for( $i = 0; $i < $region_count; $i++ )
        {
            // use the new object to replace the old one        
            if( $this->page_regions[ $i ]->getName() == $name )
            {
                $this->page_regions[ $i ] = $page_region;
                break;
            }
        }
        
        return $this;
    }
    
    public function setPageRegionBlock( $name, $block=NULL, $block_recycled=false, $no_block=false )
    {
        $page_region = $this->getPageRegion( $name );
        $page_region->setBlock( $block, $block_recycled, $no_block );
        $this->setPageRegion( $name, $page_region );
        
        return $this;
    }
    
    public function setPageRegionFormat( $name, $format=NULL, $format_recycled=false, $no_format=false )
    {
        $page_region = $this->getPageRegion( $name );
        $page_region->setFormat( $format, $format_recycled, $no_format );
        $this->setPageRegion( $name, $page_region );
        
        return $this;
    }
    
    public function setXml( $xml )
    {
        if( trim( $xml ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );

        $this->getProperty()->xml = $xml;
        return $this;
    }
    
    public static function processPageRegions( 
        $regions, &$page_regions, &$page_region_map, $service )
    {
        if( $regions == NULL )
            return;
            
        if( !is_array( $regions ) )
        {
            $regions = array( $regions );
        }
        
        $page_regions = array();
        $page_region_map = array();
        
        foreach( $regions as $region )
        {
            $pr             = new p\PageRegion( $region, $service );
            $page_regions[] = $pr;
            $page_region_map[ $region->name ] = $pr;
        }
    }

    private $format;
    private $page_regions;       // ordered PageRegion objects
    private $page_region_map;    // associative array: name => PageRegion objects
    private $xml;
}
?>