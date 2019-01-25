<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/25/2019 Moved staticUpdateData to Asset.
  * 1/23/2019 Added updateMetadata, updateData, update, and related static methods.
  * 12/15/2017 Updated documentation.
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
<p>The <code>DublinAwareAsset</code> class is an abstract sub-class of
<code>FolderContainedAsset</code> and the superclass of all asset classes
representing assets that can be associated with a metadata set.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "dublin-aware-asset" ),
        array( "getComplexTypeXMLByName" => "folder-contained-asset" ),
        array( "getComplexTypeXMLByName" => "containered-asset" ),
    ) );
return $doc_string;
?>
</description>
</documentation>
*/
abstract class DublinAwareAsset extends FolderContainedAsset
{
    const AUTHOR           = "author";
    const DISPLAY_NAME     = "displayName";
    const END_DATE         = "endDate";
    const KEYWORDS         = "keywords";
    const METADATA         = "metadata";
    const META_DESCRIPTION = "metaDescription";
    const REVIEW_DATE      = "reviewDate";
    const START_DATE       = "startDate";
    const SUMMARY          = "summary";
    const TEASER           = "teaser";
    const TITLE            = "title";
    const DEBUG            = false;

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
    
    public function testMethod( string $method_name ) : bool
    {
        if( method_exists( $this, $method_name ) )
            return true;
        return false;
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
<documentation><description><p>Sets <code>reviewEvery</code> and returns the calling object.</p></description>
<example>$page->setReviewEvery( 90 )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setReviewEvery( int $days=0 ) : Asset
    {
        if( $days != 0 && $days != 30 && $days != 90 && $days != 180 && $days != 365 )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $days must be 0, 30, 90, 180, or 365." . E_SPAN );

        if( $days != 0 )
            $this->getProperty()->reviewOnSchedule = true;
        $this->getProperty()->reviewEvery = $days;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>reviewOnSchedule</code> and returns the calling object.</p></description>
<example>$page->setReviewOnSchedule( true )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setReviewOnSchedule( bool $bool ) : Asset
    {
        $this->getProperty()->reviewOnSchedule = $bool;
        return $this;
    }

/**
<documentation><description><p>Updates both the data and the metadata by calling
both <code>staticUpdateMetadata</code> and <code>staticUpdateData</code>, and returns the calling
object.</p>
</description>
<example>    $page->update(
        array(
            a\DublinAwareAsset::METADATA => array(
                // wired fields
                a\DublinAwareAsset::AUTHOR       => "Wing",
                a\DublinAwareAsset::DISPLAY_NAME => "Struts 2 in Action",
                a\DublinAwareAsset::KEYWORDS     => "",
                a\DublinAwareAsset::SUMMARY      => "Struts 2 in Action",
                // dynamic fields
                "exclude-from-menu"              => NULL,
                "tree-picker"                    => array( "inherited" )
            ),
            // page settings
            a\DublinAwareAsset::SHOULD_BE_PUBLISHED     => false,
            a\DublinAwareAsset::SHOULD_BE_INDEXED       => false,
            a\DublinAwareAsset::MAINTAIN_ABSOLUTE_LINKS => true,
            // structured data nodes
            "main-group;h1"                   => "Struts 2 in Action",
            "main-group;mul-pre-h1-chooser;0" => NULL // remove block
        )
    );</example>
<return-type>Asset</return-type>
<exception>RequiredFieldException, NoSuchValueException</exception>
</documentation>
*/
    public function update( array $params )
    {
    	self::staticUpdateMetadata( $this, $params );
    	self::staticUpdateData( $this, $params );
    	return $this;
    }

/**
<documentation><description><p>Updates the metadata by calling <code>staticUpdateMetadata</code>,
and returns the calling object.
The array passed in should contain a key whose value is <code>metadata</code>,
and whose value is an array, containing either names of wired fields or of dynamic fields
as keys, and strings as values for wired fields, and arrays of strings for dynamic fields.
If a dynamic field can accept a NULL, then pass in a NULL for that entry. Note that
this method calls <code>setX</code> for wired fields and <code>setDynamicFieldValue</code>
for dynamic fields, exceptions can be thrown from these called methods.</p>
<p class="text_red"><strong>Important:</strong> All entries must be inside a sub-array
referenced by the key <code>metadata</code>.</p>
<p>To make this method more efficient, a second bool parameter can be passed in.
The value is defaulted to <code>true</code>, and the <code>edit</code> method will
be called. But if <code>edit</code> will be called later, then a <code>false</code>
can be passed into so that no extra <code>edit</code> is called.</p>
</description>
<example>    $page->updateMetadata(
        array(
            a\DublinAwareAsset::METADATA => array(
                // wired fields
                a\DublinAwareAsset::AUTHOR      => "Wing Ming Chan",
                a\DublinAwareAsset::DISPLAYNAME => "New Page",
                a\DublinAwareAsset::SUMMARY     => "New Page",
                // dynamic fields
                "exclude-from-menu"             => NULL,
                "tree-picker"                   => array( "inherited" )
            )
        )
    );</example>
<return-type>Asset</return-type>
<exception>RequiredFieldException, NoSuchValueException</exception>
</documentation>
*/
    public function updateMetadata( array $params, bool $commit=true )
    {
    	self::staticUpdateMetadata( $this, $params, $commit );
    	return $this;
    }

/**
<documentation><description><p>Updates the metadata. This <code>static</code> method
is used by the instance <code>update</code> method.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public static function staticUpdateMetadata(
        Asset $a, array $params, bool $commit=true ) : Asset
    {
        $metadata = $a->getMetadata();
        
        if( isset( $params[ DublinAwareAsset::METADATA ] ) )
        {
            $metadata_params = $params[ DublinAwareAsset::METADATA ];
            
            foreach( $params[ DublinAwareAsset::METADATA ] as $key => $value )
            {
                // wired fields
                $method_name = "set" . ucwords( $key );
                
                if( method_exists( $metadata, $method_name ) )
                {
                    $metadata->$method_name( $value );
                }
                else // dynamic fields, only accept array values
                {
                    $metadata->setDynamicFieldValue( $key, $value );
                }
            }
            
            if( $commit )
            {
                $a->edit();
            }
        }
    
        return $a;
    }
    
    private $metadata_set;
}
?>