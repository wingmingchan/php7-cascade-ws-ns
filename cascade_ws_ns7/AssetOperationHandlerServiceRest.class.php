<?php
/**
  Author: Wing Ming Chan, German Drulyk
  Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                       German Drulyk <drulykg@upstate.edu>
  MIT Licensed
  Modification history:
  7/20/2018 Fixed a bug in readAudits.
  4/12/2018 Added exception throwing to apiOperation.
  1/19/2018 Added authInContent and related code.
  1/18/2018 Added documentation.
  1/18/2018 Fixed a bug in readAudits.
  1/17/2018 Moved the private arrays to the parent.
  1/12/2018 Integrated German's new code into siteCopy.
            success should be a bool, not a string.
  1/8/2018 Changed createId so that the root metadata set container is read when
  reading a site. Added a while loop in siteCopy.
  1/5/2018 Added code to createIdString to escape space.
  1/4/2018 Added cloud transport-related entries in $types and $properties.
  Added more tests in createIdString. Added the $command array and related methods.
  12/29/2017 Added getReadURL.
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
<p>This class is a child class of <code>AssetOperationHandlerService</code>. It encapsulates the REST URL, and provides services of almost all operations defined in the WSDL. There are 28 operations defined in the WSDL, and as of Cascade 8.9, this class supports 26 of them (except <code>batch</code> and <code>sendMessage</code>):</p>
<ul>
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
<li>siteCopy</li>
</ul><p>The general format of a method encapsulating an operation is the following:</p>
<ol>
<li>Create the URL, incorporating authentication information</li>
<li>Create the parameters, if there are any, for the operation</li>
<li>Call <code>file_get_contents</code> by passing in the URL and the parameters</li>
<li>Store the results</li>
<li>Return the reply</li>
</ol>
<p>Here is the code of <code>read</code>, for example:</p>
<pre>
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
</pre>
<p>Besides encapsulating the 26 operations, there are also other utility methods:</p>
<ul>
<li><code>createX</code> methods to create IDs (stdClass objects) for asset retrieval</li>
<li>Other minor methods</li>
</ul>
<p>Most methods defined in the class call the private method <code>apiOperation</code>. This method stores the URL, and the parameters if there are any, in an array named <code>\$commands</code>. This array can be retrieved by calling the instance method named <code>getCommands</code>:</p>
<pre>\$asset = \$service->read( \$service->createId( \$type, \$path, \$siteName ) );
u\DebugUtility::dump( \$service->getCommands() );
</pre>
<p>The contents of the array can be used for debugging purposes. For example, after a <code>read</code> operation, the <code>\$commands</code> array can be dumped to display the following:</p><pre>
array(1) {
  [0]=>
  array(1) {
    [\"command\"]=>
    string(143) \"http://mydomain.edu:1234/api/v1/read/block/formats/_cascade/blocks/data/latin-wysiwyg?u=wing&amp;p=password\"
  }
}
</pre><p>Note that when a command, like <code>read</code>, does not require parameters, the URL can be copied and pasted into the URL field of a browser and the result will be displayed in the browser.</p><h2>About Identifiers</h2><p>REST requires identifiers of a certain structure. This is normally encapsulated in methods like <code>createId</code>. But if raw code is to be sent to Cascade, we need to know the exact structure. For example, when a path and a site name are supplied:</p><pre>
    {
        \"path\":{
            \"path\":\"velocity\/api-documentation\/java-lang\",
            \"siteName\":\"formats\"
        },
        \"type\":\"folder\",
    }
</pre><p>On the other hand, when an ID string is supplie:</p><pre>
    {
        \"id\":\"945455f48b7ffe8347b1526ee30fc62e\",
        \"type\":\"page\"
    }
</pre>";
return $doc_string; ?></description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/tree/master/working-with-AssetOperationHandlerService">working-with-AssetOperationHandlerService</a></li></ul></postscript>
<advanced>
</advanced>
</documentation>
*/
class AssetOperationHandlerServiceRest extends AssetOperationHandlerService
{
    const DEBUG = false;
    const DUMP  = false;

/**
<documentation><description><p>The constructor.</p></description>
<example>$type     = aohs\AssetOperationHandlerService::REST_STRING;
$url      = "http://mydomain.edu:1234/api/v1/";
$username = "wing";
$password = "password";
$auth     = ( object )[ 'u' => $username, 'p' => $password ];
$service  = new aohs\AssetOperationHandlerServiceRest( $type, $url, $auth );</example>
<return-type>void</return-type></documentation>
*/
    public function __construct(
        string $type, string $url, \stdClass $auth, $context=NULL )
    {
        parent::__construct( $type, $url, $auth, $context );
        
        $this->url            = $url;
        $this->auth           = $auth;
        $this->message        = '';
        $this->success        = '';
        //$this->createdAssetId = '';
        $this->reply = new \stdClass();
        $this->commands = array();
        // if not provided, defaulted to true
        $this->auth_in_content = $auth->authInContent ?? true;

        try
        {
            if( $this->auth_in_content === false )
            {
                $json_str = json_encode( $auth );
                $json_str = trim( $json_str, "{}" );
                $json_str = str_replace( '"', '', $json_str );
                $json_str = str_replace( ':', '=', $json_str );
                $json_str = str_replace( ',', '&', $json_str );
                $auth_str = str_replace( " ", "%20", $json_str );
                $this->auth = '?' . $auth_str;
            }
            else
            {
                unset( $auth->authInContent );
                $this->auth = $auth;
            }
        }
        catch( \Exception $e )
        {
            throw new e\ServerException( S_SPAN . $e->getMessage() . E_SPAN );
        }
        
        u\DebugUtility::dump( $this->auth );
    }

/**
<documentation><description><p>Sends out the command and returns the reply. When <code>$params</code> is not <code>NULL</code>, it can be either an <code>stdClass</code> object or a JSON string.</p></description>
<example>// using an stdClass as $params
$url = "http://mydomain.edu:1234/api/v1/search?u=wing&amp;amp;p=password";
$params = new \stdClass();
$params->searchInformation = new \stdClass();
$params->searchInformation->searchTerms = "group";
$params->searchInformation->siteId = "61885ac08b7ffe8377b637e83a86cca5";
$params->searchInformation->searchTypes = array( "format" );
$reply = $service->apiOperation( $url, $params );
u\DebugUtility::dump( $reply );

// using a string as $params
$url = "http://mydomain.edu:1234/api/v1/search?u=wing&amp;amp;p=password";
$params = '{"searchInformation":{"searchTerms":"group","siteId":"61885ac08b7ffe8377b637e83a86cca5","searchTypes":["format"]}}';
$reply = $service->apiOperation( $url, $params );
u\DebugUtility::dump( $reply );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function apiOperation( string $command, $params=NULL )// : \stdClass
    {
        $input_params = array(
            'http' => array(
                'header'  => "Authorization: Basic " . $this->getAuthString() . "\r\n" .
                    "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST'
            ) );
        
        $entry = array( "command" => $command );
        
        if( !is_null( $params ) )
        {
            if( is_string( $params ) )
            {
                if( trim( $params ) != "" )
                {
                    $input_params[ 'http' ][ 'content' ] = trim( $params );
                }
            }
            else
            {
                if( $this->auth_in_content === false )
                {
                    $input_params[ 'http' ][ 'content' ] = json_encode( $params );
                }
                else
                {
                    $input_params[ 'http' ][ 'content' ] = 
                        json_encode( array_merge( ( array )$params, 
                            [ 'authentication' =>
                                [ 'username' => $this->auth->u,
                                  'password' => $this->auth->p ] ] ) );
                }
            }
            
            // skip the id
            if( !isset( $params->type ) )
            {
                $entry[ "params" ] = json_encode( $params );
            }
        }
        elseif( $this->auth_in_content === true )
        {
            $input_params[ 'http' ][ 'content' ] =
                json_encode( 
                    [ 'authentication' =>
                        [ 'username' => $this->auth->u, 
                          'password' => $this->auth->p ] ] );
        }
        
        //$entry[ "http-content" ] = $input_params[ 'http' ][ 'content' ];
        $this->commands[] = $entry;
        
        $operation_result = @file_get_contents(
            $command,
            false,
            stream_context_create( $input_params ) );
            
        if( $operation_result === false )
        {
            $error = error_get_last();
            $message = trim( $error[ "message" ] ) . " in " . 
                trim( $error[ "file" ] ) . " on line " .
                $error[ "line" ];
            
            throw new \Exception( $message );
        }

        return json_decode( $operation_result );
    }

/*/  
    function batch( array $operations ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $params            = new \stdClass();
        $params = array( 'operation' => $operations );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
/*/
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Checks in an asset with the given identifier.</p><p>checkIn:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("checkIn"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("checkInResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("checkIn"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(2) {
  [0]=>
  string(135) \"https://mydomain.edu:1234/api/v1/checkIn/page/c12eb9978b7ffe83129ed6d80132aa29?u=wing&amp;p=password\"
  [1]=>
  array(2) {
    [\"command\"]=>
    string(135) \"https://mydomain.edu:1234/api/v1/checkIn/page/c12eb9978b7ffe83129ed6d80132aa29?u=wing&amp;p=password\"
    [\"params\"]=>
    string(22) \"{\"comments\":\"testing\"}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$path = "/files/global-editor.css";
$id = $service->createId( a\File::TYPE, $path, "cascade-admin" );
$service->checkIn( $id, 'Testing the checkIn method.' );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function checkIn( \stdClass $identifier, string $comments="" ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $this->commands[] = $command;
        
        if( $comments != "" )
        {
            $params = new \stdClass();
            $params->comments = $comments;
            $this->reply = $this->apiOperation( $command, $params );
        }
        else
            $this->reply = $this->apiOperation( $command );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Checks out an asset with the given identifier.</p><p>checkOut:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("checkOut"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("checkOutResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("checkOut"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("checkOutResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(136) \"https://mydomain.edu:1234/api/v1/checkOut/page/c12eb9978b7ffe83129ed6d80132aa29?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$path = "/files/AssetOperationHandlerService.class.php.zip";
$id = $service->createId( a\File::TYPE, $path, "cascade-admin" );
$service->checkOut( $id );
</example>
<return-type>stdClass</return-type></documentation>
*/
    public function checkOut( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
/**
<documentation><description>
<p>Removes the contents of the array <code>$commands</code>.</p>
</description>
<example>$service->clearCommands();</example>
<return-type>void</return-type></documentation>
*/
    public function clearCommands()
    {
        $this->commands = array();
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Copies the asset with the given identifier.</p><p>copy:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("copy"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("copyResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("copy"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("copyParameters"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(133) \"https://mydomain.edu:1234/api/v1/copy/block/089c28d98b7ffe83785cac8a79fe2145?u=wing&amp;p=password\"
    [\"params\"]=>
    string(152) \"{\"copyParameters\":{\"destinationContainerIdentifier\":{\"id\":\"c12dcef18b7ffe83129ed6d85960d93d\",\"type\":\"folder\"},\"newName\":\"new-hello\",\"doWorkflow\":false}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>// the block to be copy
$block_id = $service->createId( a\TextBlock::TYPE, "_cascade/blocks/code/text-block", "cascade-admin" );
// the parent folder where the new block should be placed
$parent_id = $service->createId( a\Folder::TYPE, "_cascade/blocks/code", "cascade-admin" );
// new name for the copy
$new_name = "another-text-block";
// no workflow
$do_workflow = false;
$service->copy( $block_id, $parent_id, $new_name, $do_workflow );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function copy( \stdClass $identifier, \stdClass $newIdentifier, 
        string $newName="", bool $doWorkflow=false ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }

        $params  = new \stdClass();
        $params->destinationContainerIdentifier = $newIdentifier;
        
        if( $newName != "" )
            $params->newName = $newName;
        if( !is_null( $doWorkflow ) )
            $params->doWorkflow = $doWorkflow;    
        
        $params = array( 'copyParameters' => $params );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }

/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Creates the asset.</p><p>create:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("create"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("createResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("create"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("createResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(96) \"https://mydomain.edu:1234/api/v1/create?u=wing&amp;p=password\"
    [\"params\"]=>
    string(239) \"{\"asset\":{\"textBlock\":{\"text\":\"My new text block content\",\"metadataSetId\":\"618861da8b7ffe8377b637e8ad3dd499\",\"metadataSetPath\":\"_brisk:Block\",\"name\":\"new-text-block-again\",\"parentFolderPath\":\"_cascade\/blocks\/code\",\"siteName\":\"formats\"}}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>// get the image data
$img_url     = "http://www.upstate.edu/scripts/faculty/thumbs/nadkarna.jpg";
$img_binary  = file_get_contents( $img_url );
// the folder where the file should be created
$parent_path = 'images';
$site_name   = 'cascade-admin';
$img_name    = 'nadkarna.jpg';
// create the asset
$asset       = new \stdClass();
$asset->file = new \stdClass();
$asset->file->name = $img_name;
$asset->file->siteName = $site_name;
$asset->file->parentFolderPath = $parent_path;

// binary data must be converted to a char array
$asset->file->data = u\StringUtility::binaryToCharArray( $img_binary );
$service->create( $asset );    
</example>
<return-type>stdClass</return-type></documentation>
*/
    public function create( \stdClass $asset ) : \stdClass
    {
        //u\DebugUtility::dump( $asset );
/*
object(stdClass)#22 (2) {
  ["success"]=>
  bool(false)
  ["message"]=>
  string(74) "java.lang.IllegalStateException: Expected BEGIN_ARRAY but was BEGIN_OBJECT"
}
*/    
        $command = $this->url . __function__;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }

        $asset = array( 'asset' => $asset );
        $this->reply = $this->apiOperation( $command, $asset );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description><p>Creates an id object for an asset. Note the when the asset is a site asset, only the 32-digit hex ID string works. To fix this problem, the site metadata set container is retrieved, and the site id is read from the result.</p></description>
<example>$block_id = $service->createId( a\TextBlock::TYPE, "_cascade/blocks/code/text-block", "cascade-admin" );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createId(
        string $type, string $id_path, string $site_name = NULL ) : \stdClass
    {
        //u\DebugUtility::out( $id_path );
        $id = new \stdClass();
        
        if( $this->isHexString( $id_path ) )
            $id->id = $id_path;
        // patch for Cascade 8.7.1
        elseif( $type == c\T::SITE )
        {
            // retrieve the site root metadata set container
            $msc_id   = $this->createId( c\T::METADATASETCONTAINER, "/", $id_path );
            $msc      = $this->read( $msc_id );
            
            if( isset( $msc->metadataSetContainer->siteId ) )
                $id->id  = $msc->metadataSetContainer->siteId;
        }
        elseif( $type == c\T::GROUP || $type == c\T::USER )
        {
            $id->id = $id_path;
        }
        else
        {
            $id->path = new \stdClass();
            $id->path->path = $id_path;
            
            if( $id_path == "/" )
                $id->path->path = "%252F"; // the root folder
                
            $id->path->siteName = $site_name;
        }
        
        $id->type = $type;
        return $id;
    }
    
/**
<documentation><description><p>Creates an ID string to be attached to the URL.</p></description>
<example>$block_id = $service->createId( a\TextBlock::TYPE, "_cascade/blocks/code/text-block", "cascade-admin" );
$block_id_string = $service->createIdString( $block_id );</example>
<return-type>string</return-type></documentation>
*/
    public function createIdString( \stdClass $id ) : string
    {
        if( isset( $id->id ) )
            $id_string = $id->type . '/' . $id->id;
        elseif( isset( $id->path->path ) )
        {
            $path = $id->path->path;
        
            if( $path != "%252F" )
                $path = str_replace( " ", "%20", $path );
        
        
            if( $id->type == "role" ||
                $id->type == "site" ||
                $id->type == "group" ||
                $id->type == "user"
            )
            {
                $id_string = $id->type . '/' . $path;
            }
            else
            {
                $id_string = $id->type . '/' . $id->path->siteName . '/' . $path;
            }
        }
        else
            $id_string = "";
        
        $id_string = str_replace( "//", "/", $id_string );
        
        return $id_string;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Deletes the asset with the given identifier.</p><p>delete:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("delete"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("deleteResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("delete"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(135) \"https://mydomain.edu:1234/api/v1/delete/block/0959f8158b7ffe83785cac8a915f92fa?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";return $doc_string;
?>
</description>
<example>$path = "/_cascade/blocks/code/text-block2";
$service->delete( $service->createId( a\TextBlock::TYPE, $path, "cascade-admin" ) );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function delete( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
        
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Deletes the message with the given identifier.</p><p>deleteMessage:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("deleteMessage"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("deleteMessageResponse"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(144) \"https://mydomain.edu:1234/api/v1/deleteMessage/message/5dafb4228b7ffe833b19adb81c850f47?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$mid = "9e10ae5b8b7ffe8364375ac78e212e42";
$service->deleteMessage( $service->createId( c\T::MESSAGE, $mid ) );
</example>
<return-type>stdClass</return-type>
</documentation>
*/    public function deleteMessage( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Edits the given asset.</p><p>edit:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("edit"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("editResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("edit"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(2) {
  [0]=>
  array(1) {
    [\"command\"]=>
    string(133) \"https://mydomain.edu:1234/api/v1/read/block/c12da9c78b7ffe83129ed6d8411290fe?u=wing&amp;p=password\"
  }
  [1]=>
  array(2) {
    [\"command\"]=>
    string(94) \"https://mydomain.edu:1234/api/v1/edit?u=wing&amp;p=password\"
    [\"params\"]=>
    string(2019) \"{\"asset\":{\"xhtmlDataDefinitionBlock\":{\"structuredData\":{\"definitionId\":\"618863658b7ffe8377b637e8ee4f3e42\",\"definitionPath\":\"_brisk:Wysiwyg\",\"structuredDataNodes\":[{\"type\":\"text\",\"identifier\":\"display\",\"text\":\"yes\",\"recycled\":false},{\"type\":\"group\",\"identifier\":\"wysiwyg-group\",\"structuredDataNodes\":[{\"type\":\"text\",\"identifier\":\"wysiwyg-content\",\"text\":\"&lt;p>Content
.&lt;\/p>
&lt;p>
Text appended.&lt;\/p>\",\"recycled\":false},{\"type\":\"text\",\"identifier\":\"admin-options\",\"text\":\"::CONTENT-XML-CHECKBOX<span></span>::\",\"recycled\":false}],\"recycled\":false}]},\"expirationFolderRecycled\":false,\"metadataSetId\":\"618861da8b7ffe8377b637e8ad3dd499\",\"metadataSetPath\":\"_brisk:Block\",\"metadata\":{\"dynamicFields\":[{\"name\":\"macro\",\"fieldValues\":[{\"value\":\"processWysiwygMacro\"}]}]},\"reviewOnSchedule\":false,\"reviewEvery\":0,\"parentFolderId\":\"c12dceb28b7ffe83129ed6d8535fb721\",\"parentFolderPath\":\"_cascade\/blocks\/data\",\"lastModifiedDate\":\"Nov 29, 2017 8:19:41 AM\",\"lastModifiedBy\":\"wing\",\"createdDate\":\"Nov 15, 2017 2:35:16 PM\",\"createdBy\":\"wing\",\"path\":\"_cascade\/blocks\/data\/latin-wysiwyg\",\"siteId\":\"c12d8c498b7ffe83129ed6d81ea4076a\",\"siteName\":\"formats\",\"name\":\"latin-wysiwyg\",\"id\":\"c12da9c78b7ffe83129ed6d8411290fe\"}}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$asset = new \stdClass();
$asset->xhtmlDataDefinitionBlock = $block;
$service->edit( $asset );
</example>
<return-type>stdClass</return-type>
</documentation>
*/    
    public function edit( \stdClass $asset ) : \stdClass
    {
        $command = $this->url . __function__;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $asset = array( 'asset' => $asset );
        try
        {
            $this->reply = $this->apiOperation( $command, $asset );
        }
        catch( \Exception $e )
        {
            throw new e\EditingFailureException( $e );
        }
            
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Edits the given accessRightsInformation.</p><p>editAccessRights:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("editAccessRights"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("editAccessRightsResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("editAccessRights"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(2) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(146) \"https://mydomain.edu:1234/api/v1/readAccessRights/folder/c12d8d0d8b7ffe83129ed6d86dd9f853?u=wing&amp;p=password\"
  }
  [1]=>
  array(2) {
    [\"command\"]=>
    string(146) \"https://mydomain.edu:1234/api/v1/editAccessRights/folder/c12d8d0d8b7ffe83129ed6d86dd9f853?u=wing&amp;p=password\"
    [\"params\"]=>
    string(415) \"{\"accessRightsInformation\":{\"identifier\":{\"id\":\"c12d8d0d8b7ffe83129ed6d86dd9f853\",\"path\":{\"path\":\"\/\",\"siteId\":\"c12d8c498b7ffe83129ed6d81ea4076a\",\"siteName\":\"formats\"},\"type\":\"folder\",\"recycled\":false},\"aclEntries\":[{\"level\":\"read\",\"type\":\"group\",\"name\":\"CWT-Designers\"},{\"level\":\"write\",\"type\":\"user\",\"name\":\"wing\"},{\"level\":\"read\",\"type\":\"group\",\"name\":\"CWT-Designers\"}],\"allLevel\":\"none\"},\"applyToChildren\":true}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$accessRightInfo->aclEntries->aclEntry = $aclEntries;
// false: do not apply to children
$service->editAccessRights( $accessRightInfo, false ); 
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function editAccessRights(
        \stdClass $afInfo, 
        bool $applyToChildren=false ) : \stdClass
    {
        $id_string = $this->createIdString( $afInfo->identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $params  = array( 
            'accessRightsInformation' => $afInfo, 
            'applyToChildren'         => $applyToChildren );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Edits the preferences.</p><p>editPreference:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("editPreference"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("editPreferenceResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("editPreferenceResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(104) \"https://mydomain.edu:1234/api/v1/editPreference?u=wing&amp;p=password\"
    [\"params\"]=>
    string(93) \"{\"preference\":{\"name\":\"system_pref_global_area_external_link_check_on_publish\",\"value\":\"on\"}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->editPreference( "system_pref_allow_font_assignment", "off" );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function editPreference( string $name, string $value ) : \stdClass
    {
        $command = $this->url . __function__;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }

        $params = new \stdClass();
        $params->name = $name;
        $params->value = $value;
        $params = array( 'preference' => $params );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Edits the given workflowSettings.</p><p>editWorkflowSettings:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("editWorkflowSettings"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("editWorkflowSettingsResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("editWorkflowSettings"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(2) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(150) \"https://mydomain.edu:1234/api/v1/readWorkflowSettings/folder/c12dcf268b7ffe83129ed6d81d964c24?u=wing&amp;p=password\"
  }
  [1]=>
  array(2) {
    [\"command\"]=>
    string(150) \"https://mydomain.edu:1234/api/v1/editWorkflowSettings/folder/c12dcf268b7ffe83129ed6d81d964c24?u=wing&amp;p=password\"
    [\"params\"]=>
    string(418) \"{\"workflowSettings\":{\"workflowDefinitions\":[{\"id\":\"3bdf56e78b7f085600a5bfd5770fe30e\",\"path\":{\"path\":\"Automatic News Publish Create Edit Copy\",\"siteId\":\"fd27691f8b7f08560159f3f02754e61d\",\"siteName\":\"_common\"},\"type\":\"workflowdefinition\",\"recycled\":false}],\"inheritedWorkflowDefinitions\":[],\"inheritWorkflows\":false,\"requireWorkflow\":false},\"applyInheritWorkflowsToChildren\":false,\"applyRequireWorkflowToChildren\":false}\"
  }
}
";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>    $reply  = 
        $service->readWorkflowSettings( $service->createId( $type, $id ) );
    $workflowDefinitions = $reply->workflowSettings->workflowDefinitions;
    $inheritedWorkflowDefinitions =
        $reply->workflowSettings->inheritedWorkflowDefinitions;
    $reply = $service->editWorkflowSettings(
        $service->createId( $type, $id ),
        $workflowDefinitions,
        $inheritedWorkflowDefinitions,
        false, // inheritWorkflows
        false // requireWorkflow
        // false and false by default for applyInheritWorkflowsToChildren and
        // applyRequireWorkflowToChildren
    );    
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function editWorkflowSettings(
        \stdClass $identifier,
        $workflowDefinitions=NULL,
        $inheritedWorkflowDefinitions=NULL,
        bool $inheritWorkflows=false,
        bool $requireWorkflow=false,
        bool $applyInheritWorkflowsToChildren=false,
        bool $applyRequireWorkflowToChildren=false
    ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $params = new \stdClass();
        
        if( !is_null( $workflowDefinitions ) )
        {
            if( !is_array( $workflowDefinitions ) )
            {
                $workflowDefinitions = array( $workflowDefinitions );
            }
            $params->workflowDefinitions = $workflowDefinitions;
        }
        
        if( !is_null( $inheritedWorkflowDefinitions ) )
        {
            if( !is_array( $inheritedWorkflowDefinitions ) )
            {
                $inheritedWorkflowDefinitions = array( $inheritedWorkflowDefinitions );
            }
            $params->inheritedWorkflowDefinitions = $inheritedWorkflowDefinitions;
        }
        
        $params->inheritWorkflows = $inheritWorkflows;
        $params->requireWorkflow  = $requireWorkflow;
        $params = array(
            'workflowSettings' => $params,
            'applyInheritWorkflowsToChildren' => $applyInheritWorkflowsToChildren,
            'applyRequireWorkflowToChildren'  => $applyRequireWorkflowToChildren
        );
        
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description><p>Creates an asset object, bridging this class and the Asset classes.</p></description>
<example>$page = $service->getAsset( a\Page::TYPE, $page_id )</example>
<exception>NoSuchTypeException</exception>
<return-type>Asset</return-type></documentation>
*/
    public function getAsset(
        string $type, string $id_path, string $site_name=NULL ) : a\Asset
    {
        if( !in_array( $type, c\T::getTypeArray() ) )
            throw new e\NoSuchTypeException( 
                S_SPAN . "The type $type does not exist." . E_SPAN );
            
        $class_name = c\T::$type_class_name_map[ $type ]; // get class name
        $class_name = a\Asset::NAME_SPACE . "\\" . $class_name;
        
        try
        {
            return new $class_name( // call constructor
                $this, 
                $this->createId( $type, $id_path, $site_name ) );
        }
        catch( \Exception $e )
        {
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::out( $e->getMessage() ); }
            throw $e;
        }        
    }

/**
<documentation><description><p>Gets the audits object after the call of readAudits().</p></description>
<example>u\DebugUtility::dump( $service->getAudits() );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getAudits()
    {
        return $this->audits;
    }
    
/**
<documentation><description><p>Returns the array named <code>$commands</code>.</p></description>
<example>u\DebugUtility::dump( $service->getCommands() );</example>
<return-type>array</return-type></documentation>
*/
    public function getCommands() : array
    {
        return $this->commands;
    }
    
/**
<documentation><description><p>Gets the message after an operation.</p></description>
<example>echo $service->getMessage();</example>
<return-type>string</return-type></documentation>
*/
    public function getMessage() : string
    {
        if( isset( $this->reply->message ) )
        {
            $this->message = $this->reply->message;
        }
        return $this->message;
    }
    
/**
<documentation><description><p>Returns a URL string that can be used to read an asset.</p></description>
<example>echo $service->getReadURL( a\Page::TYPE, "c12eb9978b7ffe83129ed6d80132aa29" );</example>
<return-type>string</return-type></documentation>
*/
    public function getReadURL(
        string $type, string $id_path, string $site_name=NULL ) : string
    {
        $url = "";
        $id  = $this->createId( $type, $id_path, $site_name );
        $url = $this->createIdString( $id );
        $url = $this->url . "read" . '/' . $url . $this->auth;
        return $url;
    }

/**
<documentation><description><p>Returns the reply.</p></description>
<example>$reply = $service->getReply();</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReply() : \stdClass
    {
        return $this->reply;
    }

/**
<documentation><description><p>Returns <code>matches</code>.</p></description>
<example>$reply = $service->getReply();</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getSearchMatches() : array
    {
        return $this->reply->matches;
    }

/**
<documentation><description><p>Returns true if an operation is successful.</p></description>
<example>$service->readPreferences();
if( $service->isSuccessful() )
{
    // do something
}</example>
<return-type>bool</return-type></documentation>
*/
    public function isSuccessful() : bool
    {
        return $this->success;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Lists editor configurations. The <code>\$id</code> should
be an <code>stdClass</code> object, the ID of a site.</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listEditorConfigurations"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listEditorConfigurationsResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("listEditorConfigurationsResult"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("editorConfiguration"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(152) \"https://mydomain.edu:1234/api/v1/listEditorConfigurations/site/c12d8c498b7ffe83129ed6d81ea4076a?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";return $doc_string;
?>
</description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function listEditorConfigurations( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }

/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Lists all messages.</p><p>listMessages:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listMessages"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listMessagesResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("listMessagesResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(1) {
    [\"command\"]=>
    string(102) \"https://mydomain.edu:1234/api/v1/listMessages?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->listMessages();</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function listMessages() : \stdClass
    {
        return $this->performOperation( __function__ );
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Lists all sites.</p><p>listSites:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listSites"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listSitesResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("listSitesResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(1) {
    [\"command\"]=>
    string(99) \"https://mydomain.edu:1234/api/v1/listSites?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->listSites();</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function listSites() : \stdClass
    {
        return $this->performOperation( __function__ );
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Lists all subscribers of an asset.</p><p>listSubscribers:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listSubscribers"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("listSubscribersResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("listSubscribers"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("listSubscribersResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(144) \"https://mydomain.edu:1234/api/v1/listSubscribers/block/c12d9e7b8b7ffe83129ed6d851168bbf?u=wing&amp;p=password\"
    [\"params\"]=>
    string(56) \"{\"id\":\"c12d9e7b8b7ffe83129ed6d851168bbf\",\"type\":\"block\"}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->listSubscribers( 
    $service->createId( $type, $path, $site_name ) );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function listSubscribers( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Marks a message as 'read' or 'unread'.</p><p>markMessage:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("markMessage"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("markMessageResponse"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(142) \"https://mydomain.edu:1234/api/v1/markMessage/message/6e8c72538b7ffe833b19adb8d79fa0bc?u=wing&amp;p=password\"
    [\"params\"]=>
    string(21) \"{\"markType\":\"unread\"}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->markMessage( 
    $service->createIdWithIdType( $id, c\T::MESSAGE ), 
    c\T::UNREAD );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function markMessage( \stdClass $identifier, string $markType="read" ) :
        \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $params = new \stdClass();
        $params->markType = $markType;
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Moves the asset with the given identifier.</p><p>move:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("move"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("moveResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("move"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("moveParameters"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(133) \"https://mydomain.edu:1234/api/v1/move/block/089c28d98b7ffe83785cac8a79fe2145?u=wing&amp;p=password\"
    [\"params\"]=>
    string(130) \"{\"moveParameters\":{\"destinationContainerIdentifier\":{\"id\":\"c12dce3c8b7ffe83129ed6d8f4f9b820\",\"type\":\"folder\"},\"doWorkflow\":false}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->move( $block_id, $parent_id, $new_name, $do_workflow );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function move( \stdClass $identifier, \stdClass $parentId=NULL,
        string $newName="", bool $doWorkflow=false ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $params = new \stdClass();
        
        if( !is_null( $parentId ) )
            $params->destinationContainerIdentifier = $parentId;
        if( !is_null( $newName ) && $newName != "" )
            $params->newName = $newName;
        if( !is_null( $doWorkflow ) )
            $params->doWorkflow = $doWorkflow;
            
        $params = array( 'moveParameters' => $params );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Performs the workflow transition.</p><p>performWorkflowTransition:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("performWorkflowTransition"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("performWorkflowTransitionResponse"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(115) \"https://mydomain.edu:1234/api/v1/performWorkflowTransition?u=wing&amp;p=password\"
    [\"params\"]=>
    string(146) \"{\"workflowTransitionInformation\":{\"workflowId\":\"1238fd1e8b7ffe83785cac8aa6c35877\",\"actionIdentifier\":\"finished\",\"transitionComment\":\"No comment\"}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->performWorkflowTransition( $id, $action, 'Testing' );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function performWorkflowTransition(
        string $workflowId, string $actionIdentifier, string $transitionComment=''
    ) : \stdClass
    {
        $command   = $this->url . __function__;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $params = new \stdClass();
        $params->workflowId        = $workflowId;
        $params->actionIdentifier  = $actionIdentifier;
        $params->transitionComment = $transitionComment;
        $params = array( 'workflowTransitionInformation' => $params );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Publishes the asset with the given identifier.</p><p>publish:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("publish"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("publishResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("publish"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("publishInformation"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(135) \"https://mydomain.edu:1234/api/v1/publish/page/9a1416488b7f08ee5d439b31921d08b6?u=wing&amp;p=password\"
    [\"params\"]=>
    string(185) \"{\"publishInformation\":{\"destinations\":[{\"id\":\"c34b58ca8b7f08ee4fe76bb83ba1613b\",\"type\":\"destination\"},{\"id\":\"c34d2a868b7f08ee4fe76bb87c352c01\",\"type\":\"destination\"}],\"unpublish\":false}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$folder_path = "projects/web-services/reports";
$service->publish( $service->createId( a\Folder::TYPE, $folder_path, "cascade-admin" ) );

$p  = $cascade->getAsset( a\Page::TYPE, "9a1416488b7f08ee5d439b31921d08b6" );
$dest_web =
    $cascade->getAsset( a\Destination::TYPE, "03b2789b8b7f08ee357fca92fc1cfc40" );
$dest_www =
    $cascade->getAsset( a\Destination::TYPE, "c34d2a868b7f08ee4fe76bb87c352c01" );
$service->publish( $p->getIdentifier(), 
    array( $dest_web->getIdentifier(), $dest_www->getIdentifier() ) );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function publish(
        \stdClass $identifier, $destination=NULL, $unpublish=false ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        if( isset( $destination ) )
        {
            if( !is_array( $destination ) )
            {
                $destination = array( $destination );
            }
            
            $params = new \stdClass();
            $params->publishInformation = new \stdClass();
            $params->publishInformation->destinations = $destination;
            $params->publishInformation->unpublish    = $unpublish;
            $this->reply = $this->apiOperation( $command, $params );
        }
        else
        {
            $this->reply = $this->apiOperation( $command );
        }
        $this->success = $this->reply->success;
        return $this->reply;
    }

/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Reads the asset with the given identifier.</p><p>read:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("read"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("read"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [\"command\"]=>
  string(133) \"https://mydomain.edu:1234/api/v1/read/block/c12da9c78b7ffe83129ed6d8411290fe?u=wing&amp;p=password\"
}

array(1) {
  [\"command\"]=>
  string(116) \"https://mydomain.edu:1234/api/v1/read/page/about/index?u=wing&amp;p=password\"
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->read( 
    $service->createId( a\Folder::TYPE, $path, "cascade-admin" ) );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function read( \stdClass $identifier )
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }

        $this->reply   = $this->apiOperation( $command );
        $this->success = $this->reply->success;
        
        return $this->reply->asset ?? NULL;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Reads the access rights of the asset with the given identifier.</p><p>readAccessRights:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readAccessRights"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readAccessRightsResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readAccessRights"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readAccessRightsResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(2) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(146) \"https://mydomain.edu:1234/api/v1/readAccessRights/folder/c12d8d0d8b7ffe83129ed6d86dd9f853?u=wing&amp;p=password\"
  }
  [1]=>
  array(2) {
    [\"command\"]=>
    string(146) \"https://mydomain.edu:1234/api/v1/editAccessRights/folder/c12d8d0d8b7ffe83129ed6d86dd9f853?u=wing&amp;p=password\"
    [\"params\"]=>
    string(415) \"{\"accessRightsInformation\":{\"identifier\":{\"id\":\"c12d8d0d8b7ffe83129ed6d86dd9f853\",\"path\":{\"path\":\"\/\",\"siteId\":\"c12d8c498b7ffe83129ed6d81ea4076a\",\"siteName\":\"formats\"},\"type\":\"folder\",\"recycled\":false},\"aclEntries\":[{\"level\":\"read\",\"type\":\"group\",\"name\":\"CWT-Designers\"},{\"level\":\"write\",\"type\":\"user\",\"name\":\"wing\"},{\"level\":\"read\",\"type\":\"group\",\"name\":\"CWT-Designers\"}],\"allLevel\":\"none\"},\"applyToChildren\":true}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->readAccessRights( 
    $service->createId( a\TextBlock::TYPE, $path, "cascade-admin" ) );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function readAccessRights( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }

/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Reads the audits of the asset with the given parameters.</p><p>readAudits:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readAudits"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readAuditsResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("auditParameters"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readAuditsResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(101) \"https://mydomain.edu:1234/api/v1/readAudits/?u=wing&amp;p=password\"
    [\"params\"]=>
    string(40) \"{\"auditParameters\":{\"auditType\":\"copy\"}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$page_id = "980d85f48b7f0856015997e492c9b83b";
$audit_params = new \stdClass();
$audit_params->identifier = $service->createId( a\Page::TYPE, $page_id );
$audit_params->auditType  = c\T::EDIT;
$service->readAudits( $audit_params );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function readAudits(
        \stdClass $identifier, \stdClass $auditParams=NULL ) : \stdClass
    {
    	$id_string = "";
    	
        if( isset( $identifier->identifier->id ) )
        {
            $id_string = $this->createIdString( $identifier->identifier );
        }
        // properties like username and roleid are created in Asset::getAudits
        elseif( isset( $identifier->username ) )
            $id_string = "user" . "/" . $identifier->username;
        elseif( isset( $identifier->groupname ) )
            $id_string = "group" . "/" . $identifier->groupname;
        elseif( isset( $identifier->roleid ) )
            $id_string = "role" . "/" . $identifier->roleid;
        
        $command = $this->url . __function__ . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }  
        
        if( !is_null( $auditParams ) )
        {
            $params = array( 'auditParameters' => $auditParams );
            $this->reply = $this->apiOperation( $command, $params );
        }
        else
            $this->reply = $this->apiOperation( $command );
        $this->audits  = $this->reply->audits;
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Reads the preferences.</p><p>readPreferences:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readPreferences"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readPreferencesResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readPreferencesResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(1) {
    [\"command\"]=>
    string(105) \"https://mydomain.edu:1234/api/v1/readPreferences?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$service->readPreferences();</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function readPreferences() : \stdClass
    {
        return $this->performOperation( __function__ );
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Reads the workflow information associated with the given identifier.</p><p>readWorkflowInformation:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readWorkflowInformation"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readWorkflowInformationResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readWorkflowInformationResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(151) \"https://mydomain.edu:1234/api/v1/readWorkflowInformation/page/c12deeb18b7ffe83129ed6d85a5d3d95?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$path = '/projects/web-services/reports/creating-format';
$service->readWorkflowInformation( 
    $service->createId( a\Page::TYPE, $path, "cascade-admin" ) );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function readWorkflowInformation( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Reads the workflow settings associated with the given identifier.</p><p>readWorkflowSettings:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readWorkflowSettings"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("readWorkflowSettingsResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readWorkflowSettings"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("readWorkflowSettingsResult"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(150) \"https://mydomain.edu:1234/api/v1/readWorkflowSettings/folder/b70a87c38b7ffe8353cc17e9fe08ff77?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$site_name = "cascade-admin";
$service->readWorkflowSettings( 
    $service->createId( a\Folder::TYPE, "/", $site_name ) );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function readWorkflowSettings( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
/**
<documentation><description><p>Retrieves a property of an asset.</p></description>
<example>$page = $service->retrieve( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>mixed</return-type></documentation>
*/
    public function retrieve( \stdClass $id )
    {
        $property = c\T::$type_property_name_map[ $id->type ];
        $asset    = $this->read( $id );

        if( isset( $this->reply->asset ) )
            return $this->reply->asset->$property;
        return NULL;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Searches for some entity.</p><p>search:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("search"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("searchResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("searchResult"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("search-matches"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("searchInformation"));
$doc_string .= "\r";
// searchFields does not work in 7.14
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("searchFields"));
$doc_string .= "\r";
// searchTypes does not work in 7.14
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("searchTypes"));
$doc_string .= "\r";
// searchFieldString does not work in 7.14
$doc_string .= $eval->replaceBrackets($service->getSimpleTypeXMLByName("searchFieldString"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(1) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(96) \"https://mydomain.edu:1234/api/v1/search?u=wing&amp;p=password\"
    [\"params\"]=>
    string(114) \"{\"searchInformation\":{\"searchTerms\":\"group\",\"siteId\":\"61885ac08b7ffe8377b637e83a86cca5\",\"searchTypes\":[\"format\"]}}\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function search( \stdClass $searchInfo ) : \stdClass
    {
        $command = $this->url . __function__;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $params  = array( 'searchInformation' => $searchInfo );
        $this->reply   = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Sends a message. Note that this operation is deprecated.</p><p>sendMessage:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("sendMessage"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("sendMessageResponse"));
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$message          = new \stdClass();
$message->to      = 'test'; // a group
$message->from    = 'chanw';
$message->subject = 'test';
$message->body    = 'This is a test. This is only a test.';
$service->sendMessage( $message );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function sendMessage( \stdClass $message ) 
    {
        $command = $this->url . __function__;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }

        $params  = new \stdClass();
        $params  = array( 'message' => $params );
        $this->reply   = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }    
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Copies the site with the given identifier.</p><p>siteCopy:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("siteCopy"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("siteCopyResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("siteCopy"));
$doc_string .= "</pre>";
$doc_string .= "<p>REST command dump:</p><pre>";
$doc_string .= "array(2) {
  [0]=>
  array(2) {
    [\"command\"]=>
    string(98) \"https://mydomain.edu:1234/api/v1/siteCopy?u=wing&amp;p=password\"
    [\"params\"]=>
    string(115) \"{\"originalSiteId\":\"6a8d58418b7ffe83164c9314aed51787\",\"originalSiteName\":\"_rwd_seed\",\"newSiteName\":\"_rwd_seed_copy\"}\"
  }
  [1]=>
  array(1) {
    [\"command\"]=>
    string(136) \"https://mydomain.edu:1234/api/v1/read/metadatasetcontainer/_rwd_seed_copy/%252f?u=wing&amp;p=password\"
  }
}";
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$seed_site_id   = "a0d0fb818b7f08ee0990fe6e89648961";
$seed_site_name = "_rwd_seed";
$new_site_name  = "access-test";
$service->$seed_site_id   = "a0d0fb818b7f08ee0990fe6e89648961";
$seed_site_name = "_rwd_seed";
$new_site_name  = "access-test";
$service->siteCopy( $seed_site_id, $seed_site_name, $new_site_name );
</example>
<return-type></return-type>
</documentation>
*/
    public function siteCopy(
        string $originalSiteId, string $originalSiteName, 
        string $newSiteName, int $max_wait_seconds=0 ) :
        \stdClass
    {
        $command   = $this->url . __function__;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $params  = new \stdClass();
        $params->originalSiteId   = $originalSiteId;
        $params->originalSiteName = $originalSiteName;
        $params->newSiteName      = $newSiteName;
        $this->reply   = $this->apiOperation( $command, $params );
        u\DebugUtility::dump( $this->reply );
        
        // If $max_wait_seconds is less than 1 then this will immediately skip past the while loop
        if( $max_wait_seconds < 1 )
            $max_wait_seconds = ( (int) ini_get( 'max_execution_time' ) - 2 );

        // max_execution_time could be 0 especially for CLI so we need to re-check $max_wait_seconds
        if( $max_wait_seconds < 1 )
            $max_wait_seconds = 600;
            
        $start = microtime( true );
        $site_copied_within_time_limit = false;

		u\DebugUtility::dump( $this->reply->success );

        if( $this->reply->success === true )
        {
            $command = $this->url .
                "read/metadatasetcontainer/$newSiteName/%252f";
        
            if( $this->auth_in_content === false )
            {
                $command .= $this->auth;
            }
                
            while( ( microtime( true ) - $start ) < $max_wait_seconds )
            {
                $this->reply = $this->apiOperation( $command );
                
                if( $this->reply->success === true )
                {
                    $site_copied_within_time_limit = true;
                    break;
                }
                else
                {
                    sleep( 1 );
                }
            }
            
            $this->success = $this->reply->success;
        }
        else
        {
            throw new e\SiteCreationFailureException(
                S_SPAN . "Failed to create the new site." . E_SPAN );
        }
        
        if( !$site_copied_within_time_limit )
        {
            throw new e\SiteCreationFailureException(
                S_SPAN . 'Site copy has exceeded the max wait time of ' .
                $max_wait_seconds .
                '. The site copy process may still be processing within Cascade.' .
                E_SPAN );
        }
        
        return $this->reply;
    }
    
/**
<documentation><description><p>Unpublishes the asset with the given identifier.</p></description>
<example>$service->unpublish( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function unpublish( \stdClass $identifier, $destination=NULL ) : \stdClass
    {
        return $this->publish( $identifier, $destination, true );
    }
    
    private function performOperation( string $opName ) : \stdClass
    {
        $command = $this->url . $opName;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }

        $this->reply = $this->apiOperation( $command );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    private function performOperationWithIdentifier(
        string $opName, \stdClass $identifier ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        
        $command = $this->url . $opName . '/' . $id_string;
        
        if( $this->auth_in_content === false )
        {
            $command .= $this->auth;
        }
        
        $this->reply = $this->apiOperation( $command, $identifier );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    private function getAuthString()
    {
        if( is_string( $this->auth ) )
        {
            $authString = str_replace( "u=", "", trim( $this->auth, '?' ) );
            $authString = str_replace( "p=", "", $authString );
            $authString = str_replace( "&", ":", $authString );
        }
        else
        {
            $authString = $this->auth->u . ":" . $this->auth->p;
        }
        
        $authString = base64_encode( $authString );
        
        return $authString;
    }

    // from the constructor
    /*@var string The url */
    private $url;
    /*@var stdClass The authentication */
    private $auth;
    
    // from the response
    /*@var string The message of the response */
    private $message;
    /*@var string The string 'true' or 'false' */
    private $success;
    /*@var string The id string of a created asset */
    //private $createdAssetId;
    /*@var stdClass The object returned from an operation */
    private $reply;
    /*@var stdClass The audits object */
    private $audits;
    /*@var stdClass The searchMatches object */
    private $searchMatches;
    /*@var stdClass The listed editor configurations */
    private $listed_editor_configurations;
    /*@var stdClass The listed messages */
    private $listed_messages;
    
    private $preferences;
    private $commands;
    private $auth_in_content;
}
?>