<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
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
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function addUser( User $u ) : Aset
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
                $users = u\StringUtility::getExplodedStringArray( self::DELIMITER, $this->getUsers() );
                
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function addUserName( string $u_name ) : Aset
    {
        if( !$this->hasUserName( $u_name ) )
        {
            $users = u\StringUtility::getExplodedStringArray( self::DELIMITER, $this->getUsers() );
            $users[] = $u_name;
            $this->getProperty()->users = implode( self::DELIMITER, $users );
        }
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCssClasses() : string
    {
        return $this->getProperty()->cssClasses;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupAssetFactoryContainerId() : string
    {
        return $this->getProperty()->groupAssetFactoryContainerId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupAssetFactoryContainerPath() : string
    {
        return $this->getProperty()->groupAssetFactoryContainerPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupBaseFolderId() : string
    {
        return $this->getProperty()->groupBaseFolderId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupBaseFolderPath() : string
    {
        return $this->getProperty()->groupBaseFolderPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupBaseFolderRecycled() : string
    {
        return $this->getProperty()->groupBaseFolderRecycled;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupName() : string
    {
        return $this->getProperty()->groupName;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupStartingPageId() : string
    {
        return $this->getProperty()->groupStartingPageId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupStartingPagePath() : string
    {
        return $this->getProperty()->groupStartingPagePath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getGroupStartingPageRecycled() : string
    {
        return $this->getProperty()->groupStartingPageRecycled;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getId() : string
    {
        return $this->getProperty()->groupName;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getName() : string
    {
        return $this->getProperty()->groupName;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRole() : string
    {
        return $this->getProperty()->role;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getUsers() : string
    {
        return $this->getProperty()->users;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowFontAssignment() : string
    {
        return $this->getProperty()->wysiwygAllowFontAssignment;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowFontFormatting() : string
    {
        return $this->getProperty()->wysiwygAllowFontFormatting;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowImageInsertion() : string
    {
        return $this->getProperty()->wysiwygAllowImageInsertion;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowTableInsertion() : string
    {
        return $this->getProperty()->wysiwygAllowTableInsertion;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowTextFormatting() : string
    {
        return $this->getProperty()->wysiwygAllowTextFormatting;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWysiwygAllowViewSource() : string
    {
        return $this->getProperty()->wysiwygAllowViewSource;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
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
            $users = u\StringUtility::getExplodedStringArray( self::DELIMITER, $this->getUsers() );
            return ( in_array( $u->getName(), $users ) );
        }
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function hasUserName( $u_name ) : bool
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
                $users = u\StringUtility::getExplodedStringArray( self::DELIMITER, $this->getUsers() );
                return ( in_array( $u_name, $users ) );
            }
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
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
                $user_array = u\StringUtility::getExplodedStringArray( self::DELIMITER, $this->getUsers() );
                
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function removeUserName( $u_name ) : Asset
    {
        if( $this->hasUserName( $u_name ) )
        {
            $user_array = u\StringUtility::getExplodedStringArray( self::DELIMITER, $this->getUsers() );
                
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setWysiwygAllowFontAssignment( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );   
        $this->getProperty()->wysiwygAllowFontAssignment = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setWysiwygAllowFontFormatting( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );  
        $this->getProperty()->wysiwygAllowFontFormatting = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setWysiwygAllowImageInsertion( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );
        $this->getProperty()->wysiwygAllowImageInsertion = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setWysiwygAllowTableInsertion( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );   
        $this->getProperty()->wysiwygAllowTableInsertion = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setWysiwygAllowTextFormatting( bool $bool ) : Asset
    {
        $this->checkBoolean( $bool );  
        $this->getProperty()->wysiwygAllowTextFormatting = $bool;
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
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
