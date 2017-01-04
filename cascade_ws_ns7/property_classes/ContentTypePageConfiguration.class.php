<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
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
<p>A <code>ContentTypePageConfiguration</code> object represents a <code>contentTypePageConfiguration</code> property found in a <a href="web-services/api/asset-classes/content-type"><code>ContentType</code></a> object.</p>
<h2>Structure of <code>ContentTypePageConfiguration</code></h2>
<pre>ContentTypePageConfiguration
  pageConfigurationId
  pageConfigurationName
  publishMode
  destinations
</pre>
<h2>Design Issues</h2>
<p>Currently this class cannot be used to modify the destinations.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class ContentTypePageConfiguration extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct(
        \stdClass $ctpc=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $ctpc ) )
        {
            $this->page_configuration_id   = $ctpc->pageConfigurationId;
            $this->page_configuration_name = $ctpc->pageConfigurationName;
            $this->publish_mode            = $ctpc->publishMode;
            $this->destinations            = $ctpc->destinations;
        }
    }
    
/**
<documentation><description><p>Displays some information of the object for debugging purposes and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function display()
    {
        echo $this->page_configuration_name . ": " . $this->publish_mode . BR;
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>destinations</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDestinations()
    {
        return $this->destinations;
    }
    
/**
<documentation><description><p>Returns <code>pageConfigurationId</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationId()
    {
        return $this->page_configuration_id;
    }
    
/**
<documentation><description><p>Returns <code>pageConfigurationName</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationName()
    {
        return $this->page_configuration_name;
    }
    
/**
<documentation><description><p>Returns <code>publishMode</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPublishMode() : string
    {
        return $this->publish_mode;
    }
    
/**
<documentation><description><p>Sets <code>publishMode</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPublishMode( string $mode )
    {
        if( $mode != a\ContentType::PUBLISH_MODE_ALL_DESTINATIONS && 
            $mode != a\ContentType::PUBLISH_MODE_DO_NOT_PUBLISH )
        {
            throw new \Exception( "The mode $mode is not supported." );
        }
        $this->publish_mode = $mode;
        
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
        $obj = new \stdClass();
        $obj->pageConfigurationId = $this->page_configuration_id;
        $obj->pageConfigurationName = $this->page_configuration_name;
        $obj->publishMode = $this->publish_mode;
        $obj->destinations = $this->destinations;
        
        return $obj;
    }
    
    private $page_configuration_id;
    private $page_configuration_name;
    private $publish_mode;
    private $destinations;
}
?>