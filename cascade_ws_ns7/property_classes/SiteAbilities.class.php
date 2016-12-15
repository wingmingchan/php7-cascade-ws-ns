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
<p>A <code>SiteAbilities</code> object represents the <code>siteAbilities</code> property found in a role asset. This class is a sub-class of <a href="/web-services/api/property-classes/abilities"><code>Abilities</code></a>.</p>
<h2>Properties of <code>siteAbilities</code></h2>
<p>Besides the 49 properties (Cascade 8) shared with the sibling class <a href="/web-services/api/property-classes/global-abilities"><code>GlobalAbilities</code></a> (which are defined in the parent class <a href="/web-services/api/property-classes/abilities"><code>Abilities</code></a>), this class has two more additional properties:</p>
<pre>accessConnectors
accessDestinations
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class SiteAbilities extends Abilities
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        \stdClass $a=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $a ) )
        {
            parent::__construct( $a );
        }
        
        $this->access_connectors       = $a->accessConnectors;
        $this->access_destinations     = $a->accessDestinations;
    }
        
/**
<documentation><description><p>Returns <code>accessConnectors</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessConnectors() : bool
    {
        return $this->access_connectors;
    }
    
/**
<documentation><description><p>Returns <code>accessDestinations</code>.</p></description>
<example></example>
<return-type>bool</return-type>
</documentation>
*/
    public function getAccessDestinations() : bool
    {
        return $this->access_destinations;
    }

/**
<documentation><description><p>Sets <code>accessConnectors</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessConnectors( bool $bool ) : Property
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->access_connectors = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>accessDestinations</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setAccessDestinations( bool $bool ) : Property
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->access_destinations = $bool;
        return $this;
    }

/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj = parent::toStdClass();
        $obj->accessDestinations   = $this->access_destinations;
        $obj->accessConnectors     = $this->access_connectors;
        
        return $obj;
    }
    
    private $access_destinations;
    private $access_connectors;
}
?>