<?php
namespace cascade_ws_AOHS;

use cascade_ws_utility as u;

class AssetOperationHandlerService
{
    const DEBUG = false;
    const DUMP  = false;
    const NAME_SPACE = "cascade_ws_AOHS";

    public function __construct( string $url, \stdClass $auth )
    {
        $this->url    = $url;
        $this->auth   = $auth;
        $this->reply = new \stdClass();

        try
        {
            $json_str = json_encode( $auth );
            $json_str = trim( $json_str, "{}" );
            $json_str = str_replace( '"', '', $json_str );
            $json_str = str_replace( ':', '=', $json_str );
            $json_str = str_replace( ',', '&', $json_str );
            $auth_str = str_replace( " ", "%20", $json_str );
            $this->auth = '?' . $auth_str;
        }
        catch( \Exception $e )
        {
            throw new e\ServerException( S_SPAN . $e->getMessage() . E_SPAN );
        }
    }
    
    function apiOperation( $command, $params=NULL )
    {
        $input_params = array(
            'http' => array(
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST'
            ) );
            
        if( !is_null( $params ) )
        {
            $input_params[ 'http' ][ 'content' ] = json_encode( $params );
        }
            
        return json_decode(
            file_get_contents(
                $command, 
                false, 
                stream_context_create( $input_params ) ) );
    }

    public function createId( string $type, string $id_path, string $site_name = NULL )
    {
        $id = new \stdClass();
        
        if( $this->isHexString( $id_path ) )
            $id->id = $id_path;
        else
        {
            $id->path = new \stdClass();
            $id->path->path     = $id_path;
            $id->path->siteName = $site_name;
        }
        $id->type = $type;
        return $id;
    }
    
    public function getReply()
    {
        return $this->reply;
    }
    
    public function create( \stdClass $asset ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $asset = array( 'asset' => $asset );
        $this->reply = $this->apiOperation( $command, $asset );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function delete( \stdClass $identifier ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
        $this->reply = json_decode( file_get_contents( $command ) );
        $this->success = $this->reply->success;
        return $this->reply;
    }
        
    public function edit( \stdClass $asset ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $asset = array( 'asset' => $asset );
        $this->reply = $this->apiOperation( $command, $asset );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function move( \stdClass $identifier, \stdClass $newIdentifier=NULL,
        string $newName="", bool $doWorkflow=false ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
        $params = new \stdClass();
        
        if( !is_null( $newIdentifier ) )
            $params->destinationContainerIdentifier = $newIdentifier;
        if( !is_null( $newName ) && $newName != "" )
            $params->newName = $newName;
        if( !is_null( $doWorkflow ) )
            $params->doWorkflow = $doWorkflow;
            
        $params = array( 'moveParameters' => $params );
        
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function read( \stdClass $identifier ) : \stdClass
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $identifier ); }
        
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
        $this->reply = json_decode( file_get_contents( $command ) );
        $this->success = $this->reply->success;
        
        if( $this->success )
            return $this->reply->asset;
        else
            return NULL;
    }
    
    public function createIdString( \stdClass $id )
    {
        $id_string = $id->type . '/';
        
        if( isset( $id->id ) )
            $id_string = $id_string . $id->id;
        else
            $id_string = $id->type . '/' . $id->path->siteName . '/' . $id->path->path;
        return $id_string;
    }
    
    public function isHexString( string $string ) : bool
    {
        $pattern = "/[0-9a-f]{32}/";
        $matches = array();
        
        preg_match( $pattern, $string, $matches );
        
        if( isset( $matches[ 0 ] ) )
            return $matches[ 0 ] == $string;
        return false;
    }

    public function isSuccessful() : bool
    {
        return $this->success;
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
    private $createdAssetId;
    /*@var string The XML of the last request */
    private $lastRequest;
    /*@var string The XML of the last response */
    private $lastResponse;
    /*@var stdClass The object returned from an operation */
    private $reply;
    /*@var stdClass The audits object */
    private $audits;
    /*@var stdClass The searchMatches object */
    private $searchMatches;
    /*@var stdClass The listed messages */
    private $listed_messages;
    
    private $preferences;
}
?>