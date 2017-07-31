<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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

JSON:
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
<pre>
{ "asset":{
    "symlink":{
      "linkURL":"http://help.hannonhill.com/discussions/product-feedback",
      "expirationFolderRecycled":false,
      "metadataSetId":"f7a963087f0000012693e3d9b68e9e1d",
      "metadataSetPath":"Default",
      "metadata":{},
      "parentFolderId":"f7a9630b7f0000012693e3d99c134cef",
      "parentFolderPath":"/",
      "lastModifiedDate":"Apr 8, 2016 3:56:27 PM",
      "lastModifiedBy":"tim.reilly",
      "createdDate":"Apr 8, 2016 3:56:27 PM",
      "createdBy":"tim.reilly",
      "path":"GIVE US FEEDBACK",
      "siteId":"f7a963087f0000012693e3d9932e44ba",
      "siteName":"SUNY Upstate",
      "name":"GIVE US FEEDBACK",
      "id":"f7a967857f0000012693e3d97239afde"}},
  "success":true
}
</pre>
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