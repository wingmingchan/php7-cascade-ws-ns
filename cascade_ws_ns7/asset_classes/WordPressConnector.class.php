<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<description><h2>Introduction</h2>
<p>A <code>WordPressConnector</code> object represents a WordPress connector asset. This class is a sub-class of <a href="/web-services/api/asset-classes/connector"><code>Connector</code></a>.</p>
<h2>Structure of <code>wordPressConnector</code></h2>
<pre>wordPressConnector
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  auth1
  auth2
  url
  verified
  verifiedDate
  connectorParameters (empty)
  connectorContentTypeLinks
    connectorContentTypeLink
      contentTypeId
      contentTypePath
      pageConfigurationId
      pageConfigurationName
      connectorContentTypeLinkParams
        name
        value
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/wp_connector.php">wp_connector.php</a></li></ul></postscript>
</documentation>
*/
class WordPressConnector extends Connector
{
    const DEBUG      = false;
    const TYPE       = c\T::WORDPRESSCONNECTOR;
    const CATEGORIES = "Metadata mapping for categories";
    const TAGS       = "Metadata mapping for tags";
    
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
<documentation><description><p>Sets <code>auth1</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setAuth1( $value ) : Asset
    {
        $this->getProperty()->auth1 = $value;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>auth2</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setAuth2( string $value ) : Asset
    {
        $this->getProperty()->auth2 = $value;
        return $this;
    }
    
/**
<documentation><description><p>Uses the name (either <code>Metadata mapping for
tags</code> or <code>Metadata mapping for categories</code>) and sets its value, both of
which are listed under the content type, and returns the calling object. Possible values
are drawn from the metadata set associated with the content type. They include seven
textual wired fields (like 'author', 'summary' and so on) and all the dynamic field names.</p></description>
<example></example>
<return-type></return-type>
<exception>NullAssetException, UnacceptableValueException, Exception</exception>
</documentation>
*/
    public function setMetadataMapping(
        ContentType $ct, string $name, string $value ) : Asset
    {
        if( $ct == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_CONTENT_TYPE . E_SPAN );
            
        if( $name != self::TAGS && $name != self::CATEGORIES )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The name $name is not acceptable." . E_SPAN );
            
        $links = $this->getConnectorContentTypeLinks();
        
        foreach( $links as $link )
        {
            if( $link->getContentTypeId() == $ct->getId() )
            {
                $link->setMetadataMapping( $name, $value );
                return $this;
            }
        }
        
        throw new \Exception( 
            S_SPAN . "The content does not exist in the connector." . E_SPAN );
    }
    
/**
<documentation><description><p>Sets <code>url</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setUrl( string $u ) : Asset
    {
        if( trim( $u ) == "" )
            throw e\EmptyValueException(
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
            
        $this->getProperty()->url = $u;
        return $this;
    }
}
?>