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
<p>A <code>GoogleAnalyticsConnector</code> object represents a Google analytics connector asset. This class is a sub-class of <a href="/web-services/api/asset-classes/connector"><code>Connector</code></a>.</p>
<h2>Structure of <code>googleAnalyticsConnector</code></h2>
<pre>googleAnalyticsConnector
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
  connectorContentTypeLinks (empty)
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/google_connector.php">google_connector.php</a></li></ul></postscript>
</documentation>
*/
class GoogleAnalyticsConnector extends Connector
{
    const DEBUG     = false;
    const TYPE      = c\T::GOOGLEANALYTICSCONNECTOR;
    const BASEPATH  = "Base Path";
    const PROFILEID = "Google Analytics Profile Id";
    
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
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Base Path</code>.</p></description>
<example>echo $gc->getBasePath(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBasePath() : string
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::BASEPATH )
            {
                return $param->getValue();
            }
        }
        return NULL;
    }
    
/**
<documentation><description><p>Returns the value of the <code>connectorParameter</code> named <code>Google Analytics Profile Id</code>.</p></description>
<example>echo $gc->getProfileId(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getProfileId()
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PROFILEID )
            {
                return $param->getValue();
            }
        }
        return NULL;
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Base Path</code>, and returns the calling object.</p></description>
<example>$gc->setBasePath( 'all' )->setProfileId( 'two' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setBasePath( string $value ) : Asset
    {
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::BASEPATH )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets the value of the <code>connectorParameter</code> named <code>Google Analytics Profile Id</code>, and returns the calling object. The <code>$value</code> cannot be empty.</p></description>
<example>$gc->setBasePath( 'all' )->setProfileId( 'two' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setProfileId( string $value ) : Asset
    {
        if( trim( $value) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . "The profile ID cannot be empty." . E_SPAN );
        }
        $connector_parameters = $this->getConnectorParameters();
        
        foreach( $connector_parameters as $param )
        {
            if( $param->getName() == self::PROFILEID )
            {
                $param->setValue( $value );
            }
        }
        return $this;
    }
}
?>