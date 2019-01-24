<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 3/30/2018 Added group-related functions.
  * 2/6/2018 Added global functions used by $admin
  * 1/27/2017 Added assetTreeSearchForNeedleInHaystack.
 */
use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
 
// global functions for AssetTree

function assetTreeAssociateWithMetadataSet( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    // get the child type
    $type = $child->getType();

    // the metadata set must be supplied
    if( !is_array( $params ) || !isset( $params[ $type ][ a\MetadataSet::TYPE ] ) )
        throw new e\NullAssetException( 
            S_SPAN . "The metadata set must be supplied for $type" . E_SPAN );
    // retrieve the metadata set
    $ms = $params[ $type ][ a\MetadataSet::TYPE ];
    // associate metadata set with asset
    $child->getAsset( $service )->setMetadataSet( $ms );        
}

function assetTreeCopyAFGroupAccess(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( !isset( $params[ 'old-group' ] ) )
        throw new \Exception( "The old group is not included" );
    if( !isset( $params[ 'new-group' ] ) )
        throw new \Exception( "The new group is not included" );
    
    $old_group = $params[ 'old-group' ];
    $new_group = $params[ 'new-group' ];
    
    $type = $child->getType();
    
    if( $type != a\AssetFactoryContainer::TYPE && 
        $type != a\AssetFactory::TYPE )
        return;
        
    $asset = $child->getAsset( $service );
        
    if( $asset->isApplicableToGroup( $old_group ) )
    {
        $asset->addGroup( $new_group )->edit();
    }
}

function assetTreeCopyGroupReadAccess(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( !isset( $params[ 'cascade' ] ) )
        throw new \Exception( "The Cascade object is not included" );
    if( !isset( $params[ 'old-group' ] ) )
        throw new \Exception( "The old group is not included" );
    if( !isset( $params[ 'new-group' ] ) )
        throw new \Exception( "The new group is not included" );
    
    $cascade   = $params[ 'cascade' ];
    $old_group = $params[ 'old-group' ];
    $new_group = $params[ 'new-group' ];
    
    $type = $child->getType();
    
    if( $type != a\DataBlock::TYPE && 
        $type != a\FeedBlock::TYPE &&
        $type != a\TextBlock::TYPE &&
        $type != a\IndexBlock::TYPE &&
        $type != a\XmlBlock::TYPE &&
        $type != a\File::TYPE &&
        $type != a\Folder::TYPE &&
        $type != a\Reference::TYPE &&
        $type != a\Symlink::TYPE &&
        $type != a\Page::TYPE )
        return;
        
    $id = $child->getId();
    $ari = $cascade->getAccessRights( $type, $id );
    
    if( $ari->hasGroup( $old_group ) &&
        $ari->getGroupLevel( $old_group ) == c\T::READ )
    {
        $ari->addGroupWriteAccess( $new_group );
        $cascade->setAccessRights( $ari, false );
    }
}

function assetTreeCopyGroupWriteAccess(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( !isset( $params[ 'cascade' ] ) )
        throw new \Exception( "The Cascade object is not included" );
    if( !isset( $params[ 'old-group' ] ) )
        throw new \Exception( "The old group is not included" );
    if( !isset( $params[ 'new-group' ] ) )
        throw new \Exception( "The new group is not included" );
    
    $cascade   = $params[ 'cascade' ];
    $old_group = $params[ 'old-group' ];
    $new_group = $params[ 'new-group' ];
    
    $type = $child->getType();
    
    if( $type != a\DataBlock::TYPE && 
        $type != a\FeedBlock::TYPE &&
        $type != a\TextBlock::TYPE &&
        $type != a\IndexBlock::TYPE &&
        $type != a\XmlBlock::TYPE &&
        $type != a\File::TYPE &&
        $type != a\Folder::TYPE &&
        $type != a\Reference::TYPE &&
        $type != a\Symlink::TYPE &&
        $type != a\Page::TYPE )
        return;
        
    $id = $child->getId();
    $ari = $cascade->getAccessRights( $type, $id );
    
    if( $ari->hasGroup( $old_group ) && 
        $ari->getGroupLevel( $old_group ) == c\T::WRITE )
    {
        $ari->addGroupWriteAccess( $new_group );
        $cascade->setAccessRights( $ari, false );
    }
}

function assetTreeCount(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    $type = $child->getType();
    
    if( !isset( $results[ c\F::COUNT ][ $type ] ) )
        $results[ c\F::COUNT ][ $type ] = 1;
    else
        $results[ c\F::COUNT ][ $type ] = $results[ c\F::COUNT ][ $type ] + 1;
}

function assetTreeDisplay( aohs\AssetOperationHandlerService $service, p\Child $child )
{
    $child->display();
}

function assetTreeGetAssets( aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
{
    if( is_array( $results ) )
    {
        $results[ c\F::GET_ASSETS ][ $child->getType() ][] = $child->getAsset( $service );
    }
}

function assetTreePublish( aohs\AssetOperationHandlerService $service, p\Child $child )
{
    $service->publish( $child->toStdClass() );
}

function assetTreeRemoveAsset( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( is_array( $results ) &&
        is_array( $results[ c\F::REPORT_ORPHANS ] ) &&
        in_array( 
            $child->getPathPath(), $results[ c\F::REPORT_ORPHANS ][ $child->getType() ] ) )
    {
        if( isset( $params[ c\F::REMOVE_ASSET ][ c\F::UNCONDITIONAL_REMOVAL ] ) &&
            $params[ c\F::REMOVE_ASSET ][ c\F::UNCONDITIONAL_REMOVAL ] == true )
        {
            $service->delete( $child->toStdClass() );
        }
        // if the id and path are NOT found in the array
        else if( 
            !in_array( 
                $child->getId(), $params[ c\F::REMOVE_ASSET ][ $child->getType() ] ) && 
            !in_array( 
                $child->getPathPath(), $params[ c\F::REMOVE_ASSET ][ $child->getType() ] )
        )
        {
            $service->delete( $child->toStdClass() );
        }
    }
}

function assetTreeRemovePhantomNodes(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    // get the child type
    $type = $child->getType();
    
    // the results array is optional
    if( isset( $results ) )
    {
        if( !isset( $results[ $type ] ) )
        {
            $results[ $type ] = array();
            $results[ $type ][ "A" ] = array();
            $results[ $type ][ "B" ] = array();
        }
    }

    if( $type != a\Page::TYPE && $type != a\DataDefinitionBlock::TYPE )
    {
        return;
    }
    
    try
    {
        $asset = $child->getAsset( $service );
        
        // type B
        if( $asset->hasPhantomNodes() )
        {
            $asset->removePhantomNodes( $results );
        }
    }
    catch( e\WrongBlockTypeException $e )
    {
        // do nothing
    }
    catch( e\WrongPageTypeException $e )
    {
        // do nothing
    }
    // type A
    catch( e\NoSuchFieldException $e )
    {
        switch( $type )
        {
            case a\DataDefinitionBlock::TYPE:
                $asset = new a\DataDefinitionBlockPhantom(
                    $service, $child->toStdClass() );
                break;
            case a\Page::TYPE:
                $asset = new a\PagePhantom(
                    $service, $child->toStdClass() );
                break;
        }
        $dd           = $asset->getDataDefinition();
        $healthy_sd   = new p\StructuredData(
            $dd->getStructuredData(), $service, $dd->getId() );
        $phantom_sd   = $asset->getStructuredDataPhantom();
        $healthy_sd   = $healthy_sd->removePhantomNodes( $phantom_sd );
        $asset->setStructuredData( $healthy_sd );
        
        if( isset( $results ) )
        {
            $results[ $type ][ "A" ][] = $child->getPathPath();
        }
    }
}

function assetTreeRemovePhantomValues(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    // get the child type
    $type = $child->getType();
    
    // the results array is optional
    if( isset( $results ) )
    {
        if( !isset( $results[ $type ] ) )
        {
            $results[ $type ] = array();
        }
    }

    if( $type != a\Page::TYPE && $type != a\DataDefinitionBlock::TYPE )
    {
        return;
    }
    
    try
    {
        $child->getAsset( $service )->removePhantomValues( $results );
    }
    catch( e\WrongBlockTypeException $e )
    {
        // do nothing
    }
    catch( e\WrongPageTypeException $e )
    {
        // do nothing
    }
}

function assetTreeReportAssetFactoryGroupAssignment( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( $child->getType() != a\AssetFactory::TYPE )
    {
        throw new e\WrongAssetTypeException( 
            "The asset tree does not contain asset factories." );
    }

    if( isset( $params[ c\F::REPORT_FACTORY_GROUP ][ 'site-name' ] ) 
        && trim( $params[ c\F::REPORT_FACTORY_GROUP ][ 'site-name' ] ) != ""
        && is_array( $results ) )
    {
        $site_name = trim( $params[ c\F::REPORT_FACTORY_GROUP ][ 'site-name' ] );
        
        if( !isset( $results[ c\F::REPORT_FACTORY_GROUP ][ $site_name ] ) )
        {
            $results[ c\F::REPORT_FACTORY_GROUP ][ $site_name ] = array();
        }
        
        $af     = $child->getAsset( $service );
        $groups = $af->getApplicableGroups();
        
        $results[ c\F::REPORT_FACTORY_GROUP ][ $site_name ][ $af->getName() ] = $groups;
    }
}

function assetTreeReportDataDefinitionFlag(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( isset( $params[ c\F::REPORT_DATA_DEFINITION_FLAG ]
            [ $child->getType() ] ) &&
        is_array( $params[ c\F::REPORT_DATA_DEFINITION_FLAG ]
            [ $child->getType() ] ) )
    {
        // only one value per dynamic field
        $identifier_text_array = $params[ c\F::REPORT_DATA_DEFINITION_FLAG ]
            [ $child->getType() ];
        
        if( !isset( $results[ c\F::REPORT_DATA_DEFINITION_FLAG ]
            [ $child->getType() ] ) )
        {
            $results[ c\F::REPORT_DATA_DEFINITION_FLAG ]
                [ $child->getType() ] = array();
        }
        
        foreach( $identifier_text_array as $identifier => $text )
        {
            $asset = $child->getAsset( $service );
            
            if( $asset->hasStructuredData() &&
                $asset->hasIdentifier( $identifier ) && 
                $text == $asset->getText( $identifier ) )
            {
                $results[ c\F::REPORT_DATA_DEFINITION_FLAG ]
                    [ $child->getType() ][] = $child;
            }
        }
    }
}

function assetTreeReportMetadataFlag(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( isset( $params[ c\F::REPORT_METADATA_FLAG ][ $child->getType() ] ) &&
        is_array( $params[ c\F::REPORT_METADATA_FLAG ][ $child->getType() ] ) )
    {
        // only one value per dynamic field
        $name_value_array = $params[ c\F::REPORT_METADATA_FLAG ]
            [ $child->getType() ];
        
        if( !isset( $results[ c\F::REPORT_METADATA_FLAG ][ $child->getType() ] ) )
        {
            $results[ c\F::REPORT_METADATA_FLAG ][ $child->getType() ] = array();
        }
        
        foreach( $name_value_array as $field => $value )
        {
            $asset = $child->getAsset( $service );
            
            if( $asset->hasDynamicField( $field )
                && 
                in_array( $value, $asset->getMetadata()->
                    getDynamicFieldValues( $field ) ) )
            {
                $results[ c\F::REPORT_METADATA_FLAG ][ $child->getType() ][] = 
                    $child;
            }
        }
    }
}

function assetTreeReportNumberOfTemplates(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( !isset( $params[ 'cache' ] ) )
        throw new e\ReportException( c\M::NULL_CACHE );
        
    // set up cache
    $cache = $params[ 'cache' ];

    // get type of asset
    $type = $child->getType();
    
    if( $type != a\Template::TYPE )
        return;
    
    if( !isset( $results[ $type ][ 'number' ] ) )
        $results[ $type ][ 'number' ] = 0;

    $results[ $type ][ 'number' ]++;
}

function assetTreeReportOrphans( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( is_array( $results ) )
    {
        $subscribers = $child->getAsset( $service )->getSubscribers();
        
        if( $subscribers == NULL )
        {
            $results[ c\F::REPORT_ORPHANS ][ $child->getType() ][] =
                $child->getPathPath();
        }
    }
}

function assetTreeReportPageWithPageLevelBlockFormat(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    // only works for pages
    if( $child->getType() != a\Page::TYPE )
    {
        return;
    }

    if( !isset( $results[ c\F::REPORT_PAGE_LEVEL ] ) )
    {
        $results[ c\F::REPORT_PAGE_LEVEL ] = array(); // 175
    }
    
    $page  = $child->getAsset( $service );
    $array = $page->getPageLevelRegionBlockFormat();

    if( !empty( $array )  )
    {
        $results[ c\F::REPORT_PAGE_LEVEL ][ $child->getId() ] = $child->getPathPath();
    }
}

function assetTreeReportPhantomNodes( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, array $params=NULL, array &$results=NULL )
{
    if( !isset( $results ) || !is_array( $results ) )
        throw new \Exception( "The results array is required" );
        
    $type = $child->getType();
    
    if( !isset( $results[ $type ] ) )
    {
        $results[ $type ] = array( "A" => array(), "B" => array() );
    }
    
    if( $type != a\Page::TYPE && $type != a\DataDefinitionBlock::TYPE )
        return;
    
    try
    {
        $asset = $child->getAsset( $service );
        
        if( $asset->hasPhantomNodes() )
        {
            $results[ $type ][ "B" ][] = $child->getPathPath();
        }
    }
    // XHTML block
    catch( e\WrongBlockTypeException $e )
    {
        // do nothing
    }
    // XHTML page
    catch( e\WrongPageTypeException $e )
    {
        // do nothing
    }
    catch( e\NoSuchFieldException $e )
    {
        $results[ $type ][ "A" ][] = $child->getPathPath();
    }
}

function assetTreeReportPhantomValues(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( !isset( $results ) || !is_array( $results ) )
        throw new \Exception( "The results array is required" );
        
    $type = $child->getType();
    
    if( !isset( $results[ $type ] ) )
    {
        $results[ $type ] = array();
    }
    
    if( !isset( $results[ "phantom" ] ) )
    {
        $results[ "phantom" ] = array();
    }

    if( $type != a\Page::TYPE && $type != a\DataDefinitionBlock::TYPE )
    {
        return;
    }
    
    try
    {
        if( $child->getAsset( $service )->getStructuredData()->hasPhantomValues() )
        {
            $results[ $type ][] = $child->getPathPath();
        }
    }
    // XHTML block
    catch( e\WrongBlockTypeException $e )
    {
        // do nothing
    }
    // XHTML page
    catch( e\WrongPageTypeException $e )
    {
        // do nothing
    }
    // type A only
    catch( e\NoSuchFieldException $e )
    {
        // skip the asset
        $results[ "phantom" ][] = $child->getPathPath();
        return;    
    }
}

function assetTreeReportTemplateFormatPaths(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( !isset( $params[ 'cache' ] ) )
        throw new e\ReportException( c\M::NULL_CACHE );
        
    // set up cache
    $cache = $params[ 'cache' ];

    // get type of asset
    $type = $child->getType();
    
    if( $type != a\Template::TYPE )
        return;
        
    $path        = $child->getPathPath();
    $asset       = $cache->retrieveAsset( $child );
    $format_path = $asset->getFormatPath();
    
    if( $format_path )
        $results[ $type ][ $path ] = $format_path;
}
    
function assetTreeReportTemplatePaths(
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, array $params=NULL, array &$results=NULL )
{
    if( !isset( $params[ 'cache' ] ) )
        throw new e\ReportException( c\M::NULL_CACHE );
        
    // set up cache
    $cache = $params[ 'cache' ];

    // get type of asset
    $type = $child->getType();
    
    if( $type != a\Template::TYPE )
        return;
        
    $path = $child->getPathPath();
        
    $results[ $type ][] = $path;
}

function assetTreeSearchByName( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( is_array( $results ) )
    {
        $path = $child->getPathPath();
        $type = $child->getType();
        
        // no name supplied
        if( !isset( $params[ c\F::SEARCH_BY_NAME ][ $type ][ 'name' ] ) )
        {
            return;
        }
        else
        {
            $name = $params[ c\F::SEARCH_BY_NAME ][ $type ][ 'name' ];
        }

        if( !isset( $results[ c\F::SEARCH_BY_NAME ] ) )
        {
            $results[ c\F::SEARCH_BY_NAME ] = array(); //
            
            if( !isset( $results[ c\F::SEARCH_BY_NAME ][ $type ] ) )
                $results[ c\F::SEARCH_BY_NAME ][ $type ] = array();
        }
        
        // if name is found in asset name
        if( strpos( $path, $name ) !== false )
            $results[ c\F::SEARCH_BY_NAME ][ $type ][] = $path;
    }
}

function assetTreeSearchForString( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( !isset( $params[ "string" ] ) )
        throw new \Exception( "The search string is not supplied" );
    else
        $string = $params[ "string" ];

    if( is_array( $results ) )
    {
        $page = $child->getAsset( $service );
        $ids  = $page->searchText( $string );
                
        if( isset( $ids ) )
        {
            $results[] = $child->getPathPath();
        }
    }
}

function assetTreeSearchForNeedleInHaystack( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    $type     = $child->getType();
    $needle   = $params[ $type ][ 'needle' ];
    // the method call should be defined for the asset
    // and returns a string
    $method   = $params[ $type ][ 'method' ];
    $code     = "\$haystack = \$child->getAsset( \$service )->$method;";
    //echo $code, BR;
    eval( $code ); 
    
    if( strpos( $haystack, $needle ) !== false )
    {
        $results[ $type ][] = $child->getPathPath();
    }
}

function assetTreeStoreAssetPath( 
    aohs\AssetOperationHandlerService $service, 
    p\Child $child, $params=NULL, &$results=NULL )
{
    if( is_array( $results ) )
    {
        if( !isset( $results[ c\F::STORE_ASSET_PATH ] ) )
        {
            $results[ c\F::STORE_ASSET_PATH ] = array(); // 1597
        }

        $results[ c\F::STORE_ASSET_PATH ][] = $child->getPathPath();
    }
}
?>
