<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 10/10/2018 Bug fixes in static methods.
  * 11/28/2017 Changed parent class to FolderContainedAsset.
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
<description><h2>Introduction</h2>
<p>The <code>Format</code> class is the superclass of <code>ScriptFormat</code> and <code>XsltFormat</code>. It is an abstract class and defines all methods shared by the two sub-classes.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/format.php">format.php</a></li>
</ul></postscript>
</documentation>
*/
abstract class Format extends FolderContainedAsset
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
<example>$f->setXML( $xml )->edit();</example>
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
    
/**
<documentation><description><p>Returns <code>createdBy</code>.</p></description>
<example>echo "getCreatedBy: ", $f->getCreatedBy(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedBy() : string
    {
        return $this->getProperty()->createdBy;
    }
    
/**
<documentation><description><p>Returns <code>createdDate</code>.</p></description>
<example>echo "getCreatedDate: ", $f->getCreatedDate(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedDate() : string
    {
        return $this->getProperty()->createdDate;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedBy</code>.</p></description>
<example>echo "getLastModifiedBy: ", $f->getLastModifiedBy(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedBy() : string
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedDate</code>.</p></description>
<example>echo "getLastModifiedDate: ", $f->getLastModifiedDate(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p>Returns a <code>Format</code> object bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a format. This methods only works for SOAP.</p></description>
<example>$f = a\Format::getFormat( $service, "9fea0fa68b7ffe83164c9314f20b318a" );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public static function getFormat(
        aohs\AssetOperationHandlerService $service,
        string $id_string )
    {
        if( $service->isSoap() )
            return self::getAsset( $service, 
                self::getFormatType( $service, $id_string ), $id_string );
        return NULL;
    }

/**
<documentation><description><p>Returns the type of the format bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a format. This methods only works for SOAP.</p></description>
<example>echo a\Format::getFormatType( $service, "9fea17498b7ffe83164c931447df1bfb" );</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getFormatType(
        aohs\AssetOperationHandlerService $service,
        string $id_string ) : string
    {
        if( $service->isSoap() )
        {
            $types      
                = array( ScriptFormat::TYPE, XsltFormat::TYPE );
            $type_count = count( $types );
        
            for( $i = 0; $i < $type_count; $i++ )
            {
                $id = $service->createId( $types[ $i ], $id_string );
                $operation = new \stdClass();
                $read_op   = new \stdClass();
    
                $read_op->identifier = $id;
                $operation->read     = $read_op;
                $operations[]        = $operation;
            }
        
            $service->batch( $operations );
        
            $reply_array = $service->getReply()->batchReturn;
        
            for( $j = 0; $j < $type_count; $j++ )
            {
                if( $reply_array[ $j ]->readResult->success == 'true' )
                {
                    foreach( c\T::$type_property_name_map as $type => $property )
                    {
                        if( isset( $reply_array[ $j ]->readResult->asset->$property ) )
                            return $type;
                    }
                }
            }
        }
        return NULL;
    }
}
?>