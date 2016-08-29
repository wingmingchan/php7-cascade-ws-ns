<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/28/2016 Added setAccessRights, denyAccessToAllGroups, denyAccessToAllUsers.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_asset as a;

class AccessRightsInformation extends Property
{
    const DEBUG = false;
    
    public function __construct( 
        \stdClass $ari=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $ari ) )
        {
            $this->identifier  = new Identifier( $ari->identifier );
            
            if( isset( $ari->aclEntries ) && isset( $ari->aclEntries->aclEntry ) )
            {
                $this->processAclEntries( $ari->aclEntries->aclEntry );
            }
            
            $this->all_level  = $ari->allLevel;
        }
    }
    
    public function addGroupReadAccess( a\Group $g )
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting read access to " . $g->getName() );  }
        $this->setAccess( $g, c\T::READ );
        return $this;
    }
    
    public function addGroupWriteAccess( a\Group $g )
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting write access to " . $g->getName() );  }
        $this->setAccess( $g, c\T::WRITE );
        return $this;
    }
    
    public function addUserReadAccess( a\User $u )
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting read access to " . $u->getName() );  }
        $this->setAccess( $u, c\T::READ );
        return $this;
    }
    
    public function addUserWriteAccess( a\User $u )
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting write access to " . $u->getName() );  }
        $this->setAccess( $u, c\T::WRITE );
        return $this;
    }
    
    public function clearPermissions()
    {
        $this->acl_entries = array();
        $this->all_level   = c\T::NONE;
        return $this;
    }
    
    public function denyAccessToAllGroups()
    {
        foreach( $this->acl_entries as $entry )
        {
            if( $entry->getType() != c\T::GROUP )
            {
                $temp[] = $entry;
            }
        }
        $this->acl_entries = $temp;
        return $this;
    }
    
    public function denyAccessToAllUsers()
    {
        foreach( $this->acl_entries as $entry )
        {
            if( $entry->getType() != c\T::USER )
            {
                $temp[] = $entry;
            }
        }
        $this->acl_entries = $temp;
        return $this;
    }

    public function denyGroupAccess( a\Group $g )
    {
        $this->denyAccess( $g, $g->getType() );
        return $this;
    }
    
    public function denyUserAccess( a\User $u )
    {
        $this->denyAccess( $u, $u->getType() );
        return $this;
    }
    
    public function display()
    {
        echo S_PRE;
        var_dump( $this->toStdClass() );
        echo E_PRE;
        return $this;
    }
    
    public function getAclEntries()
    {
        return $this->acl_entries;
    }
    
    public function getAllLevel()
    {
        return $this->all_level;
    }
    
    public function getGroupLevel( a\Group $g )
    {
        $entry = $this->getEntry( $g );
        
        if( isset( $entry ) )
        {
            return $entry->getLevel();
        }
        return NULL;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    public function getUserLevel( a\User $u )
    {
        $entry = $this->getEntry( $u );
        
        if( isset( $entry ) )
        {
            return $entry->getLevel();
        }
        return NULL;
    }

    public function grantGroupReadAccess( a\Group $g )
    {
        return $this->addGroupReadAccess( $g );
    }
    
    public function grantGroupWriteAccess( a\Group $g )
    {
        return $this->addGroupWriteAccess( $g );
    }
    
    public function grantUserReadAccess( a\User $u )
    {
        return $this->addUserReadAccess( $u );
    }
    
    public function grantUserWriteAccess( a\User $u )
    {
        return $this->addUserWriteAccess( $u );
    }
    
    public function hasGroup( a\Group $g )
    {
        return $this->getEntry( $g ) != NULL;
    }

    public function hasUser( a\User $u )
    {
        return $this->getEntry( $u ) != NULL;
    }

    public function setAllLevel( $level )
    {
        if( !c\LevelValues::isLevel( $level ) )
        {
            throw new e\UnacceptableValueException( "The level $level is unacceptable." );
        }
    
        $this->all_level = $level;
        return $this;
    }
    
    public function setGroupReadAccess( a\Group $g )
    {
        return $this->addGroupReadAccess( $g );
    }
    
    public function setGroupWriteAccess( a\Group $g )
    {
        return $this->addGroupWriteAccess( $g );
    }
    
    public function setUserReadAccess( a\User $u )
    {
        return $this->addUserReadAccess( $u );
    }
    
    public function setUserWriteAccess( a\User $u )
    {
        return $this->addUserWriteAccess( $u );
    }
    
    public function setAccessRights( a\Asset $a, $level )
    {
        $this->setAccess( $a, $level );
        return $this;
    }
    
    public function toStdClass()
    {
        $obj = new \stdClass();
        
        $obj->identifier = $this->identifier->toStdClass();
        
        $entry_array = array();
        
        foreach( $this->acl_entries as $entry )
        {
            $entry_array[] = $entry->toStdClass();
        }
        
        $obj->aclEntries           = new \stdClass();
        $obj->aclEntries->aclEntry = $entry_array;
        $obj->allLevel             = $this->all_level;
        
        return $obj;
    }
    
    private function denyAccess( a\Asset $a, $type )
    {
        $temp = array();
        
        foreach( $this->acl_entries as $entry )
        {
            if( $entry->getType() != $type || 
                $entry->getName() != $a->getName() )
            {
                $temp[] = $entry;
            }
        }
        $this->acl_entries = $temp;
    }
    
    private function getEntry( a\Asset $a )
    {
        if( count( $this->acl_entries ) > 0 )
        {
            foreach( $this->acl_entries as $entry )
            {
                if( $entry->getType() == $a->getType() && 
                    $entry->getName() == $a->getName() )
                {
                    return $entry;
                }
            }
        }
        return NULL;
    }
    
    private function processAclEntries( $entries )
    {
        $this->acl_entries = array();

        if( !is_array( $entries ) )
        {
            $entries = array( $entries );
        }
        
        foreach( $entries as $entry )
        {
            // skip empty entries
            if( isset( $entry->name ) )
            {
                $this->acl_entries[] = new AclEntry( $entry );
            }
        }
    }
    
    /* $a: either a group or a user */
    private function setAccess( a\Asset $a, $level )
    {
        $type = $a->getType();
        
        if( $type != c\T::USER && $type != c\T::GROUP )
        {
            throw new e\WrongAssetTypeException( c\M::ACCESS_TO_USERS_GROUPS );
        }
        
        if( !c\LevelValues::isLevel( $level ) )
        {
            throw new e\UnacceptableValueException( "The level $level is unacceptable." );
        }
        
        $entry = $this->getEntry( $a );
        
        // not exist
        if( $entry == NULL )
        {
            $entry_std           = new \stdClass();
            $entry_std->level    = $level;
            $entry_std->type     = $a->getType();
            $entry_std->name     = $a->getName();
            $this->acl_entries[] = new AclEntry( $entry_std );
        }
        else
        {
            $entry->setLevel( $level );
        }
    }
    
    private $identifier;
    private $acl_entries;
    private $all_level;
}
?>