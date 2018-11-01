<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/12/2018 Changed return type of getGroups and getUsers, using
    StringUtility::attachStringWithDelimiter.
  * 7/18/2017 Replaced static WSDL code with call to getXMLFragments.
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
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>RoleAssignment</code> object represents a <code>roleAssignment</code> property found in a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/site.php\"><code>a\Site</code></a> object.</p>
<h2>Structure of <code>roleAssignment</code></h2>
<pre>roleAssignment
  roleId
  roleName
  users
  groups
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "role-assignments" ),
        array( "getComplexTypeXMLByName" => "role-assignment" ),
    ) );
return $doc_string;
?>
</description>
<postscript></postscript>
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
            if( isset( $ra->roleId ) )
                $this->role_id   = $ra->roleId;
            if( isset( $ra->roleName ) )
                $this->role_name = $ra->roleName;
            if( isset( $ra->users ) )
                $this->users     = $ra->users;
            if( isset( $ra->groups ) )
                $this->groups    = $ra->groups;
        }
    }
    
/**
<documentation><description><p>Adds a group to <code>groups</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function addGroup( a\Group $g ) : Property
    {
        if( $g == NULL )
        {
            throw new e\NullAssetException( S_SPAN . c\M::NULL_GROUP . E_SPAN );
        }

        $this->groups = u\StringUtility::attachStringWithDelimiter(
            $this->groups, self::DELIMITER, $g->getName() );
            
        return $this;
    }

/**
<documentation><description><p>Adds a user to <code>users</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function addUser( a\User $u ) : Property
    {
        if( $u == NULL )
        {
            throw new e\NullAssetException( S_SPAN . c\M::NULL_USER . E_SPAN );
        }
        
        $this->users = u\StringUtility::attachStringWithDelimiter(
            $this->users, self::DELIMITER, $u->getName() );
            
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>groups</code> (a string).</p></description>
<example></example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getGroups()
    {
        return isset( $this->groups ) ? $this->groups : NULL;
    }
    
/**
<documentation><description><p>Returns <code>roleId</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getRoleId() : string
    {
        return $this->role_id;
    }
    
/**
<documentation><description><p>Returns <code>roleName</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRoleName() : string
    {
        return $this->role_name;
    }
    
/**
<documentation><description><p>Returns <code>users</code> (a string).</p></description>
<example></example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getUsers()
    {
        return isset( $this->users ) ? $this->users : NULL;
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