<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/11/2018 Updated up to 8.8. modifyDictionary not available yet.
  * 6/29/2017 Rewrote code for 8.4.1.
  * 6/12/2017 Added WSDL.
  * 9/7/2016 Added accessAdminArea.
  * 9/6/2016 Removed newSiteWizard, siteMigration, recycleBinChecker, pathRepairTool.
  * 12/29/2015 Added member and methods for changeIdentity.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

/**
<documentation>
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>GlobalAbilities</code> object represents the <code>globalAbilities</code> property found in a role asset. As of Cascade 8.8, there are 37 of them.</p>
<h2>Properties of <code>globalAbilities</code> (Sorted)</h2>
<pre>accessAllSites
accessAudits
accessConfiguration
accessDefaultEditorConfiguration
accessRoles
accessSecurityArea
accessSiteManagement
broadcastMessages
bypassAllPermissionsChecks
changeIdentity
configureLogging
createGroups
createRoles
createSites
createUsers
databaseExportTool
deleteAllUsers
deleteAnyGroup
deleteMemberGroups
deleteUsersInMemberGroups
diagnosticTests
editAccessRights
editAnyGroup
editAnyUser
editMemberGroups
editSystemPreferences
editUsersInMemberGroups
forceLogout
modifyDictionary
optimizeDatabase
searchingIndexing
syncLdap
viewAllGroups
viewAllUsers
viewMemberGroups
viewSystemInfoAndLogs
viewUsersInMemberGroups
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "global-abilities" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class GlobalAbilities extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        \stdClass $a=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $a ) )
        {
            if( isset( $a->accessAllSites ) )
                $this->access_all_sites              = $a->accessAllSites;
            if( isset( $a->accessAudits ) )
                $this->access_audits                 = $a->accessAudits;
            if( isset( $a->accessConfiguration ) )
                $this->access_configuration          = $a->accessConfiguration;
            if( isset( $a->accessDefaultEditorConfiguration ) )
                $this->access_default_editor_configuration =
                    $a->accessDefaultEditorConfiguration;
            if( isset( $a->accessRoles ) )
                $this->access_roles                  = $a->accessRoles;
            if( isset( $a->accessSecurityArea ) )
                $this->access_security_area          = $a->accessSecurityArea;
            if( isset( $a->accessSiteManagement ) )
                $this->access_site_management        = $a->accessSiteManagement;
            if( isset( $a->broadcastMessages ) )
                $this->broadcast_messages            = $a->broadcastMessages;
            if( isset( $a->bypassAllPermissionsChecks ) )
                $this->bypass_all_permissions_checks = $a->bypassAllPermissionsChecks;
            if( isset( $a->changeIdentity ) )
                $this->change_identity               = $a->changeIdentity;
            if( isset( $a->configureLogging ) )
                $this->configure_logging             = $a->configureLogging;
            if( isset( $a->createGroups ) )
                $this->create_groups                 = $a->createGroups;
            if( isset( $a->createRoles ) )
                $this->create_roles                  = $a->createRoles;
            if( isset( $a->createSites ) )
                $this->create_sites                  = $a->createSites;
            if( isset( $a->createUsers ) )
                $this->create_users                  = $a->createUsers;
            if( isset( $a->databaseExportTool ) )
                $this->database_export_tool          = $a->databaseExportTool;
            if( isset( $a->deleteAllUsers ) )
                $this->delete_all_users              = $a->deleteAllUsers;
            if( isset( $a->deleteAnyGroup ) )
                $this->delete_any_group              = $a->deleteAnyGroup;
            if( isset( $a->deleteMemberGroups ) )
                $this->delete_member_groups          = $a->deleteMemberGroups;
            if( isset( $a->deleteUsersInMemberGroups ) )
                $this->delete_users_in_member_groups = $a->deleteUsersInMemberGroups;
            if( isset( $a->diagnosticTests ) )
                $this->diagnostic_tests              = $a->diagnosticTests;
            if( isset( $a->editAccessRights ) )
                $this->edit_access_rights            = $a->editAccessRights;
            if( isset( $a->editAnyGroup ) )
                $this->edit_any_group                = $a->editAnyGroup;
            if( isset( $a->editAnyUser ) )
                $this->edit_any_user                 = $a->editAnyUser;
            if( isset( $a->editMemberGroups ) )
                $this->edit_member_groups            = $a->editMemberGroups;
            if( isset( $a->editSystemPreferences ) )
                $this->edit_system_preferences       = $a->editSystemPreferences;
            if( isset( $a->editUsersInMemberGroups ) )
                $this->edit_users_in_member_groups   = $a->editUsersInMemberGroups;
            if( isset( $a->forceLogout ) )
                $this->force_logout                  = $a->forceLogout;
            if( isset( $a->optimizeDatabase ) )
                $this->modify_dictionary             = $a->modifyDictionary; // 8.8
            if( isset( $a->optimizeDatabase ) )
                $this->optimize_database             = $a->optimizeDatabase;
            if( isset( $a->searchingIndexing ) )
                $this->searching_indexing            = $a->searchingIndexing;
            if( isset( $a->syncLdap ) )
                $this->sync_ldap                     = $a->syncLdap;
            if( isset( $a->viewAllGroups ) )
                $this->view_all_groups               = $a->viewAllGroups;
            if( isset( $a->viewAllUsers ) )
                $this->view_all_users                = $a->viewAllUsers;
            if( isset( $a->viewMemberGroups ) )
                $this->view_member_groups            = $a->viewMemberGroups;
            if( isset( $a->viewSystemInfoAndLogs ) )
                $this->view_system_info_and_logs     = $a->viewSystemInfoAndLogs;
            if( isset( $a->viewUsersInMemberGroups ) )
                $this->view_users_in_member_groups   = $a->viewUsersInMemberGroups;
        }
    }
    
/**
<documentation><description><p>Returns <code>accessAllSites</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessAllSites() : bool
    {
        return $this->access_all_sites;
    }

/**
<documentation><description><p>Returns <code>accessAudits</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessAudits() : bool
    {
        return $this->access_audits;
    }

/**
<documentation><description><p>Returns <code>accessConfiguration</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessConfiguration() : bool
    {
        return $this->access_configuration;
    }

/**
<documentation><description><p>Returns <code>accessDefaultEditorConfiguration</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessDefaultEditorConfiguration() : bool
    {
        return $this->access_default_editor_configuration;
    }

/**
<documentation><description><p>Returns <code>accessRoles</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessRoles() : bool
    {
        return $this->access_roles;
    }

/**
<documentation><description><p>Returns <code>accessSecurityArea</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessSecurityArea() : bool
    {
        return $this->access_security_area;
    }

/**
<documentation><description><p>Returns <code>accessSiteManagement</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessSiteManagement() : bool
    {
        return $this->access_site_management;
    }

/**
<documentation><description><p>Returns <code>broadcastMessages</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getBroadcastMessages() : bool
    {
        return $this->broadcast_messages;
    }

/**
<documentation><description><p>Returns <code>bypassAllPermissionsChecks</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getBypassAllPermissionsChecks() : bool
    {
        return $this->bypass_all_permissions_checks;
    }

/**
<documentation><description><p>Returns <code>changeIdentity</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getChangeIdentity() : bool
    {
        return $this->change_identity;
    }

/**
<documentation><description><p>Returns <code>configureLogging</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getConfigureLogging() : bool
    {
        return $this->configure_logging;
    }

/**
<documentation><description><p>Returns <code>createGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getCreateGroups() : bool
    {
        return $this->create_groups;
    }

/**
<documentation><description><p>Returns <code>createRoles</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getCreateRoles() : bool
    {
        return $this->create_roles;
    }

/**
<documentation><description><p>Returns <code>createSites</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getCreateSites() : bool
    {
        return $this->create_sites;
    }

/**
<documentation><description><p>Returns <code>createUsers</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getCreateUsers() : bool
    {
        return $this->create_users;
    }

/**
<documentation><description><p>Returns <code>databaseExportTool</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getDatabaseExportTool() : bool
    {
        return $this->database_export_tool;
    }

/**
<documentation><description><p>Returns <code>deleteAllUsers</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getDeleteAllUsers() : bool
    {
        return $this->delete_all_users;
    }

/**
<documentation><description><p>Returns <code>deleteAnyGroup</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getDeleteAnyGroup() : bool
    {
        return $this->delete_any_group;
    }

/**
<documentation><description><p>Returns <code>deleteMemberGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getDeleteMemberGroups() : bool
    {
        return $this->delete_member_groups;
    }

/**
<documentation><description><p>Returns <code>deleteUsersInMemberGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getDeleteUsersInMemberGroups() : bool
    {
        return $this->delete_users_in_member_groups;
    }

/**
<documentation><description><p>Returns <code>diagnosticTests</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getDiagnosticTests() : bool
    {
        return $this->diagnostic_tests;
    }

/**
<documentation><description><p>Returns <code>editAccessRights</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessRights() : bool
    {
        return $this->edit_access_rights;
    }

/**
<documentation><description><p>Returns <code>editAnyGroup</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getEditAnyGroup() : bool
    {
        return $this->edit_any_group;
    }

/**
<documentation><description><p>Returns <code>editAnyUser</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getEditAnyUser() : bool
    {
        return $this->edit_any_user;
    }

/**
<documentation><description><p>Returns <code>editMemberGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getEditMemberGroups() : bool
    {
        return $this->edit_member_groups;
    }

/**
<documentation><description><p>Returns <code>editSystemPreferences</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getEditSystemPreferences() : bool
    {
        return $this->edit_system_preferences;
    }

/**
<documentation><description><p>Returns <code>editUsersInMemberGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getEditUsersInMemberGroups() : bool
    {
        return $this->edit_users_in_member_groups;
    }

/**
<documentation><description><p>Returns <code>forceLogout</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getForceLogout() : bool
    {
        return $this->force_logout;
    }

/**
<documentation><description><p>Returns <code>modifyDictionary</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getModifyDictionary() : bool
    {
        return $this->modify_dictionary;
    }

/**
<documentation><description><p>Returns <code>optimizeDatabase</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getOptimizeDatabase() : bool
    {
        return $this->optimize_database;
    }

/**
<documentation><description><p>Returns <code>searchingIndexing</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getSearchingIndexing() : bool
    {
        return $this->searching_indexing;
    }

/**
<documentation><description><p>Returns <code>syncLdap</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getSyncLdap() : bool
    {
        return $this->sync_ldap ;
    }

/**
<documentation><description><p>Returns <code>viewAllGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getViewAllGroups() : bool
    {
        return $this->view_all_groups;
    }

/**
<documentation><description><p>Returns <code>viewAllUsers</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getViewAllUsers() : bool
    {
        return $this->view_all_users;
    }

/**
<documentation><description><p>Returns <code>viewMemberGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getViewMemberGroups() : bool
    {
        return $this->view_member_groups;
    }

/**
<documentation><description><p>Returns <code>viewSystemInfoAndLogs</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getViewSystemInfoAndLogs() : bool
    {
        return $this->view_system_info_and_logs;
    }

/**
<documentation><description><p>Returns <code>viewUsersInMemberGroups</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getViewUsersInMemberGroups() : bool
    {
        return $this->view_users_in_member_groups;
    }

/**
<documentation><description><p>Sets <code>accessAllSites</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessAllSites( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_all_sites = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessAudits</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessAudits( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_audits = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessConfiguration</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessConfiguration( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_configuration = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessDefaultEditorConfiguration</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessDefaultEditorConfiguration( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_default_editor_configuration = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessRoles</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessRoles( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_roles = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessSecurityArea</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessSecurityArea( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_security_area = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessSiteManagement</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessSiteManagement( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_site_management = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>broadcastMessages</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setBroadcastMessages( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->broadcast_messages = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>bypassAllPermissionsChecks</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setBypassAllPermissionsChecks( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_all_permissions_checks = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>changeIdentity</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setChangeIdentity( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->change_identity = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>configureLogging</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setConfigureLogging( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->configure_logging = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>createGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setCreateGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->create_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>createRoles</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setCreateRoles( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->create_roles = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>createSites</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setCreateSites( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->create_sites = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>createUsers</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setCreateUsers( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->create_users = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>databaseExportTool</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDatabaseExportTool( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->database_export_tool = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>deleteAllUsers</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDeleteAllUsers( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->delete_all_users = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>deleteAnyGroup</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDeleteAnyGroup( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->delete_any_group = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>deleteMemberGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDeleteMemberGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->delete_member_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>deleteUsersInMemberGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDeleteUsersInMemberGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->delete_users_in_member_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>diagnosticTests</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDiagnosticTests( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->diagnostic_tests = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>editAccessRights</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEditAccessRights( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_access_rights = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>editAnyGroup</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEditAnyGroup( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_any_group = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>editAnyUser</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEditAnyUser( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_any_user = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>editMemberGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEditMemberGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_member_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>editSystemPreferences</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEditSystemPreferences( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_system_preferences = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>editUsersInMemberGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEditUsersInMemberGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_users_in_member_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>forceLogout</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setForceLogout( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->force_logout = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>modifyDictionary</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setModifyDictionary( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->modify_dictionary = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>optimizeDatabase</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setOptimizeDatabase( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->optimize_database = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>searchingIndexing</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSearchingIndexing( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->searching_indexing = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>syncLdap</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSyncLdap( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->sync_ldap  = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>viewAllGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setViewAllGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_all_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>viewAllUsers</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setViewAllUsers( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_all_users = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>viewMemberGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setViewMemberGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_member_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>viewSystemInfoAndLogs</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setViewSystemInfoAndLogs( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_system_info_and_logs = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>viewUsersInMemberGroups</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setViewUsersInMemberGroups( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_users_in_member_groups = $bool;
        return $this;
    }

/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj = new \stdClass();

        $obj->accessAllSites            = $this->access_all_sites;
        $obj->accessAudits              = $this->access_audits;
        $obj->accessConfiguration       = $this->access_configuration;
        $obj->accessDefaultEditorConfiguration =
            $this->access_default_editor_configuration;
        $obj->accessRoles               = $this->access_roles;
        $obj->accessSecurityArea        = $this->access_security_area;
        $obj->accessSiteManagement      = $this->access_site_management;
        $obj->broadcastMessages         = $this->broadcast_messages;
        $obj->bypassAllPermissionsChecks = $this->bypass_all_permissions_checks;
        $obj->changeIdentity            = $this->change_identity;
        $obj->configureLogging          = $this->configure_logging;
        $obj->createGroups              = $this->create_groups;
        $obj->createRoles               = $this->create_roles;
        $obj->createSites               = $this->create_sites;
        $obj->createUsers               = $this->create_users;
        $obj->databaseExportTool        = $this->database_export_tool;
        $obj->deleteAllUsers            = $this->delete_all_users;
        $obj->deleteAnyGroup            = $this->delete_any_group;
        $obj->deleteMemberGroups        = $this->delete_member_groups;
        $obj->deleteUsersInMemberGroups = $this->delete_users_in_member_groups;
        $obj->diagnosticTests           = $this->diagnostic_tests;
        $obj->editAccessRights          = $this->edit_access_rights;
        $obj->editAnyGroup              = $this->edit_any_group;
        $obj->editAnyUser               = $this->edit_any_user;
        $obj->editMemberGroups          = $this->edit_member_groups;
        $obj->editSystemPreferences     = $this->edit_system_preferences;
        $obj->editUsersInMemberGroups   = $this->edit_users_in_member_groups;
        $obj->forceLogout               = $this->force_logout;
        $obj->modifyDictionary          = $this->modify_dictionary;
        $obj->optimizeDatabase          = $this->optimize_database;
        $obj->searchingIndexing         = $this->searching_indexing;
        $obj->syncLdap                  = $this->sync_ldap ;
        $obj->viewAllGroups             = $this->view_all_groups;
        $obj->viewAllUsers              = $this->view_all_users;
        $obj->viewMemberGroups          = $this->view_member_groups;
        $obj->viewSystemInfoAndLogs     = $this->view_system_info_and_logs;
        $obj->viewUsersInMemberGroups   = $this->view_users_in_member_groups;
        
        return $obj;
    }
    
    private function checkBoolean( bool $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
    }
    
    private $access_all_sites;
    private $access_audits;
    private $access_configuration;
    private $access_default_editor_configuration;
    private $access_roles;
    private $access_security_area;
    private $access_site_management;
    private $broadcast_messages;
    private $bypass_all_permissions_checks;
    private $change_identity;
    private $configure_logging;
    private $create_groups;
    private $create_roles;
    private $create_sites;
    private $create_users;
    private $database_export_tool;
    private $delete_all_users;
    private $delete_any_group;
    private $delete_member_groups;
    private $delete_users_in_member_groups;
    private $diagnostic_tests;
    private $edit_access_rights;
    private $edit_any_group;
    private $edit_any_user;
    private $edit_member_groups;
    private $edit_system_preferences;
    private $edit_users_in_member_groups;
    private $force_logout;
    private $modify_dictionary;
    private $optimize_database;
    private $searching_indexing;
    private $sync_ldap;
    private $view_all_groups;
    private $view_all_users;
    private $view_member_groups;
    private $view_system_info_and_logs;
    private $view_users_in_member_groups;
}