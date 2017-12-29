<?php
require_once('auth_test.php');

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

//$mode = 'all';
$mode = 'display';
$mode = 'dump';
//$mode = 'get';
//$mode = 'assignment';
//$mode = 'set';
//$mode = 'publish';
$mode = 'asset-tree';
//$mode = 'raw';
//$mode = 'delete';
//$mode = 'none';

try
{
    $s = $cascade->getAsset( a\Site::TYPE, "formats" );
    
    switch( $mode )
    {
        case 'all':
        case 'display':
            $s->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $s->dump();
            
            if( $mode != 'all' )
                break;
                
        case 'get':
            echo $service->getReadURL( a\Site::TYPE, "formats" ), BR;
        
            echo $s->getBaseFolderId(), BR;
            
            echo $s->getDefaultMetadataSetId(), BR;
            echo $s->getDefaultMetadataSetPath(), BR;
            echo u\StringUtility::boolToString( $s->getExternalLinkCheckOnPublish() ), BR;
            echo u\StringUtility::boolToString( $s->getLinkCheckerEnabled() ), BR;
            echo $s->getRecycleBinExpiration(), BR;
                
            echo $s->getRootAssetFactoryContainerId(), BR;
            echo $s->getRootConnectorContainerId(), BR;
            echo $s->getRootContentTypeContainerId(), BR;
            echo $s->getRootDataDefinitionContainerId(), BR;
            echo $s->getRootFolderId(), BR;
            echo $s->getRootMetadataSetContainerId(), BR;
            echo $s->getRootPageConfigurationSetContainerId(), BR;
            echo $s->getRootPublishSetContainerId(), BR;
            echo $s->getRootSiteDestinationContainerId(), BR;
            echo $s->getRootTransportContainerId(), BR;
            echo $s->getRootWorkflowDefinitionContainerId(), BR;
            echo $s->getSiteAssetFactoryContainerId(), BR;
            echo $s->getSiteAssetFactoryContainerPath(), BR;
            
            echo u\StringUtility::getCoalescedString( $s->getSiteStartingPageId() ), BR;
            echo u\StringUtility::getCoalescedString( $s->getSiteStartingPagePath() ), BR;
            echo u\StringUtility::boolToString( $s->getSiteStartingPageRecycled() ), BR;
            echo u\StringUtility::boolToString( $s->getUnpublishOnExpiration() ), BR;
            echo $s->getUrl(), BR;
            echo u\StringUtility::boolToString( $s->hasRole(
                $cascade->getAsset( a\Role::TYPE, 5 ) ) ), BR;
            
            echo u\StringUtility::getCoalescedString(
                $s->getScheduledPublishDestinationMode() ), BR;
            echo u\StringUtility::getCoalescedString(
                $s->getScheduledPublishDestinations() ), BR;

            echo "Inherit naming rules: ", 
                u\StringUtility::boolToString( $s->getInheritNamingRules() ), BR;
            u\DebugUtility::dump( $s->getNamingRuleAssets() );
            u\DebugUtility::dump( $s->getNamingRuleCase() );
            u\DebugUtility::dump( $s->getNamingRuleSpacing() );
                
            if( $mode != 'all' )
                break;
            
        case 'assignment':
            $r = $cascade->getAsset( a\Role::TYPE, 50 ); // site publisher
            $s->addRole( $r )->edit();
        
            $s->addUserToRole( 
                $r, $cascade->getAsset( a\User::TYPE, 'chanw' ) )->
                addUserToRole( 
                    $r, $cascade->getAsset( a\User::TYPE, 'tuw' ) )->
                addGroupToRole( 
                    $r, $cascade->getAsset( a\Group::TYPE, 'cru' ) )->
                edit();
        
            $s->removeRole( $r )->edit();
        /*/
        /*/
             u\DebugUtility::dump( $s->getRoleAssignments() );
                
            if( $mode != 'all' )
                break;
                
        case 'set':
            $s->
                // URL
                setUrl( 'http://www.upstate.edu/tuw-test' )->
                // metadata set
                setDefaultMetadataSet( 
                    $cascade->getAsset( a\MetadataSet::TYPE,
                        '618861c68b7ffe8377b637e863b6a785' ) )->
                // send report on error
                setSendReportOnErrorOnly( true )->
                // add user to send report
                addUserToSendReport( a\Asset::getAsset( 
                    $service, a\User::TYPE, 'wing' ) )->
                // add group to send report
                addGroupToSendReport( a\Asset::getAsset( 
                    $service, a\Group::TYPE, 'Administrators' ) )->
                // expiration
                setRecycleBinExpiration( a\Site::NEVER )->
                setExternalLinkCheckOnPublish( true )->
                setLinkCheckerEnabled( false )->
                setUnpublishOnExpiration( true )->
                //setInheritNamingRules( true )->
                setNamingRuleCase( a\Site::LOWER_CASE )->
                setNamingRuleSpacing( a\Site::HYPHEN_SPACE )->
                setStartingPage(
                    $cascade->getAsset( a\Page::TYPE,
                        'c12eb9978b7ffe83129ed6d80132aa29' )
                )->       
                edit()->dump();
                
            if( $mode != 'all' )
                break;
                
        case 'publish':
            //$s->publish();
                
            if( $mode != 'all' )
                break;
        
        case 'asset-tree':
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootAssetFactoryContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootConnectorContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootContentTypeContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootDataDefinitionContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootMetadataSetContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootPageConfigurationSetContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootPublishSetContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootSiteDestinationContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootTransportContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getRootWorkflowDefinitionContainerAssetTree()->
                toXml() ) );
            u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
                $s->getSiteAssetFactoryContainerAssetTree()->
                toXml() ) );
                
            if( $mode != 'all' )
                break;
        
        case 'raw':
            $s_std = $service->retrieve( $service->createId( 
                c\T::SITE, $id ), c\P::SITE );
/*          
            if( $s_std->timeToPublish == NULL )
                unset( $s_std->timeToPublish );
            else if( strpos( $s_std->timeToPublish, '-' ) !== false )
            {
                $pos = strpos( $s_std->timeToPublish, '-' );
                $s_std->timeToPublish = substr( $s_std->timeToPublish, 0, $pos );
            }
            
            if( $s_std->publishIntervalHours == NULL )
                unset( $s_std->publishIntervalHours );
                
            if( $s_std->publishDaysOfWeek == NULL )
                unset( $s_std->publishDaysOfWeek );
          
            $asset->site = $s_std;
            $service->edit( $asset );
            
            if( !$service->isSuccessful() )
            {
                echo "Failed to edit." . $service->getMessage() . BR;
            }
*/ 

            echo S_PRE;
            var_dump( $s_std );
            echo E_PRE;

            if( $mode != 'all' )
                break;
                
        case 'delete':
            $site = $cascade->getSite( "new_test" );
            $cascade->deleteAsset( $site );
            
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