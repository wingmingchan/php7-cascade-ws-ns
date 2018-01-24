<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/28/2017 Replaced static WSDL code with call to getXMLFragments.
  * Removed JSON dump. Updated documentation.
  * 1/24/2018 Updated documentation.
  * 6/12/2017 Added WSDL.
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
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>Role</code> object represents a role asset.</p>
<p>There are two types of roles in Cascade: global and site. In a <code>role</code> property, there are two sub-properties: <code>globalAbilities</code> and <code>siteAbilities</code>. For a global role, the <code>siteAbilities</code> property stores a <code>NULL</code> value. For a site role, the <code>globalAbilities</code> property stores a <code>NULL</code> value. Corresponding to these two properties, there are two classes: <a href=\"http://www.upstate.edu/web-services/api/property-classes/global-abilities.php\"><code>p\GlobalAbilities</code></a> and <a href=\"http://www.upstate.edu/web-services/api/property-classes/site-abilities.php\"><code>p\SiteAbilities</code></a>. A <code>Role</code> object has a <code>p\GlobalAbilities</code> object and a <code>p\SiteAbilities</code> object.</p>
<h2>Structure of <code>role</code></h2>
<pre>role
  id
  name
  roleType
  globalAbilities
    (37 properties, v.8.8)
  siteAbilities
    (52 properties, v.8.8)
</pre>
<h2>Design Issues</h2>
<p>Since there are too many methods (84 <code>get</code> and 84 <code>set</code> methods) involved here, I decide not to repeat these methods in various classes. Instead, I provide two <code>get</code> methods, i.e., <code>getGlobalAbilities()</code> and <code>getSiteAbilities()</code> in this class, returning a <code>GlobalAbilities</code> object and a <code>SiteAbilities</code> object respectively, allowing us to manipulate these two objects directly. Therefore, there are no <code>set</code> methods in this class.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "role" ),
        array( "getSimpleTypeXMLByName"  => "role-types" ),
        array( "getComplexTypeXMLByName" => "global-abilities" ),
        array( "getComplexTypeXMLByName" => "site-abilities" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/role/10

{
  "asset":{
    "role":{
      "roleType":"global",
      "globalAbilities":{
        "bypassAllPermissionsChecks":false,
        "editAccessRights":false,
        "accessAudits":false,
        "accessSiteManagement":false,
        "createSites":false,
        "accessAllSites":true,
        "viewSystemInfoAndLogs":false,
        "forceLogout":false,
        "diagnosticTests":false,
        "accessSecurityArea":false,
        "optimizeDatabase":false,
        "syncLdap":false,
        "configureLogging":false,
        "searchingIndexing":false,
        "accessConfiguration":false,
        "editSystemPreferences":false,
        "broadcastMessages":false,
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
        "accessDefaultEditorConfiguration":false,
        "databaseExportTool":false,
        "modifyDictionary":false
      },
      "name":"Default",
      "id":"10"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}

{
  "asset":{
    "role":{
      "roleType":"site",
      "siteAbilities":{
        "bypassAllPermissionsChecks":true,
        "uploadImagesFromWysiwyg":true,
        "multiSelectCopy":true,
        "multiSelectPublish":true,
        "multiSelectMove":true,
        "multiSelectDelete":true,
        "editPageLevelConfigurations":true,
        "editPageContentType":true,
        "editDataDefinition":true,
        "publishReadableHomeAssets":true,
        "publishWritableHomeAssets":true,
        "editAccessRights":true,
        "viewVersions":true,
        "activateDeleteVersions":true,
        "accessAudits":true,
        "bypassWorkflow":true,
        "assignApproveWorkflowSteps":true,
        "deleteWorkflows":true,
        "breakLocks":true,
        "assignWorkflowsToFolders":true,
        "bypassAssetFactoryGroupsNewMenu":true,
        "bypassDestinationGroupsWhenPublishing":true,
        "bypassWorkflowDefintionGroupsForFolders":true,
        "accessManageSiteArea":true,
        "accessAssetFactories":true,
        "accessConfigurationSets":true,
        "accessDataDefinitions":true,
        "accessMetadataSets":true,
        "accessPublishSets":true,
        "accessDestinations":true,
        "accessTransports":true,
        "accessWorkflowDefinitions":true,
        "accessContentTypes":true,
        "accessConnectors":true,
        "publishReadableAdminAreaAssets":true,
        "publishWritableAdminAreaAssets":true,
        "importZipArchive":true,
        "bulkChange":true,
        "recycleBinViewRestoreUserAssets":true,
        "recycleBinDeleteAssets":true,
        "recycleBinViewRestoreAllAssets":true,
        "moveRenameAssets":true,
        "diagnosticTests":true,
        "alwaysAllowedToToggleDataChecks":true,
        "viewPublishQueue":true,
        "reorderPublishQueue":true,
        "cancelPublishJobs":true,
        "sendStaleAssetNotifications":true,
        "brokenLinkReportAccess":false,
        "brokenLinkReportMarkFixed":false,
        "bypassWysiwygEditorRestrictions":true,
        "accessEditorConfigurations":true
      },
      "name":"My Test Site Role",
      "id":"210"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre>
<h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/role.php">role.php</a></li></ul>
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
        $asset = new \stdClass();
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
        if( isset( $this->getProperty()->roleType ) )
            return $this->getProperty()->roleType;
        return NULL;
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
