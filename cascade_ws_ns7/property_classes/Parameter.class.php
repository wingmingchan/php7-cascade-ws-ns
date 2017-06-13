<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/13/2017 Added WSDL.
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
<p>A <code>Parameter</code> object represents a <code>parameter</code> property found in a <a href="/web-services/api/property-classes/plugin"><code>Plugin</code></a> object in an <a href="web-services/api/asset-classes/asset-factory"><code>a\AssetFactory</code></a> object.</p>
<h2>Structure of <code>parameter</code></h2>
<pre>parameter
  name
  value
</pre>
<p>WSDL:</p>
<pre>&lt;complexType name="asset-factory-plugin-parameters">
  &lt;sequence>
    &lt;element maxOccurs="unbounded" minOccurs="0" name="parameter" type="impl:asset-factory-plugin-parameter"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="asset-factory-plugin-parameter">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="name" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="value" type="xsd:string"/>
  &lt;/sequence>
&lt;/complexType>
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class Parameter extends Property
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
            $this->name  = $p->name;
            $this->value = $p->value;
        }
    }
    
/**
<documentation><description><p>Returns <code>name</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getName() : string
    {
        return $this->name;
    }
    
/**
<documentation><description><p>Returns <code>value</code>.</p></description>
<example></example>
<return-type>string</return-type>
</documentation>
*/
    public function getValue() : string
    {
        return $this->value;
    }
    
/**
<documentation><description><p>Sets <code>value</code> and returns the calling object.</p></description>
<example></example>
<return-type>Property</return-type>
</documentation>
*/
    public function setValue( string $value ) : Property
    {
        $this->value = $value;
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
        $obj        = new \stdClass();
        $obj->name  = $this->name;
        $obj->value = $this->value;
        return $obj;
    }

    private $name;
    private $value;
}
?>
