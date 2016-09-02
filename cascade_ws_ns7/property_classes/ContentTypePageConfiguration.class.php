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
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_asset as a;

class ContentTypePageConfiguration extends Property
{
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
    
    public function display()
    {
        echo $this->page_configuration_name . ": " . $this->publish_mode . BR;
        return $this;
    }
    
    public function getDestinations()
    {
        return $this->destinations;
    }
    
    public function getPageConfigurationId()
    {
        return $this->page_configuration_id;
    }
    
    public function getPageConfigurationName()
    {
        return $this->page_configuration_name;
    }
    
    public function getPublishMode()
    {
        return $this->publish_mode;
    }
    
    public function setPublishMode( $mode )
    {
        if( $mode != a\ContentType::PUBLISH_MODE_ALL_DESTINATIONS && 
            $mode != a\ContentType::PUBLISH_MODE_DO_NOT_PUBLISH )
        {
            throw new \Exception( "The mode $mode is not supported." );
        }
        $this->publish_mode = $mode;
        
        return $this;
    }
    
    public function toStdClass()
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