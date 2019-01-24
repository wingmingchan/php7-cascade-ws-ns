<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/24/2018 Updated documentation.
  * 12/27/2017 Added REST code and updated documentation.
    Changed signatures of setConfigurationPageRegionBlock and setConfigurationPageRegionFormat
    to allow NULL values.
  * 6/26/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON structure and JSON dump.
  * 5/28/2015 Added namespaces.
      Swapped the last two arguments of PageConfiguration.
  * 9/22/2014 Fixed a bug in addPageConfiguration.
  * 9/8/2014 Fixed a bug in deletePageConfiguration.
  * 7/3/2014 Added addConfiguration.
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
<p>A <code>PageConfigurationSet</code> object represents a page configuration set asset.</p>
<h2>Structure of <code>pageConfigurationSet</code></h2>
<pre>SOAP:
pageConfigurationSet
  id
  name
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  pageConfigurations
    pageConfiguration
      id
      name
      defaultConfiguration
      templateId
      templatePath
      formatId
      formatPath
      formatRecycled
      pageRegions
        pageRegion
          id
          name
          blockId
          blockPath
          blockRecycled
          noBlock
          formatId
          formatPath
          formatRecycled
          noFormat
      outputExtension
      serializationType
      includeXMLDeclaration
      publishable

REST:
pageConfigurationSet
  pageConfigurations (array)
    stdClass
      name
      defaultConfiguration
      templateId
      templatePath
      formatId
      formatPath
      formatRecycled
      pageRegions (array)
        stdClass
          name
          blockId
          blockPath
          blockRecycled
          noBlock
          formatId
          formatPath
          formatRecycled
          noFormat
          id
      serializationType
      outputExtension
      includeXMLDeclaration
      publishable
      id
  parentContainerId
  parentContainerPath
  path
  siteId
  siteName
  name
  id
</pre>
<p>There is an important issue related to page regions of a configuration. A configuration contains a page region only if the page region is attached with either a block or a format or both. If a region is not attached with a block or a format, then it will not show up in the configuration; namely, it does not exist. To test whether a certain region exists in a configuration, do that test through the associated <code>Template</code> object.</p>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "pageConfigurationSet" ),
        array( "getComplexTypeXMLByName" => "page-configurations" ),
        array( "getComplexTypeXMLByName" => "pageConfiguration" ),
        array( "getComplexTypeXMLByName" => "page-regions" ),
        array( "getComplexTypeXMLByName" => "pageRegion" ),
        array( "getSimpleTypeXMLByName"  => "serialization-type" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/page_configuration_set.php">page_configuration_set.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>http://mydomain.edu:1234/api/v1/read/pageconfigurationset/6188631a8b7ffe8377b637e8d9af95ee

{
  "asset":{
    "pageConfigurationSet":{
      "pageConfigurations":[
      {
        "name":"HTM",
        "defaultConfiguration":true,
        "templateId":"618863fc8b7ffe8377b637e865012e5d",
        "templatePath":"core/xml",
        "formatRecycled":false,
        "pageRegions":[],
        "serializationType":"HTML",
        "outputExtension":".php",
        "includeXMLDeclaration":false,
        "publishable":true,
        "id":"6188631a8b7ffe8377b637e8e5007426"
      } ],
      "parentContainerId":"61885b988b7ffe8377b637e865c21202",
      "parentContainerPath":"/",
      "path":"HTM",
      "siteId":"61885ac08b7ffe8377b637e83a86cca5",
      "siteName":"_brisk",
      "name":"HTM",
      "id":"6188631a8b7ffe8377b637e8d9af95ee"
    }
  },
  "authentication":{
    "username":"user",
    "password":"secret"
  }
}</pre>
</postscript>
</documentation>
*/
class PageConfigurationSet extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::CONFIGURATIONSET;
    
/**
<documentation><description><p>The constructor, overriding the parent method to process
the page configurations.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->processPageConfigurations();
    }
    
/**
<documentation><description><p>An alias of <code>addPageConfiguration</code>.</p></description>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function addConfiguration(
        string $name, Template $t, string $extension, string $type ) : Asset
    {
        return $this->addPageConfiguration( $name, $t, $extension, $type );
    }
    
/**
<documentation><description><p>Adds a configuration to the configuration set, calls
<code>edit</code>, and returns the calling object.</p></description>
<example>$pcs->addPageConfiguration( 
    'XML', // name
    $cascade->getAsset( a\Template::TYPE, 'fd27b6798b7f08560159f3f08e013f23' ),
    '.xml',
    T::XML
);</example>
<return-type>Asset</return-type>
<exception>EmptyValueException, WrongSerializationTypeException</exception>
</documentation>
*/
    public function addPageConfiguration(
        string $name, Template $t, string $extension, string $type ) : Asset
    {
        if( trim( $extension ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_FILE_EXTENSION . E_SPAN );
            
        if( !c\SerializationTypeValues::isSerializationTypeValue( $type ) )
            throw new e\WrongSerializationTypeException( 
                S_SPAN . "The serialization type $type is not acceptable. " . E_SPAN );
        
        $config                    = AssetTemplate::getPageConfiguration();
        $config->name              = $name;
        $config->templateId        = $t->getId();
        $config->templatePath      = $t->getPath();
        $config->pageRegions       = $t->getPageRegionStdForPageConfiguration();
        $config->outputExtension   = $extension;
        $config->serializationType = $type;

        $p = new p\PageConfiguration( $config, $this->getService(), NULL );
        $this->page_configurations[] = $p;
        $this->edit();
        
        if( $this->getService()->isSoap() )
            $this->processPageConfigurations( 
                $this->getProperty()->pageConfigurations->pageConfiguration );
        elseif( $this->getService()->isRest() )
            $this->processPageConfigurations( 
                $this->getProperty()->pageConfigurations );

        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>deletePageConfiguration</code>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function deleteConfiguration( string $name ) : Asset
    {
        return $this->deletePageConfiguration( $name );        
    }
    
/**
<documentation><description><p>Deletes the named page configuration from the set, calls
<code>edit</code>, and returns the calling object.</p></description>
<example>$pcs->deletePageConfiguration( 'XML' )->dump();</example>
<return-type>Asset</return-type>
<exception>Exception</exception>
</documentation>
*/
    public function deletePageConfiguration( string $name ) : Asset
    {
        if( $this->getDefaultConfiguration() == $name )
        {
            throw new \Exception( 
                S_SPAN . "Cannot delete the default configuration." . E_SPAN );
        }
        
        if( !$this->hasConfiguration( $name ) )
            return $this;
            
        $id = $this->page_configuration_map[ $name ]->getId();
        $service = $this->getService();
        $service->delete( $service->createId( c\T::CONFIGURATION, $id ) );
        
        $this->reloadProperty();
        
        if( $this->getService()->isSoap() )
            $this->processPageConfigurations(
                $this->getProperty()->pageConfigurations->pageConfiguration );
        elseif( $this->getService()->isRest() )
            $this->processPageConfigurations(
                $this->getProperty()->pageConfigurations );

        return $this;        
    }
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
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
        $asset        = new \stdClass();
        $config_array = array();
        $config_count = count( $this->page_configurations );
        
        // convert PageConfiguration objects back to stdClass objects
        for( $i = 0; $i < $config_count; $i++ )
        {
            $config_array[ $i ] = $this->page_configurations[ $i ]->toStdClass();
        }
        
        if( $this->getService()->isSoap() )
            $this->getProperty()->pageConfigurations->pageConfiguration = $config_array;
        elseif( $this->getService()->isRest() )
            $this->getProperty()->pageConfigurations = $config_array;
        
        $asset->{ $p = $this->getPropertyName() } = $this->getProperty();
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
<documentation><description><p>An alias of <code>getPageConfiguration</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getConfiguration( string $name ) : p\Property
    {
        return $this->getPageConfiguration( $name );
    }
    
/**
<documentation><description><p>Returns the default page configuration (a <a href="http://www.upstate.edu/web-services/api/property-classes/page-configuration.php"><code>p\PageConfiguration</code></a> object).</p></description>
<example>$default_config =  $pcs->getDefaultConfiguration();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultConfiguration() : p\Property
    {
        // there must one
        foreach( $this->page_configurations as $page_configuration )
        {
            if( $page_configuration->getDefaultConfiguration() ) // bool
            {
                return $page_configuration;
            }
        }
    }
    
/**
<documentation><description><p>Returns <code>includeXMLDeclaration</code> of the named
page configuration.</p></description>
<example>echo u\StringUtility::boolToString(
    $pcs->getIncludeXMLDeclaration( "PDF" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIncludeXMLDeclaration( string $config ) : bool
    {
        return $this->page_configuration_map[ $config ]->getIncludeXMLDeclaration();
    }
    
/**
<documentation><description><p>Returns <code>outputExtension</code> of the named page
configuration.</p></description>
<example>echo u\StringUtility::boolToString(
    $pcs->getOutputExtension( "PDF" ) ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getOutputExtension( string $config ) : string
    {
        return $this->page_configuration_map[ $config ]->getOutputExtension();
    }
    
/**
<documentation><description><p>Returns the named <a href="http://www.upstate.edu/web-services/api/property-classes/page-configuration.php"><code>p\PageConfiguration</code></a> object.</p></description>
<example>u\DebugUtility::dump( $pcs->getPageConfiguration( 
    $default_config->getName() )->
    getPageRegionNames() );</example>
<return-type>Property</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function getPageConfiguration( string $name ) : p\Property
    {
        $this->checkPageConfiguration( $name );
        $count = $this->page_configurations;
        
        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->page_configurations[ $i ]->getName() == $name )
                return $this->page_configurations[ $i ];
        }
    }
    
/**
<documentation><description><p>Returns an array of the names of page configurations in the set.</p></description>
<example>u\DebugUtility::dump( $pcs->getPageConfigurationNames() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurationNames() : array
    {
        return $this->page_configuration_names;
    }
    
/**
<documentation><description><p>Returns an array of <a href="http://www.upstate.edu/web-services/api/property-classes/page-configuration.php"><code>p\PageConfiguration</code></a> objects.</p></description>
<example>u\DebugUtility::dump( $pcs->getPageConfigurations() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getPageConfigurations() : array
    {
        $config_array = array();
        $config_count = count( $this->page_configurations );
        
        for( $i = 0; $i < $config_count; $i++ )
        {
            $config_array[ $i ] = $this->page_configurations[ $i ];
        }
        
        return $config_array;
    }
    
/**
<documentation><description><p>Returns the <a href="http://www.upstate.edu/web-services/api/asset-classes/template.php"><code>Template</code></a> object associated with the named page configuration.</p></description>
<example>$pcs->getPageConfigurationTemplate( "PDF" )->dump();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function getPageConfigurationTemplate( string $name ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $count = $this->page_configurations;

        for( $i = 0; $i < $count; $i++ )
        {
            if( $this->page_configurations[ $i ]->getName() == $name )
            {
                $id = $this->page_configurations[ $i ]->getTemplateId();
                return Asset::getAsset( $this->getService(), Template::TYPE, $id );
            }
        }
    }
    
/**
<documentation><description><p>Returns the names of page regions of the named page
configuration. Note that this array is non-empty only if there is at least one page region
attached with a block and/or a format. If no blocks and formats are attached to any
regions, this array will be empty.</p></description>
<example>u\DebugUtility::dump( $pcs->getPageRegionNames( "PDF" ) );</example>
<return-type>array</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function getPageRegionNames( string $name ) : array
    {
        $this->checkPageConfiguration( $name );
        $config = $this->page_configuration_map[ $name ];
        u\DebugUtility::dump( $config->toStdClass() );
        
        return $this->page_configuration_map[ $name ]->getPageRegionNames();
    }
    
/**
<documentation><description><p>Returns the named <a href="http://www.upstate.edu/web-services/api/property-classes/page-region.php"><code>p\PageRegion</code></a> object of the named page configuration. Note that this method returns a <code>p\PageRegion</code> only if the region is attached with a block and/or a format. If the region is not attached with anything, then the region does not exist. This includes the <code>DEFAULT</code> region.</p></description>
<example></example>
<return-type>Property</return-type>
<exception>NoSuchPageConfigurationException, NoSuchPageRegionException</exception>
</documentation>
*/
    public function getPageRegion( string $name, string $region_name ) : p\Property
    {
        $this->checkPageConfiguration( $name );
        return $this->page_configuration_map[ $name ]->getPageRegion( $region_name );
    }
    
/**
<documentation><description><p>Returns <code>publishable</code> of the named page configuration.</p></description>
<example>if( $pcs->getPublishable( $default_config->getName() ) )
{
    echo "The default config is publishable" . BR;
}</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getPublishable( string $name ) : bool
    {
        return $this->page_configuration_map[ $name ]->getPublishable();
    }
    
/**
<documentation><description><p>Returns <code>serializationType</code> of the named page configuration.</p></description>
<example>echo $pcs->getSerializationType( "PDF" ), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSerializationType( string $name ) : string
    {
        return $this->page_configuration_map[ $name ]->getSerializationType();
    }
    
/**
<documentation><description><p>An alias of <code>hasPageConfiguration</code>.</p></description>
<example></example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasConfiguration( string $name ) : bool
    {
        return $this->hasPageConfiguration( $name );
    }

/**
<documentation><description><p>Returns a bool, indicating whether the named configuration exists in the set.</p></description>
<example>echo u\StringUtility::boolToString( $pcs->hasPageConfiguration( "XML" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPageConfiguration( string $name ) : bool
    {
        return in_array( $name, $this->page_configuration_names );
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the named region exists in the named page configuration. Note that this method returns true only if the region is
attached with a block and/or a format. If the region is not attached with anything, then
the region does not exist. This includes the <code>DEFAULT</code> region.</p></description>
<example>echo u\StringUtility::boolToString( $pcs->hasPageRegion( "Mobile", "DEFAULT" ) ), BR;</example>
<return-type>bool</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function hasPageRegion( string $name, string $region_name ) : bool
    {
        $this->checkPageConfiguration( $name );
        return $this->page_configuration_map[ $name ]->hasPageRegion( $region_name );
    }
    
/**
<documentation><description><p>Attaches the block to the named region of the named page
configuration, and returns the calling object. Note that when a region is not attached with anything, that region does not exist in the configuration. To test whether a region exist, do the test through the associated <code>Template</code> object.</p></description>
<example>$pcs->setConfigurationPageRegionBlock( 'Mobile', 'DEFAULT',
    $cascade->getAsset( 
        a\DataBlock::TYPE, 
        'c23e62358b7f0856002a5e11909ccae3' )
)->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setConfigurationPageRegionBlock(
        string $name, string $region_name, Block $block=NULL ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $config = $this->page_configuration_map[ $name ];
        $config->setPageRegionBlock( $region_name, $block );
        return $this;
    }
    
/**
<documentation><description><p>Attaches the format to the named region of the named page
configuration, and returns the calling object.</p></description>
<example>$pcs->setConfigurationPageRegionFormat( 'Mobile', 'DEFAULT',
    $cascade->getAsset( 
        a\XsltFormat::TYPE, 
        '404872688b7f0856002a5e11bb8c8642' )
)->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setConfigurationPageRegionFormat(
        string $name, string $region_name, Format $format=NULL ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $config = $this->page_configuration_map[ $name ];
        $config->setPageRegionFormat( $region_name, $format );
        return $this;
    }
    
/**
<documentation><description><p>Sets the named page configuration as the default
configuration, unsets all other page configurations, and returns the calling
object.</p></description>
<example>$pcs->setDefaultConfiguration( "Mobile" )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setDefaultConfiguration( string $name ) : Asset
    {
        $this->checkPageConfiguration( $name );
        
        foreach( $this->page_configurations as $page_configuration )
        {
            if( $page_configuration->getName() != $name )
            {
                $page_configuration->setDefaultConfiguration( false );
            }
            else
            {
                $page_configuration->setDefaultConfiguration( true );
            }
        }
        return $this;
    }
    
/**
<documentation><description><p>Attaches the format to the named page configuration
and returns the calling object.</p></description>
<example>$pcs->setFormat( "Mobile",
    $cascade->getAsset( 
        a\XsltFormat::TYPE, 
        '404872688b7f0856002a5e11bb8c8642' )
)->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setFormat( string $name, Format $format ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setFormat( $format );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>includeXMLDeclaration</code> of the named page
configuration, and returns the calling object.</p></description>
<example>$pcs->setIncludeXMLDeclaration( "Mobile", true )->edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setIncludeXMLDeclaration( string $name, bool $i ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setIncludeXMLDeclaration( $i );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>outputExtension</code> of the named page
configuration, and returns the calling object.</p></description>
<example>$pcs->setOutputExtension( "Mobile", ".php" )->
    setPublishable( "Mobile", true )->
    setSerializationType( "Mobile", "XML" )->
    edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setOutputExtension( string $name, string $ext ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setOutputExtension( $ext );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>publishable</code> of the named page
configuration, and returns the calling object.</p></description>
<example>$pcs->setOutputExtension( "Mobile", ".php" )->
    setPublishable( "Mobile", true )->
    setSerializationType( "Mobile", "XML" )->
    edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setPublishable( string $name, bool $p ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setPublishable( $p );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>serializationType</code> of the named page
configuration, and returns the calling object.</p></description>
<example>$pcs->setOutputExtension( "Mobile", ".php" )->
    setPublishable( "Mobile", true )->
    setSerializationType( "Mobile", "XML" )->
    edit();</example>
<return-type>Asset</return-type>
<exception>NoSuchPageConfigurationException</exception>
</documentation>
*/
    public function setSerializationType( string $name, string $type ) : Asset
    {
        $this->checkPageConfiguration( $name );
        $this->page_configuration_map[ $name ]->setSerializationType( $type );
        return $this;
    }
    
    private function checkPageConfiguration( string $name )
    {
        if( !in_array( $name, $this->page_configuration_names ) )
        {
            throw new e\NoSuchPageConfigurationException( 
                S_SPAN . "The page configuration $name does not exist." . E_SPAN );
        }
    }
    
    private function processPageConfigurations()
    {
        $this->page_configurations      = array();
        $this->page_configuration_names = array();
        $this->page_configuration_map   = array();

        if( $this->getService()->isSoap() )
            $array = $this->getProperty()->pageConfigurations->pageConfiguration;
        elseif( $this->getService()->isRest() )
            $array = $this->getProperty()->pageConfigurations;
        
        if( isset( $array ) )
        {
            // stdClass object
            if( !is_array( $array ) )
            {
                $array = array( $array );
            }
        }
        
        $service = $this->getService();
        
        foreach( $array as $page_configuration )
        {
            $p = new p\PageConfiguration(
                $page_configuration, $this->getService(), NULL );
            $this->page_configurations[] = $p;
            $this->page_configuration_names[] = $page_configuration->name;
            $this->page_configuration_map[ $page_configuration->name ] = $p;
        }
    }

    private $page_configurations;
    private $page_configuration_names;
    private $page_configuration_map;
}
?>