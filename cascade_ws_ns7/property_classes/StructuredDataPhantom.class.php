<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/26/2016 Added mapData.
  * 1/8/2016 Added code to deal with host asset.
  * 9/15/2015 Added createNInstancesForMultipleField.
  * 5/28/2015 Added namespaces.
  * 5/1/2015 Added if statements to various get and set methods.
  *   Reason: to loosen the restriction related to phantom nodes.
  * 2/24/2015 Added getPossibleValues.
  * 9/25/2014 Added isMultiLineNode.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_asset as a;

class StructuredDataPhantom extends Property
{
    const DEBUG = false;

    public function __construct( 
        \stdClass $sd=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data_definition_id=NULL,
        $data2=NULL, 
        $data3=NULL )
    {
        // a data definition block will have a data definition id in the sd object
        // a page will need to pass into the data definition id
        if( isset( $sd ) )
        {
            // store the data
            if( isset( $sd->definitionId ) )
            {
                $this->definition_id = $sd->definitionId;
                $this->type = a\DataDefinitionBlock::TYPE;
            }
            else if( isset( $data_definition_id ) )
            {
                $this->definition_id = $data_definition_id;
                $this->type = a\Page::TYPE;
            }
                
            if( isset( $sd->definitionPath ) )
                $this->definition_path = $sd->definitionPath;
            // initialize the arrays
            $this->children        = array();
            $this->node_map        = array();
            
            // store the data definition
            $this->data_definition = new a\DataDefinition( 
                $service, $service->createId( a\DataDefinition::TYPE, $this->definition_id ) );
            // turn structuredDataNode into an array
            if( isset( $sd->structuredDataNodes->structuredDataNode ) && !is_array( $sd->structuredDataNodes->structuredDataNode ) )
            {
                $child_nodes = array( $sd->structuredDataNodes->structuredDataNode );
            }
            elseif( isset( $sd->structuredDataNodes->structuredDataNode ) )
            {
                $child_nodes = $sd->structuredDataNodes->structuredDataNode;
                if( self::DEBUG ) { u\DebugUtility::out( "Number of nodes in std: " . count( $child_nodes ) ); }
            }
            // convert stdClass to objects
            StructuredDataNodePhantom::processStructuredDataNodePhantom( 
                '', $this->children, $child_nodes, $this->data_definition );
        }
        
        $this->node_map    = $this->getIdentifierNodeMap();
        $this->identifiers = array_keys( $this->node_map );
        $this->host_asset  = $data2;
        $this->service     = $service;
        
        if( self::DEBUG ) { u\DebugUtility::out( "First node ID: " . $first_node_id ); }
    }
    
    public function appendSibling( $first_node_id )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "First node ID: " . $first_node_id ); }
        
        // if this is a multiple field, there must be a first node
        if( !$this->hasIdentifier( $first_node_id ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $first_node_id does not exist." . E_SPAN );
        }
        
        $first_node = $this->node_map[ $first_node_id ];
        $field_id   = StructuredDataNodePhantom::getFieldIdentifier( $first_node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Field ID: " . $field_id ); }
        
        // non-ambiguous path, no multipled-parent
        // no ;digits in the identifier
        if( strpos( $first_node_id, $field_id ) !== false )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "non_ambiguous" ); }
            return $this->appendNodeToField( $field_id );
        }
        
        // ambiguous, with multiple ancestors
        $parent_id   = $first_node->getParentId();
        $parent_node = $this->getNode( $parent_id );
        
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $parent_id ); }
        $shared_id = StructuredDataNodePhantom::removeLastIndex( $first_node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Shared ID: " . $shared_id ); }

        $parent_node->addChildNode( $first_node_id );
        
        $temp = $this->node_map;
        asort( $temp );
        $this->identifiers = array_keys( $temp );

        return $this;
    }
    
    public function createNInstancesForMultipleField( $number, $identifier )
    {
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
        /*
        processStructuredDataNodePhantom left out intentionally to allow loading of the block/page.
        */
        return $this;
    }
    
    public function getAssetNodeType( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        $node = $this->node_map[ $identifier ];
        
        if( $node->getType() != c\T::ASSET )
        {
            throw new e\NodeException(
                S_SPAN .  "This node is not an asset node." . E_SPAN );
        }

        return $node->getAssetType();
    }
    
    public function getBlockId( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getBlockId();
    }
    
    public function getBlockPath( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getBlockPath();
    }
    
    public function getDataDefinition()
    {
        return $this->data_definition;
    }
    
    public function getDefinitionId()
    {
        return $this->definition_id;
    }
    
    public function getDefinitionPath()
    {
        return $this->definition_path;
    }
    
    public function getFileId( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getFileId();
    }
    
    public function getFilePath( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getFilePath();
    }
    
    public function getHostAsset()
    {
        return $this->host_asset;
    }
    
    public function getIdentifierNodeMap()
    {
        foreach( $this->children as $child )
        {
            $this->node_map = array_merge( 
                $this->node_map, $child->getIdentifierNodeMap() );
        }
        
        return $this->node_map;
    }
    
    public function getIdentifiers()
    {
        $temp = $this->identifiers;
        return array_merge( $temp, StructuredDataNodePhantom::getPhantomIdentifiers() );
    }
    
    public function getLinkableId( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getLinkableId();
    }
    
    public function getLinkablePath( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getLinkablePath();
    }
    
    public function getNode( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }

        return $this->node_map[ $identifier ];
    }
    
    public function getNodeType( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }

        return $this->node_map[ $identifier ]->getType();
    }
    
    public function getNumberOfChildren()
    {
        return count( $this->children );
    }
    
    public function getNumberOfSiblings( $node_name )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Node ID: " . $node_name ); }
        $par_id     = $this->node_map[ $node_name ]->getParentId();
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $par_id ); }

        if( !in_array( $node_name, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $node_name does not exist" . E_SPAN );
        }
        
        if( $par_id != '' )
        {
            $siblings = $this->node_map[ $par_id ]->getChildren();
        }
        else
        {
            $siblings = $this->children;
        }
        
        // remove ;0
        $field_id = StructuredDataNodePhantom::removeLastIndex( $node_name );
        if( self::DEBUG ) { u\DebugUtility::out( "Field ID: " . $field_id ); }
        
        $last_sibling_index = StructuredDataNodePhantom::getPositionOfLastNode( $siblings, $field_id );
        $last_id  = $siblings[ $last_sibling_index ]->getIdentifier();
        
        return StructuredDataNodePhantom::getLastIndex( $last_id ) + 1;
    }
    
    public function getPageId( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getPageId();
    }
    
    public function getPagePath( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getPagePath();
    }
    
    public function getPossibleValues( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getPossibleValues();
    }
    
    public function getService()
    {
        return $this->service;
    }
    
    public function getSymlinkId( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getSymlinkId();
    }
    
    public function getSymlinkPath( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getSymlinkPath();
    }
    
    public function getStructuredDataNodePhantom( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ];
    }
    
    public function getText( $node_name )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            return $this->node_map[ $node_name ]->getText();
    }
    
    public function getTextNodeType( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        $node = $this->node_map[ $identifier ];
        
        if( $node->getType() != c\T::TEXT )
        {
            throw new e\NodeException( 
                S_SPAN . "This node is not a text node." . E_SPAN );
        }

        return $node->getTextType();
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function hasIdentifier( $identifier )
    {
        return $this->hasNode( $identifier );
    }
    
    public function hasNode( $identifier )
    {
        return in_array( $identifier, $this->identifiers );
    }
    
    public function isAssetNode( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isAssetNode();
    }
    
    public function isIdentifierOfFirstNode( $identifier )
    {
        if( $this->isMultiple( $identifier ) )
        {
            return u\StringUtility::endsWith( $identifier, ";0" );
        }
        return false;
    }
    
    public function isGroupNode( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isGroupNode();
    }
    
    public function isMultiLineNode( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isMultiLineNode();
    }
    
    public function isMultiple( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isMultiple();
    }
    
    public function isRequired( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isRequired();
    }

    public function isTextNode( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isTextNode();
    }
    
    public function isWYSIWYG( $identifier )
    {
        if( !in_array( $identifier, $this->identifiers ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $identifier does not exist" . E_SPAN );
        }
        
        return $this->node_map[ $identifier ]->isWYSIWYG();
    }
    
    /*
    mapData left out intentionally.
    */
    
    public function removeLastSibling( $first_node_id )
    {
        if( !$this->hasIdentifier( $first_node_id ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $first_node_id does not exist." . E_SPAN );
        }
        
        $first_node = $this->node_map[ $first_node_id ];
        $field_id   = StructuredDataNodePhantom::getFieldIdentifier( $first_node_id );
        
        // non-ambiguous path, no multiple ancestor
        if( strpos( $first_node_id, $field_id ) !== false )
        {
            return $this->removeLastNodeFromField( $field_id );
        }
        // with multiple ancestor
        $parent_id   = $first_node->getParentId();
        $parent_node = $this->node_map[ $parent_id ];
        
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: " . $parent_id ); }
        $shared_id = StructuredDataNodePhantom::removeLastIndex( $first_node_id );
        if( self::DEBUG ) { u\DebugUtility::out( "Shared ID: " . $shared_id ); }
        
        $shared_id = StructuredDataNodePhantom::removeLastIndex( $first_node_id );
        $parent_node->removeLastChildNode( $shared_id );
        $this->identifiers = array_keys( $this->node_map );

        return $this;
    }
    
    public function replaceByPattern( $pattern, $replace, $include=NULL )
    {
        $check = false;
        
        if( is_array( $include ) )
        {
            $check = true;
        }
        
        foreach( $this->identifiers as $identifier )
        {
            if( $check && !in_array( $identifier, $include ) )
            {
                continue; // skip this one
            }
            
            $cur_node = $this->node_map[ $identifier ];
        
            $current_text = $cur_node->getText();
        
            // including WYSIWYG
            if( $cur_node->getType() == c\T::TEXT &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_CHECKBOX &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_DROPDOWN &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_RADIO &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_SELECTOR
            )
            {
                $new_text = preg_replace( $pattern, $replace, $current_text );
                
                $this->setText(
                    $identifier,
                    $new_text
                );
            }
        }
        return $this;
    }
    
    public function replaceText( $search, $replace, $include=NULL )
    {
        $check = false;
        
        if( is_array( $include ) )
        {
            $check = true;
        }
        
        foreach( $this->identifiers as $identifier )
        {
            if( $check && !in_array( $identifier, $include ) )
            {
                continue; // skip this one
            }
            
            $cur_node = $this->node_map[ $identifier ];
        
            $current_text = $cur_node->getText();
        
            // including WYSIWYG
            if( $cur_node->getType() == c\T::TEXT &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_CHECKBOX &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_DROPDOWN &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_RADIO &&
                $cur_node->getTextType() != StructuredDataNodePhantom::TEXT_TYPE_SELECTOR &&
                strpos( $current_text, $search ) !== false )
            {
                $new_text = str_replace( $search, $replace, $current_text );
                
                $this->setText( 
                    $identifier,
                    $new_text
                );
            }
        }
        return $this;
    }
    
    public function searchText( $string )
    {
        $identifiers = array();
        
        foreach( $this->identifiers as $identifier )
        {
            $cur_node = $this->node_map[ $identifier ];
        
            if( $cur_node->getType() == c\T::TEXT && 
                strpos( $cur_node->getText(), $string ) !== false )
            {
                $identifiers[] = $identifier;
            }
        }
        return $identifiers;
    }
    
    public function searchWYSIWYG( $pattern )
    {
        $identifiers = array();
        
        foreach( $this->identifiers as $identifier )
        {
            $cur_node = $this->node_map[ $identifier ];
        
            // only one instance is enough, hence preg_match, not preg_match_all
            if( $cur_node->getType() == c\T::TEXT &&
                $cur_node->isWYSIWYG() &&
                preg_match( $pattern, $cur_node->getText() ) == 1 )
            {
                $identifiers[] = $identifier;
            }
        }
        return $identifiers;
    }
    
    public function setBlock( $node_name, a\Block $block=NULL )
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $block ); }
        if( self::DEBUG ) { u\DebugUtility::out( $node_name ); }
        
        if( isset( $this->node_map[ $node_name ] ) )
            $this->node_map[ $node_name ]->setBlock( $block );
        return $this;
    }
    
    public function setDataDefinition( a\DataDefinition $dd )
    {
        $this->definition_id   = $dd->getId();
        $this->definition_path = $dd->getPath();
        return $this;
    }
    
    public function setFile( $node_name, a\File $file=NULL )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            $this->node_map[ $node_name ]->setFile( $file );
        return $this;
    }
    
    public function setLinkable( $node_name, a\Linkable $linkable=NULL )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            $this->node_map[ $node_name ]->setLinkable( $linkable );
        return $this;
    }
    
    public function setPage( $node_name, a\Page $page=NULL )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            $this->node_map[ $node_name ]->setPage( $page );
        return $this;
    }
    
    public function setSymlink( $node_name, a\Symlink $symlink=NULL )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            $this->node_map[ $node_name ]->setSymlink( $symlink );
        return $this;
    }
    
    public function setText( $node_name, $text )
    {
        if( isset( $this->node_map[ $node_name ] ) )
            $this->node_map[ $node_name ]->setText( $text );
        return $this;
    }
    
    public function swapData( $node_name1, $node_name2 )
    {
        if( !$this->hasIdentifier( $node_name1 ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $node_name1 does not exists." . E_SPAN );
        }
        
        if( !$this->hasIdentifier( $node_name2 ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The node $node_name2 does not exists." . E_SPAN );
        }
        
        // must be siblings
        if( StructuredDataNodePhantom::removeLastIndex( $node_name1 ) != 
            StructuredDataNodePhantom::removeLastIndex( $node_name2 ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The nodes $node_name1 and $node_name2 are not siblings." . E_SPAN );
        }
        
        $par_id     = $this->node_map[ $node_name1 ]->getParentId();
        // get the data
        $node1_data = $this->node_map[ $node_name1 ]->toStdClass();
        $node2_data = $this->node_map[ $node_name2 ]->toStdClass();
        
        if( self::DEBUG ) { u\DebugUtility::out( "Parent ID: $par_id" . 
            BR . "Node 1: $node_name1" . BR . "Node 2: $node_name2" ); }
        
        if( $par_id != '' )
            $siblings = $this->node_map[ $par_id ]->getChildren();
        else
            $siblings = $this->children;
            
        $sibling_count = count( $siblings );
        
        if( self::DEBUG ) { u\DebugUtility::out( "Sibling count: $sibling_count" ); }
        
        for( $i = 0; $i < $sibling_count; $i++ )
        {
            if( self::DEBUG ) { u\DebugUtility::out( $siblings[ $i ]->getIdentifier() ); }
            
            // find the two positions
            if( $siblings[ $i ]->getIdentifier() == $node_name1 )
            {
                $node_pos1 = $i;
                if( self::DEBUG ) { u\DebugUtility::out( "Node 1 position: $node_pos1" ); }
            }
            if( $siblings[ $i ]->getIdentifier() == $node_name2 )
            {
                $node_pos2 = $i;
                if( self::DEBUG ) { u\DebugUtility::out( "Node 2 position: $node_pos2" . BR ); }
            }
        }
        
        // create new nodes
        $new_node1 = new StructuredDataNodePhantom( 
            $node2_data, NULL, $this->data_definition, $node_pos1, $par_id . structuredDataNode::DELIMITER );
        $new_node2 = new StructuredDataNodePhantom( 
            $node1_data, NULL, $this->data_definition, $node_pos2, $par_id . structuredDataNode::DELIMITER );
        
        // insert new nodes
        // must assign new nodes to the original arrays, not $siblings
        if( $par_id != '' )
        {
            $this->node_map[ $par_id ]->swapChildren( 
                $node_pos1, $new_node1, $node_pos2, $new_node2 );
        }
        else
        {
            $this->children[ $node_pos1 ] = $new_node1;
            $this->children[ $node_pos2 ] = $new_node2;
        }
        
        $this->node_map[ $node_name1 ] = $new_node1;
        $this->node_map[ $node_name2 ] = $new_node2;
        
        return $this;
    }
    
    public function toStdClass()
    {
        $obj = new \stdClass();
        
        if( $this->type == a\DataDefinitionBlock::TYPE )
        {
            $obj->definitionId   = $this->definition_id;
            $obj->definitionPath = $this->definition_path;
        }
        
        $child_count = count( $this->children );
        
        if( self::DEBUG ) { u\DebugUtility::out( "child count: $child_count" ); }
        
        if( $child_count == 1 )
        {
            $obj->structuredDataNodes                     = new \stdClass();
            $obj->structuredDataNodes->structuredDataNode = $this->children[0]->toStdClass();
        }
        else
        {
            $obj->structuredDataNodes                     = new \stdClass();
            $obj->structuredDataNodes->structuredDataNode = array();
            
            for( $i = 0; $i < $child_count; $i++ )
            {
                $obj->structuredDataNodes->structuredDataNode[] = $this->children[$i]->toStdClass();
            }
        }
        return $obj;
    }
    
    private function appendNodeToField( $field_name )
    {
        if( self::DEBUG ) { u\DebugUtility::out( $field_name ); }

        if( !$this->data_definition->hasIdentifier( $field_name ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field name $field_name does not exist." . E_SPAN );
        }
        
        if( !$this->data_definition->isMultiple( $field_name ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The field $field_name is not multiple." . E_SPAN );
        }
        
        // get the parent id through the first node
        // alternative: use the field name to work out the parent id
        $first_node = $this->getNode( $field_name . a\DataDefinition::DELIMITER . '0' );
        $par_id     = $first_node->getParentId();
        
        if( $par_id == '' ) // top level
        {
            $child_count = count( $this->children );
            //$first_pos   = StructuredDataNodePhantom::getPositionOfFirstNode( $this->children, $field_name );
            $last_pos    = StructuredDataNodePhantom::getPositionOfLastNode( $this->children, $field_name );
            $cloned_node = $this->children[ $last_pos ]->cloneNode();

            if( $child_count > $last_pos + 1 ) // in the middle
            {
                $before = array_slice( $this->children, 0, $last_pos + 1 );
                $after  = array_slice( $this->children, $last_pos + 1 );
                $this->children = array_merge( $before, array( $cloned_node ), $after );
            }
            else // the last one
            {
                $this->children[] = $cloned_node;
            }
            
            // add new node to map
            $this->node_map = array_merge( 
                $this->node_map, array( $cloned_node->getIdentifier() => $cloned_node ) );
        }
        else
        {
            $this->getNode( $par_id )->addChildNode( $field_name );
        }
        // add new identifier to identifiers
        $temp = $this->node_map;
        asort( $temp );
        $this->identifiers = array_keys( $temp );

        return $this;
    }
    
    private function removeLastNodeFromField( $field_name )
    {
        if( !$this->data_definition->hasIdentifier( $field_name ) )
        {
            throw new e\NoSuchFieldException( 
                S_SPAN . "The field name $field_name does not exist." . E_SPAN );
        }
        
        if( !$this->data_definition->isMultiple( $field_name ) )
        {
            throw new e\NodeException( 
                S_SPAN . "The field $field_name is not multiple." . E_SPAN );
        }
    
        $first_node = $this->getNode( $field_name . a\DataDefinition::DELIMITER . '0' );
        $par_id     = $first_node->getParentId();

        if( $par_id == '' ) // top level
        {
            $last_pos  = StructuredDataNodePhantom::getPositionOfLastNode( $this->children, $field_name );
            $first_pos = StructuredDataNodePhantom::getPositionOfFirstNode( $this->children, $field_name );
            $last_id   = $this->children[ $last_pos ]->getIdentifier();
            
            if( $first_pos == $last_pos ) // the only node
            {
                throw new e\NodeException( 
                    S_SPAN . "Cannot remove the only node in the field." . E_SPAN );
            }
            
            $child_count = count( $this->children );
            
            if( $child_count > $last_pos )
            {
                $before = array_slice( $this->children, 0, $last_pos );
                $after = array_slice( $this->children, $last_pos + 1 );
                $this->children = array_merge( $before, $after );
            }
        }
        else
        {
            $this->getNode( $par_id )->removeLastChildNode( $field_name );
        }
        
        unset( $this->node_map[ $last_id ] );
        $this->identifiers = array_keys( $this->node_map );

        return $this;
    }    

    private $definition_id;
    private $definition_path;
    private $children;
    private $identifiers;
    private $data_definition;
    private $node_map;
    private $type; // block or page
    private $host_asset;
    private $service;
}
?>