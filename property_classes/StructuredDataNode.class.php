<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/2/2016 Added aliases. Replaced most string literals with constants.
  * 6/1/2016 Added isBlockChooser, isCalendarNode, isCheckboxNode, isDatetimeNode, isDropdownNode,
  * isFileChooser, isLinkableChooser, isMultiLineNode, isMultiSelectorNode, isPageChooser,
  * isRadioNode, isSymlinkChooser, isTextBox, and isWYSIWYGNode.
  * 12/23/2015 Fixed a bug in addChildNode.
  * 5/28/2015 Added namespaces.
  * 2/24/2015 Added getPossibleValues.
  * 2/24/2015 Fixed a bug in setText. Added tests of $this->required to deal with empty strings or NULL.
  * 9/25/2014 Added isMultiLineNode.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

class StructuredDataNode extends Property
{
    const DEBUG = false;

    const DELIMITER          = a\DataDefinition::DELIMITER;
    const CHECKBOX_PREFIX    = '::CONTENT-XML-CHECKBOX::';
    const SELECTOR_PREFIX    = '::CONTENT-XML-SELECTOR::';
    const TEXT_TYPE_CALENDAR = c\T::CALENDAR;
    const TEXT_TYPE_CHECKBOX = c\T::CHECKBOX;
    const TEXT_TYPE_DATETIME = c\T::DATETIME;
    const TEXT_TYPE_DROPDOWN = c\T::DROPDOWN;
    const TEXT_TYPE_RADIO    = c\T::RADIOBUTTON;
    const TEXT_TYPE_SELECTOR = c\T::MULTISELECTOR;
    
    public function __construct( 
        \stdClass $node=NULL,
        aohs\AssetOperationHandlerService $service=NULL,
        $dd=NULL, 
        $index=NULL, 
        $parent_id=NULL ) 
    {
        if( isset( $node ) ) // $node always a single non-NULL object
        {
            $this->parent_id       = $parent_id;
            $this->type            = $node->type;
            $this->data_definition = $dd;
            $this->node_map        = array();
            
            // attach parent identifier to current node identifier
            // note that parent_id ends with a semi-colon
            $this->identifier = $parent_id . $node->identifier;
            
            $field_identifier = self::getFieldIdentifier( $this->identifier );
            $field            = $this->data_definition->getField( $field_identifier );
            
            // check if this is a multiple field
            if( isset( $field[ c\T::MULTIPLE ] ) )
            {
                $this->multiple = $field[ c\T::MULTIPLE ];
            }
            
            // check if this is a radio
            if( isset( $field[ c\T::TYPE ] ) )
            {
                $this->radio = ( $field[ c\T::TYPE ] == c\T::RADIOBUTTON );
            }
            
            // store the items for radio, multi-selectors, and so on
            if( isset( $field[ c\T::ITEMS ] ) )
            {
                $this->items = $field[ c\T::ITEMS ];
            }
            
            // is it required?
            if( isset( $field[ c\T::REQUIRED ] ) )
            {
                $this->required = $field[ c\T::REQUIRED ];
            }
            
            // type mostly for setText
            if( isset( $field[ c\T::TYPE ] ) )
            {
                $this->text_type = $field[ c\T::TYPE ];
            }
            
            // is it multi-line?
            if( isset( $field[ c\T::MULTILINE ] ) )
            {
                $this->multi_line = $field[ c\T::MULTILINE ];
            }
            
            // is it wysiwyg?
            if( isset( $field[ c\T::WYSIWYG ] ) )
            {
                $this->wysiwyg = $field[ c\T::WYSIWYG ];
            }
            
            // add the index if this is a multiple field
            if( $this->multiple == true )
            {
                $this->index       = $index;
                $this->identifier .= self::DELIMITER . $this->index;
            }
            
            if( $this->type != c\T::GROUP ) // text or asset
            {
                $this->structured_data_nodes = NULL;
                $this->text         = $node->text;
                $this->asset_type   = $node->assetType;
                $this->block_id     = $node->blockId;
                $this->block_path   = $node->blockPath;
                $this->file_id      = $node->fileId;
                $this->file_path    = $node->filePath;
                $this->page_id      = $node->pageId;
                $this->page_path    = $node->pagePath;
                $this->symlink_id   = $node->symlinkId;
                $this->symlink_path = $node->symlinkPath;
                $this->node_map     = array( $this->identifier => $this );
            }
            else // group
            {
                $this->structured_data_nodes = array();
                $this->text         = NULL;
                $this->asset_type   = NULL;
                $this->block_id     = NULL;
                $this->block_path   = NULL;
                $this->file_id      = NULL;
                $this->file_path    = NULL;
                $this->page_id      = NULL;
                $this->page_path    = NULL;
                $this->symlink_id   = NULL;
                $this->symlink_path = NULL;
            
                $cur_identifier     = $this->identifier;
                // make sure there is exactly one trailing delimiter
                $cur_identifier = trim( $cur_identifier, self::DELIMITER );
                $cur_identifier .= self::DELIMITER;
                
                // recursively process the data
                self::processStructuredDataNodes( 
                    $cur_identifier, // the parent id
                    $this->structured_data_nodes, // array to store children
                    $node->structuredDataNodes->structuredDataNode, // stdClass
                    $this->data_definition
                );
                
                // for easy look-up
                $this->node_map[ $this->identifier ] = $this;
                
                foreach( $this->structured_data_nodes as $child_node )
                {
                    $this->node_map = array_merge( 
                        ( array )$this->node_map, ( array )$child_node->node_map );
                }
            }
        
            $this->recycled = $node->recycled;
        }
    }
    
    public function addChildNode( $node_id )
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $this->structured_data_nodes ); }
    
        if( $this->structured_data_nodes == NULL )
        {
            throw new e\NodeException(
                S_SPAN . "Cannot add a node to a node that has no children." . E_SPAN );
        }
        
        // remove digits and semi-colons, turning node id to field id
        $field_id = self::getFieldIdentifier( $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Node ID: " . $node_id . BR . "Field ID: " . $field_id ); }
        
        if( !$this->data_definition->isMultiple( $field_id ) )
        {
            throw new e\NodeException( 
                S_SPAN . "Cannot add a node to a non-multiple field." . E_SPAN );
        }

        $last_pos    = self::getPositionOfLastNode( $this->structured_data_nodes, $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Last position: " . $last_pos ); }
        
        // create a copy of the last sibling
        $cloned_node = $this->structured_data_nodes[ $last_pos ]->cloneNode();
        if( self::DEBUG ) { u\DebugUtility::dump( $cloned_node->toStdClass() ); }


        $this->structured_data_nodes[] = $cloned_node;
        $this->node_map = array_merge( 
            $this->node_map, array( $cloned_node->getIdentifier() => $cloned_node ) );

        return $this;
    }
    
    public function cloneNode()
    {
        // clone the calling node
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $this->parent_id ); }
        
        $clone_obj = new StructuredDataNode( 
            $this->toStdClass(), NULL, $this->data_definition, 0, $this->parent_id );
            
        if( self::DEBUG ) { u\DebugUtility::dump( $clone_obj->toStdClass() ); }
        
        // work out the new identifier
        $this_identifier       = $this->identifier;
        if( self::DEBUG ) { u\DebugUtility::out( $this_identifier ); }
        $index                 = self::getLastIndex( $this->identifier ) + 1;
        $clone_identifier      = self::removeLastIndex( $this->identifier ) . 
                                 self::DELIMITER . $index;
        $clone_obj->identifier = $clone_identifier;
        
        if( self::DEBUG ) { u\DebugUtility::out( $clone_identifier ); }
        
        return $clone_obj;
    }
    
    public function display()
    {
        switch( $this->type )
        {
            case c\T::ASSET:
                break;
                
            case c\T::GROUP:
                echo "Type: " . $this->type . BR .
                    "Identifier: " . $this->identifier . BR;
                break;
                
            case c\T::TEXT:
                echo "Type: " . $this->type . BR .
                    "Identifier: " . $this->identifier . BR;
                break;
        }
        return $this;
    }
    
    public function dump()
    {
        echo S_PRE;
        var_dump( $this );
        echo S_PRE;
        return $this;
    }
    
    public function getAssetType()
    {
        return $this->asset_type;
    }
    
    public function getBlockId()
    {
        return $this->block_id;
    }
    
    public function getBlockPath()
    {
        return $this->block_path;
    }
    
    public function getChildren()
    {
        return $this->getStructuredDataNodes();
    }
    
    public function getDataDefinition()
    {
        return $this->data_definition;
    }
    
    public function getFileId()
    {
        return $this->file_id;
    }
    
    public function getFilePath()
    {
        return $this->file_path;
    }
    
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    public function getIdentifierNodeMap()
    {
        return $this->node_map;
    }
    
    public function getItems()
    {
        return $this->items;
    }
    
    public function getLinkableId()
    {
        if( isset( $this->file_id ) )
            return $this->file_id;
        else if( isset( $this->page_id ) )
            return $this->page_id;
        else // NULL or not 
            return $this->symlink_id;
    }
    
    public function getLinkablePath()
    {
        if( isset( $this->file_path ) )
            return $this->file_path;
        else if( isset( $this->page_path ) )
            return $this->page_path;
        else // NULL or not
            return $this->symlink_path;
    }
    
    public function getPageId()
    {
        return $this->page_id;
    }
    
    public function getPagePath()
    {
        return $this->page_path;
    }
    
    public function getParentId()
    {
        return trim( $this->parent_id, self::DELIMITER );
    }
    
    public function getPossibleValues()
    {
        if( isset( $this->items ) && strlen( $this->items ) > 0 )
            return explode( self::DELIMITER, $this->items );
        return NULL;
    }
    
    public function getRecycled()
    {
        return $this->recycled;
    }
    
    public function getStructuredDataNodes()
    {
        return $this->structured_data_nodes;
    }
    
    public function getSymlinkId()
    {
        return $this->symlink_id;
    }
    
    public function getSymlinkPath()
    {
        return $this->symlink_path;
    }
    
    public function getText()
    {
        return $this->text;
    }
    
    public function getTextNodeType()
    {
        return $this->text_type;
    }
    
    public function getTextType()
    {
        return $this->getTextNodeType();
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function hasItem( $item )
    {
        if( $this->items == '' )
            return false;
            
        $items = explode( self::DELIMITER, $this->items );
        return in_array( $item, $items );
    }
    
    public function isAsset()
    {
        return $this->type == c\T::ASSET;
    }
    
    public function isAssetNode()
    {
        return $this->type == c\T::ASSET;
    }
    
    public function isBlockChooser()
    {
        return $this->asset_type == c\T::BLOCK;
    }

    public function isBlockChooserNode()
    {
        return $this->asset_type == c\T::BLOCK;
    }

    public function isCalendar()
    {
        return $this->text_type == c\T::CALENDAR;
    }
    
    public function isCalendarNode()
    {
        return $this->text_type == c\T::CALENDAR;
    }
    
    public function isCheckbox()
    {
        return $this->text_type == c\T::CHECKBOX;
    }
    
    public function isCheckboxNode()
    {
        return $this->text_type == c\T::CHECKBOX;
    }
    
    public function isDatetime()
    {
        return $this->text_type == c\T::DATETIME;
    }
    
    public function isDatetimeNode()
    {
        return $this->text_type == c\T::DATETIME;
    }
    
    public function isDropdown()
    {
        return $this->text_type == c\T::DROPDOWN;
    }
    
    public function isDropdownNode()
    {
        return $this->text_type == c\T::DROPDOWN;
    }
    
    public function isFileChooser()
    {
        return $this->asset_type == c\T::FILE;
    }

    public function isFileChooserNode()
    {
        return $this->asset_type == c\T::FILE;
    }

    public function isGroup()
    {
        return $this->type == c\T::GROUP;
    }
    
    public function isGroupNode()
    {
        return $this->type == c\T::GROUP;
    }
    
    public function isLinkableChooser()
    {
        return $this->asset_type == c\T::PFS;
    }

    public function isLinkableChooserNode()
    {
        return $this->asset_type == c\T::PFS;
    }

    public function isMultiLine()
    {
        return $this->multi_line;
    }
    
    public function isMultiLineNode()
    {
        return $this->multi_line;
    }
    
    public function isMultiple()
    {
        return $this->multiple;
    }
    
    public function isMultiSelector()
    {
        return $this->text_type == c\T::MULTISELECTOR;
    }

    public function isMultiSelectorNode()
    {
        return $this->text_type == c\T::MULTISELECTOR;
    }

    public function isPageChooser()
    {
        return $this->asset_type == c\T::PAGE;
    }

    public function isPageChooserNode()
    {
        return $this->asset_type == c\T::PAGE;
    }

    public function isRadio()
    {
        return $this->radio;
    }
    
    public function isRadioNode()
    {
        return $this->radio;
    }
    
    public function isRequired()
    {
        return $this->required;
    }

    public function isSymlinkChooser()
    {
        return $this->asset_type == c\T::SYMLINK;
    }

    public function isSymlinkChooserNode()
    {
        return $this->asset_type == c\T::SYMLINK;
    }

    public function isText()
    {
        return $this->type == c\T::TEXT;
    }
    
    public function isTextarea()
    {
        return $this->multi_line;
    }
    
    public function isTextareaNode()
    {
        return $this->multi_line;
    }
    
    public function isTextBox()
    {
        if( !$this->isTextNode() || $this->multi_line || $this->wysiwyg ||
            $this->text_type == c\T::DATETIME || $this->text_type == c\T::CALENDAR || 
            $this->text_type == c\T::MULTISELECTOR || $this->text_type == c\T::DROPDOWN ||
            $this->text_type == c\T::CHECKBOX || $this->radio
        )
            return false;
        return true;
    }
    
    public function isTextBoxNode()
    {
        return $this->isTextBox();
    }
    
    public function isTextNode()
    {
        return $this->type == c\T::TEXT;
    }
    
    public function isWYSIWYG()
    {
        return $this->wysiwyg;
    }
    
    public function isWYSIWYGNode()
    {
        return $this->wysiwyg;
    }
    
    public function removeLastChildNode( $node_id )
    {
        if( $this->structured_data_nodes == NULL )
        {
            throw new e\NodeException( 
                S_SPAN . "Cannot remove a node from a node that has no children." . E_SPAN );
        }
        
        // remove digits and semi-colons
        $field_id = self::getFieldIdentifier( $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Field ID: " . $field_id ); }
        if( !$this->data_definition->isMultiple( $field_id ) )
            throw new e\NodeException( 
                S_SPAN . "Cannot remove a node from a non-multiple field" . E_SPAN );

        $last_pos     = self::getPositionOfLastNode( $this->structured_data_nodes, $node_id );
        $first_pos    = self::getPositionOfFirstNode( $this->structured_data_nodes, $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( 
            "First position: " . $first_pos . BR . "Last position: " . $last_pos ); }
        $last_node_id = $this->structured_data_nodes[ $last_pos ]->getIdentifier();
        if( self::DEBUG ) { u\DebugUtility::out( "Last node ID: " . $last_node_id ); }
            
        if( $first_pos == $last_pos ) // the only node
            throw new e\NodeException(
                S_SPAN . "Cannot remove the only node in the field." . E_SPAN );
        
        $child_count = count( $this->structured_data_nodes ); // total children
    
        // node to be deleted in the middle
        if( $child_count > $last_pos + 1 )
        {
            $before = array_slice( $this->structured_data_nodes, 0, $last_pos );
            $after = array_slice( $this->structured_data_nodes, $last_pos + 1 );
            $this->structured_data_nodes = array_merge( $before, $after );
        }
        else // at the end
        {
            array_pop( $this->structured_data_nodes );
        }
            
        unset( $this->node_map, $last_node_id );
        
        return $this;
    }

    public function setBlock( a\Block $block=NULL )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "setBlock called: " . $block->getId() ); }

        // required
        if( $this->required && $block == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_BLOCK . E_SPAN );
        }
        
        if( $this->asset_type != c\T::BLOCK )
        {
            throw new e\NodeException( 
                S_SPAN . "The asset does not accept a block." . E_SPAN );
        }
        
        if( isset( $block ) )
        {
            $this->block_id   = $block->getId();
            $this->block_path = $block->getPath();
        }
        else
        {
            $this->block_id   = NULL;
            $this->block_path = NULL;
        }
        
        return $this;
    }
    
    public function setFile( a\File $file=NULL )
    {
        // required
        if( $this->required && $file == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_FILE . E_SPAN );
        }
        
        if( $this->asset_type != c\T::FILE )
        {
            throw new e\NodeException( 
                S_SPAN . "The asset does not accept a file." . E_SPAN );
        }
        
        if( isset( $file ) )
        {
            $this->file_id   = $file->getId();
            $this->file_path = $file->getPath();
        }
        else
        {
            $this->file_id   = NULL;
            $this->file_path = NULL;
        }
        
        return $this;
    }
    
    public function setLinkable( a\Linkable $linkable=NULL )
    {
        // required
        if( $this->required && $linkable == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_LINKABLE . E_SPAN );
        }
        
        if( $this->asset_type != c\T::PFS )
        {
            throw new e\NodeException( 
                S_SPAN . "The asset does not accept a linkable." . E_SPAN );
        }
        
        if( isset( $linkable ) )
        {
            $type = $linkable->getType();
            
            if( $type == c\T::FILE )
            {
                $this->file_id      = $linkable->getId();
                $this->file_path    = $linkable->getPath();
                $this->page_id      = NULL;
                $this->page_path    = NULL;
                $this->symlink_id   = NULL;
                $this->symlink_path = NULL;
            }
            else if( $type == c\T::PAGE )
            {
                $this->page_id      = $linkable->getId();
                $this->page_path    = $linkable->getPath();
                $this->file_id      = NULL;
                $this->file_path    = NULL;
                $this->symlink_id   = NULL;
                $this->symlink_path = NULL;
            }
            else if( $type == c\T::SYMLINK )
            {
                $this->symlink_id   = $linkable->getId();
                $this->symlink_path = $linkable->getPath();
                $this->file_id      = NULL;
                $this->file_path    = NULL;
                $this->page_id      = NULL;
                $this->page_path    = NULL;
            }
        }
        else
        {
            $this->file_id      = NULL;
            $this->file_path    = NULL;
            $this->page_id      = NULL;
            $this->page_path    = NULL;
            $this->symlink_id   = NULL;
            $this->symlink_path = NULL;
        }
        
        return $this;
    }
    
    public function setPage( a\Page $page=NULL )
    {
        // required
        if( $this->required && $page == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_PAGE . E_SPAN );
        }
        
        if( $this->asset_type != c\T::PAGE )
        {
            throw new e\NodeException( 
                S_SPAN . "The asset does not accept a page." . E_SPAN );
        }
        
        if( isset( $page ) )
        {
            $this->page_id   = $page->getId();
            $this->page_path = $page->getPath();
        }
        else
        {
            $this->page_id   = NULL;
            $this->page_path = NULL;
        }
        
        return $this;
    }
    
    public function setSymlink( a\Symlink $symlink=NULL )
    {
        // required
        if( $this->required && $symlink == NULL )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::NULL_SYMLINK . E_SPAN );
        }
        
        if( $this->asset_type != c\T::SYMLINK )
        {
            throw new e\NodeException( 
                S_SPAN . "The asset does not accept a symlink." . E_SPAN );
        }
        
        if( isset( $symlink ) )
        {
            $this->symlink_id   = $symlink->getId();
            $this->symlink_path = $symlink->getPath();
        }
        else
        {
            $this->symlink_id   = NULL;
            $this->symlink_path = NULL;
        }
        
        return $this;
    }
    
    public function setText( $text )
    {
        $text = trim( $text );
        
        // required
        if( $this->required && $text == '' )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_TEXT . E_SPAN );
        }
        
        // no text to group
        if( $this->type == c\T::GROUP )
        {
            throw new e\NodeException(
                S_SPAN . "Group cannot have text." . E_SPAN );
        }
        else if( $this->items == '' ) // normal text, datetime, calendar
        {
            if( $this->text_type == self::TEXT_TYPE_DATETIME )
            {
                if( !is_numeric( $text) )
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "$text is not an acceptable datetime value." . E_SPAN );
                    
                $this->text = $text;
            }
            else if( $this->text_type == self::TEXT_TYPE_CALENDAR ) // month-day-year
            {
                $date_array = explode( '-', $text );
                
                // must have three parts
                if( count( $date_array ) != 3 )
                {
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "$text is not an acceptable date value." . E_SPAN );
                }
                
                list( $month, $day, $year ) = $date_array;
                
                // convert strings to integers
                $month = intval( $month );
                $day   = intval( $day );
                $year  = intval( $year );
                
                // check the date
                if( !checkdate( $month, $day, $year ) )
                {
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "$text is not an acceptable date value." . E_SPAN );
                }
                
                // compare years, Cascade only has a range of 20 years
                $today     = getdate();
                $this_year = $today[ 'year' ];
                
                if( abs( $this_year - $year ) > 10 )
                {
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "$text is not an acceptable date value." . E_SPAN );
                }
                
                // convert integers back to strings
                if( $month < 10 )
                {
                    $month_string = '0' . $month;
                }
                else
                {
                    $month_string = $month;
                }
                
                if( $day < 10 )
                {
                    $day_string = '0' . $day;
                }
                else
                {
                    $day_string = $day;
                }
                
                $this->text = $month_string . '-' . $day_string . '-' . $year;
            }
            else
            {
                $this->text = $text;
            }
            
            return $this;
        }
        else // checkbox, radio, select, dropdown
        {
            $item_array = explode( self::DELIMITER, $this->items ); // could be NULL
            
            if( strpos( $text, self::CHECKBOX_PREFIX ) !== false ) // no semi-colon
            {
                $input_array = explode( self::CHECKBOX_PREFIX, $text );
            }
            else if( strpos( $text, self::SELECTOR_PREFIX ) !== false ) // no semi-colon
            {
                $input_array = explode( self::SELECTOR_PREFIX, $text );
            }
            else // with semi-colon
            {
                $input_array = explode( self::DELIMITER, $text );
            }
            
            if( count( $item_array ) == 1 )  // single item checkbox or dropdown
            {
                if( $this->text_type == self::TEXT_TYPE_CHECKBOX )
                {
                    // unacceptable input
                    if( $text != $this->items && $text != '' && $text != self::CHECKBOX_PREFIX )
                    {
                        throw new e\NoSuchValueException(
                            S_SPAN . "The value $text does not exist." . E_SPAN );
                    }
                    else if( $text == '' || $text == self::CHECKBOX_PREFIX )
                    {
                        $this->text = self::CHECKBOX_PREFIX;
                    }
                    else
                    {
                        $this->text = $text;
                    }
                }
                else if( $this->text_type == self::TEXT_TYPE_DROPDOWN )
                {
                    if( $text != "" && !in_array( $text, $item_array ) )
                    {
                        throw new e\NoSuchValueException( 
                            S_SPAN . "The value $text does not exist." . E_SPAN );
                    }
                    $this->text = $text;
                }
                
                return $this;
            }
            else // multiple items
            {
                if( $this->text_type == self::TEXT_TYPE_CHECKBOX )
                {
                    if( $text == '' || $text == self::CHECKBOX_PREFIX )
                    {
                        $this->text = self::CHECKBOX_PREFIX;
                    }
                    else
                    {
                        $temp = '';
                        
                        foreach( $input_array as $input )
                        {
                            if( $input == '' )
                            {
                                continue;
                            }
                            else if( !in_array( $input, $item_array ) )
                            {
                                throw new e\NoSuchValueException( 
                                    S_SPAN . "The value $input does not exist." . E_SPAN );
                            }
                            else
                            {
                                $temp .= self::CHECKBOX_PREFIX . $input;
                            }
                        }
                    
                        $this->text = $temp;
                    }
                    return $this;
                }
                else if( $this->text_type == self::TEXT_TYPE_RADIO )
                {
                    if( count( $input_array ) > 1 )
                    {
                        throw new e\UnacceptableValueException( 
                            S_SPAN . "Radio button does not allow more than one value." . E_SPAN );
                    }
                
                    if( $text != "" && !in_array( $text, $item_array ) )
                    {
                        throw new e\NoSuchValueException( 
                            S_SPAN . "The value $text does not exist" . E_SPAN );
                    }
                    
                    if( $this->required && $text == "" )
                    {
                        throw new e\EmptyValueException( 
                            S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
                    }
                    
                    $this->text = $text;
                    
                    return $this;
                }
                else if( $this->text_type == self::TEXT_TYPE_SELECTOR )
                {
                    if( $text == '' || $text == self::SELECTOR_PREFIX )
                    {
                        $this->text = self::SELECTOR_PREFIX;
                    }
                    else
                    {
                        $temp = '';
                        
                        foreach( $input_array as $input )
                        {
                            // skip empty string
                            if( $input == '' )
                            {
                                continue;
                            }
                            else if( !in_array( $input, $item_array ) )
                            {
                                throw new e\UnacceptableValueException( 
                                    S_SPAN . "The value $input does not exist." . E_SPAN );
                            }
                            else
                            {
                                $temp .= self::SELECTOR_PREFIX . $input;
                            }
                        }
                        
                        $this->text = $temp;
                    }
                    return $this;
                }
                else if( $this->text_type == self::TEXT_TYPE_DROPDOWN )
                {
                    $this->text = $text;
                }
            }
        }
    }

    public function swapChildren( $pos1, $node1, $pos2, $node2 )
    {
        $this->structured_data_nodes[ $pos1 ] = $node1;
        $this->structured_data_nodes[ $pos2 ] = $node2;
        return $this;
    }
    
    public function toStdClass()
    {
        $obj       = new \stdClass();
        $obj->type = $this->type;
        $id_array  = explode( self::DELIMITER, $this->identifier );
        $id_count  = count( $id_array );
        
        // work out the identifier of the node
        $id = $id_array[ $id_count - 1 ];
        
        if( is_numeric( $id ) )
        {
            $obj->identifier = $id_array[ $id_count - 2 ];
        }
        else
        {
            $obj->identifier = $id_array[ $id_count - 1 ];
        }
    
        if( $this->type == c\T::GROUP )
        {
            $node_count = count( $this->structured_data_nodes );
        
            if( $node_count == 1 )
            {
                $obj->structuredDataNodes = new \stdClass();
                $obj->structuredDataNodes->structuredDataNode =
                    $this->structured_data_nodes[0]->toStdClass();
            }
            else
            {
                $obj->structuredDataNodes = new \stdClass();
                $obj->structuredDataNodes->structuredDataNode = array();
        
                for( $i = 0; $i < $node_count; $i++ )
                {
                    $obj->structuredDataNodes->structuredDataNode[] = 
                        $this->structured_data_nodes[$i]->toStdClass();
                }
            }
        }
        else
        {
            $obj->structuredDataNodes = NULL;
        }
    
        $obj->text        = $this->text;
        $obj->assetType   = $this->asset_type;
        $obj->blockId     = $this->block_id;
        $obj->blockPath   = $this->block_path;
        $obj->fileId      = $this->file_id;
        $obj->filePath    = $this->file_path;
        $obj->pageId      = $this->page_id;
        $obj->pagePath    = $this->page_path;
        $obj->symlinkId   = $this->symlink_id;
        $obj->symlinkPath = $this->symlink_path;
        $obj->recycled    = $this->recycled;
        
        return $obj;
    }
    
    public static function getFieldIdentifier( $node_id )
    {
        /* this code looks unnecessarily long; just to make sure 
           only digits surrounded by ; are removed
           see StringUtility::getFullyQualifiedIdentifierWithoutPositions
        */
        // remove digit;
        $field_id = preg_replace( '/;(\d)+/', ';', $node_id );
        // remove any doubled-semi-colons
        $field_id = str_replace( ';;', ';', $field_id );
        // trim last semi-colon
        $field_id = trim( $field_id, ';' );
        return $field_id;
    }

    public static function getLastIndex( $node_id )
    {
        $matches = array();
        $result = preg_match( '/;(\d+)$/', $node_id, $matches );
        
        if( $result )
        {
            return intval( $matches[ 1 ] );
        }
        return -1;
    }
    
    public static function getPositionOfFirstNode( $array, $field_id )
    {
        $child_count = count( $array );
        
        for( $i = 0; $i < $child_count; $i++ )
        {
            if( strpos( $array[ $i ]->getIdentifier(), $field_id . a\DataDefinition::DELIMITER ) !== false )
                break;
        }
        return $i;
    }
    
    public static function getPositionOfLastNode( $array, $node_id )
    {
        $child_count = count( $array );
        if( self::DEBUG ) { u\DebugUtility::out( "Child count: " . $child_count ); }
        $shared_id   = self::removeLastIndex( $node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Shared ID: " . $shared_id ); }
        
        for( $i = $child_count - 1; $i > 0; $i-- )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Child ID: " . $array[ $i ]->getIdentifier() ); }            
            if( strpos( $array[ $i ]->getIdentifier(), $shared_id ) !== false )
            {
                if( self::DEBUG ) { u\DebugUtility::out( "Found in $i" ); }  
                break;
            }
        }
        return $i;
    }
    
    public static function processStructuredDataNodes( 
        $parent_id, &$node_array, $node_std, $data_definition )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $parent_id ); }  
        
        if( !is_array( $node_std ) )
        {
            $node_std = array( $node_std );
        }
        
        $node_count  = count( $node_std );
        
        if( self::DEBUG ) { u\DebugUtility::out( "Node count: " . $node_count ); }  
        
        // these are used to calculate the index
        $previous_identifier;
        $current_identifier;
        $cur_index = 0;
        
        // work out the id of the current node for the data definition
        // no digits in the fully qualified identifiers
        for( $i = 0; $i < $node_count; $i++ )
        {
            $fq_identifier = $node_std[$i]->identifier;
            
            if( $parent_id != '' )
            {
                $parent_id_array = explode( self::DELIMITER, $parent_id );
                $temp            = '';
                
                foreach( $parent_id_array as $part )
                {
                    if( !is_numeric( $part ) )
                    {
                        $temp .= $part . self::DELIMITER;
                    }
                }
                
                $temp          = trim( $temp, self::DELIMITER );
                $fq_identifier = 
                    $temp . self::DELIMITER . $node_std[$i]->identifier;
            }
        
            $is_multiple         = $data_definition->isMultiple( $fq_identifier );
            if( isset( $current_identifier ) )
                $previous_identifier = $current_identifier;
            $current_identifier  = $node_std[$i]->identifier;
            
            // a multiple text or group, work out fully qualified identifier
            if( $is_multiple )
            {
                // an old one, keep counting
                if( isset( $previous_identifier ) && $previous_identifier == $current_identifier ) 
                {
                    $cur_index++;
                }
                else // a new one, start from 0 again
                {
                    $cur_index = 0;
                }
            }
            
            if( $parent_id != '' )
            {
                $n = new StructuredDataNode( 
                    $node_std[$i], NULL, $data_definition, $cur_index, $parent_id );
            }
            else
            {
                $n = new StructuredDataNode( 
                    $node_std[$i], NULL, $data_definition, $cur_index );
            }
            
            $n->parent_id = $parent_id;
            
            $node_array[ $i ] = $n;
        }
    }
    
    public static function removeLastIndex( $node_id )
    {
        return preg_replace( '/;(\d)+$/', '', $node_id );
    }

    private $type;                  // asset, group, text
    private $identifier;            // fully qualified identifier
    private $structured_data_nodes; // children of a group
    private $text;
    private $asset_type;
    private $block_id;
    private $block_path;
    private $file_id;
    private $file_path;
    private $multi_line;
    private $page_id;
    private $page_path;
    private $symlink_id;
    private $symlink_path;
    private $recycled;
    private $parent_id;
    private $multiple;  // whether this is a multiple node
    private $required;  // whether value is required
    private $text_type; // type of text, radiobutton, dropdown, and so on
    private $index;     // index of a multiple field
    private $items;     // items string of radio, checkbox, dropdown & selector
    private $radio;     // whether this is a radio
    private $wysiwyg;   // whether this is a wysiwyg
    private $data_definition;
    private $node_map;
}
?>
