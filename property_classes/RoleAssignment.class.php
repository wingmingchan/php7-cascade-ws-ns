<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_asset as a;

class RoleAssignment extends Property
{
    const DELIMITER = ',';

    public function __construct( 
        \stdClass $ra=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $ra ) )
        {
            $this->role_id   = $ra->roleId;
            $this->role_name = $ra->roleName;
            $this->users     = $ra->users;
            $this->groups    = $ra->groups;
        }
    }
    
    public function addGroup( a\Group $g ) 
    {
        if( $g == NULL )
            throw new e\NullAssetException(
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
    
        $g_name      = $g->getName();
        $group_array = explode( self::DELIMITER, $this->groups );
        $temp        = array();
        
        foreach( $group_array as $group )
        {
            if( $group != "" )
            {
                $temp[] = $group;
            }
        }
        $group_array = $temp;
        
        if( !in_array( $g_name, $group_array ) )
        {
            $group_array[] = $g_name;
        }
        
        $this->groups = implode( self::DELIMITER, $group_array );
        return $this;
    }

    public function addUser( a\User $u ) 
    {
        if( $u == NULL )
            throw new e\NullAssetException(
                S_SPAN . c\M::NULL_USER . E_SPAN );
                
        $u_name     = $u->getName();
        $user_array = explode( self::DELIMITER, $this->users );
        $temp       = array();
        
        foreach( $user_array as $user )
        {
            if( $user != "" )
            {
                $temp[] = $user;
            }
        }
        $user_array = $temp;
        
        if( !in_array( $u_name, $user_array ) )
        {
            $user_array[] = $u_name;
        }
        
        $this->users = implode( self::DELIMITER, $user_array );
        return $this;
    }
    
    public function getGroups()
    {
        return $this->groups;
    }
    
    public function getRoleId()
    {
        return $this->role_id;
    }
    
    public function getRoleName()
    {
        return $this->role_name;
    }
    
    public function getUsers()
    {
        return $this->users;
    }
    
    public function toStdClass()
    {
        $obj           = new \stdClass();
        $obj->roleId   = $this->role_id;
        $obj->roleName = $this->role_name;
        $obj->users    = $this->users;
        $obj->groups   = $this->groups;
        return $obj;
    }

    private $role_id;
    private $role_name;
    private $users; // NULL or string, use commas
    private $groups;
}
?>
