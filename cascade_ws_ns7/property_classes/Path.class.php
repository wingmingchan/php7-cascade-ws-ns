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
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

/**
<documentation><description><h2>Introduction</h2>
<p>A <code>Path</code> object represents a <code>path</code> (the parent) property found in a <a href="/web-services/api/property-classes/child"><code>Child</code></a> object inside a <a href="web-services/api/asset-classes/folder"><code>a\Folder</code></a> object.</p>
<h2>Structure of <code>path</code></h2>
<pre>path
  path
  siteId
  siteName
</pre>
<h2>Design Issues</h2>
<ul>
<li>There are no <code>set</code> methods in this class.</li>
</ul>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class Path extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
    
/**
<documentation><description><p>Returns <code>path</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPath()
    {
        return $this->path;
    }
    
/**
<documentation><description><p>Returns <code>siteId</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getSiteId()
    {
        return $this->site_id;
    }
    
/**
<documentation><description><p>Returns <code>siteName</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getSiteName()
    {
        return $this->site_name;
    }

/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
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