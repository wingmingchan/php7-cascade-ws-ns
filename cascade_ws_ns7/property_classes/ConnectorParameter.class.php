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

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p><code>ConnectorParameter</code> is an empty sub-class of <a href=\"http://www.upstate.edu/web-services/api/property-classes/parameter.php\"><code>Parameter</code></a> used by <a href=\"http://www.upstate.edu/web-services/api/asset-classes/connector.php\"><code>Connector</code></a> and its sub-classes.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "connector-parameter-list" ),
        array( "getComplexTypeXMLByName" => "connector-parameter" ),
    ) );
return $doc_string;
?>
</description>
</documentation>
*/
class ConnectorParameter extends Parameter{}
?>