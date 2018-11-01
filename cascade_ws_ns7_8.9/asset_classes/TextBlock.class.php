<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/30/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 10/24/2016 Fixed a namespace, added constructor.
  * 5/28/2015 Added namespaces.
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
<p>A <code>TextBlock</code> object represents a text block asset. This class is a sub-class of
<a href=\"http://www.upstate.edu/web-services/api/asset-classes/block.php\"><code>Block</code></a>.</p>
<h2>Structure of <code>textBlock</code></h2>
<pre>SOAP:
textBlock
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
  expirationFolderRecycled
  text
  
REST:
textBlock
  text
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled
  metadataSetId
  metadataSetPath
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
    dynamicFields (array)
      stdClass
        name
        fieldValues (array)
          stdClass
            value
  parentFolderId
  parentFolderPath
  lastModifiedDate
  lastModifiedBy
  createdDate
  createdBy
  path
  siteId
  siteName
  name
  id</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "textBlock" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/text_block.php">text_block.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/block_TEXT/089c28d98b7ffe83785cac8a79fe2145

{
  "asset":{
    "textBlock":{
      "text":"My new text block content",
      "expirationFolderRecycled":false,
      "metadataSetId":"618861da8b7ffe8377b637e8ad3dd499",
      "metadataSetPath":"_brisk:Block",
      "metadata":{
        "author":null,
        "displayName":null,
        "endDate":null,
        "keywords":null,
        "metaDescription":null,
        "reviewDate":null,
        "startDate":null,
        "summary":null,
        "teaser":null,
        "title":null,
        "dynamicFields":[ {
          "name":"macro",
          "fieldValues":[]
        } ]
      },
      "reviewOnSchedule":false,
      "reviewEvery":0,
      "parentFolderId":"c12dce3c8b7ffe83129ed6d8f4f9b820",
      "parentFolderPath":"_cascade\/blocks",
      "lastModifiedDate":"Jan 23, 2018 8:20:41 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Nov 29, 2017 11:28:59 AM",
      "createdBy":"wing",
      "path":"_cascade\/blocks\/hello",
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "name":"hello",
      "id":"089c28d98b7ffe83785cac8a79fe2145"
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
class TextBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::TEXTBLOCK;
    
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
<documentation><description><p>Returns <code>text</code>.</p></description>
<example>echo $tb->getText() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getText() : string
    {
        return $this->getProperty()->text;
    }
    
/**
<documentation><description><p></p></description>
<example>$tb->setText( $text )->edit()->dump();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setText( string $text ) : Asset
    {
        if( trim( $text ) == '' )
        {
            throw new e\EmptyValueException( S_SPAN . c\M::EMPTY_TEXT . E_SPAN );
        }
        
        $this->getProperty()->text = $text;
        return $this;
    }
}
?>