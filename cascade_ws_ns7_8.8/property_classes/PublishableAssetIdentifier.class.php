<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 7/14/2017 Added call to getXMLFragments.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>PublishableAssetIdentifier</code> object represents a <code>publishableAssetIdentifier</code> property found in a <a href=\"http://www.upstate.edu/web-services/api/asset-classes/publish-set.php\"><code>a\PublishSet</code></a> object. It is used to identify publishable assets of type page, file, and folder.</p>
<h2>Structure of <code>publishableAssetIdentifier</code></h2>
<pre>publishableAssetIdentifier (NULL, object or array)
  id
  path
    path
    siteId
    siteName (always NULL)
  type
  recycled
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "publishable-asset-list" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript></postscript>
</documentation>
*/
class PublishableAssetIdentifier extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
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
            if( isset( $psi->id ) )
                $this->id       = $psi->id;
            if( isset( $psi->path ) )
                $this->path     = new Path( $psi->path );
            if( isset( $psi->type ) )
                $this->type     = $psi->type;
            if( isset( $psi->recycled ) )
                $this->recycled = $psi->recycled;
        }
    }
    
/**
<documentation><description><p>Returns <code>id</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getId()
    {
        return $this->id;
    }
    
/**
<documentation><description><p>Returns (the parent) <code>path</code> ( a <code>Path</code> object).</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getPath()
    {
        return $this->path;
    }
    
/**
<documentation><description><p>Returns <code>recycled</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getRecycled()
    {
        return $this->recycled;
    }
    
/**
<documentation><description><p>Returns <code>type</code>.</p></description>
<example></example>
<return-type></return-type>
</documentation>
*/
    public function getType()
    {
        return $this->type;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
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