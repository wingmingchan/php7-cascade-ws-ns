<?php 
require_once('auth_test.php');

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$mode = 'none';
//$mode = 'all';
$mode = 'copy';
$mode = 'display';
$mode = 'dump';
$mode = 'edit';
$mode = 'getAudits';
$mode = 'get';
$mode = 'publishSubscribers';

try
{
    $page_id = "2c8216e48b7f08ee59cfffeb1a90e53f";
    
    // test static method
    $page = a\Asset::getAsset(
        $service, a\Page::TYPE, $page_id ); //->dump();
    // static method can still be called through an object
    $folder = $page->getAsset(
        $service, a\Folder::TYPE, "9a1409a78b7f08ee5d439b313f5eb372" );
        
    switch( $mode )
    {
        case 'all':
        case 'copy':
            $page->copy(
                // the target folder
                $cascade->getAsset( 
                    a\Folder::TYPE, "9a1409a78b7f08ee5d439b313f5eb372" ), 
                "test-asset" // new name
            );
            
            if( $mode != 'all' )
                break;
        
        case 'display':
            $page->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $page->dump();
            
            if( $mode != 'all' )
                break;

        case 'edit':
            $page->setText( "main-group;wysiwyg", "Test content" )->
                edit();
            
            if( $mode != 'all' )
                break;

        case 'getAudits':
            //$audits = $page->getAudits();
            //$user   = $cascade->getAsset( a\User::TYPE, "chanw" );
            //$audits = $user->getAudits();
            //$group   = $cascade->getAsset( a\Group::TYPE, "22q" );
            //$audits = $group->getAudits();
            $role   = $cascade->getAsset( a\Role::TYPE, 5 );
            $audits = $role->getAudits();
            // not defined for Role
            u\DebugUtility::out( count( $audits ) );
            
            if( $mode != 'all' )
                break;

        case 'get':
            echo "Test get methods:", BR, 
                "getId: ", $page->getId(), BR;
            
            echo "getIdentifier:", BR;
            u\DebugUtility::dump( $page->getIdentifier() );
            
            // convert the stdClass identifier to an Identifier object
            $identifier = new p\Identifier( $page->getIdentifier(), $service );
            u\DebugUtility::dump( $identifier->toStdClass() );
            
            echo "getName: ", $page->getName(), BR,
                 "getPath: ", $page->getPath(), BR;
        
            echo "getProperty:", BR;
            u\DebugUtility::dump( $page->getProperty() );
            
            echo "getPropertyName:", $page->getPropertyName(), BR;
            
            echo "getService:", BR;
            u\DebugUtility::dump( $page->getService() );
            
            echo "getSiteId: ", $page->getSiteId(), BR,
                 "getSiteName: ", $page->getSiteName(), BR;

            echo "getSubscribers: ";
            $subscribers = $page->getSubscribers(); // array of Identifier objects
            
            echo "There are " . count( $subscribers ) . " subscribers.", BR;

            echo "getType: ", $page->getType(), BR;

            if( $mode != 'all' )
                break;
                
        case 'publishSubscribers':
        	$block = $cascade->getAsset(
        		a\XmlBlock::TYPE, "9e3b67348b7f08ee30f79ea3434996e4" );
            $block->publishSubscribers( 
                $cascade->getAsset(
                    a\Destination::TYPE, "c34b58ca8b7f08ee4fe76bb83ba1613b" ) 
            );
            
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