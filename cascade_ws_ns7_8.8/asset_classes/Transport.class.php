<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
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
<p>The <code>Transport</code> class is the superclass of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/database-transport.php\"><code>DatabaseTransport</code></a>, <a href=\"http://www.upstate.edu/web-services/api/asset-classes/file-system-transport.php\"><code>FileSystemTransport</code></a>, and <a href=\"http://www.upstate.edu/web-services/api/asset-classes/ftp-transport.php\"><code>FtpTransport</code></a>. It is an abstract class and defines all shared methods of its sub-classes.</p><h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getSimpleTypeXMLByName"  => "authMode" ),
        array( "getSimpleTypeXMLByName"  => "ftpProtocolType" ),
        array( "getComplexTypeXMLByName" => "entity-type" ),
    ) );
return $doc_string;
?></description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/ftp_transport.php">ftp_transport.php</a></li>
</ul></postscript>
</documentation>
*/
abstract class Transport extends ContainedAsset
{
    const DEBUG = false;

/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    protected function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
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
        $asset                                    = new \stdClass();
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
}
?>
