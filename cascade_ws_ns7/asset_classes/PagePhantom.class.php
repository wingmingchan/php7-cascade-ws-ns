<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/8/2016 Added code to deal with host asset.
  * 1/4/2016 Fixed a bug in publish.
  * 12/11/2015 Added getPageRegions and getPageRegion.
  * 10/30/2015 Added unpublish.
  * 9/10/2015 Added the display of string id to checkStructuredData. 
  * 6/23/2015 Fixed a bug in edit.
  * 5/28/2015 Added namespaces.
  * 5/1/2015 Changed signature of edit and added editWithoutException.
  *   Reason: when changing the content type associated with a page,
  *           if a different data definition is used, phantom nodes will
  *           cause a lot of exceptions. The restriction must be loosened
  *           so that a page can be modified.
  * 4/9/2015 Added a flag to setContentType to avoid exception.
  * 2/24/2015 Added getPossibleValues.
  * 2/23/2015 Added the missing isMultiLineNode.
  * 10/2/2014 Fixed a bug in edit.
  * 9/18/2014 Added getMetadataSet, getMetadataSetId, getMetadataSetPath.
  * 8/29/2014 Fixed bugs in appendSibling and removeLastSibling.
  * 8/27/2014 Added getParentFolder, getParentFolderId, getParentFolderPath.
  * 8/20/2014 Added hasConfiguration.
  * 7/23/2014 Split getPageLevelRegionBlockFormat into getPageLevelRegionBlockFormat and getBlockFormatMap and
  * added no-block and no-format.
  * 7/22/2014 Added getMetadataStdClass, isPublishable, setMetadata.
  * 6/5/2014 Fixed a bug in getPageLevelRegionBlockFormat.
  * 5/13/2014 Added createNInstancesForMultipleField 
  *   and replaced all string literals with constants
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class PagePhantom extends Linkable
{
    const DEBUG = false;
    const DUMP  = false;
    const TYPE  = c\T::PAGE;

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->content_type = new ContentType( 
            $service, $service->createId( ContentType::TYPE, 
            $this->getProperty()->contentTypeId ) );
            
        parent::setPageContentType( $this->content_type );
            
        if( isset( $this->getProperty()->structuredData ) )
        {
            $this->data_definition_id = $this->content_type->getDataDefinitionId();

            // structuredDataNode could be empty for xml pages
            if( isset( $this->getProperty()->structuredData ) &&
                isset( $this->getProperty()->structuredData->structuredDataNodes ) &&
                isset( $this->getProperty()->structuredData->structuredDataNodes->structuredDataNode )
            )
            {
                $this->processStructuredDataPhantom( $this->data_definition_id );
            }
        }
        elseif( isset( $this->getProperty()->xhtml ) )
        {
            $this->xhtml = $this->getProperty()->xhtml;
        }
        
        $this->processPageConfigurations( $this->getProperty()->pageConfigurations->pageConfiguration );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function appendSibling( $identifier )
    {
        $this->checkStructuredData();
        $this->structured_data->appendSibling( $identifier );
        $this->edit();
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function createNInstancesForMultipleField( $number, $identifier )
    {
        $this->checkStructuredData();      
        $number = intval( $number );
        
        if( !$number > 0 )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $number is not a number." . E_SPAN );
        }
        
        if( !$this->hasNode( $identifier ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist." . E_SPAN );
        }
        
        $num_of_instances  = $this->getNumberOfSiblings( $identifier );
    
        if( $num_of_instances < $number ) // more needed
        {
            while( $this->getNumberOfSiblings( $identifier ) != $number )
            {
                $this->appendSibling( $identifier );
            }
        }
        else if( $num_of_instances > $number )
        {
            while( $this->getNumberOfSiblings( $identifier ) != $number )
            {
                $this->removeLastSibling( $identifier );
            }
        }

        $this->reloadProperty();
        $this->processStructuredDataPhantom( $this->data_definition_id );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function displayDataDefinition()
    {
        $this->checkStructuredData();
        $this->structured_data->getDataDefinition()->displayXML();
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function displayXhtml()
    {
        if( !$this->hasStructuredData() )
        {
            $xhtml_string = u\XMLUtility::replaceBrackets( $this->xhtml );
            echo S_H2 . 'XHTML' . E_H2;
            echo $xhtml_string . HR;
        }
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function edit( 
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        $new_workflow_name="", 
        $comment="",
        $exception=true )
    {
        $asset = new \stdClass();
        $page  = $this->getProperty();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page->pageConfigurations ); }

        $page->metadata = $this->getMetadata()->toStdClass();
        
        if( isset( $this->structured_data ) )
        {
            $page->structuredData = $this->structured_data->toStdClass();
            $page->xhtml = NULL;
        }
        else
        {
            $page->structuredData = NULL;
            
            if( isset( $this->xhtml ) )
                $page->xhtml = $this->xhtml;
        }
        
        $page->pageConfigurations->pageConfiguration = array();
        
        foreach( $this->page_configurations as $config )
        {
            $page->pageConfigurations->pageConfiguration[] = $config->toStdClass();
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page->pageConfigurations ); }
        
        if( isset( $wd ) )
        {
            $wf_config                       = new \stdClass();
            $wf_config->workflowDefinitionId = $wd->getId();
            $wf_config->workflowComments     = $comment;
            
            if( isset( $wf ) )
            {
                $wf_config->workflowName     = $wf->getName();
            }
            else
            {
                if( trim( $new_workflow_name ) == "" )
                    throw new e\EmptyValueException( c\M::EMPTY_WORKFLOW_NAME );
                    
                $wf_config->workflowName     = $new_workflow_name;
            }
            
            $asset->workflowConfiguration    = $wf_config;
        }
        
        $asset->{ $p = $this->getPropertyName() } = $page;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page ); }
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        
        if( $exception )
            $this->reloadProperty();
        
        if( isset( $this->data_definition_id ) && $exception )
            $this->processStructuredDataPhantom( $this->data_definition_id );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getAssetNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getAssetNodeType( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBlockFormatMap( p\PageConfiguration $configuration )
    {
        $block_format_array  = array();
        $configuration_name  = $configuration->getName();
        $config_page_regions = $configuration->getPageRegions();
        $config_region_names = $configuration->getPageRegionNames();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $config_region_names ); }
        
        $page_level_config  = $this->page_configuration_map[ $configuration_name ];
        $page_level_regions = $page_level_config->getPageRegions();
        $page_region_names  = $page_level_config->getPageRegionNames();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page_region_names ); }
        
        $template = $this->getContentType()->getConfigurationSet()->
            getPageConfigurationTemplate( $configuration_name );
        $template_region_names = $template->getPageRegionNames();
        
        foreach( $page_region_names as $page_region_name )
        {
            // initialize id variables
            $block_id = NULL;
            $format_id = NULL;

            // for debugging
            if( self::DEBUG )
            {
                u\DebugUtility::out( $page_region_name );

                if( $template->hasPageRegion( $page_region_name ) )
                {
                    u\DebugUtility::out( "template block: " . 
                        $template->getPageRegion( $page_region_name )->getBlockId() );
                    u\DebugUtility::out( "template format: " . 
                        $template->getPageRegion( $page_region_name )->getFormatId() );
                }
            
                if( $configuration->hasPageRegion( $page_region_name ) )
                {
                    u\DebugUtility::out( "Config block: " . 
                        $configuration->getPageRegion( $page_region_name )->getBlockId() );
                    u\DebugUtility::out( "Config format: " . 
                        $configuration->getPageRegion( $page_region_name )->getFormatId() );
                }
                
                if( $page_level_config->hasPageRegion( $page_region_name ) )
                {
                    u\DebugUtility::out( "Page block: " . 
                        $page_level_config->getPageRegion( $page_region_name )->getBlockId() );
                    u\DebugUtility::out( "Page format: " . 
                        $page_level_config->getPageRegion( $page_region_name )->getFormatId() );
                } 
            }
            
            // template level
            if( $template->hasPageRegion( $page_region_name ) )
            {
                $template_block_id  = $template->
                    getPageRegion( $page_region_name )->getBlockId();
                $template_format_id = $template->
                    getPageRegion( $page_region_name )->getFormatId();
            }
            // config level
            if( $configuration->hasPageRegion( $page_region_name ) )
            {
                $config_block_id  = $configuration->
                    getPageRegion( $page_region_name )->getBlockId();
                $config_format_id = $configuration->
                    getPageRegion( $page_region_name )->getFormatId();
            }
            // page level
            else
            {
                $config_block_id  = NULL;
                $config_format_id = NULL;
            }
            
            if( $page_level_config->hasPageRegion( $page_region_name ) )
            {
                $page_block_id  = $page_level_config->
                    getPageRegion( $page_region_name )->getBlockId();
                $page_format_id = $page_level_config->
                    getPageRegion( $page_region_name )->getFormatId();
                $page_no_block  = $page_level_config->
                    getPageRegion( $page_region_name )->getNoBlock();
                $page_no_format = $page_level_config->
                    getPageRegion( $page_region_name )->getNoFormat();
            } 

            if( isset( $page_block_id ) )
            {
                $block_id = NULL;
                
                if( !isset( $config_block_id ) )
                {
                    if( $page_block_id != $template_block_id )
                    {
                        $block_id = $page_block_id;
                    }
                }
                else if( $config_block_id != $page_block_id )
                {
                    $block_id = $page_block_id;
                }
            }

            if( isset( $page_format_id ) )
            {
                $format_id = NULL;
                
                if( !isset( $config_format_id ) )
                {
                    if( $page_format_id != $template_format_id )
                    {
                        $format_id = $page_format_id;
                    }
                }
                else if( $config_format_id != $page_format_id )
                {
                    $format_id = $page_format_id;
                }
            }
            // store page-level block/format info
            if( isset( $block_id ) )
            {
                if( !isset( $block_format_array[ $page_region_name ] ) )
                {
                    $block_format_array[ $page_region_name ] = array();
                }
                
                $block_format_array[ $page_region_name ][ 'block' ] = $block_id;
            }
            
            if( isset( $format_id ) )
            {
                if( !isset( $block_format_array[ $page_region_name ] ) )
                {
                    $block_format_array[ $page_region_name ] = array();
                }
                
                $block_format_array[ $page_region_name ][ 'format' ] = $format_id;
            }
            
            if( $page_no_block )
            {
                $block_format_array[ $page_region_name ][ 'no-block' ] = true;
            }

            if( $page_no_format )
            {
                $block_format_array[ $page_region_name ]['no-format' ] = true;
            }
        }
        return $block_format_array;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBlockId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBlockPath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockPath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getConfigurationSet()
    {
        return $this->getPageConfigurationSet();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getConfigurationSetId()
    {
        return $this->getProperty()->configurationSetId; // NULL for page
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getConfigurationSetPath()
    {
        return $this->getProperty()->configurationSetPath; // NULL for page
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getContentType()
    {
        $service = $this->getService();
        
        return Asset::getAsset( $service,
            ContentType::TYPE,
            $this->getProperty()->contentTypeId );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getContentTypeId()
    {
        return $this->getProperty()->contentTypeId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getContentTypePath()
    {
        return $this->getProperty()->contentTypePath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDataDefinition()
    {
        $this->checkStructuredData();
        return $this->structured_data->getDataDefinition();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getFileId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFileId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getFilePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFilePath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifiers()
    {
        $this->checkStructuredData();
        return $this->structured_data->getIdentifiers();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedDate()
    {
        return $this->getProperty()->lastPublishedDate;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedBy()
    {
        return $this->getProperty()->lastPublishedBy;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLinkableId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkableId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLinkablePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkablePath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMaintainAbsoluteLinks()
    {
        return $this->getProperty()->maintainAbsoluteLinks;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSet()
    {
        return $this->getContentType()->getMetadataSet();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetId()
    {
        return $this->getContentType()->getMetadataSet()->getId();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataSetPath()
    {
        return $this->getContentType()->getMetadataSet()->getPath();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMetadataStdClass()
    {
        return $this->getMetadata()->toStdClass();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getNodeType( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getNumberOfSiblings( $identifier )
    {
        $this->checkStructuredData();
        
        if( trim( $identifier ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_IDENTIFIER . E_SPAN );
        }
        
        if( !$this->hasIdentifier( $identifier ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist." . E_SPAN );
        }
        return $this->structured_data->getNumberOfSiblings( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationSet()
    {
        // the page does not store page configuration set info
        return $this->content_type->getPageConfigurationSet();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPageId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageLevelRegionBlockFormat()
    {
        $configuration = $this->getContentType()->getConfigurationSet()->getDefaultConfiguration();
        return $this->getBlockFormatMap( $configuration );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPagePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPagePath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegion( $config_name, $region_name )
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
        
        return $this->page_configuration_map[ $config_name ]->getPageRegion( $region_name );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegions( $config_name )
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
        
        return $this->page_configuration_map[ $config_name ]->getPageRegions();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegionNames( $config_name )
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
        
        return $this->page_configuration_map[ $config_name ]->getPageRegionNames();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolder()
    {
        return $this->getAsset( $this->getService(), Folder::TYPE, $this->getParentFolderId() );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolderId()
    {
        return $this->getProperty()->parentFolderId;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getParentFolderPath()
    {
        return $this->getProperty()->parentFolderPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPossibleValues( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPossibleValues( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBeIndexed()
    {
        return $this->getProperty()->shouldBeIndexed;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBePublished()
    {
        return $this->getProperty()->shouldBePublished;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredDataPhantom()
    {
        $this->checkStructuredData();
        return $this->structured_data;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkPath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkPath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getText( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getText( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getTextNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getTextNodeType( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflow()
    {
        $service = $this->getService();
        $service->readWorkflowInformation( $service->createId( self::TYPE, $this->getProperty()->id ) );
        
        if( $service->isSuccessful() )
        {
            if( isset( $service->getReply()->readWorkflowInformationReturn->workflow ) )
                return new p\Workflow( $service->getReply()->readWorkflowInformationReturn->workflow, $service );
            else
                return NULL; // no workflow
        }
        else
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::READ_WORKFLOW_FAILURE . E_SPAN );
        }
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getXhtml()
    {
        return $this->getProperty()->xhtml;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasConfiguration( $config_name )
    {
        return isset( $this->page_configuration_map[ $config_name ] );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasIdentifier( $identifier )
    {
        $this->checkStructuredData();
        return $this->hasNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->hasNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasPageConfiguration( $config_name )
    {
        return $this->hasConfiguration( $config_name );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasPageRegion( $config_name, $region_name )
    {
        return $this->hasConfiguration( $config_name ) &&
            $this->page_configuration_map[ $config_name ]->
            hasPageRegion( $region_name );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasStructuredData()
    {
        return $this->structured_data != NULL;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isAssetNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isAssetNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isGroupNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isGroupNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isMultiLineNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isMultiple( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiple( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isPublishable()
    {
        $parent = $this->getAsset( $this->getService(), Folder::TYPE, $this->getParentContainerId() );
        return $parent->isPublishable() && $this->getShouldBePublished();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isRequired( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isRequired( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isTextNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isWYSIWYG( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isWYSIWYG( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL )
    {
        if( isset( $destination ) )
        {
            $destination_std       = new \stdClass();
            $destination_std->id   = $destination->getId();
            $destination_std->type = $destination->getType();
        }
        
        if( $this->getProperty()->shouldBePublished )
        {
            $service = $this->getService();
            
            if( isset( $destination ) )
                $service->publish( 
                    $service->createId( $this->getType(), $this->getId() ), $destination_std );
            else
                $service->publish( 
                    $service->createId( $this->getType(), $this->getId() ) );
        }
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function removeLastSibling( $identifier )
    {
        $this->checkStructuredData();
        $this->structured_data->removeLastSibling( $identifier );
        $this->edit();
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function replaceByPattern( $pattern, $replace, $include=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->replaceByPattern( $pattern, $replace, $include );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function replaceXhtmlByPattern( $pattern, $replace )
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_XHTML_PAGE . E_SPAN );
        }
        
        $this->xhtml = preg_replace( $pattern, $replace, $this->xhtml );
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function replaceText( $search, $replace, $include=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->replaceText( $search, $replace, $include );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchText( $string )
    {
        $this->checkStructuredData();
        return $this->structured_data->searchText( $string );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchXhtml( $string )
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_XHTML_PAGE . E_SPAN );
        }

        return strpos( $this->xhtml, $string ) !== false;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setBlock( $identifier, Block $block=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setBlock( $identifier, $block );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setContentType( ContentType $c, $exception=true )
    {
           // nothing to do if already set
        if( $c->getId() == $this->getContentType()->getId() )
        {
            echo "Nothing to do" . BR;
            return $this;
        }
    
        // part 1: get the page level blocks and formats
        $block_format_array = $this->getPageLevelRegionBlockFormat();
        
        // just the default config, other config can be added
        $default_configuration       = $this->getContentType()->
            getConfigurationSet()->getDefaultConfiguration();
        $default_configuration_name  = $default_configuration->getName();
        $default_config_page_regions = 
            $default_configuration->getPageRegions();
        $default_region_names        = 
            $default_configuration->getPageRegionNames();
        
        $page_level_config  = 
            $this->page_configuration_map[ $default_configuration_name ];
        $page_level_regions = $page_level_config->getPageRegions();
        $page_region_names  = $page_level_config->getPageRegionNames();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $block_format_array ); }
        
        // part 2: switch content type
        if( $c == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET . E_SPAN );

        $page = $this->getProperty();
        $page->contentTypeId      = $c->getId();
        $page->contentTypePath    = $c->getPath();
        
        $configuration_array = array();
        $new_configurations = $c->getPageConfigurationSet()->
            getPageConfigurations();
        
        foreach( $new_configurations as $new_configuration )
        {
            $configuration_array[] = $new_configuration->toStdClass();
        }
        
        $page->pageConfigurations->pageConfiguration = $configuration_array;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page->pageConfigurations ); }
        
        $asset = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $page;
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );        
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $this->getProperty()->pageConfigurations ); }
        
        $this->reloadProperty();
        $this->processPageConfigurations( 
            $this->getProperty()->pageConfigurations->pageConfiguration );
        
        $this->content_type = $c;
        parent::setPageContentType( $this->content_type );
        
            
        if( isset( $this->getProperty()->structuredData ) )
        {
            $this->data_definition_id = $this->content_type->getDataDefinitionId();
            

            // structuredDataNode could be empty for xml pages
            if( isset( $this->getProperty()->structuredData )  &&
                isset( $this->getProperty()->structuredData->structuredDataNodes ) &&
                isset( $this->getProperty()->structuredData->structuredDataNodes->structuredDataNode ) 
            )
            {
                if( $exception ) // defaulted to true
                    $this->processStructuredDataPhantom( $this->data_definition_id );
            }
        }
        else
        {
            $this->xhtml = $this->getProperty()->xhtml;
        }
        

        // part 3: plug the blocks and formats back in
        $count = count( array_keys( $block_format_array) );
        
        if( $count > 0 )
        {
            $service = $this->getService();
            $page_level_config  = 
                $this->page_configuration_map[ $default_configuration_name ];
            $page_region_names  = $page_level_config->getPageRegionNames();
            
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page_region_names ); }
            
            foreach( $block_format_array as $region => $block_format )
            {
                // only if the region exists in the current config
                if( in_array( $region, $page_region_names ) )
                {
                    if( isset( $block_format[ 'block' ] ) )
                    {
                        $block_id = $block_format[ 'block' ];
                    }
                    if( isset( $block_format[ 'format' ] ) )
                    {
                        $format_id = $block_format[ 'format' ];
                    }
                
                    if( isset( $block_id ) )
                    {
                        $block = $this->getAsset( 
                            $service, $service->getType( $block_id ), $block_id );
                        $this->setRegionBlock( 
                            $default_configuration_name, $region, $block );
                    }
                    else if( isset( $block_format[ 'no-block' ] ) )
                    {
                        $this->setRegionNoBlock( 
                            $default_configuration_name, $region, true );
                    }
                
                    if( isset( $format_id ) )
                    {
                        $format = $this->getAsset( 
                            $service, $service->getType( $format_id ), $format_id );
                        $this->setRegionFormat( 
                            $default_configuration_name, $region, $format );
                    }
                    else if( isset( $block_format[ 'no-format' ] ) )
                    {
                        $this->setRegionNoFormat( 
                            $default_configuration_name, $region, true );
                    }
                }
            }
            
            if( $exception )
                $this->edit();
            else
                $this->editWithoutException();
        }
        
        if( self::DEBUG && self::DUMP ) { $page  = $this->getProperty(); u\DebugUtility::dump( $page->pageConfigurations ); }

        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setFile( $identifier, File $file=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setFile( $identifier, $file );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setLinkable( $identifier, Linkable $linkable=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setLinkable( $identifier, $linkable );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setMaintainAbsoluteLinks( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean" . E_SPAN );
        
        $this->getProperty()->maintainAbsoluteLinks = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setMetadata( p\Metadata $m )
    {
        $page = $this->getProperty();
        $page->metadata = $m->toStdClass();
        
        $asset = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $page;
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPage( $identifier, Page $page=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setPage( $identifier, $page );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setRegionBlock( $config_name, $region_name, Block $block=NULL )
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException(
                S_SPAN . "Path: " . $this->getPath() . E_SPAN . BR .
                "The page configuration $config_name does not exist." 
            );
        }
    
        if( self::DEBUG )
        {
            u\DebugUtility::out( "Setting block to region" . BR . "Region name: " . $region_name );
            if( isset( $block ) )
                u\DebugUtility::out( "Block ID: " . $block->getId() );
            else
                u\DebugUtility::out( "No block passed in." );
        }
        
        $this->page_configuration_map[ $config_name ]->setRegionBlock( $region_name, $block );
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setRegionFormat( $config_name, $region_name, Format $format=NULL )
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        $this->page_configuration_map[ $config_name ]->setRegionFormat( $region_name, $format );
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setRegionNoBlock( $config_name, $region_name, $no_block )
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        $this->page_configuration_map[ $config_name ]->setRegionNoBlock( $region_name, $no_block );
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setRegionNoFormat( $config_name, $region_name, $no_format )
    {
        if( !isset( $this->page_configuration_map[ $config_name ] ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $config_name does not exist." . E_SPAN );
        }
    
        $this->page_configuration_map[ $config_name ]->setRegionNoFormat( $region_name, $no_format );
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setShouldBeIndexed( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean" . E_SPAN );
            
        $this->getProperty()->shouldBeIndexed = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setShouldBePublished( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean" . E_SPAN );
            
        $this->getProperty()->shouldBePublished = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setStructuredData( p\StructuredData $structured_data )
    {
        $this->structured_data = $structured_data;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $structured_data ); }
        
        $this->edit();
        $dd_id = $this->getDataDefinition()->getId();
        $this->processStructuredDataPhantom( $dd_id );
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setSymlink( $identifier, Symlink $symlink=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setSymlink( $identifier, $symlink );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setText( $identifier, $text )
    {
        $this->checkStructuredData();
        $this->structured_data->setText( $identifier, $text );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setXhtml( $xhtml )
    {
        if( !$this->hasStructuredData() )
        {
            $this->xhtml = $xhtml;
        }
        else
        {
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_XHTML_PAGE . E_SPAN );
        }
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function swapData( $identifier1, $identifier2 )
    {
        $this->checkStructuredData();
        $this->structured_data->swapData( $identifier1, $identifier2 );
        $this->edit()->processStructuredDataPhantom( $this->data_definition_id );

        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function unpublish()
    {
        $this->getService()->unpublish( $this->getIdentifier() );
        return $this;
    }
    
    private function checkStructuredData()
    {
        if( !$this->hasStructuredData() )
        {
            throw new e\WrongPageTypeException( 
                S_SPAN . c\M::NOT_DATA_DEFINITION_PAGE . " " . $this->getId() . E_SPAN );
        }
    }
    
    // to bypass processStructuredData
    private function editWithoutException()
    {
        return $this->edit( NULL, NULL, "", "", false );
    }

    private function processPageConfigurations( $page_config_std )
    {
        $this->page_configurations = array();
        
        if( !is_array( $page_config_std ) )
        {
            $page_config_std = array( $page_config_std );
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $page_config_std ); }
        
        foreach( $page_config_std as $pc_std )
        {
            $pc = new p\PageConfiguration( $pc_std, $this->getService(), self::TYPE );
            $this->page_configurations[] = $pc;
            $this->page_configuration_map[ $pc->getName() ] = $pc;
        }
    }

    private function processStructuredDataPhantom( $data_definition_id )
    {
        $this->structured_data = new p\StructuredDataPhantom( 
            $this->getProperty()->structuredData, 
            $this->getService(),
            $data_definition_id,
            $this
        );
    }

    private $structured_data;
    private $page_configurations; // an array of objects
    private $page_configuration_map;
    private $data_definition_id;
    private $content_type;
}