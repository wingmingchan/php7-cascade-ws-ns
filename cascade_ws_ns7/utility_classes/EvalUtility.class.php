<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/15/2017 File created.
 */
namespace cascade_ws_utility; 

/**
<documentation><description><h2>Introduction</h2>
<p>This class provides a wrapper object through which static methods of other classes
can be called in PHP code to be evaluated when generating documentation. When PHP code is
embedded in documentation, calls like <code>u\DebugUtility::dump()</code> is impossible.
Instead, an object named <code>$eval</code>, instantiated in an authentication file, can
be used to invoke the <code>dump</code> method defined in this class to call
<code>u\DebugUtility::dump()</code>.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/utility-class-test-code/xml-utility.php">xml-utility.php</a></li></ul></postscript>
</documentation>
*/
class EvalUtility
{
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct()
    {
        /* empty constructor */
    }
    
/**
<documentation><description><p>Calls <code>DebugUtility::dump</code>.</p></description>
<example>$eval->dump( $asset );</example>
<return-type>EvalUtility</return-type>
<exception></exception>
</documentation>
*/
    public function dump( $obj )
    {
        DebugUtility::dump( $obj );
        return $this;
    }

/**
<documentation><description><p>Calls <code>XMLUtility::replaceBrackets</code>.</p></description>
<example>echo $eval->replaceBrackets( $xml );</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function replaceBrackets( string $xml ) : string
    {
        return XMLUtility::replaceBrackets( $xml );
    }
}
?>