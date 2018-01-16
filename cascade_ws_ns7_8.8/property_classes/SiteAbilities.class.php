<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/4/2018 Added missing properties and methods for 8.7.1.
  * 6/29/2017 Rewrote code for 8.4.1.
  * 6/12/2017 Added WSDL.
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
<p>A <code>SiteAbilities</code> object represents the <code>siteAbilities</code> property found in a role asset. As of Cascade 8.7.1, there are 52 of them.</p>
<h2>Properties of <code>siteAbilities</code></h2>
<pre>accessAssetFactories
accessAudits
accessConfigurationSets
accessConnectors
accessContentTypes
accessDataDefinitions
accessDestinations
accessEditorConfigurations
accessManageSiteArea
accessMetadataSets
accessPublishSets
accessTransports
accessWorkflowDefinitions
activateDeleteVersions
alwaysAllowedToToggleDataChecks
assignApproveWorkflowSteps
assignWorkflowsToFolders
breakLocks
brokenLinkReportAccess
brokenLinkReportMarkFixed
bulkChange
bypassAllPermissionsChecks
bypassAssetFactoryGroupsNewMenu
bypassDestinationGroupsWhenPublishing
bypassWorkflow
bypassWorkflowDefintionGroupsForFolders
bypassWysiwygEditorRestrictions
cancelPublishJobs
deleteWorkflows
diagnosticTests
editAccessRights
editDataDefinition
editPageContentType
editPageLevelConfigurations
importZipArchive
moveRenameAssets
multiSelectCopy
multiSelectDelete
multiSelectMove
multiSelectPublish
publishReadableAdminAreaAssets
publishReadableHomeAssets
publishWritableAdminAreaAssets
publishWritableHomeAssets
recycleBinDeleteAssets
recycleBinViewRestoreAllAssets
recycleBinViewRestoreUserAssets
reorderPublishQueue
sendStaleAssetNotifications
uploadImagesFromWysiwyg
viewPublishQueue
viewVersions
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "site-abilities" ),
    ) );
return $doc_string;
?>
</description>
<postscript></postscript>
</documentation>
*/
class SiteAbilities extends Property
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
        if( isset( $a->accessAssetFactories ) )
            $this->access_asset_factories    = $a->accessAssetFactories;
        if( isset( $a->accessAudits ) )
            $this->access_audits             = $a->accessAudits;
        if( isset( $a->accessConfigurationSets ) )
            $this->access_configuration_sets = $a->accessConfigurationSets;
        if( isset( $a->accessConnectors ) )
            $this->access_connectors         = $a->accessConnectors;
        if( isset( $a->accessContentTypes ) )
            $this->access_content_types      = $a->accessContentTypes;
        if( isset( $a->accessDataDefinitions ) )
            $this->access_data_definitions   = $a->accessDataDefinitions;
        if( isset( $a->accessDestinations ) )
            $this->access_destinations       = $a->accessDestinations;
        if( isset( $a->accessEditorConfigurations ) )
            $this->access_editor_configurations = $a->accessEditorConfigurations;
        if( isset( $a->accessManageSiteArea ) )
            $this->access_manage_site_area   = $a->accessManageSiteArea;
        if( isset( $a->accessMetadataSets ) )
            $this->access_metadata_sets      = $a->accessMetadataSets;
        if( isset( $a->accessPublishSets ) )
            $this->access_publish_sets       = $a->accessPublishSets;
        if( isset( $a->accessTransports ) )
            $this->access_transports         = $a->accessTransports;
        if( isset( $a->accessWorkflowDefinitions ) )
            $this->access_workflow_definitions = $a->accessWorkflowDefinitions;
        if( isset( $a->activateDeleteVersions ) )
            $this->activate_delete_versions  = $a->activateDeleteVersions;
        if( isset( $a->alwaysAllowedToToggleDataChecks ) )
            $this->always_allowed_to_toggle_data_checks =
                $a->alwaysAllowedToToggleDataChecks;
        if( isset( $a->assignApproveWorkflowSteps ) )
            $this->assign_approve_workflow_steps = $a->assignApproveWorkflowSteps;
        if( isset( $a->assignWorkflowsToFolders ) )
            $this->assign_workflows_to_folders   = $a->assignWorkflowsToFolders;
        if( isset( $a->breakLocks ) )
            $this->break_locks                   = $a->breakLocks;
        if( isset( $a->brokenLinkReportAccess ) )
            $this->broken_link_report_access     = $a->brokenLinkReportAccess;
        if( isset( $a->brokenLinkReportMarkFixed ) )
            $this->broken_link_report_mark_fixed = $a->brokenLinkReportMarkFixed;
        if( isset( $a->bulkChange ) )
            $this->bulk_change                   = $a->bulkChange;
        if( isset( $a->bypassAllPermissionsChecks ) )
            $this->bypass_all_permissions_checks = $a->bypassAllPermissionsChecks;
        if( isset( $a->bypassAssetFactoryGroupsNewMenu ) )
            $this->bypass_asset_factory_groups_new_menu =
                $a->bypassAssetFactoryGroupsNewMenu;
        if( isset( $a->bypassDestinationGroupsWhenPublishing ) )
            $this->bypass_destination_groups_when_publishing =
                $a->bypassDestinationGroupsWhenPublishing;
        if( isset( $a->bypassWorkflow ) )
            $this->bypass_workflow               = $a->bypassWorkflow;
        if( isset( $a->bypassWorkflowDefintionGroupsForFolders ) )
            $this->bypass_workflow_defintion_groups_for_folders =
                $a->bypassWorkflowDefintionGroupsForFolders;
        if( isset( $a->bypassWysiwygEditorRestrictions ) )
            $this->bypass_wysiwyg_editor_restrictions =
                $a->bypassWysiwygEditorRestrictions;
        if( isset( $a->cancelPublishJobs ) )
            $this->cancel_publish_jobs            = $a->cancelPublishJobs;
        if( isset( $a->deleteWorkflows ) )
            $this->delete_workflows               = $a->deleteWorkflows;
        if( isset( $a->diagnosticTests ) )
            $this->diagnostic_tests               = $a->diagnosticTests;
        if( isset( $a->editAccessRights ) )
            $this->edit_access_rights             = $a->editAccessRights;
        if( isset( $a->editDataDefinition ) )
            $this->edit_data_definition           = $a->editDataDefinition;
        if( isset( $a->editPageContentType ) )
            $this->edit_page_content_type         = $a->editPageContentType;
        if( isset( $a->editPageLevelConfigurations ) )
            $this->edit_page_level_configurations = $a->editPageLevelConfigurations;
        if( isset( $a->importZipArchive ) )
            $this->import_zip_archive             = $a->importZipArchive;
        if( isset( $a->moveRenameAssets ) )
            $this->move_rename_assets             = $a->moveRenameAssets;
        if( isset( $a->multiSelectCopy ) )
            $this->multi_select_copy              = $a->multiSelectCopy;
        if( isset( $a->multiSelectDelete ) )
            $this->multi_select_delete            = $a->multiSelectDelete;
        if( isset( $a->multiSelectMove ) )
            $this->multi_select_move              = $a->multiSelectMove;
        if( isset( $a->multiSelectPublish ) )
            $this->multi_select_publish           = $a->multiSelectPublish;
        if( isset( $a->publishReadableAdminAreaAssets ) )
            $this->publish_readable_admin_area_assets =
                $a->publishReadableAdminAreaAssets;
        if( isset( $a->publishReadableHomeAssets ) )
            $this->publish_readable_home_assets   = $a->publishReadableHomeAssets;
        if( isset( $a->publishWritableAdminAreaAssets ) )
            $this->publish_writable_admin_area_assets =
                $a->publishWritableAdminAreaAssets;
        if( isset( $a->publishWritableHomeAssets ) )
            $this->publish_writable_home_assets   = $a->publishWritableHomeAssets;
        if( isset( $a->recycleBinDeleteAssets ) )
            $this->recycle_bin_delete_assets      = $a->recycleBinDeleteAssets;
        if( isset( $a->recycleBinViewRestoreAllAssets ) )
            $this->recycle_bin_view_restore_all_assets =
                $a->recycleBinViewRestoreAllAssets;
        if( isset( $a->recycleBinViewRestoreUserAssets ) )
            $this->recycle_bin_view_restore_user_assets =
                $a->recycleBinViewRestoreUserAssets;
        if( isset( $a->reorderPublishQueue ) )
            $this->reorder_publish_queue          = $a->reorderPublishQueue;
        if( isset( $a->sendStaleAssetNotifications ) )
            $this->send_stale_asset_notifications = $a->sendStaleAssetNotifications;
        if( isset( $a->uploadImagesFromWysiwyg ) )
            $this->upload_images_from_wysiwyg     = $a->uploadImagesFromWysiwyg;
        if( isset( $a->viewPublishQueue ) )
            $this->view_publish_queue             = $a->viewPublishQueue;
        if( isset( $a->viewVersions ) )
            $this->view_versions                  = $a->viewVersions;        
    }

/**
<documentation><description><p>Returns <code>accessAssetFactories</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessAssetFactories() : bool
    {
        return $this->access_asset_factories;
    }

/**
<documentation><description><p>Returns <code>accessConfigurationSets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessAudits() : bool
    {
        return $this->access_audits;
    }
    
/**
<documentation><description><p>Returns <code>accessConfigurationSets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessConfigurationSets() : bool
    {
        return $this->access_configuration_sets;
    }
    
/**
<documentation><description><p>Returns <code>accessConnectors</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessConnectors() : bool
    {
        return $this->access_connectors;
    }

/**
<documentation><description><p>Returns <code>accessContentTypes</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessContentTypes() : bool
    {
        return $this->access_content_types;
    }

/**
<documentation><description><p>Returns <code>accessDataDefinitions</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessDataDefinitions() : bool
    {
        return $this->access_data_definitions;
    }
    
/**
<documentation><description><p>Returns <code>accessDestinations</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessDestinations() : bool
    {
        return $this->access_destinations;
    }

/**
<documentation><description><p>Returns <code>accessEditorConfigurations</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessEditorConfigurations() : bool
    {
        return $this->access_editor_configurations;
    }

/**
<documentation><description><p>Returns <code>accessManageSiteArea</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessManageSiteArea() : bool
    {
        return $this->access_manage_site_area;
    }
    
/**
<documentation><description><p>Returns <code>accessMetadataSets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessMetadataSets() : bool
    {
        return $this->access_metadata_sets;
    }
    
/**
<documentation><description><p>Returns <code>accessPublishSets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessPublishSets() : bool
    {
        return $this->access_publish_sets;
    }
    
/**
<documentation><description><p>Returns <code>accessTransports</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessTransports() : bool
    {
        return $this->access_transports;
    }
    
/**
<documentation><description><p>Returns <code>accessWorkflowDefinitions</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAccessWorkflowDefinitions() : bool
    {
        return $this->access_workflow_definitions;
    }
    
/**
<documentation><description><p>Returns <code>activateDeleteVersions</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getActivateDeleteVersions() : bool
    {
        return $this->activate_delete_versions;
    }
    
/**
<documentation><description><p>Returns <code>alwaysAllowedToToggleDataChecks</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAlwaysAllowedToToggleDataChecks() : bool
    {
        return $this->always_allowed_to_toggle_data_checks;
    }

/**
<documentation><description><p>Returns <code>assignApproveWorkflowSteps</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAssignApproveWorkflowSteps() : bool
    {
        return $this->assign_approve_workflow_steps;
    }
    
/**
<documentation><description><p>Returns <code>assignWorkflowsToFolders</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getAssignWorkflowsToFolders() : bool
    {
        return $this->assign_workflows_to_folders;
    }
    
/**
<documentation><description><p>Returns <code>breakLocks</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBreakLocks() : bool
    {
        return $this->break_locks;
    }
    
/**
<documentation><description><p>Returns <code>brokenLinkReportAccess</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBrokenLinkReportAccess() : bool
    {
        return $this->broken_link_report_access;
    }
    
/**
<documentation><description><p>Returns <code>brokenLinkReportMarkFixed</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBrokenLinkReportMarkFixed() : bool
    {
        return $this->broken_link_report_mark_fixed;
    }
    
/**
<documentation><description><p>Returns <code>bulkChange</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBulkChange() : bool
    {
        return $this->bulk_change;
    }
    
/**
<documentation><description><p>Returns <code>bypassAllPermissionsChecks</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassAllPermissionsChecks() : bool
    {
        return $this->$bypass_all_permissions_checks;
    }
    
/**
<documentation><description><p>Returns <code>bypassAssetFactoryGroupsNewMenu</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassAssetFactoryGroupsNewMenu() : bool
    {
        return $this->bypass_asset_factory_groups_new_menu;
    }
    
/**
<documentation><description><p>Returns <code>bypassDestinationGroupsWhenPublishing</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassDestinationGroupsWhenPublishing() : bool
    {
        return $this->bypass_destination_groups_when_publishing;
    }
    
/**
<documentation><description><p>Returns <code>bypassWorkflow</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassWorkflow() : bool
    {
        return $this->bypass_workflow;
    }
    
/**
<documentation><description><p>Returns <code>bypassWorkflowDefintionGroupsForFolders</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassWorkflowDefintionGroupsForFolders() : bool
    {
        return $this->bypass_workflow_defintion_groups_for_folders;
    }    

/**
<documentation><description><p>Returns <code>bypassWysiwygEditorRestrictions</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getBypassWysiwygEditorRestrictions() : bool
    {
        return $this->bypass_wysiwyg_editor_restrictions;
    }    
    
/**
<documentation><description><p>Returns <code>cancelPublishJobs</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getCancelPublishJobs() : bool
    {
        return $this->cancel_publish_jobs;
    }
    
/**
<documentation><description><p>Returns <code>deleteWorkflows</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getDeleteWorkflows() : bool
    {
        return $this->delete_workflows;
    }
    
/**
<documentation><description><p>Returns <code>diagnosticTests</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getDisgnosticTests() : bool
    {
        return $this->diagnostic_tests;
    }
    
/**
<documentation><description><p>Returns <code>editAccessRights</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditAccessRights() : bool
    {
        return $this->edit_access_rights;
    }
    
/**
<documentation><description><p>Returns <code>editDataDefinition</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditDataDefinition() : bool
    {
        return $this->edit_data_definition;
    }
    
/**
<documentation><description><p>Returns <code>editPageContentType</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditPageContentType() : bool
    {
        return $this->edit_page_content_type;
    }
    
/**
<documentation><description><p>Returns <code>editPageLevelConfigurations</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getEditPageLevelConfigurations() : bool
    {
        return $this->edit_page_level_configurations;
    }
    
/**
<documentation><description><p>Returns <code>importZipArchive</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getImportZipArchive() : bool
    {
        return $this->import_zip_archive;
    }
    
/**
<documentation><description><p>Returns <code>moveRenameAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMoveRenameAssets() : bool
    {
        return $this->move_rename_assets;
    }

/**
<documentation><description><p>Returns <code>multiSelectCopy</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectCopy() : bool
    {
        return $this->multi_select_copy;
    }
    
/**
<documentation><description><p>Returns <code>multiSelectDelete</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectDelete() : bool
    {
        return $this->multi_select_delete;
    }
    
/**
<documentation><description><p>Returns <code>multiSelectMove</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectMove() : bool
    {
        return $this->multi_select_move;
    }
    
/**
<documentation><description><p>Returns <code>multiSelectPublish</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getMultiSelectPublish() : bool
    {
        return $this->multi_select_publish;
    }
    
/**
<documentation><description><p>Returns <code>publishReadableAdminAreaAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishReadableAdminAreaAssets() : bool
    {
        return $this->publish_readable_admin_area_assets;
    }
    
/**
<documentation><description><p>Returns <code>publishReadableHomeAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishReadableHomeAssets() : bool
    {
        return $this->publish_readable_home_assets;
    }
    
/**
<documentation><description><p>Returns <code>publishWritableAdminAreaAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishWritableAdminAreaAssets() : bool
    {
        return $this->publish_writable_admin_area_assets;
    }
    
/**
<documentation><description><p>Returns <code>publishWritableHomeAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPublishWritableHomeAssets() : bool
    {
        return $this->publish_writable_home_assets;
    }

/**
<documentation><description><p>Returns <code>recycleBinDeleteAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRecycleBinDeleteAssets() : bool
    {
        return $this->recycle_bin_delete_assets;
    }
    
/**
<documentation><description><p>Returns <code>recycleBinViewRestoreAllAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRecycleBinViewRestoreAllAssets() : bool
    {
        return $this->recycle_bin_view_restore_all_assets;
    }
    
/**
<documentation><description><p>Returns <code>recycleBinViewRestoreUserAssets</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRecycleBinViewRestoreUserAssets() : bool
    {
        return $this->recycle_bin_view_restore_user_assets;
    }
    
/**
<documentation><description><p>Returns <code>reorderPublishQueue</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getReorderPublishQueue() : bool
    {
        return $this->reorder_publish_queue;
    }
    
/**
<documentation><description><p>Returns <code>sendStaleAssetNotifications</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getSendStaleAssetNotifications() : bool
    {
        return $this->send_stale_asset_notifications;
    }

/**
<documentation><description><p>Returns <code>uploadImagesFromWysiwyg</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getUploadImagesFromWysiwyg() : bool
    {
        return $this->upload_images_from_wysiwyg;
    }
    
/**
<documentation><description><p>Returns <code>viewPublishQueue</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getViewPublishQueue() : bool
    {
        return $this->view_publish_queue;
    }
    
/**
<documentation><description><p>Returns <code>viewVersions</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getViewVersions() : bool
    {
        return $this->view_versions;
    }

/**
<documentation><description><p>Sets <code>accessAssetFactories</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessAssetFactories( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_asset_factories = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessAudits</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessAudits( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_audits = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessConfigurationSets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessConfigurationSets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_configuration_sets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessConnectors</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessConnectors( bool $bool ) : Property
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->access_connectors = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessContentTypes</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessContentTypes( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_content_types = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessDataDefinitions</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessDataDefinitions( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_data_definitions = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessDestinations</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessDestinations( bool $bool ) : Property
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->access_destinations = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessEditorConfigurations</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessEditorConfigurations( bool $bool ) : Property
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->access_editor_configurations = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessManageSiteArea</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/

    public function setAccessManageSiteArea( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_manage_site_area = $bool;
        return $this;
    }
   
/**
<documentation><description><p>Sets <code>accessMetadataSets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessMetadataSets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_metadata_sets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessPublishSets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessPublishSets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_publish_sets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessTransports</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessTransports( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_transports = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessWorkflowDefinitions</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAccessWorkflowDefinitions( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->access_workflow_definitions = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>activateDeleteVersions</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setActivateDeleteVersions( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->activate_delete_versions = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>alwaysAllowedToToggleDataChecks</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAlwaysAllowedToToggleDataChecks( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->always_allowed_to_toggle_data_checks = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>assignApproveWorkflowSteps</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAssignApproveWorkflowSteps( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->assign_approve_workflow_steps = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>assignWorkflowsToFolders</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setAssignWorkflowsToFolders( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->assign_workflows_to_folders = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>breakLocks</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBreakLocks( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->break_locks = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>brokenLinkReportAccess</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBrokenLinkReportAccess( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->broken_link_report_access = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>brokenLinkReportMarkFixed</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBrokenLinkReportMarkFixed( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->broken_link_report_mark_fixed = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>bulkChange</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBulkChange( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bulk_change = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>bypassAllPermissionsChecks</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassAllPermissionsChecks( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_all_permissions_checks = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>bypassAssetFactoryGroupsNewMenu</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassAssetFactoryGroupsNewMenu( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_asset_factory_groups_new_menu = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>bypassDestinationGroupsWhenPublishing</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassDestinationGroupsWhenPublishing( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_destination_groups_when_publishing = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>bypassWorkflow</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassWorkflow( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_workflow = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>bypassWorkflowDefintionGroupsForFolders</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassWorkflowDefintionGroupsForFolders( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_workflow_defintion_groups_for_folders = $bool;
        return $this;
    }    

/**
<documentation><description><p>Sets <code>bypassWysiwygEditorRestrictions</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setBypassWysiwygEditorRestrictions( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->bypass_wysiwyg_editor_restrictions = $bool;
        return $this;
    }    

/**
<documentation><description><p>Sets <code>cancelPublishJobs</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setCancelPublishJobs( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->cancel_publish_jobs = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>deleteWorkflows</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setDeleteWorkflows( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->delete_workflows = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>deleteWorkflows</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setDiagnosticTests( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->diagnostic_tests = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>editDataDefinition</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditAccessRights( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_access_rights = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>editDataDefinition</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditDataDefinition( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_data_definition = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>editPageContentType</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditPageContentType( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_page_content_type = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>editPageLevelConfigurations</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setEditPageLevelConfigurations( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->edit_page_level_configurations = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>importZipArchive</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setImportZipArchive( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->import_zip_archive = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>moveRenameAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMoveRenameAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->move_rename_assets = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>multiSelectCopy</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectCopy( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_copy = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>multiSelectDelete</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectDelete( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_delete = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>multiSelectMove</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectMove( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_move = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>multiSelectPublish</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setMultiSelectPublish( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->multi_select_publish = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>publishReadableAdminAreaAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishReadableAdminAreaAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_readable_admin_area_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>publishReadableHomeAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishReadableHomeAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_readable_home_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>publishWritableAdminAreaAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishWritableAdminAreaAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_writable_admin_area_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>publishWritableHomeAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setPublishWritableHomeAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->publish_writable_home_assets = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>recycleBinDeleteAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setRecycleBinDeleteAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_delete_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>recycleBinViewRestoreAllAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setRecycleBinViewRestoreAllAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_view_restore_all_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>recycleBinViewRestoreUserAssets</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setRecycleBinViewRestoreUserAssets( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->recycle_bin_view_restore_user_assets = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>reorderPublishQueue</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setReorderPublishQueue( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->reorder_publish_queue = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>sendStaleAssetNotifications</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setSendStaleAssetNotifications( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->send_stale_asset_notifications = $bool;
        return $this;
    }

/**
<documentation><description><p>Returns <code>uploadImagesFromWysiwyg</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setUploadImagesFromWysiwyg( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->upload_images_from_wysiwyg = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>viewPublishQueue</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setViewPublishQueue( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_publish_queue = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>viewVersions</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function setViewVersions( bool $bool ) : Property
    {
        $this->checkBoolean( $bool );
        $this->view_versions = $bool;
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

        $obj->accessAssetFactories                    = $this->access_asset_factories;
        $obj->accessConfigurationSets                 = $this->access_configuration_sets;
        $obj->accessConnectors                        = $this->access_connectors;
        $obj->accessContentTypes                      = $this->access_content_types;
        $obj->accessDataDefinitions                   = $this->access_data_definitions;
        $obj->accessDestinations                      = $this->access_destinations;
        $obj->accessEditorConfigurations              =
            $this->access_editor_configurations;
        $obj->accessManageSiteArea                    = $this->access_manage_site_area;
        $obj->accessMetadataSets                      = $this->access_metadata_sets;
        $obj->accessPublishSets                       = $this->access_publish_sets;
        $obj->accessTransports                        = $this->access_transports;
        $obj->accessWorkflowDefinitions               =
            $this->access_workflow_definitions;
        $obj->activateDeleteVersions                  = $this->activate_delete_versions;
        $obj->alwaysAllowedToToggleDataChecks         =
            $this->always_allowed_to_toggle_data_checks;
        $obj->assignApproveWorkflowSteps              =
            $this->assign_approve_workflow_steps;
        $obj->assignWorkflowsToFolders                =
            $this->assign_workflows_to_folders;
        $obj->breakLocks                              = $this->break_locks;
        $obj->brokenLinkReportAccess                  = $this->broken_link_report_access;
        $obj->brokenLinkReportMarkFixed               =
            $this->broken_link_report_mark_fixed;
        $obj->bulkChange                              = $this->bulk_change;
        $obj->bypassAssetFactoryGroupsNewMenu         =
            $this->bypass_asset_factory_groups_new_menu;
        $obj->bypassDestinationGroupsWhenPublishing   =
            $this->bypass_destination_groups_when_publishing;
        $obj->bypassWorkflow                          = $this->bypass_workflow;
        $obj->bypassWorkflowDefintionGroupsForFolders =
            $this->bypass_workflow_defintion_groups_for_folders;
        $obj->bypassWysiwygEditorRestrictions         =
            $this->bypass_wysiwyg_editor_restrictions;
        $obj->cancelPublishJobs                       = $this->cancel_publish_jobs;
        $obj->deleteWorkflows                         = $this->delete_workflows;
        $obj->diagnosticTests                         = $this->diagnostic_tests;
        $obj->editDataDefinition                      = $this->edit_data_definition;
        $obj->editPageContentType                     = $this->edit_page_content_type;
        $obj->editPageLevelConfigurations             =
            $this->edit_page_level_configurations;
        $obj->importZipArchive                        = $this->import_zip_archive;
        $obj->moveRenameAssets                        = $this->move_rename_assets;
        $obj->multiSelectCopy                         = $this->multi_select_copy;
        $obj->multiSelectDelete                       = $this->multi_select_delete;
        $obj->multiSelectMove                         = $this->multi_select_move;
        $obj->multiSelectPublish                      = $this->multi_select_publish;
        $obj->publishReadableAdminAreaAssets          =
            $this->publish_readable_admin_area_assets;
        $obj->publishReadableHomeAssets               =
            $this->publish_readable_home_assets;
        $obj->publishWritableAdminAreaAssets          =
            $this->publish_writable_admin_area_assets;
        $obj->publishWritableHomeAssets               =
            $this->publish_writable_home_assets;
        $obj->recycleBinDeleteAssets                  = $this->recycle_bin_delete_assets;
        $obj->recycleBinViewRestoreAllAssets          =
            $this->recycle_bin_view_restore_all_assets;
        $obj->recycleBinViewRestoreUserAssets         =
            $this->recycle_bin_view_restore_user_assets;
        $obj->reorderPublishQueue                     = $this->reorder_publish_queue;
        $obj->sendStaleAssetNotifications             =
            $this->send_stale_asset_notifications;
        $obj->uploadImagesFromWysiwyg                 = $this->upload_images_from_wysiwyg;
        $obj->viewVersions                            = $this->view_versions;
        $obj->viewPublishQueue                        = $this->view_publish_queue;

        return $obj;
    }
    
    private function checkBoolean( bool $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
    }

    private $access_asset_factories;
    private $access_audits;
    private $access_configuration_sets;
    private $access_connectors;
    private $access_content_types;
    private $access_data_definitions;
    private $access_destinations;
    private $access_editor_configurations;
    private $access_manage_site_area;
    private $access_metadata_sets;
    private $access_publish_sets;
    private $access_transports;
    private $access_workflow_definitions;
    private $activate_delete_versions;
    private $always_allowed_to_toggle_data_checks;
    private $assign_approve_workflow_steps;
    private $assign_workflows_to_folders;
    private $break_locks;
    private $broken_link_report_access;
    private $broken_link_report_mark_fixed;
    private $bulk_change;
    private $bypass_all_permissions_checks;
    private $bypass_asset_factory_groups_new_menu;
    private $bypass_destination_groups_when_publishing;
    private $bypass_workflow;
    private $bypass_workflow_defintion_groups_for_folders;
    private $bypass_wysiwyg_editor_restrictions;
    private $cancel_publish_jobs;
    private $delete_workflows;
    private $diagnostic_tests;
    private $edit_access_rights;
    private $edit_data_definition;
    private $edit_page_content_type;
    private $edit_page_level_configurations;
    private $import_zip_archive;
    private $move_rename_assets;
    private $multi_select_copy;
    private $multi_select_delete;
    private $multi_select_move;
    private $multi_select_publish;
    private $publish_readable_admin_area_assets;
    private $publish_readable_home_assets;
    private $publish_writable_admin_area_assets;
    private $publish_writable_home_assets;
    private $recycle_bin_delete_assets;
    private $recycle_bin_view_restore_all_assets;
    private $recycle_bin_view_restore_user_assets;
    private $reorder_publish_queue;
    private $send_stale_asset_notifications;
    private $upload_images_from_wysiwyg;
    private $view_publish_queue;
    private $view_versions;
}
?>