<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/15/2017 Removed all methods related to configurations per Cascade 8.4.
  * 6/12/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
  * 1/27/2016 Modified addUser, removeUser and hasUser, using StringUtility::getExplodedStringArray.
  *           Added addUserName, hasUserName, removeUserName.
  *           Added getGroupStartingPageRecycled.
  * 1/26/2016 Added hasUser.
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
<p>A <code>Group</code> object represents a group asset.</p>
<h2>Structure of <code>group</code></h2>
<pre>group
  groupName
  users
  role
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "user-group-identifier" ),
        array( "getComplexTypeXMLByName" => "group" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/group.php">group.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "group":{
    "groupName":"22q",
    "users":"marrinae",
    "roles":"Default" } },
  "success":true
}</pre>
</postscript>
</documentation>
*/
class Group extends Asset
{
    const DEBUG     = false;
    const TYPE      = c\T::GROUP;
    const DELIMITER = ";";
    
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
<documentation><description><p>Adds a user to the group and returns the calling object.</p></description>
<example>$g->addUser( $cascade->getAsset( a\User::TYPE, 'chanw' ) )->edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addUser( User $u ) : Asset
    {
        if( isset( $u ) )
        {
            $u_name = $u->getName();
            
            if( $this->getProperty()->users == "" || $this->getProperty()->users == NULL )
            {
                $this->getProperty()->users = $u_name;
            }
            else
            {
                $users = u\StringUtility::getExplodedStringArray(
                    self::DELIMITER, $this->getUsers() );
                
                if( !in_array( $u_name, $users ) )
                {
                    $users[] = $u_name;
                }
                
                $this->getProperty()->users = implode( self::DELIMITER, $users );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Adds a user name to the group and returns the calling object.</p></description>
<example>$g->addUserName( 'chanw' )->edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addUserName( string $u_name ) : Asset
    {
        if( !$this->hasUserName( $u_name ) )
        {
            $users = u\StringUtility::getExplodedStringArray(
                self::DELIMITER, $this->getUsers() );
            $users[] = $u_name;
            $this->getProperty()->users = implode( self::DELIMITER, $users );
        }
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
<documentation><description><p>Returns <code>groupName</code>.</p></description>
<example>echo $g->getGroupName(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupName() : string
    {
        return $this->getProperty()->groupName;
    }
    
/**
<documentation><description><p>Overrides the parent method and returns <code>groupName</code>.</p></description>
<example>echo $g->getId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getId() : string
    {
        return $this->getProperty()->groupName;
    }
    
/**
<documentation><description><p>Overrides the parent method and returns <code>groupName</code>.</p></description>
<example>echo $g->getName(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getName() : string
    {
        return $this->getProperty()->groupName;
    }
    
/**
<documentation><description><p>Returns <code>role</code>. This is the global role assigned to the group.</p></description>
<example>echo $g->getRole(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getRole() : string
    {
        return $this->getProperty()->role;
    }
    
/**
<documentation><description><p>Returns <code>users</code>.</p></description>
<example>echo $g->getUsers(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getUsers() : string
    {
        return $this->getProperty()->users;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the group includes the user.</p></description>
<example>echo u\StringUtility::boolToString(
    $g->hasUser( $cascade->getAsset( a\User::TYPE, 'chanw' ) ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasUser( User $u ) : bool
    {
        // no users yet
        if( $this->getProperty()->users == "" || $this->getProperty()->users == NULL )
        {
            return false;
        }
        else
        {
            $users = u\StringUtility::getExplodedStringArray(
                self::DELIMITER, $this->getUsers() );
            return ( in_array( $u->getName(), $users ) );
        }
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether <code>users</code> includes the user name.</p></description>
<example>echo u\StringUtility::boolToString( $g->hasUserName( 'chanw' ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasUserName( string $u_name ) : bool
    {
        if( trim( $u_name ) == "" )
            throw new e\EmptyValueException();
        
        // no users yet
        if( $this->getProperty()->users == "" || $this->getProperty()->users == NULL )
        {
            return false;
        }
        else
        {
            $users = u\StringUtility::getExplodedStringArray(
                self::DELIMITER, $this->getUsers() );
            return ( in_array( $u_name, $users ) );
        }
    }
    
/**
<documentation><description><p>Removes the user from the group, returns the calling object.</p></description>
<example>$g->removeUser( $cascade->getAsset( a\User::TYPE, 'chanw' ) )->edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeUser( User $u ) : Asset
    {
        if( isset( $u ) )
        {
            $u_name = $u->getName();
            
            // nothing to remove
            if( $this->getProperty()->users == "" || $this->getProperty()->users == NULL )
            {
                return $this;
            }
            else
            {
                $user_array = u\StringUtility::getExplodedStringArray(
                    self::DELIMITER, $this->getUsers() );
                
                $temp = array();
                
                foreach( $user_array as $user )
                {
                    if( $user != $u_name )
                    {
                        $temp[] = $user;
                    }
                }
                
                $this->getProperty()->users = implode( self::DELIMITER, $temp );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Removes the user name from the group, and returns the calling object.</p></description>
<example>$g->removeUserName( 'chanw' )->edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeUserName( string $u_name ) : Asset
    {
        if( $this->hasUserName( $u_name ) )
        {
            $user_array = u\StringUtility::getExplodedStringArray(
                self::DELIMITER, $this->getUsers() );
                
            $temp = array();
                
            foreach( $user_array as $user )
            {
                if( $user != $u_name )
                {
                    $temp[] = $user;
                }
            }
                
            $this->getProperty()->users = implode( self::DELIMITER, $temp );
        }
        return $this;
    }
}
?>