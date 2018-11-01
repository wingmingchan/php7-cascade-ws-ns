<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/9/2018 Fixed a bug in toStdClass.
  * 12/22/2017 Changed toStdClass so that it works with REST.
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/28/2016 Added setAccessRights, denyAccessToAllGroups, denyAccessToAllUsers.
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
<p>An <code>AccessRightsInformation</code> object represents an <code>accessRightsInformation</code> property returned from Cascade when the access rights information of an asset is read. See below for an example read dump.</p>
<h2>Structure of <code>accessRightsInformation</code></h2>
<pre>accessRightsInformation
  identifier
    id
    path
      path
      siteId
      siteName
    type
    recycled
  aclEntries
    aclEntry
      level
      type
      name
  allLevel
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "accessRightsInformation" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
        array( "getComplexTypeXMLByName" => "acl-entries" ),
        array( "getComplexTypeXMLByName" => "aclEntry" ),
        array( "getSimpleTypeXMLByName"  => "acl-entry-level" ),
        array( "getSimpleTypeXMLByName"  => "acl-entry-type" ),
        array( "getSimpleTypeXMLByName"  => "all-level" ),
    ) );
return $doc_string;
?>
</description>
<postscript>
<h2>Read Dump</h2>
<pre>object(stdClass)#21 (3) {
  ["identifier"]=&gt;
  object(stdClass)#26 (4) {
    ["id"]=&gt;
    string(32) "ffe39a278b7f08ee3e513744c5d70ead"
    ["path"]=&gt;
    object(stdClass)#27 (3) {
      ["path"]=&gt;
      string(4) "test"
      ["siteId"]=&gt;
      string(32) "980a7aa38b7f0856015997e4dd095185"
      ["siteName"]=&gt;
      string(13) "cascade-admin"
    }
    ["type"]=&gt;
    string(6) "folder"
    ["recycled"]=&gt;
    bool(false)
  }
  ["aclEntries"]=&gt;
  object(stdClass)#23 (1) {
    ["aclEntry"]=&gt;
    array(3) {
      [0]=&gt;
      object(stdClass)#24 (3) {
        ["level"]=&gt;
        string(5) "write"
        ["type"]=&gt;
        string(4) "user"
        ["name"]=&gt;
        string(5) "chanw"
      }
      [1]=&gt;
      object(stdClass)#25 (3) {
        ["level"]=&gt;
        string(5) "write"
        ["type"]=&gt;
        string(4) "user"
        ["name"]=&gt;
        string(10) "chanw-test"
      }
      [2]=&gt;
      object(stdClass)#22 (3) {
        ["level"]=&gt;
        string(4) "read"
        ["type"]=&gt;
        string(5) "group"
        ["name"]=&gt;
        string(13) "CWT-Designers"
      }
    }
  }
  ["allLevel"]=&gt;
  string(4) "none"
}
</pre>
<h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/access_rights_information.php">access_rights_information.php</a></li></ul></postscript>
</documentation>
*/
class AccessRightsInformation extends Property
{
    const DEBUG = false;
    
/**
<documentation><description><p>The constructor.</p></description>
<example>// get the object
$ari = $cascade->getAccessRights(
    a\Folder::TYPE, $folder_path, $site_name );</example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $ari=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
        
        if( isset( $ari ) )
        {
            if( isset( $ari->identifier ) )
                $this->identifier = new Identifier( $ari->identifier );
        
            if( isset( $ari->aclEntries ) )
            {
                if( $this->service->isSoap() && isset( $ari->aclEntries->aclEntry ) )
                    $this->processAclEntries( $ari->aclEntries->aclEntry );
                elseif( $this->service->isRest() )
                    $this->processAclEntries( $ari->aclEntries );
            }
        
            $this->all_level  = $ari->allLevel;
        }
    }
    
/**
<documentation><description><p>Grants the group read access to the asset, and returns the calling object.</p></description>
<example>$ari->addGroupReadAccess( $team );
// true means apply to children
$cascade->setAccessRights( $ari, true );
</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function addGroupReadAccess( a\Group $g ) : Property
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting read access to " . $g->getName() );  }
        $this->setAccess( $g, c\T::READ );
        return $this;
    }
    
/**
<documentation><description><p>Grants the group write access to the asset, and returns the calling object.</p></description>
<example>$ari->addGroupWriteAccess( $team );
// true means apply to children
$cascade->setAccessRights( $ari, true );
</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function addGroupWriteAccess( a\Group $g ) : Property
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting write access to " . $g->getName() );  }
        $this->setAccess( $g, c\T::WRITE );
        return $this;
    }
    
/**
<documentation><description><p>Grants the user read access to the asset, and returns the calling object.</p></description>
<example>$ari->addUserReadAccess( $thomas );
// true means apply to children
$cascade->setAccessRights( $ari, true );
</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function addUserReadAccess( a\User $u ) : Property
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting read access to " . $u->getName() );  }
        $this->setAccess( $u, c\T::READ );
        return $this;
    }
    
/**
<documentation><description><p>Grants the user write access to the asset, and returns the calling object.</p></description>
<example>$ari->addUserWriteAccess( $wing );
// true means apply to children
$cascade->setAccessRights( $ari, true );
</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function addUserWriteAccess( a\User $u ) : Property
    {
        if( self::DEBUG ){ u\DebugUtility::out( "Granting write access to " . $u->getName() );  }
        $this->setAccess( $u, c\T::WRITE );
        return $this;
    }
    
/**
<documentation><description><p>Clears all permissions and returns the calling object.</p></description>
<example>$ari->clearPermissions();
// false means do not apply to children
$cascade->setAccessRights( $ari, false );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function clearPermissions() : Property
    {
        $this->acl_entries = array();
        $this->all_level   = c\T::NONE;
        return $this;
    }
    
/**
<documentation><description><p>Removes all group access, and returns the calling object.</p></description>
<example>$ari->denyAccessToAllGroups();
// true means apply to children
$cascade->setAccessRights( $ari, true );
</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function denyAccessToAllGroups() : Property
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
    
/**
<documentation><description><p>Removes all user access, and returns the calling object.</p></description>
<example>$ari->denyAccessToAllUsers();
// true means apply to children
$cascade->setAccessRights( $ari, true );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function denyAccessToAllUsers() : Property
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

/**
<documentation><description><p>Removes the group from the <code>aclEntries</code>, and returns the calling object.</p></description>
<example>$ari->denyGroupAccess( $cru );
// true means apply to children
$cascade->setAccessRights( $ari, true );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function denyGroupAccess( a\Group $g ) : Property
    {
        $this->denyAccess( $g, $g->getType() );
        return $this;
    }
    
/**
<documentation><description><p>Removes the user from the <code>aclEntries</code>, and returns the calling object.</p></description>
<example>$ari->denyUserAccess( $thomas );
// true means apply to children
$cascade->setAccessRights( $ari, true );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function denyUserAccess( a\User $u ) : Property
    {
        $this->denyAccess( $u, $u->getType() );
        return $this;
    }
    
/**
<documentation><description><p>Displays and returns calling the object.</p></description>
<example>$ari->display();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function display() : Property
    {
        echo S_PRE;
        var_dump( $this->toStdClass() );
        echo E_PRE;
        return $this;
    }
    
/**
<documentation><description><p>Returns an array of <code>AclEntry</code> objects.</p></description>
<example>u\DebugUtility::dump( $ari->getAclEntries() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getAclEntries() : array
    {
        return $this->acl_entries;
    }
    
/**
<documentation><description><p>Returns <code>allLevel</code>.</p></description>
<example>echo $ari->getAllLevel(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getAllLevel() : string
    {
        return $this->all_level;
    }
    
/**
<documentation><description><p>Returns the <code>level</code> string of the named group or <code>NULL</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $ari->getGroupLevel( $cru ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupLevel( a\Group $g )
    {
        $entry = $this->getEntry( $g );
        
        if( isset( $entry ) )
        {
            return $entry->getLevel();
        }
        return NULL;
    }

/**
<documentation><description><p>Returns <code>identifier</code> (an <code>Identifier</code> object) of the associated asset.</p></description>
<example>u\DebugUtility::dump( $ari->getIdentifier() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifier() : Property
    {
        return $this->identifier;
    }
    
/**
<documentation><description><p>Returns the <code>level</code> string of the named user or <code>NULL</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $ari->getUserLevel( $thomas ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getUserLevel( a\User $u )
    {
        $entry = $this->getEntry( $u );
        
        if( isset( $entry ) )
        {
            return $entry->getLevel();
        }
        return NULL;
    }

/**
<documentation><description><p>An alias of <code>addGroupReadAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function grantGroupReadAccess( a\Group $g ) : Property
    {
        return $this->addGroupReadAccess( $g );
    }
    
/**
<documentation><description><p>An alias of <code>addGroupWriteAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function grantGroupWriteAccess( a\Group $g ) : Property
    {
        return $this->addGroupWriteAccess( $g );
    }
    
/**
<documentation><description><p>An alias of <code>addUserReadAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function grantUserReadAccess( a\User $u ) : Property
    {
        return $this->addUserReadAccess( $u );
    }
    
/**
<documentation><description><p>An alias of <code>addUserWriteAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function grantUserWriteAccess( a\User $u ) : Property
    {
        return $this->addUserWriteAccess( $u );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named group has access to the asset.</p></description>
<example>echo u\StringUtility::boolToString(
    $ari->hasGroup( $cru ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasGroup( a\Group $g ) : bool
    {
        return $this->getEntry( $g ) != NULL;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the named user has access to the asset.</p></description>
<example>echo u\StringUtility::boolToString(
    $ari->hasUser( $thomas ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasUser( a\User $u ) : bool
    {
        return $this->getEntry( $u ) != NULL;
    }

/**
<documentation><description><p>Sets the access rights with <code>$level</code> for a group or user asset, and returns the calling object.</p></description>
<example>$ari->setAccessRights( $cru, c\T::READ );
// true means apply to children
$cascade->setAccessRights( $ari, true );</example>
<return-type>Property</return-type>
<exception>WrongAssetTypeException</exception>
</documentation>
*/
    public function setAccessRights( a\Asset $a, string $level ) : Property
    {
        $this->setAccess( $a, $level );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>allLevel</code> and returns the calling object.</p></description>
<example>$ari->setAllLevel( c\T::NONE );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setAllLevel( $level ) : Property
    {
        if( !c\LevelValues::isLevel( $level ) )
        {
            throw new e\UnacceptableValueException( "The level $level is unacceptable." );
        }
    
        $this->all_level = $level;
        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>addGroupReadAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setGroupReadAccess( a\Group $g ) : Property
    {
        return $this->addGroupReadAccess( $g );
    }
    
/**
<documentation><description><p>An alias of <code>addGroupWriteAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setGroupWriteAccess( a\Group $g ) : Property
    {
        return $this->addGroupWriteAccess( $g );
    }
    
/**
<documentation><description><p>An alias of <code>addUserReadAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setUserReadAccess( a\User $u ) : Property
    {
        return $this->addUserReadAccess( $u );
    }
    
/**
<documentation><description><p>An alias of <code>addUserWriteAccess</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setUserWriteAccess( a\User $u ) : Property
    {
        return $this->addUserWriteAccess( $u );
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj = new \stdClass();
        
        $obj->identifier = $this->identifier->toStdClass();
        
        $entry_array = array();
        
        if( isset( $this->acl_entries ) )
        {
            foreach( $this->acl_entries as $entry )
            {
                $entry_array[] = $entry->toStdClass();
            }
        }
        
        $obj->aclEntries           = new \stdClass();
        
        if( $this->service->isSoap() )
            $obj->aclEntries->aclEntry = $entry_array;
        else
            $obj->aclEntries = $entry_array;
            
        $obj->allLevel = $this->all_level;
        
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
    private $service;
}
?>