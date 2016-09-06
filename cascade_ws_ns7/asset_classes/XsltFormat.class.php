<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 8/12/2014 Used SimpleXMLElement in setXML.
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
class XsltFormat extends Format
{
    const DEBUG = false;
    const TYPE  = c\T::XSLTFORMAT;
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function displayXml()
    {
        $xml_string = htmlentities( $this->getProperty()->xml ); // &
        $xml_string = u\XMLUtility::replaceBrackets( $xml_string );
        
        echo S_H2 . "XML" . E_H2 .
             S_PRE . $xml_string . E_PRE . HR;
        
        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getXml()
    {
        return $this->getProperty()->xml;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setXml( $xml, $enforce_xml=false )
    {
        if( trim( $xml ) == '' )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );
        }
        if( $enforce_xml )
        {
            $xml_obj = new \SimpleXMLElement( $xml );
            $this->getProperty()->xml = $xml_obj->asXML();
        }
        else
        {
            $this->getProperty()->xml = $xml;
        }
        return $this;
    }
}
?>