<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/11/2016 Added constants and methods related to plugins.
  * 5/28/2015 Added namespaces.
  * 9/25/2014 Added setWorkflowMode.
  * 7/29/2014 Added getPluginStd, setPlugins.
  * 7/1/2014 Removed copy.
  * 5/23/2014 Fixed a bug in setBaseAsset.
  * 5/22/2014 Added setAllowSubfolderPlacement, 
  *   setFolderPlacementPosition, setOverwrite, and setBaseAsset.
  * 5/21/2014 Fixed some bugs related to foreach.
 */
/**
 * An AssetFactory object represents an asset factory asset
 *
 * @link http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/  asset-factory.php
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class AssetFactory extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::ASSETFACTORY;
    
    const WORKFLOW_MODE_FACTORY = c\T::FACTORY_CONTROLLED;
    const WORKFLOW_MODE_FOLDER  = c\T::FOLDER_CONTROLLED;
    const WORKFLOW_MODE_NONE    = c\T::NONE;
    
    /* 7.14.2 */
    const CREATE_RESIZED_IMAGES_PLUGIN                 = "com.cms.assetfactory.CreateResizedImagesPlugin";
    const DISPLAY_TO_SYSTEM_NAME_PLUGIN                = "com.cms.assetfactory.DisplayToSystemNamePlugin";
    const FILE_LIMIT_PLUGIN                            = "com.cms.assetfactory.FileLimitPlugin";
    const IMAGE_RESIZER_PLUGIN                         = "com.cms.assetfactory.ImageResizerPlugin";
    const PAGE_NAME_CHARS_LIMIT_PLUGIN                 = "com.cms.assetfactory.PageNameCharsLimitPlugin";
    const SET_START_DATE_PLUGIN                        = "com.cms.assetfactory.SetStartDatePlugin";
    const STRUCTURED_DATA_FIELD_TO_SYSTEM_NAME_PLUGIN  = "com.cms.assetfactory.StructuredDataFieldToSystemNamePlugin";
    const STRUCTURED_DATA_FIELDS_TO_SYSTEM_NAME_PLUGIN = "com.cms.assetfactory.StructuredDataFieldsToSystemNamePlugin";
    const TITLE_TO_SYSTEM_NAME_PLUGIN                  = "com.cms.assetfactory.TitleToSystemNamePlugin";
    
    const CREATE_RESIZED_PARAM_NUM_ADDITIONAL_IMAGES   = "assetfactory.plugin.createresized.param.name.numadditionalimages";
    const CREATE_RESIZED_PARAM_WIDTH                   = "assetfactory.plugin.createresized.param.name.width";
    const CREATE_RESIZED_PARAM_HEIGHT                  = "assetfactory.plugin.createresized.param.name.height";
    const FILE_LIMIT_PARAM_SIZE                        = "assetfactory.plugin.filelimit.param.name.size";
    const FILE_LIMIT_PARAM_FILENAME_REGEX              = "assetfactory.plugin.filelimit.param.name.filenameregex";
    const IMAGE_RESIZER_PARAM_HEIGHT                   = "assetfactory.plugin.imageresizer.param.name.height";
    const IMAGE_RESIZER_PARAM_WIDTH                    = "assetfactory.plugin.imageresizer.param.name.width";
    const PAGE_NAME_PARAM_NAME_REGEX                   = "assetfactory.plugin.pagename.param.name.nameregex";
    const SET_START_DATE_PARAM_OFFSET                  = "assetfactory.plugin.setstartdate.param.name.offset";
    const SD_FIELD_TO_SYSTEM_NAME_PARAM_FIELD_ID       = "assetfactory.plugin.sdfieldtosystemname.param.name.fieldid";
    const SD_FIELDS_TO_SYSTEM_NAME_PARAM_SPACE_TOKEN   = "assetfactory.plugin.sdfieldstosystemname.param.name.spacetoken";
    const SD_FIELDS_TO_SYSTEM_NAME_PARAM_CONCAT_TOKEN  = "assetfactory.plugin.sdfieldstosystemname.param.name.concattoken";
    const SD_FIELDS_TO_SYSTEM_NAME_PARAM_FIELD_IDS     = "assetfactory.plugin.sdfieldstosystemname.param.name.fieldids";
    
    public function __construct( aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        if( isset( $this->getProperty()->plugins ) && 
            isset( $this->getProperty()->plugins->plugin ) )
        {
            $this->processPlugins();
        }
    }
    
    public function addGroup( Group $g ) : Asset
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }
    
        $group_name   = $g->getName();
        $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
        
        if( !in_array( $group_name, $group_array ) )
        {
            $group_array[] = $group_name;
        }
        
        $group_string = implode( ';', $group_array );
        $this->getProperty()->applicableGroups = $group_string;
        return $this;
    }

    public function addPlugin( $name ) : Asset
    {
        if( !in_array( $name, self::$plugin_names ) )
            throw new e\NoSuchPluginException( 
                S_SPAN . "The plugin $name does not exist." . E_SPAN );
                
        if( $this->hasPlugin( $name ) )
            return $this;
            
        $new_plugin_std             = new \stdClass();
        $new_plugin_std->name       = $name;
        $new_plugin_std->parameters = new \stdClass();
        $this->plugins[]            = new p\Plugin( $new_plugin_std );
        
        return $this->edit();
    }
    
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $asset = new \stdClass();
        $this->getProperty()->plugins->plugin = array();
        
        if( count( $this->plugins ) > 0 )
        {
            foreach( $this->plugins as $plugin )
            {
                $this->getProperty()->plugins->plugin[] = $plugin->toStdClass();
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
    
    public function getAllowSubfolderPlacement() : bool
    {
        return $this->getProperty()->allowSubfolderPlacement;
    }

    public function getApplicableGroups()
    {
        return $this->getProperty()->applicableGroups;
    }
    
    public function getAssetType()
    {
        return $this->getProperty()->assetType;
    }
    
    public function getBaseAssetId()
    {
        return $this->getProperty()->baseAssetId;
    }
    
    public function getBaseAssetPath()
    {
        return $this->getProperty()->baseAssetPath;
    }
    
    public function getBaseAssetRecycled() : bool
    {
        return $this->getProperty()->baseAssetRecycled;
    }
    
    public function getFolderPlacementPosition()
    {
        return $this->getProperty()->folderPlacementPosition;
    }
    
    public function getOverwrite() : bool
    {
        return $this->getProperty()->overwrite;
    }
    
    public function getPlacementFolderId()
    {
        return $this->getProperty()->placementFolderId;
    }
    
    public function getPlacementFolderPath()
    {
        return $this->getProperty()->placementFolderPath;
    }
    
    public function getPlacementFolderRecycled() : bool
    {
        return $this->getProperty()->placementFolderRecycled;
    }

    public function getPlugin( string $name ) : p\Plugin
    {
        if( $this->hasPlugin( $name ) )
        {
            foreach( $this->plugins as $plugin )
            {
                if( $plugin->getName() == $name )
                {
                    return $plugin;
                }
            }
        }
        throw new e\NoSuchPluginException( 
            S_SPAN . "The plugin $name does not exist." . E_SPAN );    
    }
    
    public function getPluginNames() : array
    {
        $names = array();
        
        if( count( $this->plugins ) > 0 )
        {
            foreach( $this->plugins as $plugin )
            {
                $names[] = $plugin->getName();
            }
        }
        return $names;
    }
    
    public function getPluginStd() : \stdClass
    {
        return $this->getProperty()->plugins;
    }
    
    public function getWorkflowDefinitionId()
    {
        return $this->getProperty()->workflowDefinitionId;
    }
    
    public function getWorkflowDefinitionPath()
    {
        return $this->getProperty()->workflowDefinitionPath;
    }
    
    public function getWorkflowMode()
    {
        return $this->getProperty()->workflowMode;
    }
    
    public function hasPlugin( string $name ) : bool
    {
        if( count( $this->plugins ) > 0 )
        {
            foreach( $this->plugins as $plugin )
            {
                if( $plugin->getName() == $name )
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function isApplicableToGroup( Group $g ) : bool
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }

        $group_name = $g->getName();
        $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
        return in_array( $group_name, $group_array );
    }
    
    public function removeGroup( Group $g ) : Asset
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }
        
        $group_name   = $g->getName();
        $group_string = $this->getProperty()->applicableGroups;
        $group_array  = explode( ';', $group_string );
            
        if( in_array( $group_name, $group_array ) )
        {
            $temp = array();
            
            foreach( $group_array as $group )
            {
                if( $group != $group_name )
                {
                    $temp[] = $group;
                }
            }
            $group_array = $temp;
        }
        
        $group_string = implode( ';', $group_array );
        $this->getProperty()->applicableGroups = $group_string;
        
        return $this;
    }
    
    public function removePlugin( string $name ) : Asset
    {
        if( !in_array( $name, self::$plugin_names ) )
            throw new e\NoSuchPluginException( 
                S_SPAN . "The plugin $name does not exist." . E_SPAN );
                
        if( count( $this->plugins ) > 0 )
        {
            $temp = array();
            
            foreach( $this->plugins as $plugin )
            {
                if( $plugin->getName() != $name )
                {
                    $temp[] = $plugin;
                }
            }
            $this->plugins = $temp;
        }
            
        return $this->edit();
    }
    
    public function removePluginParameter( string $plugin_name, string $param_name ) : Asset
    {
        if( !in_array( $plugin_name, self::$plugin_names ) )
            throw new e\NoSuchPluginException( 
                S_SPAN . "The plugin $name does not exist." . E_SPAN );
                
        $plugin = $this->getPlugin( $plugin_name );
        $plugin->removeParameter( $param_name );
        
        return $this->edit();
    }
    
    public function setAllowSubfolderPlacement( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->allowSubfolderPlacement = $bool;
        
        return $this;
    }
    
    public function setBaseAsset( Asset $a=NULL ) : Asset
    {
        if( isset( $a ) )
        {
            $type = $a->getType();
            
            if( u\StringUtility::startsWith( strtolower( $type ), 'block' ) )
            {
                $type = 'block';
            }
            else if( u\StringUtility::startsWith( strtolower( $type ), 'format' ) )
            {
                $type = 'format';
            }
            
            $this->getProperty()->assetType     = $type;
            $this->getProperty()->baseAssetId   = $a->getId();
            $this->getProperty()->baseAssetPath = $a->getPath();
        }
        else
        {
            $this->getProperty()->assetType     = File::TYPE; // dummpy type
            $this->getProperty()->baseAssetId   = NULL;
            $this->getProperty()->baseAssetPath = NULL;
        }
        return $this;
    }
    
    public function setFolderPlacementPosition( $value ) : Asset
    {
        if( is_nan( $value ) )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "$value is not a number" . E_SPAN );
        }
        
        $this->getProperty()->folderPlacementPosition = intval( $value );
        
        return $this;
    }
    
    public function setOverwrite( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->overwrite = $bool;
        
        return $this;
    }
    
    public function setPlacementFolder( Folder $folder ) : Asset
    {
        if( $folder == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_FOLDER . E_SPAN );
            
        $this->getProperty()->placementFolderId   = $folder->getId();
        $this->getProperty()->placementFolderPath = $folder->getPath();
        
        return $this;
    }
    
    public function setPluginParameterValue( string $plugin_name, string $param_name, string $param_value ) : Asset
    {
        if( !in_array( $plugin_name, self::$plugin_names ) )
            throw new e\NoSuchPluginException( 
                S_SPAN . "The plugin $plugin_name does not exist." . E_SPAN );
        
        if( !in_array( $param_name, self::$plugin_name_param_map[ $plugin_name ] ) )
            throw new e\NoSuchPluginParameterException( 
                S_SPAN . "The parameter $param_name does not exist." . E_SPAN );
        
        
        $plugin    = $this->getPlugin( $plugin_name );
        
        if( $plugin->hasParameter( $param_name ) )
            $plugin->setParameterValue( $param_name, $param_value );
        else
            $plugin->addParameter( $param_name, $param_value );
        
        return $this->edit();
    }
    
    public function setPlugins( \stdClass $plugins ) : Asset
    {
        $property = $this->getProperty();
        $property->plugins = $plugins;
        $asset = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $property;
        
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
    
    public function setWorkflowMode( string $mode=c\T::NONE, WorkflowDefinition $wd=NULL ) : Asset
    {
        if( !c\WorkflowModeValues::isWorkflowMode( $mode ) )
            throw new e\UnacceptableWorkflowModeException( 
                S_SPAN . "The workflow mode $mode is unacceptable." . E_SPAN );
            
        if( $mode == self::WORKFLOW_MODE_FACTORY )
        {
            if( $wd == NULL )
                throw new e\NullAssetException( 
                    S_SPAN . c\M::NULL_WORKFLOW_DEFINITION . E_SPAN );
            else
            {
                $this->getProperty()->workflowDefinitionId   = $wd->getId();
                $this->getProperty()->workflowDefinitionPath = $wd->getPath();
            }
        }
        else
        {
            $this->getProperty()->workflowDefinitionId   = NULL;
            $this->getProperty()->workflowDefinitionPath = NULL;
        }
        
        $this->getProperty()->workflowMode = $mode;
        return $this;
    }
    
    private function processPlugins()
    {
        $this->plugins = array();

        $plugins = $this->getProperty()->plugins->plugin;
            
        if( !is_array( $plugins ) )
        {
            $plugins = array( $plugins );
        }
        
        $count = count( $plugins );
        
        for( $i = 0; $i < $count; $i++ )
        {
            $this->plugins[] = 
                new p\Plugin( $plugins[ $i ] );
        }
    }
    
    public static $plugin_names = array(
        self::CREATE_RESIZED_IMAGES_PLUGIN,
        self::DISPLAY_TO_SYSTEM_NAME_PLUGIN,
        self::FILE_LIMIT_PLUGIN,
        self::IMAGE_RESIZER_PLUGIN,
        self::PAGE_NAME_CHARS_LIMIT_PLUGIN,
        self::SET_START_DATE_PLUGIN,
        self::STRUCTURED_DATA_FIELD_TO_SYSTEM_NAME_PLUGIN,
        self::STRUCTURED_DATA_FIELDS_TO_SYSTEM_NAME_PLUGIN,
        self::TITLE_TO_SYSTEM_NAME_PLUGIN
    );
    
    public static $plugin_name_param_map = array(
        self::CREATE_RESIZED_IMAGES_PLUGIN => array(
            self::CREATE_RESIZED_PARAM_NUM_ADDITIONAL_IMAGES,
            self::CREATE_RESIZED_PARAM_WIDTH,
            self::CREATE_RESIZED_PARAM_HEIGHT
        ),
        self::FILE_LIMIT_PLUGIN => array(
            self::FILE_LIMIT_PARAM_SIZE,
            self::FILE_LIMIT_PARAM_FILENAME_REGEX
        ),
        self::IMAGE_RESIZER_PLUGIN => array(
            self::IMAGE_RESIZER_PARAM_HEIGHT,
            self::IMAGE_RESIZER_PARAM_WIDTH
        ),
        self::PAGE_NAME_CHARS_LIMIT_PLUGIN => array(
            self::PAGE_NAME_PARAM_NAME_REGEX ),
        self::SET_START_DATE_PLUGIN => array(
            self::SET_START_DATE_PARAM_OFFSET ),
        self::STRUCTURED_DATA_FIELD_TO_SYSTEM_NAME_PLUGIN => array(
            self::SD_FIELD_TO_SYSTEM_NAME_PARAM_FIELD_ID ),
        self::STRUCTURED_DATA_FIELDS_TO_SYSTEM_NAME_PLUGIN => array(
            self::SD_FIELDS_TO_SYSTEM_NAME_PARAM_SPACE_TOKEN,
            self::SD_FIELDS_TO_SYSTEM_NAME_PARAM_CONCAT_TOKEN,
            self::SD_FIELDS_TO_SYSTEM_NAME_PARAM_FIELD_IDS
        )
    );
    
    private $plugins;
}
?>