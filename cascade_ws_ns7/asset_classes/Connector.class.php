<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/3/2018 Added REST code and code to test for NULL.
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
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
<p>The <code>Connector</code> class is the superclass of <code>GoogleAnalyticsConnector</code>,
<code>TwitterConnector</code>, and <code>WordPressConnector</code>. It is an abstract
class and defines methods commonly shared by its sub-classes. Note that there are methods
defined here that are used by some, but not all, sub-classes. If a method is called
through an object that should not be associated with the method in the first place, an
exception will be thrown. For example, the <code>setDestination</code> method is not
intended for <code>GoogleAnalyticsConnector</code> nor <code>WordPressConnector</code>
objects. If the method is called by such an object, an exception will be thrown by this
class.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "connector" ),
        array( "getComplexTypeXMLByName" => "connector-parameter-list" ),
        array( "getComplexTypeXMLByName" => "connector-parameter" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-list" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-param-list" ),
        array( "getComplexTypeXMLByName" => "connector-content-type-link-param" ),
    ) );
return $doc_string;
?>
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
        
        $this->connector_content_type_links[] =
            new p\ConnectorContentTypeLink( $obj, $this->getService() );
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
        
        if( $this->getService()->isSoap() )
        {
            $this->getProperty()->connectorContentTypeLinks = new \stdClass();
        }
        elseif( $this->getService()->isRest() )
        {
            $this->getProperty()->connectorContentTypeLinks = array();
        }
        
        $count = count( $this->connector_content_type_links );
        
        if( $count > 0 )
        {
            if( $count == 1 )
            {
                if( $this->getService()->isSoap() )
                    $this->getProperty()->
                        connectorContentTypeLinks->connectorContentTypeLink =
                            $this->connector_content_type_links[ 0 ]->toStdClass();
                elseif( $this->getService()->isRest() )
                    $this->getProperty()->
                        connectorContentTypeLinks =
                            array( $this->connector_content_type_links[ 0 ]->toStdClass() );
            }
            else
            {
                if( $this->getService()->isSoap() )
                    $this->getProperty()->connectorContentTypeLinks->
                        connectorContentTypeLink = array();
                elseif( $this->getService()->isRest() )
                    $this->getProperty()->connectorContentTypeLinks = array();
                    
                foreach( $this->connector_content_type_links as $link )
                {
                    if( $this->getService()->isSoap() )
                        $this->getProperty()->connectorContentTypeLinks->
                            connectorContentTypeLink[] =
                                $link->toStdClass();
                    elseif( $this->getService()->isRest() )
                        $this->getProperty()->connectorContentTypeLinks[] =
                            $link->toStdClass();
                }
            }
        }
        
        $count = count( $this->connector_parameters );
        
        if( $count > 0 )
        {
            if( $count == 1 )
            {
                if( $this->getService()->isSoap() )
                    $this->getProperty()->connectorParameters->connectorParameter =
                        $this->connector_parameters[ 0 ]->toStdClass();
                elseif( $this->getService()->isRest() )
                    $this->getProperty()->connectorParameters =
                        array( $this->connector_parameters[ 0 ]->toStdClass() );
            }
            else
            {
                if( $this->getService()->isSoap() )
                    $this->getProperty()->connectorParameters->
                        connectorParameter = array();
                elseif( $this->getService()->isRest() )
                    $this->getProperty()->connectorParameters = array();
                
                foreach( $this->connector_parameters as $param )
                {
                    if( $this->getService()->isSoap() )
                        $this->getProperty()->connectorParameters->
                            connectorParameter[] =
                                $param->toStdClass();
                    elseif( $this->getService()->isRest() )
                        $this->getProperty()->connectorParameters[] =
                            $param->toStdClass();
                }
            }
        }
        
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
        u\DebugUtility::dump( $this->getProperty() );
        
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
        if( isset( $this->getProperty()->auth1 ) )
            return $this->getProperty()->auth1;
        return NULL;
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
        if( isset( $this->getProperty()->auth2 ) )
            return $this->getProperty()->auth2;
        return NULL;
    }
    
/**
<documentation><description><p>eturns an array of <a href="http://www.upstate.edu/web-services/api/property-classes/connector-content-type-link.php"><code>p\ConnectorContentTypeLink</code></a> objects.</p></description>
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
<documentation><description><p>Returns an array of <a href="http://www.upstate.edu/web-services/api/property-classes/connector-parameter.php"><code>p\ConnectorParameter</code></a> objects.</p></description>
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
        if( isset( $this->getProperty()->url ) )
            return $this->getProperty()->url;
        return NULL;
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
        if( isset( $this->getProperty()->verifiedDate ) )
            return $this->getProperty()->verifiedDate;
        return NULL;
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
        if( isset( $this->getProperty()->connectorParameters ) )
        {
            if( $this->getService()->isSoap() &&
                isset( $this->getProperty()->connectorParameters->connectorParameter ) )
                $params = $this->getProperty()->connectorParameters->connectorParameter;
            elseif( $this->getService()->isRest() )
                $params = $this->getProperty()->connectorParameters;
            
            if( !is_array( $params ) )
            {
                $params = array( $params );
            }
            foreach( $params as $param )
            {
                $this->connector_parameters[] = new p\ConnectorParameter( $param );
            }
        }
        
        if( isset( $this->getProperty()->connectorContentTypeLinks ) )
        {
            if( $this->getService()->isSoap() &&
                isset( $this->getProperty()->connectorContentTypeLinks->
                    connectorContentTypeLink ) )
                $links = $this->getProperty()->connectorContentTypeLinks->
                    connectorContentTypeLink;
            elseif( $this->getService()->isRest() )
                $links = $this->getProperty()->connectorContentTypeLinks;
            
            if( !is_array( $links ) )
            {
                $links = array( $links );
            }
            
            foreach( $links as $link )
            {
                $this->connector_content_type_links[] = 
                    new p\ConnectorContentTypeLink( $link, $this->getService() );
            }
        }
    }
    
    private $connector_parameters;
    private $connector_content_type_links;
}
?>