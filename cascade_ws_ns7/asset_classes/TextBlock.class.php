<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 10/24/2016 Fixed a namespace, added constructor.
  * 5/28/2015 Added namespaces.
 */
 
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>A <code>TextBlock</code> object represents a text block asset. This class is a sub-class of
<a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/block.php"><code>Block</code></a>.</p>
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
  
JSON:
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
<p>WSDL:</p>
<pre>&lt;complexType name="textBlock">
  &lt;complexContent>
    &lt;extension base="impl:block">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="text" type="xsd:string"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/text_block.php">text_block.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{ "asset":{
    "textBlock":{
      "text":"Some text here.",
      "expirationFolderRecycled":false,
      "metadataSetId":"45a6e8db7f00000178d6a41af950de9e",
      "metadataSetPath":"Block",
      "metadata":{
        "displayName":"",
        "title":"",
        "summary":"",
        "teaser":"",
        "keywords":"",
        "metaDescription":"",
        "author":"",
        "dynamicFields":[ {
          "name":"macro",
          "fieldValues":[ {
            "value":"processTextBlock" } ]
          },
          {
            "name":"dummy",
            "fieldValues":[{
              "value":"dummy" } ]
          } ]
      },
      "parentFolderId":"45a78dc77f00000178d6a41a7cd3b6b2",
      "parentFolderPath":"blocks",
      "lastModifiedDate":"Dec 28, 2016 8:40:19 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Dec 28, 2016 8:39:21 AM",
      "createdBy":"wing",
      "path":"blocks/text",
      "siteId":"3e15e3fe0a00016b00677c0a42ef3909",
      "siteName":"wing",
      "name":"text",
      "id":"45a81b127f00000178d6a41acb6004f7"
    }
  },
  "success":true
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