<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/28/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/12/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
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
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
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
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "reference" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/reference.php">reference.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/reference/28c750538b7ffe8343b94c28494a5cf8

{
  "asset":{
    "reference":{
      "referencedAssetId":"0fba8a9c8b7ffe8343b94c28b76ed48a",
      "referencedAssetPath":"index",
      "referencedAssetType":"page",
      "parentFolderId":"0fa6f6fc8b7ffe8343b94c282bf4e100",
      "parentFolderPath":"/",
      "lastModifiedDate":"Jan 24, 2018 10:26:46 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Jan 24, 2018 10:26:46 AM",
      "createdBy":"wing",
      "path":"index-ref",
      "siteId":"0fa6f6f18b7ffe8343b94c28251e132e",
      "siteName":"about-test",
      "name":"index-ref",
      "id":"28c750538b7ffe8343b94c28494a5cf8"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
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