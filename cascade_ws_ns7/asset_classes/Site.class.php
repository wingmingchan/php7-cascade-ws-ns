<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/28/2016 Added a set of get methods to return root containers.
  * 10/29/2015 Added getExternalLinkCheckOnPublish and setExternalLinkCheckOnPublish.
  * 5/28/2015 Added namespaces.
  * 8/12/2014 Added getUnpublishOnExpiration, setUnpublishOnExpiration, getLinkCheckerEnabled, and setLinkCheckerEnabled.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
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
    
/**
<documentation><description><p></p></description>
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function addRole( Role $r )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function addGroupToRole( Role $r, Group $g )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function addUserToRole( Role $r, User $u )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
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
            if( !isset( $site->timeToPublish ) )
                unset( $site->timeToPublish );
            // fix the time unit
            else if( strpos( $site->timeToPublish, '-' ) !== false )
            {
                $pos = strpos( $site->timeToPublish, '-' );
                $site->timeToPublish = substr( $site->timeToPublish, 0, $pos );
            }
            
            if( !isset( $site->publishIntervalHours ) )
                unset( $site->publishIntervalHours );
                
            if( !isset( $site->publishDaysOfWeek ) )
                unset( $site->publishDaysOfWeek );
                
            if( !isset( $site->cronExpression ) )
                unset( $site->cronExpression );
        }

        $assignment_count      = count( $this->role_assignments );
        $site->roleAssignments = new \stdClass();
        
        if( $assignment_count == 1 )
        {
            $site->roleAssignments->roleAssignment = $this->role_assignments[ 0 ]->toStdClass();
        }
        else if( $assignment_count > 1 )
        {
            $site->roleAssignments->roleAssignment = array();
            
            foreach( $this->role_assignments as $assignment )
            {
                $site->roleAssignments->roleAssignment[] = $assignment->toStdClass();
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getAssetTree()
    {
        return $this->getBaseFolder()->getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolder()
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolderAssetTree()
    {
        return $this->getBaseFolder()->getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getBaseFolderId()
    {
        return $this->getRootFolderId();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCssClasses()
    {
        return $this->getProperty()->cssClasses;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCssFileId()
    {
        return $this->getProperty()->cssFileId;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCssFilePath()
    {
        return $this->getProperty()->cssFilePath;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCssFileRecycled()
    {
        return $this->getProperty()->cssFileRecycled;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultMetadataSetId()
    {
        return $this->getProperty()->defaultMetadataSetId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultMetadataSetPath()
    {
        return $this->getProperty()->defaultMetadataSetPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getExternalLinkCheckOnPublish()
    {
        return $this->getProperty()->externalLinkCheckOnPublish;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLinkCheckerEnabled()
    {
        return $this->getProperty()->linkCheckerEnabled;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRecycleBinExpiration()
    {
        return $this->getProperty()->recycleBinExpiration;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainer()
    {
        return Asset::getAsset(
            $this->getService(),
            AssetFactoryContainer::TYPE, 
            $this->getProperty()->rootAssetFactoryContainerId );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            AssetFactoryContainer::TYPE,
            $this->getProperty()->rootAssetFactoryContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetFactoryContainerId()
    {
        return $this->getProperty()->rootAssetFactoryContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootAssetTree()
    {
        return $this->getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainer()
    {
        return self::getAsset(
            $this->getService(),
            ConnectorContainer::TYPE, 
            $this->getProperty()->rootConnectorContainerId );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            ConnectorContainer::TYPE,
            $this->getProperty()->rootConnectorContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootConnectorContainerId()
    {
        return $this->getProperty()->rootConnectorContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainer()
    {
        return Asset::getAsset( $this->getService(),
            ContentTypeContainer::TYPE,
            $this->getProperty()->rootContentTypeContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            ContentTypeContainer::TYPE,
            $this->getProperty()->rootContentTypeContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootContentTypeContainerId()
    {
        return $this->getProperty()->rootContentTypeContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainer()
    {
        return Asset::getAsset( $this->getService(),
            DataDefinitionContainer::TYPE,
            $this->getProperty()->rootDataDefinitionContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            DataDefinitionContainer::TYPE,
            $this->getProperty()->rootDataDefinitionContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootDataDefinitionContainerId()
    {
        return $this->getProperty()->rootDataDefinitionContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolderAssetTree()
    {
        return $this->getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootFolderId()
    {
        return $this->getProperty()->rootFolderId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainer()
    {
        return Asset::getAsset( $this->getService(),
            MetadataSetContainer::TYPE,
            $this->getProperty()->rootMetadataSetContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            MetadataSetContainer::TYPE,
            $this->getProperty()->rootMetadataSetContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootMetadataSetContainerId()
    {
        return $this->getProperty()->rootMetadataSetContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainer()
    {
        return Asset::getAsset( $this->getService(),
            PageConfigurationSetContainer::TYPE,
            $this->getProperty()->rootPageConfigurationSetContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            PageConfigurationSetContainer::TYPE,
            $this->getProperty()->rootPageConfigurationSetContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootPageConfigurationSetContainerId()
    {
        return $this->getProperty()->rootPageConfigurationSetContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainer()
    {
        return Asset::getAsset( $this->getService(),
            PublishSetContainer::TYPE,
            $this->getProperty()->rootPublishSetContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            PublishSetContainer::TYPE,
            $this->getProperty()->rootPublishSetContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootPublishSetContainerId()
    {
        return $this->getProperty()->rootPublishSetContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainer()
    {
        return Asset::getAsset( $this->getService(),
            SiteDestinationContainer::TYPE,
            $this->getProperty()->rootSiteDestinationContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            SiteDestinationContainer::TYPE,
            $this->getProperty()->rootSiteDestinationContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootSiteDestinationContainerId()
    {
        return $this->getProperty()->rootSiteDestinationContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainer()
    {
        return Asset::getAsset( $this->getService(),
            TransportContainer::TYPE,
            $this->getProperty()->rootTransportContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            TransportContainer::TYPE,
            $this->getProperty()->rootTransportContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootTransportContainerId()
    {
        return $this->getProperty()->rootTransportContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainer()
    {
        return Asset::getAsset( $this->getService(),
            WorkflowDefinitionContainer::TYPE,
            $this->getProperty()->rootWorkflowDefinitionContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            WorkflowDefinitionContainer::TYPE,
            $this->getProperty()->rootWorkflowDefinitionContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRootWorkflowDefinitionContainerId()
    {
        return $this->getProperty()->rootWorkflowDefinitionContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainer()
    {
        return Asset::getAsset( $this->getService(),
            AssetFactoryContainer::TYPE,
            $this->getProperty()->siteAssetFactoryContainerId );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            AssetFactoryContainer::TYPE,
            $this->getProperty()->siteAssetFactoryContainerId )->
                getAssetTree();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledPublishDestinationMode()
    {
        // all-destinations or selected-destinations
        return $this->getProperty()->scheduledPublishDestinationMode;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledPublishDestinations()
    {
        return $this->getProperty()->scheduledPublishDestinations;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerId()
    {
        return $this->getProperty()->siteAssetFactoryContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteAssetFactoryContainerPath()
    {
        return $this->getProperty()->siteAssetFactoryContainerPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPageId()
    {
        return $this->getProperty()->siteStartingPageId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPagePath()
    {
        return $this->getProperty()->siteStartingPagePath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSiteStartingPageRecycled()
    {
        return $this->getProperty()->siteStartingPageRecycled;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getUnpublishOnExpiration()
    {
        return $this->getProperty()->unpublishOnExpiration;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getUrl()
    {
        return $this->getProperty()->url;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasRole( Role $r )
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

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL )
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
                $service->createId( self::TYPE, $this->getProperty()->id ), $destination_std );
        else
            $service->publish( 
                $service->createId( self::TYPE, $this->getProperty()->id ) );
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function removeRole( Role $r )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setCssClasses( $classes )
    {
        if( trim( $classes ) == "" || $class == NULL )
        {
            $this->getProperty()->cssClasses = NULL;
            return $this;
        }
        
        if( $this->getProperty()->cssFileId == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . "The CSS file must be set first." . E_SPAN );
        }
        
        $this->getProperty()->cssClasses = $classes;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setCssFile( File $css=NULL, $classes=NULL )
    {
        if( $css == NULL )
        {
            $this->getProperty()->cssClasses  = NULL;
            $this->getProperty()->cssFileId   = NULL;
            $this->getProperty()->cssFilePath = NULL;
            return $this;
        }
        $this->getProperty()->cssClasses  = $classes;
        $this->getProperty()->cssFileId   = $css->getId();
        $this->getProperty()->cssFilePath = $css->getPath();
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setDefaultMetadataSet( MetadataSet $m )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setExternalLinkCheckOnPublish( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->externalLinkCheckOnPublish = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setLinkCheckerEnabled( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->linkCheckerEnabled = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setRecycleBinExpiration( $e )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setSiteAssetFactoryContainer( AssetFactoryContainer $a=NULL )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setStartingPage( Page $p=NULL )
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setUnpublishOnExpiration( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->unpublishOnExpiration = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setUrl( $u )
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
        
        if( isset( $this->getProperty()->roleAssignments ) && 
            isset( $this->getProperty()->roleAssignments->roleAssignment ) )
            $ra = $this->getProperty()->roleAssignments->roleAssignment;
        
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
}
?>