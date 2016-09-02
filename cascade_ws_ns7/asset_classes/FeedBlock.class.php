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

class FeedBlock extends Block
{
    const DEBUG = false;
    const TYPE  = c\T::FEEDBLOCK;
    
    public function getFeedURL()
    {
        return $this->getProperty()->feedURL;
    }
    
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