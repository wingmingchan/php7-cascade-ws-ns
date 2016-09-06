<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class WordPressConnector extends Connector
{
    const DEBUG      = false;
    const TYPE       = c\T::WORDPRESSCONNECTOR;
    const CATEGORIES = "Metadata mapping for categories";
    const TAGS       = "Metadata mapping for tags";
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setAuth1( $value )
    {
        $this->getProperty()->auth1 = $value;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setAuth2( $value )
    {
        $this->getProperty()->auth2 = $value;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setMetadataMapping( ContentType $ct, $name, $value )
    {
        if( $ct == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_CONTENT_TYPE . E_SPAN );
            
        if( $name != self::TAGS && $name != self::CATEGORIES )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The name $name is not acceptable." . E_SPAN );
            
        $links = $this->getConnectorContentTypeLinks();
        
        foreach( $links as $link )
        {
            if( $link->getContentTypeId() == $ct->getId() )
            {
                $link->setMetadataMapping( $name, $value );
                return $this;
            }
        }
        
        throw new \Exception( 
            S_SPAN . "The content does not exist in the connector." . E_SPAN );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setUrl( $u )
    {
        if( trim( $u ) == "" )
            throw e\EmptyValueException(
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
            
        $this->getProperty()->url = $u;
        return $this;
    }
}
?>
