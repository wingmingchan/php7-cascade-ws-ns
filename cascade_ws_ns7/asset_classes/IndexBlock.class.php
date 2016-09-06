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

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class IndexBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::INDEXBLOCK;
 
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getAppendCallingPageData()
    {
        return $this->getProperty()->appendCallingPageData;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDepthOfIndex()
    {
        return $this->getProperty()->depthOfIndex;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexAccessRights()
    {
        return $this->getProperty()->indexAccessRights;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexBlocks()
    {
        return $this->getProperty()->indexBlocks;
    }
    
    // no setter
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexBlockType()
    {
        return $this->getProperty()->indexBlockType;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedContentTypeId()
    {
        return $this->getProperty()->indexedContentTypeId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedContentTypePath()
    {
        return $this->getProperty()->indexedContentTypePath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedFolderId()
    {
        return $this->getProperty()->indexedFolderId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedFolderPath()
    {
        return $this->getProperty()->indexedFolderPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexedFolderRecycled()
    {
        return $this->getProperty()->indexedFolderRecycled;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexFiles()
    {
        return $this->getProperty()->indexFiles;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexLinks()
    {
        return $this->getProperty()->indexLinks;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexPages()
    {
        return $this->getProperty()->indexPages;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexRegularContent()
    {
        return $this->getProperty()->indexRegularContent;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexSystemMetadata()
    {
        return $this->getProperty()->indexSystemMetadata;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexUserInfo()
    {
        return $this->getProperty()->indexUserInfo;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexUserMetadata()
    {
        return $this->getProperty()->indexUserMetadata;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getIndexWorkflowInfo()
    {
        return $this->getProperty()->indexWorkflowInfo;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMaxRenderedAssets()
    {
        return $this->getProperty()->maxRenderedAssets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageXML()
    {
        return $this->getProperty()->pageXML;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRenderingBehavior()
    {
        return $this->getProperty()->renderingBehavior;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSortMethod()
    {
        return $this->getProperty()->sortMethod;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSortOrder()
    {
        return $this->getProperty()->sortOrder;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isContent()
    {
        return $this->getProperty()->indexBlockType == c\T::CONTENTTYPEINDEX;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isFolder()
    {
        return $this->getProperty()->indexBlockType == c\T::FOLDER;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setAppendCallingPageData( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->appendCallingPageData = $b;
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexAccessRights( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexAccessRights = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexBlocks( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexBlocks = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexFiles( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexFiles = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexedFolderRecycled( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexedFolderRecycled = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexLinks( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexLinks = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexPages( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexPages = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexRegularContent( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexRegularContent = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexSystemMetadata( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexSystemMetadata = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexUserInfo( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexUserInfo = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexUserMetadata( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexUserMetadata = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIndexWorkflowInfo( $b )
    {
        $this->checkBoolean( $b );
        $this->getProperty()->indexWorkflowInfo = $b;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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