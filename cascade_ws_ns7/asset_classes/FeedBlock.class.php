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
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class FeedBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::FEEDBLOCK;
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getFeedURL()
    {
        return $this->getProperty()->feedURL;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setFeedURL( $url )
    {
        if( trim( $url ) == '' )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
        }
        
        $this->getProperty()->feedURL = $url;
        return $this;
    }
}
?>