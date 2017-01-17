<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/17/2017 Added JSON dump.
  * 5/28/2015 Added namespaces.
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
<p>A <code>Role</code> object represents a role asset.</p>
<p>There are two types of roles in Cascade: global and site. In a <code>role</code> property, there are two sub-properties: <code>globalAbilities</code> and <code>siteAbilities</code>. For a global role, the <code>siteAbilities</code> property stores a <code>NULL</code> value. For a site role, the <code>globalAbilities</code> property stores a <code>NULL</code> value. Corresponding to these two properties, there are two classes: <a href="site://cascade-admin-old/projects/web-services/oop/classes/property-classes/global-abilities"><code>p\GlobalAbilities</code></a> and <a href="site://cascade-admin-old/projects/web-services/oop/classes/property-classes/site-abilities"><code>p\SiteAbilities</code></a>. These two classes are sub-classes of the <a href="site://cascade-admin-old/projects/web-services/oop/classes/property-classes/abilities"><code>p\Abilities</code></a> class. A <code>Role</code> object has a <code>p\GlobalAbilities</code> object and a <code>p\SiteAbilities</code> object.</p>
<h2>Structure of <code>role</code></h2>
<pre>role
  id
  name
  roleType
  globalAbilities
    (82 properties, v.8)
  siteAbilities
    (51 properties)
</pre>
<h2>Design Issues</h2>
<p>Since there are too many methods (85 <code>get</code> and 85 <code>set</code> methods) involved here, I decide not to repeat these methods in various classes. Instead, I provide two <code>get</code> methods, i.e., <code>getGlobalAbilities()</code> and <code>getSiteAbilities()</code> in this class, each returning an <code>Abilities</code> object, allowing us to manipulate these two objects directly. Therefore, there are no <code>set</code> methods in this class.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/role.php">role.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{ "asset":{
  "role":{
    "roleType":"global",
    "globalAbilities":{
      "bypassAllPermissionsChecks":false,
      "accessSiteManagement":false,
      "createSites":false,
      "uploadImagesFromWysiwyg":false,
      "multiSelectCopy":false,
      "multiSelectPublish":false,
      "multiSelectMove":false,
      "multiSelectDelete":false,
      "editPageLevelConfigurations":false,
      "editPageContentType":false,
      "editDataDefinition":false,
      "publishReadableHomeAssets":false,
      "publishWritableHomeAssets":false,
      "viewPublishQueue":true,
      "reorderPublishQueue":false,
      "cancelPublishJobs":false,
      "editAccessRights":false,
      "viewVersions":false,
      "activateDeleteVersions":false,
      "accessAudits":false,
      "bypassWorkflow":false,
      "assignApproveWorkflowSteps":false,
      "deleteWorkflows":false,
      "breakLocks":false,
      "assignWorkflowsToFolders":false,
      "bypassAssetFactoryGroupsNewMenu":false,
      "bypassDestinationGroupsWhenPublishing":false,
      "bypassWorkflowDefintionGroupsForFolders":false,
      "alwaysAllowedToToggleDataChecks":false,
      "accessManageSiteArea":false,
      "accessAdminArea":false,
      "accessAssetFactories":false,
      "accessConfigurationSets":false,
      "accessDataDefinitions":false,
      "accessMetadataSets":false,
      "accessPublishSets":false,
      "accessTargetsDestinations":false,
      "accessTransports":false,
      "accessWorkflowDefinitions":false,
      "accessContentTypes":false,
      "accessAllSites":true,
      "viewSystemInfoAndLogs":false,
      "forceLogout":false,
      "diagnosticTests":false,
      "accessSecurityArea":false,
      "publishReadableAdminAreaAssets":false,
      "publishWritableAdminAreaAssets":false,
      "integrateFolder":false,
      "importZipArchive":false,
      "optimizeDatabase":false,
      "syncLdap":false,
      "bulkChange":false,
      "configureLogging":false,
      "searchingIndexing":false,
      "accessConfiguration":false,
      "editSystemPreferences":false,
      "broadcastMessages":false,
      "recycleBinViewRestoreUserAssets":false,
      "recycleBinDeleteAssets":false,
      "recycleBinViewRestoreAllAssets":false,
      "viewUsersInMemberGroups":false,
      "viewAllUsers":false,
      "createUsers":false,
      "deleteUsersInMemberGroups":false,
      "deleteAllUsers":false,
      "viewMemberGroups":false,
      "viewAllGroups":false,
      "createGroups":false,
      "deleteMemberGroups":false,
      "accessRoles":false,
      "createRoles":false,
      "deleteAnyGroup":false,
      "editAnyUser":false,
      "editUsersInMemberGroups":false,
      "editAnyGroup":false,
      "editMemberGroups":false,
      "changeIdentity":false,
      "moveRenameAssets":false,
      "databaseExportTool":false,
      "sendStaleAssetNotifications":false,
      "brokenLinkReportAccess":false,
      "brokenLinkReportMarkFixed":false },
    "name":"Default",
    "id":"10" } },
  "success":true
}

{ "asset":{
  "role":{
    "roleType":"site",
    "siteAbilities":{
      "bypassAllPermissionsChecks":false,
      "uploadImagesFromWysiwyg":false,
      "multiSelectCopy":true,
      "multiSelectPublish":true,
      "multiSelectMove":true,
      "multiSelectDelete":true,
      "editPageLevelConfigurations":true,
      "editPageContentType":true,
      "editDataDefinition":true,
      "publishReadableHomeAssets":true,
      "publishWritableHomeAssets":true,
      "editAccessRights":false,
      "viewVersions":true,
      "activateDeleteVersions":true,
      "accessAudits":true,
      "bypassWorkflow":true,
      "assignApproveWorkflowSteps":true,
      "deleteWorkflows":true,
      "breakLocks":true,
      "assignWorkflowsToFolders":true,
      "bypassAssetFactoryGroupsNewMenu":true,
      "bypassDestinationGroupsWhenPublishing":false,
      "bypassWorkflowDefintionGroupsForFolders":true,
      "accessManageSiteArea":true,
      "accessAssetFactories":true,
      "accessConfigurationSets":false,
      "accessDataDefinitions":true,
      "accessMetadataSets":true,
      "accessPublishSets":true,
      "accessDestinations":false,
      "accessTransports":false,
      "accessWorkflowDefinitions":true,
      "accessContentTypes":false,
      "accessConnectors":true,
      "publishReadableAdminAreaAssets":true,
      "publishWritableAdminAreaAssets":false,
      "integrateFolder":true,
      "importZipArchive":true,
      "bulkChange":true,
      "recycleBinViewRestoreUserAssets":true,
      "recycleBinDeleteAssets":true,
      "recycleBinViewRestoreAllAssets":true,
      "moveRenameAssets":true,
      "diagnosticTests":false,
      "alwaysAllowedToToggleDataChecks":true,
      "viewPublishQueue":true,
      "reorderPublishQueue":true,
      "cancelPublishJobs":true,
      "sendStaleAssetNotifications":true,
      "brokenLinkReportAccess":false,
      "brokenLinkReportMarkFixed":false },
    "name":"Library-Site-Administrator",
    "id":"131" } },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class Role extends Asset
{
    const DEBUG = false;
    const TYPE  = c\T::ROLE;
    
/**
<documentation><description><p>The constructor, overriding the parent method to process the abilities.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct(
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        if( isset( $this->getProperty()->globalAbilities ) )
            $this->global_abilities = new p\GlobalAbilities(
                $this->getProperty()->globalAbilities );
        else
            $this->global_abilities = NULL;
            
        if( isset( $this->getProperty()->siteAbilities ) )
            $this->site_abilities   = new p\SiteAbilities(
                $this->getProperty()->siteAbilities );
        else
            $this->site_abilities = NULL;
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
        $asset                       = new \stdClass();
        if( isset( $this->global_abilities ) )
            $this->getProperty()->globalAbilities = $this->global_abilities->toStdClass();
        else
            $this->getProperty()->globalAbilities = NULL;
            
        if( isset( $this->site_abilities ) )
            $this->getProperty()->siteAbilities = $this->site_abilities->toStdClass();
        else
            $this->getProperty()->siteAbilities = NULL;
            
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
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
<documentation><description><p>Returns the <code>p\GlobalAbilities</code> object or <code>NULL</code>.</p></description>
<example>$ga = $r->getGlobalAbilities();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGlobalAbilities()
    {
        return $this->global_abilities;
    }
    
/**
<documentation><description><p>Returns <code>roleType</code>.</p></description>
<example>echo $r->getRoleType(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getRoleType()
    {
        return $this->getProperty()->roleType;
    }
    
/**
<documentation><description><p>Returns the <code>p\SiteAbilities</code> object or <code>NULL</code>.</p></description>
<example>$sa = $r->getSiteAbilities();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAbilities()
    {
        return $this->site_abilities;
    }
    
    private $global_abilities;
    private $site_abilities;
}
?>
