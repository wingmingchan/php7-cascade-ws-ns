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
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>The <code>Format</code> class is the superclass of <code>ScriptFormat</code> and <code>XsltFormat</code>. It is an abstract class and defines all methods shared by the two sub-classes.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li>
</ul></postscript>
</documentation>
*/
abstract class Format extends ContainedAsset
{
    const DEBUG = false;

/**
<documentation><description><p>Eedits and returns the calling object.</p></description>
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
<example></example>
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
<example></example>
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
<example></example>
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
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p>Returns a <code>Format</code> object bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a format.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getFormat(
        aohs\AssetOperationHandlerService $service,
        string $id_string ) : Asset
    {
        return self::getAsset( $service, 
            self::getFormatType( $service, $id_string ), $id_string );
    }

/**
<documentation><description><p>Returns the type of the format bearing the ID. The <code>$id_string</code> must be a 32-digit hex string of a format.</p></description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public static function getFormatType(
        aohs\AssetOperationHandlerService $service,
        string $id_string ) : string
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
        
        return "The id does not match any asset type.";
    }
}
?>