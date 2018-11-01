<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/31/2017 Added documentation and minor bug fixes.
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

/**
<documentation>
<description><h2>Introduction</h2>
<p>The <code>Report</code> class is no more than a class containing a few reporting methods, encapsulating the <code>AsseTree::traverse</code> method with various parameters, and the required global functions.</p>
<p>Internally, this class uses the <code>u\Cache</code> class to store all asset objects retrieve from Cascade.</p>
<p>Note that I have drawn a distinction between <code>node</code> and <code>field</code> in this class. Nodes refer to data nodes in a structured data, associated with blocks and pages. Fields refer to wired and dynamic fields in a metadata, associated with blocks, files, pages, and so on. This distinction is maintained in method names like <code>Report::reportPageFieldEmptyValue</code> and <code>Report::reportPageNodeEmptyValue</code>.</p>
<h2>Instantiating the Object</h2>
<p>I added the following line to my authentication file:</p>
<pre class="code">    $report  = new Report( $cascade );
</pre>
<p>This line should appear after the instantiation of the <code>Cascade</code> object.</p>
<h2>Working With Wired Fields of Metadata</h2>
<p>Every now and then we may want to generate reports, based on values (or absence of values) in various wired fields of metadata associated with assets. In the <code>Report</code> class there are a few methods that can be used to generate reports of this type.</p>
<p>Traversing a folder and retrieving assets can be time-consuming. When working with the <code>Report</code> class, this overhead is reduced considerably due to the use of a cache. But still, if we are interested in various wired fields at the same time, the <code>Report</code> class should allow for the possibility of gathering information of various types in one traversal to avoid overhead. There are two methods that involve tree traversal and can be called individually:</p>
<ul>
<li><code>reportDate( DateTime $dt )</code>: returns the <code>$results</code> array, storing information of assets related to the three date fields: <code>endDate</code>, <code>reviewDate</code>, and <code>startDate</code>.</li>
<li><code>reportMetadataWiredFields( int $max_num_of_char=1, string $substring="" )</code>: returns the <code>$results</code> array, storing metadata information of assets.</li>
</ul>
<h2><code>reportMetadataWiredFields</code></h2>
<p>This method gathers information of the following types:</p>
<ul>
<li>Metadata information of blocks, files, folders, pages, and symlinks</li>
<li>Paths of assets that either contain or do not contain values in various wired fields</li>
<li>Paths of assets that have titles and display names longer than the specified number of characters</li>
<li>When a substring is passed in, paths of assets whose metadata field values contain the substring</li>
</ul>
<p>There are related methods that work with <code>reportMetadataWiredFields</code> and retrieve only the relevant information. These methods are:</p>
<ul>
<li>There is a set of methods generated in <code>__call</code>, like <code>reportHasAuthor</code> (returning paths of assets that contains a value in the author field) and <code>reportHasNoAuthor</code></li>
<li>There is a second set of methods generated in <code>__call</code>, like <code>reportDisplayNameContains</code>, which return paths of assets whose various text field values contain the specified substring</li>
</ul>
<p>Let us look at a few examples.</p>
<h2>Use Case 1: Generating a Report on Pages with Long Titles</h2>
<p>Here we are interested in only one thing: pages with long titles. We decide that a title containing 26 or more characters is considered a long title. Since we are interested in one type of information only, we can call <code>Report::reportLongTitle( int $max_num_of_char=1, string $type=Page::TYPE, bool $retraverse=false )</code> directly. This method accepts three parameters:</p>
<ul>
<li><code>$max_num_of_char</code>: the criterion of being long. Any title containing more characters than this number is considered long.</li>
<li><code>$type</code>: the type of assets we are interested in. It is defaulted to <code>Page::TYPE</code>. But we can use <code>FeedBlock::TYPE</code> for <code>$type</code>, for example.</li>
<li><code>$retraverse</code>: when set to true, the method calls <code>reportMetadataWiredFields</code>. When <code>reportDisplayNameContains</code> is called alone, by itself there is no tree traversal involved. To invoke tree traversal, we need to make sure that <code>reportMetadataWiredFields</code> is called too. By passing in a <code>true</code> value for the parameter <code>$retraverse</code>, <code>reportMetadataWiredFields</code> will be called.</li>
</ul>
<p>Here is the code that calls <code>reportLongTitle</code>:</p>
<pre class="code">
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        '636ff42c8b7f08ee226116ff6b335718' )
                )->reportLongTitle( 25, a\Page::TYPE, true );
                    
            echo S_H2 . "Pages with Long Titles" . E_H2;
            u\DebugUtility::dump( $results );
</pre>
<p>This method call returns an array of the following type:</p>
<pre>array(2) {
  [0]=>
  string(63) "web-services/api/asset-classes/page-configuration-set-container"
  [1]=>
  string(60) "web-services/api/asset-classes/workflow-definition-container"
}
</pre>
<p>Behind the scene, this method call invokes <code>reportMetadataWiredFields</code>, and returns only a small part of the result: the part involving pages with long titles only.</p>
<h2>Use Case 2: Generating a Report on Pages with Long Titles and Pages with Long Display Names</h2>
<p>We may want to get a report on both long titles and long display names. Surely we can traverse the folder twice to generate such a report. But it is more efficient to call <code>reportMetadataWiredFields</code> once, followed by calls to <code>reportLongTitle</code> and <code>reportLongDisplayName</code> without invoking <code>reportMetadataWiredFields</code> again. Below shows the code to generate such a report. I have also included time required to execute the code in various ways:</p>
<pre class="code">            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        '636ff42c8b7f08ee226116ff6b335718' )
                )->reportMetadataWiredFields( 25 );
               
            echo S_H2 . "Pages with Long Title" . E_H2;
            u\DebugUtility::dump( $report->reportLongTitle() ); // 24 seconds
        
            echo S_H2 . "Pages with Long Display Names" . E_H2;
            u\DebugUtility::dump( $report->reportLongDisplayName() ); // 1 second
    
            $report-&gt;clearResults();
    
            // separate call to reportLongDisplayName: 24 seconds
            u\DebugUtility::dump(
                $report->
                    setRootFolder( 
                        $cascade->getFolder( 
                            '636ff42c8b7f08ee226116ff6b335718' )
                    )->reportLongDisplayName( 25, a\Page::TYPE, true ) );
</pre>
<h2>Use Case 3: Generating a Report on Pages with Empty Fields</h2>
<p>We want to generate a report on pages with missing values in various fields. Here is the code:</p>
<pre class="code">
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        '636ff42c8b7f08ee226116ff6b335718' )
                )->reportMetadataWiredFields( 25 );
               
            echo S_H2 . "Pages with Long Title" . E_H2;
            // 24 seconds
            u\DebugUtility::dump( $report->reportLongTitle() ); 
        
            echo S_H2 . "Pages with Long Display Names" . E_H2;
             // 1 second
            u\DebugUtility::dump( $report->reportLongDisplayName() );
            
            echo S_H2 . "Pages with No Author" . E_H2;
            // 0 seconds
            u\DebugUtility::dump( $report->reportHasNoAuthor( a\Page::TYPE ) ); 
            
            echo S_H2 . "Pages with No Display Name" . E_H2;
            // 0 seconds
            u\DebugUtility::dump( $report->reportHasNoDisplayName( a\Page::TYPE ) ); 
</pre>
<h2>Use Case 4: Generating a Report on Pages with Fields Containing Substrings</h2>
<p>There are seven methods that can be used to gather asset information where a text field contains some substring. These methods are:</p>
<ul>
<li><code>reportAuthorContains</code></li>
<li><code>reportDisplayNameContains</code></li>
<li><code>reportKeywordsContains</code></li>
<li><code>reportMetaDescriptionContains</code></li>
<li><code>reportSummaryContains</code></li>
<li><code>reportTeaserContains</code></li>
<li><code>reportTitleContains</code></li>
</ul>
<p>When a non-empty string is passed into <code>reportMetadataWiredFields</code> for <code>$substring</code>, the substring is used to check the seven text fields to see if the values therein contain the substring. To generate a report on pages whose author fields contain the string "nw", we can use the following code:</p>
<pre class="code">
            // get pages with authors containing a substring
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        'edcomm/web/not-in-use-classes/showcase-old', 'imt-intra' )
                // the first time, need to pass in the substring
                // and to retraverse, hence true
                )->reportAuthorContains( a\Page::TYPE, true, "nw" );

</pre>
<p>Again, when a method like this is called alone, there is no need to call <code>reportMetadataWiredFields</code>. But then we have to pass in a <code>true</code> for the parameter <code>$retraverse</code> to force the invocation of <code>reportMetadataWiredFields</code>.</p>
<h2><code>reportDate</code></h2>
<p>This method gathers information related to <code>endDate</code>, <code>reviewDate</code>, and <code>startDate</code>. There are six methods defined in <code>Report</code> that work with <code>reportDate</code>:</p>
<ul>
<li><code>reportEndDateAfter</code></li>
<li><code>reportEndDateBefore</code></li>
<li><code>reportReviewDateAfter</code></li>
<li><code>reportReviewDateBefore</code></li>
<li><code>reportStartDateAfter</code></li>
<li><code>reportStartDateBefore</code></li>
</ul>
<p>These six methods all have the same signature. For example, consider <code>reportEndDateAfter( DateTime $dt=NULL, string $type=Page::TYPE, bool $retraverse=false )</code>. There are three parameters:</p>
<ul>
<li><code>$dt</code>: the <code>DateTime</code> object used for cutting between before and after.</li>
<li><code>$type</code>: the type of assets to be considered.</li>
<li><code>$retraverse</code>: when set to <code>true</code>, invokes <code>reportDate</code> internally.</li>
</ul>
<p>Note that in these six methods, <code>Before</code> means any date before the date passed in, exclusively, whereas <code>After</code> includes the date passed in. For example, if "2014-08-18" is passed in, then <code>Before</code> means "2014-08-17" and before, whereas <code>After</code> means "2014-08-18" and after.</p>
<h2>Use Case 5: Generating a Report on Pages with Various Date Information</h2>
<pre class="code">            $date = new DateTime('2011-01-01T00:00:00.012345Z');
            
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( '2c7a19888b7f08ee603d6820e25d3dc9' )
                // called the first time, retraversal required, hence true
                )->reportStartDateBefore( $date, a\Page::TYPE, true );
            
            echo S_H2 . "Pages with End Date Set to Before 1/1/2011" . E_H2;
            u\DebugUtility::dump( $results );
            
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( '2c7a19888b7f08ee603d6820e25d3dc9' )
                // no new DateTime, retraversal not needed, hence false
                )->reportStartDateAfter( $date, a\Page::TYPE, false );
            
            echo S_H2 . "Pages with End Date Set to 1/1/2011 or After" . E_H2;
            u\DebugUtility::dump( $results );
</pre>
</description>
<postscript><h2>Test Code</h2>
<ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/report.php">report.php</a></li></ul>
<h2>Recipes</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/tree/master/recipes/report">report</a></li></ul></postscript>
</documentation>
*/
class Report
{
    const DEBUG = false;
    const DUMP  = false;

/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( Cascade $cascade )
    {
        $this->cascade = $cascade;
        $this->clearResults();
        $this->cache = u\Cache::getInstance( $cascade->getService() );
        $this->cache->clearCache();
    }
        
/**
<documentation><description><p>Dynamically generates the following methods: <code>reportHasAuthor</code>, <code>reportHasNoAuthor</code>, <code>reportHasDisplayName</code>, <code>reportHasNoDisplayName</code>,
<code>reportHasEndDate</code>, <code>reportHasNoEndDate</code>, <code>reportHasKeywords</code>, <code>reportHasNoKeywords</code>,
<code>reportHasMetaDescription</code>, <code>reportHasNoMetaDescription</code>, <code>reportHasReviewDate</code>, <code>reportHasNoReviewDate</code>,
<code>reportHasStartDate</code>, <code>reportHasNoStartDate</code>, <code>reportHasSummary</code>, <code>reportHasNoSummary</code>,
<code>reportHasTeaser</code>, <code>reportHasNoTeaser</code>, <code>reportHasTitle</code>, <code>reportHasNoTitle</code>, <code>reportAuthorContains</code>,
<code>reportDisplayNameContains</code>, <code>reportKeywordsContains</code>, <code>reportMetaDescriptionContains</code>, <code>reportSummaryContains</code>,
<code>reportTeaserContains</code>, and <code>reportTitleContains</code>.</p>
<p>There are two groups of methods here:</p>
<ul>
<li>Methods that do not involve substrings</li>
<li>Methods, whose names has a <code>Contains</code> suffix, that involve searching for a non-empty substring in a field</li>
</ul>
<p>For the first group, such a method requires two parameters. For example, <code>reportHasAuthor( string $type, bool $retraverse )</code>. The first parameter should be a type string like <code>a\Page::TYPE</code>. The bool value is used to control retraversal. If no searching of a substring is required, then set <code>$retraverse</code> to <code>true</code> for the first time, and <code>false</code> after that.</p>
<p>For the second group, such a method requires three parameters. For example, <code>reportDisplayNameContains( string $type, bool $retraverse, string $needle )</code>. The <code>$needle</code> variable should be a non-empty string to be searched for in the relevant field. Normally, when such a method is invoked, <code>$retraverse</code> should be set to <code>true</code>.</p>

<p>When a method in this set is called, an array of the following type will be generated after the traversal:</p>
<pre>
array(9) {
  ["block_XHTML_DATADEFINITION"]=>
  array(0) {
  }
  ["block_FEED"]=>
  array(0) {
  }
  ["file"]=>
  array(10) {
    ["reportHasNoAuthor"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoDisplayName"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoEndDate"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoKeywords"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoMetaDescription"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoReviewDate"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoStartDate"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoSummary"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoTeaser"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
    ["reportHasNoTitle"]=>
    array(1) {
      [0]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/flowers.txt"
    }
  }
  ["folder"]=>
  array(10) {
    ["reportHasNoAuthor"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasDisplayName"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoEndDate"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoKeywords"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoMetaDescription"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoReviewDate"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoStartDate"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoSummary"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoTeaser"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
    ["reportHasNoTitle"]=>
    array(1) {
      [0]=>
      string(42) "edcomm/web/not-in-use-classes/showcase-old"
    }
  }
  ["block_INDEX"]=>
  array(0) {
  }
  ["page"]=>
  array(19) {
    ["reportLongTitle"]=>
    array(11) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [2]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [3]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [4]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [5]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [6]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [7]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [8]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [9]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [10]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportLongDisplayName"]=>
    array(10) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [2]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [3]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [4]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [5]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [6]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [7]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [8]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [9]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasAuthor"]=>
    array(1) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
    }
    ["reportHasDisplayName"]=>
    array(11) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [2]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [3]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [4]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [5]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [6]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [7]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [8]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [9]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [10]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasEndDate"]=>
    array(1) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
    }
    ["reportHasNoKeywords"]=>
    array(12) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [2]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [3]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [4]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [5]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [6]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [7]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [8]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [9]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [10]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [11]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasNoMetaDescription"]=>
    array(12) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [2]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [3]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [4]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [5]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [6]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [7]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [8]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [9]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [10]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [11]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasReviewDate"]=>
    array(1) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
    }
    ["reportHasStartDate"]=>
    array(1) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
    }
    ["reportHasNoSummary"]=>
    array(12) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [2]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [3]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [4]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [5]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [6]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [7]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [8]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [9]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [10]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [11]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasNoTeaser"]=>
    array(12) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [2]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [3]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [4]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [5]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [6]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [7]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [8]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [9]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [10]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [11]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasTitle"]=>
    array(11) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
      [1]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [2]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [3]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [4]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [5]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [6]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [7]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [8]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [9]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [10]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportAuthorContains"]=>
    array(1) {
      [0]=>
      string(52) "edcomm/web/not-in-use-classes/showcase-old/accordion"
    }
    ["reportHasNoAuthor"]=>
    array(11) {
      [0]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [1]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [2]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [3]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [4]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [5]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [6]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [7]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [8]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [9]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [10]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasNoEndDate"]=>
    array(11) {
      [0]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [1]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [2]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [3]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [4]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [5]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [6]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [7]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [8]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [9]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [10]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasNoReviewDate"]=>
    array(11) {
      [0]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [1]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [2]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [3]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [4]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [5]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [6]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [7]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [8]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [9]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [10]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasNoStartDate"]=>
    array(11) {
      [0]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
      [1]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
      [2]=>
      string(50) "edcomm/web/not-in-use-classes/showcase-old/cluetip"
      [3]=>
      string(53) "edcomm/web/not-in-use-classes/showcase-old/flowplayer"
      [4]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/galleriffic"
      [5]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/index"
      [6]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/rollover"
      [7]=>
      string(58) "edcomm/web/not-in-use-classes/showcase-old/simpleslideshow"
      [8]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/slide_flash"
      [9]=>
      string(49) "edcomm/web/not-in-use-classes/showcase-old/slides"
      [10]=>
      string(54) "edcomm/web/not-in-use-classes/showcase-old/tablesorter"
    }
    ["reportHasNoTitle"]=>
    array(1) {
      [0]=>
      string(48) "edcomm/web/not-in-use-classes/showcase-old/boxes"
    }
    ["reportHasNoDisplayName"]=>
    array(1) {
      [0]=>
      string(51) "edcomm/web/not-in-use-classes/showcase-old/carousel"
    }
  }
  ["symlink"]=>
  array(0) {
  }
  ["block_TEXT"]=>
  array(0) {
  }
  ["block_XML"]=>
  array(0) {
  }
}
</pre>
<p>The method name then is used as the key to retrieve the relevant report.</p>
<p>Note that entries like "reportAuthorContains" won't exist in the array unless the corresponding method (a method in the second group) is called with a non-empty substring passed in.</p>
</description>
<example>            // get pages with authors containing a substring
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        'edcomm/web/not-in-use-classes/showcase-old', 'imt-intra' )
                // the first time, need to pass in the substring
                // and to retraverse, hence true
                )->reportAuthorContains( a\Page::TYPE, true, "nw" );
                
            echo S_H2 . "Pages with Author Containing 'nw'" . E_H2;
            u\DebugUtility::dump( $results );
        
            // get pages with authors
            // $results already contains the information from the first traversal
            // no need to retraverse
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        'edcomm/web/not-in-use-classes/showcase-old', 'imt-intra' )
                // the second time, no substring, hence false
                )->reportHasAuthor( a\Page::TYPE, false );
                
            echo S_H2 . "Pages with Authors" . E_H2;
            u\DebugUtility::dump( $results );
</example>
<return-type>mixed</return-type>
<exception>NoSuchMethodException, UnacceptableValueException</exception>
</documentation>
*/
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
            
        // substring defaulted to false
        if( isset( $params[ 2 ] ) )
            $substring = $params[ 2 ];
        else
            $substring = false;
            
        if( !c\BooleanValues::isBoolean( $retraverse ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $retraverse must be a boolean." . E_SPAN );
                
        // retraverse
        if( $retraverse )
        {
            if( isset( $substring ) && $substring !== false )
                $this->reportMetadataWiredFields( strlen( $substring), $substring );
            else
                $this->reportMetadataWiredFields( 1 );
        }
        
        //u\DebugUtility::dump( $this->results );
        
        if( is_array( $this->results ) &&
            array_key_exists( $type, $this->results ) &&
            array_key_exists( $func, $this->results[ $type ] ) )
            return $this->results[ $type ][ $func ];
        else
            return NULL;
    }
    
/**
<documentation><description><p>Clears the <code>$results</code> array, removing entries from previous traversals.</p></description>
<example>$report->clearResults();</example>
<return-type>a\Report</return-type>
<exception></exception>
</documentation>
*/
    public function clearResults() : Report
    {
        $this->results = array();
        return $this;
    }
    
/**
<documentation><description><p>Returns the <code>u\Cache</code> object.</p></description>
<example>u\DebugUtility::dump( $report->getCache() );</example>
<return-type>u\Cache</return-type>
<exception></exception>
</documentation>
*/
    public function getCache() : u\Cache
    {
        return $this->cache;
    }
    
/**
<documentation><description><p>Returns the <code>$results</code> array.</p></description>
<example>u\DebugUtility::dump( $report->getResults() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getResults() : array
    {
        return $this->results;
    }
    
/**
<documentation><description><p>Returns the root container or <code>NULL</code>.</p></description>
<example>$root = $report->getRoot();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getRoot()
    {
        return $this->root;
    }
    
/**
<documentation><description><p>Returns <code>$results</code>, containing information related to <code>endDate</code>, <code>reviewDate</code>, and <code>startDate</code>. 
This method is called by <code>reportEndDateBefore</code>, <code>reportEndDateAfter</code>,
<code>reportReviewDateBefore</code>, <code>reportReviewDateAfter</code>,
<code>reportStartDateBefore</code>, and <code>reportStartDateAfter</code>.</p>
<p>Note that for an invocation of this method to be meaningful, a valid <code>DateTime</code> object must be passed in. Every time a new <code>DateTime</code> object is passed in, a retraversal is required.</p>
<p>A method like <code>reportEndDateAfter</code> has three parameters: <code>reportEndDateAfter( \DateTime $dt, string $type=Page::TYPE, bool $retraverse=false )</code>. The first one is a <code>DateTime</code> object. The second one is a type string like <code>a\Page::TYPE</code>. The third one, a bool, controls retraveral. When one of these six methods is called for the first time, set <code>$retraverse</code> to <code>true</code> to trigger retraversal. After that, if no new <code>DateTime</code> object is involved, then set <code>$retraverse</code> to <code>false</code>. Every time when a new <code>DateTime</code> object is involved, a retraversal is required.</p>
</description>
<example>            $date = new DateTime('2011-01-01T00:00:00.012345Z');
            
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( '2c7a19888b7f08ee603d6820e25d3dc9' )
                // called the first time, retraversal required, hence true
                )->reportStartDateBefore( $date, a\Page::TYPE, true );
            
            echo S_H2 . "Pages with End Date Set to Before 1/1/2011" . E_H2;
            u\DebugUtility::dump( $results );
            
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( '2c7a19888b7f08ee603d6820e25d3dc9' )
                // no new DateTime, retraversal not needed, hence false
                )->reportStartDateAfter( $date, a\Page::TYPE, false );
            
            echo S_H2 . "Pages with End Date Set to 1/1/2011 or After" . E_H2;
            u\DebugUtility::dump( $results );
</example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportDate( \DateTime $dt ) : array
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
    
/**
<documentation><description><p>Returns an array of path information of the type of assets specified. An asset must have the <code>endDate</code> field set, and the <code>endDate</code> is equal to or after the date <code>$dt</code>. See <code>reportDate</code> for more details.</p>
</description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function reportEndDateAfter(
        \DateTime $dt, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( array_key_exists( $type, $this->results ) &&
            array_key_exists( __FUNCTION__, $this->results[ $type ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
            
        return NULL;
    }

/**
<documentation><description><p>Returns an array of path information of the type of assets specified. An asset must have the <code>endDate</code> field set, and the <code>endDate</code> precedes the date <code>$dt</code>. See <code>reportDate</code> for more details.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function reportEndDateBefore(
        \DateTime $dt, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( array_key_exists( $type, $this->results ) &&
            array_key_exists( __FUNCTION__, $this->results[ $type ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
            
        return NULL;
    }

/**
<documentation><description><p>Returns <code>$results</code>, containing a report of files
and pages. This method can be used to work with the three dates associated with files and
pages: <code>lastModifiedDate</code>, <code>createdDate</code>, and <code>lastPublishedDate</code>.
The <code>$type</code> parameter should be one of these three values. For example, when
<code>lastModifiedDate</code> is passed in as the first parameter, the report is about
files/pages last modified. The value passed in is case-sensitive. This value is used to
retrieve the corresponding method defined in <code>File</code> and <code>Page</code>.
<code>$days_inclusive</code> is the interval we are interested in, in terms of days. The
parameter should be a positive integer. <code>$direction</code> should be either
<code>forward</code> or <code>backward</code>. When <code>forward</code> is used, we are
specifying files/pages that were created, last modified, or last published in the last
<code>$days_inclusive</code> days, looking forward. If we are interested in files/pages
created 60 days or before, we should pass in <code>61</code> as <code>$days_inclusive</code>,
and <code>backward</code> as <code>$direction</code>. We add 1 to <code>$days_inclusive</code>
because the day value is inclusive. In general, if we are interested in X days or older,
we use X + 1 days with <code>backward</code>. But if we are interest in X days or newer,
we use X and <code>forward</code>.</p></description>
<example>            // get file/page modified in the last three days
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        'edcomm/web', 'imt-intra' )
                )->reportLast( 'LastModifiedDate', 3, c\T::FORWARD ); // modified last 3 days
            echo S_H2 . "Files/Pages Last Modified in the Last Three Days" . E_H2;
            u\DebugUtility::dump( $results );
            
            // get file/page published in the last 20 days
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        '/web-services', 'cascade-admin' )
                )->reportLast( 'lastPublishedDate', 20, c\T::FORWARD ); // published last 20 days
            echo S_H2 . "Files/Pages Last Published in the Last Twenty Days" . E_H2;
            u\DebugUtility::dump( $results );
            
            // stale file/page report
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        '/web-services', 'cascade-admin' )
                )->reportLast( 'LastModifiedDate', 61, c\T::BACKWARD ); // not touched in 60 days
            echo S_H2 . "Files/Pages Not Touched in the Last Sixty Days" . E_H2;
            u\DebugUtility::dump( $results );
            
            // file/page created 3 months ago or newer
            $results = $report->
                setRootFolder( 
                    $cascade->getFolder( 
                        '/web-services', 'cascade-admin' )
                )->reportLast( 'createdDate', 90, c\T::FORWARD ); // created last 90 days
                
            echo S_H2 . "Files/Pages Created in the Last Three Months" . E_H2;
            u\DebugUtility::dump( $results );
</example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportLast(
        string $type, int $days_inclusive, string $direction ) : array
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
    
/**
<documentation><description><p>Returns an array, containing a report of assets of the specified type that contain display names longer than <code>$max_num_of_char</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function reportLongDisplayName(
        int $max_num_of_char=1, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportMetadataWiredFields( $max_num_of_char );
            
        if( isset( $this->results[ $type ] ) &&
            isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }

/**
<documentation><description><p>Returns <code>$results</code>, containing a report of assets of the specified type that contain titles longer than <code>$max_num_of_char</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function reportLongTitle(
        int $max_num_of_char=1, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportMetadataWiredFields( $max_num_of_char );
            
        if( isset( $this->results[ $type ] ) &&
            isset( $this->results[ $type ][ __FUNCTION__ ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
        else
            return NULL;
    }
    
/**
<documentation><description><p>Returns <code>$results</code> containing information of various wired fields.</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException, UnacceptableValueException</exception>
</documentation>
*/
    public function reportMetadataWiredFields(
        int $max_num_of_char=1, string $substring="" ) : array
    {
        $this->checkRootFolder();
        
        if( !is_numeric( $max_num_of_char ) || $max_num_of_char < 1 )
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report of numbers of assets of various types. The <code>$types</code> parameter can be a single type like <code>Folder::TYPE</code>, or it can be an array of types like <code>array( Folder::TYPE, Page::TYPE )</code>. Only types appearing in this parameter will be reported. All other types will be ignored. Note that different root containers and separate traversals are required if we need to generate reports on the Base Folder as well as, for example, on the metadata set root container, because metadata set containers are not children of the Base Folder.</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportNumberOfAssets( $types ) : array
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report of orphaned files (defined in terms of lack of relationships).</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportOrphanFiles() : array
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report of pages
with empty fields (fields in the metadata that contain no value). Since no value
information in required to supply to this method, unlike <code>reportPageFieldMatchesValue( $field_value, bool $or=true )</code>,
the <code>$fields</code> parameter is a simple array containing field names (wired or
dynamic). Examples are <code>array( "displayName" )</code> and <code>array( "displayName",
"title" )</code>. The method checks every field to see if it is empty. The
<code>$or</code> parameter is used to control disjunctive (<em>or</em>) vs. conjunctive (<em>and</em>) searches.</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportPageFieldEmptyValue( $fields, bool $or=true ) : array
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report of pages
with fields matching some specific values. The <code>$field_value</code> parameter is an
array of arrays, each containing a single key mapping to a single value. Examples are
<code>array( array( "summary" =&gt; "" ) )</code> and <code>array( array( "text" =&gt;
"Text" ), array( "summary" =&gt; "" ) )</code>. The first example is used to find pages
with an empty field (containing an empty string). The second example is used to find pages
containing both an empty summary field and a text field containing the string
<code>Text</code>. Since we may want to look at the same field again and again (for
example, in a multiselect), containing different values, the identifier of the field must
be able to appear again and again. This is the reason why each identifier must appear in a
separate array. Note that identifiers here mean either wired field names like
<code>summary</code> and <code>title</code>, or identifiers of dynamic fields. The
<code>$or</code> parameter is used to control disjunctive (<em>or</em>) vs. conjunctive
(<em>and</em>) searches. Disjunctive searches normally return more results than
conjunctive ones. Note that unlike <code>reportPageNodeContainsValue( $node_value,
$or=true )</code>, I do not want to deal with sub-strings here. To be able to deal with
sub-strings, I must retrieve the metadata set associated with a page and figure out the
types fields (whether they are text fields). This will slow down the search. Therefore, I
require exact value matches, even in text fields.</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportPageFieldMatchesValue(
        array $field_value, bool $or=true ) : array
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report of pages
with nodes containing some specific values. The <code>$node_value</code> parameter is an
array of arrays, each containing a single key mapping to a single value. Examples are
<code>array( array( "main-content-title" =&gt; "" ) )</code> and <code>array( array(
"main-content-title" =&gt; "" ), array( "main-content-content" =&gt; "Galleriffic" ) )</code>.
The first example is used to find pages with an empty node (containing an empty string).
The second example is used to find pages containing either a node with no value (empty
string), or pages with another node containing the text <code>Galleriffic</code> (not
necessarily a proper sub-string). This method can be used to find empty nodes, or nodes
containing one or more sub-strings. Since we may want to look at the same node again and
again, containing different sub-strings (for example, a node containing both the word
<code>Data</code> and the word <code>Block</code>), the identifier of the node must be
able to appear again and again. This is the reason why each identifier must appear in a
separate array. Note that identifiers here mean fully qualified identifiers of
<code>StructuredDataNode</code> objects. The <code>$or</code> parameter is used to control
disjunctive (<em>or</em>) vs. conjunctive (<em>and</em>) searches. Disjunctive searches
normally return more results than conjunctive ones.</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportPageNodeContainsValue(
        array $node_value, bool $or=true ) : array
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report of pages
with empty nodes (nodes containing no text). Since no textual value is required to supply
to this method, unlike <code>reportPageNodeContainsValue( $node_value, $or=true )</code>,
the <code>$nodes</code> parameter is a simple array, containing fully qualified identifiers.
Examples are <code>array( "main-content-title" )</code> and <code>array(
"main-content-title", "content-group;0;content-group-content" )</code>. The method checks
every node to see if it is empty. The <code>$or</code> parameter is used to control
disjunctive (<em>or</em>) vs. conjunctive (<em>and</em>) searches.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function reportPageNodeEmptyValue( array $nodes, bool $or=true ) : array
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
        $params = array( 'node-value' => $node_value,
            'disjunctive' => $or, 'cache' => $this->cache );
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report on
publishable/unpublishable assets (of types folder, file, and page).
The <code>$publishable</code> is used to control what to report: <code>true</code> for
publishable assets, and <code>false</code> for unpublishable assets.</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportPublishable( bool $publishable=true ) : array
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
    
/**
<documentation><description><p>Returns <code>$results</code>, containing a report on
assets that contain relatie links. Three types of assets are searched: data definition blocks, pages, and files.</p></description>
<example></example>
<return-type>array</return-type>
<exception>ReportException</exception>
</documentation>
*/
    public function reportRelativeLink() : array
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
    
/**
<documentation><description><p>Returns an array of path information of the type of assets specified. An asset must have the <code>reviewDate</code> field set, and the <code>reviewDate</code> is equal to or after the date <code>$dt</code>. See <code>reportDate</code> for more details.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function reportReviewDateAfter(
        \DateTime $dt, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( array_key_exists( $type, $this->results ) &&
            array_key_exists( __FUNCTION__, $this->results[ $type ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
            
        return NULL;
    }

/**
<documentation><description><p>Returns an array of path information of the type of assets specified. An asset must have the <code>reviewDate</code> field set, and the <code>reviewDate</code> precedes the date <code>$dt</code>. See <code>reportDate</code> for more details.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function reportReviewDateBefore(
        \DateTime $dt, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( array_key_exists( $type, $this->results ) &&
            array_key_exists( __FUNCTION__, $this->results[ $type ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
            
        return NULL;
    }
    
/**
<documentation><description><p>Returns an array of site:path information of destinations that are enabled and scheduled to publish. Note that this method visits all sites.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function reportScheduledPublishDestination() : array
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
    
/**
<documentation><description><p>This method calls <code>reportScheduledPublishDestination()</code>, <code>reportScheduledPublishPublishSet()</code>, and <code>reportScheduledPublishSite()</code>, and returns an array of combined results.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function reportScheduledPublishing() : array
    {
        $this->reportScheduledPublishDestination();
        $this->reportScheduledPublishPublishSet();
        return $this->reportScheduledPublishSite();
    }

/**
<documentation><description><p>Returns an array of site:path information of publish sets that are scheduled to publish. Note that this method visits all sites.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function reportScheduledPublishPublishSet() : array
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

/**
<documentation><description><p>Returns an array of names of sites that are scheduled to publish. Note that this method visits all sites.</p></description>
<example></example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function reportScheduledPublishSite() : array
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
                $this->results[ Site::TYPE ][] = $site->getName() . ", " .
                    $time_expression;
            }
        }
        
        return $this->results;
    }

/**
<documentation><description><p>Returns an array of path information of the type of assets specified. An asset must have the <code>startDate</code> field set, and the <code>startDate</code> is equal to or after the date <code>$dt</code>. See <code>reportDate</code> for more details.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function reportStartDateAfter(
        \DateTime $dt, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( array_key_exists( $type, $this->results ) &&
            array_key_exists( __FUNCTION__, $this->results[ $type ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
            
        return NULL;
    }

/**
<documentation><description><p>Returns an array of path information of the type of assets specified. An asset must have the <code>startDate</code> field set, and the <code>startDate</code> precedes the date <code>$dt</code>. See <code>reportDate</code> for more details.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function reportStartDateBefore(
        \DateTime $dt, string $type=Page::TYPE, bool $retraverse=false )
    {
        if( $retraverse )
            $this->reportDate( $dt );
            
        if( array_key_exists( $type, $this->results ) &&
            array_key_exists( __FUNCTION__, $this->results[ $type ] ) )
            return $this->results[ $type ][ __FUNCTION__ ];
            
        return NULL;
    }

/**
<documentation><description><p>Sets the root container for asset tree traversal, and returns the calling object.</p></description>
<example></example>
<return-type>Report</return-type>
<exception></exception>
</documentation>
*/
    public function setRootContainer( Container $root ) : Report
    {
        $this->root = $root;
        return $this;
    }
    
/**
<documentation><description><p>Sets the root folder for asset tree traversal, and returns the calling object.</p></description>
<example></example>
<return-type>Report</return-type>
<exception></exception>
</documentation>
*/
    public function setRootFolder( Folder $root ) : Report
    {
        return $this->setRootContainer( $root );
    }
        
    /* ===== static methods ===== */
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportDate(
        aohs\AssetOperationHandlerService $service, 
        p\Child $child, array $params=NULL, array &$results=NULL )
    {
        if( !isset( $params[ 'date' ] ) )
            throw new e\ReportException(
                S_SPAN . "The date is not set. " . E_SPAN );
                
        $date   = $params[ 'date' ];
        
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );

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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportLast(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportMetadataWiredFieldsContains(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
    {
        if( !isset( $params[ 'substring' ] ) )
            throw new e\ReportException(
                S_SPAN . "The substring is not set. " . E_SPAN );
                
        $substring = $params[ 'substring' ];
        
        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );

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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportMetadataWiredFields(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
    {
        if( !isset( $params[ 'max' ] ) )
            throw new e\ReportException(
                S_SPAN . "The maximum is not set. " . E_SPAN );
                
        $max    = $params[ 'max' ];

        if( !isset( $params[ 'cache' ] ) )
            throw new e\ReportException(
                S_SPAN . c\M::NULL_CACHE . E_SPAN );

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

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportNumberOfAssets(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
    {
        $type             = $child->getType();
        $results[ $type ] = $results[ $type ] + 1;
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportOrphanFiles(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportPageFieldValue(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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
            if( p\Metadata::isWiredField( $identifier ) )
            {
                $method = p\Metadata::getWiredFieldMethodName( $identifier );
                
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
                    if( p\Metadata::isWiredField( $identifier ) )
                    {
                        $method = p\Metadata::getWiredFieldMethodName( $identifier );
                
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
                    if( p\Metadata::isWiredField( $identifier ) )
                    {
                        $method = p\Metadata::getWiredFieldMethodName( $identifier );
                
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
   
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportPageNodeValue(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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
   
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportPublishable(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportRelativeLink(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportScheduledPublishDestination(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function assetTreeReportScheduledPublishPublishSet(
        aohs\AssetOperationHandlerService $service,
        p\Child $child, array $params=NULL, array &$results=NULL )
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
    
    private static function getTimeInfo( ScheduledPublishing $sp ) : string
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
            {
                foreach( $days as $day )
                {
                    $time_expression .= $day . ", ";
                }
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