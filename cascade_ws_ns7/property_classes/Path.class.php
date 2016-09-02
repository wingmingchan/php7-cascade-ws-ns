<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;

class Path extends Property
{
    public function __construct( 
        \stdClass $p=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $p ) )
        {
            $this->path      = $p->path;
            
            if( isset( $p->siteId ) )
                $this->site_id   = $p->siteId;
                
            if( isset( $p->siteName ) )
                $this->site_name = $p->siteName;
        }
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getSiteId()
    {
        return $this->site_id;
    }
    
    public function getSiteName()
    {
        return $this->site_name;
    }

    public function toStdClass()
    {
        $obj           = new \stdClass();
        $obj->path     = $this->path;
        $obj->siteId   = $this->site_id;
        $obj->siteName = $this->site_name;
        return $obj;
    }

    private $path;
    private $site_id;
    private $site_name;
}
?>
