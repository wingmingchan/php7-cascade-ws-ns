<?php
require_once( 'auth_test.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

//$mode = 'all';
$mode = 'display';
$mode = 'dump';
$mode = 'get';
$mode = 'set';
$mode = 'raw';
//$mode = 'none';

try
{
    $id = "987203c88b7ffe8353cc17e9ed0bab6c"; // folder-index
    $ifb  = $cascade->getAsset( a\IndexBlock::TYPE, $id );
    
    switch( $mode )
    {
        case 'all':
        case 'display':
            $ifb->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $ifb->dump( true );
            
            if( $mode != 'all' )
                break;
                
        case 'get':
            echo c\L::ID . $ifb->getId() . BR .
                 "Index type: " . $ifb->getIndexBlockType() . BR .
                 "Append calling page data: " . 
                     u\StringUtility::boolToString( 
                         $ifb->getAppendCallingPageData() ) . BR .
                 "Depth of index: " . $ifb->getDepthOfIndex() . BR .
                 "Index access rights: " . 
                     u\StringUtility::boolToString( $ifb->getIndexAccessRights() ) . BR .
                 "Index blocks: " . u\StringUtility::boolToString(
                     $ifb->getIndexBlocks() ) . BR .
                 "Indexed content type ID: " . u\StringUtility::getCoalescedString(
                         $ifb->getIndexedContentTypeId() ) . BR .
                 "Indexed content type path: " . u\StringUtility::getCoalescedString(
                     $ifb->getIndexedContentTypePath() ) . BR .
                 "Indexed folder ID: " . u\StringUtility::getCoalescedString(
                     $ifb->getIndexedFolderId() ) . BR .
                 "Indexed folder path: " . u\StringUtility::getCoalescedString(
                     $ifb->getIndexedFolderPath() ) . BR .
                 "Indexed folder recycled: " . u\StringUtility::boolToString(
                     $ifb->getIndexedFolderRecycled() ) . BR .
                 "Index files: " . u\StringUtility::boolToString(
                     $ifb->getIndexFiles() ) . BR .
                 "Index links: " . u\StringUtility::boolToString(
                     $ifb->getIndexLinks() ) . BR .
                 "Index pages: " . u\StringUtility::boolToString(
                     $ifb->getIndexPages() ) . BR .
                 "Index regular content: " . u\StringUtility::boolToString(
                     $ifb->getIndexRegularContent() ) . BR .
                 "Index system metadata: " . u\StringUtility::boolToString(
                     $ifb->getIndexSystemMetadata() ) . BR .
                 "Index user info: " . u\StringUtility::boolToString(
                     $ifb->getIndexUserInfo() ) . BR .
                 "Index user metadata: " . u\StringUtility::boolToString(
                     $ifb->getIndexUserMetadata() ) . BR .
                 "Index workflow info: " . u\StringUtility::boolToString(
                     $ifb->getIndexWorkflowInfo() ) . BR .
                 "Max rendered assets: " . $ifb->getMaxRenderedAssets() . BR .
                 "Page xml: " . $ifb->getPageXML() . BR .
                 "Rendering behavior: " . $ifb->getRenderingBehavior() . BR .
                 "Sort method: " . $ifb->getSortMethod() . BR .
                 "Sort order: " . $ifb->getSortOrder() . BR .
                 "Is content: " . u\StringUtility::boolToString(
                     $ifb->isContent() ) . BR .
                 "Is folder: " . u\StringUtility::boolToString(
                     $ifb->isFolder() ) . BR;
                     
            if( $ifb->getFolder() )
                $ifb->getFolder()->dump();

            if( $mode != 'all' )
                break;
             
        case 'set':
            
            $fid = 'c12dce028b7ffe83129ed6d8fdc88b47';
            $folder = $cascade->getAsset( a\Folder::TYPE, $fid );
            
            $ifb->
                setAppendCallingPageData( true )->
                setDepthOfIndex( 3 )->
                setFolder( $folder )->
                setIndexAccessRights( true )->
                setIndexBlocks( true )->
                setIndexFiles( true )->
                setIndexLinks( true )->
                setIndexPages( true )->
                setIndexRegularContent( true )->
                setIndexSystemMetadata( true )->
                setIndexUserInfo( true )->
                setIndexUserMetadata( true )->
                setIndexWorkflowInfo( true )->
                setPageXML( c\T::RENDERCURRENTPAGEONLY )->
                setMaxRenderedAssets( 50 )->
                setRenderingBehavior( c\T::HIERARCHY )->
                setSortMethod( c\T::LASTMODIFIEDDATE )->
                setSortOrder( c\T::DESCENDING )->
                
                edit()->dump( true );
        
            if( $mode != 'all' )
                break;
                
        case 'raw':
            $ifb = $service->retrieve( $service->createId( 
                c\T::INDEXBLOCK, $id ), c\P::INDEXBLOCK );

            u\DebugUtility::dump( $ifb );
        
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