<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
                       German Drulyk <drulykg@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/12/2018 Integrated German's new code into siteCopy.
              success should be a bool, not a string.
  * 1/8/2018 Changed createId so that the root metadata set container is read when
  reading a site. Added a while loop in siteCopy.
  * 1/5/2018 Added code to createIdString to escape space.
  * 1/4/2018 Added cloud transport-related entries in $types and $properties.
  Added more tests in createIdString. Added the $command array and related methods.
  * 12/29/2017 Added getReadURL.
 */
namespace cascade_ws_AOHS;

use cascade_ws_constants as c;
use cascade_ws_utility   as u;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_exception as e;

class AssetOperationHandlerService
{
    const DEBUG = true;
    const DUMP  = false;
    const NAME_SPACE = "cascade_ws_AOHS";

    public function __construct( string $url, \stdClass $auth )
    {
        $this->url    = $url;
        $this->auth   = $auth;
        $this->message        = '';
        $this->success        = '';
        $this->createdAssetId = '';
        $this->reply = new \stdClass();
        $this->commands = array();

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
/*    
    function batch( array $operations ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $params            = new \stdClass();
        $params = array( 'operation' => $operations );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
*/
    public function checkIn( \stdClass $identifier, string $comments="" ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
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
    
    public function checkOut( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
    public function clearCommands()
    {
        $this->commands = array();
    }
    
    public function getCommands() : array
    {
        return $this->commands;
    }
    
    public function copy( \stdClass $identifier, \stdClass $newIdentifier, 
        string $newName="", bool $doWorkflow=false ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
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

/*
object(stdClass)#22 (2) {
  ["success"]=>
  bool(false)
  ["message"]=>
  string(74) "java.lang.IllegalStateException: Expected BEGIN_ARRAY but was BEGIN_OBJECT"
}
*/    
    public function create( \stdClass $asset ) : \stdClass
    {
        //u\DebugUtility::dump( $asset );
        $command = $this->url . __function__ . $this->auth;
        $asset = array( 'asset' => $asset );
        $this->reply = $this->apiOperation( $command, $asset );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
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
            // retrieve site id from metadata set
            
            //u\DebugUtility::dump( $msc );
            
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
    
    public function createIdString( \stdClass $id )
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
    
    public function delete( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
        
    public function deleteMessage( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
    public function edit( \stdClass $asset ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $asset = array( 'asset' => $asset );
        $this->reply = $this->apiOperation( $command, $asset );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function editAccessRights(
        \stdClass $afInfo, 
        bool $applyToChildren=false ) : \stdClass
    {
        $id_string = $this->createIdString( $afInfo->identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
        $params  = array( 
            'accessRightsInformation' => $afInfo, 
            'applyToChildren'         => $applyToChildren );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function editPreference( string $name, string $value ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $params = new \stdClass();
        $params->name = $name;
        $params->value = $value;
        $params = array( 'preference' => $params );
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
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
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
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
    
    public function getAsset( string $type, string $id_path, string $site_name=NULL ) : a\Asset
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

    public function getAudits()
    {
        return $this->audits;
    }
    
    public function getReadURL(
        string $type, string $id_path, string $site_name=NULL ) : string
    {
        $url = "";
        $id  = $this->createId( $type, $id_path, $site_name );
        $url = $this->createIdString( $id );
        $url = $this->url . "read" . '/' . $url . $this->auth;
        return $url;
    }

    public function getReply() : \stdClass
    {
        return $this->reply;
    }
    
    public function isRest() : bool
    {
        return true;
    }

    public function isSoap() : bool
    {
        return false;
    }

    public function listEditorConfigurations( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }

    public function listMessages() : \stdClass
    {
        return $this->performOperation( __function__ );
    }
    
    public function listSites() : \stdClass
    {
        return $this->performOperation( __function__ );
    }
    
    public function listSubscribers( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
    public function markMessage( \stdClass $identifier, string $markType="read" ) :
        \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
        $params = new \stdClass();
        $params->markType = $markType;
        $this->reply = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function performWorkflowTransition(
        string $workflowId, string $actionIdentifier, string $transitionComment=''
    ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $params = new \stdClass();
        $params->workflowId        = $workflowId;
        $params->actionIdentifier  = $actionIdentifier;
        $params->transitionComment = $transitionComment;
        $params = array( 'workflowTransitionInformation' => $params );
        $this->reply = $this->apiOperation( $command, $params );
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
    
    public function publish(
        \stdClass $identifier, $destination=NULL, $unpublish=false ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
        
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

/*
https://mydomain.myorg.edu:1234/api/v1/read/format/9fea17498b7ffe84964c931447df1bfb?u=wing&p=password
*/
    public function read( \stdClass $identifier )
    {
        $id_string = $this->createIdString( $identifier );
        $command   = $this->url . __function__ . '/' . $id_string . $this->auth;
        $this->reply   = $this->apiOperation( $command );
        $this->success = $this->reply->success;
        
        if( $this->success === true )
            return $this->reply->asset;
        else
            return NULL;
    }
    
    public function readAccessRights( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }

    public function readAudits(
        \stdClass $identifier, \stdClass $auditParams=NULL ) : \stdClass
    {
        if( isset( $identifier->identifier ) )
            $id_string = $this->createIdString( $identifier->identifier );
        // properties like username and roleid are created in Asset::getAudits
        elseif( isset( $identifier->username ) )
            $id_string = "user" . "/" . $identifier->username;
        elseif( isset( $identifier->groupname ) )
            $id_string = "group" . "/" . $identifier->groupname;
        elseif( isset( $identifier->roleid ) )
            $id_string = "role" . "/" . $identifier->roleid;
            
        $command = $this->url . __function__ . '/' . $id_string  . $this->auth;
        
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
    
    public function readPreferences() : \stdClass
    {
        return $this->performOperation( __function__ );
    }
    
    public function readWorkflowInformation( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
    public function readWorkflowSettings( \stdClass $identifier ) : \stdClass
    {
        return $this->performOperationWithIdentifier( __function__, $identifier );
    }
    
    public function search( \stdClass $searchInfo ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $params  = array( 'searchInformation' => $searchInfo );
        $this->reply   = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function sendMessage( \stdClass $message ) 
    {
        $command = $this->url . __function__ . $this->auth;
        $params  = new \stdClass();
        $params  = array( 'message' => $params );
        $this->reply   = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }    
    
    public function siteCopy(
        string $originalSiteId, string $originalSiteName, 
        string $newSiteName, int $max_wait_seconds=0 ) :
        \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
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
            
        if( $this->reply->success === true )
        {
            $command = $this->url .
                "read/metadatasetcontainer/$newSiteName/%252f" . $this->auth;
                
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
                S_SPAN . $this->reply->message . E_SPAN );
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
    
    public function unpublish( \stdClass $identifier, $destination=NULL ) : \stdClass
    {
        return $this->publish( $identifier, $destination, true );
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
    
    public function retrieve( \stdClass $id )
    {
        $property = c\T::$type_property_name_map[ $id->type ];
        $asset    = $this->read( $id );

        if( isset( $this->reply->asset ) )
            return $this->reply->asset->$property;
        return NULL;
    }
    
    public function getMessage()
    {
        if( isset( $this->reply->message ) )
        {
            $this->message = $this->reply->message;
        }
        return $this->message;
    }
    
    private function apiOperation( string $command, $params=NULL )// : \stdClass
    {
        //u\DebugUtility::dump( $command );
        //u\DebugUtility::dump( $params );
    
        $input_params = array(
            'http' => array(
                'header'  => "Authorization: Basic " . $this->getAuthString() . "\r\n" .
                    "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST'
            ) );
        
        $entry = array( "command" => $command );
        
        if( !is_null( $params ) )
        {
            $input_params[ 'http' ][ 'content' ] = json_encode( $params );
            $entry[ "params" ] = json_encode( $params );
        }
        
        $this->commands[] = $entry;

        return json_decode(
            file_get_contents(
                $command,
                false,
                stream_context_create( $input_params ) ) );
    }
    
    private function performOperation( string $opName ) : \stdClass
    {
        $command = $this->url . $opName . $this->auth;
        $this->reply = $this->apiOperation( $command );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    private function performOperationWithIdentifier(
        string $opName, \stdClass $identifier ) : \stdClass
    {
        //u\DebugUtility::dump( $identifier );
    
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . $opName . '/' . $id_string . $this->auth;
        //u\DebugUtility::out( $command );
        $this->reply = $this->apiOperation( $command, $identifier );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    private function getAuthString()
    {
        $authString = str_replace( "u=", "", trim( $this->auth, '?' ) );
        $authString = str_replace( "p=", "", $authString );
        $authString = str_replace( "&", ":", $authString );
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
    private $createdAssetId;
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