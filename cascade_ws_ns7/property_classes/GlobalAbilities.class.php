<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<documentation><description><h2>Introduction</h2>
<p>A <code>GlobalAbilities</code> object represents the <code>globalAbilities</code> property found in a role asset. This class is a sub-class of <a href="/web-services/api/property-classes/abilities"><code>Abilities</code></a>.</p>
<h2>Properties of <code>globalAbilities</code> (Sorted)</h2>
<p>Besides the 49 properties (Cascade 8) shared with the sibling class <a href="/web-services/api/property-classes/site-abilities"><code>SiteAbilities</code></a> (which are defined in the parent class <a href="/web-services/api/property-classes/abilities"><code>Abilities</code></a>), this class also has its own unique properties (33 of them):</p>
<pre>accessAdminArea
accessAllSites
accessConfiguration
accessRoles
accessSecurityArea
accessSiteManagement
accessTargetsDestinations
broadcastMessages
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
editAnyGroup
editAnyUser
editMemberGroups
editSystemPreferences
editUsersInMemberGroups
forceLogout
optimizeDatabase
searchingIndexing
syncLdap
viewAllGroups
viewAllUsers
viewMemberGroups
viewSystemInfoAndLogs
viewUsersInMemberGroups
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class GlobalAbilities extends Abilities
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
            parent::__construct( $a );
            
            $this->access_admin_area             = $a->accessAdminArea;
            $this->access_all_sites              = $a->accessAllSites;
            $this->access_configuration          = $a->accessConfiguration;
            $this->access_roles                  = $a->accessRoles;
            $this->access_security_area          = $a->accessSecurityArea;
            $this->access_site_management        = $a->accessSiteManagement;
            $this->access_targets_destinations   = $a->accessTargetsDestinations;
            $this->broadcast_messages            = $a->broadcastMessages;
            $this->change_identity               = $a->changeIdentity;
            $this->configure_logging             = $a->configureLogging;
            $this->create_groups                 = $a->createGroups;
            $this->create_roles                  = $a->createRoles;
            $this->create_sites                  = $a->createSites;
            $this->create_users                  = $a->createUsers;
            $this->database_export_tool          = $a->databaseExportTool;
            $this->delete_all_users              = $a->deleteAllUsers;
            $this->delete_any_group              = $a->deleteAnyGroup;
            $this->delete_member_groups          = $a->deleteMemberGroups;
            $this->delete_users_in_member_groups = $a->deleteUsersInMemberGroups;
            $this->edit_any_group                = $a->editAnyGroup;
            $this->edit_any_user                 = $a->editAnyUser;
            $this->edit_member_groups            = $a->editMemberGroups;
            $this->edit_system_preferences       = $a->editSystemPreferences;
            $this->edit_users_in_member_groups   = $a->editUsersInMemberGroups;
            $this->force_logout                  = $a->forceLogout;
            $this->optimize_database             = $a->optimizeDatabase;
            $this->searching_indexing            = $a->searchingIndexing;
            $this->sync_ldap                     = $a->syncLdap;
            $this->view_all_groups               = $a->viewAllGroups;
            $this->view_all_users                = $a->viewAllUsers;
            $this->view_member_groups            = $a->viewMemberGroups;
            $this->view_system_info_and_logs     = $a->viewSystemInfoAndLogs;
            $this->view_users_in_member_groups   = $a->viewUsersInMemberGroups;
        }
    }
    
/**
<documentation><description><p>Returns <code>accessAdminArea</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessAdminArea() : bool
    {
        return $this->access_admin_area;
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
<documentation><description><p>Returns <code>accessTargetsDestinations</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessTargetsDestinations() : bool
    {
        return $this->access_targets_destinations;
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
<documentation><description><p>Sets <code>accessAdminArea</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessAdminArea( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_admin_area = $bool;
        return $this;
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
<documentation><description><p>Sets <code>accessTargetsDestinations</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessTargetsDestinations( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_targets_destinations = $bool;
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
        $obj = parent::toStdClass();

        $obj->accessAdminArea           = $this->access_admin_area;
        $obj->accessAllSites            = $this->access_all_sites;
        $obj->accessConfiguration       = $this->access_configuration;
        $obj->accessRoles               = $this->access_roles;
        $obj->accessSecurityArea        = $this->access_security_area;
        $obj->accessSiteManagement      = $this->access_site_management;
        $obj->accessTargetsDestinations = $this->access_targets_destinations;
        $obj->broadcastMessages         = $this->broadcast_messages;
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
        $obj->editAnyGroup              = $this->edit_any_group;
        $obj->editAnyUser               = $this->edit_any_user;
        $obj->editMemberGroups          = $this->edit_member_groups;
        $obj->editSystemPreferences     = $this->edit_system_preferences;
        $obj->editUsersInMemberGroups   = $this->edit_users_in_member_groups;
        $obj->forceLogout               = $this->force_logout;
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
    
    private $access_site_management;
    private $create_sites;
    private $access_targets_destinations;
    private $access_all_sites;
    private $view_system_info_and_logs;
    private $force_logout;
    private $access_security_area;
    private $optimize_database;
    private $sync_ldap;
    private $configure_logging;
    private $searching_indexing;
    private $access_configuration;
    private $edit_system_preferences;
    private $broadcast_messages;
    private $view_users_in_member_groups;
    private $view_all_users;
    private $create_users;
    private $delete_users_in_member_groups;
    private $delete_all_users;
    private $view_member_groups;
    private $view_all_groups;
    private $create_groups;
    private $delete_member_groups;
    private $access_roles;
    private $create_roles;
    private $delete_any_group;
    private $edit_any_user;
    private $edit_users_in_member_groups;
    private $edit_any_group;
    private $edit_member_groups;
    private $database_export_tool;
    private $change_identity;
}
?>