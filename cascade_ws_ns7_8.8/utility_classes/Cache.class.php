<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 8/26/2016 Added constant NAME_SPACE.
  * 5/28/2015 Added namespaces.
  * 7/25/2014 File created.
 */
namespace cascade_ws_utility;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;

/**
<documentation><description><h2>Introduction</h2>
<p>This class implements a cache for storing Asset objects.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/utility-class-test-code/cache.php">cache.php</a></li></ul></postscript>
</documentation>
*/
class Cache
{
    const DEBUG = false;
    const DUMP  = false;
    const NAME_SPACE = "cascade_ws_utility";
    
/**
Clears the cache array.
<documentation><description><p>Clears the cache array.</p></description>
<example>$cache->clearCache();</example>
<return-type>void</return-type>
<exception></exception>
</documentation>
*/
    public function clearCache()
    {
        $this->cache = array();
    }

/**
Retrieves and returns the asset bearing the identifier.
@param Child $child The identifier of the asset
@return Asset The asset retrieved either from the cache or from Cascade
<documentation><description><p>Retrieves and returns the asset bearing the identifier.</p></description>
<example>$template = $cache->retrieveAsset( $template_identifier );</example>
<return-type>cascade_ws_asset\Asset</return-type>
<exception></exception>
</documentation>
*/
    public function retrieveAsset( p\Child $child ) : a\Asset
    {
        $id = $child->getId();
        
        if( !isset( $this->cache[ $id ] ) )
            $this->cache[ $id ] = $child->getAsset( self::$service );
        return $this->cache[ $id ];
    }
    
/**
Returns the cache.
@param AssetOperationHandlerService $service The $service object
@return Cache The cache
<documentation><description><p>Returns the cache.</p></description>
<example>$cache = u\Cache::getInstance( $service );</example>
<return-type>Cache</return-type>
<exception></exception>
</documentation>
*/
    public static function getInstance( aohs\AssetOperationHandlerService $service ) : Cache
    {
        self::$service = $service;
        
        if( empty( self::$instance ) )
        {
            self::$instance = new Cache( $service );
        }
        return self::$instance;
    }
    
    private function __construct() { }
    
    private $cache = array();
    private static $instance;
    private static $service;
}
?>