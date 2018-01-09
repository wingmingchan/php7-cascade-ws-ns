<?php
/**
  Author: Wing Ming Chan
  Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  MIT Licensed
  Modification history:
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
class AssetOperationHandlerService
{
    const DEBUG        = false;
    const DUMP         = false;
    const NAME_SPACE   = "cascade_ws_AOHS";
    
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
<documentation><description><p>The constructor.</p></description>
<example>$service = new aohs\AssetOperationHandlerService( $wsdl, $auth );</example>
<return-type>void</return-type></documentation>
*/
    public function __construct( string $url, \stdClass $auth, $context=NULL )
    {
        $this->url            = $url;
        $this->auth           = $auth;
        $this->message        = '';
        $this->success        = '';
        $this->createdAssetId = '';
        $this->lastRequest    = '';
        $this->lastResponse   = '';
        
        foreach( $this->properties as $property )
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
                $this->soapClient = new \SoapClient( $this->url, array( 'trace' => 1 ) );
        }
        catch( \Exception $e )
        {
            throw new e\ServerException( S_SPAN . $e->getMessage() . E_SPAN );
        }
        catch( \Error $er )
        {
            throw new e\ServerException( S_SPAN . $er->getMessage() . E_SPAN );
        }
        
        $wsdl     = file_get_contents( $url );
        $domDoc   = new \DOMDocument();
        $domDoc->loadXML( $wsdl );
        
        $this->dom_xpath = new \DOMXpath( $domDoc );
        $this->dom_xpath->registerNamespace( 'wsdl', 'http://schemas.xmlsoap.org/wsdl/' );
        $this->dom_xpath->registerNamespace(
            'schema', 'http://www.w3.org/2001/XMLSchema' );
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
                $this->read_assets[ $property ] = $this->reply->readReturn->asset->$property; 
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
<return-type>void</return-type></documentation>
*/
    function batch( array $operations )
    {
        $batch_param                 = new \stdClass();
        $batch_param->authentication = $this->auth;
        $batch_param->operation      = $operations;
        
        $this->reply = $this->soapClient->batch( $batch_param );
        // the returned object is an array
        $this->storeResults();
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
<return-type>void</return-type></documentation>
*/
    function checkIn( \stdClass $identifier, string $comments='' )
    {
        $checkin_param                 = new \stdClass();
        $checkin_param->authentication = $this->auth;
        $checkin_param->identifier     = $identifier;
        $checkin_param->comments       = $comments;
        
        $this->reply = $this->soapClient->checkIn( $checkin_param );
        $this->storeResults( $this->reply->checkInReturn );
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
    function checkOut( \stdClass $identifier )
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
<return-type>void</return-type></documentation>
*/
    public function copy( \stdClass $identifier, \stdClass $newIdentifier, string $newName, bool $doWorkflow ) 
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
<return-type>void</return-type></documentation>
*/
    public function delete( \stdClass $identifier )
    {
        $delete_params                 = new \stdClass();
        $delete_params->authentication = $this->auth;
        $delete_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->delete( $delete_params );
        $this->storeResults( $this->reply->deleteReturn );
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
<return-type>void</return-type></documentation>
*/
    public function deleteMessage( \stdClass $identifier )
    {
        $delete_message_params                 = new \stdClass();
        $delete_message_params->authentication = $this->auth;
        $delete_message_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->deleteMessage( $delete_message_params );
        $this->storeResults( $this->reply->deleteMessageReturn );
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
<return-type>void</return-type></documentation>
*/
    public function edit( \stdClass $asset )
    {
        $edit_params                 = new \stdClass();
        $edit_params->authentication = $this->auth;
        $edit_params->asset          = $asset;
        
        $this->reply = $this->soapClient->edit( $edit_params );
        $this->storeResults( $this->reply->editReturn );
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
<return-type>void</return-type></documentation>
*/
    public function editAccessRights( \stdClass $accessRightsInformation, bool $applyToChildren )
    {
        $edit_params                          = new \stdClass();
        $edit_params->authentication          = $this->auth;
        $edit_params->accessRightsInformation = $accessRightsInformation;
        $edit_params->applyToChildren         = $applyToChildren;

        $this->reply = $this->soapClient->editAccessRights( $edit_params );
        $this->storeResults( $this->reply->editAccessRightsReturn );
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
<return-type>void</return-type></documentation>
*/
    public function editPreference( string $name, string $value ) 
    {
        $edit_preferences_param                    = new \stdClass();
        $edit_preferences_param->authentication    = $this->auth;
        $edit_preferences_param->preference        = new \stdClass();
        $edit_preferences_param->preference->name  = $name;
        $edit_preferences_param->preference->value = $value;
        
        $this->reply = $this->soapClient->editPreference( $edit_preferences_param );
        $this->storeResults( $this->reply->editPreferenceReturn );
    }

/**
<documentation><description><p>An alias of <code>editPreference</code>.</p>
</description>
<return-type>void</return-type></documentation>
*/
    public function editPreferences( string $name, string $value ) 
    {
        $this->editPreference( $name, $value );
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
<return-type>void</return-type></documentation>
*/
    public function editWorkflowSettings( 
        \stdClass $workflowSettings, bool $applyInheritWorkflowsToChildren, bool $applyRequireWorkflowToChildren )
    {
        $edit_params                   = new \stdClass();
        $edit_params->authentication   = $this->auth;
        $edit_params->workflowSettings = $workflowSettings;
        $edit_params->applyInheritWorkflowsToChildren = $applyInheritWorkflowsToChildren;
        $edit_params->applyRequireWorkflowToChildren  = $applyRequireWorkflowToChildren;
        
        $this->reply = $this->soapClient->editWorkflowSettings( $edit_params );
        $this->storeResults( $this->reply->editWorkflowSettingsReturn );
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
<documentation><description><p>Gets the ID of an asset newly created.</p></description>
<example>echo $service->getCreatedAssetId();</example>
<return-type>string</return-type></documentation>
*/
    public function getCreatedAssetId() : string
    {
        return $this->createdAssetId;
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
    public function getPortType()
    {
        return $this->getXMLByPath( self::PORT_TYPE_PATH );
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
<documentation><description><p></p></description>
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
<documentation><description><p>Gets the workflowSettings object after the call of readWorkflowSettings().</p></description>
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
        $type_count = count( $this->types );
        
        for( $i = 0; $i < $type_count; $i++ )
        {
            $id = $this->createId( $this->types[ $i ], $id_string );
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
<documentation><description><p>Returns <code>false</code>.</p></description>
<example>echo u\StringUtility::boolToString( $service->isRest() );</example>
<return-type>bool</return-type></documentation>
*/
    public function isRest() : bool
    {
        return false;
    }

/**
<documentation><description><p>Returns <code>true</code>.</p></description>
<example>echo u\StringUtility::boolToString( $service->isSoap() );</example>
<return-type>bool</return-type></documentation>
*/
    public function isSoap() : bool
    {
        return true;
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
$doc_string = "<p>Lists editor configurations. The <code>$id</code> should
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
<return-type>void</return-type></documentation>
*/
    public function listEditorConfigurations( \stdClass $id )
    {
        $list_editor_configurations_params                 = new \stdClass();
        $list_editor_configurations_params->authentication = $this->auth;
        $list_editor_configurations_params->identifier     = $id;
        
        $this->reply = $this->soapClient->listEditorConfigurations(
            $list_editor_configurations_params );
        $this->storeResults( $this->reply->listEditorConfigurationsReturn );

        if( $this->isSuccessful() )
        {
            $this->listed_editor_configurations =
                $this->reply->listEditorConfigurationsReturn->editorConfigurations;
        }
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
<return-type>void</return-type></documentation>
*/
    public function listMessages()
    {
        $list_messages_params                 = new \stdClass();
        $list_messages_params->authentication = $this->auth;
        
        $this->reply = $this->soapClient->listMessages( $list_messages_params );
        $this->storeResults( $this->reply->listMessagesReturn );
        
        if( $this->isSuccessful() )
        {
            $this->listed_messages = $this->reply->listMessagesReturn->messages;
        }
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
<return-type>void</return-type></documentation>
*/
    public function listSites()
    {
        $list_sites_params                 = new \stdClass();
        $list_sites_params->authentication = $this->auth;
        
        $this->reply = $this->soapClient->listSites( $list_sites_params );
        $this->storeResults( $this->reply->listSitesReturn );
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
<return-type>void</return-type></documentation>
*/
    public function listSubscribers( \stdClass $identifier )
    {
        $list_subscribers_params                 = new \stdClass();
        $list_subscribers_params->authentication = $this->auth;
        $list_subscribers_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->listSubscribers( $list_subscribers_params );
        $this->storeResults( $this->reply->listSubscribersReturn );
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
<return-type>void</return-type></documentation>
*/
    public function markMessage( \stdClass $identifier, string $markType )
    {
        $mark_message_params                 = new \stdClass();
        $mark_message_params->authentication = $this->auth;
        $mark_message_params->identifier     = $identifier;
        $mark_message_params->markType       = $markType;
        
        $this->reply = $this->soapClient->markMessage( $mark_message_params );
        $this->storeResults( $this->reply->markMessageReturn );
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
<return-type>void</return-type></documentation>
*/
    function move( \stdClass $identifier, \stdClass $newIdentifier=NULL, string $newName="", bool $doWorkflow=false ) 
    {
        $move_params                 = new \stdClass();
        $move_params->authentication = $this->auth;
        $move_params->identifier     = $identifier;
        $move_params->moveParameters = new \stdClass();
        $move_params->moveParameters->destinationContainerIdentifier = $newIdentifier;
        $move_params->moveParameters->newName                        = $newName;
        $move_params->moveParameters->doWorkflow                     = $doWorkflow;
        
        $this->reply = $this->soapClient->move( $move_params );
        $this->storeResults( $this->reply->moveReturn );
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
<return-type>void</return-type></documentation>
*/
    public function performWorkflowTransition( 
         string $workflowId, string $actionIdentifier, string $transitionComment='' )
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
<return-type>void</return-type>
</documentation>
*/
    public function publish( \stdClass $identifier, $destination=NULL ) 
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
<return-type>void</return-type>
</documentation>
*/
    public function read( \stdClass $identifier ) 
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $identifier ); }
        
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->read( $read_param );
        $this->storeResults( $this->reply->readReturn );
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
<return-type>void</return-type></documentation>
*/
    public function readAccessRights( \stdClass $identifier ) 
    {
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readAccessRights( $read_param );
        $this->storeResults( $this->reply->readAccessRightsReturn );
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
<return-type>void</return-type></documentation>
*/
    public function readAudits( \stdClass $params ) 
    {
        $read_audits_param                  = new \stdClass();
        $read_audits_param->authentication  = $this->auth;
        $read_audits_param->auditParameters = $params;
        
        $this->reply = $this->soapClient->readAudits( $read_audits_param );
        $this->storeResults( $this->reply->readAuditsReturn );
        $this->audits  = $this->reply->readAuditsReturn->audits;
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
<return-type>void</return-type></documentation>
*/
    public function readPreferences() 
    {
        $read_preferences_param                  = new \stdClass();
        $read_preferences_param->authentication  = $this->auth;
        
        $this->reply = $this->soapClient->readPreferences( $read_preferences_param );
        $this->storeResults( $this->reply->readPreferencesReturn );
        $this->preferences  = $this->reply->readPreferencesReturn->preferences;
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
<return-type>void</return-type></documentation>
*/
    public function readWorkflowInformation( \stdClass $identifier ) 
    {
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readWorkflowInformation( $read_param );
        $this->storeResults( $this->reply->readWorkflowInformationReturn );
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
<return-type>void</return-type></documentation>
*/
    public function readWorkflowSettings( \stdClass $identifier ) 
    {
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readWorkflowSettings( $read_param );
        $this->storeResults( $this->reply->readWorkflowSettingsReturn );
    }

/**
<documentation><description><p>Retrieves a property of an asset.</p></description>
<example>$page = $service->retrieve( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>stdClass</return-type></documentation>
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
<return-type>void</return-type></documentation>
*/
    public function search( \stdClass $searchInfo ) 
    {
        $search_info_param                    = new \stdClass();
        $search_info_param->authentication    = $this->auth;
        $search_info_param->searchInformation = $searchInfo;
        
        $this->reply = $this->soapClient->search( $search_info_param );
        $this->searchMatches = $this->reply->searchReturn->matches;
        $this->storeResults( $this->reply->searchReturn );
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
<return-type>void</return-type></documentation>
*/
    public function sendMessage( \stdClass $message ) 
    {
        $send_message_param                 = new \stdClass();
        $send_message_param->authentication = $this->auth;
        $send_message_param->message        = $message;
        
        $this->reply = $this->soapClient->sendMessage( $send_message_param );
        $this->storeResults( $this->reply->sendMessageReturn );
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
<return-type>void</return-type></documentation>
*/
    function siteCopy( string $original_id, string $original_name, string $new_name ) 
    {
        $site_copy_params                   = new \stdClass();
        $site_copy_params->authentication   = $this->auth;
        $site_copy_params->originalSiteId   = $original_id;
        $site_copy_params->originalSiteName = $original_name;
        $site_copy_params->newSiteName      = $new_name;

        try
        {
            $this->soapClient->siteCopy( $site_copy_params );
        }
        catch( \Exception $e )
        {
            // do nothing
        }
        
        $identifier = new \stdClass();
        $identifier->type = a\MetadataSetContainer::TYPE;
        $identifier->path = new \stdClass();
        $identifier->path->path = "/";
        $identifier->path->siteName = $new_name;
        $counter = 0;
        
        while( $counter < 600 )
        {
            $this->read( $identifier );
            sleep( 1 );
            $counter++;
            
            if( $this->isSuccessful() )
                break;
        }
        
        if( !$this->isSuccessful() )
            throw new e\SiteCreationFailureException(
                S_SPAN . c\M::SITE_CREATION_FAILURE . E_SPAN );
    }

/**
<documentation><description><p>Unpublishes the asset with the given identifier.</p></description>
<example>$service->unpublish( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>void</return-type></documentation>
*/
    public function unpublish( \stdClass $identifier, $destination=NULL ) 
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
    
    /*@var array The array of readX names */
    private $read_methods = array();
    /*@var array The array of getX names */
    private $get_methods  = array();
    /*@var array The array to store property stdClass objects */
    private $read_assets  = array();
    /*@var DOMXpath The DOMXpath object to store the WSDL */
    private $dom_xpath;
}
?>