<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 6/2/2014 Added asset expiration.
  * 5/22/2014 Fixed some bugs. Added republishFailedJob.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

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
    
    const PATTERN_ERRORS = '/<b>([^<]+)<\/b>/';

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
                $this->with_issues = true;
            }
            
            if( strpos( $this->subject, "(0 failures)" ) !== false )
            {
                $this->with_failures = false;
            }
            else
            {
                $this->with_failures = true;
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
                $this->num_successful_jobs = NULL;
            }
            
            preg_match( self::PATTERN_JOBS_WITH_ERRORS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->num_jobs_with_errors = intval( $matches[ 1 ] );
            }
            else
            {
                $this->num_jobs_with_errors = NULL;
            }
            
            preg_match( self::PATTERN_SKIPPED_JOBS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->num_skipped_jobs = intval( $matches[ 1 ] );
            }
            else
            {
                $this->num_skipped_jobs = NULL;
            }
            
            preg_match( self::PATTERN_BROKEN_LINKS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->num_broken_links = intval( $matches[ 1 ] );
            }
            else
            {
                $this->num_broken_links = NULL;
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
                $this->msg_jobs_with_errors = $matches[ 1 ];
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
                $this->msg_skipped_jobs = $matches[ 1 ];
            }
            else
            {
                $this->msg_skipped_jobs = "";
            }
            
            preg_match( self::PATTERN_BROKEN_LINKS_MSG, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $this->msg_broken_links = $matches[ 1 ];
            }
            else
            {
                $this->msg_broken_links = "";
            }
            
            $this->msg_errors = array();
            
            preg_match_all( self::PATTERN_ERRORS, $this->body, $matches );
            
            if( isset( $matches[ 1 ] ) )
            {
                $errors = $matches[ 1 ];
                
                if( !is_array( $errors ) )
                {
                    $errors = array( $errors );
                }
                    
                foreach( $errors as $error )
                {
                    $this->msg_errors[] = trim( $error );
                }
            }
        }
    }
    
    public function display()
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
    
    public function getBody()
    {
        return $this->body;
    }
    
    public function getBrokenLinks()
    {
        return $this->msg_broken_links;
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    public function getErrors()
    {
        return $this->msg_errors;
    }
    
    public function getFrom()
    {
        return $this->from;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getIsComplete()
    {
        return $this->is_complete;
    }
    
    public function getJobsWithErrors()
    {
        return $this->msg_jobs_with_errors;
    }
    
    public function getNumberOfBrokenLinks()
    {
        return $this->num_broken_links;
    }
    
    public function getNumberOfJobsWithErrors()
    {
        return $this->num_jobs_with_errors;
    }
    
    public function getNumberOfSkippedJobs()
    {
        return $this->num_skipped_jobs;
    }
    
    public function getNumberOfSuccessfulJobs()
    {
        return $this->num_successful_jobs;
    }
    
    public function getSkippedJobs()
    {
        return $this->msg_skipped_jobs;
    }
    
    public function getSubject()
    {
        return $this->subject;
    }
    
    public function getSuccessfulJobs()
    {
        return $this->msg_successful_jobs;
    }
    
    public function getTo()
    {
        return $this->to;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function getWithFailures()
    {
        return $this->with_failures;
    }

    public function getWithIssues()
    {
        return $this->with_issues;
    }
    
    public function republishFailedJobs( Cascade $cascade, Destination $destination )
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
                        $cascade->getAsset( Page::TYPE, $path, $site )->publish( $destination );
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

    /* for sorting, descending */
    public static function compare( Message $m1, Message $m2 )
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