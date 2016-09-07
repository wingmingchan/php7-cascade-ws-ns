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

class GlobalAbilities extends Abilities
{
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
            
            $this->access_site_management        = $a->accessSiteManagement;
            $this->create_sites                  = $a->createSites;
            $this->access_targets_destinations   = $a->accessTargetsDestinations;
            $this->access_all_sites              = $a->accessAllSites;
            $this->view_system_info_and_logs     = $a->viewSystemInfoAndLogs;
            $this->force_logout                  = $a->forceLogout;
            $this->access_security_area          = $a->accessSecurityArea;
            //$this->new_site_wizard               = $a->newSiteWizard;
            $this->optimize_database             = $a->optimizeDatabase;
            $this->sync_ldap                     = $a->syncLdap;
            $this->configure_logging             = $a->configureLogging;
            $this->searching_indexing            = $a->searchingIndexing;
            $this->access_configuration          = $a->accessConfiguration;
            $this->edit_system_preferences       = $a->editSystemPreferences;
            //$this->site_migration                = $a->siteMigration;
            $this->broadcast_messages            = $a->broadcastMessages;
            $this->view_users_in_member_groups   = $a->viewUsersInMemberGroups;
            $this->view_all_users                = $a->viewAllUsers;
            $this->create_users                  = $a->createUsers;
            $this->delete_users_in_member_groups = $a->deleteUsersInMemberGroups;
            $this->delete_all_users              = $a->deleteAllUsers;
            $this->view_member_groups            = $a->viewMemberGroups;
            $this->view_all_groups               = $a->viewAllGroups;
            $this->create_groups                 = $a->createGroups;
            $this->delete_member_groups          = $a->deleteMemberGroups;
            $this->access_roles                  = $a->accessRoles;
            $this->create_roles                  = $a->createRoles;
            $this->delete_any_group              = $a->deleteAnyGroup;
            $this->edit_any_user                 = $a->editAnyUser;
            $this->edit_users_in_member_groups   = $a->editUsersInMemberGroups;
            $this->edit_any_group                = $a->editAnyGroup;
            $this->edit_member_groups            = $a->editMemberGroups;
            //$this->recycle_bin_checker           = $a->recycleBinChecker;
            //$this->path_repair_tool              = $a->pathRepairTool;
            $this->database_export_tool          = $a->databaseExportTool;
            $this->change_identity               = $a->changeIdentity;
            $this->access_admin_area             = $a->accessAdminArea;
        }
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessAdminArea() : bool
    {
        return $this->access_admin_area;
    }
    
    public function getAccessAllSites()
    {
        return $this->access_all_sites;
    }

    public function getAccessConfiguration()
    {
        return $this->access_configuration;
    }

    public function getAccessRoles()
    {
        return $this->access_roles;
    }

    public function getAccessSecurityArea()
    {
        return $this->access_security_area;
    }

    public function getAccessSiteManagement()
    {
        return $this->access_site_management;
    }

    public function getAccessTargetsDestinations()
    {
        return $this->access_targets_destinations;
    }

    public function getBroadcastMessages()
    {
        return $this->broadcast_messages;
    }

    public function getChangeIdentity()
    {
        return $this->change_identity;
    }

    public function getConfigureLogging()
    {
        return $this->configure_logging;
    }

    public function getCreateGroups()
    {
        return $this->create_groups;
    }

    public function getCreateRoles()
    {
        return $this->create_roles;
    }

    public function getCreateSites()
    {
        return $this->create_sites;
    }

    public function getCreateUsers()
    {
        return $this->create_users;
    }

    public function getDatabaseExportTool()
    {
        return $this->database_export_tool;
    }

    public function getDeleteAllUsers()
    {
        return $this->delete_all_users;
    }

    public function getDeleteAnyGroup()
    {
        return $this->delete_any_group;
    }

    public function getDeleteMemberGroups()
    {
        return $this->delete_member_groups;
    }

    public function getDeleteUsersInMemberGroups()
    {
        return $this->delete_users_in_member_groups;
    }

    public function getEditAnyGroup()
    {
        return $this->edit_any_group;
    }

    public function getEditAnyUser()
    {
        return $this->edit_any_user;
    }

    public function getEditMemberGroups()
    {
        return $this->edit_member_groups;
    }

    public function getEditSystemPreferences()
    {
        return $this->edit_system_preferences;
    }

    public function getEditUsersInMemberGroups()
    {
        return $this->edit_users_in_member_groups;
    }

    public function getForceLogout()
    {
        return $this->force_logout;
    }
/*
    public function getNewSiteWizard()
    {
        return $this->new_site_wizard;
    }
*/
    public function getOptimizeDatabase()
    {
        return $this->optimize_database;
    }
/*
    public function getPathRepairTool()
    {
        return $this->path_repair_tool;
    }

    public function getRecycleBinChecker()
    {
        return $this->recycle_bin_checker;
    }
*/
    public function getSearchingIndexing()
    {
        return $this->searching_indexing;
    }

    public function getSyncLdap()
    {
        return $this->sync_ldap ;
    }

    public function getViewSystemInfoAndLogs()
    {
        return $this->view_system_info_and_logs;
    }
/*
    public function getSiteMigration()
    {
        return $this->site_migration;
    }
*/
    public function getViewAllGroups()
    {
        return $this->view_all_groups;
    }

    public function getViewAllUsers()
    {
        return $this->view_all_users;
    }

    public function getViewMemberGroups()
    {
        return $this->view_member_groups;
    }

    public function getViewUsersInMemberGroups()
    {
        return $this->view_users_in_member_groups;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessAdminArea( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_admin_area = $bool;
        return $this;
    }
    
    public function setAccessAllSites( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_all_sites = $bool;
        return $this;
    }

    public function setAccessConfiguration( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_configuration = $bool;
        return $this;
    }

    public function setAccessRoles( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_roles = $bool;
        return $this;
    }

    public function setAccessSecurityArea( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_security_area = $bool;
        return $this;
    }

    public function setAccessSiteManagement( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_site_management = $bool;
        return $this;
    }

    public function setAccessTargetsDestinations( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_targets_destinations = $bool;
        return $this;
    }

    public function setBroadcastMessages( $bool )
    {
        $this->checkBoolean( $bool );
        $this->broadcast_messages = $bool;
        return $this;
    }
    
    public function setChangeIdentity( $bool )
    {
        $this->checkBoolean( $bool );
        $this->change_identity = $bool;
        return $this;
    }
    
    public function setConfigureLogging( $bool )
    {
        $this->checkBoolean( $bool );
        $this->configure_logging = $bool;
        return $this;
    }

    public function setCreateGroups( $bool )
    {
        $this->checkBoolean( $bool );
        $this->create_groups = $bool;
        return $this;
    }

    public function setCreateRoles( $bool )
    {
        $this->checkBoolean( $bool );
        $this->create_roles = $bool;
        return $this;
    }

    public function setCreateSites( $bool )
    {
        $this->checkBoolean( $bool );
        $this->create_sites = $bool;
        return $this;
    }

    public function setCreateUsers( $bool )
    {
        $this->checkBoolean( $bool );
        $this->create_users = $bool;
        return $this;
    }

    public function setDatabaseExportTool( $bool )
    {
        $this->checkBoolean( $bool );
        $this->database_export_tool = $bool;
        return $this;
    }

    public function setDeleteAllUsers( $bool )
    {
        $this->checkBoolean( $bool );
        $this->delete_all_users = $bool;
        return $this;
    }

    public function setDeleteAnyGroup( $bool )
    {
        $this->checkBoolean( $bool );
        $this->delete_any_group = $bool;
        return $this;
    }

    public function setDeleteMemberGroups( $bool )
    {
        $this->checkBoolean( $bool );
        $this->delete_member_groups = $bool;
        return $this;
    }

    public function setDeleteUsersInMemberGroups( $bool )
    {
        $this->checkBoolean( $bool );
        $this->delete_users_in_member_groups = $bool;
        return $this;
    }

    public function setEditAnyGroup( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_any_group = $bool;
        return $this;
    }

    public function setEditAnyUser( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_any_user = $bool;
        return $this;
    }

    public function setEditMemberGroups( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_member_groups = $bool;
        return $this;
    }

    public function setEditSystemPreferences( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_system_preferences = $bool;
        return $this;
    }

    public function setEditUsersInMemberGroups( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_users_in_member_groups = $bool;
        return $this;
    }

    public function setForceLogout( $bool )
    {
        $this->checkBoolean( $bool );
        $this->force_logout = $bool;
        return $this;
    }
/*
    public function setNewSiteWizard( $bool )
    {
        $this->checkBoolean( $bool );
        $this->new_site_wizard = $bool;
        return $this;
    }
*/
    public function setOptimizeDatabase( $bool )
    {
        $this->checkBoolean( $bool );
        $this->optimize_database = $bool;
        return $this;
    }
/*
    public function setPathRepairTool( $bool )
    {
        $this->checkBoolean( $bool );
        $this->path_repair_tool = $bool;
        return $this;
    }

    public function setRecycleBinChecker( $bool )
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_checker = $bool;
        return $this;
    }
*/
    public function setSearchingIndexing( $bool )
    {
        $this->checkBoolean( $bool );
        $this->searching_indexing = $bool;
        return $this;
    }

    public function setSyncLdap( $bool )
    {
        $this->checkBoolean( $bool );
        $this->sync_ldap  = $bool;
        return $this;
    }

    public function setViewSystemInfoAndLogs( $bool )
    {
        $this->checkBoolean( $bool );
        $this->view_system_info_and_logs = $bool;
        return $this;
    }
/*
    public function setSiteMigration( $bool )
    {
        $this->checkBoolean( $bool );
        $this->site_migration = $bool;
        return $this;
    }
*/
    public function setViewAllGroups( $bool )
    {
        $this->checkBoolean( $bool );
        $this->view_all_groups = $bool;
        return $this;
    }

    public function setViewAllUsers( $bool )
    {
        $this->checkBoolean( $bool );
        $this->view_all_users = $bool;
        return $this;
    }

    public function setViewMemberGroups( $bool )
    {
        $this->checkBoolean( $bool );
        $this->view_member_groups = $bool;
        return $this;
    }

    public function setViewUsersInMemberGroups( $bool )
    {
       $this->checkBoolean( $bool );
        $this->view_users_in_member_groups = $bool;
        return $this;
    }
    
    public function toStdClass()
    {
        $obj = parent::toStdClass();

        $obj->accessSiteManagement      = $this->access_site_management;
        $obj->createSites               = $this->create_sites;
        $obj->accessTargetsDestinations = $this->access_targets_destinations;
        $obj->accessAllSites            = $this->access_all_sites;
        $obj->viewSystemInfoAndLogs     = $this->view_system_info_and_logs;
        $obj->forceLogout               = $this->force_logout;
        $obj->accessSecurityArea        = $this->access_security_area;
        //$obj->newSiteWizard             = $this->new_site_wizard;
        $obj->optimizeDatabase          = $this->optimize_database;
        $obj->syncLdap                  = $this->sync_ldap ;
        $obj->configureLogging          = $this->configure_logging;
        $obj->searchingIndexing         = $this->searching_indexing;
        $obj->accessConfiguration       = $this->access_configuration;
        $obj->editSystemPreferences     = $this->edit_system_preferences;
        //$obj->siteMigration             = $this->site_migration;
        $obj->broadcastMessages         = $this->broadcast_messages;
        $obj->viewUsersInMemberGroups   = $this->view_users_in_member_groups;
        $obj->viewAllUsers              = $this->view_all_users;
        $obj->createUsers               = $this->create_users;
        $obj->deleteUsersInMemberGroups = $this->delete_users_in_member_groups;
        $obj->deleteAllUsers            = $this->delete_all_users;
        $obj->viewMemberGroups          = $this->view_member_groups;
        $obj->viewAllGroups             = $this->view_all_groups;
        $obj->createGroups              = $this->create_groups;
        $obj->deleteMemberGroups        = $this->delete_member_groups;
        $obj->accessRoles               = $this->access_roles;
        $obj->createRoles               = $this->create_roles;
        $obj->deleteAnyGroup            = $this->delete_any_group;
        $obj->editAnyUser               = $this->edit_any_user;
        $obj->editUsersInMemberGroups   = $this->edit_users_in_member_groups;
        $obj->editAnyGroup              = $this->edit_any_group;
        $obj->editMemberGroups          = $this->edit_member_groups;
        //$obj->recycleBinChecker         = $this->recycle_bin_checker;
        //$obj->pathRepairTool            = $this->path_repair_tool;
        $obj->databaseExportTool        = $this->database_export_tool;
        $obj->changeIdentity            = $this->change_identity;
        $obj->accessAdminArea           = $this->access_admin_area;
        
        return $obj;
    }
    
    private function checkBoolean( $bool )
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
    //private $new_site_wizard;
    private $optimize_database;
    private $sync_ldap;
    private $configure_logging;
    private $searching_indexing;
    private $access_configuration;
    private $edit_system_preferences;
    //private $site_migration;
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
    //private $recycle_bin_checker;
    //private $path_repair_tool;
    private $database_export_tool;
    private $change_identity;
}
?>