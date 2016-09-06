<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/4/2016 Fixed a bug in publish.
  * 10/30/2015 Modified edit to accept workflows, and added unpublish.
  * 5/28/2015 Added namespaces.
  * 10/1/2014 Added getWorkflow.
  * 7/22/2014 Added isPublishable.
  * 7/17/2014 Fixed a bug in setText.
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
class File extends Linkable
{
    const DEBUG = false;
    const TYPE  = c\T::FILE;
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
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
        
        if( isset( $wd ) )
        {
            $wf_config                       = new \stdClass();
            $wf_config->workflowDefinitionId = $wd->getId();
            $wf_config->workflowComments     = $comment;
            
            if( isset( $wf ) )
            {
                $wf_config->workflowName     = $wf->getName();
            }
            else
            {
                if( trim( $new_workflow_name ) == "" )
                    throw new e\EmptyValueException( c\M::EMPTY_WORKFLOW_NAME );
                    
                $wf_config->workflowName     = $new_workflow_name;
            }
            
            $asset->workflowConfiguration    = $wf_config;
        }

        $this->getProperty()->metadata  = $this->getMetadata()->toStdClass();
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
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getData()
    {
        return $this->getProperty()->data;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedBy()
    {
        return $this->getProperty()->lastPublishedBy;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getLastPublishedDate()
    {
        return $this->getProperty()->lastPublishedDate;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getMaintainAbsoluteLinks()
    {
        return $this->getProperty()->maintainAbsoluteLinks;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRewriteLinks()
    {
        return $this->getProperty()->rewriteLinks;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBeIndexed()
    {
        return $this->getProperty()->shouldBeIndexed;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getShouldBePublished()
    {
        return $this->getProperty()->shouldBePublished;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getText()
    {
        return $this->getProperty()->text;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getWorkflow()
    {
        $service = $this->getService();
        $service->readWorkflowInformation( $service->createId( self::TYPE, $this->getProperty()->id ) );
        
        if( $service->isSuccessful() )
        {
            if( isset( $service->getReply()->readWorkflowInformationReturn->workflow ) )
                return new p\Workflow( 
                    $service->getReply()->readWorkflowInformationReturn->workflow, $service );
            else
                return NULL; // no workflow
        }
        else
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::READ_WORKFLOW_FAILURE . E_SPAN );
        }
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function isPublishable()
    {
        $parent = $this->getAsset( $this->getService(), Folder::TYPE, $this->getParentContainerId() );
        return $parent->isPublishable() && $this->getShouldBePublished();
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL )
    {
        if( isset( $destination ) )
        {
            $destination_std           = new \stdClass();
            $destination_std->id       = $destination->getId();
            $destination_std->type     = $destination->getType();
        }
        
        if( $this->getProperty()->shouldBePublished )
        {
            $service = $this->getService();
            
            if( isset( $destination ) )
                $service->publish( 
                    $service->createId( $this->getType(), $this->getId() ), $destination_std );
            else
                $service->publish( 
                    $service->createId( $this->getType(), $this->getId() ) );
        }
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setData( $data )
    {
        $this->getProperty()->data = $data;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setMaintainAbsoluteLinks( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        
        $this->getProperty()->maintainAbsoluteLinks = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setRewriteLinks( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        
        $this->getProperty()->rewriteLinks = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setShouldBeIndexed( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->shouldBeIndexed = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setShouldBePublished( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
            
        $this->getProperty()->shouldBePublished = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setText( $text )
    {
        $this->getProperty()->text = $text;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function unpublish()
    {
        $this->getService()->unpublish( $this->getIdentifier() );
        return $this;
    }
}
?>