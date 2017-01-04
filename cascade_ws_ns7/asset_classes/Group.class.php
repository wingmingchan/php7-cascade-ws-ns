<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<description><h2>Introduction</h2>
<p>A <code>Group</code> object represents a group asset.</p>
<h2>Structure of <code>group</code></h2>
<pre>group
  groupName
  groupStartingPageId
  groupStartingPagePath
  groupStartingPageRecycled
  groupBaseFolderId
  groupBaseFolderPath
  groupBaseFolderRecycled
  groupAssetFactoryContainerId
  groupAssetFactoryContainerPath
  cssClasses
  wysiwygAllowFontAssignment
  wysiwygAllowFontFormatting
  wysiwygAllowTextFormatting
  wysiwygAllowViewSource
  wysiwygAllowImageInsertion
  wysiwygAllowTableInsertion
  users
  role
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/group.php">group.php</a></li></ul></postscript>
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
<documentation><description><p>Returns <code>cssClasses</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $g->getCssClasses() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getCssClasses()
    {
        return $this->getProperty()->cssClasses;
    }
    
/**
<documentation><description><p>Returns <code>groupAssetFactoryContainerId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $g->getGroupAssetFactoryContainerId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupAssetFactoryContainerId()
    {
        return $this->getProperty()->groupAssetFactoryContainerId;
    }
    
/**
<documentation><description><p>Returns <code>groupAssetFactoryContainerPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $g->getGroupAssetFactoryContainerPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupAssetFactoryContainerPath()
    {
        return $this->getProperty()->groupAssetFactoryContainerPath;
    }
    
/**
<documentation><description><p>Returns <code>groupBaseFolderId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $g->getGroupBaseFolderId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupBaseFolderId()
    {
        return $this->getProperty()->groupBaseFolderId;
    }
    
/**
<documentation><description><p>Returns <code>groupBaseFolderPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $g->getGroupBaseFolderPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupBaseFolderPath()
    {
        return $this->getProperty()->groupBaseFolderPath;
    }
    
/**
<documentation><description><p>Returns <code>groupBaseFolderRecycled</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupBaseFolderRecycled() : bool
    {
        return $this->getProperty()->groupBaseFolderRecycled;
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
<documentation><description><p>Returns <code>groupStartingPageId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $g->getGroupStartingPageId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupStartingPageId()
    {
        return $this->getProperty()->groupStartingPageId;
    }
    
/**
<documentation><description><p>Returns <code>groupStartingPagePath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString(
    $g->getGroupStartingPagePath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupStartingPagePath()
    {
        return $this->getProperty()->groupStartingPagePath;
    }
    
/**
<documentation><description><p>Returns <code>groupStartingPageRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $g->getGroupStartingPageRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getGroupStartingPageRecycled() : bool
    {
        return $this->getProperty()->groupStartingPageRecycled;
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
<documentation><description><p>Returns <code>wysiwygAllowFontAssignment</code>.</p></description>
<example>echo u\StringUtility::boolToString( $g->getWysiwygAllowFontAssignment() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowFontAssignment() : bool
    {
        return $this->getProperty()->wysiwygAllowFontAssignment;
    }
    
/**
<documentation><description><p>Returns <code>wysiwygAllowFontFormatting</code>.</p></description>
<example>echo u\StringUtility::boolToString( $g->getWysiwygAllowFontFormatting() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowFontFormatting() : bool
    {
        return $this->getProperty()->wysiwygAllowFontFormatting;
    }
    
/**
<documentation><description><p>Returns <code>wysiwygAllowImageInsertion</code>.</p></description>
<example>echo u\StringUtility::boolToString( $g->getWysiwygAllowImageInsertion() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowImageInsertion() : bool
    {
        return $this->getProperty()->wysiwygAllowImageInsertion;
    }
    
/**
<documentation><description><p>Returns <code>wysiwygAllowTableInsertion</code>.</p></description>
<example>echo u\StringUtility::boolToString( $g->getWysiwygAllowTableInsertion() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowTableInsertion() : bool
    {
        return $this->getProperty()->wysiwygAllowTableInsertion;
    }
    
/**
<documentation><description><p>Returns <code>wysiwygAllowTextFormatting</code>.</p></description>
<example>echo u\StringUtility::boolToString( $g->getWysiwygAllowTextFormatting() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowTextFormatting() : bool
    {
        return $this->getProperty()->wysiwygAllowTextFormatting;
    }
    
/**
<documentation><description><p>Returns <code>wysiwygAllowViewSource</code>.</p></description>
<example>echo u\StringUtility::boolToString( $g->getWysiwygAllowViewSource() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowViewSource() : bool
    {
        return $this->getProperty()->wysiwygAllowViewSource;
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
    
/* 
setGroupBaseFolder, setGroupStartingPage, setGroupAssetFactoryContainer
not implemented because they only work for Global site
*/
    
/**
<documentation><description><p>Sets <code>wysiwygAllowFontAssignment</code> and returns the calling object.</p></description>
<example>$g->
    setWysiwygAllowFontAssignment( false )->            
    setWysiwygAllowFontFormatting( false )->            
    setWysiwygAllowImageInsertion( true )->            
    setWysiwygAllowTableInsertion( true )->            
    setWysiwygAllowTextFormatting( false )->            
    setWysiwygAllowViewSource( true )->            
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setWysiwygAllowFontAssignment( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );   
        $this->getProperty()->wysiwygAllowFontAssignment = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>wysiwygAllowFontFormatting</code> and returns the calling object.</p></description>
<example>$g->
    setWysiwygAllowFontAssignment( false )->            
    setWysiwygAllowFontFormatting( false )->            
    setWysiwygAllowImageInsertion( true )->            
    setWysiwygAllowTableInsertion( true )->            
    setWysiwygAllowTextFormatting( false )->            
    setWysiwygAllowViewSource( true )->            
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setWysiwygAllowFontFormatting( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );  
        $this->getProperty()->wysiwygAllowFontFormatting = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>wysiwygAllowImageInsertion</code> and returns the calling object.</p></description>
<example>$g->
    setWysiwygAllowFontAssignment( false )->            
    setWysiwygAllowFontFormatting( false )->            
    setWysiwygAllowImageInsertion( true )->            
    setWysiwygAllowTableInsertion( true )->            
    setWysiwygAllowTextFormatting( false )->            
    setWysiwygAllowViewSource( true )->            
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setWysiwygAllowImageInsertion( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );
        $this->getProperty()->wysiwygAllowImageInsertion = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>wysiwygAllowTableInsertion</code> and returns the calling object.</p></description>
<example>$g->
    setWysiwygAllowFontAssignment( false )->            
    setWysiwygAllowFontFormatting( false )->            
    setWysiwygAllowImageInsertion( true )->            
    setWysiwygAllowTableInsertion( true )->            
    setWysiwygAllowTextFormatting( false )->            
    setWysiwygAllowViewSource( true )->            
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setWysiwygAllowTableInsertion( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );   
        $this->getProperty()->wysiwygAllowTableInsertion = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>setWysiwygAllowTextFormatting</code> and returns the calling object.</p></description>
<example>$g->
    setWysiwygAllowFontAssignment( false )->            
    setWysiwygAllowFontFormatting( false )->            
    setWysiwygAllowImageInsertion( true )->            
    setWysiwygAllowTableInsertion( true )->            
    setWysiwygAllowTextFormatting( false )->            
    setWysiwygAllowViewSource( true )->            
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setWysiwygAllowTextFormatting( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );  
        $this->getProperty()->wysiwygAllowTextFormatting = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>wysiwygAllowViewSource</code> and returns the calling object.</p></description>
<example>$g->
    setWysiwygAllowFontAssignment( false )->            
    setWysiwygAllowFontFormatting( false )->            
    setWysiwygAllowImageInsertion( true )->            
    setWysiwygAllowTableInsertion( true )->            
    setWysiwygAllowTextFormatting( false )->            
    setWysiwygAllowViewSource( true )->            
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setWysiwygAllowViewSource( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );   
        $this->getProperty()->wysiwygAllowViewSource = $bool;
        return $this;
    }
    
    private function checkBoolean( bool $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
    }
}
?>
