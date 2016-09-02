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

abstract class Format extends ContainedAsset
{
    const DEBUG = false;

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
    
    public function getCreatedBy()
    {
        return $this->getProperty()->createdBy;
    }
    
    public function getCreatedDate()
    {
        return $this->getProperty()->createdDate;
    }
    
    public function getLastModifiedBy()
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
    public function getLastModifiedDate()
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
    public static function getFormat( $service, $id_string )
    {
        return self::getAsset( $service, 
            self::getFormatType( $service, $id_string ), $id_string );
    }

    public static function getFormatType( $service, $id_string )
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
