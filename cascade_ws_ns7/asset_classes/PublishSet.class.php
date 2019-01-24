<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 1/24/2018 Added REST code to edit.
  * 6/28/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 1/4/2016 Fixed a bug in publish.
  * 6/17/2015 Fixed a bug in edit.
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
<p>A <code>PublishSet</code> object represents a publish set asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/scheduled-publishing.php\"><code>ScheduledPublishing</code></a>.</p>
<h2>Structure of <code>publishSet</code></h2>
<pre>SOAP:
publishSet
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  files
    publishableAssetIdentifier (NULL, object or array)
      id
      path
        path
        siteId
        siteName (always NULL)
      type
      recycled
  folders
    publishableAssetIdentifier (NULL, object or array)
      id
      path
        path
        siteId
        siteName (always NULL)
      type
      recycled
  pages
    publishableAssetIdentifier (NULL, object or array)
      id
      path
        path
        siteId
        siteName (always NULL)
      type
      recycled
  usesScheduledPublishing
  scheduledPublishDestinationMode
  scheduledPublishDestinations
  timeToPublish
  publishIntervalHours
  publishDaysOfWeek
    dayOfWeek
  cronExpression
  sendReportToUsers
  sendReportToGroups
  sendReportOnErrorOnly

REST:
publishSet
  files (array)
    stdClass
      id
      path
        path
        siteId
      type
      recycled
  folders (array)
    stdClass
      id
      path
        path
        siteId
      type
      recycled
  pages (array)
    stdClass
      id
      path
        path
        siteId
      type
      recycled
  usesScheduledPublishing
  scheduledPublishDestinationMode
  scheduledPublishDestinations
  timeToPublish
  publishIntervalHours
  publishDaysOfWeek
    dayOfWeek
  cronExpression
  sendReportToUsers
  sendReportToGroups
  sendReportOnErrorOnly
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  name
  id  
</pre>
<h2>Design Issues</h2>
<p>There is something special about all <code>ScheduledPublishing</code> assets: right after such an asset is read from Cascade, if we send the asset back to Cascade by calling <code>edit</code>, even without making any changes to it, Cascade will reject the asset. To fix this problem, we have to call <code>unset</code> to unset any property related to scheduled publishing if the property stores a <code>NULL</code> value. This must be done inside <code>edit</code>, or an exception will be thrown.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "publishSet" ),
        array( "getComplexTypeXMLByName" => "publishable-asset-list" ),
        array( "getSimpleTypeXMLByName"  => "scheduledDestinationMode" ),
        array( "getComplexTypeXMLByName" => "daysOfWeek" ),
        array( "getSimpleTypeXMLByName"  => "dayOfWeek" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/publish_set.php">publish_set.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/publishset/28a960258b7ffe8343b94c282741f634

{
  "asset":{
    "publishSet":{
      "files":[],
      "folders":[],
      "pages":[],
      "usesScheduledPublishing":false,
      "sendReportOnErrorOnly":false,
      "parentContainerId":"0952d9758b7ffe8339ce5d13a1ad5e0a",
      "parentContainerPath":"Test Container",
      "path":"Test Container/test",
      "siteId":"fd27691f8b7f08560159f3f02754e61d",
      "siteName":"_common",
      "name":"test",
      "id":"28a960258b7ffe8343b94c282741f634"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}
</pre>
</postscript>
</documentation>
*/
class PublishSet extends ScheduledPublishing
{
    const DEBUG = false;
    const TYPE  = c\T::PUBLISHSET;
    
/**
<documentation><description><p>The constructor, overriding the parent method to process publish set-specific information like pages and folders.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        // store publish set info
        $this->processPublishableAssetIdentifiers();
    }
    
/**
<documentation><description><p>Adds a file to the publish set, and returns the calling object.</p></description>
<example>$ps->addFile( $cascade->getAsset(
    a\File::TYPE, '1f2259288b7ffe834c5fe91e55c1b66f' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addFile( File $file ) : Asset
    {
        $id             = $file->getId();
        $path           = new \stdClass();
        $path->path     = $file->getPath();
        $path->siteId   = $file->getSiteId();
        $path->siteName = $file->getSiteName();
        
        $psi_std           = new \stdClass();
        $psi_std->id       = $id;
        $psi_std->path     = $path;
        $psi_std->type     = File::TYPE;
        $psi_std->recycled = false;
        
        $this->files[] = new p\PublishableAssetIdentifier( $psi_std );
        return $this;
    }
    
/**
<documentation><description><p>Adds a folder to the publish set and returns the calling object.</p></description>
<example>$ps->addFolder( $cascade->getAsset(
    a\Folder::TYPE, '1f229e908b7ffe834c5fe91e04cc2303' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addFolder( Folder $folder ) : Asset
    {
        $id             = $folder->getId();
        $path           = new \stdClass();
        $path->path     = $folder->getPath();
        $path->siteId   = $folder->getSiteId();
        $path->siteName = $folder->getSiteName();
        
        $psi_std           = new \stdClass();
        $psi_std->id       = $id;
        $psi_std->path     = $path;
        $psi_std->type     = Folder::TYPE;
        $psi_std->recycled = false;
        
        $this->folders[] = new p\PublishableAssetIdentifier( $psi_std );
        return $this;
    }
    
/**
<documentation><description><p>Adds a page to the publish set and returns the calling object.</p></description>
<example>$ps->addPage( $cascade->getAsset(
    a\Page::TYPE, '1f2373488b7ffe834c5fe91e2f1fb803' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addPage( Page $page ) : Asset
    {
        $id             = $page->getId();
        $path           = new \stdClass();
        $path->path     = $page->getPath();
        $path->siteId   = $page->getSiteId();
        $path->siteName = $page->getSiteName();
        
        $psi_std           = new \stdClass();
        $psi_std->id       = $id;
        $psi_std->path     = $path;
        $psi_std->type     = Page::TYPE;
        $psi_std->recycled = false;
        
        $this->pages[] = new p\PublishableAssetIdentifier( $psi_std );
        return $this;
    }
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception>EditingFailureException</exception>
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
        $files_count = count( $this->files );
        $publish_set = $this->getProperty();
        
        if( $files_count == 0 )
        {
            if( $this->getService()->isSoap() )
            {
                $publish_set->files = new \stdClass();
            }
            elseif( $this->getService()->isRest() )
            {
                $publish_set->files = array();
            }
        }
        else if( $files_count == 1 )
        {
            if( $this->getService()->isSoap() )
            {
                $publish_set->files->publishableAssetIdentifier = 
                    $this->files[ 0 ]->toStdClass();
            }
            elseif( $this->getService()->isRest() )
            {
                $publish_set->files = array( $this->files[ 0 ]->toStdClass() );
            }
        }
        else
        {
            if( $this->getService()->isSoap() )
            {
                $this->getProperty()->files->publishableAssetIdentifier = array();
            }
            
            for( $i = 0; $i < $files_count; $i++ )
            {
                if( $this->getService()->isSoap() )
                {
                    $publish_set->files->publishableAssetIdentifier[] =
                        $this->files[ $i ]->toStdClass();
                }
                elseif( $this->getService()->isRest() )
                {
                    $this->getProperty()->files[] = $this->files[ $i ]->toStdClass();
                }
            }
        }
    
        $folders_count = count( $this->folders );
        
        if( $folders_count == 0 )
        {
            if( $this->getService()->isSoap() )
            {
                $publish_set->folders = new \stdClass();
            }
            elseif( $this->getService()->isRest() )
            {
                $publish_set->folders = array();
            }
        }
        else if( $folders_count == 1 )
        {
            if( $this->getService()->isSoap() )
            {
                $publish_set->folders->publishableAssetIdentifier = 
                    $this->folders[ 0 ]->toStdClass();
            }
            elseif( $this->getService()->isRest() )
            {
                $publish_set->folders = array( $this->folders[ 0 ]->toStdClass() );
            }
        }
        else
        {
            if( $this->getService()->isSoap() )
            {
                $this->getProperty()->folders->publishableAssetIdentifier = array();
            }
            
            for( $i = 0; $i < $folders_count; $i++ )
            {
                if( $this->getService()->isSoap() )
                {
                    $publish_set->folders->publishableAssetIdentifier[] =
                        $this->folders[ $i ]->toStdClass();
                }
                elseif( $this->getService()->isRest() )
                {
                    $this->getProperty()->folders[] = $this->folders[ $i ]->toStdClass();
                }
            }
        }
    
        $pages_count = count( $this->pages );
        
        if( $pages_count == 0 )
        {
            if( $this->getService()->isSoap() )
            {
                $publish_set->pages = new \stdClass();
            }
            elseif( $this->getService()->isRest() )
            {
                $publish_set->pages = array();
            }
        }
        else if( $pages_count == 1 )
        {
            if( $this->getService()->isSoap() )
            {
                $publish_set->pages->publishableAssetIdentifier = 
                    $this->pages[ 0 ]->toStdClass();
            }
            elseif( $this->getService()->isRest() )
            {
                $publish_set->pages = array( $this->pages[ 0 ]->toStdClass() );
            }
        }
        else
        {
            if( $this->getService()->isSoap() )
            {
                $this->getProperty()->pages->publishableAssetIdentifier = array();
            }
            
            for( $i = 0; $i < $pages_count; $i++ )
            {
                if( $this->getService()->isSoap() )
                {
                    $publish_set->pages->publishableAssetIdentifier[] =
                        $this->pages[ $i ]->toStdClass();
                }
                elseif( $this->getService()->isRest() )
                {
                    $this->getProperty()->pages[] = $this->pages[ $i ]->toStdClass();
                }
            }
        }

        if( $publish_set->usesScheduledPublishing ) // publishing is scheduled
        {
            if( !isset( $publish_set->timeToPublish ) ||
                is_null( $publish_set->timeToPublish ) )
            {
                unset( $publish_set->timeToPublish );
            }
            // fix the time unit
            else if( strpos( $publish_set->timeToPublish, '-' ) !== false )
            {
                $pos = strpos( $publish_set->timeToPublish, '-' );
                $publish_set->timeToPublish = substr(
                    $publish_set->timeToPublish, 0, $pos );
            }
      
            if( !isset( $publish_set->publishIntervalHours ) ||
                is_null( $publish_set->publishIntervalHours ) )
                unset( $publish_set->publishIntervalHours );
                
            if( !isset( $publish_set->publishDaysOfWeek ) ||
                is_null( $publish_set->publishDaysOfWeek ) )
                unset( $publish_set->publishDaysOfWeek );
                
            if( !isset( $publish_set->cronExpression ) ||
                is_null( $publish_set->cronExpression ) )
                unset( $publish_set->cronExpression );
        }

        $asset                                    = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $publish_set;
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
<documentation><description><p>Returns an array of path strings of the files.</p></description>
<example>u\DebugUtility::dump( $ps->getFilePaths() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getFilePaths() : array
    {
        $file_paths = array();
        
        foreach( $this->files as $file )
        {
            $file_paths[] = $file->getPath()->getPath();
        }
        
        return $file_paths;
    }
    
/**
<documentation><description><p>Returns an array of path strings of the folders.</p></description>
<example>u\DebugUtility::dump( $ps->getFolderPaths() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getFolderPaths() : array
    {
        $folder_paths = array();
        
        foreach( $this->folders as $folder )
        {
            $folder_paths[] = $folder->getPath()->getPath();
        }
        return $folder_paths;
    }
    
/**
<documentation><description><p>Returns an array of path strings of the pages.</p></description>
<example>u\DebugUtility::dump( $ps->getPagePaths() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getPagePaths() : array
    {
        $page_paths = array();
        
        foreach( $this->pages as $page )
        {
            $page_paths[] = $page->getPath()->getPath();
        }
        return $page_paths;
    }
    
/**
<documentation><description><p>Publishes the publish set and returns the calling object.</p></description>
<example>$ps->publish();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function publish( Destination $destination=NULL ) : Asset
    {
        if( isset( $destination ) )
        {
            $destination_std           = new \stdClass();
            $destination_std->id       = $destination->getId();
            $destination_std->type     = $destination->getType();
        }
        
        $service = $this->getService();
        
        if( isset( $destination ) )
            $service->publish( 
                $service->createId(
                    self::TYPE, $this->getProperty()->id ), $destination_std );
        else
            $service->publish( 
                $service->createId( self::TYPE, $this->getProperty()->id ) );
        return $this;
    }
    
/**
<documentation><description><p>Removes the file and returns the calling object.</p></description>
<example>$ps->removeFile( $cascade->getAsset(
    a\File::TYPE, '1f2259288b7ffe834c5fe91e55c1b66f' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeFile( File $file ) : Asset
    {
        $id = $file->getId();
        
        $temp = array();
        
        foreach( $this->files as $file )
        {
            if( $file->getId() != $id )
            {
                $temp[] = $file;
            }
        }
        $this->files = $temp;
        return $this;
    }
    
/**
<documentation><description><p>Removes the folder and returns the calling object.</p></description>
<example>$ps->removeFolder( $cascade->getAsset(
    a\Folder::TYPE, '1f229e908b7ffe834c5fe91e04cc2303' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removeFolder( Folder $folder ) : Asset
    {
        $id = $folder->getId();
        
        $temp = array();
        
        foreach( $this->folders as $folder )
        {
            if( $folder->getId() != $id )
            {
                $temp[] = $folder;
            }
        }
        $this->folders = $temp;
        return $this;
    }
    
/**
<documentation><description><p>Removes the page and returns the calling object.</p></description>
<example>$ps->removeFolder( $cascade->getAsset(
    a\Page::TYPE, '1f2373488b7ffe834c5fe91e2f1fb803' ) )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function removePage( Page $page ) : Asset
    {
        $id = $page->getId();
        
        $temp = array();
        
        foreach( $this->pages as $page )
        {
            if( $page->getId() != $id )
            {
                $temp[] = $page;
            }
        }
        $this->pages = $temp;
        return $this;
    }

    private function processPublishableAssetIdentifiers()
    {
        $this->files   = array();
        $this->folders = array();
        $this->pages   = array();

        // files
        if( isset( $this->getProperty()->files) &&
            isset( $this->getProperty()->files->publishableAssetIdentifier ) )
        {
            $identifiers = $this->getProperty()->files->publishableAssetIdentifier;
            
            if( !is_array( $identifiers ) )
            {
                $identifiers = array( $identifiers );
            }
            
            foreach( $identifiers as $identifier )
            {
                $this->files[] = new p\PublishableAssetIdentifier( $identifier );
            }
        }
        // folders
        if( isset( $this->getProperty()->folders ) &&
            isset( $this->getProperty()->folders->publishableAssetIdentifier ) )
        {
            $identifiers = $this->getProperty()->folders->publishableAssetIdentifier;
            
            if( !is_array( $identifiers ) )
            {
                $identifiers = array( $identifiers );
            }
            
            foreach( $identifiers as $identifier )
            {
                $this->folders[] = new p\PublishableAssetIdentifier( $identifier );
            }
        }
        // pages
        if( isset( $this->getProperty()->pages ) &&
            isset( $this->getProperty()->pages->publishableAssetIdentifier ) )
        {
            $identifiers = $this->getProperty()->pages->publishableAssetIdentifier;
            
            if( !is_array( $identifiers ) )
            {
                $identifiers = array( $identifiers );
            }
            
            foreach( $identifiers as $identifier )
            {
                $this->pages[] = new p\PublishableAssetIdentifier( $identifier );
            }
        }
    }
    
    private $files;
    private $folders;
    private $pages;
}
?>