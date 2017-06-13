<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/12/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
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
<p>WSDL:</p>
<pre>&lt;complexType name="scriptFormat">
  &lt;complexContent>
    &lt;extension base="impl:folder-contained-asset">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="script" type="xsd:string"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/script_format.php">script_format.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "scriptFormat":{
    "script":"##",
    "parentFolderId":"1f22ab8e8b7ffe834c5fe91e555a2a38",
    "parentFolderPath":"_cascade/formats/test-velocity",
    "lastModifiedDate":"Sep 12, 2016 12:04:00 PM",
    "lastModifiedBy":"wing",
    "createdDate":"Sep 12, 2016 12:04:00 PM",
    "createdBy":"wing",
    "path":"_cascade/formats/test-velocity/test-config",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"test-config",
    "id":"1f24139b8b7ffe834c5fe91ea124b974" } },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class ScriptFormat extends Format
{
    const DEBUG = false;
    const TYPE  = c\T::VELOCITYFORMAT;
    
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