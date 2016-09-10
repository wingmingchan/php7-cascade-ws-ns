<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  
  * 9/8/2016 Changed signature of denyAccess, grantAccess, moving $asset to the front.
  * 9/2/2016 Changed checkOut so that a reference of a string can be passed in to store the id of the working copy.
  * Changed the type of the third parameter of clearPermissions to string.
  * 3/18/2016 Fixed bugs related to MessageArrays.
  * 3/16/2016 Fixed a bug in createFolderIndexBlock.
  * 1/5/2016 Added copyAsset.
  * 7/6/2015 Added getPreference and setPreference.
  * 5/28/2015 Added namespaces.
  * 5/5/2015 Added $seconds to copySite, and changed the returned object to $this to avoid the exception.
  * 5/4/2015 Added moveAsset and renameAsset.
  * 10/3/2014 Added getGroupsByName and getUsersByName.
  * 8/26/2014 Fixed a bug in getUsers.
  * 8/7/2014 Fixed a bug in createFolder.
  * 8/1/2014 Fixed a bug in getAudits.
  * 7/23/2014 Added getAssetByIdString.
  * 7/16/2014 Started using u\DebugUtility::out and u\DebugUtility::dump.
  * 7/11/2014 Added deleteX to __call. Added getBaseFolderAssetTree.
  * 7/10/2014 Added createFormat, createIndexBlock, createPage, and createXhtmlDataDefinitionBlock.
  * 7/10/2014 Fixed a bug in createPage. Added __call.
  * 7/9/2014 Finished all createX methods.
  * 7/7/2014 Modified createAsset to take care of roles.
  * 7/3/2014 Added createPageConfigurationSet.
  * 7/2/2014 Continued to add createX methods, 24 so far.
  * 6/23/2014 Started adding createX methods.
  * 6/10/2014 Added deleteAsset.
  * 6/9/2014 Fix a bug in getSite.
  * 6/2/2014 Added deleteExpirationMessages.
  * 5/22/2014 Fixed some bugs.
  * 5/21/2014 Added message related methods.
  * 5/14/2014 Added search methods.
  * 5/14/2014 Added checkIn and checkOut.
  * 5/12/2014 Added getAudits.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>The <code>Cascade</code> class is designed to create, retrieve and delete assets. It also provides methods to deal with a few specific types of entities:
sites, access rights, roles, groups, users, audits, and messages, as well as a few <code>search</code> methods.</p>
<h2>Instantiating the <code>Cascade</code> Object</h2>
<p>A <code>Cascade</code> object, <code>$cascade</code>, has already been instantiated in an authentication file and can be used directly.</p>
<h2>Manipulating Sites</h2>
<p>A <code>Cascade</code> object stores all site information. Since a Cascade instance might have hundreds of sites,
I do not want to create <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/site.php"><code>Site</code></a> objects
directly inside a <code>Cascade</code> object. Instead, when the <code>Cascade</code> object is created, it only stores site information in
<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/identifier.php"><code>p\Identifier</code></a> objects,
which are much smaller than <code>Site</code> objects. When <code>Site</code> objects are needed, they can be obtained by calling
<code>p\Identifier::getAsset()</code> or <code>Cascade::getAsset</code>. Therefore, the <code>Cascade::getSites</code> method
only returns an array of <code>p\Identifier</code> objects. On the other hand, when a single site is needed,
<code>Cascade::getSite( $site_name )</code> does return a <code>Site</code> object.</p>
<p>When we need to visit every site and do some simple thing to each of them, we may want to continue what we want to do
even if the operation fails in a site or two. Since <code>Asset::getAsset</code> throws an exception when the asset in question does not exist,
we may want to just ignore the exception and move on. One simple technique can be used to achieve this.
We can use a <code>try&#8230;catch</code> block inside <code>foreach</code>:</p>
<pre class="code">require_once('cascade_ws_ns/auth_chanw.php');

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    $sites = $cascade-&gt;getSites();
    
    foreach( $sites as $site )
    {
        try
        {
            // try to modify the same factory of every site
            $af = $cascade-&gt;getAsset( 
                a\AssetFactory::TYPE, 
                "Upstate/Upload PDF-Max 10M", 
                $site-&gt;getPathPath() );

            $af-&gt;setPluginParameterValue(
                "com.cms.assetfactory.FileLimitPlugin",
                "assetfactory.plugin.filelimit.param.name.size",
                "10000"
            )-&gt;edit();
        }
        catch( \Exception $e ) // if the factory does not exist
        {
            echo $site-&gt;getPathPath() . 
                " failed to modify Upload PDF-Max 10M" . BR;
            continue;
        }
    }
}
catch( \Exception $e )
{
    echo S_PRE . $e . E_PRE;
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE; 
}
</pre>
<h2>Manipulating Access Rights</h2>
<p>There are two ways to deal with access rights through the <code>$cascade</code> object.
We can retrieve the <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/access-rights-information.php"><code>p\AccessRightsInformation</code></a>
object and manipulate that object directly. Or we can use the <code>$cascade</code> object without going through the <code>p\AccessRightsInformation</code> object.</p>
<p>Note that since the <code>$cascade</code> object can be used to manipulate the access rights of a large amount of assets,
it cannot store all this information. This means that every time before <code>Cascade::setAccessRights</code> is called on a different asset,
we need to read the access rights of that asset first.</p>
<h2>Working With Roles, Groups, and Users</h2>
<p>This class is designed to overcome the following problems inherent in Cascade CMS:</p>
<ul>
<li>When reading, Cascade returns at most 250 groups/users.</li>
<li>Roles cannot be retrieved by their names.</li>
</ul>
<p>This class provides three methods to retrieve all roles, groups, and users: <code>Cascade::getRoles</code>, <code>Cascade::getGroups</code>,
and <code>Cascade::getUsers</code>. For <code>Cascade::getUsers</code>, the method returns the result of searching the users,
using a wild-card character, plus any users that belong to one group or another (provided that the number of groups does not exceed 250, see below).
That is to say, it returns the union of two sets: the maximum number of users read from Cascade, and any other users that belong to one group or another.
This may not cover all users, but at least it covers users that belong to some group. As for groups, because they are not subscribers of any other asset,
there is no way to read all of them if the maximum number exceeds 250.</p>
<p>This class also provides a few methods working with role names (not the numeric ID's).
For example, we can retrieve a <code>Role</code> object by using <code>Cascade::getRoleAssetByName( string $role_name )</code>.</p>
<h2>Working With Audits</h2>
<p>This class provides a <code>getAudits</code> methods that returns an array of
<a href="http://www.upstate.edu/cascade-admin/web-services/api/audit.php"><code>Audit</code></a> object.</p>
<h2>Working With Messages</h2>
<p>This class provides a number of methods to retrieve and delete messages.
See <a href="http://www.upstate.edu/cascade-admin/web-services/api/message.php">Message</a> for methods working with individual messages.</p>
<h2>Creating Assets</h2>
<p>In the <code>Cascade</code> class, there are more than forty <code>createX</code> methods, where <code>X</code> is a type of asset.
Some examples are <code>Cascade::createAssetFactory</code>, <code>Cascade::createTextBlock</code> and <code>Cascade::createXsltFormat</code>.
A <code>createX</code> method will first test if the named asset already exists. If it does, it will return an object representing the asset.
If the asset does not exist, then it will create the asset, and return an object representing the new asset.
Note that if data is passed into a <code>createX</code> method, and if the asset already exists before the method call,
then the existing asset will keep its original data and the new data passed in will be ignored.
The new data will be used to populate the asset only when the asset is newly created.</p>
<h2>Getting <code>Asset</code> Objects</h2>
<p>There are two ways to retrieve an <code>Asset</code> object representing an asset using the <code>$cascade</code> object.
The first way is to call <code>Cascade::getAsset</code>.
This method requires parameters, including type information, to identify the asset to be retrieved, and throws an exception
if the asset does not exist. The second way is to call <code>Cascade::getX</code>, where <code>X</code> is a class name,
a name of a concrete sub-class of the <code>Asset</code> class, like <code>AssetFactory</code> or <code>IndexBlock</code>.
A method like <code>Cascade::getIndexBlock</code> requires the identifier of the asset to be retrieved,
but it does not require type information. If the asset exists, the method returns the object representing the asset;
else it returns <code>NULL</code>. Therefore, these methods can be used to test the existence of assets without involving exceptions. Example:</p>
<pre class="code">$a = $cascade-&gt;
    getAssetFactory( 'Upstate/New Default 3-Column Page', 'cascade-admin' );
if( isset( $a ) ) echo $a-&gt;getId() . BR;
</pre>
<p>The only exception in this group of methods is <code>Cascade::getSite</code>, which is independently defined and throws an exception if the site does not exist.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/cascade.php">cascade.php</a></li></ul></postscript>
</documentation>
*/
class Cascade
{
    const DEBUG      = false;
    const DUMP       = false;
    const NAME_SPACE = "cascade_ws_asset";

/**
<documentation><description><p>The constructor. An instance of this class has been instantiated in an authenticatioin file.</p></description>
<example>$cascade = new a\Cascade( $service );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( aohs\AssetOperationHandlerService $service )
    {
        try
        {
            $this->service = $service;
            //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $service ); }
        }
        catch( \Exception $e )
        {
            echo S_PRE . $e . E_PRE;
        }
    }
    
/**
<documentation><description><p>This single method generates all <code>getX</code> methods (like <code>getIndexBlock</code> and <code>getPage</code>)
and <code>deleteX</code> methods. A <code>getX</code> method returns either an object representing the asset, or <code>NULL</code>
if the asset does not exist. A <code>deleteX</code> will delete an existing asset, and returns the <code>Cascade</code> object,
or simply returns the <code>Cascade</code> object, if the asset does not exist, without throwing any exceptions.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    function __call( string $func, $params )
    {
        $delete = false;
        
        // derive the class name from method name
        if( strpos( $func, 'get' ) === 0 )
        {
            $class = substr( $func, 3 );
        }
        else if( strpos( $func, 'delete' ) === 0 )
        {
            $class  = substr( $func, 6 );
            $delete = true;
        }
        else
        {
            throw new e\NoSuchMethodException( 
                S_SPAN . "The method Cascade::$func does not exist." . E_SPAN );
        }
        
        if( isset( $class ) )
        {
            $class = Asset::NAME_SPACE . "\\" . $class;
            $type  = $class . "::TYPE";
            
            if( !defined( $type ) )
                throw new e\NoSuchTypeException( 
                    S_SPAN . "Class $class has no constant TYPE defined." . E_SPAN );

            try
            {
                // get the id/path and site name
                $param0 = NULL;
                $param1 = NULL;

                if( is_array( $params ) && count( $params ) > 0 )
                {
                    $param0 = $params[ 0 ]; // id or path
                
                    if( isset( $params[ 1 ] ) )
                    {
                        $param1 = $params[ 1 ]; // site name
                    }
                }
                // delete
                if( $delete )
                {
                    try
                    {
                        $this->service->delete( 
                            $this->service->createId( $class::TYPE, $param0, $param1 ) );
                        return $this;
                    }
                    catch( \Exception $e )
                    {
                        u\DebugUtility::out( $e->getMessage() . ' Deletion failed.' );
                    }
                }
                // get
                else
                {
                    return $this->getAsset( $class::TYPE, $param0, $param1 );
                }
            }
            // gobble the exception
            catch( e\NullAssetException $e )
            {
                if( $delete )
                    return $this;
                else
                    return NULL;
            }
            catch( \Exception $e )
            {
                if( $delete )
                    return $this;
                else
                    return NULL;
            }
        }
        else
        {
            if( $delete )
                return $this;
            else
                return NULL;
        }
    }
    
/**
<documentation><description><p>Checks in the asset and returns <code>$cascade</code>.</p></description>
<example>$cascade->checkIn( $page );</example>
<return-type>Cascade</return-type>
<exception>NullAssetException, Exception</exception>
</documentation>
*/
    public function checkIn( Asset $a, string $comments='' ) : Cascade
    {
        if( $a == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET );
        }
        
        if( !is_string( $comments ) )
        {
            throw new \Exception( 
                S_SPAN . c\M::COMMENT_NOT_STRING . E_SPAN );
        }
        
        $this->service->checkIn( $a->getIdentifier(), $comments );
        return $this;
    }
    
/**
<documentation><description><p>Checks out the asset and returns <code>$cascade</code>.
To access the id of the working copy, pass in a string variable as the second argument.</p></description>
<example>$id = "";
$cascade->checkOut( $page, $id );

// work with the working copy
$working_page = $cascade->getAsset( a\Page::TYPE, $id );
$working_page->getMetadata()->setDisplayName( "Upgrade Cascade CMS" )->
    getHostAsset()->edit();
// merge the changes
$cascade->checkIn( $page );</example>
<return-type>Cascade</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function checkOut( Asset $a, string &$id="" ) : Cascade
    {
        if( $a == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET . E_SPAN );
        }
        
        $id = $this->service->checkOut( $a->getIdentifier() );
        return $this;
    }
    
/**
<documentation><description><p>Clears all access rights from groups and users, and sets all level to <code>none</code>,
and returns <code>$cascade</code>. Note that when an ID string is supplied and the <code>$site_name</code> is not needed,
an empty string must be passed in as the third argument if there is a fourth argument.</p></description>
<example>$cascade->clearPermissions( a\Page::TYPE, "51e41e738b7f08ee0eb80213bbea02b9" );</example>
<return-type>Cascade</return-type>
<exception></exception>
</documentation>
*/
    public function clearPermissions( string $type, string $id_path, string $site_name="", bool $applied_to_children=false ) : Cascade
    {
        $ari = $this->getAccessRights( $type, $id_path, $site_name );
        $ari->clearPermissions();
        $this->setAccessRights( $ari, $applied_to_children );
        return $this;
    }
    
/**
<documentation><description><p>Creates a copy of the asset in the container with the new name, and returns <code>$cascade</code>.</p></description>
<example>$cascade->copyAsset(
    $page,
    $cascade->getAsset( a\Folder::TYPE, "fff3a7538b7f08ee3e513744ae475537" ), // target folder
    "new-page" // new name
);</example>
<return-type>Cascade</return-type>
<exception></exception>
</documentation>
*/
    public function copyAsset( Asset $asset, Container $container, string $new_name ) : Cascade
    {
        $asset->copy( $container, $new_name );
        return $this;
    }
    
/**
<documentation><description><p>Copies the site, create a new site,
and returns <code>$cascade</code>. Set <code>$seconds</code> to a large enough positive integer so that the copying process can finish.</p></description>
<example>$cascade->copySite( $seed, 'test', 50 );</example>
<return-type>Cascade</return-type>
<exception>UnacceptableValueException, SiteCreationFailureException</exception>
</documentation>
*/
    public function copySite( Site $s, string $new_name, int $seconds=10 ) : Cascade
    {
        if( !is_numeric( $seconds ) || !$seconds > 0 )
            throw new e\UnacceptableValueException( 
                S_SPAN . c\M::UNACCEPTABLE_SECONDS. E_SPAN );
            
        $this->service->siteCopy( $s->getId(), $s->getName(), $new_name );
        // wait until it is done
        sleep( $seconds );
        
        if( $this->service->isSuccessful() )
        {
            return $this;
        }
        
        throw new e\SiteCreationFailureException( 
            S_SPAN . c\M::SITE_CREATION_FAILURE . E_SPAN . $this->service->getMessage() );
    }
    
/* the create group */
/**
<documentation><description><p>Returns an <code>AssetFactory</code> object,
representing either an existing asset factory, or an asset factory newly created by the method.
<code>$type</code> is the asset type associated with the asset factory, and <code>$mode</code> is the workflow mode.</p></description>
<example>$cascade->createAssetFactory(
    $cascade->getAsset( a\AssetFactoryContainer::TYPE, "3789d91a8b7f08ee2347507a434b94d3" ),
    "Upload 1000x500 Image",
    a\File::TYPE
)->dump();</example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createAssetFactory( 
        AssetFactoryContainer $parent, string $name, string $type, string $mode=c\T::NONE ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_ASSET_FACTORY_NAME . E_SPAN );
            
        $asset                                    = AssetTemplate::getAssetFactory();
        $asset->assetFactory->name                = $name;
        $asset->assetFactory->parentContainerPath = $parent->getPath();
        $asset->assetFactory->siteName            = $parent->getSiteName();
        $asset->assetFactory->assetType           = $type;
        $asset->assetFactory->workflowMode        = $mode;
        
        return $this->createAsset(
            $asset, AssetFactory::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns an <code>AssetFactoryContainer</code> object,
representing either an existing asset factory container, or an asset factory container newly created by the method.</p></description>
<example>$cascade->createAssetFactoryContainer(
    $cascade->getAsset( a\AssetFactoryContainer::TYPE, "980a7cff8b7f0856015997e40fe58068" ),
    "Upload"
)->dump();</example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createAssetFactoryContainer( AssetFactoryContainer $parent, string $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_ASSET_FACTORY_CONTAINER_NAME . E_SPAN );
        
        $property =c\T::$type_property_name_map[ AssetFactoryContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, AssetFactoryContainer::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>ConnectorContainer</code> object,
representing either an existing connector container, or a connector container newly created by the method.</p></description>
<example>$cascade->createConnectorContainer(
    $cascade->getAsset( a\ConnectorContainer::TYPE, "980a826b8b7f0856015997e424411695" ),
    "Test"
)->dump();</example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createConnectorContainer( ConnectorContainer $parent, string $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONNECTOR_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ ConnectorContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, ConnectorContainer::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>ContentType</code> object,
representing either an existing content type, or a content type newly created by the method.</p></description>
<example>$cascade->createContentType(
    $cascade->getAsset( a\ContentTypeContainer::TYPE, "980a7c9f8b7f0856015997e4dbf4ab28" ),
    "Test",
    $cascade->getAsset( a\PageConfigurationSet::TYPE, "5f1e42b08b7f08ee226116ffc4f6aac7" ),
    $cascade->getAsset( a\MetadataSet::TYPE, "980d70498b7f0856015997e430d5c886" ),
    $cascade->getAsset( a\DataDefinition::TYPE, "9e18141d8b7f08560053896c87327dcd" )
)->dump();</example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createContentType( 
        ContentTypeContainer $parent, 
        string $name, 
        PageConfigurationSet $pcs,
        MetadataSet $ms,
        DataDefinition $dd=NULL ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_ASSET_FACTORY_NAME . E_SPAN );
            
        $asset                                        = AssetTemplate::getContentType();
        $asset->contentType->name                     = $name;
        $asset->contentType->parentContainerPath      = $parent->getPath();
        $asset->contentType->siteName                 = $parent->getSiteName();
        $asset->contentType->pageConfigurationSetPath = $pcs->getPath();
        $asset->contentType->metadataSetPath          = $ms->getPath();
        
        if( isset( $dd ) )
            $asset->contentType->dataDefinitionPath   = $dd->getPath();
        
        return $this->createAsset(
            $asset, ContentType::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>ContentTypeContainer</code> object,
representing either an existing content type container, or a content type container newly created by the method.</p></description>
<example>$cascade->createContentTypeContainer(
    $cascade->getAsset( a\ContentTypeContainer::TYPE, "980a7c9f8b7f0856015997e4dbf4ab28" ),
    "Test Container"
)->dump();</example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createContentTypeContainer( ContentTypeContainer $parent, string $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONTENT_TYPE_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ ContentTypeContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, ContentTypeContainer::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns an <code>IndexBlock</code> object,
representing either an existing index block of type "content-type", or an index block newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createContentTypeIndexBlock( 
        Folder $parent, 
        string $name, 
        ContentType $ct=NULL,
        $max_rendered_assets=0 ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );
            
        $asset                                    = AssetTemplate::getIndexBlock(c\T::CONTENTTYPEINDEX );
        $asset->indexBlock->name                  = $name;
        $asset->indexBlock->parentFolderPath      = $parent->getPath();
        $asset->indexBlock->siteName              = $parent->getSiteName();
        if( isset( $ct ) )
            $asset->indexBlock->indexedContentTypeId  = $ct->getId();
        $asset->indexBlock->maxRenderedAssets     = $max_rendered_assets;
        $asset->indexBlock->renderingBehavior     = "render-normally";
        $asset->indexBlock->indexPages            = false;
        $asset->indexBlock->indexBlocks           = false;
        $asset->indexBlock->indexLinks            = false;
        $asset->indexBlock->indexFiles            = false;
        $asset->indexBlock->indexRegularContent   = false;
        $asset->indexBlock->indexSystemMetadata   = false;
        $asset->indexBlock->indexUserMetadata     = false;
        $asset->indexBlock->indexAccessRights     = false;
        $asset->indexBlock->indexUserInfo         = false;
        $asset->indexBlock->indexWorkflowInfo     = false;
        $asset->indexBlock->appendCallingPageData = false;
        $asset->indexBlock->sortMethod            =c\T::ALPHABETICAL;
        $asset->indexBlock->sortOrder             =c\T::DESCENDING;
        $asset->indexBlock->pageXML               =c\T::NORENDER;
        
        return $this->createAsset(
            $asset, IndexBlock::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>DatabaseTransport</code> object,
representing either an existing database transport, or a database transport newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createDatabaseTransport( 
        TransportContainer $parent, 
        string $name, 
        string $server, 
        stirng $port, 
        string $username,
        string $database, 
        string $transport ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_TRANSPORT_NAME . E_SPAN );
        if( trim( $server ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_SERVER_NAME . E_SPAN );
        if( trim( $port ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_SERVER_PORT . E_SPAN );
        if( trim( $username ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_USER_NAME . E_SPAN );
        if( trim( $database ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_DATABASE_NAME . E_SPAN );
        if( trim( $transport ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_TRANSPORT_SITE_ID . E_SPAN );
            
        $asset                                         = AssetTemplate::getDatabaseTransport();
        $asset->databaseTransport->name                = $name;
        $asset->databaseTransport->siteName            = $parent->getSiteName();
        $asset->databaseTransport->parentContainerPath = $parent->getPath();
        $asset->databaseTransport->username            = trim( $username );
        $asset->databaseTransport->serverName          = trim( $server );
        $asset->databaseTransport->serverPort          = trim( $port );
        $asset->databaseTransport->databaseName        = trim( $database );
        $asset->databaseTransport->transportSiteId     = trim( $transport );
        
        return $this->createAsset(
            $asset, DatabaseTransport::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>DataDefinition</code> object,
representing either an existing data definition, or a data definition newly created by the method.
Note that the <code>$xml</code> string is sent to Cascade without data checking.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createDataDefinition( DataDefinitionContainer $parent, string $name, string $xml ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_DATA_DEFINITION_NAME . E_SPAN );
            
        if( trim( $xml ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
            
        $asset                                      = AssetTemplate::getDataDefinition();
        $asset->dataDefinition->name                = $name;
        $asset->dataDefinition->parentContainerPath = $parent->getPath();
        $asset->dataDefinition->siteName            = $parent->getSiteName();
        $asset->dataDefinition->xml                 = $xml;
        
        return $this->createAsset(
            $asset, DataDefinition::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>DataDefinitionBlock</code> object,
representing either an existing data definition block, or a data definition block newly created by the method.
Also see <code>createXhtmlBlock( Folder $parent, $name, $text="" )</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createDataDefinitionBlock( Folder $parent, string $name, DataDefinition $d ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );

        $asset                                             = AssetTemplate::getDataDefinitionBlock();
        $asset->xhtmlDataDefinitionBlock->name             = $name;
        $asset->xhtmlDataDefinitionBlock->parentFolderPath = $parent->getPath();
        $asset->xhtmlDataDefinitionBlock->siteName         = $parent->getSiteName();
        $asset->xhtmlDataDefinitionBlock->structuredData   = $d->getStructuredData();
        
        return $this->createAsset(
            $asset, DataDefinitionBlock::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>DataDefinitionContainer</code> object,
representing either an existing data definition container, or a data definition container newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createDataDefinitionContainer( DataDefinitionContainer $parent, string $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_DATA_DEFINITION_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ DataDefinitionContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, DataDefinitionContainer::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Page</code> object,
representing either an existing page associated with a data definition, or a page newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createDataDefinitionPage( Folder $parent, string $name, ContentType $ct ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_NAME . E_SPAN );

        $asset                         = AssetTemplate::getDataDefinitionPage();
        $asset->page->name             = $name;
        $asset->page->parentFolderPath = $parent->getPath();
        $asset->page->siteName         = $parent->getSiteName();
        $asset->page->contentTypeId    = $ct->getId(); // could be from a different site
        $asset->page->structuredData   = $ct->getDataDefinition()->getStructuredData();
            
        return $this->createAsset(
            $asset, Page::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Destination</code> object,
representing either an existing destination, or a destination newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createDestination( 
        SiteDestinationContainer $parent, string $name, Transport $transport ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_DESTINATION_NAME . E_SPAN );
            
        $asset                                   = AssetTemplate::getDestination();
        $asset->destination->name                = $name;
        $asset->destination->parentContainerPath = $parent->getPath();
        
        $transport_path = $transport->getPath();
        $transport_site = $transport->getSiteName();
        
        // add site name if from Global
        if( $transport_site == NULL )
            $transport_path = "Global:" . $transport_path;
        
        $asset->destination->transportPath       = $transport_path;
        $asset->destination->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, Destination::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>FacebookConnector</code> object,
representing either an existing Facebook connector, or a Facebook connector newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>CreationErrorException</exception>
</documentation>
*/
    public function createFacebookConnector( ConnectorContainer $parent, string $name, Destination $d,
        string $pg_value, string $px_value,
        ContentType $ct, string $page_config ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONNECTOR_NAME . E_SPAN );
        if( trim( $pg_value ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_NAME . E_SPAN );
        if( trim( $px_value ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PREFIX . E_SPAN );
        if( trim( $page_config ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONFIGURATION_NAME . E_SPAN );
            
        $asset                                         = AssetTemplate::getFacebookConnector();
        $asset->facebookConnector->name                = $name;
        $asset->facebookConnector->parentContainerPath = $parent->getPath();
        $asset->facebookConnector->siteName            = $parent->getSiteName();
        $asset->facebookConnector->destinationId       = $d->getId();
        
        $page_name = new \stdClass();
        $page_name->name = "Page Name";
        $page_name->value = $pg_value;
        
        $prefix = new \stdClass();
        $prefix->name = "Prefix";
        $prefix->value = $px_value;
        
        $asset->facebookConnector->connectorParameters->
            connectorParameter = array();
        $asset->facebookConnector->connectorParameters->
            connectorParameter[] = $page_name;
        $asset->facebookConnector->connectorParameters->
            connectorParameter[] = $prefix;
        
        $asset->facebookConnector->connectorContentTypeLinks->
            connectorContentTypeLink->contentTypeId = $ct->getId();
        $asset->facebookConnector->connectorContentTypeLinks->
            connectorContentTypeLink->pageConfigurationName = $page_config;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $asset ); }
        return $this->createAsset(
            $asset, FacebookConnector::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>FeedBlock</code> object,
representing either an existing feed block, or a feed block newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createFeedBlock( Folder $parent, $name, $url ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException(                 
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );
            
        if( trim( $url ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_TEXT . E_SPAN );
            
        $asset                              = AssetTemplate::getFeedBlock();
        $asset->feedBlock->name             = $name;
        $asset->feedBlock->parentFolderPath = $parent->getPath();
        $asset->feedBlock->siteName         = $parent->getSiteName();
        $asset->feedBlock->feedURL          = $url;
        
        return $this->createAsset(
            $asset, FeedBlock::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>File</code> object, representing either an
existing file, or a file newly created by the method. <code>$text</code> is the textual
information to be inserted into the file, and <code>$data</code> is the binary data.
Either <code>$text</code> or <code>$data</code> must contain non-empty and non-<code>NULL</code>
information. If both do, <code>$text</code> takes precedence.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createFile( Folder $parent, $name, $text="", $data=NULL ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException(                 
                S_SPAN . c\M::EMPTY_FILE_NAME . E_SPAN );
            
        if( trim( $text ) == "" && $data == NULL )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_TEXT_DATA . E_SPAN );
            
        $asset                              = AssetTemplate::getReference();
        $asset->file->name                = $name;
        $asset->file->parentFolderPath    = $parent->getPath();
        $asset->file->siteName            = $parent->getSiteName();
        
        if( trim( $text ) != "" )
            $asset->file->text = trim( $text );
        else
            $asset->file->data = $data;
        
        return $this->createAsset(
            $asset, File::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>FileSystemTransport</code> object, representing either an existing file system transport, or a file system transport newly
created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createFileSystemTransport(
        TransportContainer $parent, $name, $directory ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_TRANSPORT_NAME . E_SPAN );
        if( trim( $directory ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_DIRECTORY . E_SPAN );
            
        $asset                                           = AssetTemplate::getFileSystemTransport();
        $asset->fileSystemTransport->name                = $name;
        $asset->fileSystemTransport->siteName            = $parent->getSiteName();
        $asset->fileSystemTransport->parentContainerPath = $parent->getPath();
        $asset->fileSystemTransport->directory           = $directory;
        
        return $this->createAsset(
            $asset, FileSystemTransport::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Folder</code> object, representing either
an existing folder, or a folder newly created by the method. Since this method can be used
to retrieve the Base Folder of a site, the <code>$parent</code> can be <code>NULL</code>.
In this case, the <code>$name</code> must the string <code>"/"</code> and the site name
must be non-empty. When a non-<code>NULL</code> parent folder is passed in, the name must
be non-empty, but the site name can be empty.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createFolder( Folder $parent=NULL, $name="", $site_name="" ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_FOLDER_NAME . E_SPAN );
            
        if( $parent == NULL && trim( $site_name ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_SITE_NAME . E_SPAN );
            
        $asset                               = AssetTemplate::getFolder();
        $asset->folder->name                 = $name;
        
        if( isset( $parent ) )
        {
            $asset->folder->parentFolderPath = $parent->getPath();
            $site_name = $parent->getSiteName();
        }            

           $asset->folder->siteName = $site_name;
        
        return $this->createAsset(
            $asset, Folder::TYPE, $this->getPath( $parent, $name ), $site_name );
    }
    
/**
<documentation><description><p>Returns a <code>IndexBlock</code> object, representing
either an existing index block of type "folder", or an index block newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createFolderIndexBlock( Folder $parent, $name, Folder $f=NULL,
        $max_rendered_assets=0 ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );
            
        $asset                                    = AssetTemplate::getIndexBlock(c\T::FOLDER );
        $asset->indexBlock->name                  = $name;
        $asset->indexBlock->parentFolderPath      = $parent->getPath();
        $asset->indexBlock->siteName              = $parent->getSiteName();
        $asset->indexBlock->maxRenderedAssets     = $max_rendered_assets;
        $asset->indexBlock->renderingBehavior     = "render-normally";
        if( isset( $f ) )
        {
            $asset->indexBlock->indexFolderId     = $f->getId();
            $asset->indexBlock->indexedFolderPath = $f->getPath();
        }
        $asset->indexBlock->indexPages            = false;
        $asset->indexBlock->indexBlocks           = false;
        $asset->indexBlock->indexLinks            = false;
        $asset->indexBlock->indexFiles            = false;
        $asset->indexBlock->indexRegularContent   = false;
        $asset->indexBlock->indexSystemMetadata   = false;
        $asset->indexBlock->indexUserMetadata     = false;
        $asset->indexBlock->indexAccessRights     = false;
        $asset->indexBlock->indexUserInfo         = false;
        $asset->indexBlock->indexWorkflowInfo     = false;
        $asset->indexBlock->appendCallingPageData = false;
        $asset->indexBlock->sortMethod            =c\T::ALPHABETICAL;
        $asset->indexBlock->sortOrder             =c\T::DESCENDING;
        $asset->indexBlock->pageXML               =c\T::NORENDER;
        
        return $this->createAsset(
            $asset, IndexBlock::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>This method combines <code>createScriptFormat</code> and
<code>createXsltFormat</code>. The <code>$type</code> must be either <code>ScriptFormat::TYPE</code> or <code>XsltFormat::TYPE</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createFormat( Folder $parent, $name, $type, $script="", $xml="" ) : Asset
    {
        $type = trim( $type );
        
        if( $type != XsltFormat::TYPE && $type != ScriptFormat::TYPE )
            throw new e\WrongAssetTypeException(
                S_SPAN . "$type is not a type of format." . E_SPAN );

        if( $type == ScriptFormat::TYPE )
            return $this->createScriptFormat( $parent, $name, $script );
        else
            return $this->createXsltFormat( $parent, $name, $xml );
    }
    
/**
<documentation><description><p>Returns a <code>FtpTransport</code> object, representing either an existing ftp transport, or an ftp transport newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createFtpTransport( 
        TransportContainer $parent, $name, $server, $port, $username, $password ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_TRANSPORT_NAME . E_SPAN );
        if( trim( $server ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_SERVER_NAME . E_SPAN );
        if( trim( $port ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_SERVER_PORT . E_SPAN );
        if( trim( $username ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_USER_NAME . E_SPAN );
        if( trim( $password ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );
            
        $asset                                    = AssetTemplate::getFtpTransport();
        $asset->ftpTransport->name                = $name;
        $asset->ftpTransport->siteName            = $parent->getSiteName();
        $asset->ftpTransport->parentContainerPath = $parent->getPath();
        $asset->ftpTransport->username            = trim( $username );
        $asset->ftpTransport->password            = trim( $password );
        $asset->ftpTransport->hostName            = trim( $server );
        $asset->ftpTransport->port                = trim( $port );
        
        return $this->createAsset(
            $asset, FtpTransport::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>GoogleAnalyticsConnector</code> object, representing either an existing Google Analytics connector, or a Google Analytics connector newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createGoogleAnalyticsConnector( ConnectorContainer $parent, $name, $profile_id ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_CONNECTOR_NAME . E_SPAN );
        if( trim( $profile_id ) == "" )
            throw new e\CreationErrorException(
                S_SPAN . c\M::EMPTY_PROFILE_ID . E_SPAN );
            
        $asset                                                = AssetTemplate::getGoogleAnalyticsConnector();
        $asset->googleAnalyticsConnector->name                = $name;
        $asset->googleAnalyticsConnector->parentContainerPath = $parent->getPath();
        $asset->googleAnalyticsConnector->siteName            = $parent->getSiteName();
        
        $param        = new \stdClass();
        $param->name  = "Google Analytics Profile Id";
        $param->value = $profile_id;
        $asset->googleAnalyticsConnector->
            connectorParameters->connectorParameter = array();
        $asset->googleAnalyticsConnector->
            connectorParameters->connectorParameter[ 0 ] = $param;

        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $asset ); }
        return $this->createAsset(
            $asset, GoogleAnalyticsConnector::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Group</code> object, representing either an existing group, or a group newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createGroup( $group_name, $role_name='Default' ) : Asset
    {
        if( trim( $group_name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_GROUP_NAME . E_SPAN );
            
        $asset                   = AssetTemplate::getGroup();
        $asset->group->groupName = $group_name;
        $asset->group->role      = $role_name;
        
        return $this->createAsset( $asset, Group::TYPE, $group_name );
    }
    
/**
<documentation><description><p>This method combines <code>createContentTypeIndexBlock</code> and <code>createFolderIndexBlock</code>. The type of index block created depends on the value of <code>$type</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createIndexBlock( Folder $parent, $name, $type, ContentType $ct=NULL, Folder $f=NULL,
        $max_rendered_assets=0 ) : Asset
    {
        if( $type == c\T::CONTENTTYPEINDEX )
            return $this->createContentTypeIndexBlock( 
                $parent, $name, $ct, $max_rendered_assets );
        else
            return $this->createFolderIndexBlock( 
                $parent, $name, $f, $max_rendered_assets );
    }

/**
<documentation><description><p>Returns a <code>MetadataSet</code> object, representing either an existing metadata set, or a metadata set newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createMetadataSet( MetadataSetContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_METADATA_SET_NAME . E_SPAN );
            
        $asset                                   = AssetTemplate::getMetadataSet();
        $asset->metadataSet->name                = $name;
        $asset->metadataSet->parentContainerPath = $parent->getPath();
        $asset->metadataSet->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, MetadataSet::TYPE, 
            $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>MetadataSetContainer</code> object, representing either an existing metadata set container, or a metadata set container newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createMetadataSetContainer( MetadataSetContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_METADATA_SET_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ MetadataSetContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, MetadataSetContainer::TYPE, 
            $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>This method combines <code>createDataDefinitionPage</code> and <code>createXhtmlPage</code>. The resulting page type depends on whether the content type passed in has a data definition.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createPage( Folder $parent, $name, ContentType $ct, $xhtml="" ) : Asset
    {
        if( $ct->getDataDefinition() != NULL )
            return $this->createDataDefinitionPage( $parent, $name, $ct );
        else
            return $this->createXhtmlPage( $parent, $name, $xhtml, $ct );
    }

/**
<documentation><description><p>Returns a <code>PageConfigurationSet</code> object, representing either an existing configuration set, or a configuration set newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createPageConfigurationSet( 
        PageConfigurationSetContainer $parent, 
        $name,        // configuration set name
        $config_name, // default configuration name
        Template $t, $extension, $type ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_CONFIGURATION_SET_NAME . E_SPAN );
            
        if( trim( $config_name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_CONFIGURATION_NAME . E_SPAN );

        if( trim( $extension ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_FILE_EXTENSION . E_SPAN );
            
        if( !c\SerializationTypeValues::isSerializationTypeValue( $type ) )
            throw new e\WrongSerializationTypeException( 
                S_SPAN . "The serialization type $type is not acceptable. " . E_SPAN );
        
        $config                        = AssetTemplate::getPageConfiguration();
        $config->name                  = $config_name;
        $config->defaultConfiguration  = true;
        $config->templateId            = $t->getId();
        $config->templatePath          = $t->getPath();
        $config->pageRegions           = $t->getPageRegionStdForPageConfiguration();
        $config->outputExtension       = $extension;
        $config->serializationType     = $type;
        
        $asset                                            = AssetTemplate::getPageConfigurationSet();
        $asset->pageConfigurationSet->name                = $name;
        $asset->pageConfigurationSet->parentContainerPath = $parent->getPath();
        $asset->pageConfigurationSet->siteName            = $parent->getSiteName();
        $asset->pageConfigurationSet->pageConfigurations->pageConfiguration = $config;
        
        return $this->createAsset(
            $asset, PageConfigurationSet::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>PageConfigurationSetContainer</code> object, representing either an existing page configuration set container, or a page configuration set container newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createPageConfigurationSetContainer( PageConfigurationSetContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_CONFIGURATION_SET_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ PageConfigurationSetContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, PageConfigurationSetContainer::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>PublishSet</code> object, representing either an existing publish set, or a publish set newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createPublishSet( PublishSetContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PUBLISH_SET_NAME. E_SPAN );
            
        $asset                                  = AssetTemplate::getPublishSet();
        $asset->publishSet->name                = $name;
        $asset->publishSet->parentContainerPath = $parent->getPath();
        $asset->publishSet->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, PublishSet::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }

/**
<documentation><description><p>Returns a <code>PublishSetContainer</code> object, representing either an existing publish set container, or a publish set container newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createPublishSetContainer( PublishSetContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PUBLISH_SET_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ PublishSetContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, PublishSetContainer::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Reference</code> object, representing either an existing reference, or a reference newly created by the method. <code>$a</code> is the object representing an asset (a page, file, or folder) to be referenced.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createReference( Asset $a, Folder $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_REFERENCE_NAME . E_SPAN );
            
        $asset                                 = AssetTemplate::getReference();
        $asset->reference->name                = $name;
        $asset->reference->parentFolderPath    = $parent->getPath();
        $asset->reference->siteName            = $parent->getSiteName();
        $asset->reference->referencedAssetType = $a->getType();
        $asset->reference->referencedAssetId   = $a->getId();
        
        return $this->createAsset(
            $asset, Reference::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Role</code> object, representing either an existing role, or a role newly created by the method. The type should be either "site" or "global".</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createRole( $name, $type ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_ROLE_NAME . E_SPAN );
        if( !c\RoleTypeValues::isRoleTypeValue( trim( $type ) ) )
            throw new e\CreationErrorException( 
                S_SPAN . "Unacceptable role type $type." . E_SPAN );
            
        $asset                 = AssetTemplate::getRole();
        $asset->role->name     = $name;
        $asset->role->roleType = $type;
        
        if( $type == Site::TYPE )
            $asset->role->siteAbilities   = new \stdClass();
        else
            $asset->role->globalAbilities = new \stdClass();
        
        return $this->createAsset( $asset, Role::TYPE, $name );
    }
    
/**
<documentation><description><p>Returns a <code>ScriptFormat</code> object, representing either an existing Velocity format, or a Velocity format newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createScriptFormat( Folder $parent, $name, $script ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_FORMAT_NAME . E_SPAN );
            
        if( trim( $script ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_SCRIPT . E_SPAN );
            
        $asset                                 = AssetTemplate::getFormat( c\P::SCRIPTFORMAT );
        $asset->scriptFormat->name             = $name;
        $asset->scriptFormat->parentFolderPath = $parent->getPath();
        $asset->scriptFormat->siteName         = $parent->getSiteName();
        $asset->scriptFormat->script           = $script;
        
        return $this->createAsset(
            $asset, ScriptFormat::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Site</code> object, representing either an existing site, or a site newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createSite( $name, $url, $recycle_bin_expiration ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_SYMLINK_NAME . E_SPAN );
            
        if( trim( $url ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_URL . E_SPAN );            
        
        if( trim( $recycle_bin_expiration ) == "" || 
            !c\RecycleBinExpirationValues::isRecycleBinExpirationValue( trim( $recycle_bin_expiration ) ) )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_RECYCLE_BIN_EXPIRATION . E_SPAN );
            
        $asset              = AssetTemplate::getSite();
        $asset->site->name  = $name;
        $asset->site->url   = $url;
        $asset->site->recycleBinExpiration = $recycle_bin_expiration;
        
        $site = $this->createAsset( $asset, Site::TYPE, $name );
        $site->setUrl( $url )->setRecycleBinExpiration( $recycle_bin_expiration )->edit();
        
        return $site;
    }
    
/**
<documentation><description><p>Returns a <code>SiteDestinationContainer</code> object, representing either an existing site destination container, or a site destination container newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createSiteDestinationContainer( SiteDestinationContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_SITE_DESTINATION_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ SiteDestinationContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, SiteDestinationContainer::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Symlink</code> object, representing either an existing symlink, or a symlink newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createSymlink( Folder $parent, $name, $url ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_SYMLINK_NAME . E_SPAN );
            
        if( trim( $url ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
            
        $asset                            = AssetTemplate::getSymlink();
        $asset->symlink->name             = $name;
        $asset->symlink->parentFolderPath = $parent->getPath();
        $asset->symlink->siteName         = $parent->getSiteName();
        $asset->symlink->linkURL          = $url;
        
        return $this->createAsset(
            $asset, Symlink::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>Template</code> object, representing either an existing template, or a template newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createTemplate( Folder $parent, $name, $xml ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_TEMPLATE_NAME . E_SPAN );
            
        if( trim( $xml ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
            
        $asset                              = AssetTemplate::getTemplate();
        $asset->template->name              = $name;
        $asset->template->parentFolderPath  = $parent->getPath();
        $asset->template->siteName          = $parent->getSiteName();
        $asset->template->xml               = trim( $xml );
        
        return $this->createAsset(
            $asset, Template::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>TextBlock</code> object, representing either an existing text block, or a text block newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createTextBlock( Folder $parent, $name, $text ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );
            
        if( trim( $text ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_TEXT . E_SPAN );
            
        $asset                              = AssetTemplate::getTextBlock();
        $asset->textBlock->name             = $name;
        $asset->textBlock->parentFolderPath = $parent->getPath();
        $asset->textBlock->siteName         = $parent->getSiteName();
        $asset->textBlock->text             = $text;
        
        return $this->createAsset(
            $asset, TextBlock::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>TransportContainer</code> object, representing either an existing transport container, or a transport container newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createTransportContainer( TransportContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_TRANSPORT_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ TransportContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, TransportContainer::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>TwitterConnector</code> object, representing either an existing Twitter connector, or a Twitter connector newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createTwitterConnector( ConnectorContainer $parent, $name, Destination $d,
        $ht_value, $px_value,
        ContentType $ct, $page_config ) : Asset
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Hash tag: $ht_value Prefix: $px_value" ); }
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONNECTOR_NAME . E_SPAN );
        if( trim( $ht_value ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_NAME . E_SPAN );
        if( trim( $px_value ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PREFIX . E_SPAN );
        if( trim( $page_config ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONFIGURATION_NAME . E_SPAN );
            
        $asset                                        = AssetTemplate::getTwitterConnector();
        $asset->twitterConnector->name                = $name;
        $asset->twitterConnector->parentContainerPath = $parent->getPath();
        $asset->twitterConnector->siteName            = $parent->getSiteName();
        $asset->twitterConnector->destinationId       = $d->getId();
        
        $ht_name = new \stdClass();
        $ht_name->name = "Hash Tag";
        $ht_name->value = $pg_value;
        
        $prefix = new \stdClass();
        $prefix->name = "Prefix";
        $prefix->value = $px_value;
        
        $asset->twitterConnector->connectorParameters->
            connectorParameter = array();
        $asset->twitterConnector->connectorParameters->
            connectorParameter[] = $ht_name;
        $asset->twitterConnector->connectorParameters->
            connectorParameter[] = $prefix;
        
        $asset->twitterConnector->connectorContentTypeLinks->
            connectorContentTypeLink->contentTypeId = $ct->getId();
        
        $asset->twitterConnector->connectorContentTypeLinks->
            connectorContentTypeLink->pageConfigurationName = $page_config;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $asset ); }
        return $this->createAsset(
            $asset, TwitterConnector::TYPE, $this->getPath( $parent, $name ),
            $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>User</code> object, representing either an existing user, or a user newly created by the method. Note that the role passed in should be a global role.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createUser( $user_name, $password, Group $group, Role $global_role ) : Asset
    {
        if( trim( $user_name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_USER_NAME . E_SPAN );
            
        if( trim( $password ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );
            
        $asset                 = AssetTemplate::getUser();
        $asset->user->username = $user_name;
        $asset->user->password = $password;
        $asset->user->groups   = $group->getId();
        $asset->user->role     = $global_role->getName();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $asset ); }
        return $this->createAsset( $asset, User::TYPE, $user_name );
    }
    
/**
<documentation><description><p>Returns a <code>WordPressConnector</code> object, representing either an existing WordPress connector, or a WordPress connector newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createWordPressConnector( ConnectorContainer $parent, $name, $url,
        ContentType $ct, $page_config ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONNECTOR_NAME . E_SPAN );
        if( trim( $url ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_NAME . E_SPAN );
        if( trim( $page_config ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_CONFIGURATION_NAME . E_SPAN );
            
        $asset                                          = AssetTemplate::getWordPressConnector();
        $asset->wordPressConnector->name                = $name;
        $asset->wordPressConnector->parentContainerPath = $parent->getPath();
        $asset->wordPressConnector->siteName            = $parent->getSiteName();
        $asset->wordPressConnector->url                 = trim( $url );
        
        $asset->wordPressConnector->connectorContentTypeLinks->
            connectorContentTypeLink->contentTypeId = $ct->getId();
        
        $asset->wordPressConnector->connectorContentTypeLinks->
            connectorContentTypeLink->pageConfigurationName = $page_config;
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $asset ); }
        return $this->createAsset(
            $asset, WordPressConnector::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>WorkflowDefinition</code> object, representing either an existing workflow definition, or a workflow definition newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createWorkflowDefinition( 
        WorkflowDefinitionContainer $parent, $name, $naming_behavior, $xml ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );
            
        if( trim( $xml ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_XML .E_SPAN );
            
        if( !c\NamingBehaviorValues::isNamingBehaviorValue( $naming_behavior ) )
            throw new e\UnacceptableValueException(                 
                S_SPAN . "The naming behavior $naming_behavior is unacceptable. " . E_SPAN );
    
        $asset                                          = AssetTemplate::getWorkflowDefinition();
        $asset->workflowDefinition->name                = $name;
        $asset->workflowDefinition->parentContainerPath = $parent->getPath();
        $asset->workflowDefinition->siteName            = $parent->getSiteName();
        $asset->workflowDefinition->xml                 = $xml;
        $asset->workflowDefinition->namingBehavior      = $naming_behavior;
        $asset->workflowDefinition->copy                = true;
        
        return $this->createAsset(
            $asset, WorkflowDefinition::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>WorkflowDefinitionContainer</code> object, representing either an existing workflow definition container, or a workflow definition container newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createWorkflowDefinitionContainer( WorkflowDefinitionContainer $parent, $name ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_WORKFLOW_DEFINITION_CONTAINER_NAME . E_SPAN );
            
        $property =c\T::$type_property_name_map[ WorkflowDefinitionContainer::TYPE ];
        $asset                                 = AssetTemplate::getContainer( $property );
        $asset->$property->name                = $name;
        $asset->$property->parentContainerPath = $parent->getPath();
        $asset->$property->siteName            = $parent->getSiteName();
        
        return $this->createAsset(
            $asset, WorkflowDefinitionContainer::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>DataDefinitionBlock</code> object, representing either an existing XHTML block, or an XHTML block newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createXhtmlBlock( Folder $parent, $name, $xhtml="" ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );

        $asset                                             = AssetTemplate::getDataDefinitionBlock();
        $asset->xhtmlDataDefinitionBlock->name             = $name;
        $asset->xhtmlDataDefinitionBlock->parentFolderPath = $parent->getPath();
        $asset->xhtmlDataDefinitionBlock->siteName         = $parent->getSiteName();
        
        if( trim( $xhtml ) != "" )
            $asset->xhtmlDataDefinitionBlock->xhtml        = $xhtml;
            
        return $this->createAsset(
            $asset, DataDefinitionBlock::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>This method combines <code>createXhtmlBlock</code> and <code>createDataDefinitionBlock</code>. The resulting block type depends on whether the data definition passed in is <code>NULL</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createXhtmlDataDefinitionBlock( Folder $parent, $name, DataDefinition $d=NULL, $xhtml="" ) : Asset
    {
        if( $d == NULL )
            return $this->createXhtmlBlock( $parent, $name, $xhtml );
        else
            return $this->createDataDefinitionBlock( $parent, $name, $d );
    }
    
/**
<documentation><description><p>Returns a <code>Page</code> object, representing either an existing page that is not associated with a data definition, or a page newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createXhtmlPage( Folder $parent, $name, $xhtml="", ContentType $ct ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_PAGE_NAME . E_SPAN );

        $asset                         = AssetTemplate::getXhtmlPage();
        $asset->page->name             = $name;
        $asset->page->parentFolderPath = $parent->getPath();
        $asset->page->siteName         = $parent->getSiteName();
        $asset->page->contentTypePath  = $ct->getPath();
        
        if( trim( $xhtml ) != "" )
            $asset->page->xhtml = $xhtml;
            
        return $this->createAsset(
            $asset, Page::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns an <code>XmlBlock</code> object, representing either an existing XML block, or an XML block newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createXmlBlock( Folder $parent, $name, $xml ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_BLOCK_NAME . E_SPAN );
            
        if( trim( $xml ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
            
        $asset                             = AssetTemplate::getXmlBlock();
        $asset->xmlBlock->name             = $name;
        $asset->xmlBlock->parentFolderPath = $parent->getPath();
        $asset->xmlBlock->siteName         = $parent->getSiteName();
        $asset->xmlBlock->xml              = $xml;
        
        return $this->createAsset(
            $asset, XmlBlock::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
/**
<documentation><description><p>Returns a <code>XsltFormat</code> object, representing either an existing XSLT format, or an XSLT format newly created by the method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function createXsltFormat( Folder $parent, $name, $xml ) : Asset
    {
        if( trim( $name ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_FORMAT_NAME . E_SPAN );
            
        if( trim( $xml ) == "" )
            throw new e\CreationErrorException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
            
        $asset                               = AssetTemplate::getFormat( c\P::XSLTFORMAT );
        $asset->xsltFormat->name             = $name;
        $asset->xsltFormat->parentFolderPath = $parent->getPath();
        $asset->xsltFormat->siteName         = $parent->getSiteName();
        $asset->xsltFormat->xml              = $xml;
        
        return $this->createAsset(
            $asset, XsltFormat::TYPE, $this->getPath( $parent, $name ), $parent->getSiteName() );
    }
    
    /* ================= */
    
/**
<documentation><description><p>Deletes all messages and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type>Cascade</return-type>
<exception></exception>
</documentation>
*/
    public function deleteAllMessages() : Cascade
    {
        MessageArrays::initialize( $this->service );
        return $this->deleteMessagesWithIds( 
            MessageArrays::$all_message_ids );
    }
    
/**
<documentation><description><p>Deletes all messages without issues, and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function deleteAllMessagesWithoutIssues()
    {
        MessageArrays::initialize( $this->service );
        return
            $this->deletePublishMessagesWithoutIssues()->
                   deleteUnpublishMessagesWithoutIssues();
    }
    
/**
<documentation><description><p>Deletes the asset, unsets the variable, and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type></return-type>
<exception>DeletionErrorException</exception>
</documentation>
*/
    public function deleteAsset( Asset $a )
    {
        $this->service->delete( $this->service->createId( $a->getType(), $a->getId() ) );
        
        if( !$this->service->isSuccessful() )
            throw new e\DeletionErrorException( 
                S_SPAN . c\M::DELETE_ASSET_FAILURE . E_SPAN . $e );

        unset( $a );
        return $this;
    }
    
/**
<documentation><description><p>Deletes all asset expiration messages, and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function deleteExpirationMessages()
    {
        MessageArrays::initialize( $this->service );
        return $this->deleteMessagesWithIds( 
            MessageArrays::$asset_expiration_message_ids );
    }
    
/**
<documentation><description><p>Deletes all publish messages without issues, and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function deletePublishMessagesWithoutIssues()
    {
        MessageArrays::initialize( $this->service );
        return $this->deleteMessagesWithIds( 
            MessageArrays::$publish_message_ids_without_issues );
    }
    
/**
<documentation><description><p>Deletes all summary messages without failures, and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function deleteSummaryMessagesNoFailures()
    {
        MessageArrays::initialize( $this->service );
        return $this->deleteMessagesWithIds( 
            MessageArrays::$summary_message_ids_no_failures );
    }
    
/**
<documentation><description><p>Deletes all unpublish messages without issues, and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function deleteUnpublishMessagesWithoutIssues()
    {
        MessageArrays::initialize( $this->service );
        return $this->deleteMessagesWithIds( 
            MessageArrays::$unpublish_message_ids_without_issues );
    }
    
/**
<documentation><description><p>Deletes all completed workflow messages, and returns <code>$cascade</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function deleteWorkflowMessagesIsComplete()
    {
        MessageArrays::initialize( $this->service );
        return $this->deleteMessagesWithIds( 
            MessageArrays::$workflow_message_ids_is_complete );
    }
    
/**
<documentation><description><p>Removes the access rights of the <code>Group</code> or <code>User</code> to the asset, and returns <code>$cascade</code>. The <code>$a</code> object must be either a <code>Group</code> object or a <code>User</code> object.</p></description>
<example></example>
<return-type>Cascade</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function denyAccess( Asset $a, string $type, string $id_path, 
        string $site_name=NULL, bool $applied_to_children=false ) : Cascade
    {
        $ari = $this->getAccessRights( $type, $id_path, $site_name );
        
        if( $a == NULL || ( $a->getType() != Group::TYPE && $a->getType() != User::TYPE ) )
        {
            throw new e\WrongAssetTypeException( 
                S_SPAN . c\M::ACCESS_TO_USERS_GROUPS . E_SPAN );
        }
        
        if( $a->getType() == Group::TYPE )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Denying " . $a->getName() . " access" ); }
            $func_name = 'denyGroupAccess';
        }
        else
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Denying " . $a->getName() . " access" ); }
            $func_name = 'denyUserAccess';
        }
        
        $ari->$func_name( $a );
        $this->setAccessRights( $ari, $applied_to_children );
        return $this;
    }
    
/**
<documentation><description><p>Sets all level to <code>none</code>, and returns <code>$cascade</code>. Note that when <code>$applied_to_children</code> is supplied while the <code>$site_name</code> is not needed, a <code>NULL</code> value must be passed in as the third argument.</p></description>
<example></example>
<return-type>Cascade</return-type>
<exception></exception>
</documentation>
*/
    public function denyAllAccess( string $type, string $id_path, 
        string $site_name=NULL, $applied_to_children=false ) : Cascade
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Denying all access" ); }
        $ari = $this->getAccessRights( $type, $id_path, $site_name );
        $ari->setAllLevel(c\T::NONE );
        $this->setAccessRights( $ari, $applied_to_children );
        return $this;
    }
    
/**
<documentation><description><p>Returns the access rights information (an <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/access-rights-information.php"><code>p\AccessRightsInformation</code></a> object).</p></description>
<example></example>
<return-type></return-type>
<exception>AccessRightsException</exception>
</documentation>
*/
    public function getAccessRights( $type, $id_path, $site_name=NULL )
    {
        // to make sure the asset exists
        $this->getAsset( $type, $id_path, $site_name );
        
        $this->service->readAccessRights(
            $this->service->createId( $type, $id_path, $site_name ) );
            
        if( $this->service->isSuccessful() )
        {
            return new p\AccessRightsInformation(
                $this->service->getReadAccessRightInformation() );
        }
        else
        {
            throw new e\AccessRightsException( $this->service->getMessage() );
        }
    }
    
/**
<documentation><description><p>Returns an array of messages (<a href="http://www.upstate.edu/cascade-admin/web-services/api/message.php"><code>Message</code></a> objects).</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getAllMessages()
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$all_messages;
    }
    
/**
<documentation><description><p>eturns an <code>Asset</code> object.</p></description>
<example></example>
<return-type></return-type>
<exception>NullAssetException, NoSuchTypeException, Exception</exception>
</documentation>
*/
    public function getAsset( $type, $id_path, $site_name=NULL )
    {
        try
        {
            return Asset::getAsset( $this->service, $type, $id_path, $site_name );
        }
        catch( \Exception $e )
        {
            throw $e;
        }
    }
    
/**
<documentation><description><p>Returns an object representing the asset bearing the ID, or <code>NULL</code> if there is no asset bearing that ID.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getAssetByIdString( $id_string )
    {
        $type = $this->service->getType( $id_string );
        
        if( $type != "The id does not match any asset type." )
        {
            return $this->getAsset( $type, $id_string );
        }
        return NULL;
    }
    
/**
<documentation><description><p>Returns an array of <a href="http://www.upstate.edu/cascade-admin/web-services/api/audit.php"><code>Audit</code></a> objects. The <code>$type</code> string can be empty, or one of the types defined for auditing. The two <code>DateTime</code> objects can be used to filter the returned objects. <code>$start_time</code> must be before or identical to <code>$end_time</code>.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getAudits( 
        Asset $a, string $type="", 
        \DateTime $start_time=NULL, \DateTime $end_time=NULL ) : array
    {
        return $a->getAudits( $type, $start_time, $end_time );
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object, with the root set to be the Base Folder of the site.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolderAssetTree( string $site_name ) : AssetTree
    {
        return $this->getFolder( '/', $site_name )->getAssetTree();
    }
    
/**
<documentation><description><p>Returns an array of groups (<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/identifier.php"><code>p\Identifier</code></a> objects).</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroups()
    {
        if( $this->groups == NULL )
        {
            $search_for               = new \stdClass();
            $search_for->matchType    =c\T::MATCH_ANY;
            $search_for->searchGroups = true;
            $search_for->assetName    = '*';
    
            $this->service->search( $search_for );
            
            if ( $this->service->isSuccessful() )
            {
                if( !is_null( $this->service->getSearchMatches()->match ) )
                {
                    $groups = $this->service->getSearchMatches()->match;
                    $this->groups = array();
                    
                    if( count( $groups ) == 1 ) // a string
                        $this->groups[] = new p\Identifier( $groups );
                    else
                        foreach( $groups as $group )
                            $this->groups[] = new p\Identifier( $group );
                }
            }
        }
        return $this->groups;
    }
    
/**
<documentation><description><p>Returns an array of groups (<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/identifier.php"><code>p\Identifier</code></a> objects) bearing the name. If <code>$name</code> is not supplied, then this method becomes an alias of <code>getGroups()</code>. The name can be an ID of a group, or it can be a string containing wild-card characters.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupsByName( $name="" )
    {
        if( $name == "" )
            return $this->getGroups();
            
        $group_ids                = array();
        $search_for               = new \stdClass();
        $search_for->matchType    =c\T::MATCH_ANY;
        $search_for->searchGroups = true;
        $search_for->assetName    = $name;

        $this->service->search( $search_for );
        
        if ( $this->service->isSuccessful() )
        {
            if( !is_null( $this->service->getSearchMatches()->match ) )
            {
                $groups = $this->service->getSearchMatches()->match;
        
                if( count( $groups ) == 1 )
                    $group_ids[] = new p\Identifier( $groups );
                else
                    foreach( $groups as $group )
                        $group_ids[] = new p\Identifier( $group );
            }
        }
        return $group_ids;
    }    
    
/**
<documentation><description><p>Returns a <a href="http://www.upstate.edu/cascade-admin/web-services/api/message.php"><code>Message</code></a> object bearing the id.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMessage( string $id )
    {
        MessageArrays::initialize( $this->service );
    
        if( isset( MessageArrays::$id_obj_map[ $id ] ) )
            return MessageArrays::$id_obj_map[ $id ];
            
        return NULL;
    }
    
/**
<documentation><description><p>Returns the id-<code>Message</code> object map.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getMessageIdObjMap() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$id_obj_map;
    }
    
/**
<documentation><description><p>Returns a <code><a href="http://www.upstate.edu/cascade-admin/web-services/api/preference.php">Preference</a></code> object, representing system preferences.</p></description>
<example></example>
<return-type>Preference</return-type>
<exception></exception>
</documentation>
*/
    public function getPreference() : Preference
    {
        if( is_null( $this->preference ) )
        {
            $this->service->readPreferences();
            $this->preference = 
                new Preference( $this->service, $this->service->getPreferences() );
        }
        return $this->preference;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Publish".</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getPublishMessages() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$publish_messages;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Publish" with issues.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPublishMessagesWithIssues()
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$publish_messages_with_issues;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Publish" without issues.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPublishMessagesWithoutIssues()
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$publish_messages_without_issues;
    }
    
/**
<documentation><description><p>Returns the <code>Role</code> object bearing the numeric ID. This is equivalent to <code>$cascade-&gt;getAsset( Role::TYPE, $role_id )</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function getRoleAssetById( string $role_id ) : Asset
    {
        if( $this->roles == NULL )
        {
            $this->getRoles();
        }
        
        if( !$this->hasRoleId( $role_id ) )
            throw new e\NullAssetException( 
                S_SPAN . c\M::WRONG_ROLE . E_SPAN );
        
        return $this->role_id_object_map[ $role_id ];
    }
    
/**
<documentation><description><p>Returns the <code>Role</code> object bearing the name. Note that this method throws a <code>NullAssetException</code> object if the named role does not exist.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function getRoleAssetByName( $role_name ) : Asset
    {
        $this->getRoles();
        
        if( !$this->hasRoleName( $role_name ) )
            throw new e\NullAssetException( 
                S_SPAN . c\M::WRONG_ROLE . E_SPAN );
        
        return $this->role_name_object_map[ $role_name ];
    }
    
/**
<documentation><description><p>An alias of <code>getRoleAssetById( $role_id )</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function getRoleById( $role_id ) : Asset
    {
        return $this->getRoleAssetById( $role_id );
    }
    
/**
<documentation><description><p>An alias of <code>getRoleAssetByName( $role_name )</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function getRoleByName( $role_name ) : Asset
    {
        return $this->getRoleAssetByName( $role_name );
    }
    
/**
<documentation><description><p>Returns an array of all role numeric ID's.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getRoleIds() : array
    {
        if( $this->roles == NULL )
        {
            $this->getRoles();
        }
        return array_keys( $this->role_id_object_map );
    }
    
/**
<documentation><description><p>Returns an array of all role names.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getRoleNames() : array
    {
        if( $this->roles == NULL )
        {
            $this->getRoles();
        }
        return array_keys( $this->role_name_object_map );
    }
    
/**
<documentation><description><p>Returns an array of roles (<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/identifier.php"><code>p\Identifier</code></a> objects).</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getRoles() : array
    {
        // sleep for creation of new roles
        sleep( 5 );
    
        $this->role_name_object_map = array();
        $this->role_id_object_map   = array();
    
        $search_for              = new \stdClass();
        $search_for->matchType   =c\T::MATCH_ANY;
        $search_for->searchRoles = true;
        $search_for->assetName   = '*';

        $this->service->search( $search_for );
        
        if ( $this->service->isSuccessful() )
        {
            if( !is_null( $this->service->getSearchMatches()->match ) )
            {
                $roles = $this->service->getSearchMatches()->match;
                $this->roles = array();
        
                foreach( $roles as $role )
                {
                    $role_identifier = new p\Identifier( $role );
                    $this->roles[]   = $role_identifier;
                    $role_object     = $role_identifier->getAsset( $this->service );
                    if( self::DEBUG ) { u\DebugUtility::out( $role_object->getName() ); }
                    $this->role_name_object_map[ $role_object->getName() ] = $role_object;
                    $this->role_id_object_map[ $role_object->getId() ]     = $role_object;
                }
            }
        }
        return $this->roles;
    }    

/**
<documentation><description><p>Returns the <code>$service</code> object passes into the constructor.</p></description>
<example></example>
<return-type>AssetOperationHandlerService</return-type>
<exception></exception>
</documentation>
*/
    public function getService() : aohs\AssetOperationHandlerService
    {
        return $this->service;
    }
    
/**
<documentation><description><p>Returns the named site (a <code>Site</code> object). Note that this method throws <code>NoSuchSiteException</code> if the named site does not exists.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NoSuchSiteException</exception>
</documentation>
*/
    public function getSite( string $site_name ) : Asset
    {
        $this->getSites();
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $this->name_site_map ); }
        
        if( !isset( $this->name_site_map[ $site_name ] ) )
        {
            throw new e\NoSuchSiteException(                 
                S_SPAN . "The site $site_name does not exist." . E_SPAN );
        }
        
        return Asset::getAsset( $this->service, Site::TYPE, 
            $this->name_site_map[ $site_name ]->getId() );
    }
    
/**
<documentation><description><p>Returns an array of site identifiers (<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/identifier.php"><code>p\Identifier</code></a> objects).</p></description>
<example></example>
<return-type>array</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function getSites() : array
    {
        if( $this->sites == NULL )
        {
            $this->service->listSites();
            $this->name_site_map = array();
            
            if( $this->service->isSuccessful() )
            {
                $assetIdentifiers = $this->service->getReply()->listSitesReturn->sites->assetIdentifier;
                
                foreach( $assetIdentifiers as $identifier )
                {
                    $site = new p\Identifier( $identifier );
                    $this->sites[] = $site;
                    $this->name_site_map[ $identifier->path->path ] = $site;
                }
            }
            else
            {
                throw new \Exception( $this->service->getMessage() );
            }
        }
        return $this->sites;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Summary".</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getSummaryMessages() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$summary_messages;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Summary" without failures.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getSummaryMessagesNoFailures() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$summary_messages_no_failures;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Summary" with failures.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getSummaryMessagesWithFailures() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$summary_messages_with_failures;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Un-publish".</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getUnpublishMessages() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$unpublish_messages;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Un-publish" with issues.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getUnpublishMessagesWithIssues() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$unpublish_messages_with_issues;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Un-publish" without issues.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getUnpublishMessagesWithoutIssues() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$unpublish_messages_without_issues;
    }
    
/**
<documentation><description><p>Returns an array of users (<code>p\Identifier</code> objects).</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getUsers() : array
    {
        $user_name_array = array();
        
        // maximally returns 250 users
        if( $this->users == NULL )
        {
            $search_for              = new \stdClass();
            $search_for->matchType   =c\T::MATCH_ANY;
            $search_for->searchUsers = true;
            $search_for->assetName   = '*';
    
            $this->service->search( $search_for );
            
            if ( $this->service->isSuccessful() )
            {
                if( !is_null( $this->service->getSearchMatches()->match ) )
                {
                    $users = $this->service->getSearchMatches()->match;
                    $this->users = array();
            
                    foreach( $users as $user )
                    {
                        $this->users[] = new p\Identifier( $user );
                        $user_name_array[]  = $user->id;
                    }
                }
            }
        }
        
        // add those that belong to groups
        $extra_names = array();
        $extra_users = array();
        
        if( $this->groups == NULL || count( $this->groups ) == 0 )
        {
            $this->getGroups();
        }
        
        if( count( $this->groups ) > 0 )
        {
            foreach( $this->groups as $group )
            {
                $users = $group->getAsset( $this->service )->getUsers();
            
                $users = explode( ';', $users ); // array
                
                foreach( $users as $user )
                {
                    if( trim( $user ) != "" && !in_array( $user, $user_name_array ) &&
                        !in_array( $user, $extra_names ) )
                    {
                        $user_std       = new \stdClass();
                        $user_std->id   =  $user;
                        $user_std->path = new \stdClass();
                        $user_std->path->path = NULL;
                        $user_std->path->siteName = NULL;
                        $user_std->type = User::TYPE;
                        $user_std->recycled = false;
                        $extra_users[] = new p\Identifier( $user_std );
                        $extra_names[] = $user;
                    }
                }
            }
        }
        return array_merge( $this->users, $extra_users );
    }
    
/**
<documentation><description><p>Returns an array of users (<a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/identifier.php"><code>p\Identifier</code></a> objects) bearing the name. If <code>$name</code> is not supplied, then this method becomes an alias of <code>getUsers()</code>. The name can be an ID of a user, or it can be a string containing wild-card characters.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getUsersByName( string $name="" ) : array
    {
        if( $name == "" )
            return $this->getUsers();
            
        $user_ids                 = array();
        $search_for               = new \stdClass();
        $search_for->matchType    =c\T::MATCH_ANY;
        $search_for->searchUsers  = true;
        $search_for->assetName    = $name;

        $this->service->search( $search_for );
        
        if ( $this->service->isSuccessful() )
        {
            if( !is_null( $this->service->getSearchMatches()->match ) )
            {
                $users = $this->service->getSearchMatches()->match;
        
                if( count( $users ) == 1 )
                    $user_ids[] = new p\Identifier( $users );
                else
                    foreach( $users as $user )
                        $user_ids[] = new p\Identifier( $user );
            }
        }
        return $user_ids;
    }    
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Workflow".</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflowMessages() : array
    {
        MessageArrays::initialize( $this->service );
        return $workflow_messages;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Workflow" which are complete.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflowMessagesIsComplete() : array
    {
        MessageArrays::initialize( $this->service );
        return $workflow_messages_complete;
    }
    
/**
<documentation><description><p>Returns an array of <code>Message</code> objects of type "Workflow" which are non-complete.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflowMessagesOther() : array
    {
        MessageArrays::initialize( $this->service );
        return MessageArrays::$workflow_messages_other;
    }
    
/**
<documentation><description><p>Grants access rights to a <code>Group</code> or a <code>User</code> to the asset, and returns the object. Note that the asset <code>$a</code> must be either a <code>Group</code> object or a <code>User</code> object. <code>$level</code> can be either <code>constants\T::READ</code> or <code>constants\T::WRITE</code>.</p></description>
<example></example>
<return-type>Cascade</return-type>
<exception></exception>
</documentation>
*/
    public function grantAccess( Asset $a, string $type, string $id_path, 
        string $site_name=NULL, bool $applied_to_children=false, 
        string $level=c\T::READ ) : Cascade
    {
        $ari = $this->getAccessRights( $type, $id_path, $site_name );
        
        if( $a == NULL || ( $a->getType() != Group::TYPE && $a->getType() != User::TYPE ) )
        {
            throw new e\WrongAssetTypeException( 
                S_SPAN . c\M::ACCESS_TO_USERS_GROUPS . E_SPAN );
        }
        
        if( !c\LevelValues::isLevel( $level ) )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "The level $level is unacceptable." . E_SPAN );
        }
        
        if( $a->getType() == Group::TYPE && $level ==c\T::READ )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Granting " . $a->getName() . " read access to " . $id_path ); }
            $func_name = 'grantGroupReadAccess';
        }
        else if( $a->getType() == Group::TYPE && $level ==c\T::WRITE )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Granting " . $a->getName() . " write access to " . $id_path ); }
            $func_name = 'grantGroupWriteAccess';
        }
        else if( $a->getType() == User::TYPE && $level ==c\T::READ )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Granting " . $a->getName() . " read access to " . $id_path ); }
            $func_name = 'grantUserReadAccess';
        }
        else if( $a->getType() == User::TYPE && $level ==c\T::WRITE )
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Granting " . $a->getName() . " write access to " . $id_path ); }
            $func_name = 'grantUserWriteAccess';
        }
        
        if( isset( $func_name ) )
        {
            $ari->$func_name( $a );
            $this->setAccessRights( $ari, $applied_to_children );
        }
        else
        {
            if( self::DEBUG ) { u\DebugUtility::out( "The function name is not set." ); }
        }
        return $this;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether group exists.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasGroup( string $group_name ) : bool
    {
        try
        {
            $this->getAsset( Group::TYPE, $group_name );
            return true;
        }
        catch( \Exception $e )
        {
            echo S_PRE . $e . E_PRE;
            return false;
        }
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasRoleId( $role_id )
    {
        if( $this->roles == NULL )
        {
            $this->getRoles();
        }
        return in_array( $role_id, array_keys( $this->role_id_object_map ) );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasRoleName( $role_name )
    {
        if( $this->roles == NULL )
        {
            $this->getRoles();
        }
        return in_array( $role_name, array_keys( $this->role_name_object_map ) );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function moveAsset( Asset $a, Container $new_parent )
    {
        if( $a == NULL || $new_parent == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET . E_SPAN );
        }
        if( $a->getParentContainer()->getId() == $new_parent->getId() )
        {
            throw new e\RenamingFailureException( 
                S_SPAN . c\M::SAME_CONTAINER . E_SPAN );
        }
        
        $this->service->move( 
            $a->getIdentifier(),
            $new_parent->getIdentifier(),
            $a->getName(),
            false );
            
        if( !$this->service->isSuccessful() )
        {
            throw new e\RenamingFailureException( 
                S_SPAN . c\M::RENAME_ASSET_FAILURE . E_SPAN );
        }
            
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function renameAsset( Asset $a, $new_name, $doWorkflow=false )
    {
        if( $a == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ASSET . E_SPAN );
        }
        if( trim( $new_name ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_NAME . E_SPAN );
        }
        
        $this->service->move( 
            $a->getIdentifier(),
            $a->getParentContainer()->getIdentifier(),
            $new_name,
            $doWorkflow );
            
        if( !$this->service->isSuccessful() )
        {
            throw new e\RenamingFailureException( 
                S_SPAN . c\M::RENAME_ASSET_FAILURE . E_SPAN );
        }
            
        return $this;
    }   

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchForAll( $asset_name, $asset_content, $asset_metadata, $search_type )
    {
        return $this->search(c\T::MATCH_ALL, $asset_name, $asset_content, $asset_metadata, $search_type );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchForAssetContent( $asset_content, $search_type )
    {
        if( trim( $asset_content ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_ASSET_CONTENT . E_SPAN );
        }
        return $this->search(c\T::MATCH_ANY, "", $asset_content, "", $search_type );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchForAssetName( $asset_name, $search_type )
    {
        if( trim( $asset_name ) == "" )
        {
            throw new e\EmptyNameException(
                S_SPAN . c\M::EMPTY_ASSET_NAME . E_SPAN );
        }
        return $this->search(c\T::MATCH_ANY, $asset_name, "", "", $search_type );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function searchForAssetMetadata( $asset_metadata, $search_type )
    {
        if( trim( $asset_metadata ) == "" )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_ASSET_METADATA . E_SPAN );
        }
        return $this->search(c\T::MATCH_ANY, "", "", $asset_metadata, $search_type );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setAccessRights( p\AccessRightsInformation $ari, $apply_to_children=false )
    {
        if( !c\BooleanValues::isBoolean( $apply_to_children ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $apply_to_children must be a boolean." . E_SPAN );
    
        if( isset( $ari ) )
        {
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $ari->toStdClass() ); }
        
            $this->service->editAccessRights( $ari->toStdClass(), $apply_to_children ); 
        }
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setAllLevel( $type, $id_path, $site_name=NULL, $level=C\T::NONE, $applied_to_children=false )
    {
        $ari = $this->getAccessRights( $type, $id_path, $site_name );
        $ari->setAllLevel( $level );
        $this->setAccessRights( $ari, $applied_to_children );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPreference( $name, $value )
    {
        if( !isset( $this->preference ) )
            $this->getPreference();
            
        $this->preference->setValue( $name, $value );
        return $this;
    }
    
    private function createAsset( \stdClass $std, $type, $id_path, $site_name="" )
    {
        // try retrieval first to avoid creating asset of the same name
        try
        {
            if( $type == Role::TYPE )
            {
                return $this->getRoleByName( $id_path );
            }
            return $this->getAsset( $type, $id_path, $site_name );
        }
        catch( \Exception $e )
        {
            $this->service->create( $std );
        
            if( !$this->service->isSuccessful() )
            {
                echo $this->service->getLastResponse();
                throw new e\CreationErrorException(
                    S_SPAN . c\M::CREATE_ASSET_FAILURE . E_SPAN . $this->service->getMessage() );
            }
            //else echo "Successfully created the asset $type, $id_path, $site_name. " . BR;
        }
        // returns the object created
        if( $type == Role::TYPE )
        {
            return $this->getRoleByName( $id_path );
        }
        return $this->getAsset( $type, $id_path, $site_name );
    }
    
    private function deleteMessagesWithIds( $ids )
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Inside deleteMessagesWithIds" ); }
        
        if( !is_array( $ids ) )
            throw new \Exception( 
                S_SPAN . c\M::NOT_ARRAY . E_SPAN );
            
        if( count( $ids ) > 0 )
        {
            foreach( $ids as $id )
            {
                $this->service->deleteMessage( 
                    $this->service->createIdWithIdType( $id,c\T::MESSAGE ) );
            }
        }
        
        return $this;
    }
    
    private function getPath( Asset $parent=NULL, $name="" )
    {
        if( $parent == NULL || $parent->getPath() == "/" )
            $path = $name;
        else
            $path = $parent->getPath() . '/' . $name;
        
        return $path;
    }
    
    private function search( 
        $match_type=c\T::MATCH_ANY, 
        $asset_name='', 
        $asset_content='', 
        $asset_metadata='', // metadata overrides others when any
        $search_type='' )
    {
        if( !c\SearchTypes::isSearchType( trim( $search_type ) ) )
        {
            throw new e\NoSuchTypeException( 
                S_SPAN . "The search type $search_type does not exist." . E_SPAN );
        }
        
        if( $match_type !=c\T::MATCH_ANY && $match_type !=c\T::MATCH_ALL )
        {
            throw new e\NoSuchTypeException( 
                S_SPAN . "The match type $match_type does not exist." . E_SPAN );
        }
    
        $search_for = new \stdClass();
        $search_for->matchType     = $match_type;
        $search_for->$search_type  = true;
        
        if( trim( $asset_name ) != "" )
            $search_for->assetName = $asset_name;
        if( trim( $asset_content ) != "" )
            $search_for->assetContent = $asset_content;
        if( trim( $asset_metadata ) != "" )
            $search_for->assetMetadata = $asset_metadata;
            
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $search_for ); }
            
        $this->service->search( $search_for );
    
        // if succeeded
        if ( $this->service->isSuccessful() )
        {
            $results = array();
            
            if( !is_null( $this->service->getSearchMatches()->match ) )
            {
                $temp = $this->service->getSearchMatches()->match;
                
                if( !is_array( $temp ) )
                {
                    $temp = array( $temp );
                }
                    
                foreach( $temp as $match )
                {
                    $results[] = new p\Identifier( $match );
                }
            }
            return $results;
        }
        else
        {
            throw new e\SearchException( $this->service->getMessage() );
        }
    }
    
    private $service;
    private $sites;
    private $name_site_map;
    private $groups;
    private $roles;
    private $role_name_object_map;
    private $role_id_object_map;
    private $users;
    private $preference;
}
?>