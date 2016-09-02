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

class PublishableAssetIdentifier extends Property
{
    public function __construct( 
        \stdClass $psi=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        // could be NULL for text
        if( isset( $psi ) )
        {
            $this->id       = $psi->id;
            $this->path     = new Path( $psi->path );
            $this->type     = $psi->type;
            $this->recycled = $psi->recycled;
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getRecycled()
    {
        return $this->recycled;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function toStdClass()
    {
        $obj                 = new \stdClass();
        $obj->id             = $this->id;
        $obj->path           = $this->path->toStdClass();
        $obj->path->siteName = NULL;
        $obj->type           = $this->type;
        $obj->recycled       = $this->recycled;
        return $obj;
    }

    private $id;
    private $path;
    private $type;
    private $recycled;
}
?>
