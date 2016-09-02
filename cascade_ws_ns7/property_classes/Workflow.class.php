<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 2/13/2016 Added start_date, end_date, and the get methods.
  * 5/28/2015 Added namespaces.
  * 10/1/2014 Fixed a bug in getRelatedEntity.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;

class Workflow extends Property
{
    public function __construct( 
        \stdClass $wf=NULL, 
        aohs\AssetOperationHandlerService $service=NULL,
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $wf ) )
        {
            $this->workflow        = $wf;
            $this->related_entity  = new Identifier( $wf->relatedEntity );
            $this->ordered_steps   = array();
            $this->unordered_steps = array();
            $this->service         = $service;
        
            if( isset( $wf->orderedSteps ) && isset( $wf->orderedSteps->step ) )
            {
                $steps = $wf->orderedSteps->step;
                
                if( !is_array( $steps ) )
                {
                    $steps = array( $steps );
                }
                
                foreach( $steps as $step )
                {
                    $s                     = new Step( $step );
                    $this->ordered_steps[] = $s;
                    
                    $this->ordered_step_possible_action_map[ $s->getIdentifier() ]  =
                        $s->getActionIdentifiers();
                }
            }
        
            if( isset( $wf->unorderedSteps ) && isset( $wf->unorderedSteps->step ) )
            {
                $steps = $wf->unorderedSteps->step;
                
                if( !is_array( $steps ) )
                {
                    $steps = array( $steps );
                }
                
                foreach( $steps as $step )
                {
                    $s                       = new Step( $step );
                    $this->unordered_steps[] = $s;
                    
                    $this->unordered_step_possible_action_map[ $s->getIdentifier() ]  =
                        $s->getActionIdentifiers();
                }
            }
            $this->start_date = $wf->startDate;
            $this->end_date   = $wf->endDate;
        }
    }
    
    public function getCurrentStep()
    {
        return $this->workflow->currentStep;
    }
    
    public function getCurrentStepPossibleActions()
    {
        return $this->ordered_step_possible_action_map[ $this->workflow->currentStep ];
    }
    
    public function getEndDate()
    {
        return $this->end_date;
    }
    
    public function getId()
    {
        return $this->workflow->id;
    }
    
    public function getName()
    {
        return $this->workflow->name;
    }
    
    public function getRelatedEntity()
    {
        return $this->related_entity;
    }
    
    public function getStartDate()
    {
        return $this->start_date;
    }
    
    public function isPossibleAction( $a_name )
    {
        if( is_array( $this->ordered_step_possible_action_map[ $this->workflow->currentStep ] ) &&
            in_array( $a_name,
                $this->ordered_step_possible_action_map[ $this->workflow->currentStep ] ) )
            return true;
            
        if( is_array( $this->unordered_step_possible_action_map[ $this->workflow->currentStep ] ) &&
            in_array( $a_name,
                $this->unordered_step_possible_action_map[ $this->workflow->currentStep ] ) )
            return true;
            
        return false;
    }
    
    public function performWorkflowTransition( $a_name, $comment="" )
    {
        if( !$this->isPossibleAction( $a_name ) )
            throw new e\NoSuchActionException(
                S_SPAN . "The action $a_name is not defined in the workflow." . E_SPAN );
            
        $this->service->performWorkflowTransition( $this->workflow->id, $a_name, $comment );
        
        if( $this->service->isSuccessful() )
        {
            return $this;
        }
        else
        {
            throw new e\WorkflowTransitionFailureException(
                S_SPAN . "The transition cannot be performed." . E_SPAN .
                $this->service->getMessage() );
        }
    }
    
    public function toStdClass()
    {
        $obj                = new \stdClass();
        $obj->id            = $this->workflow->id;
        $obj->name          = $this->workflow->name;
        $obj->relatedEntity = $this->related_entity->toStdClass();
        $obj->currentStep   = $this->workflow->currentStep;
        $obj->orderedSteps  = new \stdClass();
        $os_count           = count( $this->ordered_steps );
        
        if( $os_count > 0 )
        {
            $obj->orderedSteps = new \stdClass();

            if( $os_count == 1 )
            {
                $obj->orderedSteps->step = $this->ordered_steps[ 0 ]->toStdClass();
            }
            else
            {
                $obj->orderedSteps->step = array();
                
                foreach( $this->ordered_steps as $step )
                {
                    $obj->orderedSteps->step[] = $step->toStdClass();
                }
            }
        }
        
        $us_count          = count( $this->unordered_steps );
        
        if( $us_count > 0 )
        {
            if( $us_count == 1 )
            {
                $obj->unorderedSteps->step = $this->unordered_steps[ 0 ]->toStdClass();
            }
            else
            {
                $obj->unorderedSteps->step = array();
                
                foreach( $this->unordered_steps as $step )
                {
                    $obj->unorderedSteps->step[] = $step->toStdClass();
                }
            }
        }
        
        $obj->startDate = $this->workflow->startDate;
        $obj->endDate   = $this->workflow->endDate;
    
        return $obj;
    }
    
    private $workflow;
    private $related_entity;
    private $ordered_steps;
    private $unordered_steps;
    private $ordered_step_possible_action_map;
    private $unordered_step_possible_action_map;
    private $action_id_identifier_map;
    private $start_date;
    private $end_date;
    private $service;
}
?>