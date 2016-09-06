<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/17/2016 Added scheduledPublishDestinationMode to the class.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

abstract class ScheduledPublishing extends ContainedAsset
{
    const DEBUG     = false;
    const SUNDAY    = c\T::SUNDAY;
    const MONDAY    = c\T::MONDAY;
    const TUESDAY   = c\T::TUESDAY;
    const WEDNESDAY = c\T::WEDNESDAY;
    const THURSDAY  = c\T::THURSDAY;
    const FRIDAY    = c\T::FRIDAY;
    const SATURDAY  = c\T::SATURDAY;
    
    const ALLDESTINATIONS      = c\T::ALLDESTINATIONS;
    const SELECTEDDESTINATIONS = c\T::SELECTEDDESTINATIONS;
    
    const DEFAULT_TIME = "00:00:00.000";

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->days_of_week = array( 
            self::SUNDAY, self::MONDAY, 
            self::TUESDAY, self::WEDNESDAY, self::THURSDAY,
            self::FRIDAY, self::SATURDAY
        );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function addGroupToSendReport( Group $g )
    {
        if( $g == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        
        $g_name       = $g->getName();
        $group_string = $this->getProperty()->sendReportToGroups;
        $group_array  = explode( ';', $group_string );
        
        if( !in_array( $g_name, $group_array ) )
        {
            $group_array[] = $g_name;
        }
        $group_string = implode( ';', $group_array );
        $this->getProperty()->sendReportToGroups = $group_string;

        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function addUserToSendReport( User $u )
    {
        if( $u == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_USER . E_SPAN );
        
        $u_name      = $u->getName();
        $user_string = $this->getProperty()->sendReportToUsers;
        $user_array  = explode( ';', $user_string );
        
        if( !in_array( $u_name, $user_array ) )
        {
            $user_array[] = $u_name;
        }
        $user_string = implode( ';', $user_array );
        $this->getProperty()->sendReportToUsers = $user_string;

        return $this;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCronExpression()
    {
        return $this->getProperty()->cronExpression;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getDaysOfWeek()
    {
        return $this->days_of_week;
    }
   
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPublishDaysOfWeek()
    {
        return $this->getProperty()->publishDaysOfWeek;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPublishIntervalHours()
    {
        return $this->getProperty()->publishIntervalHours;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledDestinationMode()
    {
        return $this->getProperty()->scheduledPublishDestinationMode;
    }
  
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSendReportOnErrorOnly()
    {
        return $this->getProperty()->sendReportOnErrorOnly;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSendReportToGroups()
    {
        return $this->getProperty()->sendReportToGroups;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getSendReportToUsers()
    {
        return $this->getProperty()->sendReportToUsers;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getTimeToPublish()
    {
        return $this->getProperty()->timeToPublish;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getUsesScheduledPublishing()
    {
        return $this->getProperty()->usesScheduledPublishing;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setCronExpression( $cron )
    {
        if( isset( $cron ) && trim( $cron ) != "" )
        {
            return $this->setScheduledPublishing( true, NULL, NULL, $cron, NULL );
        }
        throw new e\EmptyValueException( 
            S_SPAN . c\M::EMPTY_CRON_EXPRESSION . E_SPAN );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setDayOfWeek( $days, $time=NULL )
    {
        return $this->setPublishDayOfWeek( $days, $time=NULL );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setIntervalHours( $hours, $time=NULL )
    {
        return $this->setPublishIntervalHours( $hours, $time=NULL );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPublishDayOfWeek( $days, $time=NULL )
    {
        if( isset( $days ) )
        {
            return $this->setScheduledPublishing( true, $days, NULL, NULL, $time );
        }
        throw new e\EmptyValueException(
            S_SPAN . c\M::NULL_DAYS . E_SPAN );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setPublishIntervalHours( $hours, $time=NULL )
    {
        if( isset( $hours ) )
        {
            return $this->setScheduledPublishing( true, NULL, $hours, NULL, $time );
        }
        throw new e\EmptyValueException( 
            S_SPAN . c\M::NULL_INTERVAL . E_SPAN );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function setScheduledPublishing( 
        $uses_scheduled_publishing=false,
        //$mode="",
        //$destinations=NULL,
        $day_of_week=NULL, 
        $publish_interval_hours=NULL, 
        $cron_expression=NULL, 
        $time_to_publish=NULL
    )
    {
        if( !c\BooleanValues::isBoolean( $uses_scheduled_publishing ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $uses_scheduled_publishing must be a boolean. " . E_SPAN );
    
        if( !$uses_scheduled_publishing ) // unset
        {
            $this->getProperty()->usesScheduledPublishing = false;
            $this->getProperty()->timeToPublish           = NULL;
            $this->getProperty()->publishIntervalHours    = NULL;
            $this->getProperty()->publishDaysOfWeek       = NULL;
            $this->getProperty()->cronExpression          = NULL;
            $this->getProperty()->sendReportToUsers       = NULL;
            $this->getProperty()->sendReportToGroups      = NULL;
            $this->getProperty()->sendReportOnErrorOnly   = false;
            
            return $this;
        }
        else // use scheduled publishing
        {
            $this->getProperty()->usesScheduledPublishing = true;
            $this->getProperty()->scheduledPublishDestinationMode = c\T::ALLDESTINATIONS;
            
/*            
            if( $mode != c\T::SELECTEDDESTINATIONS && $mode != c\T::ALLDESTINATIONS )
                $mode = c\T::ALLDESTINATIONS;
        
            if( $mode == c\T::SELECTEDDESTINATIONS )
            {
                if( isset( $destinations ) )
                {
                    if( is_array( $destinations ) )
                    {
                        if( count( $destinations ) > 0 )
                            $this->getProperty()->scheduledPublishDestinations = $destinations;
                        else
                            $mode = c\T::ALLDESTINATIONS;
                    }
                    else // not an array
                    {
                        $this->getProperty()->scheduledPublishDestinations = array( $destinations );
                    }
                }
                else
                {
                    echo "All mode" . BR;
                    $mode = c\T::ALLDESTINATIONS;
                }
            }
        
            //if( $mode == c\T::ALLDESTINATIONS )
                //$this->getProperty()->scheduledPublishDestinations = NULL;
        
            $this->getProperty()->scheduledPublishDestinationMode = $mode;
*/
            // days are supplied
            if( isset( $day_of_week ) )
            {
                // a string
                if( in_array( $day_of_week, $this->days_of_week ) )
                {
                    $this->getProperty()->publishDaysOfWeek->dayOfWeek = $day_of_week;
                
                    // possible error message from Cascade?
                    if( isset( $time_to_publish ) )
                    {
                        $this->getProperty()->timeToPublish = $time_to_publish;
                    }
                    else
                    {
                        $this->getProperty()->timeToPublish = self::DEFAULT_TIME;
                    }
                }
                // an array of strings
                else if( is_array( $day_of_week ) )
                {
                    foreach( $day_of_week as $day )
                    {
                        if( !in_array( $day, $this->days_of_week ) )
                        {
                            throw new e\UnacceptableValueException( 
                                S_SPAN . "The value $day is not acceptable." . E_SPAN );
                        }
                    }
            
                    $temp = array();
            
                    // to preserve order, which does not matter
                    foreach( $this->days_of_week as $day ) 
                    {
                        if( in_array( $day, $day_of_week ) )
                        {
                            $temp[] = $day;
                        }
                    }
                
                    $this->getProperty()->publishDaysOfWeek->dayOfWeek = $temp;
                    // possible error message from Cascade: yes
                    if( isset( $time_to_publish ) )
                    {
                        $this->getProperty()->timeToPublish = $time_to_publish;
                    }
                    else
                    {
                        $this->getProperty()->timeToPublish = self::DEFAULT_TIME;
                    }
                }
                else
                {
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "The value $day_of_week is not acceptable." . E_SPAN );
                }
                unset( $this->getProperty()->publishIntervalHours );
                unset( $this->getProperty()->cronExpression );
            }
            // interval is supplied
            else if( isset( $publish_interval_hours ) ) 
            {
                if( intval( $publish_interval_hours ) > 0 &&
                    intval( $publish_interval_hours ) < 24 )
                {
                    $this->getProperty()->publishIntervalHours = $publish_interval_hours;
                }
                else
                {
                    throw new e\UnacceptableValueException( 
                        S_SPAN . "The value $publish_interval_hours is not acceptable." . E_SPAN );
                }
            
                unset( $this->getProperty()->publishDaysOfWeek );
                unset( $this->getProperty()->cronExpression );
                // possible error message from Cascade?
                $this->getProperty()->timeToPublish     = $time_to_publish;
            }
            // a cron expression is supplied
            else if( isset( $cron_expression ) )
            {
                unset( $this->getProperty()->timeToPublish );
                unset( $this->getProperty()->publishIntervalHours );
                unset( $this->getProperty()->publishDaysOfWeek );
                $this->getProperty()->cronExpression = $cron_expression;
            }
            else
            {
                throw new e\EmptyValueException(
                    S_SPAN . c\M::EMPTY_VALUE . E_SPAN );
            }
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
    public function setSendReportOnErrorOnly( $bool )
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        $this->getProperty()->sendReportOnErrorOnly   = $bool;
        return $this;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function unsetScheduledPublishing()
    {
        return $this->setScheduledPublishing( false );
    }

    private $days_of_week;
}
?>