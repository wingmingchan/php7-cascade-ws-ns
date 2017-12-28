<?php
require_once( 'auth_test.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

/*
array(15) {
  [0]=>
  string(5) "group"
  [1]=>
  string(10) "group;text"
  [2]=>
  string(15) "group;multiline"
  [3]=>
  string(13) "group;wysiwyg"
  [4]=>
  string(14) "group;checkbox"
  [5]=>
  string(14) "group;dropdown"
  [6]=>
  string(11) "group;radio"
  [7]=>
  string(17) "group;multiselect"
  [8]=>
  string(14) "group;calendar"
  [9]=>
  string(14) "group;datetime"
  [10]=>
  string(10) "group;page"
  [11]=>
  string(10) "group;file"
  [12]=>
  string(11) "group;block"
  [13]=>
  string(13) "group;symlink"
  [14]=>
  string(14) "group;linkable"
}
*/
try
{
/*/
    // part 1: no multiple nodes
    $block = $service->getAsset(
        a\DataBlock::TYPE, "c12d96ea8b7ffe83129ed6d89af982f0" );
        
    //u\DebugUtility::dump( $block->getIdentifiers() );
        
    $block2 = $service->getAsset(
        a\DataBlock::TYPE, "46ab0b618b7ffe831131a667336745aa" );
    //$block->setStructuredData( $block2->getStructuredData() );
    $block->setText( "group;text", "Wonderful News" )->edit();

    $id = "group;wysiwyg";
    echo u\StringUtility::boolToString( $block->isWYSIWYGNode( $id ) ), BR;
    
    if( $block->isWYSIWYGNode( $id ) )
    {
    	$block->setText( $id, "<p>Some different content for the block</p>" )->edit();
    }
    
    //u\DebugUtility::dump( $block->getStructuredData() );
    $block->copyDataTo( $block2 );
    $block->displayDataDefinition();
    
    $block->setBlock(
        	"group;block",
        	$block2 )->
        setFile( "group;file" )-> 
        setPage( "group;page" )-> 
        setLinkable( "group;linkable" )-> 
        setSymlink( "group;symlink" )->
        
        setText( "group;multiline", "Here\rThere\rEverywhere" )->
        edit();

	// only works for SOAP
    // $block->mapData()->dump();
    //echo $block->getType(), BR;
   
    $id = "group;radio";
    
    if( $block->hasPossibleValues( $id ) )
        u\DebugUtility::dump( $block->getPossibleValues( $id ) );
  
    $id = "group;wysiwyg";
    
    if( $block->isText( $id ) )
    	echo $block->getText( $id ), BR;
    	
    echo u\StringUtility::getCoalescedString( $block->getTextNodeType( $id ) ), BR;
      
    if( $block2->isWYSIWYGNode( $id ) )
    {
        $block2->replaceByPattern(
            "/" . "<" . "p>([^<]+)<\/p>/", 
            "<div class='text_red'>$1</div>", 
            array( $id )
        )->edit();
    }
    else
        echo "Not WYSIWYG node", BR;
    // affects all text nodes    
    $block2->replaceText( "Wonderful", "Amazing" )->edit();
   
    u\DebugUtility::dump( $block2->searchText( "Amazing" ) );
    u\DebugUtility::dump( $block2->searchWYSIWYGByPattern( "/<p>([^<]+)<\/p>/" ) );
   
    $id = "group;symlink";
    echo $block->getAssetNodeType( $id ), BR;
    echo $block->getNodeType( $id ), BR;

    if( $block->hasNode( $id ) && $block->isAsset( $id ) )
    {
        if( $block->isSymlinkChooserNode( $id ) )
        {
            echo u\StringUtility::getCoalescedString( $block->getSymlinkId( $id ) ), BR;
        }
    }
    
    $id = "group;page";
    
    if( $block->isAsset( $id ) )
    {
        if( $block->getAssetNodeType( $id ) == c\T::PAGE )
        {
            echo u\StringUtility::getCoalescedString( $block->getPageId( $id ) ), BR;
            echo u\StringUtility::getCoalescedString( $block->getPagePath( $id ) ), BR;
        }
    }
    
    $id = "group;linkable";
    
    if( $block->isAsset( $id ) )
    {
        if( $block->getAssetNodeType( $id ) == c\T::LINKABLE )
        {
            echo u\StringUtility::getCoalescedString( $block->getLinkableId( $id ) ), BR;
            echo u\StringUtility::getCoalescedString( $block->getLinkablePath( $id ) ), BR;
            
            //u\DebugUtility::dump( $block->getNode( $id )->toStdClass() );
            echo $block->getNodeType( $id ), BR;
        }
    }
    
    $id = "group;file";
    
    if( $block->isAsset( $id ) )
    {
        if( $block->getAssetNodeType( $id ) == "file" )
        {
            echo u\StringUtility::getCoalescedString( $block->getFileId( $id ) ), BR;
            echo u\StringUtility::getCoalescedString( $block->getFilePath( $id ) ), BR;
        }
    }
    $id = "group;block";

    if( $block->isAsset( $id ) )
    {
        //echo $block->getAssetNodeType( $id ), BR;
        if( $block->isBlockChooserNode( $id ) )
        {
            echo u\StringUtility::getCoalescedString( $block->getBlockId( $id ) ), BR;
            echo u\StringUtility::getCoalescedString( $block->getBlockPath( $id ) ), BR;
        }
    }
/*/
    // part 2: multiple nodes
/*
array(11) {
  [0]=>
  string(16) "multiple-first;0"
  [1]=>
  string(16) "multiple-first;1"
  [2]=>
  string(6) "single"
  [3]=>
  string(17) "multiple-second;0"
  [4]=>
  string(17) "multiple-second;1"
  [5]=>
  string(17) "multiple-second;2"
  [6]=>
  string(5) "group"
  [7]=>
  string(28) "group;group-multiple-first;0"
  [8]=>
  string(28) "group;group-multiple-first;1"
  [9]=>
  string(18) "group;group-single"
  [10]=>
  string(29) "group;group-multiple-second;0"
}
}*/
    $block = $service->getAsset(
        a\DataBlock::TYPE, "9d76a3aa8b7ffe8353cc17e9aa17f209" );
/*/
    u\DebugUtility::dump( $block->getIdentifiers() );
    $block->//appendSibling( "multiple-first;0" )->
        createNInstancesForMultipleField( 10, "multiple-first;0" );
        //->
        //edit();
        
    echo $block->getNumberOfSiblings( "multiple-first;0" ), BR;
    $block->removeLastSibling( "multiple-first;0" );
    $block->swapData( "multiple-first;0", "multiple-first;2" );

    // renew the object
    $block  = $block->getStructuredData();
    
    echo $block->getNumberOfChildren(), BR;
    echo $block->getNumberOfSiblings( "multiple-first;0" ), BR;
/*/
        
    echo u\StringUtility::boolToString(
        $block->isMultiple( "multiple-second;1" ) ), BR;
        
    $sd = $block->getStructuredData();
    $sd->removeLastSibling( "multiple-first;0" )->getHostAsset()->edit();
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