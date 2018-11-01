<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/29/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 10/12/2016 Removed folder-related code because it is in Linkable.
  * 9/6/2016 Added expiration folder-related code.
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
<p>A <code>Symlink</code> represents a symlink asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/linkable.php\"><code>Linkable</code></a>.</p>
<h2>Structure of <code>symlink</code></h2>
<pre>SOAP:
symlink
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
  linkURL

REST:
symlink
  linkURL
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
  id   
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "symlink" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/symlink.php">symlink.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/symlink/5045ce7a8b7ffe8353cc17e9559b8b12

{
  "asset":{
    "symlink":{
      "linkURL":"http://www.upstate.edu/president/",
      "expirationFolderRecycled":false,
      "metadataSetId":"618862168b7ffe8377b637e883fd8dcc",
      "metadataSetPath":"_brisk:Symlink",
      "metadata":{
        "displayName":"President\u0027s Office",
        "title":"Link",
        "dynamicFields":[ {
          "name":"exclude-from-menu",
          "fieldValues":[]
        },
        {
          "name":"exclude-from-left-folder-nav",
          "fieldValues":[]
        },
        {
          "name":"exclude-from-mobile-menu",
          "fieldValues":[]
        } ]
      },
      "reviewOnSchedule":false,
      "reviewEvery":60,
      "parentFolderId":"5044ba638b7ffe8353cc17e9188c8096",
      "parentFolderPath":"/",
      "lastModifiedDate":"Jan 23, 2018 11:02:41 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Dec 13, 2017 9:27:20 AM",
      "createdBy":"wing",
      "path":"president",
      "siteId":"5044b9f98b7ffe8353cc17e9f24c362d",
      "siteName":"about",
      "name":"president",
      "id":"5045ce7a8b7ffe8353cc17e9559b8b12"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}</pre>
</postscript>
</documentation>
*/
class Symlink extends Linkable
{
    const DEBUG = false;
    const TYPE  = c\T::SYMLINK;
    
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
<documentation><description><p>Returns <code>linkURL</code>.</p></description>
<example>echo "Link URL: ", $s->getLinkURL() . BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLinkURL() : string
    {
        return $this->getProperty()->linkURL;
    }

/**
<documentation><description><p>Sets <code>linkURL</code>, and returns the calling object.</p></description>
<example>$s->setLinkURL( "http://web.upstate.edu/cascade-training/" )->
    edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setLinkURL( string $url ) : Asset
    {
        $this->getProperty()->linkURL = $url;
        return $this;
    }
}
?>