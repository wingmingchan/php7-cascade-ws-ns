<?php
require_once('auth_test.php');

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$mode = 'all';
$mode = 'attributes';
$mode = 'display';
$mode = 'dump';
$mode = 'get';
//$mode = 'set';
//$mode = 'raw';
//$mode = 'default';

try
{
    $dd = 
        $cascade->getAsset( a\DataDefinition::TYPE, '2329ad0b8b7f0856002a5e11a5cbe84d' );
   
    switch( $mode )
    {
        case 'all':
        case 'attributes':
            $dd->displayXML();
            $dd->displayAttributes();
            
            if( $mode != 'all' )
                break;

        case 'display':
            $dd->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $dd->dump();
            
            if( $mode != 'all' )
                break;

        case 'get':
/*/
            echo S_STRONG . "ID: " . E_STRONG . $dd->getId() . BR;
            echo S_STRONG . "Path: " . E_STRONG . $dd->getPath() . BR;
            echo S_STRONG . "Site name: " . E_STRONG . $dd->getSiteName() . BR;
            
            u\DebugUtility::dump( $dd->getIdentifiers() );
            
            $identifier = "group;group-single";
            
            if( $dd->hasIdentifier( $identifier ) )
            {
                u\DebugUtility::dump( $dd->getField( $identifier ) );
                
                echo ( $dd->isMultiple( $identifier ) ? 
                    'Multiple' : 'Not multiple' ) . BR;
            }
            
            $identifier = "group;group-multiple-first";
            
            if( $dd->hasIdentifier( $identifier ) )
            {
                u\DebugUtility::dump( $dd->getField( $identifier ) );
                
                echo ( $dd->isMultiple( $identifier ) ? 
                    'Multiple' : 'Not multiple' ) . BR;
            }
/*/            
            u\DebugUtility::dump( $dd->getStructuredData() );
            //u\DebugUtility::dump( $dd->getStructuredDataObject() );
                
            if( $mode != 'all' )
                break;
         
        case 'set':
            $xml = $dd->getXml();
            $dd->setXML( $xml )->edit();
            $dd->displayXML();
            if( $mode != 'all' )
                break;

        case 'default':
            $xml = $dd->display();
            
            if( $mode != 'all' )
                break;

        case 'raw':
            $block = $service->retrieve( 
                $service->createId( c\T::DATADEFINITIONBLOCK, 'd6a354728b7f085600adcd81fabd52bd' ),
                c\P::DATADEFINITIONBLOCK ); 
            echo S_PRE;
            //var_dump( $block );
            echo E_PRE;
            
            $block->structuredData->definitionId = '342cf48a8b7f08560139425c72f81faf';
            $block->structuredData->definitionPath = '_common:Upstate/Site/Test Group Text';
            $block->structuredData->structuredDataNodes = new \stdClass();
            
            $asset->xhtmlDataDefinitionBlock = $block;
            $service->edit( $asset );
/*            
            $block = a\Asset::getAsset( $service, c\T::DATADEFINITIONBLOCK, 'd5b44bc48b7f085600adcd8196623117' );
            $par_id = new \stdClass();
            $par_id->id = '980d67ab8b7f0856015997e4b8d84c5d';
            $par_id->type = c\T::FOLDER;
            $new_block = $block->copy( $par_id, 'my_brick10' );
*/            
            
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