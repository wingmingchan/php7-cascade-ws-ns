<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 11/2/2018 Class created.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>SharedField</code> represents a shared field asset.</p>
<h2>Structure of <code>sharedField</code></h2>
<pre>sharedField
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  xml
</pre>

<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "sharedField" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/sharedField.php">shared_field.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/sharedfield/fd27a12d8b7f08560159f3f087ef9165

{
  "asset":
  {
    "sharedField":
    {
      "xml":"\u003csystem-data-structure\u003e\n\t\u003cgroup identifier\u003d\"text-group\" label\u003d\"Text Group\" multiple\u003d\"true\"\u003e\n\t\t\u003ctext identifier\u003d\"text\"/\u003e\n\t\u003c/group\u003e\n\u003c/system-data-structure\u003e",
      "parentContainerId":"580ec1ebac1e001b1730aec6861b78c8",
      "parentContainerPath":"/",
      "path":"text-group",
      "siteId":"f7a963087f0000012693e3d9932e44ba",
      "siteName":"upstate",
      "name":"text-group",
      "id":"cf4cc0adac1e001b36b86cda3fb840cf"
    }
  },
  "success":true
}
</pre>
</postscript>
</documentation>
*/
class SharedField extends ContainedAsset
{
    const DEBUG     = false;
    const TYPE      = c\T::SHAREDFIELD;
    const DELIMITER = ';';

/**
<documentation><description><p>The constructor, overriding the parent method to parse and
process the definition XML.</p></description>
<example></example>
<return-type></return-type>
<exception>MissingDefaultValueException</exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        $this->xml             = $this->getProperty()->xml;
    }
    
/**
<documentation><description><p>Displays <code>xml</code> and the attributes
array, and returns the calling object.</p></description>
<example>$dd->display();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function display() : Asset
    {
        $xml_string = u\XMLUtility::replaceBrackets( $this->xml );
        
        echo S_H2 . "XML" . E_H2 .
             S_PRE . $xml_string . E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p>Displays <code>xml</code> and returns the calling object.
The flag <code>$formatted</code> controls whether the XML should be formatted for HTML output.</p></description>
<example>$dd->displayXML();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayXml( bool $formatted=true ) : Asset
    {
        if( $formatted )
        {
            $xml_string = u\XMLUtility::replaceBrackets( $this->xml );
            echo S_H2 . "XML" . E_H2 . S_PRE;
        }

        echo $xml_string;
        
        if( $formatted )
             echo E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
</documentation>
*/
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $asset = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException(
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        return $this->reloadProperty();
    }

/**
<documentation><description><p>Returns <code>xml</code>.</p></description>
<example>$xml = $dd->getXml();</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getXml() : string
    {
        return $this->xml;
    }
    
    private $xml; // the definition xml
}
?>