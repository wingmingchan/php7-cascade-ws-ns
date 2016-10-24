<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<pre>textBlock
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
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/text_block.php">text_block.php</a></li></ul></postscript>
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