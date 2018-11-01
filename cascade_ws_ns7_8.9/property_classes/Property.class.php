<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 8/26/2016 Added constant NAME_SPACE.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

/**
<documentation><description>
<h2>Relationship between <code>Property</code> and Other Property Classes</h2>
<p>I decide to make <code>Property</code> an abstract class. Other property classes like <code>PageRegion</code> and <code>Metadata</code> should extend this abstract class. Later, we may need interfaces of which the <code>Property</code> class will be an implementation.</p>
<h2>Definition</h2>
<p>The <code>Property</code> class is defined in this way:</p>
<pre class="code">&lt;?php 
abstract class Property
{
    public abstract function __construct( 
        \stdClass $obj=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL );

    public abstract function toStdClass();
}
?&gt;
</pre>
<p>The constructor converts an <code>\stdClass</code> object to a <code>Property</code> object, and the <code>toStdClass</code> method convert a <code>Property</code> object back to an <code>\stdClass</code> object. Parameters like <code>$data1</code> are used by individual sub-classes.</p>
</description>
</documentation>
*/
abstract class Property
{
    const NAME_SPACE = "cascade_ws_property";

/**
<documentation><description></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public abstract function __construct(
        \stdClass $obj=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL );
        
/**
<documentation><description></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public abstract function toStdClass();
}
?>