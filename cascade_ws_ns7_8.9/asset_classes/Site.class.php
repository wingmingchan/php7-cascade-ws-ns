<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/22/2018 Added listEditorConfigurations. However, there is a bug in SOAP, as of 8.9.1.
  * 1/24/2018 Updated documentation.
  * 1/12/2018 Added REST code to processRoleAssignments.
  * 1/11/2018 Added getSiteImproveUrl and setSiteImproveUrl.
  * 1/9/2018 Added removeNamingRuleAsset and clearNamingRuleAssets.
  * 12/29/2017 Added REST code and updated documentation.
  * 12/21/2017 Added getRoleAssignments.
  * 11/27/2017 Removed CSS properties and methods, added naming properties and methods.
  * 9/14/2017 Added getExtensionsToStrip and setExtensionsToStrip.
  * 6/29/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
    Added getDefaultEditorConfigurationId and getDefaultEditorConfigurationPath.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 1/28/2016 Added a set of get methods to return root containers.
  * 10/29/2015 Added getExternalLinkCheckOnPublish and setExternalLinkCheckOnPublish.
  * 5/28/2015 Added namespaces.
  * 8/12/2014 Added getUnpublishOnExpiration, setUnpublishOnExpiration, getLinkCheckerEnabled, and setLinkCheckerEnabled.
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
<p>An <code>Site</code> object represents a site asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/scheduled-publishing.php\"><code>ScheduledPublishing</code></a>.</p>
<h2>Structure of <code>site</code></h2>
<pre>SOAP:
site
  id
  name
  url
  siteImproveUrl
  extensionsToStrip (8.6)
  defaultMetadataSetId
  defaultMetadataSetPath
  siteAssetFactoryContainerId
  siteAssetFactoryContainerPath
  defaultEditorConfigurationId (8.4)
  defaultEditorConfigurationPath (8.4)
  siteStartingPageId
  siteStartingPagePath
  siteStartingPageRecycled (bool)
  roleAssignments
    roleAssignment
      roleId
      roleName
      users
      groups
  usesScheduledPublishing (bool)
  scheduledPublishDestinationMode
  scheduledPublishDestinations
  timeToPublish
  publishIntervalHours
  publishDaysOfWeek
    dayOfWeek
  cronExpression
  sendReportToUsers
  sendReportToGroups
  sendReportOnErrorOnly (bool)
  recycleBinExpiration
  unpublishOnExpiration (bool)
  linkCheckerEnabled (bool)
  externalLinkCheckOnPublish (bool)
  inheritDataChecksEnabled (bool, 8.6)
  spellCheckEnabled
  linkCheckEnabled
  accessibilityCheckEnabled
  inheritNamingRules (bool, 8.7)
  namingRuleCase (8.7)
  namingRuleSpacing (8.7)
  namingRuleAssets (8.7)
  rootFolderId
  rootAssetFactoryContainerId
  rootPageConfigurationSetContainerId
  rootContentTypeContainerId
  rootConnectorContainerId
  rootDataDefinitionContainerId
  rootMetadataSetContainerId
  rootPublishSetContainerId
  rootSiteDestinationContainerId
  rootTransportContainerId
  rootWorkflowDefinitionContainerId
</pre>
<h2>Design Issues</h2>
<p>There is something special about all <code>ScheduledPublishing</code> assets: right after such an asset is read from Cascade, if we send the asset back to Cascade by calling <code>edit</code>, even without making any changes to it, Cascade will reject the asset. To fix this problem, we have to call <code>unset</code> to unset any property related to scheduled publishing if the property stores a <code>NULL</code> value. This must be done inside <code>edit</code>, or an exception will be thrown.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "site" ),
        array( "getSimpleTypeXMLByName"  => "recycleBinExpiration" ),
        array( "getComplexTypeXMLByName" => "role-assignments" ),
        array( "getComplexTypeXMLByName" => "role-assignment" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/site.php">site.php</a></li></ul>
<h2>REST Dump</h2>
<pre>
object(stdClass)#10 (34) {
  ["defaultMetadataSetId"]=>
  string(32) "c12dd0738b7ffe83129ed6d86580d804"
  ["defaultMetadataSetPath"]=>
  string(7) "Default"
  ["siteAssetFactoryContainerId"]=>
  string(32) "c12d932d8b7ffe83129ed6d8112a2203"
  ["siteAssetFactoryContainerPath"]=>
  string(7) "Default"
  ["siteStartingPageRecycled"]=>
  bool(false)
  ["url"]=>
  string(30) "http://www.upstate.edu/formats"
  ["recycleBinExpiration"]=>
  string(2) "15"
  ["roleAssignments"]=>
  array(1) {
    [0]=>
    object(stdClass)#11 (2) {
      ["roleId"]=>
      string(1) "8"
      ["roleName"]=>
      string(18) "Site-Administrator"
    }
  }
  ["usesScheduledPublishing"]=>
  bool(false)
  ["sendReportOnErrorOnly"]=>
  bool(false)
  ["rootFolderId"]=>
  string(32) "c12d8d0d8b7ffe83129ed6d86dd9f853"
  ["rootAssetFactoryContainerId"]=>
  string(32) "c12d8dbe8b7ffe83129ed6d84cc7c0c7"
  ["rootPageConfigurationSetContainerId"]=>
  string(32) "c12d8e0c8b7ffe83129ed6d885ebd843"
  ["rootContentTypeContainerId"]=>
  string(32) "c12d90058b7ffe83129ed6d81395b93a"
  ["rootDataDefinitionContainerId"]=>
  string(32) "c12d90528b7ffe83129ed6d87f0a0643"
  ["rootMetadataSetContainerId"]=>
  string(32) "c12d909e8b7ffe83129ed6d849f35757"
  ["rootPublishSetContainerId"]=>
  string(32) "c12d90e58b7ffe83129ed6d884612ddf"
  ["rootSiteDestinationContainerId"]=>
  string(32) "c12d918b8b7ffe83129ed6d80a701fef"
  ["rootTransportContainerId"]=>
  string(32) "c12d91d58b7ffe83129ed6d8c3e45888"
  ["rootWorkflowDefinitionContainerId"]=>
  string(32) "c12d8cc78b7ffe83129ed6d8693cf2f7"
  ["rootConnectorContainerId"]=>
  string(32) "c12d913d8b7ffe83129ed6d8e9bb4cbf"
  ["unpublishOnExpiration"]=>
  bool(false)
  ["linkCheckerEnabled"]=>
  bool(false)
  ["externalLinkCheckOnPublish"]=>
  bool(false)
  ["inheritDataChecksEnabled"]=>
  bool(true)
  ["spellCheckEnabled"]=>
  bool(true)
  ["linkCheckEnabled"]=>
  bool(true)
  ["accessibilityCheckEnabled"]=>
  bool(true)
  ["inheritNamingRules"]=>
  bool(false)
  ["namingRuleCase"]=>
  string(5) "LOWER"
  ["namingRuleSpacing"]=>
  string(6) "HYPHEN"
  ["namingRuleAssets"]=>
  array(2) {
    [0]=>
    string(8) "template"
    [1]=>
    string(4) "file"
  }
  ["name"]=>
  string(7) "formats"
  ["id"]=>
  string(32) "c12d8c498b7ffe83129ed6d81ea4076a"
}
</pre>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/site/0fa6f6f18b7ffe8343b94c28251e132e

{
  "asset":{
    "site":{
      "defaultMetadataSetId":"0fa6f6f08b7ffe8343b94c28a0eaa566",
      "defaultMetadataSetPath":"Default",
      "siteAssetFactoryContainerId":"0fa6f7688b7ffe8343b94c284f318b63",
      "siteAssetFactoryContainerPath":"Default",
      "siteStartingPageRecycled":false,
      "url":"http://www.upstate.edu",
      "siteImproveUrl":"https://www.google.com/",
      "recycleBinExpiration":"15",
      "roleAssignments":[
      {
        "roleId":"271",
        "roleName":"Test WS Site Role",
        "users":"test-ws-user"
      } ],
      "usesScheduledPublishing":false,
      "sendReportOnErrorOnly":false,
      "rootFolderId":"0fa6f6fc8b7ffe8343b94c282bf4e100",
      "rootAssetFactoryContainerId":"0fa6f7378b7ffe8343b94c286d081809",
      "rootPageConfigurationSetContainerId":"0fa6f77a8b7ffe8343b94c28f91e4b01",
      "rootContentTypeContainerId":"0fa6f7c38b7ffe8343b94c28a89a74d7",
      "rootDataDefinitionContainerId":"0fa6f7d08b7ffe8343b94c2859f282eb",
      "rootMetadataSetContainerId":"0fa6f8108b7ffe8343b94c288b3ebd24",
      "rootPublishSetContainerId":"0fa6f7de8b7ffe8343b94c2874a23606",
      "rootSiteDestinationContainerId":"0fa6f7f58b7ffe8343b94c28d64b3e7e",
      "rootTransportContainerId":"0fa6f7e98b7ffe8343b94c28a1414bed",
      "rootWorkflowDefinitionContainerId":"0fa6f8038b7ffe8343b94c28997165ca",
      "rootConnectorContainerId":"0fa6f7878b7ffe8343b94c28eecf4668",
      "unpublishOnExpiration":true,
      "linkCheckerEnabled":true,
      "externalLinkCheckOnPublish":false,
      "inheritDataChecksEnabled":true,
      "spellCheckEnabled":true,
      "linkCheckEnabled":true,
      "accessibilityCheckEnabled":true,
      "inheritNamingRules":false,
      "namingRuleCase":"LOWER",
      "namingRuleSpacing":"REMOVE",
      "name":"about-test",
      "id":"0fa6f6f18b7ffe8343b94c28251e132e"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre>
</postscript>
</documentation>
*/
class Site extends ScheduledPublishing
{
    const DEBUG    = false;
    const DUMP     = false;
    const TYPE     = c\T::SITE;
    const NEVER    = c\T::NEVER;
    const ONE      = c\T::ONE;
    const FIFTEEN  = c\T::FIFTEEN;
    const THIRTY   = c\T::THIRTY;
    const ANY_CASE         = "ANY";
    const LOWER_CASE       = "LOWER";
    const UPPER_CASE       = "UPPER";
    const HYPHEN_SPACE     = "HYPHEN";
    const REMOVE_SPACE     = "REMOVE";
    const ALLOW_SPACE      = "SPACE";
    const UNDERSCORE_SPACE = "UNDERSCORE";
    const ASSETS = array(
        "block", "file", "folder", "page", "symlink", "template", "reference", "format"
    );


/**
<documentation><description><p>The constructor, overriding the parent method to process role assignments.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->processRoleAssignments();
    }

/**
<documentation><description><p>Adds a new role to <code>roleAssignments</code>, and returns the calling object.</p></description>
<example>$s->addRole( $r )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addRole( Role $r ) : Asset
    {
        if( $r == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ROLE . E_SPAN );
        
        if( $this->hasRole( $r ) )
        {
            return $this;
        }
    
        $ra = new \stdClass();
        $ra->roleId   = $r->getId();
        $ra->roleName = $r->getName();
        $ra->users    = NULL;
        $ra->groups   = NULL;
    
        $this->role_assignments[] = new p\RoleAssignment( $ra );
    
        return $this;
    }

/**
<documentation><description><p>Adds the group to the named role, and returns the calling object.</p></description>
<example>$s->addUserToRole( 
        $r, $cascade->getAsset( a\User::TYPE, 'chanw' ) )->
    addGroupToRole( 
        $r, $cascade->getAsset( a\Group::TYPE, 'cru' ) )->
    edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addGroupToRole( Role $r, Group $g ) : Asset
    {
        $role_name = $r->getName();
    
        if( !$this->hasRole( $r ) )
        {
            $this->addRole( $r );
        }
    
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() == $role_name )
            {
                $assignment->addGroup( $g );
                break;
            }
        }
    
        return $this;
    }

/**
<documentation><description><p>Adds the user to the named role, and returns the calling object.</p></description>
<example>$s->addUserToRole( 
        $r, $cascade->getAsset( a\User::TYPE, 'chanw' ) )->
    addGroupToRole( 
        $r, $cascade->getAsset( a\Group::TYPE, 'cru' ) )->
    edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addUserToRole( Role $r, User $u ) : Asset
    {
        $role_name = $r->getName();

        if( !$this->hasRole( $r ) )
        {
            $this->addRole( $r );
        }
    
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() == $role_name )
            {
                $assignment->addUser( $u );
                break;
            }
        }
    
        return $this;
    }

/**
<documentation><description><p>Removes all strings from the <code>namingRuleAssets</code> array, and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function clearNamingRuleAssets() : Asset
    {
        if( isset( $this->getProperty()->namingRuleAssets ) &&
            count( $this->getProperty()->namingRuleAssets ) > 0 )
        {
            $this->getProperty()->namingRuleAssets = array();
        }
        return $this;
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
        $site = $this->getProperty();
    
        if( $site->usesScheduledPublishing ) // publishing is scheduled
        {
            if( !isset( $site->timeToPublish ) ||
                is_null( $site->timeToPublish ) )
            {
                unset( $site->timeToPublish );
            }
            // fix the time unit
            else if( strpos( $site->timeToPublish, '-' ) !== false )
            {
                $pos = strpos( $site->timeToPublish, '-' );
                $site->timeToPublish = substr(
                    $site->timeToPublish, 0, $pos );
            }
  
            if( !isset( $site->publishIntervalHours ) ||
                is_null( $site->publishIntervalHours ) )
                unset( $site->publishIntervalHours );
            
            if( !isset( $site->publishDaysOfWeek ) ||
                is_null( $site->publishDaysOfWeek ) )
                unset( $site->publishDaysOfWeek );
            
            if( !isset( $site->cronExpression ) ||
                is_null( $site->cronExpression ) )
                unset( $site->cronExpression );
        }

        $assignment_count      = count( $this->role_assignments );
    
        if( $this->getService()->isSoap() )
            $site->roleAssignments = new \stdClass();
        elseif( $this->getService()->isRest() )
            $site->roleAssignments = array();
    
        if( $assignment_count == 1 )
        {
            if( $this->getService()->isSoap() )
                $site->roleAssignments->roleAssignment = 
                    $this->role_assignments[ 0 ]->toStdClass();
            elseif( $this->getService()->isRest() )
                $site->roleAssignments = 
                    array( $this->role_assignments[ 0 ]->toStdClass() );
        }
        else if( $assignment_count > 1 )
        {
            if( $this->getService()->isSoap() )
                $site->roleAssignments->roleAssignment = array();
        
            foreach( $this->role_assignments as $assignment )
            {
                if( $this->getService()->isSoap() )
                    $site->roleAssignments->roleAssignment[] = $assignment->toStdClass();
                elseif( $this->getService()->isRest() )
                    $site->roleAssignments[] = $assignment->toStdClass();
            }
        }
    
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $site ); }
    
        $asset                                    = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $site;
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
<documentation><description><p>Returns <code>accessibilityCheckEnabled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getAccessibilityCheckEnabled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getAccessibilityCheckEnabled() : bool
    {
        return $this->getProperty()->accessibilityCheckEnabled;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at Base Folder.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getAssetTree() : AssetTree
    {
        return $this->getBaseFolder()->getAssetTree();
    }

/**
<documentation><description><p>Returns Base Folder (a <code>Folder</code> object).</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolder() : Asset
    {
        if( $this->base_folder == NULL )
        {
            $this->base_folder = 
                Folder::getAsset( $this->getService(), 
                Folder::TYPE, $this->getRootFolderId() );
        }
        return $this->base_folder;
    }

/**
<documentation><description><p>An alias of <code>getAssetTree</code>.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolderAssetTree() : AssetTree
    {
        return $this->getBaseFolder()->getAssetTree();
    }

/**
<documentation><description><p>An alias of <code>getRootFolderId</code>.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolderId() : string
    {
        return $this->getRootFolderId();
    }

/**
<documentation><description><p>Returns <code>defaultEditorConfigurationId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getDefaultEditorConfigurationId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultEditorConfigurationId()
    {
        if( isset( $this->getProperty()->defaultEditorConfigurationId ) )
            return $this->getProperty()->defaultEditorConfigurationId;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>defaultEditorConfigurationPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getDefaultEditorConfigurationPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultEditorConfigurationPath()
    {
        if( isset( $this->getProperty()->defaultEditorConfigurationPath ) )
            return $this->getProperty()->defaultEditorConfigurationPath;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>defaultMetadataSetId</code>.</p></description>
<example>echo $s->getDefaultMetadataSetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultMetadataSetId() : string
    {
        return $this->getProperty()->defaultMetadataSetId;
    }

/**
<documentation><description><p>Returns <code>defaultMetadataSetPath</code>.</p></description>
<example>echo $s->getDefaultMetadataSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultMetadataSetPath() : string
    {
        return $this->getProperty()->defaultMetadataSetPath;
    }

/**
<documentation><description><p>Returns <code>extensionsToStrip</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExtensionsToStrip()
    {
        if( isset( $this->getProperty()->extensionsToStrip ) )
            return $this->getProperty()->extensionsToStrip;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>externalLinkCheckOnPublish</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getExternalLinkCheckOnPublish() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getExternalLinkCheckOnPublish() : bool
    {
        return $this->getProperty()->externalLinkCheckOnPublish;
    }

/**
<documentation><description><p>Returns <code>inheritDataChecksEnabled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getInheritDataChecksEnabled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getInheritDataChecksEnabled() : bool
    {
        return $this->getProperty()->inheritDataChecksEnabled;
    }

/**
<documentation><description><p>Returns <code>inheritNamingRules</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getInheritNamingRules() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getInheritNamingRules() : bool
    {
        return $this->getProperty()->inheritNamingRules;
    }

/**
<documentation><description><p>Returns <code>linkCheckerEnabled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getLinkCheckerEnabled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getLinkCheckerEnabled() : bool
    {
        return $this->getProperty()->linkCheckerEnabled;
    }

/**
<documentation><description><p>Returns <code>namingRuleAssets</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getNamingRuleAssets() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getNamingRuleAssets()
    {
        if( isset( $this->getProperty()->namingRuleAssets ) )
            return $this->getProperty()->namingRuleAssets;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>namingRuleCase</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getNamingRuleCase() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getNamingRuleCase()
    {
        if( isset( $this->getProperty()->namingRuleCase ) )
            return $this->getProperty()->namingRuleCase;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>namingRuleSpacing</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getNamingRuleSpacing() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getNamingRuleSpacing()
    {
        if( isset( $this->getProperty()->namingRuleSpacing ) )
            return $this->getProperty()->namingRuleSpacing;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>recycleBinExpiration</code>.</p></description>
<example>echo $s->getRecycleBinExpiration(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRecycleBinExpiration() : string
    {
        return $this->getProperty()->recycleBinExpiration;
    }

/**
<documentation><description><p>Returns an array of <code>roleAssignment</code> objects or an empty array.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getRoleAssignments() : array
    {
        return $this->role_assignments;
    }

/**
<documentation><description><p>Returns an <code>AssetFactoryContainer</code> object
representing the root asset factory container.</p></description>
<example>$afc = $s->getRootAssetFactoryContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainer() : Asset
    {
        if( is_null( $this->root_asset_factory_container ) )
        {
            $this->root_asset_factory_container = 
                Asset::getAsset(
                    $this->getService(),
                    AssetFactoryContainer::TYPE, 
                    $this->getProperty()->rootAssetFactoryContainerId );
        }
        return $this->root_asset_factory_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root asset factory container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootAssetFactoryContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainerAssetTree() : AssetTree
    {
        return $this->getRootAssetFactoryContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootAssetFactoryContainerId</code>.</p></description>
<example>echo $s->getRootAssetFactoryContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainerId() : string
    {
        return $this->getProperty()->rootAssetFactoryContainerId;
    }

/**
<documentation><description><p>An alias of <code>getAssetTree</code>.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetTree() : AssetTree
    {
        return $this->getAssetTree();
    }

/**
<documentation><description><p>Returns an <code>ConnectorContainer</code> object representing the root connector container.</p></description>
<example>$cc = $s->getRootConnectorContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainer() : Asset
    {
        if( is_null( $this->root_connector_container ) )
        {
            $this->root_connector_container = 
                Asset::getAsset(
                    $this->getService(),
                    ConnectorContainer::TYPE, 
                    $this->getProperty()->rootConnectorContainerId );
        }
        return $this->root_connector_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root connector container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootConnectorContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainerAssetTree() : AssetTree
    {
        return $this->getRootConnectorContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootConnectorContainerId</code>.</p></description>
<example>echo $s->getRootConnectorContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainerId() : string
    {
        return $this->getProperty()->rootConnectorContainerId;
    }

/**
<documentation><description><p>Returns an <code>ContentTypeContainer</code> object representing the root content type container.</p></description>
<example>$ctc = $s->getRootContentTypeContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainer() : Asset
    {
        if( is_null( $this->root_content_type_container ) )
        {
            $this->root_content_type_container = 
                Asset::getAsset(
                    $this->getService(),
                    ContentTypeContainer::TYPE, 
                    $this->getProperty()->rootContentTypeContainerId );
        }
        return $this->root_content_type_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root content type container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootContentTypeContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainerAssetTree() : AssetTree
    {
        return $this->getRootContentTypeContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootContentTypeContainerId</code>.</p></description>
<example>echo $s->getRootContentTypeContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainerId() : string
    {
        return $this->getProperty()->rootContentTypeContainerId;
    }

/**
<documentation><description><p>Returns an <code>DataDefinitionContainer</code> object representing the root data definition container.</p></description>
<example>$ddc = $s->getRootDataDefinitionContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainer() : Asset
    {
        if( is_null( $this->root_data_definition_container ) )
        {
            $this->root_data_definition_container = 
                Asset::getAsset(
                    $this->getService(),
                    DataDefinitionContainer::TYPE, 
                    $this->getProperty()->rootDataDefinitionContainerId );
        }
        return $this->root_data_definition_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root data definition container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootDataDefinitionContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainerAssetTree() : AssetTree
    {
        return $this->getRootDataDefinitionContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootDataDefinitionContainerId</code>.</p></description>
<example>echo $s->getRootDataDefinitionContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainerId() : string
    {
        return $this->getProperty()->rootDataDefinitionContainerId;
    }
  
/**
<documentation><description><p>An alias of <code>getBaseFolder</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolder() : Asset
    {
        return $this->getBaseFolder();
    }
  
/**
<documentation><description><p>An alias of <code>getAssetTree</code>.</p></description>
<example></example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolderAssetTree() : AssetTree
    {
        return $this->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootFolderId</code>.</p></description>
<example>echo $s->getRootFolderId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolderId() : string
    {
        return $this->getProperty()->rootFolderId;
    }

/**
<documentation><description><p>Returns an <code>MetadataSetContainer</code> object representing the root metadata set container.</p></description>
<example>$mdc = $s->getRootMetadataSetContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainer() : Asset
    {
        if( is_null( $this->root_metadata_set_container ) )
        {
            $this->root_metadata_set_container = 
                Asset::getAsset(
                    $this->getService(),
                    MetadataSetContainer::TYPE, 
                    $this->getProperty()->rootMetadataSetContainerId );
        }
        return $this->root_metadata_set_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root metadata set container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootMetadataSetContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainerAssetTree() : AssetTree
    {
        return $this->getRootMetadataSetContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootMetadataSetContainerId</code>.</p></description>
<example>echo $s->getRootMetadataSetContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainerId() : string
    {
        return $this->getProperty()->rootMetadataSetContainerId;
    }

/**
<documentation><description><p>Returns a <code>PageConfigurationSetContainer</code> object representing the root page configuration set container.</p></description>
<example>$pcsc = $s->getRootPageConfigurationSetContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainer() : Asset
    {
        if( is_null( $this->root_page_configuration_set_container ) )
        {
            $this->root_page_configuration_set_container = 
                Asset::getAsset(
                    $this->getService(),
                    PageConfigurationSetContainer::TYPE, 
                    $this->getProperty()->rootPageConfigurationSetContainerId );
        }
        return $this->root_page_configuration_set_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root page configuration set container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootPageConfigurationSetContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainerAssetTree() : AssetTree
    {
        return $this->getRootPageConfigurationSetContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootPageConfigurationSetContainerId</code>.</p></description>
<example>echo $s->getRootPageConfigurationSetContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainerId() : string
    {
        return $this->getProperty()->rootPageConfigurationSetContainerId;
    }

/**
<documentation><description><p>Returns a <code>PublishSetContainer</code> object representing the root publish set container.</p></description>
<example>$psc = $s->getRootPublishSetContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainer() : Asset
    {
        if( is_null( $this->root_publish_set_container ) )
        {
            $this->root_publish_set_container = 
                Asset::getAsset(
                    $this->getService(),
                    PublishSetContainer::TYPE, 
                    $this->getProperty()->rootPublishSetContainerId );
        }
        return $this->root_publish_set_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root publish set container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootPublishSetContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainerAssetTree() : AssetTree
    {
        return $this->getRootPublishSetContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootPublishSetContainerId</code>.</p></description>
<example>echo $s->getRootPublishSetContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainerId() : string
    {
        return $this->getProperty()->rootPublishSetContainerId;
    }

/**
<documentation><description><p>Returns a <code>SiteDestinationContainer</code> object representing the root site destination container.</p></description>
<example>$sdc = $s->getRootSiteDestinationContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainer() : Asset
    {
        if( is_null( $this->root_site_destination_container ) )
        {
            $this->root_site_destination_container = 
                Asset::getAsset(
                    $this->getService(),
                    SiteDestinationContainer::TYPE, 
                    $this->getProperty()->rootSiteDestinationContainerId );
        }
        return $this->root_site_destination_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root site destination container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootSiteDestinationContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainerAssetTree() : AssetTree
    {
        return $this->getRootSiteDestinationContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootSiteDestinationContainerId</code>.</p></description>
<example>echo $s->getRootSiteDestinationContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainerId() : string
    {
        return $this->getProperty()->rootSiteDestinationContainerId;
    }

/**
<documentation><description><p>Returns a <code>TransportContainer</code> object representing the root transport container.</p></description>
<example>$tc = $s->getRootTransportContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainer() : Asset
    {
        if( is_null( $this->root_transport_container ) )
        {
            $this->root_transport_container = 
                Asset::getAsset(
                    $this->getService(),
                    TransportContainer::TYPE, 
                    $this->getProperty()->rootTransportContainerId );
        }
        return $this->root_transport_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root transport container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootTransportContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainerAssetTree() : AssetTree
    {
        return $this->getRootTransportContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootTransportContainerId</code>.</p></description>
<example>echo $s->getRootTransportContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainerId() : string
    {
        return $this->getProperty()->rootTransportContainerId;
    }

/**
<documentation><description><p>Returns a <code>WorkflowDefinitionContainer</code> object representing the root workflow definition container.</p></description>
<example>$wdc = $s->getRootWorkflowDefinitionContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainer() : Asset
    {
        if( is_null( $this->root_workflow_definition_container ) )
        {
            $this->root_workflow_definition_container = 
                Asset::getAsset(
                    $this->getService(),
                    WorkflowDefinitionContainer::TYPE, 
                    $this->getProperty()->rootWorkflowDefinitionContainerId );
        }
        return $this->root_workflow_definition_container;
    }

/**
<documentation><description><p>Returns an <code>AssetTree</code> object rooted at the root workflow definition container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getRootWorkflowDefinitionContainerAssetTree()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainerAssetTree() : AssetTree
    {
        return $this->getRootWorkflowDefinitionContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>rootWorkflowDefinitionContainerId</code>.</p></description>
<example>echo $s->getRootWorkflowDefinitionContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainerId() : string
    {
        return $this->getProperty()->rootWorkflowDefinitionContainerId;
    }

/**
<documentation><description><p>Returns <code>scheduledPublishDestinationMode</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $s->getScheduledPublishDestinationMode() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledPublishDestinationMode()
    {
        // all-destinations or selected-destinations
        if( isset( $this->getProperty()->scheduledPublishDestinationMode ) )
            return $this->getProperty()->scheduledPublishDestinationMode;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>scheduledPublishDestinations</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $s->getScheduledPublishDestinations() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledPublishDestinations()
    {
        if( isset( $this->getProperty()->scheduledPublishDestinations ) )
            return $this->getProperty()->scheduledPublishDestinations;
        return NULL;
    }

/**
<documentation><description><p>Returns an <code>AssetFactoryContainer</code> object representing the site asset factory container.</p></description>
<example>$safc = $s->getSiteAssetFactoryContainer();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainer() : Asset
    {
        if( is_null( $this->root_site_asset_factory_container ) )
        {
            $this->root_site_asset_factory_container = 
                Asset::getAsset(
                    $this->getService(),
                    AssetFactoryContainer::TYPE, 
                    $this->getProperty()->siteAssetFactoryContainerId );
        }
        return $this->root_site_asset_factory_container;
    }

/**
<documentation><description><p>Return an <code>AssetTree</code> object rooted at the site asset factory container.</p></description>
<example>u\DebugUtility::dump( u\XMLUtility::replaceBrackets( 
    $s->getSiteAssetFactoryContainer()->
    toXml() ) );</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerAssetTree() : AssetTree
    {
        return $this->getSiteAssetFactoryContainer()->getAssetTree();
    }

/**
<documentation><description><p>Returns <code>siteAssetFactoryContainerId</code>.</p></description>
<example>echo $s->getSiteAssetFactoryContainerId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerId() : string
    {
        return $this->getProperty()->siteAssetFactoryContainerId;
    }

/**
<documentation><description><p>Returns <code>siteAssetFactoryContainerPath</code>.</p></description>
<example>echo $s->getSiteAssetFactoryContainerPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerPath() : string
    {
        return $this->getProperty()->siteAssetFactoryContainerPath;
    }

/**
<documentation><description><p>Returns <code>siteImproveUrl</code>.</p></description>
<example>echo $s->getSiteImproveUrl(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteImproveUrl()
    {
        if( isset( $this->getProperty()->siteImproveUrl ) )
            return $this->getProperty()->siteImproveUrl;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>siteStartingPageId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getSiteStartingPageId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPageId()
    {
        if( isset( $this->getProperty()->siteStartingPageId ) )
            return $this->getProperty()->siteStartingPageId;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>siteStartingPagePath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $s->getSiteStartingPagePath() ), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPagePath()
    {
        if( isset( $this->getProperty()->siteStartingPagePath ) )
            return $this->getProperty()->siteStartingPagePath;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>siteStartingPageRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getSiteStartingPageRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPageRecycled() : bool
    {
        return $this->getProperty()->siteStartingPageRecycled;
    }

/**
<documentation><description><p>Returns <code>getSpellCheckEnabled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getSpellCheckEnabled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getSpellCheckEnabled() : bool
    {
        return $this->getProperty()->siteStartingPageRecycled;
    }

/**
<documentation><description><p>Returns <code>unpublishOnExpiration</code>.</p></description>
<example>echo u\StringUtility::boolToString( $s->getUnpublishOnExpiration() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getUnpublishOnExpiration() : bool
    {
        return $this->getProperty()->unpublishOnExpiration;
    }

/**
<documentation><description><p>Returns <code>url</code>.</p></description>
<example>echo $s->getUrl(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getUrl() : string
    {
        return $this->getProperty()->url;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the named role is associated with the site.</p></description>
<example>echo u\StringUtility::boolToString( $s->hasRole(
    $cascade->getAsset( a\Role::TYPE, 5 ) ) ), BR;</example>
<return-type>bool</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function hasRole( Role $r ) : bool
    {
        if( $r == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ROLE . E_SPAN );
    
        $role_name = $r->getName();
    
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() == $role_name )
            {
                return true;
            }
        }
        return false;
    }

    public function listEditorConfigurations() : array
    {
    	$service = $this->getService();
    	$configs = $service->listEditorConfigurations( $this->getIdentifier() );
    	    
    	if( $service->isSuccessful() )
    	{
    		if( $service->isSoap() )
    		{
    			u\DebugUtility::dump( $configs );

    			if( !is_array( $configs ) && isset( $configs->siteId ) )
    			{
    				$configs = [ $configs ];
    			}
    			else
    			{
    				$configs = [];
    			}
    		}
    		elseif( $service->isRest() )
    		{
    			$configs = $configs->editorConfigurations;
    		}
    	}
    	    
    	return $configs;
    }

/**
<documentation><description><p>Publishes the site and returns the calling object.</p></description>
<example>$s->publish();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL ) : Asset
    {
        if( isset( $destination ) )
        {
            $destination_std       = new \stdClass();
            $destination_std->id   = $destination->getId();
            $destination_std->type = $destination->getType();
        }
    
        $service = $this->getService();
    
        if( isset( $destination ) )
            $service->publish( 
                $service->createId(
                    self::TYPE, $this->getProperty()->id ), $destination_std );
        else
            $service->publish( 
                $service->createId( self::TYPE, $this->getProperty()->id ) );
        return $this;
    }

/**
<documentation><description><p>Removes the string from the <code>namingRuleAssets</code> array if it exists, and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function removeNamingRuleAsset( string $a ) : Asset
    {
        if( isset( $this->getProperty()->namingRuleAssets ) &&
            count( $this->getProperty()->namingRuleAssets ) > 0 )
        {
            $temp_array = array();
        
            foreach( $this->getProperty()->namingRuleAssets as $asset )
                if( $asset != $a )
                    $temp_array[] = $asset;
    
            $this->getProperty()->namingRuleAssets = $temp_array;
        }
        return $this;
    }

/**
<documentation><description><p>Removes the role from <code>roleAssignments</code>, and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function removeRole( Role $r ) : Asset
    {
        if( $r == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_ROLE . E_SPAN );
    
        if( !$this->hasRole( $r ) )
        {
            return $this;
        }
    
        $role_name = $r->getName();
    
        $temp = array();
    
        foreach( $this->role_assignments as $assignment )
        {
            if( $assignment->getRoleName() != $role_name )
            {
                $temp[] = $assignment;
            }
        }
    
        $this->role_assignments = $temp;
    
        return $this;
    }

/**
<documentation><description><p>Sets <code>accessibilityCheckEnabled</code> and returns the calling object.</p></description>
<example>$s->setAccessibilityCheckEnabled( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessibilityCheckEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->accessibilityCheckEnabled = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets the default metadata set and returns the calling object.</p></description>
<example>$s->setDefaultMetadataSet( 
    $cascade->getAsset( a\MetadataSet::TYPE, '1f22ac858b7ffe834c5fe91e67ea0fcf' )
)->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function setDefaultMetadataSet( MetadataSet $m ) : Asset
    {
        if( $m == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_METADATA_SET . E_SPAN );
        }
        $this->getProperty()->defaultMetadataSetId   = $m->getId();
        $this->getProperty()->defaultMetadataSetPath = $m->getPath();
        return $this;
    }

/**
<documentation><description><p>Set <code>extensionsToStrip</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setExtensionsToStrip( $ext=NULL )
    {
        $this->getProperty()->extensionsToStrip = $ext;
        return $this;
    }

/**
<documentation><description><p>Sets <code>externalLinkCheckOnPublish</code> and returns the calling object.</p></description>
<example>$s->setExternalLinkCheckOnPublish( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setExternalLinkCheckOnPublish( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->externalLinkCheckOnPublish = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>inheritDataChecksEnabled</code> and returns the calling object.</p></description>
<example>$s->setInheritDataChecksEnabled( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setInheritDataChecksEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->inheritDataChecksEnabled = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>inheritNamingRules</code> and returns the
calling object. Note that when this property is set to <code>true</code>,
<code>namingRuleCase</code>, <code>namingRuleSpacing</code> and <code>namingRuleAssets</code> cannot be modified.</p></description>
<example>$s->setInheritNamingRules( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setInheritNamingRules( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->inheritNamingRules = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>linkCheckEnabled</code> and returns the calling object.</p></description>
<example>$s->setLinkCheckEnabled( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setLinkCheckEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->linkCheckEnabled = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>linkCheckerEnabled</code> and returns the calling object.</p></description>
<example>$s->setLinkCheckerEnabled( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setLinkCheckerEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->linkCheckerEnabled = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>namingRuleAssets</code> and returns the calling
object. Note that when this method is called successfully, <code>inheritNamingRules</code>
will be set to <code>false</code>. Also note that whatever asset types are already in the
<code>namingRuleAssets</code> array will be kept.</p></description>
<example>$s->setNamingRuleAssets( array( "file" ) )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setNamingRuleAssets( $assets=NULL ) : Asset
    {
        if( is_array( $assets ) )
        {
            foreach( $assets as $asset )
            {
                if( !is_string( $asset ) || !in_array( $asset, self::ASSETS ) )
                {
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "The " . $asset->toString() . " is not acceptable." .
                        E_SPAN );
                }
            }
        }

        $this->getProperty()->inheritNamingRules = false;
    
        if( isset( $this->getProperty()->namingRuleAssets ) )
            $asset_array = $this->getProperty()->namingRuleAssets;
        else
            $asset_array = array();
        
        foreach( $assets as $asset )
        {
            if( !in_array( $asset, $asset_array ) )
            {
                $asset_array[] = $asset;
            }
        }
    
        $this->getProperty()->namingRuleAssets = $asset_array;
    
        return $this;
    }

/**
<documentation><description><p>Sets <code>namingRuleCase</code> and returns the calling
object. Note that when this method is called successfully, <code>inheritNamingRules</code>
will be set to <code>false</code>.</p></description>
<example>$s-></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setNamingRuleCase( string $case=NULL ) : Asset
    {
        if( !is_null( $case ) && $case != self::UPPER_CASE && 
            $case != self::LOWER_CASE && $case != self::ANY_CASE )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $case is not acceptable." . E_SPAN );
        $this->getProperty()->inheritNamingRules = false;
        $this->getProperty()->namingRuleCase     = $case;
        return $this;
    }

/**
<documentation><description><p>Sets <code>namingRuleSpacing</code> and returns the calling
object. Note that when this method is called successfully, <code>inheritNamingRules</code>
will be set to <code>false</code>.</p></description>
<example>$s-></example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setNamingRuleSpacing( string $space=NULL ) : Asset
    {
        if( !is_null( $space ) && $space != self::HYPHEN_SPACE &&
            $space != self::ALLOW_SPACE &&
            $space != self::UNDERSCORE_SPACE && $space != self::REMOVE_SPACE )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $space is not acceptable." . E_SPAN );
        $this->getProperty()->inheritNamingRules = false;
        $this->getProperty()->namingRuleSpacing  = $space;
        return $this;
    }

/**
<documentation><description><p>Sets <code>getRecycleBinExpiration</code> and returns the calling object.</p></description>
<example>$s->setRecycleBinExpiration( a\Site::NEVER )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setRecycleBinExpiration( string $e ) : Asset
    {
        if( $e != self::NEVER && $e != self::ONE && 
            $e != self::FIFTEEN && $e != self::THIRTY
        )
        {
            throw new e\UnacceptableValueException( 
                S_SPAN . "Unacceptable value: $e" . E_SPAN );
        }
        $this->getProperty()->recycleBinExpiration = $e;
        return $this;
    }

/**
<documentation><description><p>Sets <code>siteAssetFactoryContainerId</code> and <code>siteAssetFactoryContainerPath</code> and returns the calling object.</p></description>
<example>$s->setSiteAssetFactoryContainer( 
    $cascade->getAsset( a\AssetFactoryContainer::TYPE,
    '1f217d838b7ffe834c5fe91e9832f910' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setSiteAssetFactoryContainer( AssetFactoryContainer $a=NULL ) : Asset
    {
        if( $a == NULL )
        {
            $this->getProperty()->siteAssetFactoryContainerId   = NULL;
            $this->getProperty()->siteAssetFactoryContainerPath = NULL;
            return $this;
        }
        $this->getProperty()->siteAssetFactoryContainerId   = $a->getId();
        $this->getProperty()->siteAssetFactoryContainerPath = $a->getPath();
        return $this;
    }

/**
<documentation><description><p>Sets <code>siteImproveUrl</code> and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setSiteImproveUrl( string $u="" ) : Asset
    {
        $this->getProperty()->siteImproveUrl = $u;
        return $this;
    }

/**
<documentation><description><p>Sets <code>spellCheckEnabled</code> and returns the calling object.</p></description>
<example>$s->setSpellCheckEnabled( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSpellCheckEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->spellCheckEnabled = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>siteStartingPageId</code> and <code>siteStartingPagePath</code> and returns the calling object.</p></description>
<example>$s->setStartingPage( 
    $cascade->getAsset( a\Page::TYPE, 
    '1f2376798b7ffe834c5fe91ead588ce1' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setStartingPage( Page $p=NULL ) : Asset
    {
        if( $p == NULL )
        {
            $this->getProperty()->siteStartingPageId   = NULL;
            $this->getProperty()->siteStartingPagePath = NULL;
            return $this;
        }
        $this->getProperty()->siteStartingPageId   = $p->getId();
        $this->getProperty()->siteStartingPagePath = $p->getPath();
        return $this;
    }

/**
<documentation><description><p>ets <code>unpublishOnExpiration</code> and returns the calling object.</p></description>
<example>$s->setUnpublishOnExpiration( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setUnpublishOnExpiration( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->unpublishOnExpiration = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>url</code> and returns the calling object.</p></description>
<example>$s->setUrl( 'http://www.upstate.edu/tuw-test' )->edit();</example>
<return-type>string </return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setUrl( string $u ) : Asset
    {
        if( trim( $u ) == "" )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
        }
    
        $this->getProperty()->url = $u;
        return $this;
    }

    private function processRoleAssignments()
    {
        $this->role_assignments = array();
        
        if( $this->getService()->isSoap() )
        {
            if( isset( $this->getProperty()->roleAssignments ) && 
                isset( $this->getProperty()->roleAssignments->roleAssignment ) )
            {
                $ra = $this->getProperty()->roleAssignments->roleAssignment;
            }
        }
        elseif( $this->getService()->isRest() )
        {
            if( isset( $this->getProperty()->roleAssignments ) )
            {
                $ra = $this->getProperty()->roleAssignments;
            }
        }
    
        if( isset( $ra ) )
        {
            if( !is_array( $ra ) )
            {
                $ra = array( $ra );
            }
        
            foreach( $ra as $role_assignment )
            {
                $this->role_assignments[] = new p\RoleAssignment( $role_assignment );
            }
        }
    }

    private $role_assignments;
    private $base_folder;
    private $root_asset_factory_container;
    private $root_connector_container;
    private $root_content_type_container;
    private $root_data_definition_container;
    private $root_metadata_set_container;
    private $root_page_configuration_set_container;
    private $root_publish_set_container;
    private $root_site_destination_container;
    private $root_transport_container;
    private $root_workflow_definition_container;
    private $root_site_asset_factory_container;
}
?>