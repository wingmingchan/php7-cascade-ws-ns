<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/29/2015 Added the two missing members
  * 5/28/2015 Added namespaces.
  * 8/10/2014 Added getBrokenLinkReportAccess, getBrokenLinkReportMarkFixed, setBrokenLinkReportAccess, and setBrokenLinkReportMarkFixed.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

/**
 * Abilities
 * The parent class of GlobalAbilities and SiteAbilities. This class only deals with the 49 properties shared
 * by the two children.
 *
 * @link http://www.upstate.edu/cascade-admin/projects/web-services/oop/classes/property-classes/abilities.php
 */
abstract class Abilities extends Property
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
            $this->access_admin_area                            = $a->accessAdminArea;
            $this->access_asset_factories                       = $a->accessAssetFactories;
            $this->access_audits                                = $a->accessAudits;
            $this->access_configuration_sets                    = $a->accessConfigurationSets;
            $this->access_content_types                         = $a->accessContentTypes;
            $this->access_data_definitions                      = $a->accessDataDefinitions;
            $this->access_metadata_sets                         = $a->accessMetadataSets;
            $this->access_publish_sets                          = $a->accessPublishSets;
            $this->access_transports                            = $a->accessTransports;
            $this->access_workflow_definitions                  = $a->accessWorkflowDefinitions;
            $this->activate_delete_versions                     = $a->activateDeleteVersions;
            $this->always_allowed_to_toggle_data_checks         = $a->alwaysAllowedToToggleDataChecks;
            $this->assign_approve_workflow_steps                = $a->assignApproveWorkflowSteps;
            $this->assign_workflows_to_folders                  = $a->assignWorkflowsToFolders;
            $this->break_locks                                  = $a->breakLocks;
            $this->broken_link_report_access                    = $a->brokenLinkReportAccess;
            $this->broken_link_report_mark_fixed                = $a->brokenLinkReportMarkFixed;
            $this->bulk_change                                  = $a->bulkChange;
            $this->bypass_all_permissions_checks                = $a->bypassAllPermissionsChecks;
            $this->bypass_asset_factory_groups_new_menu         = $a->bypassAssetFactoryGroupsNewMenu;
            $this->bypass_destination_groups_when_publishing    = $a->bypassDestinationGroupsWhenPublishing;
            $this->bypass_workflow                              = $a->bypassWorkflow;
            $this->bypass_workflow_defintion_groups_for_folders = $a->bypassWorkflowDefintionGroupsForFolders;
            $this->cancel_publish_jobs                          = $a->cancelPublishJobs;
            $this->delete_workflows                             = $a->deleteWorkflows;
            $this->diagnostic_tests                             = $a->diagnosticTests;
            $this->edit_access_rights                           = $a->editAccessRights;
            $this->edit_data_definition                         = $a->editDataDefinition;
            $this->edit_page_content_type                       = $a->editPageContentType;
            $this->edit_page_level_configurations               = $a->editPageLevelConfigurations;
            $this->import_zip_archive                           = $a->importZipArchive;
            $this->integrate_folder                             = $a->integrateFolder;
            $this->move_rename_assets                           = $a->moveRenameAssets;
            $this->multi_select_copy                            = $a->multiSelectCopy;
            $this->multi_select_delete                          = $a->multiSelectDelete;
            $this->multi_select_move                            = $a->multiSelectMove;
            $this->multi_select_publish                         = $a->multiSelectPublish;
            $this->publish_readable_admin_area_assets           = $a->publishReadableAdminAreaAssets;
            $this->publish_writable_admin_area_assets           = $a->publishWritableAdminAreaAssets;
            $this->publish_readable_home_assets                 = $a->publishReadableHomeAssets;
            $this->publish_writable_home_assets                 = $a->publishWritableHomeAssets;
            $this->recycle_bin_delete_assets                    = $a->recycleBinDeleteAssets;
            $this->recycle_bin_view_restore_user_assets         = $a->recycleBinViewRestoreUserAssets;
            $this->recycle_bin_view_restore_all_assets          = $a->recycleBinViewRestoreAllAssets;
            $this->reorder_publish_queue                        = $a->reorderPublishQueue;
            $this->send_stale_asset_notifications               = $a->sendStaleAssetNotifications;
            $this->upload_images_from_wysiwyg                   = $a->uploadImagesFromWysiwyg;
            $this->view_publish_queue                           = $a->viewPublishQueue;
            $this->view_versions                                = $a->viewVersions;
        }
    }
    
    public function getAccessAdminArea()
    {
        return $this->access_admin_area;
    }
    
    public function getAccessAssetFactories()
    {
        return $this->access_asset_factories;
    }
    
    public function getAccessAudits()
    {
        return $this->access_audits;
    }
    
    public function getAccessConfigurationSets()
    {
        return $this->access_configuration_sets;
    }
    
    public function getAccessContentTypes()
    {
        return $this->access_content_types;
    }
    
    public function getAccessDataDefinitions()
    {
        return $this->access_data_definitions;
    }
    
    public function getAccessMetadataSets()
    {
        return $this->access_metadata_sets;
    }
    
    public function getAccessPublishSets()
    {
        return $this->access_publish_sets;
    }
    
    public function getAccessTransports()
    {
        return $this->access_transports;
    }
    
    public function getAccessWorkflowDefinitions()
    {
        return $this->access_workflow_definitions;
    }
    
    public function getActivateDeleteVersions()
    {
        return $this->activate_delete_versions;
    }
    
    public function getAlwaysAllowedToToggleDataChecks()
    {
        return $this->always_allowed_to_toggle_data_checks;
    }

    public function getAssignApproveWorkflowSteps()
    {
        return $this->assign_approve_workflow_steps;
    }
    
    public function getAssignWorkflowsToFolders()
    {
        return $this->assign_workflows_to_folders;
    }
    
    public function getBreakLocks()
    {
        return $this->break_locks;
    }
    
    public function getBrokenLinkReportAccess()
    {
        return $this->broken_link_report_access;
    }
    
    public function getBrokenLinkReportMarkFixed()
    {
        return $this->broken_link_report_mark_fixed;
    }
    
    public function getBulkChange()
    {
        return $this->bulk_change;
    }
    
    public function getBypassWorkflow()
    {
        return $this->bypass_workflow;
    }
    
    public function getBypassAssetFactoryGroupsNewMenu()
    {
        return $this->bypass_asset_factory_groups_new_menu;
    }
    
    public function getBypassDestinationGroupsWhenPublishing()
    {
        return $this->bypass_destination_groups_when_publishing;
    }
    
    public function getBypassWorkflowDefintionGroupsForFolders()
    {
        return $this->bypass_workflow_defintion_groups_for_folders;
    }    
    
    public function getBypassAllPermissionsChecks()
    {
        return $this->bypass_all_permissions_checks;
    }
    
    public function getCancelPublishJobs()
    {
        return $this->cancel_publish_jobs;
    }
    
    public function getDeleteWorkflows()
    {
        return $this->delete_workflows;
    }
    
    public function getDiagnosticTests()
    {
        return $this->diagnostic_tests;
    }
    
    public function getEditAccessRights()
    {
        return $this->edit_access_rights;
    }
    
    public function getEditDataDefinition()
    {
        return $this->edit_data_definition;
    }
    
    public function getEditPageContentType()
    {
        return $this->edit_page_content_type;
    }
    
    public function getEditPageLevelConfigurations()
    {
        return $this->edit_page_level_configurations;
    }
    
    public function getIntegrateFolder()
    {
        return $this->integrate_folder;
    }
    
    public function getImportZipArchive()
    {
        return $this->import_zip_archive;
    }
    
    public function getMoveRenameAssets()
    {
        return $this->move_rename_assets;
    }

    public function getMultiSelectCopy()
    {
        return $this->multi_select_copy;
    }
    
    public function getMultiSelectDelete()
    {
        return $this->multi_select_delete;
    }
    
    public function getMultiSelectMove()
    {
        return $this->multi_select_move;
    }
    
    public function getMultiSelectPublish()
    {
        return $this->multi_select_publish;
    }
    
    public function getPublishReadableAdminAreaAssets()
    {
        return $this->publish_readable_admin_area_assets;
    }
    
    public function getPublishReadableHomeAssets()
    {
        return $this->publish_readable_home_assets;
    }
    
    public function getPublishWritableAdminAreaAssets()
    {
        return $this->publish_writable_admin_area_assets;
    }
    
    public function getPublishWritableHomeAssets()
    {
        return $this->publish_writable_home_assets;
    }

    public function getRecycleBinDeleteAssets()
    {
        return $this->recycle_bin_delete_assets;
    }
    
    public function getRecycleBinViewRestoreAllAssets()
    {
        return $this->recycle_bin_view_restore_all_assets;
    }
    
    public function getRecycleBinViewRestoreUserAssets()
    {
        return $this->recycle_bin_view_restore_user_assets;
    }
    
    public function getReorderPublishQueue()
    {
        return $this->reorder_publish_queue;
    }
    
    public function getSendStaleAssetNotifications()
    {
        return $this->send_stale_asset_notifications;
    }

    public function getUploadImagesFromWysiwyg()
    {
        return $this->upload_images_from_wysiwyg;
    }
    
    public function getViewPublishQueue()
    {
        return $this->view_publish_queue;
    }
    
    public function getViewVersions()
    {
        return $this->view_versions;
    }
    
    public function setAccessAdminArea( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_admin_area = $bool;
        return $this;
    }
    
    public function setAccessAssetFactories( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_asset_factories = $bool;
        return $this;
    }
    
    public function setAccessAudits( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_audits = $bool;
        return $this;
    }
    
    public function setAccessConfigurationSets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_configuration_sets = $bool;
        return $this;
    }
    
    public function setAccessContentTypes( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_content_types = $bool;
        return $this;
    }
    
    public function setAccessDataDefinitions( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_data_definitions = $bool;
        return $this;
    }
    
    public function setAccessMetadataSets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_metadata_sets = $bool;
        return $this;
    }
    
    public function setAccessPublishSets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_publish_sets = $bool;
        return $this;
    }
    
    public function setAccessTransports( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_transports = $bool;
        return $this;
    }
    
    public function setAccessWorkflowDefinitions( $bool )
    {
        $this->checkBoolean( $bool );
        $this->access_workflow_definitions = $bool;
        return $this;
    }
    
    public function setActivateDeleteVersions( $bool )
    {
        $this->checkBoolean( $bool );
        $this->activate_delete_versions = $bool;
        return $this;
    }
    
    public function setAlwaysAllowedToToggleDataChecks( $bool )
    {
        $this->checkBoolean( $bool );
        $this->always_allowed_to_toggle_data_checks = $bool;
        return $this;
    }

    public function setAssignApproveWorkflowSteps( $bool )
    {
        $this->checkBoolean( $bool );
        $this->assign_approve_workflow_steps = $bool;
        return $this;
    }
    
    public function setAssignWorkflowsToFolders( $bool )
    {
        $this->checkBoolean( $bool );
        $this->assign_workflows_to_folders = $bool;
        return $this;
    }
    
    public function setBrokenLinkReportAccess( $bool )
    {
        $this->checkBoolean( $bool );
        $this->broken_link_report_access = $bool;
        return $this;
    }
    
    public function setBrokenLinkReportMarkFixed( $bool )
    {
        $this->checkBoolean( $bool );
        $this->broken_link_report_mark_fixed = $bool;
        return $this;
    }
    
    public function setBulkChange( $bool )
    {
        $this->checkBoolean( $bool );
        $this->bulk_change = $bool;
        return $this;
    }
    
    public function setBypassWorkflow( $bool )
    {
        $this->checkBoolean( $bool );
        $this->bypass_workflow = $bool;
        return $this;
    }
    
    public function setBreakLocks( $bool )
    {
        $this->checkBoolean( $bool );
        $this->break_locks = $bool;
        return $this;
    }
    
    public function setBypassAssetFactoryGroupsNewMenu( $bool )
    {
        $this->checkBoolean( $bool );
        $this->bypass_asset_factory_groups_new_menu = $bool;
        return $this;
    }
    
    public function setBypassDestinationGroupsWhenPublishing( $bool )
    {
        $this->checkBoolean( $bool );
        $this->bypass_destination_groups_when_publishing = $bool;
        return $this;
    }
    
    public function setBypassWorkflowDefintionGroupsForFolders( $bool )
    {
        $this->checkBoolean( $bool );
        $this->bypass_workflow_defintion_groups_for_folders = $bool;
        return $this;
    }    
    
    public function setBypassAllPermissionsChecks( $bool )
    {
        $this->checkBoolean( $bool );
        $this->bypass_all_permissions_checks = $bool;
        return $this;
    }
    
    public function setCancelPublishJobs( $bool )
    {
        $this->checkBoolean( $bool );
        $this->cancel_publish_jobs = $bool;
        return $this;
    }
    
    public function setDeleteWorkflows( $bool )
    {
        $this->checkBoolean( $bool );
        $this->delete_workflows = $bool;
        return $this;
    }
    
    public function setDiagnosticTests( $bool )
    {
        $this->checkBoolean( $bool );
        $this->diagnostic_tests = $bool;
        return $this;
    }
    
    public function setEditAccessRights( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_access_rights = $bool;
        return $this;
    }
    
    public function setEditDataDefinition( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_data_definition = $bool;
        return $this;
    }
    
    public function setEditPageContentType( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_page_content_type = $bool;
        return $this;
    }
    
    public function setEditPageLevelConfigurations( $bool )
    {
        $this->checkBoolean( $bool );
        $this->edit_page_level_configurations = $bool;
        return $this;
    }
    
    public function setIntegrateFolder( $bool )
    {
        $this->checkBoolean( $bool );
        $this->integrate_folder = $bool;
        return $this;
    }
    
    public function setImportZipArchive( $bool )
    {
        $this->checkBoolean( $bool );
        $this->import_zip_archive = $bool;
        return $this;
    }
    
    public function setMoveRenameAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->move_rename_assets = $bool;
        return $this;
    }

    public function setMultiSelectCopy( $bool )
    {
        $this->checkBoolean( $bool );
        $this->multi_select_copy = $bool;
        return $this;
    }
    
    public function setMultiSelectDelete( $bool )
    {
        $this->checkBoolean( $bool );
        $this->multi_select_delete = $bool;
        return $this;
    }
    
    public function setMultiSelectMove( $bool )
    {
        $this->checkBoolean( $bool );
        $this->multi_select_move = $bool;
        return $this;
    }
    
    public function setMultiSelectPublish( $bool )
    {
        $this->checkBoolean( $bool );
        $this->multi_select_publish = $bool;
        return $this;
    }
    
    public function setPublishReadableAdminAreaAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->publish_readable_admin_area_assets = $bool;
        return $this;
    }
    
    public function setPublishReadableHomeAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->publish_readable_home_assets = $bool;
        return $this;
    }
    
    public function setPublishWritableAdminAreaAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->publish_writable_admin_area_assets = $bool;
        return $this;
    }
    
    public function setPublishWritableHomeAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->publish_writable_home_assets = $bool;
        return $this;
    }

    public function setRecycleBinDeleteAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_delete_assets = $bool;
        return $this;
    }
    
    public function setRecycleBinViewRestoreAllAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_view_restore_all_assets = $bool;
        return $this;
    }
    
    public function setRecycleBinViewRestoreUserAssets( $bool )
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_view_restore_user_assets = $bool;
        return $this;
    }
    
    public function setReorderPublishQueue( $bool )
    {
        $this->checkBoolean( $bool );
        $this->reorder_publish_queue = $bool;
        return $this;
    }

    public function setSendStaleAssetNotifications( $bool )
    {
        $this->checkBoolean( $bool );
        $this->send_stale_asset_notifications = $bool;
        return $this;
    }

    public function setUploadImagesFromWysiwyg( $bool )
    {
        $this->checkBoolean( $bool );
        $this->upload_images_from_wysiwyg = $bool;
        return $this;
    }
    
    public function setViewPublishQueue( $bool )
    {
        $this->checkBoolean( $bool );
        $this->view_publish_queue = $bool;
        return $this;
    }
    
    public function setViewVersions( $bool )
    {
        $this->checkBoolean( $bool );
        $this->view_versions = $bool;
        return $this;
    }
    
    public function toStdClass()
    {
        $obj           = new \stdClass();
        
        $obj->bypassAllPermissionsChecks              = $this->bypass_all_permissions_checks;
        $obj->uploadImagesFromWysiwyg                 = $this->upload_images_from_wysiwyg;
        $obj->multiSelectCopy                         = $this->multi_select_copy;
        $obj->multiSelectPublish                      = $this->multi_select_publish;
        $obj->multiSelectMove                         = $this->multi_select_move;
        $obj->multiSelectDelete                       = $this->multi_select_delete;
        $obj->editPageLevelConfigurations             = $this->edit_page_level_configurations;
        $obj->editPageContentType                     = $this->edit_page_content_type;
        $obj->editDataDefinition                      = $this->edit_data_definition;
        $obj->publishReadableHomeAssets               = $this->publish_readable_home_assets;
        $obj->publishWritableHomeAssets               = $this->publish_writable_home_assets;
        $obj->editAccessRights                        = $this->edit_access_rights;
        $obj->viewVersions                            = $this->view_versions;
        $obj->activateDeleteVersions                  = $this->activate_delete_versions;
        $obj->accessAudits                            = $this->access_audits;
        $obj->bypassWorkflow                          = $this->bypass_workflow;
        $obj->assignApproveWorkflowSteps              = $this->assign_approve_workflow_steps;
        $obj->deleteWorkflows                         = $this->delete_workflows;
        $obj->breakLocks                              = $this->break_locks;
        $obj->assignWorkflowsToFolders                = $this->assign_workflows_to_folders;
        $obj->bypassAssetFactoryGroupsNewMenu         = $this->bypass_asset_factory_groups_new_menu;
        $obj->bypassDestinationGroupsWhenPublishing   = $this->bypass_destination_groups_when_publishing;
        $obj->bypassWorkflowDefintionGroupsForFolders = $this->bypass_workflow_defintion_groups_for_folders;
        $obj->accessAdminArea                         = $this->access_admin_area;
        $obj->accessAssetFactories                    = $this->access_asset_factories;
        $obj->accessConfigurationSets                 = $this->access_configuration_sets;
        $obj->accessDataDefinitions                   = $this->access_data_definitions;
        $obj->accessMetadataSets                      = $this->access_metadata_sets;
        $obj->accessPublishSets                       = $this->access_publish_sets;
        $obj->accessTransports                        = $this->access_transports;
        $obj->accessWorkflowDefinitions               = $this->access_workflow_definitions;
        $obj->accessContentTypes                      = $this->access_content_types;
        $obj->publishReadableAdminAreaAssets          = $this->publish_readable_admin_area_assets;
        $obj->publishWritableAdminAreaAssets          = $this->publish_writable_admin_area_assets;
        $obj->integrateFolder                         = $this->integrate_folder;
        $obj->importZipArchive                        = $this->import_zip_archive;
        $obj->bulkChange                              = $this->bulk_change;
        $obj->recycleBinViewRestoreUserAssets         = $this->recycle_bin_view_restore_user_assets;
        $obj->recycleBinDeleteAssets                  = $this->recycle_bin_delete_assets;
        $obj->recycleBinViewRestoreAllAssets          = $this->recycle_bin_view_restore_all_assets;
        $obj->moveRenameAssets                        = $this->move_rename_assets;
        $obj->diagnosticTests                         = $this->diagnostic_tests;
        $obj->alwaysAllowedToToggleDataChecks         = $this->always_allowed_to_toggle_data_checks;
        $obj->viewPublishQueue                        = $this->view_publish_queue;
        $obj->reorderPublishQueue                     = $this->reorder_publish_queue;
        $obj->cancelPublishJobs                       = $this->cancel_publish_jobs;
        $obj->sendStaleAssetNotifications             = $this->send_stale_asset_notifications;
        $obj->brokenLinkReportAccess                  = $this->broken_link_report_access;
        $obj->brokenLinkReportMarkFixed               = $this->broken_link_report_mark_fixed;
        
        return $obj;
    }
    
    private function checkBoolean( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
    }

    // 49 members
    private $bypass_all_permissions_checks;
    private $upload_images_from_wysiwyg;
    private $multi_select_copy;
    private $multi_select_publish;
    private $multi_select_move;
    private $multi_select_delete;
    private $edit_page_level_configurations;
    private $edit_page_content_type;
    private $edit_data_definition;
    private $publish_readable_home_assets;
    private $publish_writable_home_assets;
    private $edit_access_rights;
    private $view_versions;
    private $activate_delete_versions;
    private $access_audits;
    private $bypass_workflow;
    private $assign_approve_workflow_steps;
    private $delete_workflows;
    private $break_locks;
    private $assign_workflows_to_folders;
    private $bypass_asset_factory_groups_new_menu;
    private $bypass_destination_groups_when_publishing;
    private $bypass_workflow_defintion_groups_for_folders;
    private $access_admin_area;
    private $access_asset_factories;
    private $access_configuration_sets;
    private $access_data_definitions;
    private $access_metadata_sets;
    private $access_publish_sets;
    private $access_transports;
    private $access_workflow_definitions;
    private $access_content_types;
    private $publish_readable_admin_area_assets;
    private $publish_writable_admin_area_assets;
    private $integrate_folder;
    private $import_zip_archive;
    private $bulk_change;
    private $recycle_bin_view_restore_user_assets;
    private $recycle_bin_delete_assets;
    private $recycle_bin_view_restore_all_assets;
    private $move_rename_assets;
    private $diagnostic_tests;
    private $always_allowed_to_toggle_data_checks;
    private $view_publish_queue;
    private $reorder_publish_queue;
    private $cancel_publish_jobs;
    private $send_stale_asset_notifications;
    private $broken_link_report_access;
    private $broken_link_report_mark_fixed;
}
?>