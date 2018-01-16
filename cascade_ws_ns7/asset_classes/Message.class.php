<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 12/16/2016 Fixed a bug related to $this->msg_errors.
  * 5/28/2015 Added namespaces.
  * 6/2/2014 Added asset expiration.
  * 5/22/2014 Fixed some bugs. Added republishFailedJob.
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
<p>A <code>Message</code> object represents a message. This class is an independent class that does not extend another class.</p>
<p>Messages can be classified into various types, depending on what is included in the subject line.
Currently, this class supports six types: \"Asset expiration\", \"Publish\", \"Un-publish\", \"Summary\", \"Workflow\", and \"Others\".
These types are used mainly in <a href=\"http://www.upstate.edu/web-services/api/message-arrays.php\"><code>MessageArrays</code></a> and <a href=\"http://www.upstate.edu/web-services/api/cascade.php\"><code>Cascade</code></a>.</p>
<h2>Structure of <code>message</code></h2>
<pre>message
  id
  to
  from
  subject
  body
  date
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "message" ),
        array( "getComplexTypeXMLByName" => "messagesList" ),
        array( "getSimpleTypeXMLByName"  => "message-mark-type" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/message.php">message.php</a></li></ul></postscript>
</documentation>
*/
class Message
{
    const DEBUG = false;
    const DUMP  = false;
    
    const TYPE_EXPIRATION = "Asset expiration";
    const TYPE_PUBLISH    = "Publish";
    const TYPE_UNPUBLISH  = "Un-publish";
    const TYPE_SUMMARY    = "Summary";
    const TYPE_WORKFLOW   = "Workflow";
    const TYPE_OTHERS     = "Others";
    
    const PATTERN_SUCCESSFUL_JOBS  = "/Successful Jobs \((\d+)\)/";
    const PATTERN_JOBS_WITH_ERRORS = "/Jobs with Errors \((\d+)\)/";
    const PATTERN_SKIPPED_JOBS     = "/Skipped Jobs \((\d+)\)/";
    const PATTERN_BROKEN_LINKS     = "/Broken Links \((\d+)\)/";
    
    const PATTERN_SUCCESSFUL_JOBS_MSG  = "/<a name=\"completedJobs\W*br\/>Successful Jobs \(\d+\): .+\W+br\/>-+([\w\W]+)<a name=\"unsuccessfuljobs\"/";
    const PATTERN_JOBS_WITH_ERRORS_MSG = "/<a name=\"unsuccessfuljobs\"\/>\W+br.+\W+.+-+([\w\W]+)<a name=\"skippedjobs\"/";
    const PATTERN_SKIPPED_JOBS_MSG1    = "/<a name=\"skippedjobs\"\/>\W+br.+\W+.+-+([\w\W]+)<a name=\"brokenlinks\"/";
    const PATTERN_SKIPPED_JOBS_MSG2    = "/<a name=\"skippedjobs\"\/>\W+br.+\W+.+-+([\w\W]+)<\/font/";
    const PATTERN_BROKEN_LINKS_MSG     = "/<a name=\"brokenlinks\"\/>\W+br.+\W+.+-+([\w\W]+)<\/font/";

/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( \stdClass $message )
    {
        if( isset( $message ) )
        {
            $this->id      = $message->id;
            $this->to      = $message->to;
            $this->from    = $message->from;
            $this->date    = new \DateTime( $message->date );
            $this->subject = trim( $message->subject );
            $this->body    = $message->body;
            
            $this->msg_errors = array();
            
            if( u\StringUtility::startsWith( $this->subject, self::TYPE_EXPIRATION ) )
            {
                //echo "Type " . self::TYPE_EXPIRATION . BR;
                $this->type = self::TYPE_EXPIRATION;
            }
            else if( u\StringUtility::startsWith( $this->subject, self::TYPE_PUBLISH ) )
            {
                //echo "Type " . self::TYPE_PUBLISH . BR;
                $this->type = self::TYPE_PUBLISH;
            }
            else if( u\StringUtility::startsWith( $this->subject, self::TYPE_UNPUBLISH ) )
            {
                //echo "Type " . self::TYPE_UNPUBLISH . BR;
                $this->type = self::TYPE_UNPUBLISH;
            }
            else if( u\StringUtility::startsWith( $this->subject, self::TYPE_SUMMARY ) )
            {
                //echo "Type " . self::TYPE_SUMMARY . BR;
                $this->type = self::TYPE_SUMMARY;
            }
            else if( u\StringUtility::startsWith( $this->subject, self::TYPE_WORKFLOW ) )
            {
                //echo "Type " . self::TYPE_WORKFLOW . BR;
                $this->type = self::TYPE_WORKFLOW;
            }
            else
            {
                $this->type = self::TYPE_OTHERS;
            }
            
            if( strpos( $this->subject, "(0 issue(s))" ) !== false )
            {
                $this->with_issues = false;
            }
            else // not relevant to summary
            {
                if( $this->type == self::TYPE_SUMMARY )
                    $this->with_issues = true;
                else
                    $this->with_issues = false;
            }
            
            if( strpos( $this->subject, "(0 failures)" ) !== false )
            {
                $this->with_failures = false;
            }
            else
            {
                if( $this->type == self::TYPE_SUMMARY )
                    $this->with_failures = true;
                else
                    $this->with_failures = false;
            }
            
            if( strpos( $this->subject, "is complete" ) !== false )
            {
                $this->is_complete = true;
            }
            else
            {
                $this->is_complete = false;
            }
            
            // body processing
            $matches = array();
            
            preg_match( self::PATTERN_SUCCESSFUL_JOBS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->num_successful_jobs = intval( $matches[ 1 ] );
            }
            else
            {
                $this->num_successful_jobs = 0;
            }
            
            preg_match( self::PATTERN_JOBS_WITH_ERRORS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->num_jobs_with_errors = intval( $matches[ 1 ] );
            }
            else
            {
                $this->num_jobs_with_errors = 0;
            }
            
            preg_match( self::PATTERN_SKIPPED_JOBS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->num_skipped_jobs = intval( $matches[ 1 ] );
            }
            else
            {
                $this->num_skipped_jobs = 0;
            }
            
            preg_match( self::PATTERN_BROKEN_LINKS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->num_broken_links = intval( $matches[ 1 ] );
            }
            else
            {
                $this->num_broken_links = 0;
            }
            
            preg_match( self::PATTERN_SUCCESSFUL_JOBS_MSG, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->msg_successful_jobs = $matches[ 1 ];
            }
            else
            {
                $this->msg_successful_jobs = "";
            }
            
            preg_match( self::PATTERN_JOBS_WITH_ERRORS_MSG, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                if( $this->num_jobs_with_errors > 0 )
                {
                    $this->msg_jobs_with_errors = $matches[ 1 ];
                    $this->msg_errors[] = trim( $this->msg_jobs_with_errors );
                }
                else
                    $this->msg_jobs_with_errors = "";
            }
            else
            {
                $this->msg_jobs_with_errors = "";
            }
            
            if( strpos( $this->body, "=\"brokenlinks\"" ) !== false )
                preg_match( self::PATTERN_SKIPPED_JOBS_MSG1, $this->body, $matches );
            else
                preg_match( self::PATTERN_SKIPPED_JOBS_MSG2, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                if( $this->num_skipped_jobs > 0 )
                {
                    $this->msg_skipped_jobs = $matches[ 1 ];
                    $this->msg_errors[] = trim( $this->msg_skipped_jobs );
                }
                else
                    $this->msg_skipped_jobs = "";
            }
            else
            {
                $this->msg_skipped_jobs = "";
            }
            
            preg_match( self::PATTERN_BROKEN_LINKS_MSG, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                if( $this->num_broken_links > 0 )
                {
                    $this->msg_broken_links = $matches[ 1 ];
                    $this->msg_errors[] = trim( $this->msg_broken_links );
                }
                else
                    $this->msg_broken_links = "";
            }
            else
            {
                $this->msg_broken_links = "";
            }
        }
    }
    
/**
<documentation><description><p>Displays the message and returns the calling object.</p></description>
<example>$message->display();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function display() : Message
    {
        echo S_H2      . "Message " . $this->id . E_H2 .
            c\L::TYPE    . $this->type . BR .
            c\L::TO      . $this->to . BR .
            c\L::FROM    . $this->from . BR .
            c\L::SUBJECT . $this->subject . BR .
            c\L::BODY    . BR . $this->body . BR .
            c\L::DATE    . date_format( $this->date, 'Y-m-d H:i:s' ) . BR . HR;
            HR;
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>body</code>.</p></description>
<example>echo $message->getBody(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getBody() : string
    {
        return $this->body;
    }
    
/**
<documentation><description><p>Returns the "Broken Links" part of the message or an empty string.</p></description>
<example>echo $message->getBrokenLinks(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getBrokenLinks() : string
    {
        return $this->msg_broken_links;
    }
    
/**
<documentation><description><p>Returns <code>date</code> (a <code>DateTime</code> object).</p></description>
<example>echo date_format( $message->getDate(), 'Y-m-d H:i:s' ), BR;</example>
<return-type>DateTime</return-type>
<exception></exception>
</documentation>
*/
    public function getDate() : \DateTime
    {
        return $this->date;
    }
    
/**
<documentation><description><p>Returns an array of error strings or an empty array.</p></description>
<example>u\DebugUtility::dump( $message->getErrors() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getErrors() : array
    {
        return $this->msg_errors;
    }
    
/**
<documentation><description><p>Returns <code>from</code>.</p></description>
<example>echo $message->getFrom(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getFrom() : string
    {
        return $this->from;
    }
    
/**
<documentation><description><p>Returns <code>id</code>.</p></description>
<example>echo $message->getId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getId() : string
    {
        return $this->id;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the job is complete. This value is only relevant to workflow messages.</p></description>
<example>echo u\StringUtility::boolToString( $message->getIsComplete() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIsComplete() : bool
    {
        return $this->is_complete;
    }
    
/**
<documentation><description><p>Returns the "Jobs with Errors" part of the message or an empty string.</p></description>
<example>echo $message->getJobsWithErrors(), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getJobsWithErrors()
    {
        return $this->msg_jobs_with_errors;
    }
    
/**
<documentation><description><p>Returns the number of broken links in the message.</p></description>
<example>echo $message->getNumberOfBrokenLinks(), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getNumberOfBrokenLinks() : int
    {
        return $this->num_broken_links;
    }
    
/**
<documentation><description><p>Returns the number of jobs with errors in the message.</p></description>
<example>echo $message->getNumberOfJobsWithErrors(), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getNumberOfJobsWithErrors() : int
    {
        return $this->num_jobs_with_errors;
    }
    
/**
<documentation><description><p>Returns the number of skipped jobs in the message.</p></description>
<example>echo $message->getNumberOfSkippedJobs(), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getNumberOfSkippedJobs() : int
    {
        return $this->num_skipped_jobs;
    }
    
/**
<documentation><description><p>Returns the number of successful jobs in the message.</p></description>
<example>echo $message->getNumberOfSuccessfulJobs(), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public function getNumberOfSuccessfulJobs() : int
    {
        return $this->num_successful_jobs;
    }
    
/**
<documentation><description><p>Returns the "Skipped Jobs" part of the message or an empty string.</p></description>
<example>echo $message->getSkippedJobs(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSkippedJobs() : string
    {
        return $this->msg_skipped_jobs;
    }
    
/**
<documentation><description><p>Returns <code>subject</code>.</p></description>
<example>echo $message->getSubject(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSubject() : string
    {
        return $this->subject;
    }
    
/**
<documentation><description><p>Returns the "Successful Jobs" part of the message or an empty string.</p></description>
<example>echo $message->getSuccessfulJobs(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSuccessfulJobs() : string
    {
        return $this->msg_successful_jobs;
    }
    
/**
<documentation><description><p>Returns <code>to</code>.</p></description>
<example>echo $message->getTo(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTo() : string
    {
        return $this->to;
    }
    
/**
<documentation><description><p>Returns the type string.</p></description>
<example>echo $message->getType(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getType() : string
    {
        return $this->type;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the message is with failures. This value is only relevant to summary messages.</p></description>
<example>echo u\StringUtility::boolToString( $message->getWithFailures() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWithFailures() : bool
    {
        return $this->with_failures;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the message is with issues. This value is only relevant to publish and unpublish messages.</p></description>
<example>echo u\StringUtility::boolToString( $message->getWithIssues() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getWithIssues() : bool
    {
        return $this->with_issues;
    }
    
/**
<documentation><description><p>For a message of type "Publish" with failed jobs, this
method analyses the error message and re-executes all failed jobs, and returns the calling
object. There are two things worth noting. First, this method ignores the destination part
of the message and republish to all destinations. Second, it assumes that all failed jobs
are page-related. If the asset that failed to publish is not a page, it will be ignored by
this method.</p></description>
<example>$message->republishFailedJobs( $cascade );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function republishFailedJobs( Cascade $cascade ) : Message
    {
        echo $this->type . BR;
        echo $this->num_jobs_with_errors . BR;
        
        if( $this->type == self::TYPE_PUBLISH && $this->num_jobs_with_errors > 0 )
        {
            foreach( $this->msg_errors as $error )
            {
                try
                {
                    list( $destination, $site, $path ) = 
                        u\StringUtility::getExplodedStringArray( "\t", $error );
                    //$destination = trim( $destination, "[]" );
                    $site = trim( $site, ":" );
                    // ignore destinations
                    if( $site != "" && $path != "" )
                    {
                        $cascade->getAsset( Page::TYPE, $path, $site )->
                            publish( $destination );
                    }
                }
                catch( \Exception $e )
                {
                    echo "Failed to republish failed job" . BR;
                    echo S_PRE . $e . E_PRE;
                    continue;
                }
            }
        }
        return $this;
    }

/**
<documentation><description><p>A method used to sort <code>Message</code> objects, using the <code>date</code> object. The sort order is descending.</p></description>
<example></example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/

    public static function compare( Message $m1, Message $m2 ) : int
    {
        if( $m1->getDate() == $m2->getDate() )
        {
            return 0;
        }
        else if( $m1->getDate() < $m2->getDate() )
        {
            return 1;
        }
        else
        {
            return -1;
        }
    }
    
    private $type;
    private $id;
    private $to;
    private $from;
    private $subject;
    private $date;
    private $body;
    private $with_issues;
    private $with_failures;
    private $is_complete;
    
    private $num_successful_jobs;
    private $num_jobs_with_errors;
    private $num_skipped_jobs;
    private $num_broken_links;
    
    private $msg_successful_jobs;
    private $msg_jobs_with_errors;
    private $msg_skipped_jobs;
    private $msg_broken_links;
    private $msg_errors;
}