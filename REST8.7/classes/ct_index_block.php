<?php
require_once('auth_test.php');

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$mode = 'all';
//$mode = 'display';
//$mode = 'dump';
//$mode = 'get';
//$mode = 'set';
//$mode = 'raw';

try
{
    $id  = "9898cfde8b7ffe8353cc17e9e30309f5"; // test-ct-index
    $icb = $cascade->getAsset( a\IndexBlock::TYPE, $id );
    
    if( $icb->isContent() ) echo "Type: " . c\T::CONTENTTYPEINDEX . BR;
    
    switch( $mode )
    {
        case 'all':
        case 'display':
            $icb->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $icb->dump();
            $icb->dumpJSON();
                        
            if( $mode != 'all' )
                break;
                
        case 'get':
            echo "ID: " . $icb->getId() . BR .
                 "Type: " . $icb->getIndexBlockType() . BR .
                 "Indexed folder recycled: " . u\StringUtility::boolToString(
                     $icb->getIndexedFolderRecycled() ) . BR .
                 "Index files: " . u\StringUtility::boolToString(
                     $icb->getIndexFiles() ) . BR .
                 "Page xml: " . $icb->getPageXML() . BR .
                     "";
            $icb->getContentType()->dump();

            if( $mode != 'all' )
                break;
             
        case 'set':
            $icb->
                setContentType( 
                    $cascade->getAsset( 
                        a\ContentType::TYPE, 
                        '7e0d51968b7f0856015997e42c3ede8e' ) )->
                setAppendCallingPageData( true )->
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
                edit()->dump();
        
            if( $mode != 'all' )
                break;
                
        case 'raw':
            $icb = $service->retrieve( $service->createId( 
                c\T::INDEXBLOCK, $id ), c\P::INDEXBLOCK );
            echo S_PRE;
            var_dump( $icb );
            echo E_PRE;
        
            if( $mode != 'all' )
                break;
    }
}
catch( \Exception $e )
{
    echo S_PRE. $e . E_PRE;
}
catch( \Error $er ) 
{
    echo S_PRE . $er . E_PRE; 
} 
?>