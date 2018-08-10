<?php
/**
  Author: Wing Ming Chan, German Drulyk
  Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                       German Drulyk <drulykg@upstate.edu>
  MIT Licensed
  Modification history:
  7/19/2018 Moved WSDL-related constants and methods from child class to here.
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
</ul><p>All 28 operations have been encapsulated in these classes (the <code>batch</code> operation, as of Cascade 8.9, is not implemented in REST by Hannon Hill). The general format of a method encapsulating an operation is the following:</p>
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
        \$command   = \$this->url . __function__ . '/' . \$id_string;
        
        if( \$this->auth_in_content === false )
        {
            \$command .= \$this->auth;
        }
        
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
    // strings used to switch between REST and SOAP
    const REST_STRING = "rest";
    const SOAP_STRING = "soap";
    
    // these constants are used to retrieve parts of the WSDL
    const BINDING_PATH = "//wsdl:definitions/wsdl:binding";
    const COMPLEX_TYPE_PATH =
        "//wsdl:definitions/wsdl:types/schema:schema/schema:complexType";
    const ELEMENT_PATH     = "//wsdl:definitions/wsdl:types/schema:schema/schema:element";
    const MESSAGE_PATH     = "//wsdl:definitions/wsdl:message";
    const PORT_TYPE_PATH   = "//wsdl:definitions/wsdl:portType";
    const SIMPLE_TYPE_PATH =
        "//wsdl:definitions/wsdl:types/schema:schema/schema:simpleType";
    
/**
<documentation><description><p>The constructor. Do not call this constructor directly. Instead, use the <code>ServiceFactory::getService</code> method to get the instance of one of the child classes.</p>
</description>
<example>$service = aohs\ServiceFactory::getService( $type, $url, $username, $password );</example>
</documentation>
*/
    public function __construct(
        string $type, string $url, \stdClass $auth, $context=NULL )
    {
        if( trim( $type ) !== self::REST_STRING && trim( $type ) !== self::SOAP_STRING )
        {
            throw new e\UnacceptableServiceTypeException(
                "The type string $type is not acceptable." );
        }
        else // store the type string
        {
            $this->service_type = trim( $type );
        }
        
        if( $this->isSoap() )
        {
            $wsdl = file_get_contents( $url );
        }
        // for REST, the url has to be calculated
        elseif( $this->isRest() )
        {
            $wsdl_url = str_replace( 'api/v1/', '', $url ) .
            "ws/services/AssetOperationService?wsdl";
            $wsdl     = file_get_contents( $wsdl_url );
        }
            
        $domDoc   = new \DOMDocument();
        $domDoc->loadXML( $wsdl );
        
        $this->dom_xpath = new \DOMXpath( $domDoc );
        $this->dom_xpath->registerNamespace( 'wsdl', 'http://schemas.xmlsoap.org/wsdl/' );
        $this->dom_xpath->registerNamespace(
            'schema', 'http://www.w3.org/2001/XMLSchema' );
    }

/**
<documentation><description><p>Returns the XML of <code>wsdl:binding</code>.</p></description>
<example>echo $eval->replaceBrackets( $service->getBinding() );</example>
<return-type>string</return-type></documentation>
*/
    public function getBinding() : string
    {
        return $this->getXMLByPath( self::BINDING_PATH );
    }

/**
<documentation><description><p>Returns a list of complex type names.</p></description>
<example>echo $service->getComplexTypeNameList();</example>
<return-type>string</return-type></documentation>
*/
    public function getComplexTypeNameList() : string
    {
        return $this->getNameList( self::COMPLEX_TYPE_PATH );
    }

/**
<documentation><description><p>Returns the XML of the named complex type.</p></description>
<example>echo $eval->replaceBrackets( $service->getComplexTypeXMLByName( "copyParameters" ) );</example>
<return-type>string</return-type></documentation>
*/
    public function getComplexTypeXMLByName( string $name ) : string
    {
        return $this->getXMLByName( self::COMPLEX_TYPE_PATH, $name );
    }

/**
<documentation><description><p>Returns the <code>DOMXpath</code> object storing the WSDL.</p></description>
<example></example>
<return-type>DOMXpath</return-type></documentation>
*/
    public function getDOMXpath() : \DOMXpath
    {
        return $this->dom_xpath;
    }

/**
<documentation><description><p>Returns a list of element names.</p></description>
<example>echo $eval->replaceBrackets( $service->getComplexTypeXMLByName( "copyParameters" ) );</example>
<return-type>string</return-type></documentation>
*/
    public function getElementNameList() : string
    {
        return $this->getNameList( self::ELEMENT_PATH );
    }

/**
<documentation><description><p>Returns the XML of the named element.</p></description>
<example>echo $eval->replaceBrackets( $service->getElementXMLByName( "deleteMessage" ) );</example>
<return-type>string</return-type></documentation>
*/
    public function getElementXMLByName( string $name ) : string
    {
        return $this->getXMLByName( self::ELEMENT_PATH, $name );
    }

/**
<documentation><description><p>Returns the XML of <code>wsdl:message</code>.</p></description>
<example>echo $eval->replaceBrackets( $service->getMessages() );</example>
<return-type>string</return-type></documentation>
*/
    public function getMessages() : string
    {
        return $this->getXMLByPath( self::MESSAGE_PATH );
    }

/**
<documentation><description><p>Returns the XML of <code>wsdl:portType</code>.</p></description>
<example>echo $eval->replaceBrackets( $service->getPortType() );</example>
<return-type>string</return-type></documentation>
*/
    public function getPortType() : string
    {
        return $this->getXMLByPath( self::PORT_TYPE_PATH );
    }

/**
<documentation><description><p>Returns the private array named <code>$properties</code>.
This array stores names of properties defined in the WSDL. These names are always
in camelCase.</p></description>
<example>u\DebugUtility::dump( $service->getProperties() );</example>
<return-type>array</return-type></documentation>
*/
    public function getProperties() : array
    {
        return $this->properties;
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
<documentation><description><p>Returns a list of simple type names.</p></description>
<example>echo $service->getSimpleTypeNameList();</example>
<return-type>string</return-type></documentation>
*/
    public function getSimpleTypeNameList() : string
    {
        return $this->getNameList( self::SIMPLE_TYPE_PATH );
    }

/**
<documentation><description><p>Returns the XML of the named complex type.</p></description>
<example>echo $eval->replaceBrackets( $service->getSimpleTypeXMLByName( "message-mark-type" ) );</example>
<return-type>string</return-type></documentation>
*/
    public function getSimpleTypeXMLByName( string $name ) : string
    {
        return $this->getXMLByName( self::SIMPLE_TYPE_PATH, $name );
    }

/**
<documentation><description><p>Returns the private array named <code>$types</code>.
This array stores type strings defined in the WSDL.</p></description>
<example>u\DebugUtility::dump( $service->getTypes() );</example>
<return-type>array</return-type></documentation>
*/
    public function getTypes() : array
    {
        return $this->types;
    }

/**
<documentation><description><p>Returns the concatenated XML fragments, based on the
supplied list of method names and element names.</p></description>
<example>$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "entity-type" ),
        array( "getSimpleTypeXMLByName"  => "entityTypeString" ),
    ) );
return $doc_string;
</example>
<return-type>string</return-type></documentation>
*/
    public function getXMLFragments( array $array ) : string
    {
        $doc_string = S_PRE;
        $str_array  = array();
        
        foreach( $array as $sub_array )
        {
            foreach( $sub_array as $key => $value )
            {
                $str_array[] = u\XMLUtility::replaceBrackets( $this->$key( $value ) );
            }
        }
        
        $doc_string .= trim( implode( "\r", $str_array ), "\r" );
        $doc_string .= E_PRE;
        return $doc_string;
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
    
    // helper functions
    private function getNameList( string $path ) : string
    {
        $nodes = $this->dom_xpath->evaluate( $path );
        $list  = "<ul>";
        $names = array();
        
        if( sizeof( $nodes ) > 0 )
        {
            for( $i = 0; $i < $nodes->length; $i++ )
            {
                $names[] = $nodes->item( $i )->getAttribute( "name" );
            }
            
            asort( $names );
            
            //u\DebugUtility::dump( $names );
            
            foreach( $names as $name )
            {
                $list .= "<li>$name</li>";
            }
        }
        
        $list .= "</ul>";
        return $list;
    }
    private function getXMLByName( string $path, string $name ) : string
    {
        $xpath_str = $path . "[@name='$name']";
        $nodes     = $this->dom_xpath->evaluate( $xpath_str );
        $xml_str   = "";
        
        if( $nodes->length > 0 )
        {
            $xml_str = $nodes[ 0 ]->ownerDocument->saveXML( $nodes[ 0 ] );
        }
        else
        {
            // not found
        }

        return $xml_str;
    }

    private function getXMLByPath( string $path_str ) : string
    {
        $elements  = $this->dom_xpath->evaluate( $path_str );
        $xml_str   = "";
        
        if( sizeof( $elements ) > 0 )
        {
            foreach( $elements as $element )
                $xml_str .= $element->ownerDocument->saveXML( $element );
        }
        return $xml_str;
    }

    private $service_type;
    private $dom_xpath;
    
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