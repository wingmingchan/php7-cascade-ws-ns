<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
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
<p>A <code>ScriptFormat</code> represents a script format asset. The class <code>ScriptFormat</code> is a sub-class of <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/format.php"><code>Format</code></a>.</p>
<h2>Structure of <code>scriptFormat</code></h2>
<pre>scriptFormat
  id
  name
  parentFolderId
  parentFolderPath
  path
  lastModifiedDate
  lastModifiedBy
  createdDate
  createdBy
  siteId
  siteName
  script
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/script_format.php">script_format.php</a></li></ul></postscript>
</documentation>
*/
class ScriptFormat extends Format
{
    const DEBUG = false;
    const TYPE  = c\T::VELOCITYFORMAT;
    
/**
<documentation><description><p>Displays the script and returns the calling object.</p></description>
<example>$f->displayScript();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayScript() : Asset
    {
        $script_string = htmlentities( $this->getProperty()->script ); // &
        $script_string = u\XMLUtility::replaceBrackets( $script_string );
        
        echo S_H2 . "Script" . E_H2 .
             S_PRE . $script_string . E_PRE . HR;
        
        return $this;
    }

/**
<documentation><description><p>Returns <code>script</code>.</p></description>
<example>echo $f->getScript(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getScript() : string
    {
        return $this->getProperty()->script;
    }
    
/**
<documentation><description><p>Sets <code>script</code> and returns the calling object.</p></description>
<example>$f->setScript( $script )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setScript( $script ) : Asset
    {
        $this->getProperty()->script = $script;
        
        return $this;
    }
}
?>