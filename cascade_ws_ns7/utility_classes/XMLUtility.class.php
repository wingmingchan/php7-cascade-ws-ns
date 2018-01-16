<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/18/2014 Added isXmlIdentical.
 */
namespace cascade_ws_utility; 

/**
<documentation><description><h2>Introduction</h2>
<p>This class provides XML utility services.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/utility-class-test-code/xml-utility.php">xml-utility.php</a></li></ul></postscript>
</documentation>
*/
class XmlUtility
{
/**
Returns a bool, indicating whether the two SimpleXMLElement objects store identical XML markups.
@param SimpleXMLElement $xml1 The first SimpleXMLElement object
@param SimpleXMLElement $xml2 The second SimpleXMLElement object
@return bool The bool value
<documentation><description><p>Returns a bool, indicating whether the two SimpleXMLElement objects store identical XML markups.</p></description>
<example>if( u\XmlUtility::isXmlIdentical( $source_s_xml, $target_s_xml ) )
    echo "Identical", BR;
else
    echo "Different", BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function isXmlIdentical( \SimpleXMLElement $xml1, \SimpleXMLElement $xml2 ) : bool
    {
        return $xml1->asXML() == $xml2->asXML();
    }
    
/**
Returns a string, with all the angular brackets replaced by &amp;lt; and &amp;gt;.
@param string $xml The XML string
@return string The string with all the angular brackets replaced by &amp;lt; and &amp;gt;
<documentation><description><p>Returns a string, with all the angular brackets replaced by <code>&amp;lt;</code> and <code>&amp;gt;</code>.</p></description>
<example>echo S_PRE, u\XmlUtility::replaceBrackets( $source_xml ), E_PRE;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function replaceBrackets( string $xml ) : string
    {
        $xml = str_replace( '<', '&lt;', $xml );
        $xml = str_replace( '>', '&gt;', $xml );
        
        return self::formatXML( self::removeWhitespace( $xml ) );
    }
    
    public static function removeWhitespace( string $str ) : string
    {
        // remove all extra whitespace characters
        $str = trim( preg_replace( '/\s\s+/', '', $str ) );
        $str = str_replace( "REQUIRED", " REQUIRED",
            str_replace( "NOT REQUIRED", " NOT REQUIRED", $str ) );
        // keep one whitespace characters
        $str = trim( preg_replace( '/\s\s+/', ' ', $str ) );

        return $str;
    }
    
    public static function formatXML( $str )
    {
        $str = str_replace( "&lt;", "\r&lt;", $str );
        return $str;
    }
}
?>