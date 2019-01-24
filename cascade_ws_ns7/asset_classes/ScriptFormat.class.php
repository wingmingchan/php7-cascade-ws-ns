<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/29/2017 Replaced static WSDL code with call to getXMLFragments.
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
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>ScriptFormat</code> represents a script format asset. The class <code>ScriptFormat</code> is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/format.php\"><code>Format</code></a>.</p>
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
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "scriptFormat" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/script_format.php">script_format.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/format_SCRIPT/c12ed4e58b7ffe83129ed6d8d7ef4a97

{
  "asset":{
    "scriptFormat":{
      "script":"## this is for external-nav\r\n#if( !$siteConfigMap )\r\n#set( $siteConfigMap \u003d {} )\r\n#end\r\n#set( $siteConfigMap[ \u0027siteLocalCSS\u0027 ]       \u003d \"site://cascade-admin/_extra/local.css\" )\r\n#set( $siteConfigMap[ \u0027siteTitleString\u0027 ]    \u003d \"Formats\" )\r\n#set( $siteConfigMap[ \u0027siteNavHomeString\u0027 ]  \u003d \"Home\" )\r\n#set( $siteConfigMap[ \u0027siteContactPerson\u0027 ]  \u003d \"Wing Ming Chan\" )\r\n#set( $siteConfigMap[ \u0027siteContactEmail\u0027 ]   \u003d \"chanw\" )\r\n\r\n## override the design setup\r\n#set( $pagesWithoutBreadcrumbs \u003d [] )\r\n#set( $siteConfigMap[ \u0027pagesWithoutBreadcrumbs\u0027 ]     \u003d $pagesWithoutBreadcrumbs )\r\n#set( $siteConfigMap[ \u0027breadcrumbsHomeString\u0027 ]       \u003d \"Formats Home\" )\r\n#set( $siteConfigMap[ \u0027breadcrumbsSeparatorString\u0027 ]  \u003d \"➤\" )\r\n##set( $siteConfigMap[ \u0027displayHtmlCode\u0027 ]             \u003d true )\r\n#set( $siteConfigMap[ \u0027pagesWithoutH1\u0027 ]          \u003d [ \"_extra/reusable-component\", \"_extra/blank-page\" ] )\r\n\r\n",
      "parentFolderId":"c12dce028b7ffe83129ed6d8fdc88b47",
      "parentFolderPath":"_cascade",
      "lastModifiedDate":"Jan 23, 2018 10:28:53 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Nov 15, 2017 2:36:32 PM",
      "createdBy":"wing",
      "path":"_cascade/setup",
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "name":"setup",
      "id":"c12ed4e58b7ffe83129ed6d8d7ef4a97"
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