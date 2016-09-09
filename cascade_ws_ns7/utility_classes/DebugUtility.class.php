<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 8/26/2016 Added constant NAME_SPACE.
  * 8/24/2016 Added documentation comments and changed the output to SCRIPT_FILENAME.
  * 3/17/2016 Minor bug fix.
  * 5/28/2015 Added namespaces.
  * 7/16/2014 Class created.
 */
namespace cascade_ws_utility; 

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