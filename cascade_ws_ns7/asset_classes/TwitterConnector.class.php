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
<p>A <code>TwitterConnector</code> object represents a Twitter connector asset. This class is a sub-class of <a href="/web-services/api/asset-classes/connector"><code>Connector</code></a>.</p>
<h2>Structure of <code>twitterConnector</code></h2>
<pre>twitterConnector
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
  connectorParameters
    connectorParameter
      name
      value
  connectorContentTypeLinks
    connectorContentTypeLink
      contentTypeId
      contentTypePath
      pageConfigurationId
      pageConfigurationName
      connectorContentTypeLinkParams
  destinationId
  destinationPath
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/twitter_connector.php">twitter_connector.php</a></li></ul></postscript>
</documentation>
*/
class TwitterConnector extends Connector
{
    const DEBUG    = false;
    const TYPE     = c\T::TWITTERCONNECTOR;
    const HASHTAGS = "Hash Tags";
    const PREFIX   = "Prefix";
    
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
<documentation><description><p>Returns <code>destinationId</code>.</p></description>
<example>echo $tc->getDestinationId(), BR;</example>
<return-type>string </return-type>
<exception></exception>
</documentation>
*/
    public function getDestinationId() : string
    {
        return $this->getProperty()->destinationId;
    }
    
/**
<documentation><description><p>Returns <code>destinationPath</code>.</p></description>
<example>echo $tc->getDestinationPath(), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDestinationPath()
    {
        return $this->getProperty()->destinationPath;
    }
    
/**
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Hash Tags</code>.</p></description>
<example>echo $tc->getHashTags(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getHashTags()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::HASHTAGS )
            {
                return $param->getValue();
            }
        }
    }
    
/**
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Prefix</code>.</p></description>
<example>echo $tc->getPrefix(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPrefix()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PREFIX )
            {
                return $param->getValue();
            }
        }
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Hash Tags</code>, and returns the calling object.</p></description>
<example>$tc->setHashTags( '' )->setPrefix( '' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setHashTags( string $value ) : Asset
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::HASHTAGS )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Prefix</code>, and returns the calling object.</p></description>
<example>$tc->setHashTags( '' )->setPrefix( '' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setPrefix( string $value ) : Asset
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PREFIX )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
}
?>