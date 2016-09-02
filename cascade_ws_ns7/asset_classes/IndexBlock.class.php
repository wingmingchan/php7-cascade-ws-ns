<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/15/2014 Added getContentType and getFolder.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class IndexBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::INDEXBLOCK;
 
    public function getAppendCallingPageData()
    {
        return $this->getProperty()->appendCallingPageData;
    }
    
    public function getContentType()
    {
        if( $this->isContent() && $this->getIndexedContentTypeId() != NULL )
        {
            $service = $this->getService();
            return new ContentType( 
                $service, $service->createId( ContentType::TYPE, $this->getIndexedContentTypeId() ) );
        }
        return NULL;
    }
    
    public function getDepthOfIndex()
    {
        return $this->getProperty()->depthOfIndex;
    }
    
    public function getFolder()
    {
        if( $this->isFolder() && $this->getIndexedFolderId() != NULL )
        {
            $service = $this->getService();
            if( self::DEBUG ) { u\DebugUtility::out( "Returning folder" . "ID " . $this->getIndexedFolderPath() ); }
            return new Folder( 
                $service, $service->createId( Folder::TYPE, $this->getIndexedFolderId() ) );
        }
        return NULL;
    }
    
    public function getIndexAccessRights()
    {
        return $this->getProperty()->indexAccessRights;
    }
    
    public function getIndexBlocks()
    {
        return $this->getProperty()->indexBlocks;
    }
    
    // no setter
    public function getIndexBlockType()
    {
        return $this->getProperty()->indexBlockType;
    }
    
    public function getIndexedContentTypeId()
    {
        return $this->getProperty()->indexedContentTypeId;
    }
    
    public function getIndexedContentTypePath()
    {
        return $this->getProperty()->indexedContentTypePath;
    }
    
    public function getIndexedFolderId()
    {
        return $this->getProperty()->indexedFolderId;
    }
    
    public function getIndexedFolderPath()
    {
        return $this->getProperty()->indexedFolderPath;
    }
    
    public function getIndexedFolderRecycled()
    {
        return $this->getProperty()->indexedFolderRecycled;
    }
    
    public function getIndexFiles()
    {
        return $this->getProperty()->indexFiles;
    }
    
    public function getIndexLinks()
    {
        return $this->getProperty()->indexLinks;
    }
    
    public function getIndexPages()
    {
        return $this->getProperty()->indexPages;
    }
    
    public function getIndexRegularContent()
    {
        return $this->getProperty()->indexRegularContent;
    }
    
    public function getIndexSystemMetadata()
    {
        return $this->getProperty()->indexSystemMetadata;
    }
    
    public function getIndexUserInfo()
    {
        return $this->getProperty()->indexUserInfo;
    }
    
    public function getIndexUserMetadata()
    {
        return $this->getProperty()->indexUserMetadata;
    }
    
    public function getIndexWorkflowInfo()
    {
        return $this->getProperty()->indexWorkflowInfo;
    }
    
    public function getMaxRenderedAssets()
    {
        return $this->getProperty()->maxRenderedAssets;
    }
    
    public function getPageXML()
    {
        return $this->getProperty()->pageXML;
    }
    
    public function getRenderingBehavior()
    {
        return $this->getProperty()->renderingBehavior;
    }
    
    public function getSortMethod()
    {
        return $this->getProperty()->sortMethod;
    }
    
    public function getSortOrder()
    {
        return $this->getProperty()->sortOrder;
    }
    
    public function isContent()
    {
        return $this->getProperty()->indexBlockType == c\T::CONTENTTYPEINDEX;
    }
    
    public function isFolder()
    {
        return $this->getProperty()->indexBlockType == c\T::FOLDER;
    }
    
    public function setAppendCallingPageData( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->appendCallingPageData = $b;
        
        return $this;
    }
    
    public function setContentType( ContentType $content_type )
    {
        if( $this->getIndexBlockType() != c\T::CONTENTTYPEINDEX )
        {
            throw new \Exception( 
                S_SPAN . "This block is not a content type index block." . E_SPAN );
        }
    
        $this->getProperty()->indexedContentTypeId   = $content_type->getId();
        $this->getProperty()->indexedContentTypePath = $content_type->getPath();
        return $this;
    }
    
    public function setDepthOfIndex( $num )
    {
        if( intval( $num ) < 1 )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $num is unacceptable." . E_SPAN );
        }
        
        if( $this->getIndexBlockType() != Folder::TYPE )
        {
            throw new \Exception( 
                S_SPAN . "This block is not a folder index block." . E_SPAN );
        }
        
        $this->getProperty()->depthOfIndex = $num;
        return $this;
    }
    
    public function setFolder( Folder $folder )
    {
        if( $this->getIndexBlockType() != Folder::TYPE )
        {
            throw new \Exception( 
                S_SPAN . "This block is not a folder index block." . E_SPAN );
        }
    
        $this->getProperty()->indexedFolderId = $folder->getId();
        $this->getProperty()->indexedFolderPath = $folder->getPath();
        return $this;
    }
    
    public function setIndexAccessRights( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexAccessRights = $b;
        return $this;
    }
    
    public function setIndexBlocks( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexBlocks = $b;
        return $this;
    }
    
    public function setIndexFiles( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexFiles = $b;
        return $this;
    }
    
    public function setIndexedFolderRecycled( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexedFolderRecycled = $b;
        return $this;
    }
    
    public function setIndexLinks( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexLinks = $b;
        return $this;
    }
    
    public function setIndexPages( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexPages = $b;
        return $this;
    }
    
    public function setIndexRegularContent( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexRegularContent = $b;
        return $this;
    }
    
    public function setIndexSystemMetadata( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexSystemMetadata = $b;
        return $this;
    }
    
    public function setIndexUserInfo( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexUserInfo = $b;
        return $this;
    }
    
    public function setIndexUserMetadata( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexUserMetadata = $b;
        return $this;
    }
    
    public function setIndexWorkflowInfo( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexWorkflowInfo = $b;
        return $this;
    }
    
    public function setMaxRenderedAssets( $num )
    {
        if( intval( $num ) < 0 )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $num is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->maxRenderedAssets = $num;
        return $this;
    }
    
    public function setPageXML( $page_xml )
    {
        if( $page_xml != c\T::NORENDER &&
            $page_xml != c\T::RENDER &&
            $page_xml != c\T::RENDERCURRENTPAGEONLY
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The pageXML $page_xml is unacceptable." . E_SPAN );
        }
    
        $this->getProperty()->pageXML = $page_xml;
        return $this;
    }
    
    public function setRenderingBehavior( $behavior )
    {
        if( $behavior != c\T::RENDERNORMALLY &&
            $behavior != c\T::HIERARCHY &&
            $behavior != c\T::HIERARCHYWITHSIBLINGS &&
            $behavior != c\T::HIERARCHYSIBLINGSFORWARD
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The behavior $behavior is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->renderingBehavior = $behavior;
        return $this;
    }
    
    public function setSortMethod( $method )
    {
        if( $method != c\T::FOLDERORDER &&
            $method != c\T::ALPHABETICAL &&
            $method != c\T::LASTMODIFIEDDATE &&
            $method != c\T::CREATEDDATE
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The method $method is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->sortMethod = $method;
        return $this;
    }
    
    public function setSortOrder( $order )
    {
        if( $order != c\T::ASCENDING &&
            $order != c\T::DESCENDING
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The order $order is unacceptable." . E_SPAN );
        }
        
        $this->getProperty()->sortOrder = $order;
        return $this;
    }
    
    private function checkBoolean( $b )
    {
        if( !c\BooleanValues::isBoolean( $b ) )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $b is not a boolean." . E_SPAN );
        }
    }
}
?>