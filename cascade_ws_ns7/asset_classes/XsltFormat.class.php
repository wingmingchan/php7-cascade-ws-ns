<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 8/12/2014 Used SimpleXMLElement in setXML.
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
<p>A <code>XsltFormat</code> represents a XSLT format asset. The class <code>ScriptFormat</code> is a sub-class of <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/format.php"><code>Format</code></a>.</p>
<h2>Structure of <code>xsltFormat</code></h2>
<pre>xsltFormat
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
  xml
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/xslt_format.php">xslt_format.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>{ "asset":{
  "xsltFormat":{
    "xml":"...",
    "parentFolderId":"1f22a5fc8b7ffe834c5fe91ec2acf245",
    "parentFolderPath":"_cascade/formats/test-xslt",
    "lastModifiedDate":"Dec 19, 2016 11:11:12 AM",
    "lastModifiedBy":"wing",
    "createdDate":"Sep 12, 2016 12:04:04 PM",
    "createdBy":"wing",
    "path":"_cascade/formats/test-xslt/rwd-slideshow-test",
    "siteId":"1f2172088b7ffe834c5fe91e9596d028",
    "siteName":"cascade-admin-webapp",
    "name":"rwd-slideshow-test",
    "id":"1f2422858b7ffe834c5fe91ecd110f7a" } },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class XsltFormat extends Format
{
    const DEBUG = false;
    const TYPE  = c\T::XSLTFORMAT;
    
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
<documentation><description><p>Displays the <code>xml</code> and returns the calling object.</p></description>
<example>$f->displayXML();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayXml() : Asset
    {
        $xml_string = htmlentities( $this->getProperty()->xml ); // &
        $xml_string = u\XMLUtility::replaceBrackets( $xml_string );
        
        echo S_H2 . "XML" . E_H2 .
             S_PRE . $xml_string . E_PRE . HR;
        
        return $this;
    }

/**
<documentation><description><p>Displays the <code>xml</code> and returns the calling object.</p></description>
<example>$xml = $f->getXml();</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getXml() : string
    {
        return $this->getProperty()->xml;
    }
    
/**
<documentation><description><p>Sets the <code>xml</code> and returns the calling object. If <code>$enforce_xml</code> is set to <code>true</code>, then the value of <code>$xml</code> will be checked for well-formedness.</p></description>
<example>$f->setXML( $xml )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setXml( string $xml, bool $enforce_xml=false ) : Asset
    {
        if( trim( $xml ) == '' )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
        }
        if( $enforce_xml )
        {
            $xml_obj = new \SimpleXMLElement( $xml );
            $this->getProperty()->xml = $xml_obj->asXML();
        }
        else
        {
            $this->getProperty()->xml = $xml;
        }
        return $this;
    }
}
?>