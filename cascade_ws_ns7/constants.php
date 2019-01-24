<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  *  11/2/2018 Added constants related to shared fields.
  *  2/2/2018 Added NULL_CASCADE.
  *  9/28/2017 Added cloud transport-related constants.
  *  9/27/2017 Added cloud transport-related constants.
  *  9/18/2017 Added constants for 8.6.
  *  11/1/2016 Added PAGE_METADATA_SET.
  *  9/26/2016 Added EXPIRATION_FOLDER_RECYCLED.
  *  8/14/2016 Added INFORMATION_NOT_AVAILABLE.
  *  8/13/2016 Added more HTML constants used in ReflectionUtility.
  *  6/2/2016 Added constants related to types of structured data nodes.
  *  5/6/2016 Added S_H3, E_H3, S_OL, E_OL.
  *  12/10/2014 Added constants JSON, JS, CSS.
  *  9/25/2014 Added WorkflowModeValues class.
  *  5/14/2014 Added S class and SearchTypes.
  *  5/9/2014 Added AuditTypes.
  *  5/6/2014 Added constants related to orphan handling.
  *  5/2/2014 Added constants related to asset factory-group association.
  *  4/25/2014 Fixed a bug in getType.
  *  4/2/2014 Added LevelValues class.
  *  3/17/2014 Added F class.
  *  3/12/2014 Added T::$type_property_name_map, M class, L class.
  *  3/10/2014 Added T::getTypeArray().
  *  3/7/2014 Added $type_class_name_map.
  *  2/26/2014 Removed workflowConfiguration from property, and twitter feed block from property and type.
  *  2/24/2014 Fixed a typo in the Property class.
  *  2/7/2014 Added days of week
  *  1/28/2014 Added 7.4 & 7.8 types
  *  1/8/2014 Added a few more constants
  *  12/3/2013 Added two classes: BooleanValues and VisibilityValues
  *  10/28/2013 Added a few more aliases to both classes.
  *             Added some xhtml tags.            
  *  10/26/2013 Added the P class.
 */
namespace cascade_ws_constants
{
use cascade_ws_asset as a;

// xhtml tags which can be used with class names.
define( 'BR',    "<br />\n" );
define( 'E_CODE',  "</code>" );
define( 'S_CODE',  "<code>" );
define( 'HR',    "<hr class='thin width100 text_lightgray bg_lightgray' />" );
define( 'E_H2',  "</h2>\n" );
define( 'S_H2',  "<h2>" );
define( 'E_H3',  "</h3>\n" );
define( 'S_H3',  "<h3>" );
define( 'E_LI', "</li>\n" );
define( 'S_LI', "<li>\n" );
define( 'S_OL', "<ol>" );
define( 'E_OL', "</ol>" );
define( 'E_P', "</p>\n" );
define( 'S_P', "<p>\n" );
define( 'E_PRE', "</pre>\n" );
define( 'S_PRE', "<pre>\n" );
define( 'SPACE', "&nbsp;&nbsp;&nbsp;&nbsp;" );
define( 'S_SPAN', "<span style='color:red;font-weight:bold'>" );
define( 'E_SPAN', "</span>" );
define( 'E_STRONG', "</strong>\n" );
define( 'S_STRONG', "<strong>\n" );
define( 'E_UL', "</ul>\n" );
define( 'S_UL', "<ul>\n" );

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>F</code> class defines constants of global function names and values used in asset tree traversal.</p>
</description>
</documentation>
*/
class F // global functions
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    const ASSOCIATE_WITH_METADATA_SET = 'assetTreeAssociateWithMetadataSet';
    const COUNT                       = 'assetTreeCount';
    const DISPLAY                     = 'assetTreeDisplay';
    const GET_ASSETS                  = 'assetTreeGetAssets';
    const PUBLISH                     = 'assetTreePublish';
    const REMOVE_ASSET                = 'assetTreeRemoveAsset';
    const REPORT_DATA_DEFINITION_FLAG = 'assetTreeReportDataDefinitionFlag';
    const REPORT_FACTORY_GROUP        = 'assetTreeReportAssetFactoryGroupAssignment';
    const REPORT_METADATA_FLAG        = 'assetTreeReportMetadataFlag';
    const REPORT_PAGE_LEVEL           = 'assetTreeReportPageWithPageLevelBlockFormat';
    const REPORT_ORPHANS              = 'assetTreeReportOrphans';
    const SEARCH_BY_NAME              = 'assetTreeSearchByName';
    const STORE_ASSET_PATH            = 'assetTreeStoreAssetPath';
    
    const SKIP_ROOT_CONTAINER         = 'skip-root-container';
    const UNCONDITIONAL_REMOVAL       = 'unconditional-removal';
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>L</code> class defines labels used in outputs.</p>
</description>
</documentation>
*/
class L // output labels for displaying purposes
{
    const NAME_SPACE              = 'cascade_ws_constants';
    
    const ACTION                  = "Action: ";
    const ASSET_TYPE              = "Asset type: ";
    const BODY                    = "Body: ";
    const CREATED_BY              = "Created by: ";
    const CREATED_DATE            = "Created date: ";
    const DATA                    = "Data: ";
    const DATE                    = "Date: ";
    const EXPIRATION_FOLDER_ID    = "Expiration folder ID: ";
    const EXPIRATION_FOLDER_PATH  = "Expiration folder path: ";
    const EXPIRATION_FOLDER_RECYCLED  = "Expiration folder recycled: ";
    const FROM                    = "From: ";
    const ID                      = "ID: ";
    const LAST_MODIFIED_BY        = "Last modified by: ";
    const LAST_MODIFIED_DATE      = "Last modified date: ";
    const LAST_PUBLISHED_BY       = "Last published by: ";
    const LAST_PUBLISHED_DATE     = "Last published date: ";
    const MAINTAIN_ABSOLUTE_LINKS = "Maintain absolute links: ";
    const METADATA_SET_ID         = "Metadata set ID: ";
    const METADATA_SET_PATH       = "Metadata set path: ";
    const NAME                    = "Name: ";
    const PATH                    = "Path: ";
    const PARENT_CONTAINER_ID     = "Parent container ID: ";
    const PARENT_CONTAINER_PATH   = "Parent container path: ";
    const PARENT_FOLDER_ID        = "Parent folder ID: ";
    const PARENT_FOLDER_PATH      = "Parent folder path: ";
    const PROPERTY_NAME           = "Property name: ";
    const READ_DUMP               = "Read Dump";
    const REWRITE_LINKS           = "Rewrite links: ";
    const SHOULD_BE_INDEXED       = "Should be indexed: ";
    const SHOULD_BE_PUBLISHED     = "Should be published: ";
    const SITE_ID                 = "Site ID: ";
    const SITE_NAME               = "Site name: ";
    const SUBJECT                 = "Subject: ";
    const TEXT                    = "Text: ";
    const TO                      = "To: ";
    const TYPE                    = "Type: ";
    const USER                    = "User: ";
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>M</code> class defines messages used in exceptions.</p>
</description>
</documentation>
*/
class M // messages for exceptions
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    const ACCESS_TO_USERS_GROUPS         = "Access can only be granted to users and groups.";
    const COMMENT_NOT_STRING             = "The comment should be a string. ";
    const COPY_ASSET_FAILURE             = "Failed to copy the asset. ";
    const COPY_BASE_FOLDER               = "Cannot copy the Base Folder. ";
    const CREATE_ASSET_FAILURE           = "Cannot create the asset. ";
    const DELETE_ASSET_FAILURE           = "Cannot delete the asset. ";
    const DIFFERENT_DATA_DEFINITIONS     = "The two data definitions are different. ";
    const EDIT_ASSET_FAILURE             = "Failed to edit the asset. ";
    const EDIT_WORKFLOW_SETTINGS_FAILURE = "Failed to edit the workflow settings. ";
    const EMPTY_ACCOUNT_NAME             = "The account name cannot be empty. ";
    const EMPTY_ASSET_CONTENT            = "The asset content cannot be empty. ";
    const EMPTY_ASSET_FACTORY_NAME       = "The asset factory name cannot be empty. ";
    const EMPTY_ASSET_FACTORY_CONTAINER_NAME = "The asset factory container name cannot be empty. ";
    const EMPTY_ASSET_METADATA           = "The asset metadata cannot be empty. ";
    const EMPTY_ASSET_NAME               = "The asset name cannot be empty. ";
    const EMPTY_AUDIT                    = "The audit cannot be empty. ";
    const EMPTY_BASE_PATH                = "The base path cannot be empty. ";
    const EMPTY_BUCKET_NAME              = "The bucket name cannot be empty. ";
    const EMPTY_BLOCK_NAME               = "The block name cannot be empty. ";
    const EMPTY_COULD_TRANSPORT_NAME     = "The cloud transport name cannot be empty. ";
    const EMPTY_COMMENT                  = "The comment cannot be empty. ";
    const EMPTY_CONFIGURATION_NAME       = "The configuration name cannot be empty. ";
    const EMPTY_CONNECTOR_NAME           = "The connector name cannot be empty. ";
    const EMPTY_CONNECTOR_CONTAINER_NAME = "The connector container name cannot be empty. ";
    const EMPTY_CONTENT_TYPE_NAME        = "The content type name cannot be empty. ";
    const EMPTY_CONTENT_TYPE_CONTAINER_NAME = "The content type container name cannot be empty. ";
    const EMPTY_CRON_EXPRESSION          = "The cron expression supplied cannot be empty. ";
    const EMPTY_DATABASE_NAME            = "The database name cannot be empty. ";
    const EMPTY_DATA_DEFINITION_NAME     = "The data definition name cannot be empty. ";
    const EMPTY_DATA_DEFINITION_CONTAINER_NAME = "The data definition container name cannot be empty. ";
    const EMPTY_DESTINATION_NAME         = "The destination name cannot be empty. ";
    const EMPTY_DESTINATION_CONTAINER_NAME = "The destination container name cannot be empty. ";
    const EMPTY_DIRECTORY                = "The directory cannot be empty. ";
    const EMPTY_FILE_NAME                = "The file name cannot be empty. ";
    const EMPTY_EMAIL                    = "The email cannot be empty. ";
    const EMPTY_FILE_EXTENSION           = "The file extension cannot be empty. ";
    const EMPTY_FOLDER_NAME              = "The folder name cannot be empty. ";
    const EMPTY_FORMAT_NAME              = "The format name cannot be empty. ";
    const EMPTY_FULL_NAME                = "The full name cannot be empty. ";
    const EMPTY_GROUP_NAME               = "The group name cannot be empty. ";
    const EMPTY_HASH_TAG                 = "The hash tag cannot be empty. ";
    const EMPTY_HOST_NAME                = "The host name cannot be empty. ";
    const EMPTY_IDENTIFIER               = "The identifier cannot be empty. ";
    const EMPTY_KEY                      = "The key cannot be empty. ";
    const EMPTY_LABEL                    = "The label cannot be empty. ";
    const EMPTY_METADATA_SET_NAME        = "The metadata set name cannot be empty. ";
    const EMPTY_METADATA_SET_CONTAINER_NAME = "The metadata set container name cannot be empty. ";
    const EMPTY_NAME                     = "The name cannot be empty. ";
    const EMPTY_PAGE_NAME                = "The page name cannot be empty. ";
    const EMPTY_PAGE_CONFIGURATION_NAME  = "The page configuration name cannot be empty. ";
    const EMPTY_PAGE_CONFIGURATION_SET_NAME = "The configuration set name cannot be empty. ";
    const EMPTY_PAGE_CONFIGURATION_SET_CONTAINER_NAME = "The configuration set container name cannot be empty. ";
    const EMPTY_PASSWORD                 = "The password cannot be empty. ";
    const EMPTY_POSSIBLE_VALUES          = "The possible value cannot be empty. ";
    const EMPTY_PREFIX                   = "The prefix cannot be empty. ";
    const EMPTY_PROFILE_ID               = "The profile ID cannot be empty. ";
    const EMPTY_PROTOCOL_TYPE            = "The protocol type cannot be empty. ";
    const EMPTY_PUBLISH_SET_NAME         = "The publish set name cannot be empty. ";
    const EMPTY_PUBLISH_SET_CONTAINER_NAME = "The publish set container name cannot be empty. ";
    const EMPTY_REFERENCE_NAME           = "The reference name cannot be empty. ";
    const EMPTY_RECYCLE_BIN_EXPIRATION   = "The recycle bin expiration cannot be empty. ";
    const EMPTY_ROLE_NAME                = "The role name cannot be empty. ";
    const EMPTY_SCRIPT                   = "The script cannot be empty. ";
    const EMPTY_SERVER_NAME              = "The server name cannot be empty. ";
    const EMPTY_SERVER_PORT              = "The server port cannot be empty. ";
    const EMPTY_SITE_NAME                = "The site name cannot be empty. ";
    const EMPTY_SITE_DESTINATION_CONTAINER_NAME = "The site destination container name cannot be empty. ";
    const EMPTY_SYMLINK_NAME             = "The symlink name cannot be empty. ";
    const EMPTY_TEMPLATE_NAME            = "The template name cannot be empty. ";
    const EMPTY_TEXT                     = "The text cannot be empty. ";
    const EMPTY_TEXT_DATA                = "Either one of text, data is required. ";
    const EMPTY_TRANSPORT_NAME           = "The transport name cannot be empty. ";
    const EMPTY_TRANSPORT_SITE_ID        = "The transport site ID cannot be empty. ";
    const EMPTY_TRANSPORT_CONTAINER_NAME = "The transport container name cannot be empty. ";
    const EMPTY_URL                      = "The URL cannot be empty. ";
    const EMPTY_USER_NAME                = "The username cannot be empty. ";
    const EMPTY_VALUE                    = "The input value cannot be empty. ";
    const EMPTY_WORKFLOW_NAME            = "The workflow name cannot be empty. ";
    const EMPTY_WORKFLOW_DEFINITION_CONTAINER_NAME = "The workflow definition container name cannot be empty. ";
    const EMPTY_XML                      = "The xml cannot be empty. ";
    const EXCEPTION_THROWN_NOT_SET       = "The exception thrown value is not set. ";
    const GOOGLE_CONNECTOR_NO_CT         = "A google analytics connector does not have content type. ";
    const INFORMATION_NOT_AVAILABLE      = "<p>Information not available.</p>";
    const MOVE_ASSET_FAILURE             = "Failed to move/rename the asset. ";
    const NOT_ARRAY                      = "The parameter is not an array. ";
    const NOT_DATA_BLOCK                 = "The block is not a data definition block. ";
    const NOT_XHTML_BLOCK                = "The block is not an xhmtl block. ";
    const NOT_DATA_DEFINITION_PAGE       = "The page is not associated with a data definition. ";
    const NOT_XHTML_PAGE                 = "The page is associated with a data definition. ";
    const NULL_ASSET                     = "The asset cannot be NULL. ";
    const NULL_BLOCK                     = "The block cannot be NULL. ";
    const NULL_CACHE                     = "The cache cannot be NULL. ";
    const NULL_CASCADE                   = "The cascade object cannot be NULL. ";
    const NULL_CONTAINER                 = "The container cannot be NULL. ";
    const NULL_CONTENT_TYPE              = "The content type cannot be NULL. ";
    const NULL_FOLDER                    = "The folder cannot be NULL. ";
    const NULL_DAYS                      = "The days supplied cannot be NULL. ";
    const NULL_FILE                      = "The file cannot be NULL. ";
    const NULL_GROUP                     = "The group cannot be NULL. ";
    const NULL_IDENTIFIER                = "The identifier cannot be NULL. ";
    const NULL_INTERVAL                  = "The interval supplied cannot be NULL. ";
    const NULL_LINKABLE                  = "The linkable supplied cannot be NULL. ";
    const NULL_METADATA_SET              = "The metadata set cannot be NULL. ";
    const NULL_PAGE                      = "The page supplied cannot be NULL. ";
    const NULL_PREFERENCE                = "The preference cannot be NULL. ";
    const NULL_ROLE                      = "The role cannot be NULL. ";
    const NULL_SERVICE                   = "The service object cannot be NULL. ";
    const NULL_SYMLINK                   = "The symlink supplied cannot be NULL. ";
    const NULL_TRANSPORT                 = "The transport cannot be NULL. ";
    const NULL_USER                      = "The user cannot be NULL. ";
    const NULL_WORKFLOW_DEFINITION       = "The workflow definition cannot be NULL. ";
    const PAGE_METADATA_SET              = "The metadata set of a page cannot be changed. ";
    const READ_WORKFLOW_FAILURE          = "Failed to read the workflow. ";
    const RENAME_ASSET_FAILURE           = "Failed to move/rename the asset. ";
    const ROOT_FOLDER_NOT_SET            = "The root folder has not been set. ";
    const SAME_CONTAINER                 = "The asset cannot be moved to the same container";
    const SITE_NOT_SET                   = "The site has not been set. ";
    const SOURCE_CASCADE_NOT_SET         = "The source Cascade object is not set. ";
    const SOURCE_SITE_NOT_SET            = "The source site is not set. ";
    const SITE_CREATION_FAILURE          = "Failed to create the site. ";
    const SITE_NO_PARENT_CONTAINER       = "Sites do not have parent containers and cannot be moved/renamed. ";
    const SMALLER_END_TIME               = "The end time cannot be smaller than the start time. ";
    const TARGET_CASCADE_NOT_SET         = "The target Cascade object is not set. ";
    const TARGET_SITE_NOT_SET            = "The target site is not set. ";
    const TEXT_NO_POSSIBLE_VALUE         = "Text field cannot have possible value. ";
    const UNACCEPTABLE_SECONDS           = " The number of seconds is not acceptable. ";
    const WRONG_ASSET_TYPE               = "The operation is not possible for this asset type. ";
    const WRONG_AUDIT_TYPE               = "The audit type does not exists. ";
    const WRONG_ROLE                     = "The role does not exists. ";
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>P</code> class defines property names.</p>
</description>
</documentation>
*/
class P // property names
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    const ASSET_FACTORY                    = "assetFactory";
    const ASSETFACTORY                     = "assetFactory";
    const ASSET_FACTORY_CONTAINER          = "assetFactoryContainer";
    const ASSETFACTORYCONTAINER            = "assetFactoryContainer";
    const CLOUD_TRANSPORT                  = "cloudTransport";
    const CLOUDTRANSPORT                   = "cloudTransport";
    const CONFIGURATION_SET                = "pageConfigurationSet";
    const CONFIGURATIONSET                 = "pageConfigurationSet";
    const CONFIGURATION_SET_CONTAINER      = "pageConfigurationSetContainer";
    const CONFIGURATIONSETCONTAINER        = "pageConfigurationSetContainer";
    const CONNECTOR_CONTAINER              = "connectorContainer";
    const CONNECTORCONTAINER               = "connectorContainer";
    const CONTENT_TYPE                     = "contentType";
    const CONTENTTYPE                      = "contentType";
    const CONTENT_TYPE_CONTAINER           = "contentTypeContainer";
    const CONTENTTYPECONTAINER             = "contentTypeContainer";
    const DATA_DEFINITION                  = "dataDefinition";
    const DATADEFINITION                   = "dataDefinition";
    const DATA_DEFINITION_BLOCK            = "xhtmlDataDefinitionBlock";
    const DATADEFINITIONBLOCK              = "xhtmlDataDefinitionBlock";
    const DATA_DEFINITION_CONTAINER        = "dataDefinitionContainer";
    const DATADEFINITIONCONTAINER          = "dataDefinitionContainer";
    const DATABASE_TRANSPORT               = "databaseTransport";
    const DATABASETRANSPORT                = "databaseTransport";
    const DATA_BLOCK                       = "xhtmlDataDefinitionBlock";
    const DATABLOCK                        = "xhtmlDataDefinitionBlock";
    const DESTINATION                      = "destination";
    const FACEBOOK_CONNECTOR               = "facebookConnector";
    const FACEBOOKCONNECTOR                = "facebookConnector";
    const FEED_BLOCK                       = "feedBlock";
    const FEEDBLOCK                        = "feedBlock";
    const FILE                             = "file";
    const FILE_SYSTEM_TRANSPORT            = "fileSystemTransport";
    const FILESYSTEMTRANSPORT              = "fileSystemTransport";
    const FOLDER                           = "folder";
    const FTP_TRANSPORT                    = "ftpTransport";
    const FTPTRANSPORT                     = "ftpTransport";
    const GOOGLE_ANALYTICS_CONNECTOR       = "googleAnalyticsConnector";
    const GOOGLEANALYTICSCONNECTOR         = "googleAnalyticsConnector";
    const GROUP                            = "group";
    const INDEX_BLOCK                      = "indexBlock";
    const INDEXBLOCK                       = "indexBlock";
    const METADATA_SET                     = "metadataSet";
    const METADATASET                      = "metadataSet";
    const METADATA_SET_CONTAINER           = "metadataSetContainer";
    const METADATASETCONTAINER             = "metadataSetContainer";
    const PAGE                             = "page";
    const PAGE_CONFIGURATION_SET           = "pageConfigurationSet";
    const PAGECONFIGURATIONSET             = "pageConfigurationSet";
    const PAGE_CONFIGURATION_SET_CONTAINER = "pageConfigurationSetContainer";
    const PAGECONFIGURATIONSETCONTAINER    = "pageConfigurationSetContainer";
    const PUBLISH_SET                      = "publishSet";
    const PUBLISHSET                       = "publishSet";
    const PUBLISH_SET_CONTAINER            = "publishSetContainer";
    const PUBLISHSETCONTAINER              = "publishSetContainer";
    const REFERENCE                        = "reference";
    const ROLE                             = "role";
    const SCRIPT_FORMAT                    = "scriptFormat";
    const SCRIPTFORMAT                     = "scriptFormat";
    const SHAREDFIELD                      = "sharedField";
    const SHARED_FIELD                     = "sharedField";
    const SHAREDFIELDCONTAINER             = "sharedFieldContainer";
    const SHARED_FIELD_CONTAINER           = "sharedFieldContainer";
    const SITE                             = "site";
    const SITE_DESTINATION_CONTAINER       = "siteDestinationContainer";
    const SITEDESTINATIONCONTAINER         = "siteDestinationContainer";
    const SYMLINK                          = "symlink";
    const TARGET                           = "target";
    const TEMPLATE                         = "template";
    const TEXT_BLOCK                       = "textBlock";
    const TEXTBLOCK                        = "textBlock";
    const TRANSPORT_CONTAINER              = "transportContainer";
    const TRANSPORTCONTAINER               = "transportContainer";
    const TWITTER_CONNECTOR                = "twitterConnector";
    const TWITTERCONNECTOR                 = "twitterConnector";
    const USER                             = "user";
    const VELOCITY_FORMAT                  = "scriptFormat";
    const VELOCITYFORMAT                   = "scriptFormat";
    const WORDPRESS_CONNECTOR              = "wordPressConnector";
    const WORDPRESSCONNECTOR               = "wordPressConnector";
    const WORKFLOW_DEFINITION              = "workflowDefinition";
    const WORKFLOWDEFINITION               = "workflowDefinition";
    const WORKFLOW_DEFINITION_CONTAINER    = "workflowDefinitionContainer";
    const WORKFLOWDEFINITIONCONTAINER      = "workflowDefinitionContainer";
    const XHTML_DATA_DEFINITION_BLOCK      = "xhtmlDataDefinitionBlock";
    const XHTMLDATADEFINITIONBLOCK         = "xhtmlDataDefinitionBlock";
    const XML_BLOCK                        = "xmlBlock";
    const XMLBLOCK                         = "xmlBlock";
    const XSLT_FORMAT                      = "xsltFormat";
    const XSLTFORMAT                       = "xsltFormat";
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>S</code> class defines types used in searches.</p>
</description>
</documentation>
*/
class S // search types
{
    const NAME_SPACE                     = 'cascade_ws_constants';
    
    const SEARCH_ASSET_FACTORIES         = 'searchAssetFactories';
    const SEARCHASSETFACTORIES           = 'searchAssetFactories';
    const SEARCH_BLOCKS                  = 'searchBlocks';
    const SEARCHBLOCKS                   = 'searchBlocks';
    const SEARCH_CONNECTORS              = 'searchConnectors';
    const SEARCHCONNECTORS               = 'searchConnectors';
    const SEARCH_CONTENT_TYPES           = 'searchContentTypes';
    const SEARCHCONTENTTYPES             = 'searchContentTypes';
    const SEARCH_DATA_DEFINITIONS        = 'searchDataDefinitions';
    const SEARCHDATADEFINITIONS          = 'searchStructuredDataDefinitions';
    const SEARCH_DESTINATIONS            = 'searchStructuredDataDefinitions';
    const SEARCHDESTINATIONS             = 'searchDestinations';
    const SEARCH_FILES                   = 'searchFiles';
    const SEARCHFILES                    = 'searchFiles';
    const SEARCH_FOLDERS                 = 'searchFolders';
    const SEARCHFOLDERS                  = 'searchFolders';
    const SEARCH_FORMATS                 = 'searchFormats';
    const SEARCHFORMATS                  = 'searchFormats';
    const SEARCH_GROUPS                  = 'searchGroups';
    const SEARCHGROUPS                   = 'searchGroups';
    const SEARCH_METADATA_SETS           = 'searchMetadataSets';
    const SEARCHMETADATASETS             = 'searchMetadataSets';
    const SEARCH_PAGE_CONFIGURATION_SETS = 'searchPageConfigurationSets';
    const SEARCHPAGECONFIGURATIONSETS    = 'searchPageConfigurationSets';
    const SEARCH_PAGES                   = 'searchPages';
    const SEARCHPAGES                    = 'searchPages';
    const SEARCH_PUBLISH_SETS            = 'searchPublishSets';
    const SEARCHPUBLISHSETS              = 'searchPublishSets';
    const SEARCH_ROLES                   = 'searchRoles';
    const SEARCHROLES                    = 'searchRoles';
    const SEARCH_SITES                   = 'searchSites';
    const SEARCHSITES                    = 'searchSites';
    const SEARCH_SYMLINKS                = 'searchSymlinks';
    const SEARCHSYMLINKS                 = 'searchSymlinks';
    const SEARCH_TARGETS                 = 'searchTargets';
    const SEARCHTARGETS                  = 'searchTargets';
    const SEARCH_TEMPLATES               = 'searchTemplates';
    const SEARCHTEMPLATES                = 'searchTemplates';
    const SEARCH_TRANSPORTS              = 'searchTransports';
    const SEARCHTRANSPORTS               = 'searchTransports';
    const SEARCH_USERS                   = 'searchUsers';
    const SEARCHUSERS                    = 'searchUsers';
    const SEARCH_WORKFLOW_DEFINITIONS    = 'searchWorkflowDefinitions';
    const SEARCHWORKFLOWDEFINITIONS      = 'searchWorkflowDefinitions';
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>T</code> class defines type strings,
and <code>get</code> methods to return information using types as keys.</p>
</description>
</documentation>
*/
class T // types
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    const ROOT_PATH                   = "/";
    const CA                          = "cascade-admin";
    
    /* All the types and aliases defined in the WSDL */
    const ACTIVATE_VERSION                 = "activate_version";
    const ACTIVATEVERSION                  = "activate_version";
    const ADVANCE_WORKFLOW                 = "advance_workflow";
    const ADVANCEWORKFLOW                  = "advance_workflow";
    const ALL_DESTINATIONS                 = "all-destinations";
    const ALLDESTINATIONS                  = "all-destinations";
    const ALPHABETICAL                     = "alphabetical";
    const ASCENDING                        = "ascending";
    const ASSET                            = "asset";
    const ASSET_FACTORY                    = "assetfactory";
    const ASSETFACTORY                     = "assetfactory";
    const ASSET_FACTORY_CONTAINER          = "assetfactorycontainer";
    const ASSETFACTORYCONTAINER            = "assetfactorycontainer";
    const AUTO_NAME                        = "auto-name";
    const AUTONAME                         = "auto-name";
    const BACKWARD                         = "backward";
    const BLOCK                            = "block";
    const BLOCK_FEED                       = "block_FEED";
    const BLOCKFEED                        = "block_FEED";
    const BLOCK_INDEX                      = "block_INDEX";
    const BLOCKINDEX                       = "block_INDEX";
    const BLOCK_TEXT                       = "block_TEXT";
    const BLOCKTEXT                        = "block_TEXT";
    const BLOCK_XHTML_DATADEFINITION       = "block_XHTML_DATADEFINITION";
    const BLOCK_XHTML_DATA_DEFINITION      = "block_XHTML_DATADEFINITION";
    const BLOCKXHTMLDATADEFINITION         = "block_XHTML_DATADEFINITION";
    const BLOCK_XML                        = "block_XML";
    const BLOCKXML                         = "block_XML";
    const BLOCK_TWITTER_FEED               = "block_TWITTER_FEED";
    const BLOCKTWITTERFEED                 = "block_TWITTER_FEED";
    const CALENDAR                         = "calendar";
    const CHECKBOX                         = "checkbox";
    const CHECKBOX_ITEM                    = "checkbox-item";
    const CHECKBOXITEM                     = "checkbox-item";
    const CHECK_IN                         = "check_in";
    const CHECKIN                          = "check_in";
    const CHECK_OUT                        = "check_out";
    const CHECKOUT                         = "check_out";
    const CLOUD_TRANSPORT                  = "transport_cloud";
    const CLOUDTRANSPORT                   = "transport_cloud";
    const CONFIGURATION                    = "pageconfiguration";
    const CONFIGURATION_SET                = "pageconfigurationset";
    const CONFIGURATIONSET                 = "pageconfigurationset";
    const CONFIGURATION_SET_CONTAINER      = "pageconfigurationsetcontainer";
    const CONFIGURATIONSETCONTAINER        = "pageconfigurationsetcontainer";
    const CONNECTOR_CONTAINER              = "connectorcontainer";
    const CONNECTORCONTAINER               = "connectorcontainer";
    const CONTENT_TYPE                     = "contenttype";
    const CONTENT_TYPE_ASSET               = "contenttype";
    const CONTENTTYPE                      = "contenttype";
    const CONTENTTYPEASSET                 = "contenttype";
    const CONTENT_TYPE_INDEX               = "content-type";
    const CONTENTTYPEINDEX                 = "content-type";
    const CONTENT_TYPE_CONTAINER           = "contenttypecontainer";
    const CONTENTTYPECONTAINER             = "contenttypecontainer";
    const COPY                             = "copy";
    const CREATE                           = "create";
    const CREATED_DATE                     = "created-date";
    const CREATEDDATE                      = "created-date";
    const CSS                              = "CSS";
    const CUSTOM                           = "custom";
    const DATA_BLOCK                       = "block_XHTML_DATADEFINITION";
    const DATABLOCK                        = "block_XHTML_DATADEFINITION";
    const DATADEFINITION                   = "datadefinition";
    const DATA_DEFINITION                  = "datadefinition";
    const DATA_DEFINITION_INLINE           = "data-definition";
    const DATADEFINITIONINLINE             = "data-definition";
    const DATA_DEFINITION_BLOCK            = "block_XHTML_DATADEFINITION";
    const DATADEFINITIONBLOCK              = "block_XHTML_DATADEFINITION";
    const DATADEFINITION_CONTAINER         = "datadefinitioncontainer";
    const DATA_DEFINITION_CONTAINER        = "datadefinitioncontainer";
    const DATADEFINITIONCONTAINER          = "datadefinitioncontainer";
    const DATABASE_TRANSPORT               = "transport_db";
    const DATABASETRANSPORT                = "transport_db";
    const DATETIME                         = "datetime";
    const DB_TRANSPORT                     = "transport_db";
    const DBTRANSPORT                      = "transport_db";
    const DELETE                           = "delete";
    const DELETE_UNPUBLISH                 = "delete_unpublish";
    const DELETEUNPUBLISH                  = "delete_unpublish";
    const DESCENDING                       = "descending";
    const DESTINATION                      = "destination";
    const DO_NOT_PUBLISH                   = "do-not-publish";
    const DONOTPUBLISH                     = "do-not-publish";
    const DROP_DOWN                        = "dropdown";
    const DROPDOWN                         = "dropdown";
    const DYNAMIC_METADATA                 = "dynamic-metadata";
    const DYNAMICMETADATA                  = "dynamic-metadata";
    const EDIT                             = "edit";
    const EDITOR_CONFIGURATION             = "editorconfiguration";
    const EDITORCONFIGURATION              = "editorconfiguration";
    /* php does not allow this */
    //const EMPTY                          = "empty"; 
    const EXTERNAL                         = "symlink";
    const EXTERNAL_LINK                    = "symlink";
    const EXTERNALLINK                     = "symlink";
    const FACEBOOK_CONNECTOR               = "facebookconnector";
    const FACEBOOKCONNECTOR                = "facebookconnector";
    const FACTORY_CONTROLLED               = "factory-controlled";
    const FACTORYCONTROLLED                = "factory-controlled";
    const FEED_BLOCK                       = "block_FEED";
    const FEEDBLOCK                        = "block_FEED";
    const FILE                             = "file";
    const FIFTEEN                          = "15";
    const FOLDER                           = "folder";
    const FOLDER_CONTROLLED                = "folder-controlled";
    const FOLDERCONTROLLED                 = "folder-controlled";
    const FOLDER_ORDER                     = "folder-order";
    const FOLDERORDER                      = "folder-order";
    const FORMAT                           = "format";
    const FORMAT_XSLT                      = "format_XSLT";
    const FORMATXSLT                       = "format_XSLT";
    const FORMAT_SCRIPT                    = "format_SCRIPT";
    const FORMATSCRIPT                     = "format_SCRIPT";
    const FORWARD                          = "forward";
    const FRIDAY                           = "Friday";
    const FS_TRANSPORT                     = "transport_fs";
    const FSTRANSPORT                      = "transport_fs";
    const FTP_TRANSPORT                    = "transport_ftp";
    const FTPTRANSPORT                     = "transport_ftp";
    
    /* php does not allow this */
    //const GLOBAL                         = "global";  
    const GOOGLE_ANALYTICS_CONNECTOR       = "googleanalyticsconnector";
    const GOOGLEANALYTICSCONNECTOR         = "googleanalyticsconnector";
    const GROUP                            = "group";
    const HIDDEN                           = "hidden";
    const HIERARCHY                        = "hierarchy";
    const HIERARCHY_WITH_SIBLINGS          = "hierarchy-with-siblings";
    const HIERARCHYWITHSIBLINGS            = "hierarchy-with-siblings";
    const HIERARCHY_SIBLINGS_FORWARD       = "hierarchy-siblings_forward";
    const HIERARCHYSIBLINGSFORWARD         = "hierarchy-siblings_forward";
    const HTML                             = "HTML";
    const IDENTIFIER                       = "identifier";
    const INDEX_BLOCK                      = "block_INDEX";
    const INDEXBLOCK                       = "block_INDEX";
    const INLINE                           = "inline";
    const INLINE_DATA_DEFINITION           = "data-definition";
    const INLINEDATADEFINITION             = "data-definition";
    const ITEMS                            = "items";    
    const JS                               = "JS";    
    const JSON                             = "JSON";    
    const LAST_MODIFIED_DATE               = "last-modified-date";
    const LASTMODIFIEDDATE                 = "last-modified-date";
    const LDAP                             = "ldap";
    const LINKABLE                         = "page,file,symlink";
    const LOG_IN                           = "login";
    const LOGIN                            = "login";
    const LOGIN_FAILED                     = "login_failed";
    const LOGINFAILED                      = "login_failed";
    const LOG_OUT                          = "logout";
    const LOGOUT                           = "logout";
    const MATCH_ALL                        = "match-all";
    const MATCHALL                         = "match-all";
    const MATCH_ANY                        = "match-any";
    const MATCHANY                         = "match-any";
    const MESSAGE                          = "message";
    const METADATA_SET                     = "metadataset";
    const METADATASET                      = "metadataset";
    const METADATASET_CONTAINER            = "metadatasetcontainer";
    const METADATA_SET_CONTAINER           = "metadatasetcontainer";
    const METADATASETCONTAINER             = "metadatasetcontainer";
    const MONDAY                           = "Monday";
    const MOVE                             = "move";
    const MULTIPLE                         = "multiple";
    const MULTI_LINE                       = "multi-line";
    const MULTILINE                        = "multi-line";
    const MULTI_SELECT                     = "multiselect";
    const MULTISELECT                      = "multiselect";
    const MULTI_SELECTOR                   = "multi-selector";
    const MULTISELECTOR                    = "multi-selector";
    const NAME                             = "name";
    const NAME_OF_DEFINITION               = "name-of-definition";
    const NAMEOFDEFINITION                 = "name-of-definition";
    const NEVER                            = "never";
    const NO_RENDER                        = "no-render";
    const NORENDER                         = "no-render";
    const NONE                             = "none";
    const NORMAL                           = "normal";
    const ONE                              = "1";
    const PAGE                             = "page";
    const PAGE_CONFIGURATION               = "pageconfiguration";
    const PAGECONFIGURATION                = "pageconfiguration";
    const PAGE_CONFIGURATION_SET           = "pageconfigurationset";
    const PAGECONFIGURATIONSET             = "pageconfigurationset";
    const PAGE_CONFIGURATION_SET_CONTAINER = "pageconfigurationsetcontainer";
    const PAGECONFIGURATIONSETCONTAINER    = "pageconfigurationsetcontainer";
    const PAGE_FILE_SYMLINK                = "page,file,symlink";
    const PAGEFILESYMLINK                  = "page,file,symlink";
    const PAGE_REGION                      = "pageregion";
    const PAGEREGION                       = "pageregion";
    const PDF                              = "PDF";
    const PFS                              = "page,file,symlink";
    const PUBLISH                          = "publish";
    const PUBLISH_SET                      = "publishset";
    const PUBLISHSET                       = "publishset";
    const PUBLISH_SET_CONTAINER            = "publishsetcontainer";
    const PUBLISHSETCONTAINER              = "publishsetcontainer";
    const RADIO                            = "radio";
    const RADIOBUTTON                      = "radiobutton";
    const READ                             = "read";
    const RECYCLE                          = "recycle";
    const REFERENCE                        = "reference";
    const RENDER                           = "render";
    const RENDER_CURRENT_PAGE_ONLY         = "render-current-page-only";
    const RENDERCURRENTPAGEONLY            = "render-current-page-only";
    const RENDER_NORMALLY                  = "render-normally";
    const RENDERNORMALLY                   = "render-normally";
    const REQUIRED                         = "required";
    const RESTORE                          = "restore";
    const ROLE                             = "role";
    const RTF                              = "RTF";
    const SATURDAY                         = "Saturday";
    const SEARCH_TERMS                     = "search-terms";
    const SEARCHTERMS                      = "search-terms";
    const SCRIPT_FORMAT                    = "format_SCRIPT"; 
    const SCRIPTFORMAT                     = "format_SCRIPT"; 
    const SELECTED_DESTINATIONS            = "selected-destinations";
    const SELECTEDDESTINATIONS             = "selected-destinations";
    const SELECTOR                         = "selector";
    const SELECTOR_ITEM                    = "selector-item";
    const SELECTORITEM                     = "selector-item";
    const SHAREDFIELD                      = "sharedfield";
    const SHARED_FIELD                     = "sharedfield";
    const SHAREDFIELDCONTAINER             = "sharedfieldcontainer";
    const SHARED_FIELD_CONTAINER           = "sharedfieldcontainer";
    const SITE                             = "site";
    const SITE_DESTINATION_CONTAINER       = "sitedestinationcontainer";
    const SITEDESTINATIONCONTAINER         = "sitedestinationcontainer";
    const SOURCE                           = "source";
    const START_WORKFLOW                   = "start_workflow";
    const STARTWORKFLOW                    = "start_workflow";
    const SUNDAY                           = "Sunday";
    const SYMLINK                          = "symlink";
    const TARGET                           = "target";
    const TEMPLATE                         = "template";
    const TEXT_BLOCK                       = "block_TEXT";
    const TEXTBLOCK                        = "block_TEXT";
    const TEXT                             = "text";
    const THIRTY                           = "30";
    const THURSDAY                         = "Thursday";
    const TRANSPORT                        = "transport";
    const TRANSPORT_CLOUD                  = "transport_cloud";
    const TRANSPORTCLOUD                   = "transport_cloud";
    const TRANSPORT_DB                     = "transport_db";
    const TRANSPORTDB                      = "transport_db";
    const TRANSPORT_FS                     = "transport_fs";
    const TRANSPORTFS                      = "transport_fs";
    const TRANSPORT_FTP                    = "transport_ftp";
    const TRANSPORTFTP                     = "transport_ftp";
    const TRANSPORT_CONTAINER              = "transportcontainer";
    const TRANSPORTCONTAINER               = "transportcontainer";
    const TUESDAY                          = "Tuesday";
    const TWITTER_CONNECTOR                = 'twitterconnector';
    const TWITTERCONNECTOR                 = 'twitterconnector';
    const TWITTERFEEDBLOCK                 = 'block_TWITTER_FEED';
    const TWITTER_FEED_BLOCK               = 'block_TWITTER_FEED';
    const TYPE                             = 'type';
    const UN_PUBLISH                       = "unpublish";
    const UNPUBLISH                        = "unpublish";
    const UN_READ                          = "unread";
    const UNREAD                           = "unread";
    const USER                             = "user";
    const USER_AND_MENTIONS                = "user-and-mentions";
    const USERANDMENTIONS                  = "user-and-mentions";
    const USER_ONLY                        = "user-only";
    const USERONLY                         = "user-only";
    const VALUE                            = "value";
    const VELOCITY_FORMAT                  = "format_SCRIPT"; 
    const VELOCITYFORMAT                   = "format_SCRIPT"; 
    const VISIBLE                          = "visible";
    const WEDNESDAY                        = "Wednesday";
    const WIRED_METADATA                   = "wired-metadata";
    const WIREDMETADATA                    = "wired-metadata";
    const WML                              = "WML";
    const WORDPRESS_CONNECTOR              = "wordpressconnector";
    const WORDPRESSCONNECTOR               = "wordpressconnector";
    const WORKFLOW                         = 'workflow';
    const WORKFLOW_DEFINITION              = "workflowdefinition";
    const WORKFLOWDEFINITION               = "workflowdefinition";
    const WORKFLOW_DEFINITION_CONTAINER    = "workflowdefinitioncontainer";
    const WORKFLOWDEFINITIONCONTAINER      = "workflowdefinitioncontainer";
    const WRITE                            = "write";
    const WYSIWYG                          = "wysiwyg";
    const XHTML                            = "xhtml";
    const XHTML_DATA_DEFINITION_BLOCK      = "block_XHTML_DATADEFINITION";
    const XHTMLDATADEFINITION_BLOCK        = "block_XHTML_DATADEFINITION";
    const XHTMLDATADEFINITIONBLOCK         = "block_XHTML_DATADEFINITION";
    const XML                              = "XML";
    const XML_BLOCK                        = "block_XML";
    const XMLBLOCK                         = "block_XML";
    const XSLT_FORMAT                      = "format_XSLT";
    const XSLTFORMAT                       = "format_XSLT";

    // used by Child
    public static $type_class_name_map = array(
            T::ASSETFACTORY                  => 'AssetFactory',
            T::ASSETFACTORYCONTAINER         => 'AssetFactoryContainer',
            T::CLOUDTRANSPORT                => 'CloudTransport',
            T::CONNECTORCONTAINER            => 'ConnectorContainer',
            T::CONTENTTYPE                   => 'ContentType',
            T::CONTENTTYPECONTAINER          => 'ContentTypeContainer',
            T::DATADEFINITION                => 'DataDefinition',
            T::DATADEFINITIONCONTAINER       => 'DataDefinitionContainer',
            T::DESTINATION                   => 'Destination',
            T::FACEBOOKCONNECTOR             => 'FacebookConnector',
            T::FEEDBLOCK                     => 'FeedBlock',
            T::FILE                          => 'File',
            T::FOLDER                        => 'Folder',
            T::GOOGLEANALYTICSCONNECTOR      => 'GoogleAnalyticsConnector',
            T::GROUP                         => 'Group',
            T::INDEXBLOCK                    => 'IndexBlock',
            T::METADATASET                   => 'MetadataSet',
            T::METADATASETCONTAINER          => 'MetadataSetContainer',
            T::PAGE                          => 'Page',
            T::PAGECONFIGURATIONSET          => 'PageConfigurationSet',
            T::PAGECONFIGURATIONSETCONTAINER => 'PageConfigurationSetContainer',
            T::PUBLISHSET                    => 'PublishSet',
            T::PUBLISHSETCONTAINER           => 'PublishSetContainer',
            T::REFERENCE                     => 'Reference',
            T::ROLE                          => 'Role',
            T::SCRIPTFORMAT                  => 'ScriptFormat',
            T::SHAREDFIELD                   => 'SharedField',
            T::SHAREDFIELDCONTAINER          => 'SharedFieldContainer',
            T::SITE                          => 'Site',
            T::SITEDESTINATIONCONTAINER      => 'SiteDestinationContainer',
            T::SYMLINK                       => 'Symlink',
            T::TEMPLATE                      => 'Template',
            T::TEXTBLOCK                     => 'TextBlock',
            T::TRANSPORTCLOUD                => 'CloudTransport',
            T::TRANSPORTDB                   => 'DatabaseTransport',
            T::TRANSPORTFS                   => 'FileSystemTransport',
            T::TRANSPORTFTP                  => 'FtpTransport',
            T::TRANSPORTCONTAINER            => 'TransportContainer',
            T::TWITTERCONNECTOR              => 'TwitterConnector',
            T::USER                          => 'User',
            T::WORDPRESSCONNECTOR            => 'WordPressConnector',
            T::WORKFLOWDEFINITION            => 'WorkflowDefinition',
            T::WORKFLOWDEFINITIONCONTAINER   => 'WorkflowDefinitionContainer',
            T::XHTMLDATADEFINITIONBLOCK      => 'DataDefinitionBlock',
            T::XMLBLOCK                      => 'XmlBlock',
            T::XSLTFORMAT                    => 'XsltFormat'
    );
    
    public static $type_parent_type_map = array(
            T::ASSETFACTORY                  => T::ASSETFACTORYCONTAINER,
            T::ASSETFACTORYCONTAINER         => T::ASSETFACTORYCONTAINER,
            T::CLOUDTRANSPORT                => T::TRANSPORT_CONTAINER,
            T::CONNECTORCONTAINER            => T::CONNECTORCONTAINER,
            T::CONTENTTYPE                   => T::CONTENTTYPECONTAINER,
            T::CONTENTTYPECONTAINER          => T::CONTENTTYPECONTAINER,
            T::DATADEFINITION                => T::DATADEFINITIONCONTAINER,
            T::DATADEFINITIONCONTAINER       => T::DATADEFINITIONCONTAINER,
            T::DESTINATION                   => T::SITEDESTINATIONCONTAINER,
            T::FACEBOOKCONNECTOR             => T::CONNECTORCONTAINER,
            T::FEEDBLOCK                     => T::FOLDER,
            T::FILE                          => T::FOLDER,
            T::FOLDER                        => T::FOLDER,
            T::GOOGLEANALYTICSCONNECTOR      => T::CONNECTORCONTAINER,
            T::INDEXBLOCK                    => T::FOLDER,
            T::METADATASET                   => T::METADATASETCONTAINER,
            T::METADATASETCONTAINER          => T::METADATASETCONTAINER,
            T::PAGE                          => T::FOLDER,
            T::PAGECONFIGURATIONSET          => T::PAGECONFIGURATIONSETCONTAINER,
            T::PAGECONFIGURATIONSETCONTAINER => T::PAGECONFIGURATIONSETCONTAINER,
            T::PUBLISHSET                    => T::PUBLISHSETCONTAINER,
            T::PUBLISHSETCONTAINER           => T::PUBLISHSETCONTAINER,
            T::REFERENCE                     => T::FOLDER,
            T::SCRIPTFORMAT                  => T::FOLDER,
            T::SHAREDFIELD                   => T::SHAREDFIELDCONTAINER,
            T::SITEDESTINATIONCONTAINER      => T::SITEDESTINATIONCONTAINER,
            T::SYMLINK                       => T::FOLDER,
            T::TEMPLATE                      => T::FOLDER,
            T::TEXTBLOCK                     => T::FOLDER,
            T::TRANSPORTCLOUD                => T::TRANSPORTCONTAINER,
            T::TRANSPORTDB                   => T::TRANSPORTCONTAINER,
            T::TRANSPORTFS                   => T::TRANSPORTCONTAINER,
            T::TRANSPORTFTP                  => T::TRANSPORTCONTAINER,
            T::TRANSPORTCONTAINER            => T::TRANSPORTCONTAINER,
            T::TWITTERCONNECTOR              => T::CONNECTORCONTAINER,
            T::WORDPRESSCONNECTOR            => T::CONNECTORCONTAINER,
            T::WORKFLOWDEFINITION            => T::WORKFLOWDEFINITIONCONTAINER,
            T::WORKFLOWDEFINITIONCONTAINER   => T::WORKFLOWDEFINITIONCONTAINER,
            T::XHTMLDATADEFINITIONBLOCK      => T::FOLDER,
            T::XMLBLOCK                      => T::FOLDER,
            T::XSLTFORMAT                    => T::FOLDER
    );
    
    public static $type_property_name_map = array(
            T::ASSETFACTORY                  => P::ASSETFACTORY,
            T::ASSETFACTORYCONTAINER         => P::ASSETFACTORYCONTAINER,
            T::CLOUDTRANSPORT                => P::CLOUDTRANSPORT,
            T::CONNECTORCONTAINER            => P::CONNECTORCONTAINER,
            T::CONTENTTYPE                   => P::CONTENTTYPE,
            T::CONTENTTYPECONTAINER          => P::CONTENTTYPECONTAINER,
            T::DATADEFINITION                => P::DATADEFINITION,
            T::DATADEFINITIONCONTAINER       => P::DATADEFINITIONCONTAINER,
            T::DESTINATION                   => P::DESTINATION,
            T::FACEBOOKCONNECTOR             => P::FACEBOOKCONNECTOR,
            T::FEEDBLOCK                     => P::FEEDBLOCK,
            T::FILE                          => P::FILE,
            T::FOLDER                        => P::FOLDER,
            T::GOOGLEANALYTICSCONNECTOR      => P::GOOGLEANALYTICSCONNECTOR,
            T::GROUP                         => P::GROUP,
            T::INDEXBLOCK                    => P::INDEXBLOCK,
            T::METADATASET                   => P::METADATASET,
            T::METADATASETCONTAINER          => P::METADATASETCONTAINER,
            T::PAGE                          => P::PAGE,
            T::PAGECONFIGURATIONSET          => P::PAGECONFIGURATIONSET,
            T::PAGECONFIGURATIONSETCONTAINER => P::PAGECONFIGURATIONSETCONTAINER,
            T::PUBLISHSET                    => P::PUBLISHSET,
            T::PUBLISHSETCONTAINER           => P::PUBLISHSETCONTAINER,
            T::REFERENCE                     => P::REFERENCE,
            T::ROLE                          => P::ROLE,
            T::SCRIPTFORMAT                  => P::SCRIPTFORMAT,
            T::SHAREDFIELD                   => P::SHAREDFIELD,
            T::SHAREDFIELDCONTAINER          => P::SHAREDFIELDCONTAINER,
            T::SITE                          => P::SITE,
            T::SITEDESTINATIONCONTAINER      => P::SITEDESTINATIONCONTAINER,
            T::SYMLINK                       => P::SYMLINK,
            T::TEMPLATE                      => P::TEMPLATE,
            T::TEXTBLOCK                     => P::TEXTBLOCK,
            T::TRANSPORTCLOUD                => P::CLOUDTRANSPORT,
            T::TRANSPORTDB                   => P::DATABASETRANSPORT,
            T::TRANSPORTFS                   => P::FILESYSTEMTRANSPORT,
            T::TRANSPORTFTP                  => P::FTPTRANSPORT,
            T::TRANSPORTCONTAINER            => P::TRANSPORTCONTAINER,
            T::TWITTERCONNECTOR              => P::TWITTERCONNECTOR,
            T::USER                          => P::USER,
            T::WORDPRESSCONNECTOR            => P::WORDPRESSCONNECTOR,
            T::WORKFLOWDEFINITION            => P::WORKFLOWDEFINITION,
            T::WORKFLOWDEFINITIONCONTAINER   => P::WORKFLOWDEFINITIONCONTAINER,
            T::XHTMLDATADEFINITIONBLOCK      => P::XHTMLDATADEFINITIONBLOCK,
            T::XMLBLOCK                      => P::XMLBLOCK,
            T::XSLTFORMAT                    => P::XSLTFORMAT
    );
    
/**
<documentation><description><p>Returns the array named <code>$type_class_name_map</code>.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public static function getTypeArray() : array
    {
        return array_keys( self::$type_class_name_map );
    }
    
/**
<documentation><description><p>Maps a type string to its corresponding class name.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getClassNameByType( string $type )
    {
        if( isset( self::$type_class_name_map[ $type ] ) )
        {
            return self::$type_class_name_map[ $type ];
        }
        return NULL;
    }
    
/**
<documentation><description><p>Maps a type string to its corresponding class name of the parent container.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getParentType( string $type )
    {
        if( isset( self::$type_parent_type_map[ $type ] ) )
        {
            return self::$type_parent_type_map[ $type ];
        }
        return NULL;
    }
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>AuditTypes</code> class defines an array storing types of audits and a method to check if a string is a type of audit.</p>
</description>
</documentation>
*/
class AuditTypes
{
    const NAME_SPACE = 'cascade_ws_constants';
    
    public static $types = array( 
        T::LOGIN, T::LOGIN_FAILED, T::LOGOUT, T::START_WORKFLOW, T::ADVANCE_WORKFLOW,
        T::EDIT, T::COPY, T::CREATE, T::REFERENCE, T::DELETE, T::DELETE_UNPUBLISH,
        T::CHECK_IN, T::CHECK_OUT, T::ACTIVATE_VERSION, T::PUBLISH, T::UNPUBLISH,
        T::RECYCLE, T::RESTORE, T::MOVE
    );
    
/**
<documentation><description><p>Returns a bool, indicating whether the string is a type of audits.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function isAuditType( string $value ) : bool
    {
        return in_array( trim( $value ), self::$types );
    }
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>BooleanValues</code> class provides two methods to check if a value is a bool value.</p>
</description>
</documentation>
*/
class BooleanValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
/**
<documentation><description><p>Returns a bool, indicating whether <code>$value</code> stores a bool value. Note that no type is used here, hence no casting.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function isBoolean( $value ) : bool
    {
        return $value === true || $value === false;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether <code>$value</code> stores a bool value.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function isBool( bool $value ) : bool
    {
        return $value === true || $value === false;
    }
}

/**
<documentation><description><h2>Introduction</h2>
<p>The <code>LevelValues</code> class provides a method to check if a value is a level value.</p>
</description>
</documentation>
*/
class LevelValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
/**
<documentation><description><p>Returns a bool, indicating whether <code>$level</code> stores a level value.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function isLevel( string $level ) : bool
    {
        return $level == T::READ || $level == T::WRITE || $level == T::NONE;
    }
}

class NamingBehaviorValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    public static function isNamingBehaviorValue( $value )
    {
        return $value == a\WorkflowDefinition::NAMING_BEHAVIOR_AUTO || 
            $value == a\WorkflowDefinition::NAMING_BEHAVIOR_DEFINITION || 
            $value == a\WorkflowDefinition::NAMING_BEHAVIOR_BLANK;
    }
}

class RecycleBinExpirationValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    public static function isRecycleBinExpirationValue( $value )
    {
        return $value == T::NEVER || $value == T::ONE || $value == T::FIFTEEN || $value == T::THIRTY;
    }
}

class RoleTypeValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    public static function isRoleTypeValue( $value )
    {
        return $value == T::SITE || $value == "global";
    }
}

class SerializationTypeValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    public static function isSerializationTypeValue( $value )
    {
        return $value == T::HTML || $value == T::PDF || $value == T::XML || $value == T::RTF;
    }
}

class SearchTypes
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    public static $types = array(
        S::SEARCHASSETFACTORIES,
        S::SEARCHBLOCKS,
        S::SEARCHCONNECTORS,
        S::SEARCHCONTENTTYPES,
        S::SEARCHDATADEFINITIONS,
        S::SEARCHDESTINATIONS,
        S::SEARCHFILES,
        S::SEARCHFOLDERS,
        S::SEARCHFORMATS,
        S::SEARCHGROUPS,
        S::SEARCHMETADATASETS,
        S::SEARCHPAGECONFIGURATIONSETS,
        S::SEARCHPAGES,
        S::SEARCHPUBLISHSETS,
        S::SEARCHROLES,
        S::SEARCHSITES,
        S::SEARCHSYMLINKS,
        S::SEARCHTARGETS,
        S::SEARCHTEMPLATES,
        S::SEARCHTRANSPORTS,
        S::SEARCHUSERS,
        S::SEARCHWORKFLOWDEFINITIONS
    );
    
    public static function isSearchType( $value )
    {
        return in_array( trim( $value ), self::$types );
    }
}

class VisibilityValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    public static function isVisibility( $value )
    {
        return $value == T::VISIBLE || $value == T::INLINE || $value == T::HIDDEN;
    }
}

class WorkflowModeValues
{
    const NAME_SPACE                  = 'cascade_ws_constants';
    
    public static function isWorkflowMode( $value )
    {
        return $value == T::NONE || $value == T::FACTORY_CONTROLLED || $value == T::FOLDER_CONTROLLED;
    }
}

} // end namespace
?>