<?php
namespace cascade_ws_AOHS;

use cascade_ws_constants as c;
use cascade_ws_utility   as u;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_exception as e;

class AssetOperationHandlerService
{
    const DEBUG = false;
    const DUMP  = false;
    const NAME_SPACE = "cascade_ws_AOHS";

    public function __construct( string $url, \stdClass $auth )
    {
        $this->url    = $url;
        $this->auth   = $auth;
        $this->message        = '';
        $this->success        = '';
        $this->createdAssetId = '';
        $this->lastRequest    = '';
        $this->lastResponse   = '';
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
    
    public function create( \stdClass $asset ) : \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $asset = array( 'asset' => $asset );
        $this->reply = $this->apiOperation( $command, $asset );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function createId(
        string $type, string $id_path, string $site_name = NULL ) : \stdClass
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
        \stdClass $identifier, \stdClass $afInfo, 
        bool $applyToChildren=false ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
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
    
    public function getAudits() : \stdClass
    {
        return $this->audits;
    }

    public function getReply() : \stdClass
    {
        return $this->reply;
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
    
    public function read( \stdClass $identifier ) : \stdClass
    {
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . __function__ . '/' . $id_string . $this->auth;
        $this->reply = $this->apiOperation( $command );
        $this->success = $this->reply->success;
        
        if( $this->success )
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
        $id_string = $this->createIdString( $identifier );
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
        string $originalSiteId, string $originalSiteName, string $newSiteName ) :
        \stdClass
    {
        $command = $this->url . __function__ . $this->auth;
        $params  = new \stdClass();
        $params->originalSiteId   = $originalSiteId;
        $params->originalSiteName = $originalSiteName;
        $params->newSiteName      = $newSiteName;
        $this->reply   = $this->apiOperation( $command, $params );
        $this->success = $this->reply->success;
        return $this->reply;
    }
    
    public function unpublish( \stdClass $identifier, $destination=NULL ) : \stdClass
    {
        return $this->publish( $identifier, $destination, true );
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

    private function apiOperation( $command, $params=NULL ) : \stdClass
    {
        $input_params = array(
            'http' => array(
                'header'  => "Authorization: Basic " . $this->getAuthString() . "\r\n" .
                	"Content-Type: application/x-www-form-urlencoded\r\n",
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
        $id_string = $this->createIdString( $identifier );
        $command = $this->url . $opName . '/' . $id_string . $this->auth;
        $this->reply = $this->apiOperation( $command );
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
    
    // 42 properties
    // property array to generate methods
    /*@var array The array of property names */
    private $properties = array(
        c\P::ASSETFACTORY,
        c\P::ASSETFACTORYCONTAINER,
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
    
    // 46 types
    /*@var array The array of types of assets */
    private $types = array(
        c\T::ASSETFACTORY,
        c\T::ASSETFACTORYCONTAINER,
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