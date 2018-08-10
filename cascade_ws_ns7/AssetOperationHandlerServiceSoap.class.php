<?php
/**
  Author: Wing Ming Chan, German Drulyk
  Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                     German Drulyk <drulykg@upstate.edu>
  MIT Licensed
  Modification history:
   7/19/2018 Moved WSDL-related constants and methods to the parent class.
   4/12/2018 Added exception throwing to edit.
   1/18/2018 Moved REST dump to AssetOperationHandlerServiceRest.
   1/17/2018 Moved the private arrays to the parent.
   1/8/2018 Added a while loop in siteCopy.
   1/4/2018 Added cloud transport-related entries in $types and $properties.
   12/21/2017 Added isSoap and isRest.
   11/28/2017 Bugs fixed in publish and unpublish related to destinations.
   10/12/2017 Updated documentation.
   7/3/2017 Removed example from search.
   6/19/2017 Added getXMLFragments.
   6/15/2017 Removed lists of XML.
             Added an DOMXpath object to process the WSDL.
             Added methods to process the WSDL and return lists and XML strings.
   6/14/2017 Added lists of elements, simple types, and complex types.
   6/13/2017 Added WSDL.
   9/2/2016 Changed checkOut so that it returns the id of the working copy.
   8/15/2016 Added comments to work with ReflectionUtility.
   7/6/2015 Added getPreferences, readPreferences, and editPreferences.
   6/23/2015 Reverted the signature of performWorkflowTransition.
   5/26/2015 Added namespace
   5/21/2015 Added more comments
   5/5/2015 Added type hints to several methods
   12/10/2014 Fixed a bug in createId
   10/5/2014 Added getAsset
   8/15/2014 Modified createId to take care of whitespace and /
   7/18/2014 Modified createId to take care of assets in Global
   7/14/2014 Added getUrl
   6/6/2014 Fixed a bug in publish and unpublish
   4/17/2014 Modified the signature of retrieve so that the property can be empty
   3/24/2014 Modified createId to throw exceptions and added isHexString
   2/26/2014 Removed workflowConfiguration from property, and twitter feed block from property and type
   2/24/2014 Fixed a typo in the Property class
   1/8/2014 Changed all property strings to constants, added the $types array and a getType method
   10/30/2013 Fixed a bug in __call
   10/29/2013 Added storeResults
   10/28/2013 Added/modified all documentation comments
   10/26/2013 Added retrieve
   10/25/2013 Added the enhanced __call method to generate read and get
   10/21/2013 Added all operation methods
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
<p>This class is a child class of <code>AssetOperationHandlerService</code>. It encapsulates the WSDL URL, the authentication object, the SoapClient object, and provides services of all operations defined in the WSDL. There are 28 operations defined in the WSDL:</p>
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
</ul>";
return $doc_string; ?></description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/tree/master/working-with-AssetOperationHandlerService">working-with-AssetOperationHandlerService</a></li></ul></postscript>
<advanced>
</advanced>
</documentation>
*/
class AssetOperationHandlerServiceSoap extends AssetOperationHandlerService
{
    const DEBUG        = false;
    const DUMP         = false;
    
/**
<documentation><description><p>The constructor.</p></description>
<example>$type     = aohs\AssetOperationHandlerService::SOAP_STRING;
$url      = "http://mydomain.edu:1234/ws/services/AssetOperationService?wsdl";
$username = "wing";
$password = "password";
$auth     = ( object )[ 'username' => $username, 'password' => $password ];
$service  = new aohs\AssetOperationHandlerServiceSoap( $type, $url, $auth );</example>
<return-type>void</return-type></documentation>
*/
    public function __construct(
        string $type, string $url, \stdClass $auth, $context=NULL )
    {
        parent::__construct( $type, $url, $auth );
        
        $this->url            = $url;
        $this->auth           = $auth;
        $this->message        = '';
        $this->success        = '';
        $this->createdAssetId = '';
        $this->lastRequest    = '';
        $this->lastResponse   = '';
        
        foreach( $this->getProperties() as $property )
        {
            // turn a property name like 'publishSet' to 'PublishSet'
            $property = ucwords( $property );
            // populate the two arrays for dynamic generation of methods
            // attach the prefixes 'read' and 'get'
            $this->read_methods[] = 'read' . $property;
            $this->get_methods[]  = 'get'  . $property;
        }
        
        try
        {
            if( is_array( $context ) )
                $this->soapClient = new \SoapClient( $this->url, $context );
            else
                $this->soapClient = new \SoapClient( $this->url, array( 'trace' => true,
                    'keep_alive' => false ) );
        }
        catch( \Exception $e )
        {
            throw new e\ServerException( S_SPAN . $e->getMessage() . E_SPAN );
        }
        catch( \Error $er )
        {
            throw new e\ServerException( S_SPAN . $er->getMessage() . E_SPAN );
        }
    }
   
/**
<documentation><description><p>Dynamically generates the read and get methods.</p></description>
<example></example>
<return-type>mixed</return-type></documentation>
*/
    function __call( string $func, array $params )
    {
        $property = "";
        // derive the property name from method name
        if( strpos( $func, 'read' ) === 0 )
            $property = substr( $func, 4 );
        else if( strpos( $func, 'get' ) === 0 )
            $property = substr( $func, 3 );
        
        $property = ucwords( $property );
        
        // read methods
        if( in_array( $func, $this->read_methods ) )
        {
            $read_param = new \stdClass();
            $read_param->authentication = $this->auth;
            $read_param->identifier     = $params[ 0 ];
    
            $this->reply = $this->soapClient->read( $read_param );
        
            if( ( $this->reply->readReturn->success == 'true' ) && 
                  isset( $this->reply->readReturn->asset->$property ) )
            {
                // store the property
                $this->read_assets[ $property ] =
                    $this->reply->readReturn->asset->$property; 
            }
   
            $this->storeResults( $this->reply->readReturn );
        }
        // get methods
        else if( in_array( $func, $this->get_methods ) )
        {
            // could be NULL
            return $this->read_assets[ $property ];
        }
    }
    
/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Batch-executes the operations.</p><p>batch:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("batch"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("batchResponse"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("operation"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getComplexTypeXMLByName("batchResult"));
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$paths = array( 
             "/_cascade/blocks/code/text-block", 
             "_cascade/blocks/code/ajax-read-profile-php" );

$operations = array();

foreach( $paths as $path )
{
    $id        = $service->createId( a\TextBlock::TYPE, $path, "cascade-admin" );
    $operation = new \stdClass();
    $read_op   = new \stdClass();
    
    $read_op->identifier = $id;
    $operation->read     = $read_op;
    $operations[]        = $operation;
}

try
{
    $service->batch( $operations );
    u\DebugUtility::dump( $service->getReply()->batchReturn );
}</example>
<return-type>stdClass</return-type>
</documentation>
*/
    function batch( array $operations ) : \stdClass
    {
        $batch_param                 = new \stdClass();
        $batch_param->authentication = $this->auth;
        $batch_param->operation      = $operations;
        
        $this->reply = $this->soapClient->batch( $batch_param );
        // the returned object is an array
        $this->storeResults();
        return $this->reply;
    }

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
return $doc_string;
?>
</description>
<example>$path = "/files/AssetOperationHandlerService.class.php.zip";
$id = $service->createId( a\File::TYPE, $path, "cascade-admin" );
$service->checkIn( $id, 'Testing the checkIn method.' );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    function checkIn( \stdClass $identifier, string $comments='' ) : \stdClass
    {
        $checkin_param                 = new \stdClass();
        $checkin_param->authentication = $this->auth;
        $checkin_param->identifier     = $identifier;
        $checkin_param->comments       = $comments;
        
        $this->reply = $this->soapClient->checkIn( $checkin_param );
        $this->storeResults( $this->reply->checkInReturn );
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
return $doc_string;
?>
</description>
<example>$path = "/files/AssetOperationHandlerService.class.php.zip";
$id = $service->createId( a\File::TYPE, $path, "cascade-admin" );
$service->checkOut( $id );
</example>
<return-type>string</return-type></documentation>
*/
    function checkOut( \stdClass $identifier ) : string
    {
        $checkout_param                 = new \stdClass();
        $checkout_param->authentication = $this->auth;
        $checkout_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->checkOut( $checkout_param );
        $this->storeResults( $this->reply->checkOutReturn );
        
        if( $this->reply->checkOutReturn->success == "true" &&
            isset( $this->reply->checkOutReturn->workingCopyIdentifier ) &&
            !is_null( $this->reply->checkOutReturn->workingCopyIdentifier->id )  )
            return $this->reply->checkOutReturn->workingCopyIdentifier->id;
        else
            return "";
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
    public function copy( \stdClass $identifier, \stdClass $newIdentifier, string $newName, bool $doWorkflow ) : \stdClass
    {
        $copy_params                 = new \stdClass();
        $copy_params->authentication = $this->auth;
        $copy_params->identifier     = $identifier;
        $copy_params->copyParameters = new \stdClass();
        $copy_params->copyParameters->destinationContainerIdentifier = $newIdentifier;
        $copy_params->copyParameters->newName                        = $newName;
        $copy_params->copyParameters->doWorkflow                     = $doWorkflow;
        
        $this->reply = $this->soapClient->copy( $copy_params );
        $this->storeResults( $this->reply->copyReturn );
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
return $doc_string;
?>
</description>
<example>// get the image data
$img_url     = "http://www.upstate.edu/scripts/faculty/thumbs/nadkarna.jpg";
$img_binary  = file_get_contents( $img_url );
// the folder where the file should be created
$parent_id   = '980d653f8b7f0856015997e4bb59f630';
$site_name   = 'cascade-admin';
$img_name    = 'nadkarna.jpg';
// create the asset
$asset       = new \stdClass();
$asset->file = $service->createFileWithParentIdSiteNameNameData( 
    $parent_id, $site_name, $img_name, $img_binary );
$service->create( $asset );    
</example>
<return-type>mixed</return-type></documentation>
*/
    public function create( \stdClass $asset )
    {
        $create_params                 = new \stdClass();
        $create_params->authentication = $this->auth;
        $create_params->asset          = $asset;
        
        $this->reply = $this->soapClient->create( $create_params );
        $this->storeResults( $this->reply->createReturn );
        
        return $this->reply->createReturn->createdAssetId;
    }

/**
<documentation><description><p>Creates an id object for an asset.</p></description>
<example>$block_id = $service->createId( a\TextBlock::TYPE, "_cascade/blocks/code/text-block", "cascade-admin" );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createId(
        string $type, string $id_path, string $site_name=NULL ) : \stdClass
    {
        if( !( is_string( $type ) && ( is_string( $id_path ) || is_int( $id_path ) ) ) )
            throw new e\UnacceptableValueException( "Only strings are accepted in createId." );
            
        $non_digital_id_types = array(
            c\T::GROUP, c\T::ROLE, c\T::SITE, c\T::USER
        );
        
        $id_path = trim( $id_path );
        
        if( strlen( $id_path ) > 1 )
        {
            $id_path = trim( $id_path );
            $id_path = trim( $id_path, '/' );
        }
    
        $identifier = new \stdClass();
        
        if( $this->isHexString( $id_path ) )
        {
            // if id string is passed in, ignore site name
            $identifier->id = $id_path;
        }
        else if( in_array( $type, $non_digital_id_types ) )
        {
            if( $type != c\T::SITE ) // not a site
            {
                $identifier->id = $id_path;
            }
            else // a site
            {
                $identifier->path       = new \stdClass();
                $identifier->path->path = $id_path;
            }
        }
        else if( u\StringUtility::startsWith( $id_path, "ROOT_" ) )
        {
            $identifier->id = $id_path;
        }
        // asset in Global
        else if( $site_name == NULL )
        {
            $identifier->path           = new \stdClass();
            $identifier->path->path     = $id_path;
            $identifier->path->siteName = $site_name;
        }
        else
        {
            if( trim( $site_name ) == "" )
            {
                throw new e\EmptyValueException( 
                    S_SPAN . c\M::EMPTY_SITE_NAME . E_SPAN );
            }
            $identifier->path           = new \stdClass();
            $identifier->path->path     = $id_path;
            $identifier->path->siteName = $site_name;
        }
        $identifier->type = $type;
        return $identifier;
    }

/**
<documentation><description><p>Creates an id object for an asset.</p></description>
<example>$block_id = $service->createIdWithIdType( "388fa7a58b7ffe83164c93149320e775", a\TextBlock::TYPE );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createIdWithIdType( string $id, string $type ) : \stdClass
    {
        return $this->createId( $type, $id );
    }

/**
<documentation><description><p>Creates an id object for an asset.</p></description>
<example>$block_id = $service->createIdWithPathSiteNameType( "_cascade/blocks/code/text-block", "cascade-admin", a\TextBlock::TYPE );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createIdWithPathSiteNameType( string $path, string $site_name, string $type ) : \stdClass
    {
        return $this->createId( $type, $path, $site_name );
    }

/**
<documentation><description><p>Creates a file stdClass object.</p></description>
<example>$asset->file = $service->createFileWithParentIdSiteNameNameData( 
    $parent_id, $site_name, $img_name, $img_binary );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createFileWithParentIdSiteNameNameData(
         string $parentFolderId, string $siteName, string $name, $data ) : \stdClass
    {
        $file                 = new \stdClass();
        $file->parentFolderId = $parentFolderId;
        $file->siteName       = $siteName;
        $file->name           = $name;
        $file->data           = $data;
        return $file;
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
return $doc_string;
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
        $delete_params                 = new \stdClass();
        $delete_params->authentication = $this->auth;
        $delete_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->delete( $delete_params );
        $this->storeResults( $this->reply->deleteReturn );
        return $this->reply;
    }

/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Deletes the message with the given identifier.</p><p>deleteMessage:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("deleteMessage"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("deleteMessageResponse"));
$doc_string .= "</pre>";
return $doc_string;
?>
</description>
<example>$mid = "9e10ae5b8b7ffe8364375ac78e212e42";
$service->deleteMessage( $service->createId( c\T::MESSAGE, $mid ) );
</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function deleteMessage( \stdClass $identifier ) : \stdClass
    {
        $delete_message_params                 = new \stdClass();
        $delete_message_params->authentication = $this->auth;
        $delete_message_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->deleteMessage( $delete_message_params );
        $this->storeResults( $this->reply->deleteMessageReturn );
        return $this->reply;
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
return $doc_string;
?>
</description>
<example>$asset = new \stdClass();
$asset->xhtmlDataDefinitionBlock = $block;
$service->edit( $asset );
</example>
<exception>EditingFailureException</exception>
<return-type>stdClass</return-type>
</documentation>
*/
    public function edit( \stdClass $asset ) : \stdClass
    {
        $edit_params                 = new \stdClass();
        $edit_params->authentication = $this->auth;
        $edit_params->asset          = $asset;
        
        try
        {
            $this->reply = $this->soapClient->edit( $edit_params );
        }
        catch ( \SoapFault $sf )
        {
            throw new e\EditingFailureException( $sf->getMessage() );
        }
        
        $this->storeResults( $this->reply->editReturn );
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
        \stdClass $accessRightsInformation, bool $applyToChildren ) : \stdClass
    {
        $edit_params                          = new \stdClass();
        $edit_params->authentication          = $this->auth;
        $edit_params->accessRightsInformation = $accessRightsInformation;
        $edit_params->applyToChildren         = $applyToChildren;

        $this->reply = $this->soapClient->editAccessRights( $edit_params );
        $this->storeResults( $this->reply->editAccessRightsReturn );
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
return $doc_string;
?>
</description>
<example>$service->editPreference( "system_pref_allow_font_assignment", "off" );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function editPreference( string $name, string $value ) : \stdClass
    {
        $edit_preferences_param                    = new \stdClass();
        $edit_preferences_param->authentication    = $this->auth;
        $edit_preferences_param->preference        = new \stdClass();
        $edit_preferences_param->preference->name  = $name;
        $edit_preferences_param->preference->value = $value;
        
        $this->reply = $this->soapClient->editPreference( $edit_preferences_param );
        $this->storeResults( $this->reply->editPreferenceReturn );
        return $this->reply;
    }

/**
<documentation><description><p>An alias of <code>editPreference</code>.</p>
</description>
<return-type>stdClass</return-type>
</documentation>
*/
    public function editPreferences( string $name, string $value ) : \stdClass
    {
        return $this->editPreference( $name, $value );
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
return $doc_string;
?>
</description>
<example>$service->editWorkflowSettings( $workflowSettings, false, false );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function editWorkflowSettings( 
        \stdClass $workflowSettings, bool $applyInheritWorkflowsToChildren, 
        bool $applyRequireWorkflowToChildren ) : \stdClass
    {
        $edit_params                   = new \stdClass();
        $edit_params->authentication   = $this->auth;
        $edit_params->workflowSettings = $workflowSettings;
        $edit_params->applyInheritWorkflowsToChildren = $applyInheritWorkflowsToChildren;
        $edit_params->applyRequireWorkflowToChildren  = $applyRequireWorkflowToChildren;
        
        $this->reply = $this->soapClient->editWorkflowSettings( $edit_params );
        $this->storeResults( $this->reply->editWorkflowSettingsReturn );
        return $this->reply;
    }

/**
<documentation><description><p>Creates an asset object, bridging this class and the Asset classes.</p></description>
<example>$page = $service->getAsset( a\Page::TYPE, $page_id )</example>
<exception>NoSuchTypeException</exception>
<return-type>Asset</return-type></documentation>
*/
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
    
/**
<documentation><description><p>Gets the audits object after the call of readAudits().</p></description>
<example>u\DebugUtility::dump( $service->getAudits() );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getAudits() : \stdClass
    {
        return $this->audits;
    }

/**
<documentation><description><p>Gets the ID of an asset newly created.</p></description>
<example>echo $service->getCreatedAssetId();</example>
<return-type>string</return-type></documentation>
*/
    public function getCreatedAssetId() : string
    {
        return $this->createdAssetId;
    }

/**
<documentation><description><p>Gets the last request XML.</p></description>
<example>echo u\XMLUtility::replaceBrackets( $service->getLastRequest() );</example>
<return-type>string</return-type></documentation>
*/
    public function getLastRequest() : string
    {
        return $this->lastRequest;
    }

/**
<documentation><description><p>Gets the last response.</p></description>
<example>echo S_PRE, u\XMLUtility::replaceBrackets( $service->getLastResponse() ), E_PRE;</example>
<return-type>string</return-type></documentation>
*/
    public function getLastResponse() : string
    {
        return $this->lastResponse;
    }

/**
<documentation><description><p>Gets the editor configurations after the call of listEditorConfigurations().</p></description>
<example>
</example>
<return-type>mixed</return-type></documentation>
*/
    public function getListedEditorConfigurations()
    {
        return $this->listed_editor_configurations;
    }

/**
<documentation><description><p>Gets the messages object after the call of listMessages().</p></description>
<example>$service->listMessages();
u\DebugUtility::dump( $service->getListedMessages() );
</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getListedMessages() : \stdClass
    {
        return $this->listed_messages;
    }
    
/**
<documentation><description><p>Gets the message after an operation.</p></description>
<example>echo $service->getMessage();</example>
<return-type>mixed</return-type></documentation>
*/
    public function getMessage()
    {
        return $this->message;
    }

/**
<documentation><description><p>Gets the preferences after the call of readPreferences().</p></description>
<example>$service->readPreferences();
u\DebugUtility::dump( $service->getPreferences() );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getPreferences() : \stdClass
    {
        return $this->preferences;
    }

/**
<documentation><description><p>Gets the accessRightInformation object after the call of readAccessRightInformation().</p></description>
<example>$accessRightInfo = $service->getReadAccessRightInformation();</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadAccessRightInformation() : \stdClass
    {
        return $this->reply->readAccessRightsReturn->accessRightsInformation;
    }

/**
<documentation><description><p>Gets the asset object after the call of read().</p></description>
<example>$container = $service->getReadAsset()->assetFactoryContainer;</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadAsset() : \stdClass
    {
        return $this->reply->readReturn->asset;
    }

/**
<documentation><description><p>Gets the file object after the call of read().</p></description>
<example>$file = $service->getReadFile();</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadFile() : \stdClass
    {
        return $this->reply->readReturn->asset->file;
    }

/**
<documentation><description><p>Gets the workflow object after the call of readWorkflow().</p></description>
<example>$service->readWorkflowInformation( 
    $service->createId( a\Page::TYPE, $path, "cascade-admin" ) );
$workflow = $service->getReadWorkflow();</example>
<return-type>mixed</return-type></documentation>
*/
    public function getReadWorkflow()
    {
        return $this->reply->readWorkflowInformationReturn->workflow;
    }

/**
<documentation><description><p>Gets the workflowSettings object after the call of readWorkflowSettings().</p></description>
<example>$service->readWorkflowSettings( 
    $service->createId( a\Folder::TYPE, "/", $site_name ) );
$workflowSettings = $service->getReadWorkflowSettings();
</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadWorkflowSettings() : \stdClass
    {
        return $this->reply->readWorkflowSettingsReturn->workflowSettings;
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
<documentation><description><p>Gets the searchMatches object after the call of search().</p></description>
<example>$service->search( $search_for );
if( is_null( $service->getSearchMatches()->match ) )
{
    // do something
}</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getSearchMatches() : \stdClass
    {
        return $this->searchMatches;
    }

/**
<documentation><description><p>Returns a bool after an operation indicating whether the search is successful.</p></description>
<example>if ( $service->getSuccess() )</example>
<return-type>bool</return-type></documentation>
*/
    public function getSuccess() : bool
    {
        return $this->success;
    }

/**
<documentation><description><p>Gets the type of an asset.</p></description>
<example>$id = "3896de848b7ffe83164c931422421045";
echo $service->getType( $id ), BR;
</example>
<return-type>string</return-type></documentation>
*/
    public function getType( string $id_string ) : string
    {
        $types      = $this->getTypes();
        $type_count = count( $types );
        
        for( $i = 0; $i < $type_count; $i++ )
        {
            $id = $this->createId( $types[ $i ], $id_string );
            $operation = new \stdClass();
            $read_op   = new \stdClass();
    
            $read_op->identifier = $id;
            $operation->read     = $read_op;
            $operations[]        = $operation;
        }
        
        $this->batch( $operations );
        
        $reply_array = $this->getReply()->batchReturn;
        
        for( $j = 0; $j < $type_count; $j++ )
        {
            if( $reply_array[ $j ]->readResult->success == 'true' )
            {
                foreach( c\T::$type_property_name_map as $type => $property )
                {
                    if( isset( $reply_array[ $j ]->readResult->asset->$property ) )
                        return $type;
                }
            }
        }
        
        return "The id does not match any asset type.";
    }

/**
<documentation><description><p>Gets the WSDL URL string.</p></description>
<example>echo $service->getUrl(), BR;</example>
<return-type>string</return-type></documentation>
*/
    public function getUrl() : string
    {
        return $this->url;
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
        return $this->success == 'true';
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
return $doc_string;
?>
</description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function listEditorConfigurations( \stdClass $id ) : \stdClass
    {
        $list_editor_configurations_params                 = new \stdClass();
        $list_editor_configurations_params->authentication = $this->auth;
        $list_editor_configurations_params->identifier     = $id;
        
        $this->reply = $this->soapClient->listEditorConfigurations(
            $list_editor_configurations_params );
        $this->storeResults( $this->reply->listEditorConfigurationsReturn );

        if( $this->isSuccessful() )
        {
             u\DebugUtility::dump( $this->reply );
            $this->listed_editor_configurations =
                $this->reply->listEditorConfigurationsReturn->editorConfigurations;
        }
        return $this->reply->listEditorConfigurationsReturn->editorConfigurations;
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
return $doc_string;
?>
</description>
<example>$service->listMessages();</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function listMessages() : \stdClass
    {
        $list_messages_params                 = new \stdClass();
        $list_messages_params->authentication = $this->auth;
        
        $this->reply = $this->soapClient->listMessages( $list_messages_params );
        $this->storeResults( $this->reply->listMessagesReturn );
        
        if( $this->isSuccessful() )
        {
            $this->listed_messages = $this->reply->listMessagesReturn->messages;
        }
        return $this->reply;
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
return $doc_string;
?>
</description>
<example>$service->listSites();</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function listSites() : \stdClass
    {
        $list_sites_params                 = new \stdClass();
        $list_sites_params->authentication = $this->auth;
        
        $this->reply = $this->soapClient->listSites( $list_sites_params );
        $this->storeResults( $this->reply->listSitesReturn );
        return $this->reply;
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
        $list_subscribers_params                 = new \stdClass();
        $list_subscribers_params->authentication = $this->auth;
        $list_subscribers_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->listSubscribers( $list_subscribers_params );
        $this->storeResults( $this->reply->listSubscribersReturn );
        return $this->reply;
    }

/**
<documentation><description>
<?php global $eval, $service;
$doc_string = "<p>Marks a message as 'read' or 'unread'.</p><p>markMessage:</p><pre>";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("markMessage"));
$doc_string .= "\r";
$doc_string .= $eval->replaceBrackets($service->getElementXMLByName("markMessageResponse"));
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
    public function markMessage( \stdClass $identifier, string $markType ) : \stdClass
    {
        $mark_message_params                 = new \stdClass();
        $mark_message_params->authentication = $this->auth;
        $mark_message_params->identifier     = $identifier;
        $mark_message_params->markType       = $markType;
        
        $this->reply = $this->soapClient->markMessage( $mark_message_params );
        $this->storeResults( $this->reply->markMessageReturn );
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
return $doc_string;
?>
</description>
<example>$service->move( $block_id, $parent_id, $new_name, $do_workflow );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    function move( \stdClass $identifier, \stdClass $parentId=NULL, 
        string $newName="", bool $doWorkflow=false ) : \stdClass
    {
        $move_params                 = new \stdClass();
        $move_params->authentication = $this->auth;
        $move_params->identifier     = $identifier;
        $move_params->moveParameters = new \stdClass();
        $move_params->moveParameters->destinationContainerIdentifier = $parentId;
        $move_params->moveParameters->newName                        = $newName;
        $move_params->moveParameters->doWorkflow                     = $doWorkflow;
        
        $this->reply = $this->soapClient->move( $move_params );
        $this->storeResults( $this->reply->moveReturn );
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
return $doc_string;
?>
</description>
<example>$service->performWorkflowTransition( $id, $action, 'Testing' );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function performWorkflowTransition( 
         string $workflowId, string $actionIdentifier,
         string $transitionComment='' ) : \stdClass
    {
        $workflowTransitionInformation                    = new \stdClass();
        $workflowTransitionInformation->workflowId        = $workflowId;
        $workflowTransitionInformation->actionIdentifier  = $actionIdentifier;
        $workflowTransitionInformation->transitionComment = $transitionComment;
        
        $transition_params                                = new \stdClass();
        $transition_params->authentication                = $this->auth;
        $transition_params->workflowTransitionInformation = $workflowTransitionInformation;
        
        $this->reply = $this->soapClient->performWorkflowTransition( $transition_params );
        $this->storeResults( $this->reply->performWorkflowTransitionReturn );
        return $this->reply;
    }

/**
<documentation><description><p>Prints the XML of the last request.</p></description>
<example>$service->printLastRequest();</example>
<return-type>void</return-type></documentation>
*/
    public function printLastRequest()
    {
        print_r( $this->lastRequest );
    }

/**
<documentation><description><p>Prints the XML of the last response.</p></description>
<example>$service->printLastResponse();</example>
<return-type>void</return-type></documentation>
*/
    public function printLastResponse()
    {
        print_r( $this->lastResponse );
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
    public function publish( \stdClass $identifier, $destination=NULL ) : \stdClass
    {
        $publish_param = new \stdClass();
        $publish_info  = new \stdClass();
        $publish_param->authentication = $this->auth;
        $publish_info->identifier      = $identifier;
        
        if( isset( $destination ) )
        {
            if( is_array( $destination ) )
                $publish_info->destinations = $destination;
            else
                $publish_info->destinations = array( $destination );
        }
        
        $publish_info->unpublish           = false;
        $publish_param->publishInformation = $publish_info;
        
        $this->reply = $this->soapClient->publish( $publish_param );
        $this->storeResults( $this->reply->publishReturn );
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
return $doc_string;
?>
</description>
<example>$service->read( 
    $service->createId( a\Folder::TYPE, $path, "cascade-admin" ) );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function read( \stdClass $identifier ) : \stdClass
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $identifier ); }
        
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->read( $read_param );
        $this->storeResults( $this->reply->readReturn );
        return $this->reply;
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
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readAccessRights( $read_param );
        $this->storeResults( $this->reply->readAccessRightsReturn );
        return $this->reply;
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
    public function readAudits( \stdClass $params ) : \stdClass
    {
        $read_audits_param                  = new \stdClass();
        $read_audits_param->authentication  = $this->auth;
        $read_audits_param->auditParameters = $params;
        
        $this->reply = $this->soapClient->readAudits( $read_audits_param );
        $this->storeResults( $this->reply->readAuditsReturn );
        $this->audits  = $this->reply->readAuditsReturn->audits;
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
return $doc_string;
?>
</description>
<example>$service->readPreferences();</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function readPreferences() : \stdClass
    {
        $read_preferences_param                  = new \stdClass();
        $read_preferences_param->authentication  = $this->auth;
        
        $this->reply = $this->soapClient->readPreferences( $read_preferences_param );
        $this->storeResults( $this->reply->readPreferencesReturn );
        $this->preferences  = $this->reply->readPreferencesReturn->preferences;
        return $this->reply;
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
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readWorkflowInformation( $read_param );
        $this->storeResults( $this->reply->readWorkflowInformationReturn );
        return $this->reply;
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
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readWorkflowSettings( $read_param );
        $this->storeResults( $this->reply->readWorkflowSettingsReturn );
        return $this->reply;
    }

/**
<documentation><description><p>Retrieves a property of an asset.</p></description>
<example>$page = $service->retrieve( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>mixed</return-type></documentation>
*/
    function retrieve( \stdClass $id, string $property="" )
    {
        if( $property == "" )
        {
            $property = c\T::$type_property_name_map[ $id->type ];
        }
        
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $id;

        $this->reply = $this->soapClient->read( $read_param );
        $this->storeResults( $this->reply->readReturn );

        if( isset( $this->reply->readReturn->asset ) )
            return $this->reply->readReturn->asset->$property;
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
return $doc_string;
?>
</description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function search( \stdClass $searchInfo ) : \stdClass
    {
        $search_info_param                    = new \stdClass();
        $search_info_param->authentication    = $this->auth;
        $search_info_param->searchInformation = $searchInfo;
        
        $this->reply = $this->soapClient->search( $search_info_param );
        $this->searchMatches = $this->reply->searchReturn->matches;
        $this->storeResults( $this->reply->searchReturn );
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
    public function sendMessage( \stdClass $message ) : \stdClass
    {
        $send_message_param                 = new \stdClass();
        $send_message_param->authentication = $this->auth;
        $send_message_param->message        = $message;
        
        $this->reply = $this->soapClient->sendMessage( $send_message_param );
        $this->storeResults( $this->reply->sendMessageReturn );
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
    function siteCopy(
        string $original_id, string $original_name, 
        string $new_name, int $max_wait_seconds=0 )
    {
        $site_copy_params                   = new \stdClass();
        $site_copy_params->authentication   = $this->auth;
        $site_copy_params->originalSiteId   = $original_id;
        $site_copy_params->originalSiteName = $original_name;
        $site_copy_params->newSiteName      = $new_name;

        // If $max_wait_seconds is less than 1 then this will immediately skip past the while loop
        if( $max_wait_seconds < 1 )
            $max_wait_seconds = ( (int) ini_get( 'max_execution_time' ) - 2 );

        // max_execution_time could be 0 especially for CLI so we need to re-check $max_wait_seconds
        if( $max_wait_seconds < 1 )
            $max_wait_seconds = 600;
        
        // Cascade >= 8.7.1 will do all of the waiting here
        $site_copy = $this->soapClient->siteCopy( $site_copy_params );
        
        $identifier = new \stdClass();
        $identifier->type = a\MetadataSetContainer::TYPE;
        $identifier->path = new \stdClass();
        $identifier->path->path = "/";
        $identifier->path->siteName = $new_name;
        
        $start = microtime( true );
        $site_copied_within_time_limit = false;
        
        // This code is primarily for Cascade < 8.7.1 but it does not hurt anything to always run it
        if( $site_copy->siteCopyReturn->success === 'true' )
        {
            while( ( microtime( true ) - $start ) < $max_wait_seconds )
            {
                $this->read( $identifier );
                
                if( $this->isSuccessful() )
                {
                    $site_copied_within_time_limit = true;
                    break;
                }
                else
                {
                    sleep( 1 );
                }
            }
        }
        else
        {
        	u\DebugUtility::dump( $site_copy->siteCopyReturn );
        
            throw new e\SiteCreationFailureException(
                S_SPAN . $site_copy->siteCopyReturn->message . E_SPAN );
        }
        
        if( !$site_copied_within_time_limit )
        {
            throw new e\SiteCreationFailureException(
                S_SPAN . 'Site copy has exceeded the max wait time of ' . $max_wait_seconds . '. The site copy process may still be processing within Cascade.' . E_SPAN );
        }
    }

/**
<documentation><description><p>Unpublishes the asset with the given identifier.</p></description>
<example>$service->unpublish( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function unpublish( \stdClass $identifier, $destination=NULL ) : \stdClass
    {
        $publish_param = new \stdClass();
        $publish_info  = new \stdClass();
        $publish_param->authentication = $this->auth;
        $publish_info->identifier      = $identifier;
        
        if( isset( $destination ) )
        {
            if( is_array( $destination ) )
                $publish_info->destinations = $destination;
            else
                $publish_info->destinations = array( $destination );
        }
        
        $publish_info->unpublish           = true;
        $publish_param->publishInformation = $publish_info;
        
        $this->reply = $this->soapClient->publish( $publish_param );
        $this->storeResults( $this->reply->publishReturn );
        return $this->reply;
    }
  
    private function storeResults( $return=NULL )
    {
        if( isset( $return ) )
        {
            $this->success  = $return->success;
            $this->message  = $return->message;
        }
        $this->lastRequest  = $this->soapClient->__getLastRequest();
        $this->lastResponse = $this->soapClient->__getLastResponse();
    }

    // from the constructor
    /*@var string The url */
    private $url;
    /*@var stdClass The authentication */
    private $auth;
    /*@var SoapClient The SoapClient */
    private $soapClient;
    
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
    /*@var stdClass The listed editor configurations */
    private $listed_editor_configurations;
    /*@var stdClass The listed messages */
    private $listed_messages;
    
    private $preferences;

    /*@var array The array of readX names */
    private $read_methods = array();
    /*@var array The array of getX names */
    private $get_methods  = array();
    /*@var array The array to store property stdClass objects */
    private $read_assets  = array();
    /*@var DOMXpath The DOMXpath object to store the WSDL */
    //private $dom_xpath;
}
?>