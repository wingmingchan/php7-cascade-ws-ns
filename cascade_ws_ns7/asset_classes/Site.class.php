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

class Site extends ScheduledPublishing
{
    const DEBUG    = false;
    const DUMP     = false;
    const TYPE     = c\T::SITE;
    const NEVER    = c\T::NEVER;
    const ONE      = c\T::ONE;
    const FIFTEEN  = c\T::FIFTEEN;
    const THIRTY   = c\T::THIRTY;
    
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->processRoleAssignments();
    }
    
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
    
    public function getAssetTree()
    {
        return $this->getBaseFolder()->getAssetTree();
    }
    
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
    
    public function getBaseFolderAssetTree()
    {
        return $this->getBaseFolder()->getAssetTree();
    }
    
    public function getBaseFolderId()
    {
        return $this->getRootFolderId();
    }
    
    public function getCssClasses()
    {
        return $this->getProperty()->cssClasses;
    }
    
    public function getCssFileId()
    {
        return $this->getProperty()->cssFileId;
    }

    public function getCssFilePath()
    {
        return $this->getProperty()->cssFilePath;
    }

    public function getCssFileRecycled()
    {
        return $this->getProperty()->cssFileRecycled;
    }

    public function getDefaultMetadataSetId()
    {
        return $this->getProperty()->defaultMetadataSetId;
    }
    
    public function getDefaultMetadataSetPath()
    {
        return $this->getProperty()->defaultMetadataSetPath;
    }
    
    public function getExternalLinkCheckOnPublish()
    {
        return $this->getProperty()->externalLinkCheckOnPublish;
    }
    
    public function getLinkCheckerEnabled()
    {
        return $this->getProperty()->linkCheckerEnabled;
    }
    
    public function getRecycleBinExpiration()
    {
        return $this->getProperty()->recycleBinExpiration;
    }
    
    public function getRootAssetFactoryContainer()
    {
        return Asset::getAsset(
            $this->getService(),
            AssetFactoryContainer::TYPE, 
            $this->getProperty()->rootAssetFactoryContainerId );
    }

    public function getRootAssetFactoryContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            AssetFactoryContainer::TYPE,
            $this->getProperty()->rootAssetFactoryContainerId )->
                getAssetTree();
    }
    
    public function getRootAssetFactoryContainerId()
    {
        return $this->getProperty()->rootAssetFactoryContainerId;
    }
    
    public function getRootAssetTree()
    {
        return $this->getAssetTree();
    }
    
    public function getRootConnectorContainer()
    {
        return self::getAsset(
            $this->getService(),
            ConnectorContainer::TYPE, 
            $this->getProperty()->rootConnectorContainerId );
    }

    public function getRootConnectorContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            ConnectorContainer::TYPE,
            $this->getProperty()->rootConnectorContainerId )->
                getAssetTree();
    }
    
    public function getRootConnectorContainerId()
    {
        return $this->getProperty()->rootConnectorContainerId;
    }
    
    public function getRootContentTypeContainer()
    {
        return Asset::getAsset( $this->getService(),
            ContentTypeContainer::TYPE,
            $this->getProperty()->rootContentTypeContainerId );
    }
    
    public function getRootContentTypeContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            ContentTypeContainer::TYPE,
            $this->getProperty()->rootContentTypeContainerId )->
                getAssetTree();
    }
    
    public function getRootContentTypeContainerId()
    {
        return $this->getProperty()->rootContentTypeContainerId;
    }
    
    public function getRootDataDefinitionContainer()
    {
        return Asset::getAsset( $this->getService(),
            DataDefinitionContainer::TYPE,
            $this->getProperty()->rootDataDefinitionContainerId );
    }
    
    public function getRootDataDefinitionContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            DataDefinitionContainer::TYPE,
            $this->getProperty()->rootDataDefinitionContainerId )->
                getAssetTree();
    }
    
    public function getRootDataDefinitionContainerId()
    {
        return $this->getProperty()->rootDataDefinitionContainerId;
    }
    
    public function getRootFolderAssetTree()
    {
        return $this->getAssetTree();
    }
    
    public function getRootFolderId()
    {
        return $this->getProperty()->rootFolderId;
    }
    
    public function getRootMetadataSetContainer()
    {
        return Asset::getAsset( $this->getService(),
            MetadataSetContainer::TYPE,
            $this->getProperty()->rootMetadataSetContainerId );
    }
    
    public function getRootMetadataSetContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            MetadataSetContainer::TYPE,
            $this->getProperty()->rootMetadataSetContainerId )->
                getAssetTree();
    }
    
    public function getRootMetadataSetContainerId()
    {
        return $this->getProperty()->rootMetadataSetContainerId;
    }
    
    public function getRootPageConfigurationSetContainer()
    {
        return Asset::getAsset( $this->getService(),
            PageConfigurationSetContainer::TYPE,
            $this->getProperty()->rootPageConfigurationSetContainerId );
    }
    
    public function getRootPageConfigurationSetContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            PageConfigurationSetContainer::TYPE,
            $this->getProperty()->rootPageConfigurationSetContainerId )->
                getAssetTree();
    }
    
    public function getRootPageConfigurationSetContainerId()
    {
        return $this->getProperty()->rootPageConfigurationSetContainerId;
    }
    
    public function getRootPublishSetContainer()
    {
        return Asset::getAsset( $this->getService(),
            PublishSetContainer::TYPE,
            $this->getProperty()->rootPublishSetContainerId );
    }
    
    public function getRootPublishSetContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            PublishSetContainer::TYPE,
            $this->getProperty()->rootPublishSetContainerId )->
                getAssetTree();
    }
    
    public function getRootPublishSetContainerId()
    {
        return $this->getProperty()->rootPublishSetContainerId;
    }
    
    public function getRootSiteDestinationContainer()
    {
        return Asset::getAsset( $this->getService(),
            SiteDestinationContainer::TYPE,
            $this->getProperty()->rootSiteDestinationContainerId );
    }
    
    public function getRootSiteDestinationContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            SiteDestinationContainer::TYPE,
            $this->getProperty()->rootSiteDestinationContainerId )->
                getAssetTree();
    }
    
    public function getRootSiteDestinationContainerId()
    {
        return $this->getProperty()->rootSiteDestinationContainerId;
    }
    
    public function getRootTransportContainer()
    {
        return Asset::getAsset( $this->getService(),
            TransportContainer::TYPE,
            $this->getProperty()->rootTransportContainerId );
    }
    
    public function getRootTransportContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            TransportContainer::TYPE,
            $this->getProperty()->rootTransportContainerId )->
                getAssetTree();
    }
    
    public function getRootTransportContainerId()
    {
        return $this->getProperty()->rootTransportContainerId;
    }
    
    public function getRootWorkflowDefinitionContainer()
    {
        return Asset::getAsset( $this->getService(),
            WorkflowDefinitionContainer::TYPE,
            $this->getProperty()->rootWorkflowDefinitionContainerId );
    }
    
    public function getRootWorkflowDefinitionContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            WorkflowDefinitionContainer::TYPE,
            $this->getProperty()->rootWorkflowDefinitionContainerId )->
                getAssetTree();
    }
    
    public function getRootWorkflowDefinitionContainerId()
    {
        return $this->getProperty()->rootWorkflowDefinitionContainerId;
    }
    
    public function getSiteAssetFactoryContainer()
    {
        return Asset::getAsset( $this->getService(),
            AssetFactoryContainer::TYPE,
            $this->getProperty()->siteAssetFactoryContainerId );
    }
    
    public function getSiteAssetFactoryContainerAssetTree()
    {
        return Asset::getAsset( $this->getService(),
            AssetFactoryContainer::TYPE,
            $this->getProperty()->siteAssetFactoryContainerId )->
                getAssetTree();
    }
    
    public function getScheduledPublishDestinationMode()
    {
        // all-destinations or selected-destinations
        return $this->getProperty()->scheduledPublishDestinationMode;
    }
    
    public function getScheduledPublishDestinations()
    {
        return $this->getProperty()->scheduledPublishDestinations;
    }
    
    public function getSiteAssetFactoryContainerId()
    {
        return $this->getProperty()->siteAssetFactoryContainerId;
    }
    
    public function getSiteAssetFactoryContainerPath()
    {
        return $this->getProperty()->siteAssetFactoryContainerPath;
    }
    
    public function getSiteStartingPageId()
    {
        return $this->getProperty()->siteStartingPageId;
    }
    
    public function getSiteStartingPagePath()
    {
        return $this->getProperty()->siteStartingPagePath;
    }
    
    public function getSiteStartingPageRecycled()
    {
        return $this->getProperty()->siteStartingPageRecycled;
    }
    
    public function getUnpublishOnExpiration()
    {
        return $this->getProperty()->unpublishOnExpiration;
    }
    
    public function getUrl()
    {
        return $this->getProperty()->url;
    }
    
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
    
    public function setExternalLinkCheckOnPublish( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->externalLinkCheckOnPublish = $bool;
        return $this;
    }
    
    public function setLinkCheckerEnabled( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->linkCheckerEnabled = $bool;
        return $this;
    }
    
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
    
    public function setUnpublishOnExpiration( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->unpublishOnExpiration = $bool;
        return $this;
    }
    
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