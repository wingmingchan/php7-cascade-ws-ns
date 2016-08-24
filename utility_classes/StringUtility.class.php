<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 8/24/2016 Added documentation comments.
  * 5/28/2015 Added namespaces.
  * 9/8/2014 Added getParentPathFromPath.
  * 8/13/2014 Added removeSiteNameFromPath.
  * 8/9/2014 Added getFullyQualifiedIdentifierWithoutPositions.
  * 7/29/2014 Added getMethodName.
  * 7/11/2014 Added getNameFromPath.
  * 5/22/2014 Fixed some bugs.
 */
namespace cascade_ws_utility; 

/**
<documentation><description><h2>Introduction</h2>
<p>This class provides string utility services.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/utility-class-test-code/string-utility.php">string-utility.php</a></li></ul></postscript>
</documentation>
*/
class StringUtility
{
/**
Returns a bool, indicating whether the $haystack ends with $needle.
@param string $haystack The string to be examined
@param string $needle The substring to be searched
@return bool Whether $haystack ends with $needle
<documentation><description><p>Returns a bool, indicating whether the <code>$haystack</code> ends with <code>$needle</code>.</p></description>
<example>echo ( u\StringUtility::endsWith( "Hello", "lo" ) ? "yes" : "no" ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function endsWith( string $haystack, string $needle ) : bool
    {
        return $needle === "" || substr( $haystack, -strlen( $needle ) ) === $needle;
    }
    
/**
Returns an array out of the string, using $delimiter as the delimiter.
@param string $delimiter The delimiter string
@param string $string The string to be split
@return array The array of split strings
<documentation><description><p>Returns an array out of the <code>$string</code>, using <code>$delimiter</code> as the delimiter.</p></description>
<example>u\DebugUtility::dump( u\StringUtility::getExplodedStringArray( ";", "this;0;that;3;these" ) );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public static function getExplodedStringArray( $delimiter, $string ) : array
    {
        $temp   = array();
        $tokens = explode( $delimiter, $string );
        
        if( count( $tokens ) > 0 )
        {
            foreach( $tokens as $token )
            {
                if( trim( $token, " \n\t" ) != "" && trim( $token, " \n\t" ) != "<?xml version=\"1.0\"?>" )
                {
                    $temp[] = trim( $token, " \n\t" );
                }
            }
        }
        return $temp;
    }
    
/**
Returns a fully qualified identifier, with all the position information stripped.
@param string $identifier The input fully qualified identifier
@return string The fully qualified identifier with all the position information stripped
<documentation><description><p>Returns a fully qualified identifier, with all the position information stripped.
For example, <code>getFullyQualifiedIdentifierWithoutPositions( "this;0;that;3;these" )</code> returns <code>this;that;these</code>.</p></description>
<example>echo u\StringUtility::getFullyQualifiedIdentifierWithoutPositions( "this;0;that;3;these" ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getFullyQualifiedIdentifierWithoutPositions( string $identifier ) : string
    {
        $temp         = self::getExplodedStringArray( ";", $identifier );
        $result_array = array();
        
        if( count( $temp ) > 0 )
        {
            foreach( $temp as $part )
            {
                if( !is_numeric(  $part ) )
                {
                    $result_array[] = $part;
                }
            }
        }
        return implode( ";", $result_array );
    }
    
/**
Returns a method name out of a property name.
@param string $property_name The property name in camel case
@return string Method name prefixed with get
<documentation><description><p>Returns a method name out of a property name.
For example, <code>getMethodName( "structuredData" )</code> returns <code>getStructuredData</code>.</p></description>
<example>echo u\StringUtility::getMethodName( "structuredData" ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getMethodName( string $property_name ) : string
    {
        return 'get' . ucwords( $property_name );
    }
    
/**
Returns the last part of a path string, the substring after the last slash.
@param string $path The path string
@return string The substring of the path after the last slash
<documentation><description><p>Returns the last part of a path string, the substring after the last slash.
For example, <code>getNameFromPath( "/web-services/api/utility-classes/debug-utility" )</code> returns <code>debug-utility</code>.</p></description>
<example>echo u\StringUtility::getNameFromPath( "/web-services/api/utility-classes/debug-utility" ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getNameFromPath( string $path ) : string
    {
        $array = StringUtility::getExplodedStringArray( '/', $path );
        $count = count( $array );
        
        if( $count > 0 )
            return $array[ $count - 1 ]; // last element
            
        return ""; // empty string
    }
    

/**
Returns the initial part of a path string, the substring before the last slash.
@param string $path The path string
@return string The substring of the path before the last slash
<documentation><description><p>Returns the initial part of a path string, the substring before the last slash.
For example, <code>getParentPathFromPath( "/web-services/api/utility-classes/debug-utility" )</code> returns <code>web-services/api/utility-classes</code>,
without leading nor trailing slashes.</p></description>
<example>echo u\StringUtility::getParentPathFromPath(
    "/web-services/api/utility-classes/debug-utility" ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getParentPathFromPath( string $path ) : string
    {
        $array = StringUtility::getExplodedStringArray( '/', $path );
        $count = count( $array );
        
        if( $count == 1 )
            return "/";
        else if( $count > 1 )
        {
            return
                implode( '/', array_slice( $array, 0, count( $array ) - 1 ) );        
        }
            
        return ""; // empty string
    }
    
/**
Returns the path part of a path string, removing the substring preceding ":".
@param string $path The path string
@return string The path part of a path string
<documentation><description><p>Returns the path part of a path string, removing the substring preceding ":".
For example, <code>removeSiteNameFromPath( "site://cascade-admin/web-services/api/utility-classes/debug-utility" )</code>
returns <code>//cascade-admin/web-services/api/utility-classes/debug-utility</code>.</p></description>
<example>echo u\StringUtility::removeSiteNameFromPath(
    "site://cascade-admin/web-services/api/utility-classes/debug-utility" ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function removeSiteNameFromPath( string $path ) : string
    {
        if( strpos( $path, ":" ) !== false )
            $path = substr( $path, strpos( $path, ":" ) + 1 );
        return $path;
    }
    
/**
Returns a bool, indicating whether the $haystack starts with $needle.
@param string $haystack The string to be examined
@param string $needle The substring to be searched
@return bool Whether $haystack starts with $needle
<documentation><description><p>Returns a bool, indicating whether the <code>$haystack</code> starts with <code>$needle</code>.</p></description>
<example>echo ( u\StringUtility::startsWith( "Hello", "He" ) ? "yes" : "no" ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function startsWith( string $haystack, string $needle ) : bool
    {
        return $needle === "" || strpos( $haystack, $needle ) === 0;
    }
}
?>