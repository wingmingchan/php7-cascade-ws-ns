<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 6/30/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/12/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
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
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>XsltFormat</code> represents a XSLT format asset. The class <code>ScriptFormat</code> is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/format.php\"><code>Format</code></a>.</p>
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
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "xsltFormat" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/xslt_format.php">xslt_format.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/format_XSLT/fd27c1988b7f08560159f3f012c360e7

{
  "asset":{
    "xsltFormat":{
      "xml":"\u003c?xml version\u003d\"1.0\" encoding\u003d\"UTF-8\"?\u003e\r\n\u003c!DOCTYPE xsl:stylesheet [\u003c!ENTITY nbsp \"\u0026amp;#160;\"\u003e]\u003e\r\n\u003cxsl:stylesheet version\u003d\"1.0\" xmlns:xsl\u003d\"http://www.w3.org/1999/XSL/Transform\"\u003e\r\n\t\u003cxsl:output indent\u003d\"yes\" method\u003d\"xml\"/\u003e\r\n    \r\n    \u003c!-- This format is used by media site --\u003e\r\n    \r\n\t\u003cxsl:variable name\u003d\"image-extensions\"\u003ejpg,gif,png\u003c/xsl:variable\u003e\r\n\t\r\n\t\u003c!-- The following match is in the event it is from an index block in the data definition --\u003e\r\n\t\u003cxsl:template match\u003d\"path | name\"/\u003e\r\n\t\r\n\t\u003c!-- Find the first configuration, and copy the template content --\u003e\r\n\t\u003cxsl:template match\u003d\"system-index-block\"\u003e\r\n\t\t\u003cxsl:if test\u003d\"//system-page[@current]/summary\"\u003e\r\n\t\t\t\u003cp\u003e\u003cxsl:value-of select\u003d\"//system-page[@current\u003d\u0027true\u0027]/summary\"/\u003e\u003c/p\u003e\r\n\t\t\u003c/xsl:if\u003e\r\n\t\t\u003cul\u003e\r\n\t\t\t\u003cxsl:apply-templates select\u003d\"system-folder | system-page[name !\u003d \u0027index\u0027 and name !\u003d \u0027default\u0027] | system-file | system-symlink\"/\u003e\r\n\t\t\u003c/ul\u003e\r\n\t\u003c/xsl:template\u003e\r\n\t\r\n\t\u003cxsl:template match\u003d\"system-folder\"\u003e\r\n\t\t\u003cxsl:if test\u003d\"display-name\"\u003e\r\n\t\t\t\u003cli\u003e\r\n\t\t\t\t\u003ca\u003e\r\n\t\t\t\t\t\u003cxsl:choose\u003e\r\n\t\t\t\t\t\t\u003cxsl:when test\u003d\"system-page/name \u003d \u0027index\u0027\"\u003e\u003cxsl:attribute name\u003d\"href\"\u003e\u003cxsl:value-of select\u003d\"path\"/\u003e/index\u003c/xsl:attribute\u003e\u003c/xsl:when\u003e\r\n\t\t\t\t\t\t\u003cxsl:otherwise\u003e\u003cxsl:attribute name\u003d\"href\"\u003e\u003cxsl:value-of select\u003d\"path\"/\u003e/index\u003c/xsl:attribute\u003e\u003c/xsl:otherwise\u003e\r\n\t\t\t\t\t\u003c/xsl:choose\u003e\r\n\t\t\t\t\t\u003cxsl:value-of select\u003d\"display-name\"/\u003e\r\n\t\t\t\t\u003c/a\u003e\r\n\t\t\t\u003c/li\u003e\r\n\t\t\t\u003cxsl:apply-templates select\u003d\"system-page[name !\u003d \u0027index\u0027 and name !\u003d \u0027default\u0027] | system-file | system-symlink\"/\u003e\r\n\t\t\u003c/xsl:if\u003e\r\n\t\u003c/xsl:template\u003e\r\n\t\r\n\t\u003cxsl:template match\u003d\"system-file\"\u003e\r\n\t\t\u003cxsl:variable name\u003d\"extension\"\u003e\r\n\t\t\t\u003cxsl:call-template name\u003d\"getExtension\"\u003e\r\n\t\t\t\t\u003cxsl:with-param name\u003d\"path\"\u003e\u003cxsl:value-of select\u003d\"path\"/\u003e\u003c/xsl:with-param\u003e\r\n\t\t\t\u003c/xsl:call-template\u003e\r\n\t\t\u003c/xsl:variable\u003e\r\n\t\t\u003cxsl:choose\u003e\r\n\t\t\t\u003cxsl:when test\u003d\"@current\u003d\u0027true\u0027\"\u003e\r\n\t\t\t \u003c/xsl:when\u003e\r\n\t\t\t\u003cxsl:otherwise\u003e\r\n\t\t\t\t\u003cli\u003e\r\n\t\t\t\t\t\u003c!-- Output the name of the page as a link --\u003e\r\n\t\t\t\t\t\u003ca class\u003d\"navlefttitle\" href\u003d\"{path}\"\u003e\r\n\t\t\t\t\t\t\u003cxsl:if test\u003d\"contains($image-extensions,$extension)\"\u003e\r\n\t\t\t\t\t\t\t\u003cimg alt\u003d\"{name}\" border\u003d\"0\" src\u003d\"{path}\"/\u003e\u003cbr/\u003e\r\n\t\t\t\t\t\t\u003c/xsl:if\u003e\r\n\t\t\t\t\t\t\u003cxsl:choose\u003e\r\n\t\t\t\t\t\t\t\u003cxsl:when test\u003d\"display-name !\u003d \u0027\u0027\"\u003e\r\n\t\t\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"name\"/\u003e - \u003cxsl:value-of select\u003d\"display-name\"/\u003e\r\n\t\t\t\t\t\t\t\u003c/xsl:when\u003e\r\n\t\t\t\t\t\t\t\u003cxsl:otherwise\u003e\r\n\t\t\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"name\"/\u003e\r\n\t\t\t\t\t\t\t\u003c/xsl:otherwise\u003e\r\n\t\t\t\t\t\t\u003c/xsl:choose\u003e\r\n\t\t\t\t\t\u003c/a\u003e\r\n\t\t\t\t\t\u003cxsl:if test\u003d\"summary\"\u003e\r\n\t\t\t\t\t\t\u0026nbsp;\u003cxsl:value-of select\u003d\"summary\"/\u003e\u003cbr/\u003e\r\n\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"author\"/\u003e\u003cbr/\u003e\r\n\t\t\t\t\t\u003c/xsl:if\u003e\r\n\t\t\t\t\u003c/li\u003e\r\n\t\t\t\u003c/xsl:otherwise\u003e\r\n\t\t\u003c/xsl:choose\u003e\r\n\t\u003c/xsl:template\u003e\r\n\t\r\n\t\u003cxsl:template match\u003d\"system-page\"\u003e\r\n\t\t\u003cxsl:choose\u003e\r\n\t\t\t\u003cxsl:when test\u003d\"@current\u003d\u0027true\u0027\"\u003e\r\n\t\t\t \u003c/xsl:when\u003e\r\n\t\t\t\u003cxsl:otherwise\u003e\r\n\t\t\t\t\u003cli\u003e\r\n\t\t\t\t\t\u003cxsl:if test\u003d\"dynamic-metadata[name\u003d\u0027Date and Time\u0027]/value !\u003d \u0027\u0027\"\u003e\r\n\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"dynamic-metadata[name\u003d\u0027Date and Time\u0027]/value\"/\u003e\u003cbr/\u003e\r\n\t\t\t\t\t\u003c/xsl:if\u003e\r\n\t\t\t\t\t\u003ca class\u003d\"navlefttitle\"\u003e\r\n\t\t\t\t\t\t\u003cxsl:attribute name\u003d\"href\"\u003e\u003cxsl:value-of select\u003d\"path\"/\u003e\u003c/xsl:attribute\u003e\r\n\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"display-name\"/\u003e\r\n\t\t\t\t\t\u003c/a\u003e\r\n\t\t\t\t\t\u003cxsl:if test\u003d\"summary\"\u003e\r\n\t\t\t\t\t\t\u0026nbsp;\u003cxsl:value-of select\u003d\"summary\"/\u003e\u003cbr/\u003e\r\n\t\t\t\t\t\u003c/xsl:if\u003e\r\n\t\t\t\t\t\u003cxsl:if test\u003d\"dynamic-metadata[name\u003d\u0027Posted By\u0027]/value !\u003d \u0027\u0027\"\u003e\r\n\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"dynamic-metadata[name\u003d\u0027Posted By\u0027]/value\"/\u003e\u003cbr/\u003e\r\n\t\t\t\t\t\u003c/xsl:if\u003e\r\n\t\t\t\t\u003c/li\u003e\r\n\t\t\t\u003c/xsl:otherwise\u003e\r\n\t\t\u003c/xsl:choose\u003e\r\n\t\u003c/xsl:template\u003e\r\n\t\r\n\t\u003cxsl:template match\u003d\"system-symlink\"\u003e\r\n\t\t\u003cxsl:choose\u003e\r\n\t\t\t\u003cxsl:when test\u003d\"@current\u003d\u0027true\u0027\"\u003e\r\n         \u003c/xsl:when\u003e\r\n\t\t\t\u003cxsl:otherwise\u003e\r\n\t\t\t\t\u003cli\u003e\r\n\t\t\t\t\t\u003ca class\u003d\"navlefttitle\"\u003e\r\n\t\t\t\t\t\t\u003cxsl:attribute name\u003d\"href\"\u003e\u003cxsl:value-of select\u003d\"link\"/\u003e\u003c/xsl:attribute\u003e\r\n\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"name\"/\u003e\r\n\t\t\t\t\t\u003c/a\u003e\r\n\t\t\t\t\t\u003cxsl:if test\u003d\"summary\"\u003e\r\n\t\t\t\t\t\t\u0026nbsp;\u003cxsl:value-of select\u003d\"summary\"/\u003e\u003cbr/\u003e\r\n\t\t\t\t\t\u003c/xsl:if\u003e\r\n\t\t\t\t\u003c/li\u003e\r\n\t\t\t\u003c/xsl:otherwise\u003e\r\n\t\t\u003c/xsl:choose\u003e\r\n\t\u003c/xsl:template\u003e\r\n\t\r\n\t\u003cxsl:template name\u003d\"getExtension\"\u003e\r\n\t\t\u003cxsl:param name\u003d\"path\"/\u003e\r\n\t\t\u003cxsl:choose\u003e\r\n\t\t\t\u003cxsl:when test\u003d\"contains($path,\u0027.\u0027)\"\u003e\r\n\t\t\t\t\u003cxsl:call-template name\u003d\"getExtension\"\u003e\r\n\t\t\t\t\t\u003cxsl:with-param name\u003d\"path\"\u003e\r\n\t\t\t\t\t\t\u003cxsl:value-of select\u003d\"substring-after($path,\u0027.\u0027)\"/\u003e\r\n\t\t\t\t\t\u003c/xsl:with-param\u003e\r\n\t\t\t\t\u003c/xsl:call-template\u003e\r\n\t\t\t\u003c/xsl:when\u003e\r\n\t\t\t\u003cxsl:otherwise\u003e\r\n\t\t\t\t\u003cxsl:value-of select\u003d\"$path\"/\u003e\r\n\t\t\t\u003c/xsl:otherwise\u003e\r\n\t\t\u003c/xsl:choose\u003e\r\n\t\u003c/xsl:template\u003e\r\n\u003c/xsl:stylesheet\u003e",
      "parentFolderId":"fd2791df8b7f08560159f3f0e8c463af",
      "parentFolderPath":"formats/index",
      "lastModifiedDate":"Jan 23, 2018 10:50:57 AM",
      "lastModifiedBy":"wing",
      "createdDate":"Aug 6, 2012 2:17:17 PM",
      "createdBy":"admin",
      "path":"formats/index/folder summary",
      "siteId":"fd27691f8b7f08560159f3f02754e61d",
      "siteName":"_common",
      "name":"folder summary",
      "id":"fd27c1988b7f08560159f3f012c360e7"
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