<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<documentation><description><h2>Introduction</h2>
<p>A <code>SiteAbilities</code> object represents the <code>siteAbilities</code> property found in a role asset. This class is a sub-class of <a href="/web-services/api/property-classes/abilities"><code>Abilities</code></a>.</p>
<h2>Properties of <code>siteAbilities</code></h2>
<p>Besides the 49 properties (Cascade 8) shared with the sibling class <a href="/web-services/api/property-classes/global-abilities"><code>GlobalAbilities</code></a> (which are defined in the parent class <a href="/web-services/api/property-classes/abilities"><code>Abilities</code></a>), this class has two more additional properties:</p>
<pre>accessConnectors
accessDestinations
</pre>
<p>WSDL:</p>
<pre>&lt;complexType name="site-abilities">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="0" name="bypassAllPermissionsChecks" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="uploadImagesFromWysiwyg" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="multiSelectCopy" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="multiSelectPublish" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="multiSelectMove" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="multiSelectDelete" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="editPageLevelConfigurations" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="editPageContentType" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="editDataDefinition" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="publishReadableHomeAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="publishWritableHomeAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="editAccessRights" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="viewVersions" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="activateDeleteVersions" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessAudits" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="bypassWorkflow" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="assignApproveWorkflowSteps" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="deleteWorkflows" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="breakLocks" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="assignWorkflowsToFolders" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="bypassAssetFactoryGroupsNewMenu" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="bypassDestinationGroupsWhenPublishing" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="bypassWorkflowDefintionGroupsForFolders" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessManageSiteArea" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessAssetFactories" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessConfigurationSets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessDataDefinitions" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessMetadataSets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessPublishSets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessDestinations" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessTransports" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessWorkflowDefinitions" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessContentTypes" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessConnectors" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="publishReadableAdminAreaAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="publishWritableAdminAreaAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="importZipArchive" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="bulkChange" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="recycleBinViewRestoreUserAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="recycleBinDeleteAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="recycleBinViewRestoreAllAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="moveRenameAssets" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="diagnosticTests" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="alwaysAllowedToToggleDataChecks" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="viewPublishQueue" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="reorderPublishQueue" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="cancelPublishJobs" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="sendStaleAssetNotifications" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="brokenLinkReportAccess" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="brokenLinkReportMarkFixed" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="accessEditorConfigurations" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="bypassWysiwygEditorRestrictions" type="xsd:boolean"/>
  &lt;/sequence>
&lt;/complexType>
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class SiteAbilities extends Abilities
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
        }
        
        $this->access_connectors       = $a->accessConnectors;
        $this->access_destinations     = $a->accessDestinations;
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
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj = parent::toStdClass();
        $obj->accessDestinations   = $this->access_destinations;
        $obj->accessConnectors     = $this->access_connectors;
        
        return $obj;
    }
    
    private $access_destinations;
    private $access_connectors;
}
?>