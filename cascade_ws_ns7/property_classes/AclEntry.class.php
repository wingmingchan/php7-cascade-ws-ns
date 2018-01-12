<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
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
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>An <code>AclEntry</code> object represents an <code>aclEntry</code> property found in an <code>AccessRightsInformation</code> object.</p>
<h2>Structure of <code>aclEntry</code></h2>
<pre>aclEntry
  level
  type
  name
</pre>
<ul>
<li>level: \"read\" or \"write\"</li>
<li>type: \"group\" or \"user\"</li>
<li>name: name of the group or user</li>
</ul>
<p>Example:</p>
<pre>[\"aclEntry\"]=&gt;
    array(3) {
      [0]=&gt;
      object(stdClass)#70 (3) {
        [\"level\"]=&gt;
        string(4) \"read\"
        [\"type\"]=&gt;
        string(5) \"group\"
        [\"name\"]=&gt;
        string(13) \"CWT-Designers\"
      }
      [1]=&gt;
      object(stdClass)#71 (3) {
        [\"level\"]=&gt;
        string(5) \"write\"
        [\"type\"]=&gt;
        string(4) \"user\"
        [\"name\"]=&gt;
        string(10) \"chanw-test\"
      }
      [2]=&gt;
      object(stdClass)#68 (3) {
        [\"level\"]=&gt;
        string(5) \"write\"
        [\"type\"]=&gt;
        string(4) \"user\"
        [\"name\"]=&gt;
        string(5) \"chanw\"
      }
    }
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "acl-entries" ),
        array( "getComplexTypeXMLByName" => "aclEntry" ),
        array( "getSimpleTypeXMLByName"  => "acl-entry-level" ),
        array( "getSimpleTypeXMLByName"  => "acl-entry-type" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class AclEntry extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        \stdClass $ae=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $ae ) )
        {
            if( isset( $ae->level ) )
                $this->level = $ae->level;
            if( isset( $ae->type ) )
                $this->type  = $ae->type;
            if( isset( $ae->name ) )
                $this->name  = $ae->name;
        }
    }
    
/**
<documentation><description><p>Display some information and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function display()
    {
        echo "Level: " . $this->level . BR .
             "Type: "  . $this->type . BR .
             "Name: "  . $this->name . BR . BR;
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>level</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLevel()
    {
        return $this->level;
    }
    
/**
<documentation><description><p>Returns <code>name</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getName()
    {
        return $this->name;
    }
    
/**
<documentation><description><p>Returns <code>type</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getType()
    {
        return $this->type;
    }
    
/**
<documentation><description><p>Sets <code>level</code> and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setLevel( $level )
    {
        if( !c\LevelValues::isLevel( $level ) )
            throw new e\UnacceptableValueException( "The level $level is unacceptable." );
            
        $this->level = $level;
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
        $obj->level = $this->level;
        $obj->type  = $this->type;
        $obj->name  = $this->name;
        return $obj;
    }
    
    private $level;
    private $type;
    private $name;
}
?>