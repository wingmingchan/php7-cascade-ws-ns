<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/11/2016 Throwing exception from getParameter. Added addParameter and removeParameter.
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
<p>A <code>Plugin</code> object represents a <code>plugin</code> property found in an <a href="web-services/api/asset-classes/asset-factory"><code>a\AssetFactory</code></a> object.</p>
<h2>Structure of <code>plugin</code></h2>
<pre>plugin
  name
  parameters
    parameter
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class Plugin extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example>// retrieve the asset factory
$af = $cascade->getAsset(
		a\AssetFactory::TYPE, "1f2179f08b7ffe834c5fe91e7eb59cc8" );
// get the plugin
$plugin = $af->getPlugin( "com.cms.assetfactory.FileLimitPlugin" );
	</example>
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
            
            if( isset( $p->parameters->parameter ) )
            {
                $this->processParameters( $p->parameters->parameter );
            }
        }
    }
    
/**
<documentation><description><p>Adds the named parameter with the supplied value.</p></description>
<example>$plugin->addParameter( $param->getName(),
    $param->getValue() );
$af->edit();</example>
<return-type>Property</return-type>
<exception>NoSuchPluginParameterException</exception>
</documentation>
*/
    public function addParameter(
        string $name, string $value ) : Property
    {
        if( $value.trim( " " ) == "" )
            $value = "1"; // there must be value
        
        if( !in_array( $name, a\AssetFactory::$plugin_name_param_map[ $this->name ] ) )
            throw new e\NoSuchPluginParameterException(
                S_SPAN . "The parameter $name does not exist." . E_SPAN
            );
            
        if( !$this->hasParameter( $name ) )
        {
            $param_std = new \stdClass();
            $param_std->name  = $name;
            $param_std->value = $value;
            $this->parameters[] = new Parameter( $param_std );
        }
        else    
            $this->setParameterValue( $name, $value );
            
        return $this;
    }
    
/**
<documentation><description><p>Returns the name.</p></description>
<example>echo $plugin->getName(), BR;</example>
<return-type>string</return-type>
</documentation>
*/
    public function getName() : string
    {
        return $this->name;
    }

/**
<documentation><description>Returns the <code>Parameter</code> object bearing that name.</p></description>
<example>if( $plugin->hasParameter( $param_name ) )
{
    $param = $plugin->getParameter( $param_name );
    u\DebugUtility::dump( $param );
}
</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getParameter( string $name )
    {
        if( !$this->hasParameter( $name ) )
        {
        	throw new e\NoSuchPluginParameterException(
            	S_SPAN . "The parameter $name does not exist." .
            	E_SPAN );
        }
        return $this->parameter;
    }
    
/**
<documentation><description><p>Returns NULL, a <code>Parameter</code> object, or an array of <code>Parameter</code> objects.</p></description>
<example>u\DebugUtility::dump( $plugin->getParameters() );</example>
<return-type>mixed</return-type>
</documentation>
*/
    public function getParameters()
    {
        return $this->parameters;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named parameter exists.</p></description>
<example>echo u\StringUtility::boolToString(
    $plugin->hasParameter( "assetfactory.plugin.filelimit" .
        ".param.name.filenameregex" ) ), BR;</example>
<return-type>bool</return-type>
</documentation>
*/
    public function hasParameter( string $name ) : bool
    {
        if( count( $this->parameters ) > 0 )
        {
            foreach( $this->parameters as $parameter )
            {
                if( $parameter->getName() == $name )
                {
                	$this->parameter = $parameter;
                    return true;
                }
                else
                {
                	$this->parameter = NULL;
                }
            }
        }
        
        return false;
    }
    
/**
<documentation><description><p>Removes the named parameter and returns the calling object.</p></description>
<example>$plugin->removeParameter( $param_name );
$af->edit();</example>
<return-type>Property</return-type>
<exception>NoSuchPluginParameterException</exception>
</documentation>
*/
    public function removeParameter( string $name ) : Property
    {
        if( !in_array( $name, a\AssetFactory::$plugin_name_param_map[ $this->name ] ) )
            throw new e\NoSuchPluginParameterException(
                S_SPAN . "The parameter $name does not exist." . E_SPAN
            );
            
        if( count( $this->parameters ) > 0 )
        {
            $temp = array();
            
            foreach( $this->parameters as $parameter )
            {
                if( $parameter->getName() != $name )
                {
                    $temp[] = $parameter;
                }
            }
            
            $this->parameters = $temp;
        }
        return $this;
    }
    
/**
<documentation><description><p>Sets the value of the parameter bearing that name and returns the calling object.</p></description>
<example>$plugin->setParameterValue( $param_name, "150" );
$af->edit();</example>
<return-type>Property</return-type>
<exception>NoSuchPluginParameterException</exception>
</documentation>
*/
    public function setParameterValue( string $name, string $value ) : Property
    {
        $parameter = $this->getParameter( $name );
        $parameter->setValue( $value );
        
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
        $obj       = new \stdClass();
        $obj->name = $this->name;
        $count     = count( $this->parameters );
        
        $obj->parameters = new \stdClass();
        
        if( $count == 0 )
        {
            // nothing
        }
        else if( $count == 1 )
        {
            $obj->parameters->parameter = $this->parameters[0];
        }
        else
        {
            $obj->parameters->parameter = array();
            
            foreach( $this->parameters as $parameter )
            {
                $obj->parameters->parameter[] = $parameter->toStdClass();
            }
        }
        
        return $obj;
    }
    
    private function processParameters( $parameters )
    {
        $this->parameters = array();

        if( !is_array( $parameters ) )
        {
            $parameters = array( $parameters );
        }
        foreach( $parameters as $parameter )
        {
            $this->parameters[] = new Parameter( $parameter );
        }
    }

    private $name;
    private $parameters;
    private $parameter;
}
?>