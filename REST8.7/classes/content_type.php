<?php
require_once('auth_test.php');

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$mode = 'all';
$mode = 'display';
//$mode = 'dump';
//$mode = 'get';
//$mode = 'set';
$mode = 'inline';
//$mode = 'raw';

try
{
    $id = "f1942e738b7ffe832dc7cebe1bdf2315"; // RWD Old
    $ct  = $cascade->getAsset( a\ContentType::TYPE, $id );
    
    switch( $mode )
    {
        case 'all':
        case 'display':
            $ct->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $ct->dump();
            
            if( $mode != 'all' )
                break;
                
        case 'get':
            echo "ID: " . $ct->getId() . BR;
            echo "Dumping names of contentTypePageConfigurations: " . BR;
            u\DebugUtility::dump( $ct->getContentTypePageConfigurationNames() );
            
            echo "Inline editable field names", BR;
            u\DebugUtility::dump( $ct->getInlineEditableFieldNames() );
            
            //echo $ct->getDataDefinitionId(), BR;
            //echo $ct->getDataDefinitionPath(), BR;
            //echo $ct->getMetadataSetId(), BR;
            //echo $ct->getMetadataSetPath(), BR;
            //echo $ct->getPageConfigurationSetId(), BR;
            //echo $ct->getPageConfigurationSetPath(), BR;
            
            echo $ct->getPublishMode( "RWD" ), BR;
            echo u\StringUtility::boolToString(
                $ct->hasDataDefinitionGroupPath( "right-column-group/right-setup" ) ), BR;
            
            echo u\StringUtility::boolToString(
                $ct->hasInlineEditableField(
                    "RWD;DEFAULT;NULL;wired-metadata;title" ) ), BR;
                    
            echo u\StringUtility::boolToString(
                $ct->hasPageConfiguration( "RWD" ) ), BR;
                
            echo u\StringUtility::boolToString(
                $ct->hasRegion( "RWD", "BANNER 12 COLUMNS" ) ), BR;
            
            //u\DebugUtility::dump( $ct->getInlineEditableFields() );
            
            //$ct->getConfigurationSet()->dump();
            //$ct->getDataDefinition()->dump();
            //$ct->getMetadataSet()->dump();
            
            //u\DebugUtility::dump( $ct->getDataDefinition()->getIdentifiers() );
            
            if( $mode != 'all' )
                break;
                
        case 'set':
            $config_name = 'Printer';
            
            if( $ct->hasPageConfiguration( $config_name ) )
            {
                $ct->setPublishMode( 
                    $config_name, 
                    a\ContentType::PUBLISH_MODE_ALL_DESTINATIONS )->
                    edit();
            }

            if( $mode != 'all' )
                break;

        case 'inline':
            $ct->dump();
            u\DebugUtility::dump( $ct->getInlineEditableFieldNames() );
            
            //
            $ief = "RWD;DEFAULT;NULL;data-definition;center-banner";
            if( $ct->hasInlineEditableField( $ief ) )
                $ct->removeInlineEditableField( $ief )->edit()->dump();
        
            $config_name = 'RWD';
            $region_name = 'DEFAULT';
            $group_path  = 'NULL';
            $type        = c\T::WIRED_METADATA;
            $name        = a\ContentType::DISPLAY_NAME;
            
            if( $ct->hasRegion( $config_name, $region_name ) )
            {
                echo "The region $config_name, $region_name is found" . BR;
            }

            // wired field
            $field_name = $config_name . a\DataDefinition::DELIMITER .
                          $region_name . a\DataDefinition::DELIMITER .
                          $group_path . a\DataDefinition::DELIMITER .
                          $type . a\DataDefinition::DELIMITER . $name;
            if( $ct->hasInlineEditableField( $field_name ) )
            {
                echo "The field is found. Now removing it:" . BR;
                $ct->removeInlineEditableField( $field_name )->edit();
            }
            else
            {
                echo "The field does not exist. Now adding it:" . BR;
                $ct->addInlineEditableField( 
                    $config_name, $region_name, $group_path, 
                    $type, $name )->edit();
            }
         
            // dynamic field
            $config_name = 'RWD';
            $region_name = 'DEFAULT';
            $group_path  = 'NULL';
            $type        = c\T::DYNAMIC_METADATA;
            $name        = "exclude-from-left";
            
            if( $ct->hasRegion( $config_name, $region_name ) )
            {
                echo "The region $config_name, $region_name is found" . BR;
            }
            
            $field_name = $config_name . a\DataDefinition::DELIMITER .
                          $region_name . a\DataDefinition::DELIMITER .
                          $group_path . a\DataDefinition::DELIMITER .
                          $type . a\DataDefinition::DELIMITER . $name;
            
            if( $ct->hasInlineEditableField( $field_name ) )
            {
                echo "The field is found. Now removing it:" . BR;
                $ct->removeInlineEditableField( $field_name )->edit();
            }
            else
            {
                echo "The field does not exist. Now adding it:" . BR;
                $ct->addInlineEditableField( 
                    $config_name, $region_name, $group_path, 
                    $type, $name )->edit();
            }
            u\DebugUtility::dump( $ct->getInlineEditableFieldNames() );
/*/   /*/            

            if( $mode != 'all' )
                break;

        case 'raw':
            $ct = $service->retrieve( $service->createId( 
                c\T::CONTENTTYPE, $id ), c\P::CONTENTTYPE );
            echo S_PRE;
            var_dump( $ct );
            echo E_PRE;
        
            if( $mode != 'all' )
                break;
    }
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