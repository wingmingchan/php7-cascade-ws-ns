<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/26/2016 Added leaveGroup and isInGroup.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class User extends Asset
{
    const DEBUG = false;
    const TYPE  = c\T::USER;
    
    public function disable() : Asset
    {
        $this->getProperty()->enabled = false;
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
        $asset                                    = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
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
    
    public function enable() : Asset
    {
        $this->getProperty()->enabled = true;
        return $this;
    }

    public function getAuthType() : string
    {
        return $this->getProperty()->authType;
    }
    
    public function getDefaultGroup() : string
    {
        return $this->getProperty()->defaultGroup;
    }
    
    public function getDefaultSiteId() : string
    {
        return $this->getProperty()->defaultSiteId;
    }
    
    public function getDefaultSiteName() : string
    {
        return $this->getProperty()->defaultSiteName;
    }
    
    public function getEnabled() : string
    {
        return $this->getProperty()->enabled;
    }
    
    public function getId() : string
    {
        return $this->getProperty()->username;
    }
    
    public function getEmail() : string
    {
        return $this->getProperty()->email;
    }
    
    public function getFullName() : string
    {
        return $this->getProperty()->fullName;
    }
    
    public function getGroups() : string
    {
        return $this->getProperty()->groups;
    }
    
    public function getName() : string
    {
        return $this->getProperty()->username;
    }
    
    public function getRole() : string
    {
        return $this->getProperty()->role;
    }
    
    public function getPassword() : string
    {
        return $this->getProperty()->password;
    }
    
    public function getUserName() : string
    {
        return $this->getProperty()->username;
    }
    
    public function isInGroup( Group $group ) : bool
    {
        $users = $group->getUsers();
        
        if( strpos( $users, Group::DELIMITER . $this->getProperty()->username . Group::DELIMITER ) !== false )
            return true;
            
        return false;
    }
    
    public function joinGroup( Group $g ) : Asset
    {
        $g->addUser( Asset::getAsset( $this->getService(),
            User::TYPE,
            $this->getProperty()->username ) )->edit();
        return $this;
    }
    
    public function leaveGroup( Group $g ) : Asset
    {
        $g->removeUser( Asset::getAsset( $this->getService(),
            User::TYPE,
            $this->getProperty()->username ) )->edit();
        return $this;
    }
    
    public function setDefaultGroup( Group $group=NULL ) : Asset
    {
        if( isset( $group ) )
        {
            $this->getProperty()->defaultGroup   = $group->getName();
        }
        return $this;
    }
    
    public function setDefaultSite( Site $site=NULL )
    {
        if( isset( $site ) )
        {
            $this->getProperty()->defaultSiteId   = $site->getId();
            $this->getProperty()->defaultSiteName = $site->getName();
        }
        else
        {
            $this->getProperty()->defaultSiteId   = NULL;
            $this->getProperty()->defaultSiteName = NULL;
        }
        return $this;
    }
    
    public function setEnabled( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( "The value $bool must be a boolean." );

        $this->getProperty()->enabled = $bool;
        return $this;
    }
    
    public function setEmail( $email )
    {
        if( trim( $email ) == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_EMAIL . E_SPAN );

        $this->getProperty()->email = $email;
        return $this;
    }
    
    public function setFullName( $name )
    {
        if( trim( $name ) == '' )
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_FULL_NAME . E_SPAN );

        $this->getProperty()->fullName = $name;
        return $this;
    }
    
    public function setPassword( $pw )
    {
        if( trim( $pw ) == '' )
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );

        $this->getProperty()->password = $pw;
        return $this;
    }
}
?>