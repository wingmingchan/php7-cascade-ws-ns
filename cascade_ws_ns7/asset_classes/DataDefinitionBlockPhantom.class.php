<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/8/2016 Added code to deal with host asset.
  * 5/28/2015 Added namespaces.
  * 2/24/2015 Added getPossibleValues.
  * 9/29/2014 Fixed in bug in edit.
  * 9/25/2014 Added isMultiLineNode.
  * 8/14/2014 Added style to error messages.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class DataDefinitionBlockPhantom extends Block
{
    const DEBUG = false;
    const DUMP  = false;
    const TYPE  = c\T::DATABLOCK;

    /**
    * The constructor
    * @param $service the AssetOperationHandlerService object
    * @param $identifier the identifier object
    */
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        if( isset( $this->getProperty()->structuredData ) )
        {
            $this->processStructuredDataPhantom();
        }
        else
        {
            $this->xhtml = $this->getProperty()->xhtml;
        }
    }

    public function appendSibling( $identifier )
    {
        $this->checkStructuredData();
        
        if( self::DEBUG ) { u\DebugUtility::out( "Calling appendSibling" ); }
        $this->structured_data->appendSibling( $identifier );
        $this->edit();
        return $this;
    }
    
    public function copyDataTo( $block )
    {
        $this->checkStructuredData();
        $block->setStructuredData( $this->getStructuredDataPhantom() );
        return $this;
    }
    
    public function createNInstancesForMultipleField( $number, $identifier )
    {
        $this->checkStructuredData();
        $number = intval( $number );
        
        if( !$number > 0 )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $number is not a number." . E_SPAN );
        }
        
        if( !$this->hasNode( $identifier ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist." . E_SPAN );
        }
        
        $num_of_instances  = $this->getNumberOfSiblings( $identifier );
    
        if( $num_of_instances < $number ) // more needed
        {
            while( $this->getNumberOfSiblings( $identifier ) != $number )
            {
                $this->appendSibling( $identifier );
            }
        }
        else if( $num_of_instances > $number )
        {
            while( $this->getNumberOfSiblings( $identifier ) != $number )
            {
                $this->removeLastSibling( $identifier );
            }
        }
        $this->reloadProperty();
        $this->processStructuredDataPhantom();
        return $this;
    }
    
    public function displayDataDefinition()
    {
        $this->checkStructuredData();
        $this->structured_data->getDataDefinition()->displayXML();
        return $this;
    }
    
    public function displayXhtml()
    {
        if( !$this->hasStructuredData() )
        {
            $xhtml_string = u\XMLUtility::replaceBrackets( $this->xhtml );
            echo S_H2 . 'XHTML' . E_H2;
            echo $xhtml_string . HR;
        }
        return $this;
    }
    
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        // edit the asset
        $asset = new \stdClass();
        $block = $this->getProperty();
        
        $block->metadata = $this->getMetadata()->toStdClass();
        
        if( isset( $this->structured_data ) )
        {
            $block->structuredData = $this->structured_data->toStdClass();
            $block->xhtml          = NULL;
        }
        else
        {
            $block->structuredData = NULL;
            $block->xhtml          = $this->xhtml;
        }

        $asset->{ $p = $this->getPropertyName() } = $block;
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $asset ); }
            throw new e\EditingFailureException(
                S_SPAN . "Block: " . $this->getPath() . E_SPAN . BR .
                c\M::EDIT_ASSET_FAILURE . $service->getMessage() );
        }
        $this->reloadProperty();
        
        if( isset( $this->getProperty()->structuredData ) )
            $this->processStructuredDataPhantom();
        return $this;
    }
    
    public function getAssetNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getAssetNodeType( $identifier );
    }

    public function getBlockId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockId( $identifier );
    }
    
    public function getBlockPath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockPath( $identifier );
    }
    
    public function getDataDefinition()
    {
        $this->checkStructuredData();
        return $this->structured_data->getDataDefinition();
    }
    
    public function getFileId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFileId( $identifier );
    }
    
    public function getFilePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFilePath( $identifier );
    }
    
    public function getIdentifiers()
    {
        $this->checkStructuredData();
        return $this->structured_data->getIdentifiers();
    }
    
    public function getLinkableId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkableId( $identifier );
    }
    
    public function getLinkablePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkablePath( $identifier );
    }
    
    public function getNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getNodeType( $identifier );
    }
    
    public function getNumberOfSiblings( $identifier )
    {
        $this->checkStructuredData();
        
        if( trim( $identifier ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_IDENTIFIER . E_SPAN );
        }
        
        if( !$this->hasIdentifier( $identifier ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist." . E_SPAN );
        }
        return $this->structured_data->getNumberOfSiblings( $identifier );
    }

    public function getPageId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPageId( $identifier );
    }
    
    public function getPagePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPagePath( $identifier );
    }
    
    public function getPossibleValues( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPossibleValues( $identifier );
    }
    
    public function getStructuredDataPhantom()
    {
        $this->checkStructuredData();
        return $this->structured_data;
    }
    
    public function getSymlinkId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkId( $identifier );
    }
    
    public function getSymlinkPath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkPath( $identifier );
    }
    
    public function getText( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getText( $identifier );
    }
    
    public function getTextNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getTextNodeType( $identifier );
    }

    public function getXhtml()
    {
        return $this->xhtml;
    }
    
    public function hasIdentifier( $identifier )
    {
        $this->checkStructuredData();
        return $this->hasNode( $identifier );
    }
    
    public function hasNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->hasNode( $identifier );
    }
    
    public function hasStructuredData()
    {
        return $this->structured_data != NULL;
    }
    
    public function isAssetNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isAssetNode( $identifier );
    }
    
    public function isGroupNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isGroupNode( $identifier );
    }
    
    public function isMultiLineNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiLineNode( $identifier );
    }
    
    public function isMultiple( $field_name )
    {
        $this->checkStructuredData();
        return $this->getDataDefinition()->isMultiple( $field_name );
    }
    
    public function isRequired( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isRequired( $identifier );
    }

    public function isTextNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextNode( $identifier );
    }
    
    public function isWYSIWYG( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isWYSIWYG( $identifier );
    }

    public function removeLastSibling( $identifier )
    {
        $this->checkStructuredData();
        $this->structured_data->removeLastSibling( $identifier );
        $this->edit();
        return $this;
    }
    
    public function replaceByPattern( $pattern, $replace, $include=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->replaceByPattern( $pattern, $replace, $include );
        return $this;
    }
    
    public function replaceXhtmlByPattern( $pattern, $replace )
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_XHTML_BLOCK . E_SPAN );
        }
        
        $this->xhtml = preg_replace( $pattern, $replace, $this->xhtml );
        
        return $this;
    }
    
    public function replaceText( $search, $replace, $include=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->replaceText( $search, $replace, $include );
        return $this;
    }
    
    public function searchText( $string )
    {
        $this->checkStructuredData();
        return $this->structured_data->searchText( $string );
    }
    
    public function searchXhtml( $string )
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_XHTML_BLOCK . E_SPAN );
        }

        return strpos( $this->xhtml, $string ) !== false;
    }

    public function setBlock( $identifier, Block $block=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setBlock( $identifier, $block );
        return $this;
    }
    
    public function setFile( $identifier, File $file=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setFile( $identifier, $file );
        return $this;
    }

    public function setLinkable( $identifier, Linkable $linkable=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setLinkable( $identifier, $linkable );
        return $this;
    }

    public function setPage( $identifier, Page $page=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setPage( $identifier, $page );
        return $this;
    }
    
    public function setStructuredData( p\StructuredData $structured_data )
    {
        $this->checkStructuredData();
        $this->structured_data = $structured_data;
        $this->edit();
        $this->processStructuredDataPhantom();
        return $this;
    }

    public function setSymlink( $identifier, Symlink $symlink=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setSymlink( $identifier, $symlink );
        return $this;
    }

    public function setText( $identifier, $text )
    {
        $this->checkStructuredData();
        $this->structured_data->setText( $identifier, $text );
        return $this;
    }
    
    public function setXhtml( $xhtml )
    {
        if( !$this->hasStructuredData() )
        {
            $this->xhtml = $xhtml;
        }
        else
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_XHTML_BLOCK . E_SPAN );
        }
        return $this;
    }

    public function swapData( $identifier1, $identifier2 )
    {
        $this->checkStructuredData();
        $this->structured_data->swapData( $identifier1, $identifier2 );
        $this->edit()->processStructuredDataPhantom();
        
        return $this;
    }

    private function checkStructuredData()
    {
        if( !$this->hasStructuredData() )
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_DATA_BLOCK . E_SPAN );
        }
    }
    
    private function processStructuredDataPhantom()
    {
        $this->structured_data = new p\StructuredDataPhantom( 
            $this->getProperty()->structuredData, 
            $this->getService(), $this );
    }

    private $structured_data;
    private $xhtml;
}
?>
