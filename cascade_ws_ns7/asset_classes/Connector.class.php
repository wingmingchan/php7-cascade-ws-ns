<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
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
<p>The <code>Connector</code> class is the superclass of <code>FacebookConnector</code>, <code>GoogleAnalyticsConnector</code>, <code>TwitterConnector</code>, and <code>WordPressConnector</code>. It is an abstract class and defines methods commonly shared by its sub-classes. Note that there are methods defined here that are used by some, but not all, sub-classes. If a method is called through an object that should not be associated with the method in the first place, an exception will be thrown. For example, the <code>setDestination</code> method is not intended for <code>GoogleAnalyticsConnector</code> nor <code>WordPressConnector</code> objects. If the method is called by such an object, an exception will be thrown by this class.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/connector.php">connector.php</a></li>

</ul></postscript>
</documentation>
*/
abstract class Connector extends ContainedAsset
{
    const DEBUG = false;

/**
<documentation><description><p>The constructor, overriding the parent method to process parameters.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    protected function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->connector_parameters         = array();
        $this->connector_content_type_links = array();
        $this->processParameters();
    }
    
/**
<documentation><description><p>Adds a content type link to the connector, and returns the
calling object.</p></description>
<example>$connector->addContentTypeLink(
    $cascade->getAsset( a\ContentType::TYPE, "1378b3e38b7f08ee1890c1e4df869132" ),
    "XML"
)->edit();
</example>
<return-type>Asset</return-type>
<exception>NullAssetException, EmptyValueException, NoSuchPageConfigurationException, Exception</exception>
</documentation>
*/
    public function addContentTypeLink(
        ContentType $ct, string $page_config_name ) : Asset
    {
        if( $this->getPropertyName() == c\P::GOOGLEANALYTICSCONNECTOR )
        {
            throw new \Exception( 
                S_SPAN . c\M::GOOGLE_CONNECTOR_NO_CT . E_SPAN );
        }
    
        if( $ct == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_CONTENT_TYPE . E_SPAN );
        }
            
        if( trim( $page_config_name ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_PAGE_CONFIGURATION_NAME . E_SPAN );
        }
            
        $config_set = $ct->getConfigurationSet();
        
        if( !$config_set->hasPageConfiguration( $page_config_name ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN ."The page configuration $page_config_name does not exist. " . E_SPAN );
        }
        
        $config = $config_set->getPageConfiguration( $page_config_name );

        foreach( $this->connector_content_type_links as $link )
        {
            // link exist
            if( $link->getContentTypeId() == $ct->getId() )
            {
                $cur_config = $link->getPageConfigurationName();
                
                // remove site name
                if( strpos( $cur_config, ":" ) !== false )
                {
                    $pos = strpos( $cur_config, ":" );
                    $cur_config = substr( $cur_config, $pos + 1 );
                }
                // replace current one
                if( $cur_config != $config->getName() )
                {
                    $link->setPageConfiguration( $config );
                }
                
                return $this;
            }
        }
        
        // link does not exist
        $obj                                 = new \stdClass();
        $obj->contentTypeId                  = $ct->getId();
        $obj->contentTypePath                = $ct->getPath();
        $obj->pageConfigurationId            = $config->getId();
        $obj->pageConfigurationName          = $config->getName();
        $obj->connectorContentTypeLinkParams = new \stdClass();
        
        $this->connector_content_type_links[] = new p\ConnectorContentTypeLink( $obj );
        return $this;
    }
        
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $asset                                          = new \stdClass();
        $this->getProperty()->connectorContentTypeLinks = new \stdClass();
        
        $count = count( $this->connector_content_type_links );
        
        if( $count > 0 )
        {
            if( $count == 1 )
            {
                $this->getProperty()->connectorContentTypeLinks->connectorContentTypeLink =
                    $this->connector_content_type_links[ 0 ]->toStdClass();
            }
            else
            {
                $this->getProperty()->connectorContentTypeLinks->connectorContentTypeLink = array();
                
                foreach( $this->connector_content_type_links as $link )
                {
                    $this->getProperty()->connectorContentTypeLinks->connectorContentTypeLink[] =
                        $link->toStdClass();
                }
            }
        }
        
        $count = count( $this->connector_parameters );
        
        if( $count > 0 )
        {
            if( $count == 1 )
            {
                $this->getProperty()->connectorParameters->connectorParameter =
                    $this->connector_parameters[ 0 ]->toStdClass();
            }
            else
            {
                $this->getProperty()->connectorParameters->connectorParameter = array();
                
                foreach( $this->connector_parameters as $param )
                {
                    $this->getProperty()->connectorParameters->connectorParameter[] =
                        $param->toStdClass();
                }
            }
        }
        
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
        
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        return $this->reloadProperty();
    }
    
/**
<documentation><description><p>Returns <code>auth1</code> (the username).</p></description>
<example>echo u\StringUtility::getCoalescedString( $connector->getAuth1() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getAuth1()
    {
        return $this->getProperty()->auth1;
    }
    
/**
<documentation><description><p>Returns <code>auth2</code> (the password). The returned string is useless.</p></description>
<example>echo u\StringUtility::getCoalescedString( $connector->getAuth2() ), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getAuth2()
    {
        return $this->getProperty()->auth2;
    }
    
/**
<documentation><description><p>eturns an array of <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/connector-content-type-link.php"><code>p\ConnectorContentTypeLink</code></a> objects.</p></description>
<example>u\DebugUtility::dump( $connector->getConnectorContentTypeLinks() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getConnectorContentTypeLinks() : array
    {
        return $this->connector_content_type_links;
    }
    
/**
<documentation><description><p>Returns an array of <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/connector-parameter.php"><code>p\ConnectorParameter</code></a> objects.</p></description>
<example>u\DebugUtility::dump( $connector->getConnectorParameters() );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getConnectorParameters()
    {
        return $this->connector_parameters;
    }
    
/**
<documentation><description><p>Returns <code>url</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $connector->getUrl() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getUrl()
    {
        return $this->getProperty()->url;
    }
    
/**
<documentation><description><p>Returns <code>verified</code>.</p></description>
<example>echo u\StringUtility::boolToString( $connector->getVerified() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getVerified() : bool
    {
        return $this->getProperty()->verified;
    }
    
/**
<documentation><description><p>Returns <code>verifiedDate</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $connector->getVerifiedDate() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getVerifiedDate()
    {
        return $this->getProperty()->verifiedDate;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the content type is
chosen in the connector. <code>$ct_path</code> is the <code>path</code> string of the content type.</p></description>
<example>echo u\StringUtility::boolToString(
    $connector->hasContentType( "_common_assets:RWD One Region" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasContentType( string $ct_path ) : bool
    {
        if( $this->getPropertyName() == c\P::GOOGLEANALYTICSCONNECTOR )
        {
            return false;
        }

        foreach( $this->connector_content_type_links as $ct_link )
        {
            if( $ct_link->getContentTypePath() == $ct_path )
                return true;
        }
        
        return false;
    }
    
/**
<documentation><description><p>Removes the content type from the connector, and returns
the calling object.</p></description>
<example>$connector->removeContentTypeLink( $ct );</example>
<return-type>Asset</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function removeContentTypeLink( ContentType $ct ) : Asset
    {
        if( $this->getPropertyName() == c\P::GOOGLEANALYTICSCONNECTOR )
        {
            throw new \Exception( 
                S_SPAN . c\M::GOOGLE_CONNECTOR_NO_CT . E_SPAN );
        }
    
        $temp = array();
        
        foreach( $this->connector_content_type_links as $link )
        {
            // link exist
            if( $link->getContentTypeId() != $ct->getId() )
            {
                $temp[] = $link;
            }
        }
        
        $this->connector_content_type_links = $temp;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>destination</code> and returns the calling object.</p></description>
<example>$connector->setDestination( $destination )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDestination( Destination $d ) : Asset
    {
        if( $this->getPropertyName() != c\P::TWITTERCONNECTOR &&
            $this->getPropertyName() != c\P::FACEBOOKCONNECTOR
        )
            throw new \Exception( 
                S_SPAN . "The setDestination method cannot be called by a " .
                $this->getPropertyName() . " object." . E_SPAN );
            
        $this->getProperty()->destinationId   = $d->getId();
        $this->getProperty()->destinationPath = $d->getName();
        return $this;
    }
    
    private function processParameters()
    {
        if( isset( $this->getProperty()->connectorParameters ) &&
            isset( $this->getProperty()->connectorParameters->connectorParameter ) )
        {
            $params = $this->getProperty()->connectorParameters->connectorParameter;
            
            if( !is_array( $params ) )
            {
                $params = array( $params );
            }
            foreach( $params as $param )
            {
                $this->connector_parameters[] = new p\ConnectorParameter( $param );
            }
        }
        
        if( isset( $this->getProperty()->connectorContentTypeLinks ) &&
            isset( $this->getProperty()->connectorContentTypeLinks->connectorContentTypeLink ) )
        {
            $links = $this->getProperty()->connectorContentTypeLinks->connectorContentTypeLink;
            
            if( !is_array( $links ) )
            {
                $links = array( $links );
            }
            
            foreach( $links as $link )
            {
                if( $this->getType() == c\T::WORDPRESSCONNECTOR )
                {
                    $this->connector_content_type_links[] = 
                        new p\ConnectorContentTypeLink( $link, $this->getService() );
                }
                else
                {
                    $this->connector_content_type_links[] = new p\ConnectorContentTypeLink( $link );
                }
            }
        }
    }
    
    private $connector_parameters;
    private $connector_content_type_links;
}
?>