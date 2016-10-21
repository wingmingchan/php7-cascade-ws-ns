<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/2/2016 Added aliases.
  * 6/1/2016 Added isBlockChooser, isCalendarNode, isCheckboxNode, isDatetimeNode, isDropdownNode,
  * isFileChooser, isLinkableChooser, isMultiLineNode, isMultiSelectorNode, isPageChooser,
  * isRadioNode, isSymlinkChooser, isTextBox, and isWYSIWYGNode.
  * 3/10/2016 Added hasPhantomNodes.
  * 3/9/2016 Added mapData.
  * 1/8/2016 Added code to deal with host asset.
  * 5/28/2015 Added namespaces.
  * 2/24/2015 Added getPossibleValues.
  * 9/29/2014 Fixed in bug in edit.
  * 9/25/2014 Added isMultiLineNode.
  * 8/14/2014 Added style to error messages.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>A <code>DataDefinitionBlock</code> object represents an asset of type <code>xhtmlDataDefinitionBlock</code>. This class is a sub-class of <a href="/web-services/api/asset-classes/block"><code>Block</code></a>.</p>
<p>This class can be used to manipulate both data definition blocks and xhtml blocks. The only test available to tell them apart is the <code>DataDefinitionBlock::hasStructuredData</code> method. When it returns true, the block is a data definition block; else it is an xhtml block. We cannot consider the <code>xhtml</code> property because it can be <code>NULL</code> for both block sub-types.</p>
<p>I could have split this class into two: <code>DataDefinitionBlock</code> and <code>XhtmlBlock</code>. But this difference between with and without using a data definition also exists in a page, and I do not think I should split the <code>Page</code> class into two. Therefore, I use the same class to deal with these two types of <code>xhtmlDataDefinitionBlock</code>.</p>
<h2>Structure of <code>xhtmlDataDefinitionBlock</code></h2>
<pre>xhtmlDataDefinitionBlock
  id
  name
  parentFolderId
  parentFolderPath
  path
  lastModifiedDate
  lastModifiedBy
  createdDate
  createdBy
  siteId
  siteName
  metadata
    author
    displayName
    endDate
    keywords
    metaDescription
    reviewDate
    startDate
    summary
    teaser
    title
    dynamicFields (NULL or an stdClass)
      dynamicField (an stdClass or or array of stdClass)
        name
        fieldValues (NULL, stdClass or array of stdClass)
          fieldValue
            value
  metadataSetId
  metadataSetPath
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled (bool)
  structuredData
    definitionId
    definitionPath
    structuredDataNodes
      structuredDataNode (stdClass or array of stdClass)
        type
        identifier
        structuredDataNodes
        text
        assetType
        blockId
        blockPath
        fileId
        filePath
        pageId
        pagePath
        symlinkId
        symlinkPath
        recycled
  xhtml
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class DataDefinitionBlockPhantom extends Block
{
    const DEBUG = false;
    const DUMP  = false;
    const TYPE  = c\T::DATABLOCK;

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
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

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function appendSibling( $identifier )
    {
        $this->checkStructuredData();
        
        if( self::DEBUG ) { u\DebugUtility::out( "Calling appendSibling" ); }
        $this->structured_data->appendSibling( $identifier );
        $this->edit();
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function copyDataTo( $block )
    {
        $this->checkStructuredData();
        $block->setStructuredData( $this->getStructuredDataPhantom() );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function displayDataDefinition()
    {
        $this->checkStructuredData();
        $this->structured_data->getDataDefinition()->displayXML();
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getAssetNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getAssetNodeType( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBlockId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBlockPath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getBlockPath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDataDefinition()
    {
        $this->checkStructuredData();
        return $this->structured_data->getDataDefinition();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getFileId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFileId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getFilePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getFilePath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifiers()
    {
        $this->checkStructuredData();
        return $this->structured_data->getIdentifiers();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLinkableId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkableId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLinkablePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getLinkablePath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getNodeType( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPageId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPagePath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPagePath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPossibleValues( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getPossibleValues( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getStructuredDataPhantom()
    {
        $this->checkStructuredData();
        return $this->structured_data;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkId( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkId( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSymlinkPath( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getSymlinkPath( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getText( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getText( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getTextNodeType( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->getTextNodeType( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getXhtml()
    {
        return $this->xhtml;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasIdentifier( $identifier )
    {
        $this->checkStructuredData();
        return $this->hasNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->hasNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasStructuredData()
    {
        return $this->structured_data != NULL;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isAsset( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isAssetNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isAssetNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isAssetNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isGroupNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isGroupNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isMultiLineNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiLineNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isMultiple( $field_name )
    {
        $this->checkStructuredData();
        return $this->getDataDefinition()->isMultiple( $field_name );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isRequired( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isRequired( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isTextNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isWYSIWYG( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isWYSIWYG( $identifier );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function removeLastSibling( $identifier )
    {
        $this->checkStructuredData();
        $this->structured_data->removeLastSibling( $identifier );
        $this->edit();
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function replaceByPattern( $pattern, $replace, $include=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->replaceByPattern( $pattern, $replace, $include );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function replaceText( $search, $replace, $include=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->replaceText( $search, $replace, $include );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchText( $string )
    {
        $this->checkStructuredData();
        return $this->structured_data->searchText( $string );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchXhtml( $string )
    {
        if( $this->hasStructuredData() )
        {
            throw new e\WrongBlockTypeException( 
                S_SPAN . c\M::NOT_XHTML_BLOCK . E_SPAN );
        }

        return strpos( $this->xhtml, $string ) !== false;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setBlock( $identifier, Block $block=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setBlock( $identifier, $block );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setFile( $identifier, File $file=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setFile( $identifier, $file );
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setLinkable( $identifier, Linkable $linkable=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setLinkable( $identifier, $linkable );
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPage( $identifier, Page $page=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setPage( $identifier, $page );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setStructuredData( p\StructuredData $structured_data )
    {
        $this->checkStructuredData();
        $this->structured_data = $structured_data;
        $this->edit();
        $this->processStructuredDataPhantom();
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setSymlink( $identifier, Symlink $symlink=NULL )
    {
        $this->checkStructuredData();
        $this->structured_data->setSymlink( $identifier, $symlink );
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setText( $identifier, $text )
    {
        $this->checkStructuredData();
        $this->structured_data->setText( $identifier, $text );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
