<?php
require_once('auth_test.php');

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$mode = 'all';
$mode = 'display';
$mode = 'dump';
$mode = 'get';
$mode = 'set';
//$mode = 'raw';
$mode = "plugin";
//$mode = "none";

try
{
    $id = "a2d077cc8b7ffe8353cc17e9cb2c2582";
    $af = $cascade->getAsset( a\AssetFactory::TYPE, $id );

    switch( $mode )
    {
        case 'all':
        case 'display':
            $af->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $af->dump();
            
            if( $mode != 'all' )
                break;

        case 'get':
            echo c\L::ID . $af->getId() . BR .
                 c\L::NAME . $af->getName() . BR .
                 c\L::PATH . $af->getPath() . BR .
                 c\L::PROPERTY_NAME . $af->getPropertyName() . BR .
                 c\L::SITE_NAME . $af->getSiteName() . BR .
                 c\L::TYPE . $af->getType() . BR .
                 
                 "Allow subfolder placement: " . 
                 u\StringUtility::boolToString( $af->getAllowSubfolderPlacement() ) . BR .
                 "Applicable groups: " . 
                 u\StringUtility::getCoalescedString( $af->getApplicableGroups() ) . BR .
                 "Asset type: " . 
                 $af->getAssetType() . BR .
                 "Base asset ID: " . 
                 u\StringUtility::getCoalescedString( $af->getBaseAssetId() ) . BR .
                 "Base asset path: " . 
                 u\StringUtility::getCoalescedString( $af->getBaseAssetPath() ) . BR .
                 "Base asset recycled: " . 
                 u\StringUtility::boolToString( $af->getBaseAssetRecycled() ) . BR .
                 "Folder placement position: " . 
                 $af->getFolderPlacementPosition() . BR .
                 "Overwrite: " . 
                 u\StringUtility::boolToString( $af->getOverwrite() ) . BR .
                 c\L::PARENT_CONTAINER_ID . 
                 $af->getParentContainerId() . BR .
                 c\L::PARENT_CONTAINER_PATH . 
                 $af->getParentContainerPath() . BR .
                 
                 "Placement folder ID: " . 
                 u\StringUtility::getCoalescedString( $af->getPlacementFolderId() ) . BR .
                 "Placement folder path: " . 
                 u\StringUtility::getCoalescedString( $af->getPlacementFolderPath() ) . BR .
                 "Placement folder recycled: " . 
                 u\StringUtility::boolToString( $af->getPlacementFolderRecycled() ) . BR .
                 c\L::SITE_ID .
                 $af->getSiteId() . BR .
                 c\L::SITE_NAME . 
                 $af->getSiteName() . BR .
                 "Workflow definition ID: " . 
                 u\StringUtility::getCoalescedString( $af->getWorkflowDefinitionId() ) . BR .
                 "Workflow definition path: " . 
                 u\StringUtility::getCoalescedString( $af->getWorkflowDefinitionPath() ) . BR .
                 "Workflow mode: " . 
                 $af->getWorkflowMode() . BR ;
                 
                 if( $af->hasPlugin( a\AssetFactory::FILE_LIMIT_PLUGIN ) )
                     u\DebugUtility::dump( $af->getPlugin( a\AssetFactory::FILE_LIMIT_PLUGIN ) );
                 u\DebugUtility::dump( $af->getPluginNames() );
                 u\DebugUtility::dump( $af->getPluginStd() );
            
            if( $mode != 'all' )
                break;

        case 'set':
             $group_name = "cru";
             $group      = a\Asset::getAsset( $service, a\Group::TYPE, $group_name );
             $af->addGroup( $group )->edit();
             
             if( $af->isApplicableToGroup( $group ) )
             {
                 echo "Applicable to ", $group->getName(), BR;
             }
             else
             {
                 echo "Not applicable to $group_name" . BR;
             }
             
             $af->removeGroup( $group )->edit()->dump( true );
             
             if( $af->isApplicableToGroup( $group ) )
             {
                 echo "Applicable to $group_name" . BR;
             }
             else
             {
                 echo "Not applicable to $group_name" . BR;
             }
             
             $af->setAllowSubfolderPlacement( true )->
                 setBaseAsset()->
                 setFolderPlacementPosition( 1 )->
                 setOverwrite( true )->
                 setPlacementFolder( $cascade->getAsset(
                     a\Folder::TYPE, "_cascade", "formats" ) )->
                 setWorkflowMode( c\T::NONE )->
                 edit()->dump();
               
            if( $mode != 'all' )
                break;

        case 'plugin':
            $temp_plugins = $af->getPlugins();
            
            $af->addPlugin( a\AssetFactory::CREATE_RESIZED_IMAGES_PLUGIN )->
                addPlugin( a\AssetFactory::FILE_LIMIT_PLUGIN )->
                dump();
            $af->setPluginParameterValue(
                    a\AssetFactory::CREATE_RESIZED_IMAGES_PLUGIN,
                    a\AssetFactory::CREATE_RESIZED_PARAM_WIDTH,
                    "600" )->
                setPluginParameterValue(
                    a\AssetFactory::FILE_LIMIT_PLUGIN,
                    a\AssetFactory::FILE_LIMIT_PARAM_SIZE,
                    "13MB" )->
                dump();

            $af->removePlugin( a\AssetFactory::CREATE_RESIZED_IMAGES_PLUGIN )->dump();
            
            $af->addPlugin( a\AssetFactory::IMAGE_RESIZER_PLUGIN )->
                setPluginParameterValue(
                    a\AssetFactory::IMAGE_RESIZER_PLUGIN,
                    a\AssetFactory::IMAGE_RESIZER_PARAM_HEIGHT,
                    "45" )->
                setPluginParameterValue(
                    a\AssetFactory::IMAGE_RESIZER_PLUGIN,
                    a\AssetFactory::IMAGE_RESIZER_PARAM_WIDTH,
                    "80" )->
                dump();
                
            $af->addPlugin( a\AssetFactory::STRUCTURED_DATA_FIELD_TO_SYSTEM_NAME_PLUGIN )->
                setPluginParameterValue(
                    a\AssetFactory::STRUCTURED_DATA_FIELD_TO_SYSTEM_NAME_PLUGIN,
                    a\AssetFactory::SD_FIELD_TO_SYSTEM_NAME_PARAM_FIELD_ID,
                    "those" )->
                dump();
                
            $af->removePluginParameter( 
                    a\AssetFactory::STRUCTURED_DATA_FIELD_TO_SYSTEM_NAME_PLUGIN,
                    a\AssetFactory::SD_FIELD_TO_SYSTEM_NAME_PARAM_FIELD_ID )->
                dump();
            
            // undo everything
            $af->setPlugins( $temp_plugins )->dump();
/*/        
/*/        
                        
            if( $mode != 'all' )
                break;
            
        case 'raw':
            $af = $service->retrieve( 
                $service->createId( 
                    c\T::ASSETFACTORY, $id ), c\P::ASSETFACTORY );
            
            echo S_PRE;
            u\DebugUtility::dump( $af );
            echo E_PRE;
            
            if( $mode != 'all' )
                break;
    }
/*//*/
}
catch( \Exception $e )
{
    echo S_PRE . $e . E_PRE;
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE;
}
?>