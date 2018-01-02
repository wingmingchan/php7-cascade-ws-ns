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
$mode = 'copy';
$mode = 'delete';
//$mode = 'raw';
//$mode = 'none';
$mode = 'workflow';

try
{
    // velocity-test base folder
    //$f = $cascade->getAsset( 
        //a\Folder::TYPE, "a14dbeee8b7ffe830539acf038e9b57a" )->dump();
    //u\DebugUtility::dump( $f->getFolderChildrenIds() );

    // base folder
    $id = 'c12d8d0d8b7ffe83129ed6d86dd9f853';
    // test-child-folder
    //$id = '211c50a78b7f08560139425cdddf003a';
    // reports
    //$id = '5b9f96508b7f0856002a5e1165b08427';
    // test-Folder
    //$id = 'b8bf838f8b7f0856002a5e11586fba90';
    
    $f = $cascade->getAsset( a\Folder::TYPE, $id );

    switch( $mode )
    {
        case 'all':
        case 'display':
            $f->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $f->dump( true );
            
            if( $mode != 'all' )
                break;

        case 'get':
            /* === methods from Asset == */
            echo c\L::ID .            $f->getId() .           BR .
                 c\L::NAME .          $f->getName() .         BR .
                 c\L::PATH .          $f->getPath() .         BR .
                 c\L::SITE_ID .       $f->getSiteId() .       BR .
                 c\L::SITE_NAME .     $f->getSiteName() .     BR .
                 c\L::TYPE .          $f->getType() .         BR .
                 c\L::PROPERTY_NAME . $f->getPropertyName() . HR;
                 
            echo "Dumping identifier: ", BR;     
            u\DebugUtility::dump( $f->getIdentifier() );
            
            echo "Dumping property: ", BR;     
            u\DebugUtility::dump( $f->getProperty() );
            
            /* === methods from Container == */
            $children = $f->getChildren();
            
            foreach( $children as $child )
            {
                u\DebugUtility::dump( $child->toStdClass() );
            }
            
            $folder_children_ids = $f->getFolderChildrenIds();
            echo "Number of folder children: " . 
                count( $folder_children_ids ) . BR;
            
            foreach( $folder_children_ids as $folder_id )
            {
                u\DebugUtility::dump( $folder_id );
            }
            
            echo c\L::PARENT_CONTAINER_ID . $f->getParentFolderId() . BR .
                 c\L::PARENT_CONTAINER_PATH . $f->getParentFolderPath() . BR;
            
            echo HR;
        
            /* === methods from Folder == */
            echo c\L::CREATED_BY .   $f->getCreatedBy() .   BR .
                 c\L::CREATED_DATE . $f->getCreatedDate() . BR .
                 c\L::EXPIRATION_FOLDER_ID . u\StringUtility::getCoalescedString(
                     $f->getExpirationFolderId() ) . BR .
                 c\L::EXPIRATION_FOLDER_PATH . u\StringUtility::getCoalescedString(
                     $f->getExpirationFolderPath() ) . BR .
                 c\L::EXPIRATION_FOLDER_RECYCLED . u\StringUtility::boolToString(
                     $f->getExpirationFolderRecycled() ) . BR .
                 c\L::LAST_MODIFIED_BY . u\StringUtility::getCoalescedString(
                     $f->getLastModifiedBy() ) .   BR .
                 c\L::LAST_MODIFIED_DATE . u\StringUtility::getCoalescedString(
                     $f->getLastModifiedDate() ) . BR .
                 c\L::LAST_PUBLISHED_BY .   u\StringUtility::getCoalescedString(
                     $f->getLastPublishedBy() ) .   BR .
                 c\L::LAST_PUBLISHED_DATE . u\StringUtility::getCoalescedString(
                     $f->getLastPublishedDate() ) . BR .
                 c\L::METADATA_SET_ID .   $f->getMetadataSetId() .   BR .
                 c\L::METADATA_SET_PATH . $f->getMetadataSetPath() . BR .
                 c\L::SHOULD_BE_INDEXED . u\StringUtility::boolToString(
                     $f->getShouldBeIndexed() ) . BR .
                 c\L::SHOULD_BE_PUBLISHED . u\StringUtility::boolToString(
                     $f->getShouldBePublished() ) . BR .
                 HR;
                 
            echo "Parent folder ID: ", u\StringUtility::getCoalescedString(
                $f->getParentFolderId() ), BR;
            echo "Parent folder path: ", u\StringUtility::getCoalescedString(
                $f->getParentFolderPath() ), BR;
            echo "Is folder publishable: ", u\StringUtility::boolToString(
                $f->isPublishable() ), BR;
                 
            u\DebugUtility::dump( $f->getFolderChildrenIds() );    
            u\DebugUtility::dump( $f->getMetadata()->toStdClass() );
            u\DebugUtility::dump( $f->getMetadataStdClass() );
            
            $f->getMetadataSet()->dump();    
                
            $field_name = 'exclude-from-left';
            echo "Dumping dynamic field $field_name:";
            
            if( $f->hasDynamicField( $field_name ) )
                u\DebugUtility::dump( $f->getDynamicField( $field_name ) );
            else 
        		echo BR;
            
            echo "Dumping dynamic fields:", BR;
            u\DebugUtility::dump( $f->getDynamicFields() );
        
            if( $mode != 'all' )
                break;

        case 'set':
            $f->setShouldBeIndexed( true )->setShouldBePublished( true )->
                edit()->dump();
        
            if( $mode != 'all' )
                break;
            
        case 'copy':
        	$folder     = $cascade->getAsset( 
                c\T::FOLDER, "c12dd0308b7ffe83129ed6d8ad15e33c" );
            $parent     = $cascade->getAsset( 
                c\T::FOLDER, $folder->getParentFolderId() );
            // create a sibling by copying   
            $new_folder = $folder->copy( $parent, 'test-folder2' );
            $new_folder->display();
        
            if( $mode != 'all' )
                break;
            
        case 'delete':
            $temp_folder = a\Folder::getAsset( 
                $service, c\T::FOLDER, 'b70a87c38b7ffe8353cc17e9fe08ff77' );
            $temp_folder->deleteAllChildren();
            
            if( $mode != 'all' )
                break;
            
        case 'workflow':
            $f  = $cascade->getAsset(
                a\Folder::TYPE, "b70a87c38b7ffe8353cc17e9fe08ff77"
            );
            
            $wf_settings = $f->getWorkflowSettings();
            //u\DebugUtility::dump( $wf_settings );
            
            u\DebugUtility::dump( $wf_settings->toStdClass() );
            
            $wd = $cascade->getAsset(
                a\WorkflowDefinition::TYPE, "9fe9a65e8b7ffe83164c9314b8a987d9"
            );
            
            u\DebugUtility::out( u\StringUtility::boolToString(
                $f->hasWorkflowDefinition( $wd ) ) );
                
            if( !$f->hasWorkflowDefinition( $wd ) )
            {
                $f->addWorkflow( $wd )->editWorkflowSettings( true, true );
            }
            //u\DebugUtility::dump( $ws );

            if( $mode != 'all' )
                break;

        case 'raw':
            $folder = $service->retrieve( 
                $service->createId( c\T::FOLDER, $id), c\P::FOLDER );

            echo S_PRE;
            var_dump( $folder );
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