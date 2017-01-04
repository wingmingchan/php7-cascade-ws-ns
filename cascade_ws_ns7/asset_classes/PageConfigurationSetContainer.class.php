<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>A <code>PageConfigurationSetContainer</code> object represents a page configuration set container asset. This class is a sub-class of <a href="/web-services/api/asset-classes/container"><code>Container</code></a>.</p>
<h2>Structure of <code>pageConfigurationSetContainer</code></h2>
<pre>pageConfigurationSetContainer
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  children
    child
      id
      path
        path
        siteId
        siteName
      type
      recycled
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/page_configuration_set_container.php">page_configuration_set_container.php</a></li></ul></postscript>
</documentation>
*/
class PageConfigurationSetContainer extends Container
{
    const TYPE = c\T::PAGECONFIGURATIONSETCONTAINER;

/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }
}
?>