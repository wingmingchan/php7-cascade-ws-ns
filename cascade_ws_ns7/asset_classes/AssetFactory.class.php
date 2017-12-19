<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 2/22/2017 Added addGroupName.
  * 1/10/2017 Added JSON structure.
  * 9/7/2016 Added getDescription and setDescription.
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
 * @link http://www.upstate.edu/web-services/api/asset-classes/asset-factory.php
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>An <code>AssetFactory</code> object represents an asset factory asset.</p>
<h2>Structure of <code>assetFactory</code></h2>
<pre>SOAP:
assetFactory
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  applicableGroups
  assetType
  baseAssetId
  baseAssetPath
  baseAssetRecycled (bool)
  description
  placementFolderId
  placementFolderPath
  placementFolderRecycled (bool)
  placementFolderId
  placementFolderPath
  allowSubfolderPlacement (bool)
  folderPlacementPosition (int)
  overwrite (bool)
  workflowMode
  workflowDefinitionId
  workflowDefinitionPath
  plugins
    plugin (NULL, stdClass or array of stdClass)

JSON:
assetFactory
  applicableGroups
  assetType
  baseAssetId
  baseAssetPath
  baseAssetRecycled (bool)
  placementFolderRecycled (bool)
  allowSubfolderPlacement (bool)
  description
  folderPlacementPosition (int)
  overwrite (bool)
  workflowMode
  plugins (array)
    stdClass
      name
      parameters (array)
        stdClass
          name
          value
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
        array( "getComplexTypeXMLByName" => "assetFactory" ),
        array( "getSimpleTypeXMLByName"  => "asset-factory-workflow-mode" ),
        array( "getComplexTypeXMLByName" => "asset-factory-plugins" ),
        array( "getComplexTypeXMLByName" => "asset-factory-plugin" ),
        array( "getComplexTypeXMLByName" => "asset-factory-plugin-parameters" ),
        array( "getComplexTypeXMLByName" => "asset-factory-plugin-parameter" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/asset_factory.php">asset_factory.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{ 
  "assetFactory":{
    "applicableGroups":"SUNY Upstate-testers",
    "assetType":"page",
    "baseAssetId":"c45b81837f0000014d7031650a7252a2",
    "baseAssetPath":"suny-upstate/base-assets/index",
    "baseAssetRecycled":false,
    "placementFolderRecycled":false,
    "allowSubfolderPlacement":true,
    "description":"Create New Page",
    "folderPlacementPosition":0,
    "overwrite":true,
    "workflowMode":"folder-controlled",
    "plugins":[ {
      "name":"com.cms.assetfactory.FileLimitPlugin",
      "parameters":[] } ],
    "parentContainerId":"a23a47017f0000011d450d2affc6b348",
    "parentContainerPath":"suny-upstate",
    "path":"suny-upstate/New Page",
    "siteId":"9c8883d07f00000140b4daea7170b336",
    "siteName":"POPs",
    "name":"New Page",
    "id":"c45e55597f0000014d70316542bae384" }
}
</pre>
</postscript>
</documentation>
*/
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
    
/**
<documentation><description><p>The constructor, overriding the parent method to process plugins.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        if( isset( $this->getProperty()->plugins ) && 
            isset( $this->getProperty()->plugins->plugin ) )
        {
            $this->processPlugins();
        }
    }
    
/**
<documentation><description><p>Adds the group name to <code>applicableGroups</code> and returns the calling object.</p></description>
<example>$af->addGroup( $group )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
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

/**
<documentation><description><p>Adds the group name to <code>applicableGroups</code> and returns the calling object.</p></description>
<example>$af->addGroupName( $group_name )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addGroupName( string $group_name ) : Asset
    {
        // check the existence of the group
        $group = Asset::getAsset( $this->getService(), Group::TYPE, $group_name );
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

/**
<documentation><description><p>Adds the named plugin, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$af->addPlugin( a\AssetFactory::CREATE_RESIZED_IMAGES_PLUGIN );</example>
<return-type>Asset</return-type>
<exception>NoSuchPluginException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example>$af->addGroup( $group )->edit();</example>
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
    
/**
<documentation><description><p>Returns <code>allowSubfolderPlacement</code>.</p></description>
<example>echo u\StringUtility::boolToString( $af->getAllowSubfolderPlacement() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getAllowSubfolderPlacement() : bool
    {
        return $this->getProperty()->allowSubfolderPlacement;
    }

/**
<documentation><description><p>Returns <code>applicableGroups</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $af->getApplicableGroups() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getApplicableGroups()
    {
        return $this->getProperty()->applicableGroups;
    }
    
/**
<documentation><description><p>Returns <code>assetType</code>.</p></description>
<example>echo $af->getAssetType() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getAssetType() : string
    {
        return $this->getProperty()->assetType;
    }
    
/**
<documentation><description><p>Returns <code>baseAssetId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $af->getBaseAssetId() ) . BR</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseAssetId()
    {
        return $this->getProperty()->baseAssetId;
    }
    
/**
<documentation><description><p>Returns <code>baseAssetPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $af->getBaseAssetPath() ) . BR</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseAssetPath()
    {
        return $this->getProperty()->baseAssetPath;
    }
    
/**
<documentation><description><p>Returns <code>baseAssetRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $af->getBaseAssetRecycled() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseAssetRecycled() : bool
    {
        return $this->getProperty()->baseAssetRecycled;
    }
    
/**
<documentation><description><p>Returns <code>description</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDescription()
    {
        return $this->getProperty()->description;
    }
    
/**
<documentation><description><p>Returns <code>folderPlacementPosition</code>.</p></description>
<example>echo $af->getFolderPlacementPosition() . BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getFolderPlacementPosition() : int
    {
        return $this->getProperty()->folderPlacementPosition;
    }
    
/**
<documentation><description><p>Returns <code>overwrite</code>.</p></description>
<example>echo u\StringUtility::boolToString( $af->getOverwrite() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getOverwrite() : bool
    {
        return $this->getProperty()->overwrite;
    }
    
/**
<documentation><description><p>Returns <code>placementFolderId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $af->getPlacementFolderId() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPlacementFolderId()
    {
        return $this->getProperty()->placementFolderId;
    }
    
/**
<documentation><description><p>Returns <code>placementFolderPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $af->getPlacementFolderPath() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPlacementFolderPath()
    {
        return $this->getProperty()->placementFolderPath;
    }
    
/**
<documentation><description><p>Returns <code>placementFolderRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $af->getPlacementFolderRecycled() ) . BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getPlacementFolderRecycled() : bool
    {
        return $this->getProperty()->placementFolderRecycled;
    }

/**
<documentation><description><p>Returns the <code>p\Plugin</code> object bearing that name.</p></description>
<example>if( $af->hasPlugin( a\AssetFactory::FILE_LIMIT_PLUGIN ) )
    u\DebugUtility::dump( $af->getPlugin( a\AssetFactory::FILE_LIMIT_PLUGIN ) );</example>
<return-type>Plugin</return-type>
<exception>NoSuchPluginException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Returns an array of <code>p\Plugin</code> names.</p></description>
<example>u\DebugUtility::dump( $af->getPluginNames() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Returns the <code>plugins</code> property.</p></description>
<example>u\DebugUtility::dump( $af->getPluginStd() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function getPluginStd() : \stdClass
    {
        return $this->getProperty()->plugins;
    }
    
/**
<documentation><description><p>Returns <code>workflowDefinitionId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $af->getWorkflowDefinitionId() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflowDefinitionId()
    {
        return $this->getProperty()->workflowDefinitionId;
    }
    
/**
<documentation><description><p>Returns <code>workflowDefinitionPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $af->getWorkflowDefinitionPath() ) . BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflowDefinitionPath()
    {
        return $this->getProperty()->workflowDefinitionPath;
    }
    
/**
<documentation><description><p>Returns <code>workflowMode</code>.</p></description>
<example>echo $af->getWorkflowMode() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflowMode() : string
    {
        return $this->getProperty()->workflowMode;
    }
    
/**
<documentation><description><p>Returns a bool indicating whether the <code>p\Plugin</code> so named exists.</p></description>
<example>if( $af->hasPlugin( a\AssetFactory::FILE_LIMIT_PLUGIN ) )
    u\DebugUtility::dump( $af->getPlugin( a\AssetFactory::FILE_LIMIT_PLUGIN ) );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Returns a bool indicating whether the asset factory is applicable to the group.</p></description>
<example>if( $af->isApplicableToGroup( $group ) )
    echo "Applicable to ", $group->getName(), BR;
</example>
<return-type>bool</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Removes the group name from <code>applicableGroups</code> and returns the calling object.</p></description>
<example>$af->removeGroup( $group )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Removes the named plugin, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$af->removePlugin( a\AssetFactory::CREATE_RESIZED_IMAGES_PLUGIN );</example>
<return-type>Asset</return-type>
<exception>NoSuchPluginException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Removes the named parameter from the named plugin, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$af->removePluginParameter( 
    a\AssetFactory::STRUCTURED_DATA_FIELD_TO_SYSTEM_NAME_PLUGIN,
    a\AssetFactory::SD_FIELD_TO_SYSTEM_NAME_PARAM_FIELD_ID );</example>
<return-type>Asset</return-type>
<exception>NoSuchPluginException, NoSuchPluginParameterException</exception>
</documentation>
*/
    public function removePluginParameter( string $plugin_name, string $param_name ) : Asset
    {
        if( !in_array( $plugin_name, self::$plugin_names ) )
            throw new e\NoSuchPluginException( 
                S_SPAN . "The plugin $name does not exist." . E_SPAN );
                
        $plugin = $this->getPlugin( $plugin_name );
        $plugin->removeParameter( $param_name );
        
        return $this->edit();
    }
    
/**
<documentation><description><p>Sets <code>allowSubfolderPlacement</code> and returns the calling object.</p></description>
<example>$af->setAllowSubfolderPlacement( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAllowSubfolderPlacement( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->allowSubfolderPlacement = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>assetType</code>, <code>baseAssetId</code>, and <code>baseAssetPath</code>,
and returns the calling object. If the asset passed in is <code>NULL</code>, then a dummy type <code>File::TYPE</code> is used to set <code>assetType</code>.</p></description>
<example>$af->setBaseAsset()->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Sets <code>description</code>,
and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDescription( string $desc=NULL ) : Asset
    {
        $this->getProperty()->description = $desc;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>folderPlacementPosition</code> and returns the calling object.</p></description>
<example>$af->setFolderPlacementPosition( 1 )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setFolderPlacementPosition( int $value ) : Asset
    {
        if( is_nan( $value ) )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "$value is not a number" . E_SPAN );
        }
        
        $this->getProperty()->folderPlacementPosition = intval( $value );
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>overwrite</code> and returns the calling object.</p></description>
<example>$af->setOverwrite( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setOverwrite( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->overwrite = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>placementFolderId</code> and <code>placementFolderPath</code>, and returns the calling object.</p></description>
<example>$af->setPlacementFolder( $cascade->getFolder( "images", "cascade-admin" ) )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function setPlacementFolder( Folder $folder ) : Asset
    {
        if( $folder == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_FOLDER . E_SPAN );
            
        $this->getProperty()->placementFolderId   = $folder->getId();
        $this->getProperty()->placementFolderPath = $folder->getPath();
        
        return $this;
    }
    
/**
<documentation><description><p>Sets the value of the named <code>parameter</code> in the named plugin, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$af->setPluginParameterValue(
    a\AssetFactory::FILE_LIMIT_PLUGIN,
    a\AssetFactory::FILE_LIMIT_PARAM_SIZE,
    "13" );</example>
<return-type>Asset</return-type>
<exception>NoSuchPluginException, NoSuchPluginParameterException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Sets the plugins, calls <code>edit</code>, and returns the calling object.</p></description>
<example>$temp_plugins = $af->getPluginStd();
// do something, and then restore the plugins
$af->setPlugins( $temp_plugins );
</example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Sets <code>workflowMode</code>, and if <code>$mode</code> is <code>c\T::FACTORY_CONTROLLED</code>,
sets <code>workflowDefinitionId</code> and <code>workflowDefinitionPath</code>, and returns the calling object.</p></description>
<example>$af->setWorkflowMode( c\T::NONE )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableWorkflowModeException, NullAssetException</exception>
</documentation>
*/
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