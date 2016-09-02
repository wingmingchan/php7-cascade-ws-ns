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

class FileSystemTransport extends Transport
{
    const DEBUG = false;
    const TYPE  = c\T::FSTRANSPORT;
    
    public function getDirectory()
    {
        return $this->getProperty()->directory;
    }
    
    public function setDirectory( $d )
    {
        if( trim( $d ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_DIRECTORY . E_SPAN );
            
        $this->getProperty()->directory = $d;
        return $this;
    }
}
?>
