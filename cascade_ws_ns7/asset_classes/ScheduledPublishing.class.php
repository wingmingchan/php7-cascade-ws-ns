<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/3/2018 Added code to test for NULL.
  * 12/29/2017 Updated addGroupToSendReport and addUserToSendReport.
  * 6/29/2017 Replaced static WSDL code with call to getXMLFragments.
  * 12/16/2016 Changed return type of getSendReportToGroups and getSendReportToUsers
  * to mixed.
  * 6/13/2017 Added WSDL.
  * 1/17/2016 Added scheduledPublishDestinationMode to the class.
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
<p>A destination, a publish set, and a site share a common feature: all three can have scheduled publishing enabled. The properties related to scheduled publishing are the following:</p>
<ul>
<li><code>usesScheduledPublishing</code></li>
<li><code>timeToPublish</code></li>
<li><code>publishIntervalHours</code></li>
<li><code>publishDaysOfWeek</code></li>
<li><code>cronExpression</code></li>
<li><code>sendReportToUsers</code></li>
<li><code>sendReportToGroups</code></li>
<li><code>sendReportOnErrorOnly</code></li>
</ul>
<p>To turn on scheduled publishing, one of the three temporal settings (<code>publishIntervalHours</code>, <code>publishDaysOfWeek</code>, or <code>cronExpression</code>) must be supplied. And there is one more requirement: when a temporal setting is supplied, the other two must be unset. Assigning <code>NULL</code> to them will not work.</p>
<p>Since these eight properties and related methods are shared by <a href=\"http://www.upstate.edu/web-services/api/asset-classes/destination.php\"><code>Destination</code></a>, <a href=\"http://www.upstate.edu/web-services/api/asset-classes/publish-set.php\"><code>PublishSet</code></a> and <a href=\"http://www.upstate.edu/web-services/api/asset-classes/site.php\"><code>Site</code></a></code>, I decide to create an abstract class named <code>ScheduledPublishing</code>, which serves as the parent class of these three classes, and provides all the relevant methods in this class.</p>
<h2>Design Issues</h2>
<p>Due to a known <a href=\"https://hannonhill.jira.com/browse/CSI-861\">bug</a> when PHP is used, the <code>scheduledPublishDestinations</code> property cannot be set properly. Thereofore, the <code>setScheduledPublishing</code> method defined in this class always sets the <code>scheduledPublishDestinationMode</code> property to <code>all-destinations</code>.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getSimpleTypeXMLByName"  => "scheduledDestinationMode" ),
        array( "getComplexTypeXMLByName" => "daysOfWeek" ),
        array( "getSimpleTypeXMLByName"  => "dayOfWeek" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/destination.php">destination.php</a></li></ul></postscript>
</documentation>
*/
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
<documentation><description><p>The constructor, overriding the parent method to initialize
the private array <code>$days_of_week</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    protected function __construct( 
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
<documentation><description><p>Adds a group name to <code>sendReportToGroups</code> and
returns the calling object.</p></description>
<example>$d->addGroupToSendReport(
    $cascade->getAsset( a\Group::TYPE, 'gch' ) )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addGroupToSendReport( Group $g ) : Asset
    {
        if( $g == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
        
        $g_name       = $g->getName();
        
        if( isset( $this->getProperty()->sendReportToGroups ) )
            $group_string = $this->getProperty()->sendReportToGroups;
        else
            $group_string = "";
            
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
<documentation><description><p>Adds a user name to <code>sendReportToUsers</code> and
returns the calling object.</p></description>
<example>$d->addUserToSendReport( 
    $cascade->getAsset( a\User::TYPE, 'chanw' ) )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addUserToSendReport( User $u ) : Asset
    {
        if( $u == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_USER . E_SPAN );
        
        $u_name      = $u->getName();
        
        if( isset( $this->getProperty()->sendReportToUsers ) )
            $user_string = $this->getProperty()->sendReportToUsers;
        else
            $user_string = "";
        
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
<documentation><description><p>Returns <code>cronExpression</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $d->getCronExpression() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getCronExpression()
    {
        if( isset( $this->getProperty()->cronExpression ) )
            return $this->getProperty()->cronExpression;
        return NULL;
    }
    
/**
<documentation><description><p>Returns an array of strings containing all seven weekday constants.</p></description>
<example>u\DebugUtility::dump( $d->getDaysOfWeek() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getDaysOfWeek() : array
    {
        return $this->days_of_week;
    }
   
/**
<documentation><description><p>Returns <code>publishDaysOfWeek</code>.</p></description>
<example>u\DebugUtility::dump( $d->getPublishDaysOfWeek() );</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPublishDaysOfWeek()
    {
        if( isset( $this->getProperty()->publishDaysOfWeek ) )
            return $this->getProperty()->publishDaysOfWeek;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>publishIntervalHours</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $d->getPublishIntervalHours() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPublishIntervalHours()
    {
        if( isset( $this->getProperty()->publishIntervalHours ) )
            return $this->getProperty()->publishIntervalHours;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>scheduledPublishDestinationMode</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $d->getScheduledDestinationMode() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getScheduledDestinationMode()
    {
        if( isset( $this->getProperty()->scheduledPublishDestinationMode ) )
            return $this->getProperty()->scheduledPublishDestinationMode;
        return NULL;
    }
  
/**
<documentation><description><p>Returns <code>sendReportOnErrorOnly</code>.</p></description>
<example>echo u\StringUtility::boolToString( $d->getSendReportOnErrorOnly() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getSendReportOnErrorOnly() : bool
    {
        return $this->getProperty()->sendReportOnErrorOnly;
    }
    
/**
<documentation><description><p>Returns <code>sendReportToGroups</code>.</p></description>
<example>echo u\StringUtility::boolToString( $d->getSendReportToGroups() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSendReportToGroups()
    {
        if( isset( $this->getProperty()->sendReportToGroups ) )
            return $this->getProperty()->sendReportToGroups;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>sendReportToUsers</code>.</p></description>
<example>echo u\StringUtility::boolToString( $d->getSendReportToUsers() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getSendReportToUsers()
    {
        if( isset( $this->getProperty()->sendReportToUsers ) )
            return $this->getProperty()->sendReportToUsers;
        return NULL;
    }

/**
<documentation><description><p>Returns <code>timeToPublish</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $d->getTimeToPublish() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTimeToPublish()
    {
        if( isset( $this->getProperty()->timeToPublish ) )
            return $this->getProperty()->timeToPublish;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>usesScheduledPublishing</code>.</p></description>
<example>echo u\StringUtility::boolToString( $d->getUsesScheduledPublishing() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getUsesScheduledPublishing() : bool
    {
        return $this->getProperty()->usesScheduledPublishing;
    }
    
/**
<documentation><description><p>Sets <code>cronExpression</code> and returns the calling
object.</p></description>
<example>$d->setCronExpression( "0 4 12 * * ?" )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setCronExpression( $cron ) : Asset
    {
        if( isset( $cron ) && trim( $cron ) != "" )
        {
            return $this->setScheduledPublishing( true, NULL, NULL, $cron, NULL );
        }
        throw new e\EmptyValueException( 
            S_SPAN . c\M::EMPTY_CRON_EXPRESSION . E_SPAN );
    }
    
/**
<documentation><description><p>An alias of <code>setPublishDayOfWeek</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDayOfWeek( $days, $time=NULL ) : Asset
    {
        return $this->setPublishDayOfWeek( $days, $time );
    }
    
/**
<documentation><description><p>An alias of <code>setPublishIntervalHours</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setIntervalHours( $hours, $time=NULL ) : Asset
    {
        return $this->setPublishIntervalHours( $hours, $time );
    }
    
/**
<documentation><description><p>Sets <code>publishDaysOfWeek</code> and returns the calling
object.</p></description>
<example>$d->setPublishDayOfWeek( $d->getDaysOfWeek() )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setPublishDayOfWeek( $days, $time=NULL ) : Asset
    {
        if( isset( $days ) )
        {
            return $this->setScheduledPublishing( true, $days, NULL, NULL, $time );
        }
        throw new e\EmptyValueException(
            S_SPAN . c\M::NULL_DAYS . E_SPAN );
    }
    
/**
<documentation><description><p>Sets <code>publishIntervalHours</code> and returns the
calling object.</p></description>
<example>$d->setPublishIntervalHours( 4 )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setPublishIntervalHours( int $hours, $time=NULL ) : Asset
    {
        if( isset( $hours ) )
        {
            return $this->setScheduledPublishing( true, NULL, $hours, NULL, $time );
        }
        throw new e\EmptyValueException( 
            S_SPAN . c\M::NULL_INTERVAL . E_SPAN );
    }
    
/**
<documentation><description><p>Sets one of the three temporal settings and returns the
calling object. This method is called by all other scheduling methods.</p></description>
<example>$d->setScheduledPublishing( true, 
    array( a\PublishSet::FRIDAY, a\PublishSet::FRIDAY, 
        a\PublishSet::THURSDAY, a\PublishSet::SATURDAY, 
        a\PublishSet::SUNDAY, a\PublishSet::WEDNESDAY,
        a\PublishSet::TUESDAY, a\PublishSet::THURSDAY, 
        a\PublishSet::MONDAY, a\PublishSet::SUNDAY ), 
    NULL, NULL, '06:30:00.000' )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException, EmptyValueException</exception>
</documentation>
*/
    public function setScheduledPublishing( 
        $uses_scheduled_publishing=false,
        //$mode="",
        //$destinations=NULL,
        $day_of_week=NULL, 
        $publish_interval_hours=NULL, 
        string $cron_expression=NULL, 
        string $time_to_publish=NULL
    ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $uses_scheduled_publishing ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $uses_scheduled_publishing must be a boolean. " .
                E_SPAN );
    
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
                    $this->getProperty()->publishDaysOfWeek = new \stdClass();
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
                        S_SPAN . "The value $publish_interval_hours is not acceptable." .
                        E_SPAN );
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
<documentation><description><p>Sets <code>sendReportOnErrorOnly</code> and returns the
calling object.</p></description>
<example>$d->setSendReportOnErrorOnly( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSendReportOnErrorOnly( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );
        $this->getProperty()->sendReportOnErrorOnly   = $bool;
        
        return $this;
    }
    
/**
<documentation><description><p>Turns off scheduled publishing and returns the calling
object.</p></description>
<example>$d->unsetScheduledPublishing()->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function unsetScheduledPublishing() : Asset
    {
        return $this->setScheduledPublishing( false );
    }

    private $days_of_week;
}
?>