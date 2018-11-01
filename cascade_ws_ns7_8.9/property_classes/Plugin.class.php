<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/29/2017 Update REST code.
  * 12/26/2017 Changed toStdClass so that it works with REST.
  * 7/14/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
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
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>Plugin</code> object represents a <code>plugin</code> property found in an <a href=\"http://www.upstate.edu/web-services/api/asset-classes/asset-factory.php\"><code>a\AssetFactory</code></a> object.</p>
<h2>Structure of <code>plugin</code></h2>
<pre>plugin
  name
  parameters
    parameter
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "asset-factory-plugins" ),
        array( "getComplexTypeXMLByName" => "asset-factory-plugin" ),
        array( "getComplexTypeXMLByName" => "asset-factory-plugin-parameters" ),
        array( "getComplexTypeXMLByName" => "asset-factory-plugin-parameter" ),
    ) );
return $doc_string;
?>
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
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $p=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        $this->service = $service;
        
        if( isset( $p ) )
        {
            if( isset( $p->name ) )
                $this->name  = $p->name;
            
            if( $this->service->isSoap() && isset( $p->parameters->parameter ) )
                $this->processParameters( $p->parameters->parameter );
            elseif( $this->service->isRest() )
                $this->processParameters( $p->parameters );
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
<documentation><description><p>Returns the <code>Parameter</code> object bearing that name.</p></description>
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
        
        if( $this->service->isSoap() )
            $obj->parameters = new \stdClass();
        elseif( $this->service->isRest() )
            $obj->parameters = array();
        
        if( $count == 0 )
        {
            // nothing
        }
        else if( $count == 1 )
        {
            if( $this->service->isSoap() )
                $obj->parameters->parameter = $this->parameters[ 0 ];
            elseif( $this->service->isRest() )
            {
                $param_std = $this->parameters[ 0 ]->toStdClass();
                
                if( isset( $param_std->name ) && isset( $param_std->value ) )
                    $obj->parameters = array( $param_std );
            }
        }
        else
        {
            if( $this->service->isSoap() )
                $obj->parameters->parameter = array();
            
            foreach( $this->parameters as $parameter )
            {
                if( $this->service->isSoap() )
                    $obj->parameters->parameter[] = $parameter->toStdClass();
                elseif( $this->service->isRest() )
                {
                    $param_std = $parameter->toStdClass();
                    
                    if( isset( $param_std->name ) && isset( $param_std->value ) )
                        $obj->parameters[] = $param_std;
                }
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
            if( $this->service->isSoap() )
                $this->parameters[] = new Parameter( $parameter );
            elseif( $this->service->isRest() &&
                isset( $parameter->name ) && isset( $parameter->value ) )
                    $this->parameters[] = new Parameter( $parameter );
        }
    }

    private $name;
    private $parameters;
    private $parameter;
    private $service;
}
?>