<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                       German Drulyk <drulykg@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/12/2018 Added attachStringWithDelimiter.
  * 1/11/2018 Added binaryToCharArray to deal with binary data for REST.
  * 9/30/2016 Added getDatabasePHPCode.
  * 9/9/2016 Added $prefix to getMethodName.
  * 8/26/2016 Added constant NAME_SPACE.
  * 8/24/2016 Added documentation comments. Added boolToString and stringToBool.
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
    const NAME_SPACE = "cascade_ws_utility";
    
/**
<documentation><description><p>Returns a string, with the <code>$append</code> attached to the end, separated by <code>$delimiter</code>.</p></description>
<example>echo u\StringUtility::attachStringWithDelimiter(
    $this->users, self::DELIMITER, $u->getName() );</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function attachStringWithDelimiter(
        string $initial_string=NULL, string $delimiter, string $append ) : string
    {
        $str_array = explode( $delimiter, $initial_string . $delimiter . $append );
        $str_array = array_flip( $str_array );
        unset( $str_array[ '' ] );
        $str_array = array_keys( $str_array );

        return implode( $delimiter, $str_array );
    }
/**
<documentation><description><p>Returns a char array by converting the binary data.</p></description>
<example>u\DebugUtility::dump(
    StringUtility::binaryToCharArray( file_get_contents( $image_url ) );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function binaryToCharArray( $data ) : array
    {
        $temp_array = array();
        
        if( isset( $data ) )
            return array_values( unpack( "c*", $data ) );
        
        return $temp_array;
    }
    
/**
<documentation><description><p>Returns a string value of the bool.</p></description>
<example>echo u\StringUtility::boolToString( true ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function boolToString( bool $value ) : string
    {
        if( $value )
            return "true";
        else
            return "false";
    }
    
/**
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
<documentation><description><p>Returns a coalesed string. If the parameter is a string,
then the string is returned. If <code>NULL</code> is passed in, then the string <code>NULL</code> is returned.</p></description>
<example>echo u\StringUtility::getCoalescedString( $m->getEndDate() ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getCoalescedString( string $str_null=NULL ) : string
    {
        return $str_null ?? 'NULL';
    }

/**
<documentation><description><p>Returns the PHP code read from a URL.</p></description>
<example>$code = file_get_contents( $url );
eval( u\StringUtility::getDatabasePHPCode( $code ) );</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getDatabasePHPCode( string $code ) : string
    {
        // remove start and end tag
        $code = str_replace( '<system-region name="DEFAULT">', '', $code );
        $code = str_replace( '</system-region>', '', $code );
        // replace &gt;
        $code = str_replace( '&gt;', '>', $code );
        $code = trim( $code );

        return $code;
    }

/**
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
<documentation><description><p>Returns a method name out of a property name.
For example, <code>getMethodName( "structuredData" )</code> returns <code>getStructuredData</code>.</p></description>
<example>echo u\StringUtility::getMethodName( "structuredData" ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getMethodName( string $property_name, string $prefix="get" ) : string
    {
        return $prefix . ucwords( $property_name );
    }
    
/**
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
<documentation><description><p>Returns the path part of a path string, removing the substring preceding ":".
For example, <code>removeSiteNameFromPath( "site://web-services/api/utility-classes/debug-utility" )</code>
returns <code>//web-services/api/utility-classes/debug-utility</code>.</p></description>
<example>echo u\StringUtility::removeSiteNameFromPath(
    "site://web-services/api/utility-classes/debug-utility" ), BR;</example>
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

/**
<documentation><description><p>Returns a bool value of the string.</p></description>
<example>if( u\StringUtility::stringToBool( "true" ) )
    echo "Tis true", BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public static function stringToBool( string $value ) : bool
    {
        if( $value === "true" || $value === "1" )
            return true;
        else
            return false;
    }
}
?>