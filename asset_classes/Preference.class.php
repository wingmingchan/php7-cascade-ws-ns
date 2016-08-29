<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2015 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 7/6/2015 File created.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class Preference
{
    const DEBUG = true;
    const DUMP  = true;
    
    const ALLOW_FONT_ASSIGNMENT                      = "system_pref_allow_font_assignment"; // on, off
    // Label : Content Formatting
    const ALLOW_FONT_FORMATTING                      = "system_pref_allow_font_formatting"; // on, off
    const ALLOW_TEXT_FORMATTING                      = "system_pref_allow_text_formatting"; // on, off
    const ASSET_TREE_MODE                            = "system_pref_asset_tree_mode"; // normal, fastest
    const AVAILABLE_ASST_FACTORY_PLUGINS             = "system_pref_available_asset_factory_plugins";
    const AVAILABLE_WORKFLOW_TRIGGERS                = "system_pref_available_workflow_triggers";
    const CHECK_EXTERNAL_LINKS                       = "system_pref_check_external_links"; // on, off
    const EXTERNAL_LINKS_TIMEOUT                     = "system_pref_check_external_links_timeout";
    const CHOOSER_HEIGHT                             = "system_pref_chooser_height";
    const CHOOSER_WIDTH                              = "system_pref_chooser_width";
    const CSS_CLASSES                                = "system_pref_css_classes";
    const DEFAULT_508_COMPLIANCE_BEHAVIOR            = "system_pref_default_508_compliance_behavior"; // on, off
    const DEFAULT_LANGUAGE                           = "system_pref_default_language";
    const DEFAULT_LINK_CHECK_BEHAVIOR                = "system_pref_default_link_check_behavior"; // on, off
    const DEFAULT_SPELLCHECK_BEHAVIOR                = "system_pref_default_spellcheck_behavior"; // on, off
    const DEFAULT_TIDY_BEHAVIOR                      = "system_pref_default_tidy_behavior"; // on, off
    const DISABLE_TABLE_CTRLS                        = "system_pref_disable_table_ctrls"; // on, off
    const EDITABLE_FILE_EXTENSIONS                   = "system_pref_editable_file_extensions";
    const EMAIL_MAX_BATCH_SIZE                       = "system_pref_email_max_batch_size";
    const EMAIL_MAX_WAIT_TIME                        = "system_pref_email_max_wait_time";
    const ENABLE_FOLDER_GALLERY                      = "system_pref_enable_folder_gallery"; // true, false
    const ENABLE_SMART_PUBLISH                       = "system_pref_enable_smart_publish"; // on, off
    const EXPIRATION_FIRST_WARNING_DAYS              = "system_pref_expiration_first_warning_days";
    const EXPIRATION_SECOND_WARNING_DAYS             = "system_pref_expiration_second_warning_days";
    const LICENSE_EXPIRATION_NOTIFICATION            = "system_pref_license_expiration_notification";
    const MAX_NUM_VERSIONS                           = "system_pref_max_num_versions";
    const MAX_RENDERED_ENTITIES                      = "system_pref_max_rendered_entities";
    const MAX_UPLOAD_IN_KB                           = "system_pref_max_upload_in_kb";
    const OPTIMIZE_OFFICE_HTML                       = "system_pref_optimize_office_html"; // on, off
    const ORGANIZATION_NAME                          = "system_pref_organization_name";
    const PLAIN_TEXT_FILE_EXTENSIONS                 = "system_pref_plain_text_file_extensions";
    const PUBLISHER_CONCURRENCY                      = "system_pref_publisher_concurrency";
    const PUBLISHER_JOB_TIMEOUT                      = "system_pref_publisher_job_timeout";
    const REMOVE_FONT_AND_STYLE                      = "system_pref_remove_font_and_style"; // on, off
    const REPORT_RELATIVE_LINKS_AS_ERRORS            = "system_pref_report_relative_links_as_errors"; // on, off
    const SEARCH_INDEX_LOCATION                      = "system_pref_search_indexlocation"; // indexes
    const SEARCH_RESULTS_PER_TYPE                    = "system_pref_search_results_per_type";
    const SESSION_TIMEOUT                            = "system_pref_session_timeout";
    const SIMPLE_INTERFACE_STARTING_PAGE             = "system_pref_simple_interface_starting_page";
    const SITEWIDE_CSS_FILE                          = "system_pref_sitewide_css_file"; // ID
    const SMTP_EMAIL_ADDRESS                         = "system_pref_smtp_email_address";
    const SMTP_HOST                                  = "system_pref_smtp_host";
    const SMTP_PASSWORD                              = "system_pref_smtp_password";
    const SMTP_PORT                                  = "system_pref_smtp_port";
    const SMTP_USERNAME                              = "system_pref_smtp_username";
    const SYSTEM_KEYWORDS                            = "system_pref_system_keywords";
    const SYSTEM_URL                                 = "system_pref_system_url";
    const ULTRA_SIMPLE_INTERFACE                     = "system_pref_ultra_simple_interface"; // on, off
    const XALAN_JAVA_EXTENSIONS                      = "system_pref_xalan_java_extensions"; // on, off
    const INDEX_BLOCK_RENDERING_CACHE                = "system_pref_index_block_rendering_cache"; // on, off
    const MAX_INDEX_BLOCK_SIZE                       = "system_pref_max_index_block_size";
    const XALAN_JAVASCRIPT_EXTENSIONS                = "system_pref_xalan_javascript_extensions"; // on, off
    const RECYCLE_BIN_EXPIRATION                     = "system_pref_recycle_bin_expiration";
    const USE_OLD_CACHE                              = "system_pref_use_old_cache"; // on, off
    const UNPUBLISH_GLOBAL_ON_EXPIRATION             = "system_pref_unpublish_global_on_expiration"; // on, off
    const LINK_CHECKER_FREQUENCY                     = "system_pref_link_checker_frequency"; // daily
    const LINK_CHECKER_FREQUENCY_DAYS                = "system_pref_link_checker_frequency_days";
    const LINK_CHECKER_FREQUENCY_TIME                = "system_pref_link_checker_frequency_time";
    const GLOBAL_AREA_LINK_CHECKER_ENABLED           = "system_pref_global_area_link_checker_enabled"; // on, off
    const EDITABLE_IMAGE_FILE_EXTENSIONS             = "system_pref_editable_image_file_extensions";
    const SITEWIDE_CSS_FILE_TEXT                     = "system_pref_sitewide_css_fileText";
    const ALLOW_TABLE_EDITING                        = "system_pref_allow_table_editing";
    const TEMPLATE_CREATE_BLOCK_FOLDER               = "system_pref_template_create_block_folder";
    const TEMPLATE_CREATE_BLOCK_FOLDER_TEXT          = "system_pref_template_create_block_folderText";
    const GLOBAL_AREA_EXTERNAL_LINK_CHECK_ON_PUBLISH = "system_pref_global_area_external_link_check_on_publish";
    const LIST_SIZE                                  = "system_pref_list_size";
    const UNPUBLISH_BY_DEFAULT_ON_DELETE             = "system_pref_unpublish_by_default_on_delete";

    const ALLOW_FONT_ASSIGNMENT_MSG                      = "Allow font assignment";
    const ALLOW_FONT_FORMATTING_MSG                      = "Allow text formatting";
    const ALLOW_TEXT_FORMATTING_MSG                      = "Allow font formatting";
    const ASSET_TREE_MODE_MSG                            = "Asset tree mode";
    const AVAILABLE_ASST_FACTORY_PLUGINS_MSG             = "Available asset factory plugins";
    const AVAILABLE_WORKFLOW_TRIGGERS_MSG                = "Available workflow triggers";
    const CHECK_EXTERNAL_LINKS_MSG                       = "Check external links";
    const EXTERNAL_LINKS_TIMEOUT_MSG                     = "Check external links timeout";
    const CHOOSER_HEIGHT_MSG                             = "Chooser height";
    const CHOOSER_WIDTH_MSG                              = "Chooser width";
    const CSS_CLASSES_MSG                                = "CSS classes";
    const DEFAULT_508_COMPLIANCE_BEHAVIOR_MSG            = "Default 508 compliance behavior";
    const DEFAULT_LANGUAGE_MSG                           = "Default language";
    const DEFAULT_LINK_CHECK_BEHAVIOR_MSG                = "Default link check behavior";
    const DEFAULT_SPELLCHECK_BEHAVIOR_MSG                = "Default spellcheck behavior";
    const DEFAULT_TIDY_BEHAVIOR_MSG                      = "Default tidy behavior";
    const DISABLE_TABLE_CTRLS_MSG                        = "Disable table ctrls";
    const EDITABLE_FILE_EXTENSIONS_MSG                   = "Editable file extensions";
    const EMAIL_MAX_BATCH_SIZE_MSG                       = "Email max batch size";
    const EMAIL_MAX_WAIT_TIME_MSG                        = "Email max wait time";
    const ENABLE_FOLDER_GALLERY_MSG                      = "Enable folder gallery";
    const ENABLE_SMART_PUBLISH_MSG                       = "Enable smart publish";
    const EXPIRATION_FIRST_WARNING_DAYS_MSG              = "Expiration first warning days";
    const EXPIRATION_SECOND_WARNING_DAYS_MSG             = "Expiration second warning days";
    const LICENSE_EXPIRATION_NOTIFICATION_MSG            = "License expiration notification";
    const MAX_NUM_VERSIONS_MSG                           = "Max num versions";
    const MAX_RENDERED_ENTITIES_MSG                      = "Max rendered entities";
    const MAX_UPLOAD_IN_KB_MSG                           = "Max upload in kb";
    const OPTIMIZE_OFFICE_HTML_MSG                       = "Optimize office HTML";
    const ORGANIZATION_NAME_MSG                          = "Organization name";
    const PLAIN_TEXT_FILE_EXTENSIONS_MSG                 = "Plain text file extensions";
    const PUBLISHER_CONCURRENCY_MSG                      = "Publisher concurrency";
    const PUBLISHER_JOB_TIMEOUT_MSG                      = "Publisher job timeout";
    const REMOVE_FONT_AND_STYLE_MSG                      = "Remove font and style";
    const REPORT_RELATIVE_LINKS_AS_ERRORS_MSG            = "Report relative links as errors";
    const SEARCH_INDEX_LOCATION_MSG                      = "Search indexlocation";
    const SEARCH_RESULTS_PER_TYPE_MSG                    = "Search results per type";
    const SESSION_TIMEOUT_MSG                            = "Session timeout";
    const SIMPLE_INTERFACE_STARTING_PAGE_MSG             = "Simple interface starting page";
    const SITEWIDE_CSS_FILE_MSG                          = "Sitewide CSS file";
    const SMTP_EMAIL_ADDRESS_MSG                         = "SMTP email address";
    const SMTP_HOST_MSG                                  = "SMTP host";
    const SMTP_PASSWORD_MSG                              = "SMTP password";
    const SMTP_PORT_MSG                                  = "SMTP port";
    const SMTP_USERNAME_MSG                              = "SMTP username";
    const SYSTEM_KEYWORDS_MSG                            = "System keywords";
    const SYSTEM_URL_MSG                                 = "System URL";
    const ULTRA_SIMPLE_INTERFACE_MSG                     = "Ultra simple interface";
    const XALAN_JAVA_EXTENSIONS_MSG                      = "Xalan java extensions";
    const INDEX_BLOCK_RENDERING_CACHE_MSG                = "Index block rendering cache";
    const MAX_INDEX_BLOCK_SIZE_MSG                       = "Max index block size";
    const XALAN_JAVASCRIPT_EXTENSIONS_MSG                = "Xalan javascript extensions";
    const RECYCLE_BIN_EXPIRATION_MSG                     = "Recycle bin expiration";
    const USE_OLD_CACHE_MSG                              = "Use old cache";
    const UNPUBLISH_GLOBAL_ON_EXPIRATION_MSG             = "Unpublish global on expiration";
    const LINK_CHECKER_FREQUENCY_MSG                     = "Link checker frequency";
    const LINK_CHECKER_FREQUENCY_DAYS_MSG                = "Link checker frequency days";
    const LINK_CHECKER_FREQUENCY_TIME_MSG                = "Link checker frequency time";
    const GLOBAL_AREA_LINK_CHECKER_ENABLED_MSG           = "Global area link checker enabled";
    const EDITABLE_IMAGE_FILE_EXTENSIONS_MSG             = "Editable image file extensions";
    const SITEWIDE_CSS_FILE_TEXT_MSG                     = "Sitewide css file text";
    const ALLOW_TABLE_EDITING_MSG                        = "Allow table editing";
    const TEMPLATE_CREATE_BLOCK_FOLDER_MSG               = "Template create block folder";
    const TEMPLATE_CREATE_BLOCK_FOLDER_TEXT_MSG          = "Template create block folder text";
    const GLOBAL_AREA_EXTERNAL_LINK_CHECK_ON_PUBLISH_MSG = "Global area external link check on publish";
    const LIST_SIZE_MSG                                  = "List size";
    const UNPUBLISH_BY_DEFAULT_ON_DELETE_MSG             = "Unpublish by default on delete";

    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $preference_std )
    {
        if( $service == NULL )
        {
            throw new e\NullServiceException(
                S_SPAN . c\M::NULL_SERVICE . E_SPAN );
        }
        
        if( $preference_std == NULL )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::NULL_PREFERENCE . E_SPAN );
        }
        
        //if( self::DEBUG && self::dump ) { u\DebugUtility::dump( $preference_std ); }
        
        $this->service        = $service;
        $this->preference_std = $preference_std;
        $this->map            = array();
        $this->names          = array();
        
        $this->processPreferences( $this->preference_std );
        //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $this->names ); }
    }
    
    public function display()
    {
        echo "<table  class='preferences' summary='Preferences'>" .
            "<caption>System Preferences</caption>" .
            "<tr class='preferences-header'><th>Name</th><th>Value</th></tr>" .
            
            "<tr><td>" . self::ALLOW_FONT_ASSIGNMENT_MSG .
            "</td><td>" . $this->map[ self::ALLOW_FONT_ASSIGNMENT ] . "</td></tr>" .
            
            "<tr><td>" .  self::ALLOW_FONT_FORMATTING_MSG .
            "</td><td>" . $this->map[ self::ALLOW_FONT_FORMATTING ] . "</td></tr>" .
            
            "<tr><td>" .  self::ALLOW_TEXT_FORMATTING_MSG .
            "</td><td>" . $this->map[ self::ALLOW_TEXT_FORMATTING ] . "</td></tr>" .
            
            "<tr><td>" .  self::ASSET_TREE_MODE_MSG .
            "</td><td>" . $this->map[ self::ASSET_TREE_MODE ] . "</td></tr>" .
            
            "<tr><td>" .  self::ASSET_TREE_MODE_MSG .
            "</td><td>" . $this->map[ self::AVAILABLE_ASST_FACTORY_PLUGINS ] . "</td></tr>" .
            
            "<tr><td>" .  self::AVAILABLE_WORKFLOW_TRIGGERS_MSG .
            "</td><td>" . $this->map[ self::AVAILABLE_WORKFLOW_TRIGGERS ] . "</td></tr>" .
            
            "<tr><td>" .  self::CHECK_EXTERNAL_LINKS_MSG .
            "</td><td>" . $this->map[ self::CHECK_EXTERNAL_LINKS ] . "</td></tr>" .
            
            "<tr><td>" .  self::EXTERNAL_LINKS_TIMEOUT_MSG .
            "</td><td>" . $this->map[ self::EXTERNAL_LINKS_TIMEOUT ] . "</td></tr>" .
            
            "<tr><td>" .  self::CHOOSER_HEIGHT_MSG .
            "</td><td>" . $this->map[ self::CHOOSER_HEIGHT ] . "</td></tr>" .
            
            "<tr><td>" .  self::CHOOSER_WIDTH_MSG .
            "</td><td>" . $this->map[ self::CHOOSER_WIDTH ] . "</td></tr>" .
            
            "<tr><td>" .  self::CSS_CLASSES_MSG .
            "</td><td>" . $this->map[ self::CSS_CLASSES ] . "</td></tr>" .
            
            "<tr><td>" .  self::DEFAULT_508_COMPLIANCE_BEHAVIOR_MSG .
            "</td><td>" . $this->map[ self::DEFAULT_508_COMPLIANCE_BEHAVIOR ] . "</td></tr>" .
            
            "<tr><td>" .  self::DEFAULT_LANGUAGE_MSG .
            "</td><td>" . $this->map[ self::DEFAULT_LANGUAGE ] . "</td></tr>" .
            
            "<tr><td>" .  self::DEFAULT_LINK_CHECK_BEHAVIOR_MSG .
            "</td><td>" . $this->map[ self::DEFAULT_LINK_CHECK_BEHAVIOR ] . "</td></tr>" .
            
            "<tr><td>" .  self::DEFAULT_SPELLCHECK_BEHAVIOR_MSG .
            "</td><td>" . $this->map[ self::DEFAULT_SPELLCHECK_BEHAVIOR ] . "</td></tr>" .
            
            "<tr><td>" .  self::DEFAULT_TIDY_BEHAVIOR_MSG .
            "</td><td>" . $this->map[ self::DEFAULT_TIDY_BEHAVIOR ] . "</td></tr>" .
            
            "<tr><td>" .  self::DISABLE_TABLE_CTRLS_MSG .
            "</td><td>" . $this->map[ self::DISABLE_TABLE_CTRLS ] . "</td></tr>" .
            
            "<tr><td>" .  self::EDITABLE_FILE_EXTENSIONS_MSG .
            "</td><td>" . $this->map[ self::EDITABLE_FILE_EXTENSIONS ] . "</td></tr>" .
            
            "<tr><td>" .  self::EMAIL_MAX_BATCH_SIZE_MSG .
            "</td><td>" . $this->map[ self::EMAIL_MAX_BATCH_SIZE ] . "</td></tr>" .
            
            "<tr><td>" .  self::EMAIL_MAX_WAIT_TIME_MSG .
            "</td><td>" . $this->map[ self::EMAIL_MAX_WAIT_TIME ] . "</td></tr>" .
            
            "<tr><td>" .  self::ENABLE_FOLDER_GALLERY_MSG .
            "</td><td>" . $this->map[ self::ENABLE_FOLDER_GALLERY ] . "</td></tr>" .
            
            "<tr><td>" .  self::ENABLE_SMART_PUBLISH_MSG .
            "</td><td>" . $this->map[ self::ENABLE_SMART_PUBLISH ] . "</td></tr>" .
            
            "<tr><td>" .  self::EXPIRATION_FIRST_WARNING_DAYS_MSG .
            "</td><td>" . $this->map[ self::EXPIRATION_FIRST_WARNING_DAYS ] . "</td></tr>" .
            
            "<tr><td>" .  self::EXPIRATION_SECOND_WARNING_DAYS_MSG .
            "</td><td>" . $this->map[ self::EXPIRATION_SECOND_WARNING_DAYS ] . "</td></tr>" .
            
            "<tr><td>" .  self::LICENSE_EXPIRATION_NOTIFICATION_MSG .
            "</td><td>" . $this->map[ self::LICENSE_EXPIRATION_NOTIFICATION ] . "</td></tr>" .
            
            "<tr><td>" .  self::MAX_NUM_VERSIONS_MSG .
            "</td><td>" . $this->map[ self::MAX_NUM_VERSIONS ] . "</td></tr>" .
            
            "<tr><td>" .  self::MAX_RENDERED_ENTITIES_MSG .
            "</td><td>" . $this->map[ self::MAX_RENDERED_ENTITIES ] . "</td></tr>" .
            
            "<tr><td>" .  self::MAX_UPLOAD_IN_KB_MSG .
            "</td><td>" . $this->map[ self::MAX_UPLOAD_IN_KB ] . "</td></tr>" .
            
            "<tr><td>" .  self::OPTIMIZE_OFFICE_HTML_MSG .
            "</td><td>" . $this->map[ self::OPTIMIZE_OFFICE_HTML ] . "</td></tr>" .
            
            "<tr><td>" .  self::OPTIMIZE_OFFICE_HTML_MSG .
            "</td><td>" . $this->map[ self::OPTIMIZE_OFFICE_HTML ] . "</td></tr>" .
            
            "<tr><td>" .  self::OPTIMIZE_OFFICE_HTML_MSG .
            "</td><td>" . $this->map[ self::OPTIMIZE_OFFICE_HTML ] . "</td></tr>" .
            
            "<tr><td>" .  self::ORGANIZATION_NAME_MSG .
            "</td><td>" . $this->map[ self::ORGANIZATION_NAME ] . "</td></tr>" .
            
            "<tr><td>" .  self::PLAIN_TEXT_FILE_EXTENSIONS_MSG .
            "</td><td>" . $this->map[ self::PLAIN_TEXT_FILE_EXTENSIONS ] . "</td></tr>" .
            
            "<tr><td>" .  self::PUBLISHER_CONCURRENCY_MSG .
            "</td><td>" . $this->map[ self::PUBLISHER_CONCURRENCY ] . "</td></tr>" .
            
            "<tr><td>" .  self::PUBLISHER_JOB_TIMEOUT_MSG .
            "</td><td>" . $this->map[ self::PUBLISHER_JOB_TIMEOUT ] . "</td></tr>" .
            
            "<tr><td>" .  self::REMOVE_FONT_AND_STYLE_MSG .
            "</td><td>" . $this->map[ self::REMOVE_FONT_AND_STYLE ] . "</td></tr>" .
            
            "<tr><td>" .  self::REPORT_RELATIVE_LINKS_AS_ERRORS_MSG .
            "</td><td>" . $this->map[ self::REPORT_RELATIVE_LINKS_AS_ERRORS ] . "</td></tr>" .
            
            "<tr><td>" .  self::SEARCH_INDEX_LOCATION_MSG .
            "</td><td>" . $this->map[ self::SEARCH_INDEX_LOCATION ] . "</td></tr>" .
            
            "<tr><td>" .  self::SEARCH_RESULTS_PER_TYPE_MSG .
            "</td><td>" . $this->map[ self::SEARCH_RESULTS_PER_TYPE ] . "</td></tr>" .
            
            "<tr><td>" .  self::SESSION_TIMEOUT_MSG .
            "</td><td>" . $this->map[ self::SESSION_TIMEOUT ] . "</td></tr>" .
            
            "<tr><td>" .  self::SIMPLE_INTERFACE_STARTING_PAGE_MSG .
            "</td><td>" . $this->map[ self::SIMPLE_INTERFACE_STARTING_PAGE ] . "</td></tr>" .
            
            "<tr><td>" .  self::SITEWIDE_CSS_FILE_MSG .
            "</td><td>" . $this->map[ self::SITEWIDE_CSS_FILE ] . "</td></tr>" .
            
            "<tr><td>" .  self::SMTP_EMAIL_ADDRESS_MSG .
            "</td><td>" . $this->map[ self::SMTP_EMAIL_ADDRESS ] . "</td></tr>" .
            
            "<tr><td>" .  self::SMTP_HOST_MSG .
            "</td><td>" . $this->map[ self::SMTP_HOST ] . "</td></tr>" .
            
            "<tr><td>" .  self::SMTP_PASSWORD_MSG .
            "</td><td>" . $this->map[ self::SMTP_PASSWORD ] . "</td></tr>" .
            
            "<tr><td>" .  self::SMTP_PORT_MSG .
            "</td><td>" . $this->map[ self::SMTP_PORT ] . "</td></tr>" .
            
            "<tr><td>" .  self::SMTP_USERNAME_MSG .
            "</td><td>" . $this->map[ self::SMTP_USERNAME ] . "</td></tr>" .
            
            "<tr><td>" .  self::SYSTEM_KEYWORDS_MSG .
            "</td><td>" . $this->map[ self::SYSTEM_KEYWORDS ] . "</td></tr>" .
            
            "<tr><td>" .  self::SYSTEM_URL_MSG .
            "</td><td>" . $this->map[ self::SYSTEM_URL ] . "</td></tr>" .
            
            "<tr><td>" .  self::ULTRA_SIMPLE_INTERFACE_MSG .
            "</td><td>" . $this->map[ self::ULTRA_SIMPLE_INTERFACE ] . "</td></tr>" .
            
            "<tr><td>" .  self::XALAN_JAVA_EXTENSIONS_MSG .
            "</td><td>" . $this->map[ self::XALAN_JAVA_EXTENSIONS ] . "</td></tr>" .
            
            "<tr><td>" .  self::INDEX_BLOCK_RENDERING_CACHE_MSG .
            "</td><td>" . $this->map[ self::INDEX_BLOCK_RENDERING_CACHE ] . "</td></tr>" .
            
            "<tr><td>" .  self::MAX_INDEX_BLOCK_SIZE_MSG .
            "</td><td>" . $this->map[ self::MAX_INDEX_BLOCK_SIZE ] . "</td></tr>" .
            
            "<tr><td>" .  self::XALAN_JAVASCRIPT_EXTENSIONS_MSG .
            "</td><td>" . $this->map[ self::XALAN_JAVASCRIPT_EXTENSIONS ] . "</td></tr>" .
            
            "<tr><td>" .  self::RECYCLE_BIN_EXPIRATION_MSG .
            "</td><td>" . $this->map[ self::RECYCLE_BIN_EXPIRATION ] . "</td></tr>" .
            
            "<tr><td>" .  self::USE_OLD_CACHE_MSG .
            "</td><td>" . $this->map[ self::USE_OLD_CACHE ] . "</td></tr>" .
            
            "<tr><td>" .  self::UNPUBLISH_GLOBAL_ON_EXPIRATION_MSG .
            "</td><td>" . $this->map[ self::UNPUBLISH_GLOBAL_ON_EXPIRATION ] . "</td></tr>" .
            
            "<tr><td>" .  self::LINK_CHECKER_FREQUENCY_MSG .
            "</td><td>" . $this->map[ self::LINK_CHECKER_FREQUENCY ] . "</td></tr>" .
            
            "<tr><td>" .  self::LINK_CHECKER_FREQUENCY_DAYS_MSG .
            "</td><td>" . $this->map[ self::LINK_CHECKER_FREQUENCY_DAYS ] . "</td></tr>" .
            
            "<tr><td>" .  self::LINK_CHECKER_FREQUENCY_TIME_MSG .
            "</td><td>" . $this->map[ self::LINK_CHECKER_FREQUENCY_TIME ] . "</td></tr>" .
            
            "<tr><td>" .  self::GLOBAL_AREA_LINK_CHECKER_ENABLED_MSG .
            "</td><td>" . $this->map[ self::GLOBAL_AREA_LINK_CHECKER_ENABLED ] . "</td></tr>" .
            
            "<tr><td>" .  self::EDITABLE_IMAGE_FILE_EXTENSIONS_MSG .
            "</td><td>" . $this->map[ self::EDITABLE_IMAGE_FILE_EXTENSIONS ] . "</td></tr>" .
            
            "<tr><td>" .  self::SITEWIDE_CSS_FILE_TEXT_MSG .
            "</td><td>" . $this->map[ self::SITEWIDE_CSS_FILE_TEXT ] . "</td></tr>" .
            
            "<tr><td>" .  self::ALLOW_TABLE_EDITING_MSG .
            "</td><td>" . $this->map[ self::ALLOW_TABLE_EDITING ] . "</td></tr>" .
            
            "<tr><td>" .  self::TEMPLATE_CREATE_BLOCK_FOLDER_MSG .
            "</td><td>" . $this->map[ self::TEMPLATE_CREATE_BLOCK_FOLDER ] . "</td></tr>" .
            
            "<tr><td>" .  self::GLOBAL_AREA_EXTERNAL_LINK_CHECK_ON_PUBLISH_MSG .
            "</td><td>" . $this->map[ self::GLOBAL_AREA_EXTERNAL_LINK_CHECK_ON_PUBLISH ] . "</td></tr>" .
            
            "<tr><td>" .  self::LIST_SIZE_MSG .
            "</td><td>" . $this->map[ self::LIST_SIZE ] . "</td></tr>" .
            
            "<tr><td>" .  self::UNPUBLISH_BY_DEFAULT_ON_DELETE_MSG .
            "</td><td>" . $this->map[ self::UNPUBLISH_BY_DEFAULT_ON_DELETE ] . "</td></tr>" .
            
            "<tr><td>" .  "A_dummy_string_to_push_the_content_to_the_right" .
            "</td><td>" . "" . "</td></tr>" .
            
            "</table>" . HR ;
        return $this;
    }
    
    public function dump( $formatted=false )
    {
        if( $formatted ) echo S_H2 . c\L::READ_DUMP . E_H2 . S_PRE;
        var_dump( $this->preference_std );
        if( $formatted ) echo E_PRE . HR;
        
        return $this;
    }

    public function getValue( $name )
    {
        if( !in_array( $name, $this->names ) )
            throw new e\NoSuchNameException( "The name $name does not exist. " );
            
        return $this->map[ $name ];
    }
  
    public function toStdClass()
    {
        return $this->preference_std;
    }
    
    public function setValue( $name, $value )
    {
        if( !in_array( $name, $this->names ) )
            throw new e\NoSuchNameException( "The name $name does not exist. " );

        $this->service->editPreferences( $name, $value );
        $this->reloadPreferences();
        return $this;
    }
    
    private function reloadPreferences()
    {
        $this->service->readPreferences();
        $this->preference_std = $this->service->getPreferences();
        $this->processPreferences( $this->preference_std );
    }
    
    private function processPreferences( \stdClass $preference_std )
    {
        if( isset( $preference_std->preference ) && is_array( $preference_std->preference ) )
        {
            $pref_array = $preference_std->preference;
            
            //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $pref_array ); }
            
            foreach( $pref_array as $pref )
            {
                $this->map[ $pref->name ] = $pref->value;
            }
            
            $this->names = array_keys( $this->map );
        }
    }
    
    private $service;
    private $preference_std;
    private $map;
    private $names;
}
?>