<?php
/**
  Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                       German Drulyk <drulykg@upstate.edu>
  MIT Licensed
  Modification history:
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
$doc_string = "
<h2>Introduction</h2>
<p>This class encapsulates the WSDL URL, the authentication object, and the SoapClient object, and provides services of all operations defined in the WSDL. There are 28 operations defined in the WSDL:</p>
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
</ul><p>All 28 operations have been encapsulated in this class. The general format of a method encapsulating an operation is the following:</p>
<ol>
<li>Create the parameters for the operation</li>
<li>Call the corresponding operation through the SOAP client</li>
<li>Store the results</li>
</ol>
<p>Here is the code of <code>batch</code>, for example:</p>
<pre>
    function batch( array \$operations )
    {
        \$batch_param                 = new \stdClass();
        \$batch_param->authentication = \$this->auth;
        \$batch_param->operation      = \$operations;
        
        \$this->reply = \$this->soapClient->batch( \$batch_param );
        // the returned object is an array
        \$this->storeResults();
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
//$doc_string .= "<h3>authentication</h3>";
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
<documentation><description><p>Returns <code>false</code>.</p></description>
<example>echo u\StringUtility::boolToString( $service->isRest() );</example>
<return-type>bool</return-type></documentation>
*/
    public function isRest() : bool
    {
        return $this->service_type === self::REST_STRING;
    }

/**
<documentation><description><p>Returns <code>true</code>.</p></description>
<example>echo u\StringUtility::boolToString( $service->isSoap() );</example>
<return-type>bool</return-type></documentation>
*/
    public function isSoap() : bool
    {
        return $this->service_type === self::SOAP_STRING;
    }
    
    private $service_type;
}
?>