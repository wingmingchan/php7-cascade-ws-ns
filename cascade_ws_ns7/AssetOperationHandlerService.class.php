<?php
/**
  Author: Wing Ming Chan, German Drulyk
  Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                       German Drulyk <drulykg@upstate.edu>
  MIT Licensed
  Modification history:
  1/17/2018 Moved $properties and $types from child classes to here.
  1/12/2018 Created the parent class.
 */
namespace cascade_ws_AOHS;

use cascade_ws_constants as c;
use cascade_ws_utility   as u;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_exception as e;

/**
<documentation>
<description><?php global $eval, $service;
$doc_string = "<h2>Introduction</h2>
<p>This class is an abstract class and the parent class of <code>AssetOperationHandlerServiceRest</code> and <code>AssetOperationHandlerServiceSoap</code>. The two child classes encapsulate 
the WSDL URL, authentication information, and in the case of <code>AssetOperationHandlerServiceSoap</code>, the SoapClient object, and provide services of all operations defined in the WSDL. There are 28 operations defined in the WSDL:</p>
<ul>
<li>batch</li>
<li>checkIn</li>
<li>checkOut</li>
<li>copy</li>
<li>create</li>
<li>delete</li>
<li>deleteMessage</li>
<li>edit</li>
<li>editAccessRights</li>
<li>editPreference</li>
<li>editWorkflowSettings</li>
<li>listEditorConfigurations</li>
<li>listMessages</li>
<li>listSites</li>
<li>listSubscribers</li>
<li>markMessage</li>
<li>move</li>
<li>performWorkflowTransition</li>
<li>publish</li>
<li>read</li>
<li>readAccessRights</li>
<li>readAudits</li>
<li>readPreferences</li>
<li>readWorkflowInformation</li>
<li>readWorkflowSettings</li>
<li>search</li>
<li>sendMessage (deprecated)</li>
<li>siteCopy</li>
</ul><p>All 28 operations have been encapsulated in these classes (the <code>batch</code> operation is not implemented in REST by Hannon Hill). The general format of a method encapsulating an operation is the following:</p>
<ol>
<li>Create the parameters for the operation</li>
<li>Call the corresponding operation through REST/the SOAP client</li>
</ol>
<p>Here is the code of <code>read</code>, for example:</p>
<pre>
    // REST
    public function read( \stdClass \$identifier )
    {
        \$id_string = \$this->createIdString( \$identifier );
        \$command   = \$this->url . __function__ . '/' . \$id_string . \$this->auth;
        \$this->reply   = \$this->apiOperation( \$command );
        \$this->success = \$this->reply->success;
        
        return \$this->reply->asset ?? NULL;
    }

    // SOAP
    public function read( \stdClass \$identifier ) : \stdClass
    {
        if( self::DEBUG ) { u\DebugUtility::dump( \$identifier ); }
        
        \$read_param                 = new \stdClass();
        \$read_param->authentication = \$this->auth;
        \$read_param->identifier     = \$identifier;
        
        \$this->reply = \$this->soapClient->read( \$read_param );
        \$this->storeResults( \$this->reply->readReturn );
        return \$this->reply;
    }
</pre>
<p>Besides encapsulating the 28 operations, there are also other utility methods:</p>
<ul>
<li><code>createX</code> methods to create IDs (stdClass objects) for asset retrieval</li>
<li><code>get</code> methods to retrieve XML fragments from the WSDL</li>
<li>Other minor methods</li>
</ul>
<h2>WSDL</h2>
<h3>Elements</h3>";
$doc_string .= $service->getElementNameList();
$doc_string .= "<h3>Simple Types</h3>";
$doc_string .= $service->getSimpleTypeNameList();
$doc_string .= "<h3>Complex Types</h3>";
$doc_string .= $service->getComplexTypeNameList();
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "authentication" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
        array( "getComplexTypeXMLByName" => "operation" ),
    ) );
$doc_string .= "<h3>Operation Result</h3><pre>";
$doc_string .=
    $eval->replaceBrackets($service->getComplexTypeXMLByName("operationResult"));
$doc_string .= "</pre><h3>Messages</h3><pre>";
$doc_string .=  $eval->replaceBrackets($service->getMessages());
$doc_string .= "</pre><h3>Port Type</h3><pre>";
$doc_string .= $eval->replaceBrackets($service->getPortType());
$doc_string .= "</pre><h3>Binding</h3><pre>";
$doc_string .= $eval->replaceBrackets($service->getBinding());
$doc_string .= "</pre>";
return $doc_string; ?></description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/tree/master/working-with-AssetOperationHandlerService">working-with-AssetOperationHandlerService</a></li></ul></postscript>
<advanced>
</advanced>
</documentation>
*/
abstract class AssetOperationHandlerService
{
    const NAME_SPACE  = "cascade_ws_AOHS";
    const REST_STRING = "rest";
    const SOAP_STRING = "soap";
    
    public function __construct(
        string $type, string $url, \stdClass $auth, $context=NULL )
    {
        if( trim( $type ) !== self::REST_STRING && trim( $type ) !== self::SOAP_STRING )
        {
            throw new e\UnacceptableServiceTypeException(
                "The type string $type is not acceptable." );
        }
        else
        {
            $this->service_type = trim( $type );
        }
    }
    
/**
<documentation><description><p>Returns the private array named <code>$properties</code>.</p></description>
<example></example>
<return-type>array</return-type></documentation>
*/
    public function getProperties() : array
    {
        return $this->properties;
    }
    
/**
<documentation><description><p>Returns the private array named <code>$types</code>.</p></description>
<example></example>
<return-type>array</return-type></documentation>
*/
    public function getTypes() : array
    {
        return $this->types;
    }

/**
<documentation><description><p>Returns a string of either 'soap' or 'rest'.</p></description>
<example>echo $service->getServiceType();</example>
<return-type>string</return-type></documentation>
*/
    public function getServiceType() : string
    {
        return $this->service_type;
    }
    
/**
<documentation><description><p>Returns a bool indicating whether the string is a 32-digit hex string.</p></description>
<example>if( $service->isHexString( $id ) )
    echo $service->getType( $id ), BR;</example>
<return-type>bool</return-type></documentation>
*/
    public function isHexString( string $string ) : bool
    {
        $pattern = "/[0-9a-f]{32}/";
        $matches = array();
        
        preg_match( $pattern, $string, $matches );
        
        if( isset( $matches[ 0 ] ) )
            return $matches[ 0 ] == $string;
        return false;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the object is associated with REST.</p></description>
<example>echo u\StringUtility::boolToString( $service->isRest() );</example>
<return-type>bool</return-type></documentation>
*/
    public function isRest() : bool
    {
        return $this->service_type === self::REST_STRING;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the object is associated with SOAP.</p></description>
<example>echo u\StringUtility::boolToString( $service->isSoap() );</example>
<return-type>bool</return-type></documentation>
*/
    public function isSoap() : bool
    {
        return $this->service_type === self::SOAP_STRING;
    }
    
    private $service_type;
    
    // property array to generate methods
    /*@var array The array of property names */
    private $properties = array(
        c\P::ASSETFACTORY,
        c\P::ASSETFACTORYCONTAINER,
        C\P::CLOUDTRANSPORT,
        c\P::CONNECTORCONTAINER,
        c\P::CONTENTTYPE,
        c\P::CONTENTTYPECONTAINER,
        c\P::DATADEFINITION,
        c\P::DATADEFINITIONCONTAINER,
        c\P::DATABASETRANSPORT,
        c\P::DESTINATION,
        c\P::FACEBOOKCONNECTOR,
        c\P::FEEDBLOCK,
        c\P::FILE,
        c\P::FILESYSTEMTRANSPORT,
        c\P::FOLDER,
        c\P::FTPTRANSPORT,
        c\P::GOOGLEANALYTICSCONNECTOR,
        c\P::GROUP,
        c\P::INDEXBLOCK,
        c\P::METADATASET,
        c\P::METADATASETCONTAINER,
        c\P::PAGE,
        c\P::PAGECONFIGURATIONSET,
        c\P::PAGECONFIGURATIONSETCONTAINER,
        c\P::PUBLISHSET,
        c\P::PUBLISHSETCONTAINER,
        c\P::REFERENCE,
        c\P::ROLE,
        c\P::SCRIPTFORMAT,
        c\P::SITE,
        c\P::SITEDESTINATIONCONTAINER,
        c\P::SYMLINK,
        c\P::TARGET,
        c\P::TEMPLATE,
        c\P::TEXTBLOCK,
        c\P::TRANSPORTCONTAINER,
        c\P::USER,
        c\P::WORDPRESSCONNECTOR,
        c\P::WORKFLOWDEFINITION,
        c\P::WORKFLOWDEFINITIONCONTAINER,
        c\P::XHTMLDATADEFINITIONBLOCK,
        c\P::XMLBLOCK,
        c\P::XSLTFORMAT
    );
    
    /*@var array The array of types of assets */
    private $types = array(
        c\T::ASSETFACTORY,
        c\T::ASSETFACTORYCONTAINER,
        c\T::CLOUDTRANSPORT,
        c\T::CONNECTORCONTAINER,
        c\T::CONTENTTYPE,
        c\T::CONTENTTYPECONTAINER,
        c\T::DATADEFINITION,
        c\T::DATADEFINITIONCONTAINER,
        c\T::DESTINATION,
        c\T::FACEBOOKCONNECTOR,
        c\T::FEEDBLOCK,
        c\T::FILE,
        c\T::FOLDER,
        c\T::GOOGLEANALYTICSCONNECTOR,
        c\T::GROUP,
        c\T::INDEXBLOCK,
        c\T::MESSAGE,
        c\T::METADATASET,
        c\T::METADATASETCONTAINER,
        c\T::PAGE,
        c\T::PAGECONFIGURATION,
        c\T::PAGECONFIGURATIONSET,
        c\T::PAGECONFIGURATIONSETCONTAINER,
        c\T::PAGEREGION,
        c\T::PUBLISHSET,
        c\T::PUBLISHSETCONTAINER,
        c\T::REFERENCE,
        c\T::ROLE,
        c\T::SCRIPTFORMAT,
        c\T::SITE,
        c\T::SITEDESTINATIONCONTAINER,
        c\T::SYMLINK,
        c\T::TARGET,
        c\T::TEMPLATE,
        c\T::TEXTBLOCK,
        c\T::TRANSPORTDB,
        c\T::TRANSPORTFS,
        c\T::TRANSPORTFTP,
        c\T::TRANSPORTCONTAINER,
        c\T::USER,
        c\T::WORDPRESSCONNECTOR,
        c\T::WORKFLOW,
        c\T::WORKFLOWDEFINITION,
        c\T::WORKFLOWDEFINITIONCONTAINER,
        c\T::XHTMLDATADEFINITIONBLOCK,
        c\T::XMLBLOCK,
        c\T::XSLTFORMAT
    );
}
?>