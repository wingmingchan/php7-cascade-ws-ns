<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 1/3/2018 Added code to test for NULL.
  * 9/14/2017 Added getExtensionsToStrip and setExtensionsToStrip.
  * 6/23/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/11/2017 Added JSON dump.
  * 5/28/2015 Added namespaces.
  * 8/11/2014 Removed getParentContainer.
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
<p>A <code>Destination</code> object represents a destination asset. This class is a sub-class of <a href=\"http://www.upstate.edu/web-services/api/asset-classes/scheduled-publishing.php\"><code>ScheduledPublishing</code></a>.</p>
<h2>Structure of <code>destination</code></h2>
<pre>destination
  id
  name
  parentContainerId
  parentContainerPath
  transportId
  transportPath
  applicableGroups
  directory
  enabled (bool)
  checkedByDefault (bool)
  publishASCII (bool)
  usesScheduledPublishing (bool)
  scheduledPublishDestinationMode
  scheduledPublishDestinations
  timeToPublish
  publishIntervalHours
  publishDaysOfWeek
    dayOfWeek
  cronExpression
  sendReportToUsers
  sendReportToGroups
  sendReportOnErrorOnly (bool)
  webUrl
  siteId
  siteName
</pre>
<h2>Design Issues</h2>
<p>There is something special about all <code>ScheduledPublishing</code> assets: right after such an asset is read from Cascade, if we send the asset back to Cascade by calling <code>edit</code>, even without making any changes to it, Cascade will reject the asset. To fix this problem, we have to call <code>unset</code> to unset any property related to scheduled publishing if the property stores a <code>NULL</code> value. This must be done inside <code>edit</code>, or an exception will be thrown.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "destination" ),
        array( "getSimpleTypeXMLByName"  => "scheduledDestinationMode" ),
        array( "getComplexTypeXMLByName" => "destination-list" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
        array( "getComplexTypeXMLByName" => "daysOfWeek" ),
        array( "getSimpleTypeXMLByName"  => "dayOfWeek" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/destination.php">destination.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/destination/c12dad828b7ffe83129ed6d81fc31265

{
  "asset":{
    "destination":{
      "parentContainerId":"c12d918b8b7ffe83129ed6d80a701fef",
      "parentContainerPath":"/",
      "transportId":"2844531b8b7ffe8343b94c28ccbfc7f7",
      "transportPath":"about-test:ftp",
      "applicableGroups":"Administrators",
      "directory":"formats",
      "enabled":true,
      "checkedByDefault":true,
      "publishASCII":false,
      "usesScheduledPublishing":false,
      "sendReportOnErrorOnly":false,
      "siteId":"c12d8c498b7ffe83129ed6d81ea4076a",
      "siteName":"formats",
      "webUrl":"http://web.upstate.edu/formats","name":"formats-web",
      "id":"c12dad828b7ffe83129ed6d81fc31265"
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
class Destination extends ScheduledPublishing
{
    const DEBUG     = false;
    const TYPE      = c\T::DESTINATION;
    const DELIMITER = ";";
    
/**
<documentation><description><p>The constructor.</p></description>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
    }

/**
<documentation><description><p>Adds the group name to <code>applicableGroups</code> and
returns the calling object.</p></description>
<example>$d->addGroup( $g )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function addGroup( Group $g ) : Asset
    {
        if( $g == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
            
        $group_name = $g->getName();
        
        if( isset( $this->getProperty()->applicableGroups ) )
            $group_string = $this->getProperty()->applicableGroups;
        else
            $group_string = "";
        
        $group_array = explode( self::DELIMITER, $this->getProperty()->applicableGroups );
        
        if( !in_array( $group_name, $group_array ) )
        {
            $group_array[] = $group_name;
        }
    
        $this->getProperty()->applicableGroups = implode( self::DELIMITER, $group_array );
        return $this;
    }
    
/**
<documentation><description><p>Disables the destination and returns the calling
object.</p></description>
<example>$d->disable()->edit();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function disable()
    {
        $this->setEnabled( false );
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
        $destination = $this->getProperty();
        
        if( $destination->usesScheduledPublishing ) // publishing is scheduled
        {
            if( !isset( $destination->timeToPublish ) ||
                is_null( $destination->timeToPublish ) )
            {
                unset( $destination->timeToPublish );
            }
            // fix the time unit
            else if( strpos( $destination->timeToPublish, '-' ) !== false )
            {
                $pos = strpos( $destination->timeToPublish, '-' );
                $destination->timeToPublish = substr(
                    $destination->timeToPublish, 0, $pos );
            }
      
            if( !isset( $destination->publishIntervalHours ) ||
                is_null( $destination->publishIntervalHours ) )
                unset( $destination->publishIntervalHours );
                
            if( !isset( $destination->publishDaysOfWeek ) ||
                is_null( $destination->publishDaysOfWeek ) )
                unset( $destination->publishDaysOfWeek );
                
            if( !isset( $destination->cronExpression ) ||
                is_null( $destination->cronExpression ) )
                unset( $destination->cronExpression );
        }
        
        $asset                                    = new \stdClass();
        $asset->{ $p = $this->getPropertyName() } = $destination;
        
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
<documentation><description><p>Enables the destination and returns the calling
object.</p></description>
<example>$d->enable()->edit();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function enable()
    {
        $this->setEnabled( true );
        return $this;
    }

/**
<documentation><description><p>Returns <code>applicableGroups</code>.</p></description>
<example>echo $d->getApplicableGroups(), BR;</example>
<return-type></return-type>
<exception>mixed</exception>
</documentation>
*/
    public function getApplicableGroups()
    {
        if( isset( $this->getProperty()->applicableGroups ) )
            return $this->getProperty()->applicableGroups;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>checkedByDefault</code>.</p></description>
<example>echo u\StringUtility::boolToString( $d->getCheckedByDefault() ), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getCheckedByDefault() : bool
    {
        return $this->getProperty()->checkedByDefault;
    }
    
/**
<documentation><description><p>Returns <code>directory</code>.</p></description>
<example>echo $d->getDirectory(), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getDirectory()
    {
        if( isset( $this->getProperty()->directory ) )
            return $this->getProperty()->directory;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>enabled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $d->getEnabled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getEnabled() : bool
    {
        return $this->getProperty()->enabled;
    }
    
/**
<documentation><description><p>Returns <code>extensionsToStrip</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getExtensionsToStrip()
    {
        if( isset( $this->getProperty()->extensionsToStrip ) )
            return $this->getProperty()->extensionsToStrip;
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>publishASCII</code>.</p></description>
<example>echo u\StringUtility::boolToString( $d->getPublishASCII() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getPublishASCII() : bool
    {
        return $this->getProperty()->publishASCII;
    }
    
/**
<documentation><description><p>Returns <code>transportId</code>.</p></description>
<example>echo $d->getTransportId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTransportId() : string
    {
        return $this->getProperty()->transportId;
    }
    
/**
<documentation><description><p>Returns <code>transportPath</code>.</p></description>
<example>echo $d->getTransportPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTransportPath() : string
    {
        return $this->getProperty()->transportPath;
    }
    
/**
<documentation><description><p>Returns <code>webUrl</code>.</p></description>
<example>echo $d->getWebUrl(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getWebUrl() : string
    {
        return $this->getProperty()->webUrl;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the destination is
applicable to the group.</p></description>
<example>echo u\StringUtility::boolToString( $d->hasGroup( $g ) ), BR;</example>
<return-type>bool</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function hasGroup( Group $g ) : bool
    {
        if( $g == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
            
        $group_name = $g->getName();
        
        $group_array = explode( self::DELIMITER, $this->getProperty()->applicableGroups );
        return in_array( $group_name, $group_array );
    }
    
/**
<documentation><description><p>Removes the group name from <code>applicableGroups</code>
and returns the calling object.</p></description>
<example>$d->removeGroup( $g )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function removeGroup( Group $g ) : Asset
    {
        if( $g == NULL )
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_GROUP . E_SPAN );
            
        $group_name = $g->getName();
        
        $group_array = explode( self::DELIMITER, $this->getProperty()->applicableGroups );
        $temp        = array();
        
        foreach( $group_array as $group )
        {
            if( $group != $group_name )
            {
                $temp[] = $group;
            }
        }
    
        $this->getProperty()->applicableGroups = implode( self::DELIMITER, $temp );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>checkedByDefault</code> and returns the calling
object.</p></description>
<example>$d->setCheckedByDefault( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setCheckedByDefault( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->checkedByDefault = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>directory</code> and returns the calling
object.</p></description>
<example>$d->setDirectory( 'test' )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setDirectory( string $d ) : Asset
    {
        if( trim( $d ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_DIRECTORY . E_SPAN );
        }
        
        $this->getProperty()->directory = $d;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>enabled</code> and returns the calling
object.</p></description>
<example>$d->setEnabled( true )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setEnabled( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->enabled = $bool;
        return $this;
    }
    
/**
<documentation><description><p>Set <code>extensionsToStrip</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setExtensionsToStrip( $ext=NULL )
    {
        $this->getProperty()->extensionsToStrip = $ext;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>publishASCII</code> and returns the calling
object.</p></description>
<example>$d->setPublishASCII( false )->edit();</example>
<return-type>Asset</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setPublishASCII( bool $bool ) : Asset
    {
        if( !c\BooleanValues::isBoolean( $bool ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $bool must be a boolean." . E_SPAN );

        $this->getProperty()->publishASCII = $bool;
        return $this;
    }

/**
<documentation><description><p>Sets <code>transportId</code> and <code>transportPath</code>, and returns the calling object.</p></description>
<example>$d->setTransport( $t )->edit();</example>
<return-type>Asset</return-type>
<exception>NullAssetException</exception>
</documentation>
*/
    public function setTransport( Transport $t ) : Asset
    {
        if( $t == NULL )
        {
            throw new e\NullAssetException( 
                S_SPAN . c\M::NULL_TRANSPORT . E_SPAN );
        }
        
        $this->getProperty()->transportId   = $t->getId();
        $this->getProperty()->transportPath = $t->getPath();
        return $this;
    }

/**
<documentation><description><p>Sets <code>webUrl</code> and returns the calling
object.</p></description>
<example>$d->setWebUrl( 'http://web.upstate.edu/test' )->edit();</example>
<return-type>Asset</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setWebUrl( string $u ) : Asset
    {
        if( trim( $u ) == "" )
        {
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_URL . E_SPAN );
        }
        
        $this->getProperty()->webUrl = $u;
        return $this;
    }
}
?>