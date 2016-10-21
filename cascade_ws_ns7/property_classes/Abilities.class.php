<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/7/2016 Moved accessAdminArea to GlobalAbilities.
  * 9/6/2016 Added accessManageSiteArea.
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
 */
 
/**
<documentation><description><h2>Introduction</h2>


</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
abstract class Abilities extends Property
{
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
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
            $this->access_manage_site_area                      = $a->accessManageSiteArea;
        }
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessAssetFactories() : bool
    {
        return $this->access_asset_factories;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessAudits() : bool
    {
        return $this->access_audits;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessConfigurationSets() : bool
    {
        return $this->access_configuration_sets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessContentTypes() : bool
    {
        return $this->access_content_types;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessDataDefinitions() : bool
    {
        return $this->access_data_definitions;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessManageSiteArea() : bool
    {
        return $this->access_manage_site_area;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessMetadataSets() : bool
    {
        return $this->access_metadata_sets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessPublishSets() : bool
    {
        return $this->access_publish_sets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessTransports() : bool
    {
        return $this->access_transports;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessWorkflowDefinitions() : bool
    {
        return $this->access_workflow_definitions;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getActivateDeleteVersions() : bool
    {
        return $this->activate_delete_versions;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAlwaysAllowedToToggleDataChecks() : bool
    {
        return $this->always_allowed_to_toggle_data_checks;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAssignApproveWorkflowSteps() : bool
    {
        return $this->assign_approve_workflow_steps;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAssignWorkflowsToFolders() : bool
    {
        return $this->assign_workflows_to_folders;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBreakLocks() : bool
    {
        return $this->break_locks;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBrokenLinkReportAccess() : bool
    {
        return $this->broken_link_report_access;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBrokenLinkReportMarkFixed() : bool
    {
        return $this->broken_link_report_mark_fixed;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBulkChange() : bool
    {
        return $this->bulk_change;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassWorkflow() : bool
    {
        return $this->bypass_workflow;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassAssetFactoryGroupsNewMenu() : bool
    {
        return $this->bypass_asset_factory_groups_new_menu;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassDestinationGroupsWhenPublishing() : bool
    {
        return $this->bypass_destination_groups_when_publishing;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassWorkflowDefintionGroupsForFolders() : bool
    {
        return $this->bypass_workflow_defintion_groups_for_folders;
    }    
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassAllPermissionsChecks() : bool
    {
        return $this->bypass_all_permissions_checks;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getCancelPublishJobs() : bool
    {
        return $this->cancel_publish_jobs;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getDeleteWorkflows() : bool
    {
        return $this->delete_workflows;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getDiagnosticTests() : bool
    {
        return $this->diagnostic_tests;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditAccessRights() : bool
    {
        return $this->edit_access_rights;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditDataDefinition() : bool
    {
        return $this->edit_data_definition;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditPageContentType() : bool
    {
        return $this->edit_page_content_type;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditPageLevelConfigurations() : bool
    {
        return $this->edit_page_level_configurations;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getIntegrateFolder() : bool
    {
        return $this->integrate_folder;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getImportZipArchive() : bool
    {
        return $this->import_zip_archive;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMoveRenameAssets() : bool
    {
        return $this->move_rename_assets;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectCopy() : bool
    {
        return $this->multi_select_copy;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectDelete() : bool
    {
        return $this->multi_select_delete;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectMove() : bool
    {
        return $this->multi_select_move;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectPublish() : bool
    {
        return $this->multi_select_publish;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishReadableAdminAreaAssets() : bool
    {
        return $this->publish_readable_admin_area_assets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishReadableHomeAssets() : bool
    {
        return $this->publish_readable_home_assets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishWritableAdminAreaAssets() : bool
    {
        return $this->publish_writable_admin_area_assets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishWritableHomeAssets() : bool
    {
        return $this->publish_writable_home_assets;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRecycleBinDeleteAssets() : bool
    {
        return $this->recycle_bin_delete_assets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRecycleBinViewRestoreAllAssets() : bool
    {
        return $this->recycle_bin_view_restore_all_assets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRecycleBinViewRestoreUserAssets() : bool
    {
        return $this->recycle_bin_view_restore_user_assets;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getReorderPublishQueue() : bool
    {
        return $this->reorder_publish_queue;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getSendStaleAssetNotifications() : bool
    {
        return $this->send_stale_asset_notifications;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getUploadImagesFromWysiwyg() : bool
    {
        return $this->upload_images_from_wysiwyg;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getViewPublishQueue() : bool
    {
        return $this->view_publish_queue;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getViewVersions() : bool
    {
        return $this->view_versions;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessAssetFactories( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_asset_factories = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessAudits( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_audits = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessConfigurationSets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_configuration_sets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessContentTypes( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_content_types = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessDataDefinitions( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_data_definitions = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/

    public function setAccessManageSiteArea( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_manage_site_area = $bool;
        return $this;
    }
   
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessMetadataSets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_metadata_sets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessPublishSets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_publish_sets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessTransports( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_transports = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessWorkflowDefinitions( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_workflow_definitions = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setActivateDeleteVersions( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->activate_delete_versions = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAlwaysAllowedToToggleDataChecks( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->always_allowed_to_toggle_data_checks = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAssignApproveWorkflowSteps( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->assign_approve_workflow_steps = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAssignWorkflowsToFolders( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->assign_workflows_to_folders = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBrokenLinkReportAccess( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->broken_link_report_access = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBrokenLinkReportMarkFixed( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->broken_link_report_mark_fixed = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBulkChange( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bulk_change = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassWorkflow( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_workflow = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBreakLocks( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->break_locks = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassAssetFactoryGroupsNewMenu( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_asset_factory_groups_new_menu = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassDestinationGroupsWhenPublishing( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_destination_groups_when_publishing = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassWorkflowDefintionGroupsForFolders( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_workflow_defintion_groups_for_folders = $bool;
        return $this;
    }    
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassAllPermissionsChecks( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_all_permissions_checks = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setCancelPublishJobs( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->cancel_publish_jobs = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setDeleteWorkflows( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->delete_workflows = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setDiagnosticTests( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->diagnostic_tests = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditAccessRights( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_access_rights = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditDataDefinition( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_data_definition = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditPageContentType( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_page_content_type = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditPageLevelConfigurations( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_page_level_configurations = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setIntegrateFolder( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->integrate_folder = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setImportZipArchive( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->import_zip_archive = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMoveRenameAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->move_rename_assets = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectCopy( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_copy = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectDelete( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_delete = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectMove( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_move = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectPublish( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_publish = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishReadableAdminAreaAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_readable_admin_area_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishReadableHomeAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_readable_home_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishWritableAdminAreaAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_writable_admin_area_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishWritableHomeAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_writable_home_assets = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setRecycleBinDeleteAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_delete_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setRecycleBinViewRestoreAllAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_view_restore_all_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setRecycleBinViewRestoreUserAssets( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_view_restore_user_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setReorderPublishQueue( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->reorder_publish_queue = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setSendStaleAssetNotifications( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->send_stale_asset_notifications = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setUploadImagesFromWysiwyg( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->upload_images_from_wysiwyg = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setViewPublishQueue( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_publish_queue = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setViewVersions( $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_versions = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
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
        $obj->accessManageSiteArea                    = $this->access_manage_site_area;
        
        return $obj;
    }
    
    private function checkBoolean( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
    }

    // 50 members
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
    private $access_manage_site_area;
}
?>