<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, Peter Thomas <thomaspe.upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/5/2018 Added dumpRESTCommands.
  * 9/15/2016 Added outputDate, outputDuration and setTimeSpaceLimits.
  * 8/26/2016 Added constant NAME_SPACE.
  * 8/24/2016 Added documentation comments and changed the output to SCRIPT_FILENAME.
  * 3/17/2016 Minor bug fix.
  * 5/28/2015 Added namespaces.
  * 7/16/2014 Class created.
 */
namespace cascade_ws_utility; 

use cascade_ws_AOHS      as aohs;
use cascade_ws_asset     as a;
use cascade_ws_exception as e;

/**
<documentation><description><h2>Introduction</h2>
<p>This class can be used to output a string or any object, with additional information on the source of the method call.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/utility-class-test-code/debug-utility.php">debug-utility.php</a></li></ul></postscript>
</documentation>
*/
class DebugUtility
{
    const NAME_SPACE = "cascade_ws_utility";

/**
Outputs the contents of the variable.
@param mixed $var The variable to be output
<documentation><description><p>Outputs the contents of the variable. The output is wrapped inside a &lt;pre&gt; element. Besides the dump of the variable,
the method also outputs the source of the method call with line number.
For example, <code>cascade_ws_asset\Asset::61:</code> means that the method call
is from line 61 of the <code>Asest</code> class,
and <code>/Applications/MAMP/htdocs/utility-class-test-code/debug-utility.php::13:</code> means that
the method call is from line 13 of the test program named <code>debug-utility.php</code>.</p></description>
<example>u\DebugUtility::dump( $page );</example>
<return-type>void</return-type>
<exception></exception>
</documentation>
*/
    public static function dump( $var )
    {
        self::getCallingInfo( $class, $line );
        echo $class . "::" . $line . ": " . BR . S_PRE;
        var_dump( $var );
        echo E_PRE . HR;
    }

/**
<documentation><description><p>Outputs the contents of the array returned by <code>$service->getCommands()</code>.</p></description>
<example>u\DebugUtility::dumpRESTCommands( $service->getCommands() );</example>
<return-type>void</return-type>
<exception></exception>
</documentation>
*/
    public static function dumpRESTCommands( aohs\AssetOperationHandlerService $service )
    {
        if( $service->isRest() )
        {
            self::getCallingInfo( $class, $line );
            echo $class . "::" . $line . ": " . BR . S_PRE;
            var_dump( $service->getCommands() );
            echo E_PRE . HR;

            $service->clearCommands();
        }
        else
        {
            self::out( "Currently the service object is associated with SOAP." );
        }
    }

/**
Outputs the message string.
@param string $msg The message string
<documentation><description><p>Outputs the message string.
The difference between this method and <code>echo</code> is that this method,
besides outputting the message, it also outputs the source of the method call with line number.
For example, <code>cascade_ws_asset\Asset::61:</code> means that the method call
is from line 61 of the <code>Asest</code> class,
and <code>/Applications/MAMP/htdocs/utility-class-test-code/debug-utility.php::15:</code> means that
the method call is from line 15 of the test program named <code>debug-utility.php</code>.</p></description>
<example>u\DebugUtility::out( "Hello" );</example>
<return-type>void</return-type>
<exception></exception>
</documentation>
*/
    public static function out( string $msg=NULL )
    {
        self::getCallingInfo( $class, $line );
        echo $class . "::" . $line . ": " . StringUtility::getCoalescedString( $msg ) . BR;
    }

/**
<documentation><description><p>Outputs the current date, using <code>$format</code> as a format string.</p></description>
<example>u\DebugUtility::outputDate();</example>
<return-type>void</return-type>
<exception></exception>
</documentation>
*/
    public static function outputDate( string $format="l jS \of F Y g:i.s a" )
    {
        date_default_timezone_set( 'US/Eastern' );   
        echo S_P . "Script complete on " . date( $format ) . " &#x1f44d;" . E_P;
    }
    
/**
<documentation><description><p>Initializes the variable <code>end_time</code>, and displays the difference between <code>$start_time</code>
and <code>end_time</code>.</p></description>
<example>$start_time = time();
// code here
u\DebugUtility::outputDuration( $start_time );</example>
<return-type>void</return-type>
<exception></exception>
</documentation>
*/
    public static function outputDuration( $start_time )
    {
        $end_time = time();
        
        if( isset( $start_time ) )
            echo S_P, "Total time taken: " . ( $end_time - $start_time ) . " seconds" . E_P;
    }

/**
<documentation><description><p>Sets the time (in seconds) and space limits when performing asset tree traversal.</p></description>
<example>u\DebugUtility::setTimeSpaceLimits();</example>
<return-type>void</return-type>
<exception></exception>
</documentation>
*/
    public static function setTimeSpaceLimits(
        int $time_limit=10000, string $space_limit="2048M" )
    {
        // to prevent time-out
        set_time_limit ( 10000 );
        // to prevent using up memory when traversing a large site
        ini_set( 'memory_limit', $space_limit );
    }
    
/**
<documentation><description><p>Creates a new message from an exception and rethrows it. 
The new message includes asset information.</p></description>
<example>u\DebugUtility::throwException( $page, $e );</example>
<return-type>void</return-type>
<exception>Exceptions of all kinds</exception>
</documentation>
*/
    public static function throwException( a\Asset $a=NULL, $e )
    {
        if( !is_null( $a ) )
        {
        	$msg = "Asset ID: " .   $a->getId() . "; " .
               "site name: " .  $a->getSiteName() . "; " .
               "asset path: " . $a->getPath() . BR;
        }
        else
        {
        	$msg = "";
        }
            
        $msg .= $e->getMessage();
        
        if( $e instanceof e\EmptyValueException )
            throw new e\EmptyValueException( $msg );
        elseif( $e instanceof e\NoSuchValueException )
            throw new e\NoSuchValueException( $msg );
        elseif( $e instanceof e\UnacceptableValueException )
            throw new e\UnacceptableValueException( $msg );
        elseif( $e instanceof e\NodeException )
            throw new e\NodeException( $msg );
        else
            throw $e;
    }

    private static function getCallingInfo( &$class, &$line ) 
    {
        //get the trace
        $trace = debug_backtrace();
        
        //echo S_PRE;
        //var_dump( $trace );
        //echo E_PRE;

        // Get the class that is asking for who awoke it
        $class_temp = $trace[ 1 ][ 'class' ];
        $line       = $trace[ 1 ][ 'line' ];

        // +1 to i cos we have to account for calling this function
        for ( $i = 1; $i < count( $trace ); $i++ ) 
        {
            if ( isset( $trace[ $i ] ) && isset( $trace[ $i ][ 'class' ] ) ) // is it set?
            {
                 if ( $class_temp != $trace[ $i ][ 'class' ] ) // is it a different class
                 {
                     $class_temp = $trace[ $i ][ 'class' ];
                     break;
                }
            }
        }
        
        if( $class_temp == get_called_class() )
            $class_temp = $_SERVER[ "SCRIPT_FILENAME" ];
        
        $class = $class_temp;
    }
}