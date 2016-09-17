<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/17/2016 Added initialization of recycled.
  * 12/15/2015 Fixed a bug in getAsset.
  * 6/16/2015 Fixed a bug in toStdClass.
  * 5/28/2015 Added namespaces.
  * 8/22/2014 Fixed a bug in toXml.
  * 6/23/2014 Added lastmod attribute to toXml for site map.
  * 5/12/2014 data in $c can be NULL, for audit
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_asset as a;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
 
class Child extends Property
{
    public function __construct(
        \stdClass $c=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $c ) )
        {
            if( isset( $c->id ) )
                $this->id = $c->id;
                
            if( isset( $c->path ) )
                $this->path = new Path( $c->path );
            else
                $this->path = NULL;
            $this->type     = $c->type;
            
            if( isset( $c->recycled ) )
                $this->recycled = $c->recycled;
            else
            	$this->recycled = false;
        }
        else
        {
            throw new e\NullIdentifierException( c\M::NULL_IDENTIFIER );
        }
    }
    
    public function display()
    {
        echo "Type: " . $this->type . BR .
            "Path: "  . $this->path->getPath() . BR .
            "ID: "    . $this->id . BR . BR;
    }
    
    public function getAsset( aohs\AssetOperationHandlerService $service )
    {
        if( $service == NULL )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        if( isset( $this->id ) )
            return a\Asset::getAsset( $service, $this->type, $this->id );
        else
            return a\Asset::getAsset( 
                $service, 
                $this->type, $this->path->getPath(), $this->path->getSiteName() );
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getPathPath()
    {
        if( isset( $this->path ) )
            return $this->path->getPath();
    }
    
    public function getPathSiteId()
    {
        return $this->path->getSiteId();
    }
    
    public function getPathSiteName()
    {
        if( isset( $this->path ) )
            return $this->path->getSiteName();
    }
    
    public function getRecycled()
    {
        return $this->recycled;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function toLiString()
    {
        return S_LI . $this->type . " " . 
            $this->path->getPath() . " " . $this->id . E_LI;
    }
    
    public function toStdClass()
    {
        $obj           = new \stdClass();
        
        if( isset( $this->id ) )
            $obj->id   = $this->id;
        
        if( isset( $this->path ) )
            $obj->path = $this->path->toStdClass();
            
        $obj->type     = $this->type;
        $obj->recycled = $this->recycled;
        return $obj;
    }
    
    public function toXml( $indent, aohs\AssetOperationHandlerService $service )
    {
        if( isset( $service ) )
        {
            $asset = $this->getAsset( $service );
            
            if( method_exists( $asset, "getLastModifiedDate" ) )
            {
                $lastmod = $asset->getLastModifiedDate();
            }
        }
        return $indent . "<" . $this->type . " path=\"" .
            $this->path->getPath() . "\" id=\"" . $this->id . "\"" .
            ( isset( $lastmod ) ? " lastmod=\"" . $lastmod : ""  ) .
            "\"/>\n";
    }
    
    private $id;
    private $path;
    private $type;
    private $recycled;
}
?>
