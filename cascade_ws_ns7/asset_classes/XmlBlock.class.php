<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 12/27/2017 Updated documentation.
  * 6/30/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 10/24/2016 Added construtor.
  * 5/28/2015 Added namespaces.
  * 8/7/2014 Used SimpleXMLElement in setXML.
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
<p>An <code>XmlBlock</code> object represents an an xml block asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/block.php\"><code>Block</code></a>.</p>
<h2>Structure of <code>xmlBlock</code></h2>
<pre>SOAP:
xmlBlock
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
  reviewOnSchedule
  reviewEvery
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled
  xml
  
REST:
xmlBlock
  xml
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
    dynamicFields (array of stdClass)
      name
      fieldValues (array of stdClass)
        value
  reviewOnSchedule
  reviewEvery
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
        array( "getComplexTypeXMLByName" => "xmlBlock" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/xml_block.php">xml_block.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/block_XML/c12d969c8b7ffe83129ed6d8bf50e2db

{
  "asset":{
    "xmlBlock":{
      "xml":"\u003cscript\u003e\n    \u003ccode\u003e\n#chanwDisplayDocumentation( \"chanw_initialization\" \"core/library/velocity/chanw\" \"_brisk\" )\n    \u003c/code\u003e\n\u003c/script\u003e",
      "expirationFolderRecycled":false,
      "metadataSetId":"618861da8b7ffe8377b637e8ad3dd499",
      "metadataSetPath":"_brisk:Block",
      "metadata":
      {
        "dynamicFields":[ {
          "name":"macro",
          "fieldValues":[ {
            "value":"chanwProcessScript"
          } ]
        } ]
      },
      "reviewOnSchedule":false,
      "reviewEvery":0,
      "parentFolderId":"c12dce7e8b7ffe83129ed6d8b2e9ddb0",
      "parentFolderPath":"_cascade/blocks/script",
      "lastModifiedDate":"Jan 23, 2018 9:03:48 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Nov 15, 2017 2:35:11 PM",
      "createdBy":"wing",
      "path":"_cascade/blocks/script/test-macro",
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "name":"test-macro",
      "id":"c12d969c8b7ffe83129ed6d8bf50e2db"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre></postscript>
</documentation>
*/
class XmlBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::XMLBLOCK;
    
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
<documentation><description><p>Returns <code>xml</code>.</p></description>
<example>echo u\XmlUtility::replaceBrackets( $xb->getXML() ) . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getXML() : string
    {
        return $this->getProperty()->xml;
    }
    
/**
<documentation><description><p>Sets <code>xml</code> and returns the calling object. If <code>$enforce_xml</code> is set to <code>true</code>, then the <code>&amp;xml</code> will be checked for well-formedness.</p></description>
<example>$xb->setXML( $xml )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setXML( string $xml, bool $enforce_xml=false ) : Asset
    {
        if( trim( $xml ) == '' )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
        }
        if( $enforce_xml )
        {
            $xml_obj = new \SimpleXMLElement( $xml );
            $this->getProperty()->xml = $xml_obj->asXML();
        }
        else
        {
            $this->getProperty()->xml = $xml;
        }
        return $this;
    }
}
?>