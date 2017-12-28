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
//$mode = 'get';
//$mode = 'copy';
//$mode = 'is';
//$mode = 'metadata';
$mode = 'set';
//$mode = 'raw';
//$mode = 'none';

/*
array(18) {
  [0]=>
  string(14) "pre-main-group"
  [1]=>
  string(37) "pre-main-group;mul-pre-main-chooser;0"
  [2]=>
  string(10) "main-group"
  [3]=>
  string(31) "main-group;mul-pre-h1-chooser;0"
  [4]=>
  string(13) "main-group;h1"
  [5]=>
  string(32) "main-group;mul-post-h1-chooser;0"
  [6]=>
  string(58) "main-group;float-pre-content-blocks-around-wysiwyg-content"
  [7]=>
  string(18) "main-group;wysiwyg"
  [8]=>
  string(37) "main-group;mul-post-wysiwyg-chooser;0"
  [9]=>
  string(15) "post-main-group"
  [10]=>
  string(39) "post-main-group;mul-post-main-chooser;0"
  [11]=>
  string(9) "top-group"
  [12]=>
  string(33) "top-group;mul-top-group-chooser;0"
  [13]=>
  string(12) "bottom-group"
  [14]=>
  string(39) "bottom-group;mul-bottom-group-chooser;0"
  [15]=>
  string(11) "admin-group"
  [16]=>
  string(33) "admin-group;master-level-override"
  [17]=>
  string(31) "admin-group;page-level-override"
}
*/

try
{
    $id = "043e8eea8b7ffe83785cac8aa2081de7";
    $p  = $cascade->getAsset( a\Page::TYPE, $id );
    
    switch( $mode )
    {
        case 'all':
        case 'display':
            $p->display();
            $p->displayDataDefinition();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $p->dump();
            u\DebugUtility::dump( $p->getIdentifiers() );
            
            if( $mode != 'all' )
                break;
                
        case 'get':
            echo "Dumping the set of identifiers:" . BR;
            u\DebugUtility::dump( $p->getIdentifiers() );
            
            $node_name = "main-group;float-pre-content-blocks-around-wysiwyg-content";
            
            if( $p->hasNode( $node_name ) )
            {
                echo "The value is: " . $p->getText( $node_name ) . BR;
            }
            
            $node_name = "post-main-group;mul-post-main-chooser;0";
            
            if( $p->hasNode( $node_name ) )
            {
                $node_type = $p->getNodeType( $node_name );
                
                echo "The type is: " . $node_type . BR;
                
                if( $node_type == c\T::ASSET )
                {
                    $asset_type = $p->getAssetNodeType( $node_name );
                    echo "The asset type is: " . $asset_type . BR;
                }
            }
            
/**/            
            echo "Configuration set:" . BR;
            $p->getConfigurationSet()->display();
            
            echo $p->getConfigurationSet()->
                        getDefaultConfiguration()->getName() . BR;
            echo $p->getConfigurationSet()->getDefaultConfiguration()->
                    getOutputExtension() . BR;
            
            

            echo "Data definition:" . BR;
            $p->getDataDefinition()->display();
/*
            echo "Metadata set:" . BR;
            $p->getMetadataSet()->display();

            echo "Template of Desktop:" . BR;
            $t = $p->getConfigurationSet()
                ->getPageConfigurationTemplate( 'Desktop' )
                ->dump();
*/
            if( $mode != 'all' )
                break;
                
        case 'is':
            echo "Dumping the set of identifiers:" . BR;
            u\DebugUtility::dump( $p->getIdentifiers() );
            
            $node_name = "pre-main-group"; // group
            $node_name = "main-group;h1"; // normal text, no text type
            $node_name = "main-group;wysiwyg"; // WYSIWYG
            
            if( $p->isAssetNode( $node_name ) )
            {
                echo "This is an asset node" . BR;
                
                $asset_type = $p->getAssetNodeType( $node_name );
                echo "The asset type is: " . $asset_type . BR;
                
                if( $asset_type == c\T::BLOCK )
                {
                    echo "The block ID is: " . 
                        $p->getBlockId( $node_name ) . BR;
                }
            }
            else
            {
                echo "This is not a asset node" . BR;
            }
            
            if( $p->isGroupNode( $node_name ) )
            {
                echo "This is a group node" . BR;
            }
            else
            {
                echo "This is not a group node" . BR;
            }
            
            if( $p->isTextNode( $node_name ) )
            {
                echo "This is a text node" . BR;
                echo "The text type is: " . 
                    $p->getTextNodeType( $node_name ) . BR;
                
                if( $p->isWYSIWYG( $node_name ) )
                {
                    echo "This is a WYSIWYG". BR;
                }
            }
            else
            {
                echo "This is not a text node" . BR;
            }
            
            if( $mode != 'all' )
                break;
                
        case 'set':
        	u\DebugUtility::dump( $p->getIdentifiers() );
        	
        	try
        	{
        		$p->setText( "main-group;h1", "" )->edit();
        	}
        	catch( e\EmptyValueException $e )
        	{ /* do nothing, just testing */ }
        
            // work with DEFAULT first
            $node_name = "pre-main-group;mul-pre-main-chooser;0";
            
            $block_id = 'c12da9c78b7ffe83129ed6d8411290fe';
            $dd_block = $cascade->getAsset( a\DataBlock::TYPE, $block_id );
            
            if( $p->hasNode( $node_name ) )
            {
                $node_type = $p->getNodeType( $node_name );
                
                if( $node_type == c\T::ASSET )
                {
                    $asset_type = $p->getAssetNodeType( $node_name );
                    
                    if( $asset_type == c\T::BLOCK )
                    {
                        $p->setBlock( $node_name, $dd_block )->edit();
                    }
                }
            }

            $node_name = "main-group;wysiwyg";
             
            if( $p->hasNode( $node_name ) && 
                $p->getNodeType( $node_name ) == c\T::TEXT &&
                $p->isWYSIWYG( $node_name )
            )
            {
                $text = $p->getText( $node_name );
                $text .= "<p>Another paragraph.</p>";
                $p->setText( $node_name, $text )->edit();
            }

            // add another chooser
            $node_name = $node_name = "bottom-group;mul-bottom-group-chooser;0";
            $p->appendSibling( $node_name )->edit();

            $p->setShouldBePublished( false )->
            	setShouldBePublished( true )->
            	setMaintainAbsoluteLinks( true )->edit();

            if( $mode != 'all' )
                break;
                
        case 'metadata':
            $m = $p->getMetadata();
            
            u\DebugUtility::dump( $m->getDynamicFieldNames() );
            $field_name = "exclude-from-left-folder-nav";
            //var_dump( $m->getDynamicFieldPossibleValues( $field_name ) );
            $m->setDynamicFieldValues( $field_name, 'yes' );
            $field_name = "exclude-from-mobile-menu";
            $m->setDynamicFieldValues( $field_name, NULL );
            $field_name = "tree-picker";
            $m->setDynamicFieldValues( $field_name, "center" );
            
            $p->edit()->dump();
            
            if( $mode != 'all' )
                break;
                
        case 'copy':
            $parent     = $p->getParentFolder();
            $new_page = $p->copy( $parent, 'test2' );
            $new_page->display();
            
            if( $mode != 'all' )
                break;
                
        case 'raw':
            //$p->dump();
        
            $p_std = $service->retrieve( $service->createId( 
                c\T::PAGE, $id ), c\P::PAGE );
                
            u\DebugUtility::dump( $p_std );
        
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