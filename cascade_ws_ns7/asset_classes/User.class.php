<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 1/3/2018 Added code to test for NULL.
  * 9/14/2017 Added getLdapDN.
  * 6/30/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/12/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
  * 1/26/2016 Added leaveGroup and isInGroup.
  * 5/28/2015 Added namespaces.
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
<p>A <code>User</code> object represents a user asset. Note that if a user's type is <code>ldap</code>, then the user cannot be edited. (See <a href=\"https://hannonhill.jira.com/browse/CSI-722\">Allow editing users authenticated through LDAP</a>.)</p>
<h2>Structure of <code>user</code></h2>
<pre>user
  username
  fullName
  email
  authType
  password
  enabled
  groups
  defaultGroup
  role
  defaultSiteId
  defaultSiteName
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "user-group-identifier" ),
        array( "getComplexTypeXMLByName" => "user" ),
        array( "getSimpleTypeXMLByName"  => "user-auth-types" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/user.php">user.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/user/wing

{
  "asset":{
    "user":{
      "username":"wing",
      "email":"wing",
      "authType":"normal",
      "password":"fk3*h\u0026_sd%^#^!ew",
      "enabled":true,
      "groups":"Administrators",
      "roles":"Administrator"
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
class User extends Asset
{
    const DEBUG = false;
    const TYPE  = c\T::USER;
    
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

/**
<documentation><description><p>Disables a user and returns the calling object.</p></description>
<example>$u->disable()->edit();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function disable() : Asset
    {
        $this->getProperty()->enabled = false;
        return $this;
    }

/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
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
    
/**
<documentation><description><p>Enables a user and returns the calling object.</p></description>
<example>$u->enable()->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function enable() : Asset
    {
        $this->getProperty()->enabled = true;
        return $this;
    }

/**
<documentation><description><p>Returns <code>authType</code>.</p></description>
<example>echo $u->getAuthType(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getAuthType() : string
    {
        return $this->getProperty()->authType;
    }
    
/**
<documentation><description><p>Returns <code>defaultGroup</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $u->getDefaultGroup() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultGroup()
    {
        if( isset( $this->getProperty()->defaultGroup ) )
            return $this->getProperty()->defaultGroup;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>defaultSiteId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $u->getDefaultSiteId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultSiteId()
    {
        if( isset( $this->getProperty()->defaultSiteId ) )
            return $this->getProperty()->defaultSiteId;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>defaultSiteName</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $u->getDefaultSiteName() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultSiteName()
    {
        if( isset( $this->getProperty()->defaultSiteName ) )
            return $this->getProperty()->defaultSiteName;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>enabled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $u->getEnabled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getEnabled() : bool
    {
        return $this->getProperty()->enabled;
    }
    
/**
<documentation><description><p>Returns <code>email</code>.</p></description>
<example>echo $u->getEmail(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getEmail() : string
    {
        return $this->getProperty()->email;
    }
    
/**
<documentation><description><p>Returns <code>fullName</code>.</p></description>
<example>echo $u->getUsername(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFullName()
    {
        if( isset( $this->getProperty()->fullName ) )
            return $this->getProperty()->fullName;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>groups</code>.</p></description>
<example>echo $u->getGroups(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getGroups() : string
    {
        return $this->getProperty()->groups;
    }
    
/**
<documentation><description><p>Overriding the parent method, returns <code>username</code>.</p></description>
<example>echo $u->getId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getId() : string
    {
        return $this->getProperty()->username;
    }
    
/**
<documentation><description><p>Returns <code>ldapDN</code>.</p></description>
<example>echo $u->getLdapDN(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLdapDN() : string
    {
        return $this->getProperty()->ldapDN;
    }
    
/**
<documentation><description><p>Overriding the parent method, returns <code>username</code>.</p></description>
<example>echo $u->getName(), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getName() : string
    {
        return $this->getProperty()->username;
    }
    
/**
<documentation><description><p>Returns <code>role</code>.</p></description>
<example>echo $u->getRole(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRole() : string
    {
        return $this->getProperty()->role;
    }
    
/**
<documentation><description><p>Returns <code>password</code>. Note that the password is encrypted and the returned value is useless.</p></description>
<example>echo $u->getPassword(), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPassword() : string
    {
        if( isset( $this->getProperty()->password ) )
            return $this->getProperty()->password;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>username</code>.</p></description>
<example>echo $u->getUsername(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getUserName() : string
    {
        return $this->getProperty()->username;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the user is in the named group.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function isInGroup( Group $group ) : bool
    {
        $users = $group->getUsers();
        
        if( strpos( $users, Group::DELIMITER . $this->getProperty()->username .
            Group::DELIMITER ) !== false )
            return true;
            
        return false;
    }
    
/**
<documentation><description><p>Adds the user to the group and returns the calling object.
Note that because the <code>Group</code> object, not the <code>User</code> object, is
modified, the <code>Group::edit</code> method is called inside this method. Do not mixed
this method call with other <code>User::set</code> methods.</p></description>
<example>$u->joinGroup( $cascade->getAsset( a\Group::TYPE, "cru" ) );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function joinGroup( Group $g ) : Asset
    {
        $g->addUser( Asset::getAsset( $this->getService(),
            User::TYPE,
            $this->getProperty()->username ) )->edit();
        return $this;
    }
    
/**
<documentation><description><p>Removes the user from the group and returns the calling
object. Note that because the <code>Group</code> object, not the <code>User</code> object,
is modified, the <code>Group::edit</code> method is called inside this method. Do not
mixed this method call with other <code>User::set</code> methods.</p></description>
<example>$u->leaveGroup( $cascade->getAsset( a\Group::TYPE, "cru" ) );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function leaveGroup( Group $g ) : Asset
    {
        $g->removeUser( Asset::getAsset( $this->getService(),
            User::TYPE,
            $this->getProperty()->username ) )->edit();
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>defaultGroup</code> and returns the calling object.</p></description>
<example>$u->setDefaultGroup( $cascade->getAsset( a\Group::TYPE, "cru" ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDefaultGroup( Group $group=NULL ) : Asset
    {
        if( isset( $group ) )
        {
            $this->getProperty()->defaultGroup   = $group->getName();
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>defaultSiteId</code> and <code>defaultSiteName</code> and returns the calling object.</p></description>
<example>$u->setDefaultSite( 
    $cascade->getAsset( a\Site::TYPE, 'ede8ade68b7f08560139425c36d7307f' ) )->
    edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDefaultSite( Site $site=NULL ) : Asset
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
    
/**
<documentation><description><p>Sets <code>enabled</code> and returns the calling object.</p></description>
<example>$u->setEnabled( true )->edit();</example>
<return-type></return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( "The value $bool must be a boolean." );

        $this->getProperty()->enabled = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>email</code> and returns the calling object.</p></description>
<example>$u->setEmail( 'chanw' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setEmail( string $email ) : Asset
    {
        if( trim( $email ) == '' )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_EMAIL . E_SPAN );

        $this->getProperty()->email = $email;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>fullName</code> and returns the calling object.</p></description>
<example>$u->setFullName( 'Wing Ming Chan' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setFullName( string $name ) : Asset
    {
        if( trim( $name ) == '' )
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_FULL_NAME . E_SPAN );

        $this->getProperty()->fullName = $name;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>password</code> and returns the calling object.</p></description>
<example>$u->setPassword( '************' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setPassword( string $pw ) : Asset
    {
        if( trim( $pw ) == '' )
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_PASSWORD . E_SPAN );

        $this->getProperty()->password = $pw;
        return $this;
    }
}
?>