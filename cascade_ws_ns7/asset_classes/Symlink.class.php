<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/6/2016 Added expiration folder-related code.
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
class Symlink extends Linkable
{
    const DEBUG = false;
    const TYPE  = c\T::SYMLINK;
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderId()
    {
        return $this->getProperty()->expirationFolderId;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExpirationFolderPath()
    {
        return $this->getProperty()->expirationFolderPath;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLinkURL()
    {
        return $this->getProperty()->linkURL;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setExpirationFolder( Folder $f ) : Asset
    {
        $this->getProperty()->expirationFolderId   = $f->getId();
        $this->getProperty()->expirationFolderPath = $f->getPath();
        return $this;
    }
        
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setLinkURL( $url )
    {
        $this->getProperty()->linkURL = $url;
        return $this;
    }
}
?>