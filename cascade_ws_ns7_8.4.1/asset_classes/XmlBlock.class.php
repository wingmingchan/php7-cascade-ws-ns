<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<p>An <code>XmlBlock</code> object represents an an xml block asset. This class is a sub-class of <a href=\"/cascade-admin/web-services/api/asset-classes/block.php\"><code>Block</code></a>.</p>
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
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled
  xml
  
JSON:
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
        array( "getComplexTypeXMLByName" => "xmlBlock" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/xml_block.php">xml_block.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "xmlBlock":{
    "xml":"...",
    "expirationFolderRecycled":false,
    "metadataSetId":"4dddf3e58b7f085600a0fcdc06afa7df",
    "metadataSetPath":"_common:Default",
    "metadata":{},
    "parentFolderId":"1f22ab188b7ffe834c5fe91eed1a064a",
    "parentFolderPath":"_cascade/blocks/feed",
    "lastModifiedDate":"Sep 12, 2016 12:01:32 PM",
    "lastModifiedBy":"wing",
    "createdDate":"Sep 12, 2016 12:01:32 PM",
    "createdBy":"wing",
    "path":"_cascade/blocks/feed/xml",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"xml",
    "id":"1f21d1ef8b7ffe834c5fe91e94c764d8"}},
  "success":true
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