<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
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
<p>While I am arguing that there is no need to split the class <a href="http://www.upstate.edu/web-services/api/asset-classes/data-definition-block.php"><code>DataDefinitionBlock</code></a> into two to do justice to xhtmlDataDefinition blocks without the support of data definitions, I know that it is unfair to name the class <code>DataDefinitionBlock</code>. For blocks not using data definitions, <code>DataDefinitionBlock</code> is simply a misnomer. To remedy this problem, I want to use alias again. But this time, I want to use a class alias.</p>
<p>Class aliases are easy to create. We already have a class <code>DataDefinitionBlock</code>. We just need to extend this class and give the new class a new name. There is no implementation in the new class. It just inherits everything from its parent. In the test code, we simply change the old class name to the new class name. Everything works fine.</p>
<p><code>DataBlock</code> is just an alias (to be exact, an empty sub-class) of <a href="http://www.upstate.edu/web-services/api/asset-classes/data-definition-block.php"><code>DataDefinitionBlock</code></a>. I hope that to a certain extent this will do justice to xhtmlDataDefinition blocks without the support of data definitions.</p>
</description>
<postscript></postscript>
</documentation>
*/
class DataBlock extends DataDefinitionBlock
{
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