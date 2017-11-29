<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 11/28/2017 Class created.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>The <code>ContainedAsset</code> class is an abstract sub-class of <code>Asset</code> and the superclass of all asset classes except the following three classes:</p>
<ul>
<li><code>Group</code></li>
<li><code>Role</code></li>
<li><code>User</code></li>
</ul>
<p>These three classes do not have parent containers. Note that although <code>Site</code> is a sub-class of <code>ScheduledPublishing</code>,
which is a sub-class of <code>ContainedAsset</code>, and hence inherits all methods defined in <code>ContainedAsset</code>, a site does not have a parent container.
Calling any method defined in <code>ContainedAsset</code> on a <code>Site</code> object will cause an exception to be thrown from <code>ContainedAsset</code>.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "folder-contained-asset" ),
        array( "getComplexTypeXMLByName" => "containered-asset" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/contained_asset.php">contained_asset.php</a></li></ul></postscript>
</documentation>
*/
abstract class DublinAwareAsset extends FolderContainedAsset
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
        
        if( $this->getType() != Page::TYPE )
            $this->metadata_set = new MetadataSet( 
                $service, 
                $service->createId( MetadataSet::TYPE, 
                    $this->getProperty()->metadataSetId ) );
    }
/**
<documentation><description><p>Returns the <code>MetadataSet</code> object.</p></description>
<example>$p->getMetadataSet()->display();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/

    public function getMetadataSet() : Asset
    {
        return $this->metadata_set;
    }
  
/**
<documentation><description><p>Returns the ID of the metadata set. This method overrides
the parent method because a page does not store the ID of the metadata set. The
information must be retrieved through the associated content type object.</p></description>
<example>echo $p->getMetadataSetId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/

    public function getMetadataSetId() : string
    {
        return $this->metadata_set->getId();
    }
   
/**
<documentation><description><p>Returns the path of the metadata set. This method overrides
the parent method because a page does not store the path of the metadata set. The
information must be retrieved through the associated content type object.</p></description>
<example>echo $p->getMetadataSetPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/

    public function getMetadataSetPath() : string
    {
        return $this->metadata_set->getPath();
    }

/**
<documentation><description><p>Returns <code>reviewOnSchedule</code>.</p></description>
<example>echo u\StringUtility::boolToString( $page->getReviewOnSchedule() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getReviewOnSchedule() : bool
    {
        return $this->getProperty()->reviewOnSchedule;
    }
    
/**
<documentation><description><p>Returns <code>reviewEvery</code>.</p></description>
<example>echo $page->getReviewEvery(), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getReviewEvery() : int
    {
        $this->getProperty()->reviewEvery;
    }
    
/**
<documentation><description><p>Returns <code>reviewOnSchedule</code>.</p></description>
<example>echo u\StringUtility::boolToString( $page->getReviewOnSchedule() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function setReviewOnSchedule( bool $bool ) : Asset
    {
        $this->getProperty()->reviewOnSchedule = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>reviewEvery</code>.</p></description>
<example>echo $page->getReviewEvery(), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function setReviewEvery( int $days=30 ) : Asset
    {
    /*
    	if( $days != 0 && $days != 30 && $days != 90 && $days != 180 && $days != 365 )
    		throw new e\UnacceptableValueException( 
                S_SPAN . "The value $days must be 0, 30, 90, 180, or 365." . E_SPAN );
    */
    	if( $days != 0 )
        	$this->getProperty()->reviewOnSchedule = true;
        $this->getProperty()->reviewEvery = $days;
        return $this;
    }
    
    private $metadata_set;
}
?>