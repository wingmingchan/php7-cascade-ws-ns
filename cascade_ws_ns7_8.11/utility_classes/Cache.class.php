<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2019 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/2/2019 Added storeAsset, displayCache and displayCacheKeys.
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
<p>This class implements a cache for storing Asset objects. This is a singleton class; meaning
that only one <code>Cache</code> object is allowed. The constructor cannot be called because
it is <code>private</code>. Instead, call <code>u\Cache::getInstance( $service )</code> to
get the cache.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/utility-class-test-code/cache.php">cache.php</a></li></ul></postscript>
</documentation>
*/
class Cache
{
    const DEBUG      = false;
    const DUMP       = false;
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
Displays the cache array and returns the cache.
<documentation><description><p>Displays the cache array and returns the cache.</p></description>
<example>$cache->displayCache();</example>
<return-type>Cache</return-type>
<exception></exception>
</documentation>
*/
    public function displayCache() : Cache
    {
        DebugUtility::dump( $this->cache );
        return self::$instance;
    }

/**
Displays the keys of the cache array and returns the cache.
<documentation><description><p>Displays the keys of the cache array and returns the cache.</p></description>
<example>$cache->displayCacheKeys();</example>
<return-type>Cache</return-type>
<exception></exception>
</documentation>
*/
    public function displayCacheKeys() : Cache
    {
        DebugUtility::dump( array_keys( $this->cache ) );
        return self::$instance;
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
Stores the supplied asset in the cache.
@param Asset $a The asset to be stored
<documentation><description><p>Stores the supplied asset in the cache.</p></description>
<example>$cache->storeAsset( $page );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function storeAsset( a\Asset $a )
    {
        $id = $a->getId();
        
        if( !isset( $this->cache[ $id ] ) )
        {
            $this->cache[ $id ] = $a;
        }
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

    private function __construct() {}

    private $cache = array();
    private static $instance;
    private static $service;
}
?>