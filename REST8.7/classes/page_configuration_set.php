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
$mode = 'set';
//$mode = 'raw';
//$mode = 'xml';
$mode = 'add';
//$mode = 'delete';

try
{
    //$id = "d7b67e638b7f085600a0fcdc2ef6d531"; // 3 Column
    //$id = "fc51bcda8b7f085600406eac9dc67ed8"; // 3 Column Test 2
    
    $id = "493099ca8b7ffe83164c9314be6194c2"; // RWD old
    $pcs = $cascade->getAsset( a\PageConfigurationSet::TYPE, $id );
    
    switch( $mode )
    {
        case 'all':
        case 'display':
            $pcs->display();
            
            if( $mode != 'all' )
                break;
                
        case 'add':
            $pcs->addConfiguration( 
                'XML', // name
                $cascade->getAsset( a\Template::TYPE, '348c79d18b7ffe83164c9314043282e3' ),
                '.xml',
                c\T::XML
            );
            
            if( $mode != 'all' )
                break;
                
        case 'delete':
            $pcs->deletePageConfiguration( 'XML' )->dump();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $pcs->dump();
            
            if( $mode != 'all' )
                break;
                
        case 'get':
            echo "ID: " . $pcs->getId() . BR;
            
            u\DebugUtility::dump( $pcs->getPageConfigurationNames() );
            
            $default_config =  $pcs->getDefaultConfiguration();
/*/            
            echo u\StringUtility::boolToString( 
                $pcs->getIncludeXMLDeclaration( "PDF" ) ), BR;
            
            echo u\StringUtility::boolToString(
                $pcs->getOutputExtension( "PDF" ) ), BR;
/*/   
            u\DebugUtility::dump( $pcs->getPageConfiguration( 
                $default_config->getName() )->
                getPageRegionNames() );
            
            if( $pcs->getPublishable( $default_config->getName() ) )
            {
                echo "The default config is publishable" . BR;
            }
            else
            {
                echo "The default config is not publishable" . BR;
            }
            
            u\DebugUtility::dump( $pcs->getPageConfigurations() );
            $pcs->getPageConfigurationTemplate( "XML" )->dump();
            u\DebugUtility::dump( $pcs->getPageRegionNames( "XML" ) );
            u\DebugUtility::dump( $pcs->getPageRegion( "Print", "DEFAULT" ) );
            echo $pcs->getSerializationType( "Print" ), BR;
            
            echo u\StringUtility::boolToString( $pcs->hasPageConfiguration( "XML" ) ), BR;
            echo u\StringUtility::boolToString( $pcs->hasPageRegion( "Print", "DEFAULT" ) ), BR;
                 
            if( $mode != 'all' )
                break;
                
        case 'set':
            //$cascade->getAsset( a\XsltFormat::TYPE, "9fea17498b7ffe83164c931447df1bfb" )->dump();

            $pcs->setConfigurationPageRegionBlock( 'Print', 'DEFAULT' )->edit();
                
            $pcs->setConfigurationPageRegionFormat( 'Print', 'DEFAULT',
                    $cascade->getAsset( 
                        a\XsltFormat::TYPE, 
                        '9fea17498b7ffe83164c931447df1bfb' )
                )->edit();
  
            //$pcs->setDefaultConfiguration( "Print" )->edit();
            $pcs->setFormat( "Print",
                $cascade->getAsset( 
                    a\XsltFormat::TYPE, "9fea17498b7ffe83164c931447df1bfb" )
            )->edit();
            
            //$pcs->setIncludeXMLDeclaration( "Print", true )->edit();
            $pcs->setOutputExtension( "Print", ".html" )->
                setPublishable( "Print", false )->
                setSerializationType( "Print", "XML" )->
                edit();
              
            if( $mode != 'all' )
                break;

        case 'raw':
            $pcs = $service->retrieve( $service->createId( 
                c\T::CONFIGURATIONSET, $id ), c\P::CONFIGURATIONSET );
                
            //$pr = new PageRegion( $pcs->pageConfigurations->
                //pageConfiguration[3]->pageRegions->pageRegion[0] );
            //u\DebugUtility::dump( $pr );
            u\DebugUtility::dump( $pcs );
        
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