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

class ScriptFormat extends Format
{
    const DEBUG = false;
    const TYPE  = c\T::VELOCITYFORMAT;
    
    public function displayScript()
    {
        $script_string = htmlentities( $this->getProperty()->script ); // &
        $script_string = u\XMLUtility::replaceBrackets( $script_string );
        
        echo S_H2 . "Script" . E_H2 .
             S_PRE . $script_string . E_PRE . HR;
        
        return $this;
    }

    public function getScript()
    {
        return $this->getProperty()->script;
    }
    
    public function setScript( $script )
    {
        $this->getProperty()->script = $script;
        
        return $this;
    }
}
?>