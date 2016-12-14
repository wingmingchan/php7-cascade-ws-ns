<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/28/2016 Added a set of get methods to return root containers.
  * 10/29/2015 Added getExternalLinkCheckOnPublish and setExternalLinkCheckOnPublish.
  * 5/28/2015 Added namespaces.
  * 8/12/2014 Added getUnpublishOnExpiration, setUnpublishOnExpiration, getLinkCheckerEnabled, and setLinkCheckerEnabled.
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
<p>An <code>Site</code> object represents a site asset. This class is a sub-class of <a href="/web-services/api/asset-classes/scheduled-publishing"><code>ScheduledPublishing</code></a>.</p>
<h2>Structure of <code>site</code></h2>
<pre>site
  id
  name
  url
  defaultMetadataSetId
  defaultMetadataSetPath
  cssFileId
  cssFilePath
  cssFileRecycled (bool)
  siteAssetFactoryContainerId
  siteAssetFactoryContainerPath
  siteStartingPageId
  siteStartingPagePath
  siteStartingPageRecycled (bool)
  cssClasses
  roleAssignments
    roleAssignment
      roleId
      roleName
      users
      groups
  usesScheduledPublishing (bool)
  scheduledPublishDestinationMode
  scheduledPublishDestinations
  timeToPublish
  publishIntervalHours
  publishDaysOfWeek
    dayOfWeek
  cronExpression
  sendReportToUsers
  sendReportToGroups
  sendReportOnErrorOnly (bool)
  recycleBinExpiration
  unpublishOnExpiration (bool)
  linkCheckerEnabled (bool)
  externalLinkCheckOnPublish (bool)
  rootFolderId
  rootAssetFactoryContainerId
  rootPageConfigurationSetContainerId
  rootContentTypeContainerId
  rootConnectorContainerId
  rootDataDefinitionContainerId
  rootMetadataSetContainerId
  rootPublishSetContainerId
  rootSiteDestinationContainerId
  rootTransportContainerId
  rootWorkflowDefinitionContainerId
</pre>
<h2>Design Issues</h2>
<p>There is something special about all <code>ScheduledPublishing</code> assets: right after such an asset is read from Cascade, if we send the asset back to Cascade by calling <code>edit</code>, even without making any changes to it, Cascade will reject the asset. To fix this problem, we have to call <code>unset</code> to unset any property related to scheduled publishing if the property stores a <code>NULL</code> value. This must be done inside <code>edit</code>, or an exception will be thrown.</p>


</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/site.php">site.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{ "asset":{
    "site":{
      "defaultMetadataSetId":"f7a963087f0000012693e3d9b68e9e1d",
      "defaultMetadataSetPath":"Default",
      "siteAssetFactoryContainerId":"f7a963197f0000012693e3d94a278887",
      "siteAssetFactoryContainerPath":"Default",
      "siteStartingPageRecycled":false,
      "url":"http://sandbox.SUNY Upstate.com",
      "recycleBinExpiration":"15",
      "cssFileRecycled":false,
      "roleAssignments":[ { 
        "roleId":"11",
        "roleName":"Test Site Manager",
        "groups":"SUNY Upstate-testers" } ],
      "usesScheduledPublishing":false,
      "sendReportOnErrorOnly":false,
      "rootFolderId":"f7a9630b7f0000012693e3d99c134cef",
      "rootAssetFactoryContainerId":"f7a963107f0000012693e3d9a7be58e8",
      "rootPageConfigurationSetContainerId":"f7a9631f7f0000012693e3d90c58cbf5",
      "rootContentTypeContainerId":"f7a963297f0000012693e3d966e34575",
      "rootDataDefinitionContainerId":"f7a9632d7f0000012693e3d9809faca9",
      "rootMetadataSetContainerId":"f7a963417f0000012693e3d9d1302ce2",
      "rootPublishSetContainerId":"f7a963317f0000012693e3d9db70427b",
      "rootSiteDestinationContainerId":"f7a963387f0000012693e3d9090cd7a9",
      "rootTransportContainerId":"f7a963357f0000012693e3d98d968254",
      "rootWorkflowDefinitionContainerId":"f7a9633c7f0000012693e3d98ac14ce1",
      "rootConnectorContainerId":"f7a963247f0000012693e3d92c905759",
      "unpublishOnExpiration":true,
      "linkCheckerEnabled":true,
      "externalLinkCheckOnPublish":false,
      "name":"SUNY Upstate",
      "id":"f7a963087f0000012693e3d9932e44ba" } },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class Site extends ScheduledPublishing
{
    const DEBUG    = false;
    const DUMP     = false;
    const TYPE     = c\T::SITE;
    const NEVER    = c\T::NEVER;
    const ONE      = c\T::ONE;
    const FIFTEEN  = c\T::FIFTEEN;
    const THIRTY   = c\T::THIRTY;
    
/**
<documentation><description><p>The constructor, overriding the parent method to process role assignments.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->processRoleAssignments();
    }
    
/**
<documentation><description><p>Adds a new role to <code>roleAssignments</code>, and returns the calling object.</p></description>
<example>$s->addRole( $r )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addRole( Role $r ) : Asset
    {
        if( $r == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ROLE . E_SPAN );
            
        if( $this->hasRole( $r ) )
        {
            return $this;
        }
        
        $ra = new \stdClass();
        $ra->roleId   = $r->getId();
        $ra->roleName = $r->getName();
        $ra->users    = NULL;
        $ra->groups   = NULL;
        
        $this->role_assignments[] = new p\RoleAssignment( $ra );
        
        return $this;
    }
    
/**
<documentation><description><p>Adds the group to the named role, and returns the calling object.</p></description>
<example>$s->addUserToRole( 
        $r, $cascade->getAsset( a\User::TYPE, 'chanw' ) )->
    addGroupToRole( 
        $r, $cascade->getAsset( a\Group::TYPE, 'cru' ) )->
    edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addGroupToRole( Role $r, Group $g ) : Asset
    {
        $role_name = $r->getName();
        
        if( !$this->hasRole( $r ) )
        {
            $this->addRole( $r );
        }
        
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() == $role_name )
            {
                $assignment->addGroup( $g );
                break;
            }
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Adds the user to the named role, and returns the calling object.</p></description>
<example>$s->addUserToRole( 
        $r, $cascade->getAsset( a\User::TYPE, 'chanw' ) )->
    addGroupToRole( 
        $r, $cascade->getAsset( a\Group::TYPE, 'cru' ) )->
    edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addUserToRole( Role $r, User $u ) : Asset
    {
        $role_name = $r->getName();

        if( !$this->hasRole( $r ) )
        {
            $this->addRole( $r );
        }
        
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() == $role_name )
            {
                $assignment->addUser( $u );
                break;
            }
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
</documentation>
*/
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $site = $this->getProperty();
        
        if( $site->usesScheduledPublishing ) // publishing is scheduled
        {
            if( !isset( $site->timeToPublish ) ||
                is_null( $site->timeToPublish ) )
            {
                unset( $site->timeToPublish );
            }
            // fix the time unit
            else if( strpos( $site->timeToPublish, '-' ) !== false )
            {
                $pos = strpos( $site->timeToPublish, '-' );
                $site->timeToPublish = substr(
                    $site->timeToPublish, 0, $pos );
            }
      
            if( !isset( $site->publishIntervalHours ) ||
            	is_null( $site->publishIntervalHours ) )
                unset( $site->publishIntervalHours );
                
            if( !isset( $site->publishDaysOfWeek ) ||
            	is_null( $site->publishDaysOfWeek ) )
                unset( $site->publishDaysOfWeek );
                
            if( !isset( $site->cronExpression ) ||
            	is_null( $site->cronExpression ) )
                unset( $site->cronExpression );
        }

        $assignment_count      = count( $this->role_assignments );
        $site->roleAssignments = new \stdClass();
        
        if( $assignment_count == 1 )
        {
            $site->roleAssignments->roleAssignment = 
                $this->role_assignments[ 0 ]->toStdClass();
        }
        else if( $assignment_count > 1 )
        {
            $site->roleAssignments->roleAssignment = array();
            
            foreach( $this->role_assignments as $assignment )
            {
                $site->roleAssignments->roleAssignment[] = $assignment->toStdClass();
            }
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $site ); }
        
        $asset                                    = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $site;
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        return $this->reloadProperty();
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at Base Folder.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getAssetTree() : AssetTree
    {
        return $this->getBaseFolder()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns Base Folder (a <code>Folder</code> object).</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolder() : Asset
    {
        if( $this->base_folder == NULL )
        {
            $this->base_folder = 
                Folder::getAsset( $this->getService(), 
                Folder::TYPE, $this->getRootFolderId() );
        }
        return $this->base_folder;
    }
    
/**
<documentation><description><p>An alias of <code>getAssetTree</code>.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolderAssetTree() : AssetTree
    {
        return $this->getBaseFolder()->getAssetTree();
    }
    
/**
<documentation><description><p>An alias of <code>getRootFolderId</code>.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolderId() : string
    {
        return $this->getRootFolderId();
    }
    
/**
<documentation><description><p>Returns <code>cssClasses</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getCssClasses() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getCssClasses()
    {
        return $this->getProperty()->cssClasses;
    }
    
/**
<documentation><description><p>Returns <code>cssFileId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getCssFileId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getCssFileId()
    {
        return $this->getProperty()->cssFileId;
    }

/**
<documentation><description><p>Returns <code>cssFilePath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getCssFilePath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getCssFilePath()
    {
        return $this->getProperty()->cssFilePath;
    }

/**
<documentation><description><p>Returns <code>cssFileRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getCssFileRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getCssFileRecycled() : bool
    {
        return $this->getProperty()->cssFileRecycled;
    }

/**
<documentation><description><p>Returns <code>defaultMetadataSetId</code>.</p></description>
<example>echo $s->getDefaultMetadataSetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultMetadataSetId() : string
    {
        return $this->getProperty()->defaultMetadataSetId;
    }
    
/**
<documentation><description><p>Returns <code>defaultMetadataSetPath</code>.</p></description>
<example>echo $s->getDefaultMetadataSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultMetadataSetPath() : string
    {
        return $this->getProperty()->defaultMetadataSetPath;
    }
    
/**
<documentation><description><p>Returns <code>externalLinkCheckOnPublish</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getExternalLinkCheckOnPublish() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getExternalLinkCheckOnPublish() : bool
    {
        return $this->getProperty()->externalLinkCheckOnPublish;
    }
    
/**
<documentation><description><p>Returns <code>linkCheckerEnabled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getLinkCheckerEnabled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getLinkCheckerEnabled() : bool
    {
        return $this->getProperty()->linkCheckerEnabled;
    }
    
/**
<documentation><description><p>Returns <code>recycleBinExpiration</code>.</p></description>
<example>echo $s->getRecycleBinExpiration(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRecycleBinExpiration() : string
    {
        return $this->getProperty()->recycleBinExpiration;
    }
    
/**
<documentation><description><p>Returns an <code>AssetFactoryContainer</code> object
representing the root asset factory container.</p></description>
<example>$afc = $s->getRootAssetFactoryContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainer() : Asset
    {
        if( is_null( $this->root_asset_factory_container ) )
        {
            $this->root_asset_factory_container = 
                Asset::getAsset(
            		$this->getService(),
            		AssetFactoryContainer::TYPE, 
            		$this->getProperty()->rootAssetFactoryContainerId );
        }
        return $this->root_asset_factory_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root asset factory container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootAssetFactoryContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainerAssetTree() : AssetTree
    {
        return $this->getRootAssetFactoryContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootAssetFactoryContainerId</code>.</p></description>
<example>echo $s->getRootAssetFactoryContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainerId() : string
    {
        return $this->getProperty()->rootAssetFactoryContainerId;
    }
    
/**
<documentation><description><p>An alias of <code>getAssetTree</code>.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetTree() : AssetTree
    {
        return $this->getAssetTree();
    }
    
/**
<documentation><description><p>Returns an <code>ConnectorContainer</code> object representing the root connector container.</p></description>
<example>$cc = $s->getRootConnectorContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainer() : Asset
    {
        if( is_null( $this->root_connector_container ) )
        {
            $this->root_connector_container = 
                Asset::getAsset(
            		$this->getService(),
            		ConnectorContainer::TYPE, 
            		$this->getProperty()->rootConnectorContainerId );
        }
        return $this->root_connector_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root connector container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootConnectorContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainerAssetTree() : AssetTree
    {
        return $this->getRootConnectorContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootConnectorContainerId</code>.</p></description>
<example>echo $s->getRootConnectorContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainerId() : string
    {
        return $this->getProperty()->rootConnectorContainerId;
    }
    
/**
<documentation><description><p>Returns an <code>ContentTypeContainer</code> object representing the root content type container.</p></description>
<example>$ctc = $s->getRootContentTypeContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainer() : Asset
    {
        if( is_null( $this->root_content_type_container ) )
        {
            $this->root_content_type_container = 
                Asset::getAsset(
            		$this->getService(),
            		ContentTypeContainer::TYPE, 
            		$this->getProperty()->rootContentTypeContainerId );
        }
        return $this->root_content_type_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root content type container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootContentTypeContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainerAssetTree() : AssetTree
    {
        return $this->getRootContentTypeContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootContentTypeContainerId</code>.</p></description>
<example>echo $s->getRootContentTypeContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainerId() : string
    {
        return $this->getProperty()->rootContentTypeContainerId;
    }
    
/**
<documentation><description><p>Returns an <code>DataDefinitionContainer</code> object representing the root data definition container.</p></description>
<example>$ddc = $s->getRootDataDefinitionContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainer() : Asset
    {
        if( is_null( $this->root_data_definition_container ) )
        {
            $this->root_data_definition_container = 
                Asset::getAsset(
            		$this->getService(),
            		DataDefinitionContainer::TYPE, 
            		$this->getProperty()->rootDataDefinitionContainerId );
        }
        return $this->root_data_definition_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root data definition container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootDataDefinitionContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainerAssetTree() : AssetTree
    {
        return $this->getRootDataDefinitionContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootDataDefinitionContainerId</code>.</p></description>
<example>echo $s->getRootDataDefinitionContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainerId() : string
    {
        return $this->getProperty()->rootDataDefinitionContainerId;
    }
  
/**
<documentation><description><p>An alias of <code>getBaseFolder</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolder() : Asset
    {
        return $this->getBaseFolder();
    }
  
/**
<documentation><description><p>An alias of <code>getAssetTree</code>.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolderAssetTree() : AssetTree
    {
        return $this->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootFolderId</code>.</p></description>
<example>echo $s->getRootFolderId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolderId() : string
    {
        return $this->getProperty()->rootFolderId;
    }
    
/**
<documentation><description><p>Returns an <code>MetadataSetContainer</code> object representing the root metadata set container.</p></description>
<example>$mdc = $s->getRootMetadataSetContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainer() : Asset
    {
        if( is_null( $this->root_metadata_set_container ) )
        {
            $this->root_metadata_set_container = 
                Asset::getAsset(
            		$this->getService(),
            		MetadataSetContainer::TYPE, 
            		$this->getProperty()->rootMetadataSetContainerId );
        }
        return $this->root_metadata_set_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root metadata set container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootMetadataSetContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainerAssetTree() : AssetTree
    {
        return $this->getRootMetadataSetContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootMetadataSetContainerId</code>.</p></description>
<example>echo $s->getRootMetadataSetContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainerId() : string
    {
        return $this->getProperty()->rootMetadataSetContainerId;
    }
    
/**
<documentation><description><p>Returns a <code>PageConfigurationSetContainer</code> object representing the root page configuration set container.</p></description>
<example>$pcsc = $s->getRootPageConfigurationSetContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainer() : Asset
    {
        if( is_null( $this->root_page_configuration_set_container ) )
        {
            $this->root_page_configuration_set_container = 
                Asset::getAsset(
            		$this->getService(),
            		PageConfigurationSetContainer::TYPE, 
            		$this->getProperty()->rootPageConfigurationSetContainerId );
        }
        return $this->root_page_configuration_set_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root page configuration set container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootPageConfigurationSetContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainerAssetTree() : AssetTree
    {
        return $this->getRootPageConfigurationSetContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootPageConfigurationSetContainerId</code>.</p></description>
<example>echo $s->getRootPageConfigurationSetContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainerId() : string
    {
        return $this->getProperty()->rootPageConfigurationSetContainerId;
    }
    
/**
<documentation><description><p>Returns a <code>PublishSetContainer</code> object representing the root publish set container.</p></description>
<example>$psc = $s->getRootPublishSetContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainer() : Asset
    {
        if( is_null( $this->root_publish_set_container ) )
        {
            $this->root_publish_set_container = 
                Asset::getAsset(
            		$this->getService(),
            		PublishSetContainer::TYPE, 
            		$this->getProperty()->rootPublishSetContainerId );
        }
        return $this->root_publish_set_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root publish set container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootPublishSetContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainerAssetTree() : AssetTree
    {
        return $this->getRootPublishSetContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootPublishSetContainerId</code>.</p></description>
<example>echo $s->getRootPublishSetContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainerId() : string
    {
        return $this->getProperty()->rootPublishSetContainerId;
    }
    
/**
<documentation><description><p>Returns a <code>SiteDestinationContainer</code> object representing the root site destination container.</p></description>
<example>$sdc = $s->getRootSiteDestinationContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainer() : Asset
    {
        if( is_null( $this->root_site_destination_container ) )
        {
            $this->root_site_destination_container = 
                Asset::getAsset(
            		$this->getService(),
            		SiteDestinationContainer::TYPE, 
            		$this->getProperty()->rootSiteDestinationContainerId );
        }
        return $this->root_site_destination_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root site destination container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootSiteDestinationContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainerAssetTree() : AssetTree
    {
        return $this->getRootSiteDestinationContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootSiteDestinationContainerId</code>.</p></description>
<example>echo $s->getRootSiteDestinationContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainerId() : string
    {
        return $this->getProperty()->rootSiteDestinationContainerId;
    }
    
/**
<documentation><description><p>Returns a <code>TransportContainer</code> object representing the root transport container.</p></description>
<example>$tc = $s->getRootTransportContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainer() : Asset
    {
        if( is_null( $this->root_transport_container ) )
        {
            $this->root_transport_container = 
                Asset::getAsset(
            		$this->getService(),
            		TransportContainer::TYPE, 
            		$this->getProperty()->rootTransportContainerId );
        }
        return $this->root_transport_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root transport container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootTransportContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainerAssetTree() : AssetTree
    {
        return $this->getRootTransportContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootTransportContainerId</code>.</p></description>
<example>echo $s->getRootTransportContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainerId() : string
    {
        return $this->getProperty()->rootTransportContainerId;
    }
    
/**
<documentation><description><p>Returns a <code>WorkflowDefinitionContainer</code> object representing the root workflow definition container.</p></description>
<example>$wdc = $s->getRootWorkflowDefinitionContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainer() : Asset
    {
        if( is_null( $this->root_workflow_definition_container ) )
        {
            $this->root_workflow_definition_container = 
                Asset::getAsset(
            		$this->getService(),
            		WorkflowDefinitionContainer::TYPE, 
            		$this->getProperty()->rootWorkflowDefinitionContainerId );
        }
        return $this->root_workflow_definition_container;
    }
    
/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root workflow definition container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootWorkflowDefinitionContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainerAssetTree() : AssetTree
    {
        return $this->getRootWorkflowDefinitionContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>rootWorkflowDefinitionContainerId</code>.</p></description>
<example>echo $s->getRootWorkflowDefinitionContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainerId() : string
    {
        return $this->getProperty()->rootWorkflowDefinitionContainerId;
    }
    
/**
<documentation><description><p>Returns <code>scheduledPublishDestinationMode</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $s->getScheduledPublishDestinationMode() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledPublishDestinationMode()
    {
        // all-destinations or selected-destinations
        return $this->getProperty()->scheduledPublishDestinationMode;
    }
    
/**
<documentation><description><p>Returns <code>scheduledPublishDestinations</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $s->getScheduledPublishDestinations() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledPublishDestinations()
    {
        return $this->getProperty()->scheduledPublishDestinations;
    }
    
/**
<documentation><description><p>Returns an <code>AssetFactoryContainer</code> object representing the site asset factory container.</p></description>
<example>$safc = $s->getSiteAssetFactoryContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainer() : Asset
    {
        if( is_null( $this->root_site_asset_factory_container ) )
        {
            $this->root_site_asset_factory_container = 
                Asset::getAsset(
            		$this->getService(),
            		AssetFactoryContainer::TYPE, 
            		$this->getProperty()->siteAssetFactoryContainerId );
        }
        return $this->root_site_asset_factory_container;
    }
    
/**
<documentation><description><p>Return an <code>AssetTree</code> object rooted at the site asset factory container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getSiteAssetFactoryContainer()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerAssetTree() : AssetTree
    {
        return $this->getSiteAssetFactoryContainer()->getAssetTree();
    }
    
/**
<documentation><description><p>Returns <code>siteAssetFactoryContainerId</code>.</p></description>
<example>echo $s->getSiteAssetFactoryContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerId() : string
    {
        return $this->getProperty()->siteAssetFactoryContainerId;
    }
    
/**
<documentation><description><p>Returns <code>siteAssetFactoryContainerPath</code>.</p></description>
<example>echo $s->getSiteAssetFactoryContainerPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerPath() : string
    {
        return $this->getProperty()->siteAssetFactoryContainerPath;
    }
    
/**
<documentation><description><p>Returns <code>siteStartingPageId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getSiteStartingPageId() ), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPageId()
    {
        return $this->getProperty()->siteStartingPageId;
    }
    
/**
<documentation><description><p>Returns <code>siteStartingPagePath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getSiteStartingPagePath() ), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPagePath()
    {
        return $this->getProperty()->siteStartingPagePath;
    }
    
/**
<documentation><description><p>Returns <code>siteStartingPageRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getSiteStartingPageRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPageRecycled() : bool
    {
        return $this->getProperty()->siteStartingPageRecycled;
    }
    
/**
<documentation><description><p>Returns <code>unpublishOnExpiration</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getUnpublishOnExpiration() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getUnpublishOnExpiration() : bool
    {
        return $this->getProperty()->unpublishOnExpiration;
    }
    
/**
<documentation><description><p>Returns <code>url</code>.</p></description>
<example>echo $s->getUrl(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getUrl() : string
    {
        return $this->getProperty()->url;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named role is associated with the site.</p></description>
<example>echo u\StringUtility::boolToString( $s->hasRole(
    $cascade->getAsset( a\Role::TYPE, 5 ) ) ), BR;</example>
<return-type>bool</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function hasRole( Role $r ) : bool
    {
        if( $r == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ROLE . E_SPAN );
        
        $role_name = $r->getName();
        
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() == $role_name )
            {
                return true;
            }
        }
        return false;
    }

/**
<documentation><description><p>Publishes the site and returns the calling object.</p></description>
<example>$s->publish();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL ) : Asset
    {
        if( isset( $destination ) )
        {
            $destination_std       = new \stdClass();
            $destination_std->id   = $destination->getId();
            $destination_std->type = $destination->getType();
        }
        
        $service = $this->getService();
        
        if( isset( $destination ) )
            $service->publish( 
                $service->createId(
                    self::TYPE, $this->getProperty()->id ), $destination_std );
        else
            $service->publish( 
                $service->createId( self::TYPE, $this->getProperty()->id ) );
        return $this;
    }
    
/**
<documentation><description><p>Removes the role from <code>roleAssignments</code>, and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function removeRole( Role $r ) : Asset
    {
        if( $r == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ROLE . E_SPAN );
        
        if( !$this->hasRole( $r ) )
        {
            return $this;
        }
        
        $role_name = $r->getName();
        
        $temp = array();
        
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() != $role_name )
            {
                $temp[] = $assignment;
            }
        }
        
        $this->role_assignments = $temp;
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>cssClasses</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function setCssClasses( string $classes ) : Asset
    {
        if( trim( $classes ) == "" || $class == NULL )
        {
            $this->getProperty()->cssClasses = NULL;
            return $this;
        }
        
        if( $this->getProperty()->cssFileId == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . "The CSS file must be set first." . E_SPAN );
        }
        
        $this->getProperty()->cssClasses = $classes;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>cssFileId</code> and <code>cssFilePath</code> and returns the calling object.</p></description>
<example>$s->setCssFile(
    $cascade->getAsset( a\File::TYPE,  '4007ae9d8b7f08560139425c99384b99' ), 
    'leftobject,rightobject,center,centerobject' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setCssFile( File $css=NULL, string $classes=NULL ) : Asset
    {
        if( $css == NULL )
        {
            $this->getProperty()->cssClasses  = NULL;
            $this->getProperty()->cssFileId   = NULL;
            $this->getProperty()->cssFilePath = NULL;
            return $this;
        }
        $this->getProperty()->cssClasses  = $classes;
        $this->getProperty()->cssFileId   = $css->getId();
        $this->getProperty()->cssFilePath = $css->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets the default metadata set and returns the calling object.</p></description>
<example>$s->setDefaultMetadataSet( 
    $cascade->getAsset( a\MetadataSet::TYPE, '1f22ac858b7ffe834c5fe91e67ea0fcf' )
)->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function setDefaultMetadataSet( MetadataSet $m ) : Asset
    {
        if( $m == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_METADATA_SET . E_SPAN );
        }
        $this->getProperty()->defaultMetadataSetId   = $m->getId();
        $this->getProperty()->defaultMetadataSetPath = $m->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>externalLinkCheckOnPublish</code> and returns the calling object.</p></description>
<example>$s->setExternalLinkCheckOnPublish( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setExternalLinkCheckOnPublish( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->externalLinkCheckOnPublish = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>linkCheckerEnabled</code> and returns the calling object.</p></description>
<example>$s->setLinkCheckerEnabled( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setLinkCheckerEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->linkCheckerEnabled = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>getRecycleBinExpiration</code> and returns the calling object.</p></description>
<example>$s->setRecycleBinExpiration( a\Site::NEVER )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setRecycleBinExpiration( string $e ) : Asset
    {
        if( $e != self::NEVER && $e != self::ONE && 
            $e != self::FIFTEEN && $e != self::THIRTY
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "Unacceptable value: $e" . E_SPAN );
        }
        $this->getProperty()->recycleBinExpiration = $e;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>siteAssetFactoryContainerId</code> and <code>siteAssetFactoryContainerPath</code> and returns the calling object.</p></description>
<example>$s->setSiteAssetFactoryContainer( 
    $cascade->getAsset( a\AssetFactoryContainer::TYPE,
    '1f217d838b7ffe834c5fe91e9832f910' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setSiteAssetFactoryContainer( AssetFactoryContainer $a=NULL ) : Asset
    {
        if( $a == NULL )
        {
            $this->getProperty()->siteAssetFactoryContainerId   = NULL;
            $this->getProperty()->siteAssetFactoryContainerPath = NULL;
            return $this;
        }
        $this->getProperty()->siteAssetFactoryContainerId   = $a->getId();
        $this->getProperty()->siteAssetFactoryContainerPath = $a->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>siteStartingPageId</code> and <code>siteStartingPagePath</code> and returns the calling object.</p></description>
<example>$s->setStartingPage( 
    $cascade->getAsset( a\Page::TYPE, 
    '1f2376798b7ffe834c5fe91ead588ce1' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setStartingPage( Page $p=NULL ) : Asset
    {
        if( $p == NULL )
        {
            $this->getProperty()->siteStartingPageId   = NULL;
            $this->getProperty()->siteStartingPagePath = NULL;
            return $this;
        }
        $this->getProperty()->siteStartingPageId   = $p->getId();
        $this->getProperty()->siteStartingPagePath = $p->getPath();
        return $this;
    }
    
/**
<documentation><description><p>ets <code>unpublishOnExpiration</code> and returns the calling object.</p></description>
<example>$s->setUnpublishOnExpiration( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setUnpublishOnExpiration( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->unpublishOnExpiration = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>url</code> and returns the calling object.</p></description>
<example>$s->setUrl( 'http://www.upstate.edu/tuw-test' )->edit();</example>
<return-type>string </return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setUrl( string $u ) : Asset
    {
        if( trim( $u ) == "" )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
        }
        
        $this->getProperty()->url = $u;
        return $this;
    }

    private function processRoleAssignments()
    {
        $this->role_assignments = array();
        
        if( isset( $this->getProperty()->roleAssignments ) && 
            isset( $this->getProperty()->roleAssignments->roleAssignment ) )
            $ra = $this->getProperty()->roleAssignments->roleAssignment;
        
        if( isset( $ra ) )
        {
            if( !is_array( $ra ) )
            {
                $ra = array( $ra );
            }
            
            foreach( $ra as $role_assignment )
            {
                $this->role_assignments[] = new p\RoleAssignment( $role_assignment );
            }
        }
    }
    
    private $role_assignments;
    private $base_folder;
    private $root_asset_factory_container;
    private $root_connector_container;
    private $root_content_type_container;
    private $root_data_definition_container;
    private $root_metadata_set_container;
    private $root_page_configuration_set_container;
    private $root_publish_set_container;
    private $root_site_destination_container;
    private $root_transport_container;
    private $root_workflow_definition_container;
    private $root_site_asset_factory_container;
}
?>