<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/30/2014 Added setAsset.
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
<p>An <code>Reference</code> object represents an a reference asset. Only publishable assets (pages, files, and folders) can have references.</p>
<h2>Structure of <code>reference</code></h2>
<pre>reference
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
  referencedAssetId
  referencedAssetPath
  referencedAssetType
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/reference.php">reference.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{ "asset":{
    "reference":{
      "referencedAssetId":"aad0905d7f0000017b71f4bcf6283903",
      "referencedAssetPath":"suny-upstate/PreviewSlide.jpg",
      "referencedAssetType":"file",
      "parentFolderId":"c4389bc17f0000014d7031651f80292e",
      "parentFolderPath":"suny-upstate/templates",
      "lastModifiedDate":"Dec 8, 2016 3:58:49 PM",
      "lastModifiedBy":"wing",
      "createdDate":"Dec 8, 2016 3:58:49 PM",
      "createdBy":"wing",
      "path":"suny-upstate/templates/PreviewSlideRef",
      "siteId":"9c8883d07f00000140b4daea7170b336",
      "siteName":"POPs",
      "name":"PreviewSlideRef",
      "id":"e03b40a27f00000118d3acfc5ebaee02"}},
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class Reference extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::REFERENCE;
    
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

/**
<documentation><description><p>Returns <code>createdBy</code>.</p></description>
<example>echo $r->getCreatedBy(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedBy() : string
    {
        return $this->getProperty()->createdBy;
    }
    
/**
<documentation><description><p>Returns <code>createdDate</code>.</p></description>
<example>echo $r->getCreatedDate(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedDate() : string
    {
        return $this->getProperty()->createdDate;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedBy</code>.</p></description>
<example>echo $r->getLastModifiedBy(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedBy() : string
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedDate</code>.</p></description>
<example>echo $r->getLastModifiedDate(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p>Returns the referenced object. The type of the returned object can be <code>Page</code>, <code>File</code>, or <code>Folder</code>.</p></description>
<example>$r->getReferencedAsset()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getReferencedAsset() : Asset
    {
        return Asset::getAsset( 
            $this->getService(),
            $this->getProperty()->referencedAssetType,
            $this->getProperty()->referencedAssetId );
    }
    
/**
<documentation><description><p>Returns <code>referencedAssetId</code>.</p></description>
<example>echo $r->getReferencedAssetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getReferencedAssetId() : string
    {
        return $this->getProperty()->referencedAssetId;
    }
    
/**
<documentation><description><p>Returns <code>referencedAssetPath</code>.</p></description>
<example>echo $r->getReferencedAssetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getReferencedAssetPath() : string
    {
        return $this->getProperty()->referencedAssetPath;
    }
    
/**
<documentation><description><p>Returns <code>referencedAssetType</code>.</p></description>
<example>echo $r->getReferencedAssetType(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getReferencedAssetType() : string
    {
        return $this->getProperty()->referencedAssetType;
    }
    
/**
<documentation><description><p>Sets the referenced asset, commits the change, and returns
the calling object. Note that in the Cascade back-end references are not editable.
However, this must be allowed when synching between two sites. Since this is the only edit
required by the class, the edit is built into this method, and no <code>edit</code> method is provided.</p></description>
<example>$r->setAsset( 
    $cascade->getAsset( a\Page::TYPE, '96f6e5138b7f0856002a5e11fa547b61' ) );</example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
</documentation>
*/
    public function setAsset( Asset $asset ) : Asset
    {
        $property = $this->getProperty();
        $property->referencedAssetId   = $asset->getId();
        $property->referencedAssetPath = $asset->getPath();
        $property->referencedAssetType = $asset->getType();
        
        $asset                          = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $property;
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        return $this->reloadProperty();
    }
}
?>