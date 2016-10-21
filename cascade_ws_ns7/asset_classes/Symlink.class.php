<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 10/12/2016 Removed folder-related code because it is in Linkable.
  * 9/6/2016 Added expiration folder-related code.
  * 5/28/2015 Added namespaces.
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
<p>A <code>Symlink</code> represents a symlink asset. This class is a sub-class of <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/linkable.php"><code>Linkable</code></a>.</p>
<h2>Structure of <code>symlink</code></h2>
<pre>symlink
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
</pre></description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/symlink.php">symlink.php</a></li></ul></postscript>
</documentation>
*/
class Symlink extends Linkable
{
    const DEBUG = false;
    const TYPE  = c\T::SYMLINK;
    
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
    public function setLinkURL( $url ) : Asset
    {
        $this->getProperty()->linkURL = $url;
        return $this;
    }
}
?>