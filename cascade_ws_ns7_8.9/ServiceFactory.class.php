<?php
/**
  Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                       German Drulyk <drulykg@upstate.edu>
  MIT Licensed
  Modification history:
  1/19/2018 Added authInContent and related code.
  1/19/2018 Added documentation.
  1/16/2018 Class created.
 */
namespace cascade_ws_AOHS;

use cascade_ws_constants as c;
use cascade_ws_utility   as u;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_exception as e;

/**
<documentation>
<description>
<h2>Introduction</h2>
<p>This class provides a <code>static</code> factory method to create an instance of <code>AssetOperationHandlerService</code>, using either REST or SOAP.</p>
</description>
<postscript></postscript>
<advanced>
</advanced>
</documentation>
*/
class ServiceFactory
{
/**
<documentation><description>
<p>Returns an <code>AssetOperationHandlerService</code> object. <code>$context</code>, when defined, should be an array of the followig type:</p>
<pre>$context =
    array( 'trace' => 1,
        'proxy_host' => "111.222.33.44",
        'proxy_port' => "80",
        'stream_context' => stream_context_create(
              array( 'https' =>
                array( 'proxy' => "tcp:// 111.222.33.44:80", 'request_fulluri' => true )
            )
        )
    );
</pre>
<p>The value of the variable <code>$authInContent</code>, when provided, determines whether the authentication information should be included in the body. The variable is defaulted to <code>true</code>.</p>
</description>
<example>// REST
$type     = aohs\AssetOperationHandlerService::REST_STRING;
$url      = "http://mydomain.edu:1234/api/v1/";
$username = "user";
$password = "pw";
$service  = aohs\ServiceFactory::getService( $type, $url, $username, $password );

// SOAP
$type     = aohs\AssetOperationHandlerService::SOAP_STRING;
$url      = "http://mydomain.edu:1234/ws/services/AssetOperationService?wsdl";
$username = $_SERVER[ 'PHP_AUTH_USER' ];
$password = $_SERVER[ 'PHP_AUTH_PW' ];
$service  = aohs\ServiceFactory::getService( $type, $url, $username, $password );
</example>
<return-type>AssetOperationHandlerService</return-type></documentation>
*/
    public static function getService(
        string $type, string $url, string $username, string $password,
        $context=NULL, bool $authInContent=true ) :
        AssetOperationHandlerService
    {
        $type = strtolower( $type );
        
        if( $type === AssetOperationHandlerService::SOAP_STRING )
        {
            return new AssetOperationHandlerServiceSoap(
                $type, $url, 
                ( object ) [ 'username' => $username, 'password' => $password ],
                $context );
        }
        elseif( $type === AssetOperationHandlerService::REST_STRING )
        {
            return new AssetOperationHandlerServiceRest(
                $type, $url, 
                ( object )[ 'u' => $username, 'p' => $password,
                    'authInContent' => $authInContent ],
                $context );
        }
    }
}
?>