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
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class DataDefinitionBlock extends Block
{
    const DEBUG = false;
    const DUMP  = false;
    const TYPE  = c\T::DATABLOCK;

    /**
    * The constructor
    * @param $service the AssetOperationHandlerService object
    * @param $identifier the identifier object
    */
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
            $this->processStructuredData();
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
        $block->setStructuredData( $this->getStructuredData() );
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
        $this->processStructuredData();
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
            $this->processStructuredData();
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
    public function getStructuredData()
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
    public function hasPhantomNodes() // detects phantom nodes of type B
    {
        return $this->structured_data->hasPhantomNodes();
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
    public function isBlockChooser( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isBlockChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isBlockChooserNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isBlockChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isCalendar( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isCalendarNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isCalendarNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isCalendarNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isCheckbox( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isCheckboxNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isCheckboxNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isCheckboxNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isDatetime( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isDatetimeNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isDatetimeNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isDatetimeNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isDropdown( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isDropdownNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isDropdownNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isDropdownNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isFileChooser( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isFileChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isFileChooserNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isFileChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isGroup( $identifier )
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
    public function isLinkableChooser( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isLinkableChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isLinkableChooserNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isLinkableChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isMultiLine( $identifier )
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
    public function isMultiSelector( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiSelectorNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isMultiSelectorNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isMultiSelectorNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isPageChooser( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isPageChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isPageChooserNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isPageChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isRadio( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isRadioNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isRadioNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isRadioNode( $identifier );
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
    public function isSymlinkChooser( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isSymlinkChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isSymlinkChooserNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isSymlinkChooser( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isTextBox( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextBox( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isTextBoxNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isTextBox( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isText( $identifier )
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
    public function isTextarea( $identifier )
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
    public function isTextareaNode( $identifier )
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
    public function isWYSIWYGNode( $identifier )
    {
        $this->checkStructuredData();
        return $this->structured_data->isWYSIWYGNode( $identifier );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function mapData()
    {
        $this->checkStructuredData();
        $new_sd = $this->structured_data->mapData();
        return $this->setStructuredData( $new_sd );
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
        $this->processStructuredData();
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
        $this->edit()->processStructuredData();
        
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
    
    private function processStructuredData()
    {
        $this->structured_data = new p\StructuredData( 
            $this->getProperty()->structuredData, 
            $this->getService(), $this );
    }

    private $structured_data;
    private $xhtml;
}
?>
