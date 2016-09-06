<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
 
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_cascade_ws_AOHS as aohs;
use cascade_ws_cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class TextBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::TEXTBLOCK;
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getText()
    {
        return $this->getProperty()->text;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setText( $text )
    {
        if( trim( $text ) == '' )
        {
            throw new e\EmptyValueException( S_SPAN . c\M::EMPTY_TEXT . E_SPAN );
        }
        
        $this->getProperty()->text = $text;
        return $this;
    }
}
?>