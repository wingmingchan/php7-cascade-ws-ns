<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/27/2017 Updated documentation.
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/12/2017 Added JSON structure and JSON dump.
  * 10/24/2016 Added construtor.
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
<p>A <code>FeedBlock</code> object represents a feed block asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/block.php\"><code>Block</code></a>. Note that only the feed URL of a feed block is retrievable. The feed XML is not. In order to retrieve the feed XML, a feed block must be attached to a page and published. Once the page is published, the XML can be read directly from the page.</p>
<h2>Structure of <code>feedBlock</code></h2>
<pre>SOAP:
feedBlock
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
  feedURL
  
JSON:
feedBlock
  feedURL
  expirationFolderId
  expirationFolderPath
  expirationFolderRecycled (bool)
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
  id
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "feedBlock" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/feed_block.php">feed_block.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "feedBlock":{
    "feedURL":"http://www.upstate.edu/news/",
    "expirationFolderRecycled":false,
    "metadataSetId":"358be6af8b7ffe83164c9314f9a3c1a6",
    "metadataSetPath":"_common_assets:Block",
    "metadata":{
      "dynamicFields":[ {
        "name":"macro",
        "fieldValues":[ { "value":"" } ] } ] },
    "parentFolderId":"1f22ab188b7ffe834c5fe91eed1a064a",
    "parentFolderPath":"_cascade/blocks/feed",
    "lastModifiedDate":"Sep 12, 2016 12:01:57 PM",
    "lastModifiedBy":"wing",
    "createdDate":"Sep 12, 2016 12:01:57 PM",
    "createdBy":"wing",
    "path":"_cascade/blocks/feed/hannonhill-sandbox-wing-asset",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"hannonhill-sandbox-wing-asset",
    "id":"1f22332a8b7ffe834c5fe91e33ecd4c7"}},
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class FeedBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::FEEDBLOCK;
    
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
<documentation><description><p>Returns <code>feedURL</code>.</p></description>
<example>echo "Feed URL: " . $fb->getFeedURL() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getFeedURL() : string
    {
        return $this->getProperty()->feedURL;
    }
    
/**
<documentation><description><p>Sets <code>feedURL</code>, and returns the calling
object.</p></description>
<example>$fb->setFeedURL( $url )->edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setFeedURL( $url ) : Asset
    {
        if( trim( $url ) == '' )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
        }
        
        $this->getProperty()->feedURL = $url;
        return $this;
    }
}
?>