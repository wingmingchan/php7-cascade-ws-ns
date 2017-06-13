<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/13/2017 Added WSDL.
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
<p>A <code>RoleAssignment</code> object represents a <code>roleAssignment</code> property found in a <a href="web-services/api/asset-classes/site"><code>a\Site</code></a> object.</p>
<h2>Structure of <code>roleAssignment</code></h2>
<pre>roleAssignment
  roleId
  roleName
  users
  groups
</pre>
<p>WSDL:</p>
<pre>&lt;complexType name="role-assignments">
  &lt;sequence>
    &lt;element maxOccurs="unbounded" minOccurs="0" name="roleAssignment" type="impl:role-assignment"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="role-assignment">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="0" name="roleId" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="roleName" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="users" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="groups" type="xsd:string"/>
  &lt;/sequence>
&lt;/complexType>
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class RoleAssignment extends Property
{
    const DELIMITER = ',';

/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Adds a group to <code>groups</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
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

/**
<documentation><description><p>Adds a user to <code>users</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
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
    
/**
<documentation><description><p>Returns <code>groups</code> (a string).</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getGroups() : string
    {
        return $this->groups;
    }
    
/**
<documentation><description><p>Returns <code>roleId</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRoleId()
    {
        return $this->role_id;
    }
    
/**
<documentation><description><p>Returns <code>roleName</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRoleName()
    {
        return $this->role_name;
    }
    
/**
<documentation><description><p>Returns <code>users</code> (a string).</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getUsers() : string
    {
        return $this->users;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
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