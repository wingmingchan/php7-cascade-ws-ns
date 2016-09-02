<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/7/2016 Minor bug fixes.
  * 5/28/2015 Added namespaces.
  * 3/17/2015 Added private method getTimeInfo and method calls in 
    assetTreeReportScheduledPublishDestination,
    assetTreeReportScheduledPublishPublishSet, and reportScheduledPublishSite.
  * 8/15/2014 Added reportLongTitle.
  * 7/29/2014 Added reportRelativeLink, getCache, getResults, getRoot.
  * 7/28/2014 Added reportPageFieldEmptyValue, reportPageFieldMatchesValue.
  * 7/25/2014 Added reportPageNodeContainsValue, reportPageNodeEmptyValue.
  * 7/23/2014 Added reportOrphanFiles, reportNumberOfAssets.
  * 7/22/2014 File created.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

class Report
{
    const DEBUG = false;
    const DUMP  = false;

    public function __construct( Cascade $cascade )
    {
        $this->cascade = $cascade;
        $this->clearResults();
        $this->cache = u\Cache::getInstance( $cascade->getService() );
        $this->cache->clearCache();
    }
        
    public function __call( $func, $params )
    {
        // for metadata
        $methods = array(
            'reportHasAuthor',          'reportHasNoAuthor',
            'reportHasDisplayName',     'reportHasNoDisplayName',
            'reportHasEndDate',         'reportHasNoEndDate',
            'reportHasKeywords',        'reportHasNoKeywords',
            'reportHasMetaDescription', 'reportHasNoMetaDescription',
            'reportHasReviewDate',      'reportHasNoReviewDate',
            'reportHasStartDate',       'reportHasNoStartDate',
            'reportHasSummary',         'reportHasNoSummary',
            'reportHasTeaser',          'reportHasNoTeaser',
            'reportHasTitle',           'reportHasNoTitle',
            'reportAuthorContains',
            'reportDisplayNameContains',
            'reportKeywordsContains',
            'reportMetaDescriptionContains',
            'reportSummaryContains',
            'reportTeaserContains',
            'reportTitleContains'
        );
        
        if( !in_array( $func, $methods ) )
            throw new e\NoSuchMethodException( 
                S_SPAN . "The method Report::$func does not exist." . E_SPAN );
        
        // page is the default for type
        if( isset( $params[ 0 ] ) )
            $type = $params[ 0 ];
        else
            $type = Page::TYPE;
        
        // retraverse defaulted to false
        if( isset( $params[ 1 ] ) )
            $retraverse = $params[ 1 ];
        else
            $retraverse = false;
            
        // retraverse defaulted to false
        if( isset( $params[ 2 ] ) )
            $substring = $params[ 2 ];
        else
            $substring = false;
            
        if( !c\BooleanValues::isBoolean( $retraverse ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $retraverse must be a boolean." . E_SPAN );
        
        if( $retraverse )
        {
            if( isset( $substring ) )
                $this->reportMetadataWiredFields( 1, $substring );
            else
                $this->reportMetadataWiredFields( 1 );
        }
        
        if( isset( $this->results[ $type ] ) && isset( $this->results[ $type ][ $func ] ) )
            return $this->results[ $type ][ $func ];
        else
            return NULL;
    }
    
    public function clearResults()
    {
        $this->results = array();
        return $this;
    }
    
    public function getCache()
    {
        return $this->cache;
    }
    
    public function getResults()
    {
        return $this->results;
    }
    
    public function getRoot()
    {
        return $this->root;
    }
    
    public function reportDate( \DateTime $dt )
    {
        $this->checkRootFolder();

        $at                 = $this->root->getAssetTree();
        $params             = array();
        $params[ 'date' ]   = $dt;
        $params[ 'cache' ]  = $this->cache;
        
        $this->results[ DataDefinitionBlock::TYPE ] = array();
        $this->results[ FeedBlock::TYPE ] = array();
        $this->results[ File::TYPE ] = array();
        $this->results[ Folder::TYPE ] = array();
        $this->results[ IndexBlock::TYPE ] = array();
        $this->results[ Page::TYPE ] = array();
        $this->results[ Symlink::TYPE ] = array();
        $this->results[ TextBlock::TYPE ] = array();
        $this->results[ XmlBlock::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( 
                DataDefinitionBlock::TYPE => array( "Report::assetTreeReportDate"),
                FeedBlock::TYPE           => array( "Report::assetTreeReportDate"),
                File::TYPE                => array( "Report::assetTreeReportDate"),
                Folder::TYPE              => array( "Report::assetTreeReportDate"),
                IndexBlock::TYPE          => array( "Report::assetTreeReportDate"),
                Page::TYPE                => array( "Report::assetTreeReportDate"),
                Symlink::TYPE             => array( "Report::assetTreeReportDate"),
                TextBlock::TYPE           => array( "Report::assetTreeReportDate"),
                XmlBlock::TYPE            => array( "Report::assetTreeReportDate")
            ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportEndDateAfter( \DateTime $dt=NULL, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
        return $this->results[ $type ][ __FUNCTION__ ];
    }

    public function reportEndDateBefore( \DateTime $dt=NULL, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
        return $this->results[ $type ][ __FUNCTION__ ];
    }

    public function reportLast( $type, $days_inclusive, $direction )
    {
        $this->checkRootFolder();
        
        $at     = $this->root->getAssetTree();
        $method = u\StringUtility::getMethodName( $type );
        $params                = array();
        $params[ 'method' ]    = $method;
        $params[ 'day' ]       = $days_inclusive;
        $params[ 'direction' ] = $direction;
        $params[ 'cache' ]     = $this->cache;
        
        $this->results[ File::TYPE ] = array();
        $this->results[ Page::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( 
                File::TYPE => array( "Report::assetTreeReportLast" ),
                Page::TYPE => array( "Report::assetTreeReportLast" ),
            ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportMetadataWiredFields( $max_num_of_char=1, $substring="" )
    {
        $this->checkRootFolder();
        if( !is_numeric( $max_num_of_char ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "$max_num_of_char must be a positive integer." . E_SPAN );
            
        $max_num_of_char = intval( $max_num_of_char );
            
        $at                 = $this->root->getAssetTree();
        $params             = array();
        $params[ 'max' ]    = $max_num_of_char;
        $params[ 'cache' ]  = $this->cache;
        
        if( trim( $substring ) != "" )
        {
            $params[ 'substring' ] = $substring;
        }
        
        $this->results[ DataDefinitionBlock::TYPE ] = array();
        $this->results[ FeedBlock::TYPE ] = array();
        $this->results[ File::TYPE ] = array();
        $this->results[ Folder::TYPE ] = array();
        $this->results[ IndexBlock::TYPE ] = array();
        $this->results[ Page::TYPE ] = array();
        $this->results[ Symlink::TYPE ] = array();
        $this->results[ TextBlock::TYPE ] = array();
        $this->results[ XmlBlock::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( 
                DataDefinitionBlock::TYPE => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                FeedBlock::TYPE           => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                File::TYPE                => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                Folder::TYPE              => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                IndexBlock::TYPE          => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                Page::TYPE                => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                Symlink::TYPE             => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                TextBlock::TYPE           => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                ),
                XmlBlock::TYPE            => array( "Report::assetTreeReportMetadataWiredFields",
                    ( trim( $substring ) == "" ? NULL : "Report::assetTreeReportMetadataWiredFieldsContains" )
                )
            ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportLongDisplayName( $max_num_of_char=1, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportMetadataWiredFields( $max_num_of_char );
            
        if( isset( $this->results[ $type ] ) && isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }

    public function reportLongTitle( $max_num_of_char=1, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportMetadataWiredFields( $max_num_of_char );
            
        if( isset( $this->results[ $type ] ) && isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }
    
    public function reportNumberOfAssets( $types )
    {
        $this->checkRootFolder();
        $at = $this->root->getAssetTree();
        
        if( !is_array( $types ) )
            $types = array( $types );
        
        // set up the function array
        $functions  = array();
        
        foreach( $types as $type )
        {
            $this->results[ $type ] = 0;
            $functions[ $type ]     = array( "Report::assetTreeReportNumberOfAssets" );
        }
        
        // set up params
        $params = array();
        $params[ 'cache' ] = $this->cache;

        $at->traverse(
            // function array
            $functions,
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportOrphanFiles()
    {
        $this->checkRootFolder();
        $at = $this->root->getAssetTree();
        
        // set up params
        $params = array();
        $params[ 'cache' ] = $this->cache;

        //if( self::DEBUG ) { u\DebugUtility::out( "Traversing: " . $this->root->getPath() ); }

        // set up the report array
        $this->results[ File::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( 
                File::TYPE  => array( "Report::assetTreeReportOrphanFiles" )
            ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportPageFieldEmptyValue( $fields, $or=true )
    {
        $this->checkRootFolder();
            
        if( !is_array( $fields ) )
            throw new e\ReportException(
                S_SPAN . "The fields array is not set up properly. " . E_SPAN );
            
        if( count( $fields ) == 0 )
            throw new e\ReportException(
                S_SPAN . "The fields array is not set up properly. " . E_SPAN );
            
        $at = $this->root->getAssetTree();
        
        $field_value = array();

        foreach( $fields as $field )
        {
            $field_value[] = array( $field => "" );
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $field_value ); }
        
        // set up params
        $params = array( 'field-value' => $field_value, 'disjunctive' => $or, 'cache' => $this->cache );
        // set up results
        $this->results[ Page::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( Page::TYPE => array( "Report::assetTreeReportPageFieldValue" ) ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportPageFieldMatchesValue( $field_value, $or=true )
    {
        $this->checkRootFolder();
            
        if( !is_array( $field_value ) )
            throw new e\ReportException(
                S_SPAN . "The fields array is not set up properly. " . E_SPAN );
            
        if( count( $field_value ) == 0 )
            throw new e\ReportException(
                S_SPAN . "The fields array is not set up properly. " . E_SPAN );
            
        $at = $this->root->getAssetTree();
        
        //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $field_value ); }
        
        // set up params
        $params = array( 'field-value' => $field_value, 'disjunctive' => $or, 'cache' => $this->cache );
        // set up results
        $this->results[ Page::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( Page::TYPE => array( "Report::assetTreeReportPageFieldValue" ) ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportPageNodeContainsValue( $node_value, $or=true )
    {
        $this->checkRootFolder();
            
        if( !is_array( $node_value ) )
            throw new e\ReportException(
                S_SPAN . "The node-value array is not set up properly. " . E_SPAN );
            
        $at = $this->root->getAssetTree();
        
        // set up params
        $params = array( 'node-value' => $node_value, 'disjunctive' => $or, 'cache' => $this->cache );
        // set up results
        $this->results[ Page::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( Page::TYPE => array( "Report::assetTreeReportPageNodeValue" ) ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportPageNodeEmptyValue( $nodes, $or=true )
    {
        $this->checkRootFolder();
            
        if( !is_array( $nodes ) )
            throw new e\ReportException(
                S_SPAN . "The nodes array is not set up properly. " . E_SPAN );
            
        if( count( $nodes ) == 0 )
            throw new e\ReportException(
                S_SPAN . "The nodes array is not set up properly. " . E_SPAN );
            
        $at = $this->root->getAssetTree();
        
        $node_value = array();

        foreach( $nodes as $node )
        {
            $node_value[] = array( $node => "" );
        }
        
        if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $node_value ); }
        
        // set up params
        $params = array( 'node-value' => $node_value, 'disjunctive' => $or, 'cache' => $this->cache );
        // set up results
        $this->results[ Page::TYPE ] = array();
        
        $at->traverse(
            // function array
            array( Page::TYPE => array( "Report::assetTreeReportPageNodeValue" ) ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportPublishable( $publishable=true )
    {
        $this->checkRootFolder();
        $at = $this->root->getAssetTree();
        
        //if( self::DEBUG ) { u\DebugUtility::out( "Traversing: " . $this->root->getPath() ); }
        
        $params = array();
        
        // set up the params
        if( !$publishable ) // look for unpublishable assets
            $params[ 'publishable' ] = $publishable;
        
        $params[ 'cache' ] = $this->cache;

        // set up the report array
        $this->results[ Folder::TYPE ] = array();
        $this->results[ Page::TYPE ]   = array();
        $this->results[ File::TYPE ]   = array();
        
        $at->traverse(
            // function array
            array( 
                Folder::TYPE => array( "Report::assetTreeReportPublishable" ),
                Page::TYPE   => array( "Report::assetTreeReportPublishable" ),
                File::TYPE   => array( "Report::assetTreeReportPublishable" )
            ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportRelativeLink()
    {
        $this->checkRootFolder();
        $at = $this->root->getAssetTree();
        
        $params = array();
        $params[ 'cache' ] = $this->cache;

        // set up the report array
        $this->results[ DataDefinitionBlock::TYPE ] = array();
        $this->results[ Page::TYPE ]                = array();
        
        $at->traverse(
            // function array
            array( 
                DataDefinitionBlock::TYPE => array( "Report::assetTreeReportRelativeLink" ),
                Page::TYPE                => array( "Report::assetTreeReportRelativeLink" ),
                File::TYPE                => array( "Report::assetTreeReportRelativeLink" ),
            ),
            $params,
            $this->results
        );
        return $this->results;
    }
    
    public function reportReviewDateAfter( \DateTime $dt=NULL, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( isset( $this->results[ $type ] ) && isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }

    public function reportReviewDateBefore( \DateTime $dt=NULL, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( isset( $this->results[ $type ] ) && isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }
    
    public function reportScheduledPublishDestination()
    {
        // no need to set the root        
        $params = array();
        $params[ 'cache' ] = $this->cache;
        
        // set up the report array
        $this->results[ Destination::TYPE ] = array();
        
        // get all sites
        $sites = $this->cascade->getSites();

        foreach( $sites as $site_child )
        {
            $site = $this->cache->retrieveAsset( $site_child );
            
            $site->getRootSiteDestinationContainerAssetTree()->
                traverse(
                // function array
                array( 
                    Destination::TYPE => array( "Report::assetTreeReportScheduledPublishDestination" )
                ),
                $params,
                $this->results
            );
        }
        
        return $this->results;
    }
    
    public function reportScheduledPublishing()
    {
        $this->reportScheduledPublishDestination();
        $this->reportScheduledPublishPublishSet();
        return $this->reportScheduledPublishSite();
    }

    public function reportScheduledPublishPublishSet()
    {
        // no need to set the root        
        $params = array();
        $params[ 'cache' ] = $this->cache;
        
        // set up the report array
        $this->results[ PublishSet::TYPE ] = array();
        
        // get all sites
        $sites = $this->cascade->getSites();

        foreach( $sites as $site_child )
        {
            $site = $this->cache->retrieveAsset( $site_child );
            
            $site->getRootPublishSetContainerAssetTree()->
                traverse(
                // function array
                array( 
                    PublishSet::TYPE => array( "Report::assetTreeReportScheduledPublishPublishSet" )
                ),
                $params,
                $this->results
            );
        }
        
        return $this->results;
    }

    public function reportScheduledPublishSite()
    {
        // no need to set the root        
        $params = array();
        $params[ 'cache' ] = $this->cache;
        
        // set up the report array
        $this->results[ Site::TYPE ] = array();
        
        // get all sites
        $sites = $this->cascade->getSites();

        foreach( $sites as $site_child )
        {
            $site = $this->cache->retrieveAsset( $site_child );
            
            if( $site->getUsesScheduledPublishing() )
            {
                $time_expression               = self::getTimeInfo( $site );           
                $this->results[ Site::TYPE ][] = $site->getName() . ", " . $time_expression;
            }
        }
        
        return $this->results;
    }

    public function setRootContainer( Container $root )
    {
        $this->root = $root;
        return $this;
    }
    
    public function setRootFolder( Folder $root )
    {
        return $this->setRootContainer( $root );
    }
        
    public function reportStartDateAfter( \DateTime $dt=NULL, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( isset( $this->results[ $type ] ) && isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }

    public function reportStartDateBefore( \DateTime $dt=NULL, $type=Page::TYPE, $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( isset( $this->results[ $type ] ) && isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }

    /* ===== static methods ===== */
    
    public static function assetTreeReportDate(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'date' ] ) )
            throw new e\ReportException(
                S_SPAN . "The date is not set. " . E_SPAN );
        $date   = $params[ 'date' ];
        $cache  = $params[ 'cache' ];
    
        $type   = $child->getType();
        $path   = $child->getPathPath();
                
        $asset            = $cache->retrieveAsset( $child );
        $metadata         = $asset->getMetadata();
        $end_date         = $metadata->getEndDate();
        $review_date      = $metadata->getReviewDate();
        $start_date       = $metadata->getStartDate();
        
        if( isset( $end_date ) )
        {
            $end_date_obj = new \DateTime( $end_date );
            
            if( $end_date_obj < $date )
                $results[ $type ][ 'reportEndDateBefore' ][] = $path;
            else
                $results[ $type ][ 'reportEndDateAfter' ][] = $path;
        }
        
        if( isset( $review_date ) )
        {
            $review_date_obj = new \DateTime( $review_date );
            
            if( $review_date_obj < $date )
                $results[ $type ][ 'reportReviewDateBefore' ][] = $path;
            else
                $results[ $type ][ 'reportReviewDateAfter' ][] = $path;
        }
        
        if( isset( $start_date ) )
        {
            $start_date_obj = new \DateTime( $start_date );
            
            if( $start_date_obj < $date )
                $results[ $type ][ 'reportStartDateBefore' ][] = $path;
            else
                $results[ $type ][ 'reportStartDateAfter' ][] = $path;
        }
    }
    
    public static function assetTreeReportLast(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'method' ] ) )
            throw new e\ReportException(
                S_SPAN . "The method is not set. " . E_SPAN );
        $method = $params[ 'method' ];
        
        if( !isset( $params[ 'day' ] ) )
            throw new e\ReportException(
                S_SPAN . "The day is not set. " . E_SPAN );
        $day = $params[ 'day' ];
        $day = intval( $day );
        
        if( !isset( $params[ 'direction' ] ) )
            throw new e\ReportException(
                S_SPAN . "The direction is not set. " . E_SPAN );
        $direction = $params[ 'direction' ];
        
        if( $direction != c\T::FORWARD && $direction != c\T::BACKWARD )
            throw new e\ReportException(
                S_SPAN . "The direction $direction is not acceptable. " . E_SPAN );
        
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
        $cache = $params[ 'cache' ];
    
        $type = $child->getType();
        
        if( $type != File::TYPE && $type != Page::TYPE )
            return;
        
        $asset = $cache->retrieveAsset( $child );
        
        // make sure method exist
        if( !method_exists( $asset, $method ) )
        {
            throw new e\ReportException(
                S_SPAN . "The method $method does not exist." . E_SPAN );
        }
            
        // compare days
        $today = new \DateTime();
        $date  = new \DateTime( $asset->$method() );
        
        $interval = $today->diff( $date );
        $interval = abs( intval( $interval->format( '%R%a' ) ) );
        if( self::DEBUG ) { u\DebugUtility::out( $interval ); }
        
        // forward: newer than
        if( $direction == c\T::FORWARD && $interval <= $day )
            $results[ $type ][] = $child->getPathPath();
        else if( $direction == c\T::BACKWARD && $interval >= $day )
            $results[ $type ][] = $child->getPathPath();
    }
    
    public static function assetTreeReportMetadataWiredFieldsContains(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'substring' ] ) )
            throw new e\ReportException(
                S_SPAN . "The substring is not set. " . E_SPAN );
        $substring = $params[ 'substring' ];
        $cache     = $params[ 'cache' ];
    
        $type      = $child->getType();
        $path      = $child->getPathPath();
                
        $asset            = $cache->retrieveAsset( $child );
        $metadata         = $asset->getMetadata();
        $author           = $metadata->getAuthor();
        $display_name     = $metadata->getDisplayName();
        $keywords         = $metadata->getKeywords();
        $meta_description = $metadata->getMetaDescription();
        $summary          = $metadata->getSummary();
        $teaser           = $metadata->getTeaser();
        $title            = $metadata->getTitle();

        // search for substring
        if( isset( $author ) && $author != "" && strpos( $author, $substring ) !== false )
            $results[ $type ][ 'reportAuthorContains' ][] = $path;

        if( isset( $display_name ) && $display_name != "" && strpos( $display_name, $substring ) !== false )
            $results[ $type ][ 'reportDisplayNameContains' ][] = $path;
        
        if( isset( $keywords ) && $keywords != "" && strpos( $keywords, $substring ) !== false )
            $results[ $type ][ 'reportKeywordsContains' ][] = $path;

        if( isset( $meta_description ) && $meta_description != "" && strpos( $meta_description, $substring ) !== false )
            $results[ $type ][ 'reportMetaDescriptionContains' ][] = $path;

        if( isset( $summary ) && $summary != "" && strpos( $summary, $substring ) !== false )
            $results[ $type ][ 'reportSummaryContains' ][] = $path;

        if( isset( $teaser ) && $teaser != "" && strpos( $teaser, $substring ) !== false )
            $results[ $type ][ 'reportTeaserContains' ][] = $path;

        if( isset( $title ) && $title != "" && strpos( $title, $substring ) !== false )
            $results[ $type ][ 'reportTitleContains' ][] = $path;
    }
    
    public static function assetTreeReportMetadataWiredFields(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'max' ] ) )
            throw new e\ReportException(
                S_SPAN . "The maximum is not set. " . E_SPAN );
        $max    = $params[ 'max' ];
        $cache  = $params[ 'cache' ];
    
        $type   = $child->getType();
        $path   = $child->getPathPath();
                
        $asset            = $cache->retrieveAsset( $child );
        $metadata         = $asset->getMetadata();
        $author           = $metadata->getAuthor();
        $display_name     = $metadata->getDisplayName();
        $end_date         = $metadata->getEndDate();
        $keywords         = $metadata->getKeywords();
        $meta_description = $metadata->getMetaDescription();
        $review_date      = $metadata->getReviewDate();
        $start_date       = $metadata->getStartDate();
        $summary          = $metadata->getSummary();
        $teaser           = $metadata->getTeaser();
        $title            = $metadata->getTitle();

        if( isset( $display_name ) )
            $display_name_len = strlen( $title );
        else
            $display_name_len = 0;

        if( isset( $title ) )
            $title_len = strlen( $title );
        else
            $title_len = 0;        
        
        //if( self::DEBUG ) { u\DebugUtility::out( $title . ": " . $len ); }
        
        // length
        if( $title_len > $max )
            $results[ $type ][ 'reportLongTitle' ][] = $path;
        if( $display_name_len > $max )
            $results[ $type ][ 'reportLongDisplayName' ][] = $path;

        // content
        if( isset( $author ) && $author != "" )
            $results[ $type ][ 'reportHasAuthor' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoAuthor' ][] = $path;

        if( isset( $display_name ) && $display_name != "" )
            $results[ $type ][ 'reportHasDisplayName' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoDisplayName' ][] = $path;

        if( isset( $end_date ) && $end_date != "" )
            $results[ $type ][ 'reportHasEndDate' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoEndDate' ][] = $path;
        
        if( isset( $keywords ) && $keywords != "" )
            $results[ $type ][ 'reportHasKeywords' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoKeywords' ][] = $path;

        if( isset( $meta_description ) && $meta_description != "" )
            $results[ $type ][ 'reportHasMetaDescription' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoMetaDescription' ][] = $path;

        if( isset( $review_date ) && $review_date != "" )
            $results[ $type ][ 'reportHasReviewDate' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoReviewDate' ][] = $path;

        if( isset( $start_date ) && $start_date != "" )
            $results[ $type ][ 'reportHasStartDate' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoStartDate' ][] = $path;

        if( isset( $summary ) && $summary != "" )
            $results[ $type ][ 'reportHasSummary' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoSummary' ][] = $path;    

        if( isset( $teaser ) && $teaser != "" )
            $results[ $type ][ 'reportHasTeaser' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoTeaser' ][] = $path;

        if( isset( $title ) && $title != "" )
            $results[ $type ][ 'reportHasTitle' ][] = $path;
        else
            $results[ $type ][ 'reportHasNoTitle' ][] = $path;
    }

    public static function assetTreeReportNumberOfAssets(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        $type             = $child->getType();
        $results[ $type ] = $results[ $type ] + 1;
    }

    public static function assetTreeReportOrphanFiles(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
        $cache = $params[ 'cache' ];    
            
        $type  = $child->getType();
        
        if( $type != File::TYPE )
            return;
        
        $subscribers = $cache->retrieveAsset( $child )->getSubscribers();
        
        if( $subscribers == NULL )
        {
            $results[ File::TYPE ][] = $child->getPathPath();
        }
    }
    
    public static function assetTreeReportPageFieldValue(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'field-value' ] ) )
            throw new e\ReportException(
                S_SPAN . "The \$field-value array is not set. " . E_SPAN );
        
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
        $cache = $params[ 'cache' ];
        
        $type = $child->getType();
        
        // skip irrelevant children
        if( $type != Page::TYPE && $type != Folder::TYPE )
            return;
        
        $field_value = $params[ 'field-value' ];
        
        if( !is_array( $field_value ) )
            throw new e\ReportException(
                S_SPAN . "The \$field-value array is not set. " . E_SPAN );
            
        $count = count( $field_value );
        
        //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $field_value ); }
        //if( self::DEBUG ) { u\DebugUtility::out( "Count: $count" ); }
        
        if( $count == 0 )
            return;
        else if( $count > 1 )
        {
            if( isset( $params[ 'disjunctive' ] ) )
                $disjunctive = $params[ 'disjunctive' ];
            else
                $disjunctive = true;
        }
        
        $page     = $cache->retrieveAsset( $child );
        $metadata = $page->getMetadata();
    
        if( $count == 1 )
        {
            $identifier_value = $field_value[ 0 ];
            $keys             = array_keys( $identifier_value );
            $identifier       = $keys[ 0 ];
            $value            = trim( $identifier_value[ $identifier ] );
            
            // wired fields
            if( Metadata::isWiredField( $identifier ) )
            {
                $method = Metadata::getWiredFieldMethodName( $identifier );
                
                //if( self::DEBUG ) { u\DebugUtility::out( "A wired field." ); }
                //if( self::DEBUG ) { u\DebugUtility::out( $method ); }
                
                if( $value == "" )
                    $value = NULL;
                    
                $text = $metadata->$method();
                //if( self::DEBUG ) { u\DebugUtility::out( "Text: ". $text ); }    
                //if( self::DEBUG ) { u\DebugUtility::out( is_null( $metadata->$method() ) ? 'NULL' :  ); }
                
                if( $metadata->$method() == $value )
                {
                    if( self::DEBUG ) { u\DebugUtility::out( "Found a page" ); }
                    $results[ Page::TYPE ][] = $child->getPathPath();
                    return;
                }
                else
                    return;
            }
            // dynamic field
            else if( $metadata->hasDynamicField( $identifier ) )
            {
                //if( self::DEBUG ) { u\DebugUtility::out( "Dynamic field found" ); }
                $values = $metadata->getDynamicFieldValues( $identifier );
                
                if( $value == "" ) // this is not necessary
                    $value = NULL;
                    
                if( in_array( $value, $values ) )
                {
                    $results[ Page::TYPE ][] = $child->getPathPath();
                    return;
                }
                else
                    return;
            }
            else
                return;
        }
        else // count > 1
        {
            //if( self::DEBUG ) { u\DebugUtility::out( "Count more than 1" ); }
            
            if( $disjunctive ) // or
            {
                // pass any test
                foreach( $field_value as $field_value_pair )
                {
                    if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $field_value_pair ); }
                    $keys       = array_keys( $field_value_pair );
                    $identifier = $keys[ 0 ];
                    $value      = trim( $field_value_pair[ $identifier ] );

                    // wired fields
                    if( Metadata::isWiredField( $identifier ) )
                    {
                        $method = Metadata::getWiredFieldMethodName( $identifier );
                
                        if( $metadata->$method() == $value )
                        {
                            $results[ Page::TYPE ][] = $child->getPathPath();
                            return;
                        }
                        else
                            continue;
                    }
                    // dynamic field
                    else if( $metadata->hasDynamicField( $identifier ) )
                    {
                        $values = $metadata->getDynamicFieldValues( $identifier );
                        if( in_array( $value, $values ) )
                        {
                            $results[ Page::TYPE ][] = $child->getPathPath();
                            return;
                        }
                        else
                            continue;
                    }
                    else
                        return;
                }
            }
            else // and
            {
                //if( self::DEBUG ) { u\DebugUtility::out( "Conjunctive" ); }
                //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $field_value ); }
                
                // must pass all the tests
                foreach( $field_value as $field_value_pair )
                {
                    //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $field_value_pair ); }
                    $keys       = array_keys( $field_value_pair );
                    $identifier = $keys[ 0 ];
                    $value      = trim( $field_value_pair[ $identifier ] );
                    
                    //if( self::DEBUG ) { u\DebugUtility::out( "Identifier: $identifier" ); }
                    //if( self::DEBUG ) { u\DebugUtility::out( "Value: $value" ); }
                
                    // wired fields
                    if( Metadata::isWiredField( $identifier ) )
                    {
                        $method = Metadata::getWiredFieldMethodName( $identifier );
                
                        if( $metadata->$method() == $value )
                        {
                            continue;
                        }
                        else
                            return;
                    }
                    // dynamic field
                    else if( $metadata->hasDynamicField( $identifier ) )
                    {
                        $values = $metadata->getDynamicFieldValues( $identifier );
                        if( in_array( $value, $values ) )
                        {
                            continue;
                        }
                        else
                            return;
                    }
                    else
                        return;
                }
                $results[ Page::TYPE ][] = $child->getPathPath();
            }
        }
    }
   
    public static function assetTreeReportPageNodeValue(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'node-value' ] ) )
            throw new e\ReportException(
                S_SPAN . "The \$node-value array is not set. " . E_SPAN );
        
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
        $cache = $params[ 'cache' ];
        
        $type = $child->getType();
        
        // skip irrelevant children
        if( $type != Page::TYPE && $type != Folder::TYPE )
            return;
            
        $node_value = $params[ 'node-value' ];
        
        if( !is_array( $node_value ) )
            throw new e\ReportException(
                S_SPAN . "The \$node-value array is not set. " . E_SPAN );
            
        $count = count( $node_value );
        
        //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $node_value ); }
        //if( self::DEBUG ) { u\DebugUtility::out( "Count: $count" ); }
        
        if( $count == 0 )
            return;
        else if( $count > 1 )
        {
            if( isset( $params[ 'disjunctive' ] ) )
                $disjunctive = $params[ 'disjunctive' ];
            else
                $disjunctive = true;
        }
        
        $page = $cache->retrieveAsset( $child );
        
        // skip xhtml pages
        if( !$page->hasStructuredData() )
            return;
    
        if( $count == 1 )
        {
            $identifier_value = $node_value[ 0 ];
            $keys             = array_keys( $identifier_value );
            $identifier       = $keys[ 0 ];
            $value            = trim( $identifier_value[ $identifier ] );

            // match a node
            if( $page->hasNode( $identifier ) && $page->isTextNode( $identifier ) )
            {
                // empty node value
                if( $value == "" && $page->getText( $identifier ) == $value )
                {
                    $results[ Page::TYPE ][] = $child->getPathPath();
                }
                // non-empty substring
                else if( $value != "" && strpos( $page->getText( $identifier ), $value ) !== false )
                {
                    $results[ Page::TYPE ][] = $child->getPathPath();
                }
            }
            else
                return;
        }
        else // count > 1
        {
            if( self::DEBUG ) { u\DebugUtility::out( "Count more than 1" ); }
            
            if( $disjunctive ) // or
            {
                // pass any test
                foreach( $node_value as $node_value_pair )
                {
                    if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $node_value_pair ); }
                    $keys       = array_keys( $node_value_pair );
                    $identifier = $keys[ 0 ];
                    $value      = trim( $node_value_pair[ $identifier ] );

                    if( $page->hasNode( $identifier ) && $page->isTextNode( $identifier ) )
                    {    
                        if( self::DEBUG ) { u\DebugUtility::out( "Matched a node" ); }
                        if( self::DEBUG ) { u\DebugUtility::out( "Identifier: $identifier" ); }
                        if( self::DEBUG ) { u\DebugUtility::out( "Value: $value" ); }
                        // different
                        if( ( $value == "" && $page->getText( $identifier ) != $value )
                            ||
                            // not a substring
                            ( $value != "" && strpos( $page->getText( $identifier ), $value ) === false ) )
                        {
                            if( self::DEBUG ) { u\DebugUtility::out( 
                            "\$page->getText: " . $page->getText( $identifier ) . BR .
                            "Value not matched. Continue." ); }
                            continue; // check next pair
                        }
                        // early exit for or
                        else
                        {
                            $results[ Page::TYPE ][] = $child->getPathPath();
                            return; // found
                        }
                    }
                    else
                        continue; // check next pair
                }
            }
            else // and
            {
                if( self::DEBUG ) { u\DebugUtility::out( "Conjunctive" ); }
                if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $node_value ); }
                
                // must pass all the tests
                foreach( $node_value as $node_value_pair )
                {
                    if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $node_value_pair ); }
                    $keys       = array_keys( $node_value_pair );
                    $identifier = $keys[ 0 ];
                    $value      = trim( $node_value_pair[ $identifier ] );
                    
                    //if( self::DEBUG ) { u\DebugUtility::out( "Identifier: $identifier" ); }
                    //if( self::DEBUG ) { u\DebugUtility::out( "Value: $value" ); }
                
                    if( $page->hasNode( $identifier ) &&
                        $page->isTextNode( $identifier ) )
                    {
                        if( self::DEBUG ) { u\DebugUtility::out( "Matched a node" ); }
                        
                        // both should be empty to match
                        if( $value == "" && $page->getText( $identifier ) != "" )
                        {
                            //if( self::DEBUG ) { u\DebugUtility::out( "Empty value not matched" ); }
                            return;
                        }
                        // non-empty substring not found
                        else if( $value != "" && strpos( $page->getText( $identifier ), $value ) === false )
                        {
                            //if( self::DEBUG ) { u\DebugUtility::out( "Value not matched" ); }
                            return;
                        }
                        
                        //if( self::DEBUG ) { u\DebugUtility::out( "Continue" ); }
                    }
                    else
                        return;
                }
                $results[ Page::TYPE ][] = $child->getPathPath();
            }
        }
    }    
   
    public static function assetTreeReportPublishable(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
            
        // set up cache
        $cache = $params[ 'cache' ];
        // publishable or unpublishable
        $publishable = true; // the default
        
        if( is_array( $params ) && isset( $params[ 'publishable' ] ) )
            $publishable = $params[ 'publishable' ];
        
        // skip irrelevant children
        $type  = $child->getType();
        
        if( $type != Folder::TYPE && $type != Page::TYPE && $type != File::TYPE )
            return;
        
        $path  = $child->getPathPath();
        if( self::DEBUG ) { u\DebugUtility::out( "Path: " . $path ); }
        
        $asset = $cache->retrieveAsset( $child );
        
        if( $publishable )
        {
            if( $asset->isPublishable() )
            {
                //if( self::DEBUG ) { u\DebugUtility::out( "Publishable path: " . $asset->getPath() ); }
                $results[ $type ][] = $path;
            }
        }
        else
        {
            if( !$asset->isPublishable() )
            {
                //if( self::DEBUG ) { u\DebugUtility::out( "Publishable path: " . $asset->getPath() ); }
                $results[ $type ][] = $path;
            }
        }
    }
    
    public static function assetTreeReportRelativeLink(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
        $cache = $params[ 'cache' ];
    
        $type = $child->getType();
        
        if( $type != DataDefinitionBlock::TYPE && $type != Page::TYPE && $type != File::TYPE )
            return;
        
        $asset    = $cache->retrieveAsset( $child );
        
        // .css and .js only
        // example: href="/com/index.php
        $pattern1 = "/href=[\"']\/(\S)+\.php[\"']/";
        // example: /com/index.php
        $pattern2 = "/^\/(\S)+\.php$/";
        
        if( $type == File::TYPE )
        {
            $filename = $asset->getName();
            
            if( u\StringUtility::endsWith( $filename, '.css' ) || u\StringUtility::endsWith( $filename, '.js' ) )
            {
                $pattern3 = "/url\(\//";
                $pattern4 = "/url\(\"\//";
                $pattern5 = "/url\('\//";
                
                $matches = array();
                preg_match( $pattern3, $asset->getData(), $matches );
                if( isset( $matches[ 0 ] ) )
                {
                    $results[ $type ][] = $child->getPathPath();
                    return;
                }
                $matches = array();
                preg_match( $pattern4, $asset->getData(), $matches );
                if( isset( $matches[ 0 ] ) )
                {
                    $results[ $type ][] = $child->getPathPath();
                    return;
                }
                $matches = array();
                preg_match( $pattern5, $asset->getData(), $matches );
                if( isset( $matches[ 0 ] ) )
                {
                    $results[ $type ][] = $child->getPathPath();
                    return;
                }
            }
        }
        else if( $asset->hasStructuredData() ) // associated with a data definition
        {
            $identifiers = $asset->getIdentifiers();
            $count       = count( $identifiers );
        
            if( $count > 0 )
            {
                foreach( $identifiers as $identifier )
                {
                    if( $asset->isWYSIWYG( $identifier ) ) // WYSIWYG
                    {
                        $matches = array();
                        preg_match( $pattern1, $asset->getText( $identifier ), $matches );
                    
                        if( isset( $matches[ 0 ] ) )
                        {
                            $results[ $type ][] = $child->getPathPath();
                            return;
                        }
                    }
                    else // other text nodes
                    {
                        $matches = array();
                        preg_match( $pattern2, $asset->getText( $identifier ), $matches );
                    
                        if( isset( $matches[ 0 ] ) )
                        {
                            $results[ $type ][] = $child->getPathPath();
                            return;
                        }
                    }
                }
            }
        }
        else
        {
            $matches = array();
            preg_match( $pattern1, $asset->getXhtml(), $matches );
            
            if( isset( $matches[ 0 ] ) )
            {
                $results[ $type ][] = $child->getPathPath();
                return;
            }
        }
    }
    
    public static function assetTreeReportScheduledPublishDestination(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
        $cache = $params[ 'cache' ];
    
        $path = $child->getPathPath();
        $type = $child->getType();
        
        if( $type != Destination::TYPE )
            return;
            
        $d = $cache->retrieveAsset( $child );
        
        if( $d->getUsesScheduledPublishing() && $d->getEnabled() )
        {
            $time_expression    = self::getTimeInfo( $d );           
            $site               = $d->getSiteName();
            $results[ $type ][] = $site . ":" . $path . ", " . $time_expression;
        }
    }

    public static function assetTreeReportScheduledPublishPublishSet(
        aohs\AssetOperationHandlerService $service, p\Child $child, $params=NULL, &$results=NULL )
    {
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );
        $cache = $params[ 'cache' ];
    
        $path = $child->getPathPath();
        $type = $child->getType();
        
        if( $type != PublishSet::TYPE )
            return;
            
        $ps = $cache->retrieveAsset( $child );
        
        if( $ps->getUsesScheduledPublishing() )
        {
            $time_expression    = self::getTimeInfo( $ps );           
            $site               = $ps->getSiteName();
            $results[ $type ][] = $site . ":" . $path . ", " . $time_expression;
        }
    }
    
    private function checkRootFolder()
    {
        if( !isset( $this->root ) )
            throw new e\ReportException(
                S_SPAN . c\M::ROOT_FOLDER_NOT_SET . E_SPAN );
    }
    
    private static function getTimeInfo( ScheduledPublishing $sp )
    {
        $time_to_publish        = $sp->getTimeToPublish();
        $publish_interval_hours = $sp->getPublishIntervalHours();
        $publish_days_of_week   = $sp->getPublishDaysOfWeek();
        $cron_expression        = $sp->getCronExpression();
        $time_expression        = "";
        
        if( isset( $publish_days_of_week ) && sizeof( $publish_days_of_week ) )
        {
            $time_expression = "every ";
            $days            = $publish_days_of_week->dayOfWeek;
            
            if( is_array( $days ) )
                foreach( $days as $day )
                {
                    $time_expression .= $day . ", ";
                }
            $time_expression .= "at " . $time_to_publish;
        }
        else if( isset( $publish_interval_hours ) )
        {
            $time_expression = "every $publish_interval_hours hours";
        }
        else if( isset( $cron_expression ) )
        {
            $time_expression = $cron_expression;
        }
        
        return $time_expression;
    }

    private $cascade;
    private $cache;
    private $root;
    private $results;
}