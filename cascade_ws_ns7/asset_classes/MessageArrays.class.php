<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/27/2016 Fixed a bug in initialize.
  * 8/25/2016 Added $array_names and emptyArrays to fix a bug.
  * 5/28/2015 Added namespaces.
  * 6/2/2014 Added arrays for asset expiration.
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
<p>The <code>MessageArrays</code> class is a class containing a number of public static arrays used to store message information used by the <a href="http://www.upstate.edu/web-services/api/cascade.php"><code>Cascade</code></a> class.</p>
</description>
<postscript></postscript>
</documentation>
*/
class MessageArrays
{
    const DEBUG = false;
    const DUMP  = false;
    const NAME_SPACE = "cascade_ws_asset";

    // all messages
    public static $all_messages    = array();
    public static $all_message_ids = array();
    // from
    public static $message_ids_from = array();
    // non-system
    public static $non_system_messages = array();
    // expiration
    public static $asset_expiration_message_ids       = array();
    public static $asset_expiration_message           = array();
    // publish
    public static $publish_message_ids                = array();
    public static $publish_message_ids_with_issues    = array();
    public static $publish_message_ids_without_issues = array();
    public static $publish_messages                   = array();
    public static $publish_messages_with_issues       = array();
    public static $publish_messages_without_issues    = array();
    // unpublish
    public static $unpublish_message_ids                = array();
    public static $unpublish_message_ids_with_issues    = array();
    public static $unpublish_message_ids_without_issues = array();
    public static $unpublish_messages                   = array();
    public static $unpublish_messages_with_issues       = array();
    public static $unpublish_messages_without_issues    = array();
    // summary
    public static $summary_message_ids               = array();
    public static $summary_message_ids_no_failures   = array();
    public static $summary_message_ids_with_failures = array();
    public static $summary_messages                  = array();
    public static $summary_messages_no_failures      = array();
    public static $summary_messages_with_failures    = array();
    // workflow
    public static $workflow_message_ids             = array();
    public static $workflow_message_ids_is_complete = array();
    public static $workflow_message_ids_other       = array();
    public static $workflow_messages                = array();
    public static $workflow_messages_complete       = array();
    public static $workflow_messages_other          = array();
    // other
    public static $other_messages    = array();
    public static $other_message_ids = array();
    // objects
    public static $id_obj_map = array();
    
    private static $array_names = array(
        "all_messages",
        "all_message_ids",
        "message_ids_from",
        "non_system_messages",
        "asset_expiration_message_ids",
        "asset_expiration_message",
        "publish_message_ids",
        "publish_message_ids_with_issues",
        "publish_message_ids_without_issues",
        "publish_messages",
        "publish_messages_with_issues",
        "publish_messages_without_issues",
        "unpublish_message_ids",
        "unpublish_message_ids_with_issues",
        "unpublish_message_ids_without_issues",
        "unpublish_messages",
        "unpublish_messages_with_issues",
        "unpublish_messages_without_issues",
        "summary_message_ids",
        "summary_message_ids_no_failures",
        "summary_message_ids_with_failures",
        "summary_messages",
        "summary_messages_no_failures",
        "summary_messages_with_failures",
        "workflow_message_ids",
        "workflow_message_ids_is_complete",
        "workflow_message_ids_other",
        "workflow_messages",
        "workflow_messages_complete",
        "workflow_messages_other",
        "other_messages",
        "other_message_ids",
        "id_obj_map",
    );
    
    private static function emptyArrays()
    {
        foreach( self::$array_names as $array_name )
        {
            self::$$array_name = array();
        }
    }
    
/**
<documentation><description><p>Initizes all arrays.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function initialize( aohs\AssetOperationHandlerService $service )
    {
        self::emptyArrays();
        
        try
        {
            $service->listMessages();

            if( $service->isSuccessful() )
            {
                $messages = $service->getListedMessages();
                $temp_msg = array();
        
                if( isset( $messages->message ) )
                {
                    if( !is_array( $messages->message ) )
                    {
                        $temp_msg[] = $messages->message;
                    }
                    else
                    {
                        $temp_msg = $messages->message;
                    }

                    foreach( $temp_msg as $message )
                    {
                        $id      = $message->id;
                        $to      = $message->to;
                        $from    = $message->from;
                        $date    = $message->date;
                        $subject = trim( $message->subject );
                        $body    = $message->body;
            
                        self::$all_message_ids[] = $id;
                        $message_obj             = new Message( $message );
                        
                        // store all messages
                        self::$all_messages[]    = $message_obj;
                        self::$id_obj_map[ $id ] = $message_obj;
                
                        // from whom?
                        if( !isset( $message_ids_from[ $from ] ) )
                        {
                            self::$message_ids_from[ $from ] = array();
                        }

                        self::$message_ids_from[ $from ][] = $id;
                
                        if( $from != 'system' )
                        {
                            self::$non_system_messages[] = $message_obj;
                        }
                        
                        if( self::DEBUG ) { u\DebugUtility::out( $message_obj->getType() ); }
                
                        if( $message_obj->getType() == Message::TYPE_EXPIRATION )
                        {
                            self::$asset_expiration_message[]     = $message_obj;
                            self::$asset_expiration_message_ids[] = $id;
                        }
                        // publish messages
                        else if( $message_obj->getType() == Message::TYPE_PUBLISH )
                        {
                            self::$publish_messages[]    = $message_obj;
                            self::$publish_message_ids[] = $id;
                            
                            // no issues
                            if( strpos( $subject, "(0 issue(s))" ) !== false )
                            {
                                if( self::DEBUG ) { echo "L::121 " . $id . BR; }
                                self::$publish_message_ids_without_issues[] = $id;
                                self::$publish_messages_without_issues[]    = $message_obj;
                            }
                            else
                            {
                                if( self::DEBUG ) { echo "L::124 " . $id . BR; }
                                self::$publish_message_ids_with_issues[] = $id;
                                self::$publish_messages_with_issues[]    = $message_obj;
                            }
                        }
                        // unpublish messages
                        else if( $message_obj->getType() == Message::TYPE_UNPUBLISH )
                        {
                            self::$unpublish_messages[]    = $message_obj;
                            self::$unpublish_message_ids[] = $id;
                    
                            // no issues
                            if( strpos( $subject, "(0 issue(s))" ) !== false )
                            {
                                self::$unpublish_message_ids_without_issues[] = $id;
                                self::$unpublish_messages_without_issues[]    = $message_obj;
                            }
                            else
                            {
                                self::$unpublish_message_ids_with_issues[] = $id;
                                self::$unpublish_messages_with_issues[]    = $message_obj;
                            }
                        }
                        // summary
                        else if( $message_obj->getType() == Message::TYPE_SUMMARY )
                        {
                            self::$summary_messages[]    = $message_obj;
                            self::$summary_message_ids[] = $id;
                            
                            // 0 failures
                            if( strpos( $subject, "(0 failures)" ) !== false )
                            {
                                self::$summary_message_ids_no_failures[] = $id;
                                self::$summary_messages_no_failures[]    = $message_obj;
                            }
                            else
                            {
                                self::$summary_message_ids_with_failures[] = $id;
                                self::$summary_messages_with_failures[]    = $message_obj;
                            }
                        }
                        // workflow
                        else if( $message_obj->getType() == Message::TYPE_WORKFLOW )
                        {
                            self::$workflow_messages[]    = $message_obj;
                            self::$workflow_message_ids[] = $id;
                    
                            // is complete
                            if( strpos( $subject, "is complete" ) !== false )
                            {
                                self::$workflow_message_ids_is_complete[] = $id;
                                self::$workflow_messages_complete[]       = $message_obj;
                            }
                            else
                            {
                                self::$workflow_message_ids_other[] = $id;
                                self::$workflow_messages_other[]    = $message_obj;
                            }
                        }
                        // other
                        else
                        {
                            self::$other_messages[]    = $message_obj;
                            self::$other_message_ids[] = $id;
                        }
                        // date
                    }
                    
                    usort( self::$all_messages, self::NAME_SPACE . "\\" . 'Message::compare' );
                    usort( self::$publish_messages, self::NAME_SPACE . "\\" . 'Message::compare' );
                    usort( self::$unpublish_messages, self::NAME_SPACE . "\\" . 'Message::compare' );
                    usort( self::$workflow_messages, self::NAME_SPACE . "\\" . 'Message::compare' );
                    usort( self::$other_messages, self::NAME_SPACE . "\\" . 'Message::compare' );
                    
                    usort( self::$publish_messages_with_issues, self::NAME_SPACE . "\\" . 'Message::compare' );
                    usort( self::$unpublish_messages_with_issues, self::NAME_SPACE . "\\" . 'Message::compare' );
                    usort( self::$workflow_messages_other, self::NAME_SPACE . "\\" . 'Message::compare' );
                    
                    usort( self::$non_system_messages, self::NAME_SPACE . "\\" . 'Message::compare' );
                }
        
                //var_dump( $workflow_message_ids_is_complete );
            }
            else
                echo "Failed to list messages. " . $service->getMessage();
        }
        catch( \Exception $e )
        {
            echo S_PRE . $e . E_PRE;
        }
    }
}