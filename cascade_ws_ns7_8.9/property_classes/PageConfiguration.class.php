<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/26/2017 Changed toStdClass so that it works with REST.
  * 7/11/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/25/2017 Fixed in bug.
  * 12/29/2015 Added three more constants.
  * 5/28/2015 Added namespaces.
  * Swapped the last two arguments of the constructor.
  * 12/29/2015 Added more constants.
  * 6/5/2014 Added getTemplate. Rewrote setPageRegionBlock and setPageRegionFormat.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

/**
<documentation><description>
<?php global $service;
$doc_string = "<h2>Introduction</h2>
<p>A <code>PageConfiguration</code> object represents a <code>pageConfiguration</code> property found in a page configuration set.</p>
<h2>Structure of <code>pageConfiguration</code></h2>
<pre>pageConfigurations
  pageConfiguration (NULL, stdClass or array of stdClass)
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
    outputExtension
    serializationType
    includeXMLDeclaration
    publishable
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "page-configurations" ),
        array( "getComplexTypeXMLByName" => "pageConfiguration" ),
        array( "getComplexTypeXMLByName" => "page-regions" ),
        array( "getComplexTypeXMLByName" => "pageRegion" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/page_configuration.php">page_configuration.php</a></li></ul></postscript>
</documentation>
*/
class PageConfiguration extends Property
{
    const DEBUG = false;
    
    const DATA_TYPE_HTML = 'HTML';
    const DATA_TYPE_XML  = 'XML';
    const DATA_TYPE_PDF  = 'PDF';
    const DATA_TYPE_RTF  = 'RTF';
    const DATA_TYPE_JSON = 'JSON';
    const DATA_TYPE_JS   = 'JS';
    const DATA_TYPE_CSS  = 'CSS';
    
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        \stdClass $configuration=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $type=NULL,
        $data2=NULL, 
        $data3=NULL )
    {
        if( is_null( $service ) )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
        
        $this->service = $service;

        if( isset( $configuration ) )
        {
            if( isset( $configuration->id ) )
                $this->id                      = $configuration->id;
            if( isset( $configuration->name ) )
                $this->name                    = $configuration->name;
            if( isset( $configuration->defaultConfiguration ) )
                $this->default_configuration   = $configuration->defaultConfiguration;
            if( isset( $configuration->templateId ) )
                $this->template_id             = $configuration->templateId;
            if( isset( $configuration->templatePath ) )
                $this->template_path           = $configuration->templatePath;
            if( isset( $configuration->formatId ) )
                $this->format_id               = $configuration->formatId;
            if( isset( $configuration->formatPath ) )
                $this->format_path             = $configuration->formatPath;
            if( isset( $configuration->formatRecycled ) )
                $this->format_recycled         = $configuration->formatRecycled;
            if( isset( $configuration->outputExtension ) )
                $this->output_extension        = $configuration->outputExtension;
            if( isset( $configuration->serializationType ) )
                $this->serialization_type      = $configuration->serializationType;
            if( isset( $configuration->includeXMLDeclaration ) )
                $this->include_xml_declaration = $configuration->includeXMLDeclaration;
            if( isset( $configuration->publishable ) )
                $this->publishable             = $configuration->publishable;
        
            $this->page_regions            = array(); // order page regions
            $this->page_region_map         = array(); // name->page region map

            if( isset( $configuration->pageRegions ) )
            {
                if( $this->service->isSoap() && isset(
                    $configuration->pageRegions->pageRegion ) )
                    a\Template::processPageRegions( 
                        $configuration->pageRegions->pageRegion, 
                        $this->page_regions, 
                        $this->page_region_map,
                        $this->service );
                elseif( $this->service->isRest() )
                    a\Template::processPageRegions( 
                        $configuration->pageRegions, 
                        $this->page_regions, 
                        $this->page_region_map,
                        $this->service );
            }
            
            if( isset( $type ) && $type == c\T::PAGE )
            {
                $this->type = c\T::PAGE;
            }
        }
    }
    
/**
<documentation><description><p>Displays some basic information of the page configuration, and returns the calling object.</p></description>
<example>$pc->display();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function display() : Property
    {
        echo "ID: " . $this->id . BR .
             "Name: " . $this->name . BR;
        return $this;
    }
    
/**
<documentation><description><p>Dumps the information of the object, and returns the
calling object.</p></description>
<example>$pc->dump();</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function dump( bool $formatted=true ) : Property
    {
        if( $formatted ) echo S_H2 . c\L::READ_DUMP . E_H2 . S_PRE;
        var_dump( $this->toStdClass() );
        if( $formatted ) echo E_PRE . HR;
        
        return $this;
    }

/**
<documentation><description><p>Returns <code>defaultConfiguration</code>.</p></description>
<example>echo u\StringUtility::boolToString( $pc->getDefaultConfiguration() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getDefaultConfiguration() : bool
    {
        return $this->default_configuration;
    }
    
/**
<documentation><description><p>Returns <code>formatId</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $pc->getFormatId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatId()
    {
        return $this->format_id;
    }
    
/**
<documentation><description><p>Returns <code>formatPath</code>.</p></description>
<example>echo u\StringUtility::getCoalescedString( $pc->getFormatPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatPath()
    {
        return $this->format_path;
    }
    
/**
<documentation><description><p>Returns <code>formatRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $pc->getFormatRecycled() ), BR;</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getFormatRecycled() : bool
    {
        return $this->format_recycled;
    }
    
/**
<documentation><description><p>Returns <code>id</code>.</p></description>
<example>echo $pc->getId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getId() : string
    {
        return $this->id;
    }
    
/**
<documentation><description><p>Returns <code>includeXMLDeclaration</code>.</p></description>
<example>echo u\StringUtility::boolToString( $pc->getIncludeXMLDeclaration() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getIncludeXMLDeclaration() : bool
    {
        return $this->include_xml_declaration;
    }
    
/**
<documentation><description><p>Returns <code>name</code>.</p></description>
<example>echo $pc->getName(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getName() : string
    {
        return $this->name;
    }
    
/**
<documentation><description><p>Returns <code>outputExtension</code>.</p></description>
<example>echo $pc->getOutputExtension(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getOutputExtension() : string
    {
        return $this->output_extension;
    }
    
/**
<documentation><description><p>Returns the <code>PageRegion</code> object bearing the name. Note that for a page configuration, a region does not exist unless the region is attached with a block and/or a format.</p></description>
<example>u\DebugUtility::dump( $pc->getPageRegion( "DEFAULT" ) );</example>
<return-type>mixed</return-type>
<exception>NoSuchPageRegionException</exception>
</documentation>
*/
    public function getPageRegion( $name )
    {
        $this->checkPageRegion( $name );
        return $this->page_region_map[ $name ];
    }
    
/**
<documentation><description><p>Returns an array of page regoin names. Note that for a page configuration, a region does not exist unless the region is attached with a block and/or a format. To get all the names, use <code>Template::getPageRegionNames</code> instead.</p></description>
<example>u\DebugUtility::dump( $pc->getPageRegionNames() );</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegionNames()
    {
        return array_keys( $this->page_region_map );
    }
    
/**
<documentation><description><p>Returns an array of <code>PageRegion</code> objects. Note that for a page configuration, a region does not exist unless the region is attached with a block and/or a format.</p></description>
<example>u\DebugUtility::dump( $pc->getPageRegionNames() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegions() : array
    {
        return $this->page_regions;
    }
    
/**
<documentation><description><p>Returns the block attached to the named region or <code>NULL</code>.</p></description>
<example>u\DebugUtility::dump( $pc->getPageRegionBlock( "DEFAULT" ) );</example>
<return-type>mixed</return-type>
<exception>NoSuchPageRegionException</exception>
</documentation>
*/
    public function getPageRegionBlock( string $name )
    {
        $this->checkPageRegion( $name );
        $page_region = $this->page_region_map[ $name ];
        
        return $page_region->getBlock();
    }
    
/**
<documentation><description><p>Returns the format attached to the named region or <code>NULL</code>.</p></description>
<example>u\DebugUtility::dump( $pc->getPageRegionFormat( "DEFAULT" ) );</example>
<return-type>mixed</return-type>
<exception>NoSuchPageRegionException</exception>
</documentation>
*/
    public function getPageRegionFormat( string $name )
    {
        $this->checkPageRegion( $name );
        $page_region = $this->page_region_map[ $name ];
        
        return $page_region->getFormat();
    }
    
/**
<documentation><description><p>Returns <code>publishable</code>.</p></description>
<example>echo u\StringUtility::boolToString( $pc->getPublishable() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getPublishable() : bool
    {
        return $this->publishable;
    }
    
/**
<documentation><description><p>Returns <code>serializationType</code>.</p></description>
<example>echo $pc->getSerializationType(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getSerializationType() : string
    {
        return $this->serialization_type;
    }
    
/**
<documentation><description><p>Returns the associated <code>a\Template</code> object.</p></description>
<example>$pc->getTemplate()->dump();</example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function getTemplate()
    {
        if( $this->service == NULL )
            throw new e\NullServiceException( 
                S_SPAN . c\M::NULL_SERVICE . E_SPAN );
            
        return a\Asset::getAsset( $this->service, a\Template::TYPE, $this->template_id );
    }
    
/**
<documentation><description><p>Returns <code>templateId</code>.</p></description>
<example>echo $pc->getTemplateId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTemplateId() : string
    {
        return $this->template_id;
    }

/**
<documentation><description><p>Returns <code>templatePath</code>.</p></description>
<example>echo $pc->getTemplatePath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getTemplatePath() : string
    {
        return $this->template_path;
    }

/**
<documentation><description><p>Returns a bool, indicating whether the named region exists.
Note that for a page configuration, a region does not exist unless the region is attached
with a block and/or a format.</p></description>
<example>echo u\StringUtility::boolToString( $pc->hasPageRegion( "LOGO" ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPageRegion( string $region_name ) : bool
    {
        if( self::DEBUG ) {
            u\DebugUtility::out( "Region name fed in: " . $region_name ); }
    
        return isset( $this->page_region_map[ $region_name ] );
    }
    
/**
<documentation><description><p>Sets <code>defaultConfiguration</code> and returns the
calling object.</p></description>
<example>u\DebugUtility::dump( $pc->setDefaultConfiguration( true )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setDefaultConfiguration( bool $v ) : Property
    {
        if( !c\BooleanValues::isBoolean( $v ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $v is not a boolean." . E_SPAN );
        $this->default_configuration = $v;
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>format</code> and returns the calling
object.</p></description>
<example>u\DebugUtility::dump( $pc->setFormat(
    $cascade->getAsset( a\XsltFormat::TYPE, "255a4cec8b7ffe3b00a7e3433e083063" )
)->toStdClass() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setFormat( a\Format $format=NULL ) : Property
    {
        if( isset( $format ) )
        {
            if( $this->type != c\T::PAGE && $format->getType() != c\T::XSLTFORMAT )
            {
                throw new \Exception( S_SPAN . "Wrong type of format." . E_SPAN );
            }
        
            $this->format_id   = $format->getId();
            $this->format_path = $format->getPath();
        }
        else
        {
            $this->format_id   = NULL;
            $this->format_path = NULL;
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>includeXMLDeclaration</code> and returns the
calling object.</p></description>
<example>u\DebugUtility::dump( $pc->setIncludeXMLDeclaration( true )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setIncludeXMLDeclaration( bool $include_xml_declaration ) : Property
    {
        if( !c\BooleanValues::isBoolean( $include_xml_declaration ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $include_xml_declaration is not a boolean." . 
                E_SPAN );
                
        $this->include_xml_declaration = $include_xml_declaration;
        return $this;
    }

/**
<documentation><description><p>Sets <code>outputExtension</code> and returns the calling
object.</p></description>
<example>u\DebugUtility::dump( $pc->setOutputExtension( ".html" )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>EmptyValueException</exception>
</documentation>
*/
    public function setOutputExtension( string $ext ) : Property
    {
        $ext = trim( $ext );
        
        if( $ext == '' )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_FILE_EXTENSION . E_SPAN );
        }
        // garbage in, garbage out
        $this->output_extension = $ext;
        return $this;
    }
    
/**
<documentation><description><p>Sets the block of the named region, and returns the calling
object.</p></description>
<example>u\DebugUtility::dump( $pc->setPageRegionBlock( "SEARCH PRINT" )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>NoSuchPageRegionException</exception>
</documentation>
*/
    public function setPageRegionBlock(
        string $page_region_name, a\Block $block=NULL ) : Property
    {
        $regions = $this->getTemplate()->getRegionNames();
        
        if( !in_array( $page_region_name, $regions ) )
        {
            throw new e\NoSuchPageRegionException(
                S_SPAN . "The page region $page_region_name does not exist." . E_SPAN );
        }
        
        if( !isset( $this->page_region_map[ $page_region_name ] ) )
        {
            $this->addPageRegion( $page_region_name );
        }
        
        $this->page_region_map[ $page_region_name ]->setBlock( $block );
        return $this;
    }
    
/**
<documentation><description><p>Sets the format of the named region, and returns the
calling object.</p></description>
<example>u\DebugUtility::dump( $pc->setPageRegionFormat( "LAST MODIFIED" )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>NoSuchPageRegionException</exception>
</documentation>
*/
    public function setPageRegionFormat(
        string $page_region_name, a\Format $format=NULL ) : Property
    {
        $regions = $this->getTemplate()->getRegionNames();
        
        if( !in_array( $page_region_name, $regions ) )
        {
            throw new e\NoSuchPageRegionException(
                S_SPAN . "The page region $page_region_name does not exist." . E_SPAN );
        }
        
        if( !isset( $this->page_region_map[ $page_region_name ] ) )
        {
            $this->addPageRegion( $page_region_name );
        }
        
        $this->page_region_map[ $page_region_name ]->setFormat( $format );
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>publishable</code> and returns the calling
object.</p></description>
<example>u\DebugUtility::dump( $pc->setPublishable( false )->toStdClass() );</example>
<return-type></return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setPublishable( bool $publishable ) : Property
    {
        if( !c\BooleanValues::isBoolean( $publishable ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $publishable is not a boolean." . E_SPAN );
            
        $this->publishable = $publishable;
        return $this;
    }
    
/**
<documentation><description><p>An alias of <code>setPageRegionBlock</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setRegionBlock( string $region_name, a\Block $block=NULL ) : Property
    {
        return $this->setPageRegionBlock( $region_name, $block );
    }
    
/**
<documentation><description><p>An alias of <code>setPageRegionFormat</code>.</p></description>
<example></example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setRegionFormat(
        string $region_name, a\Format $format=NULL ) : Property
    {
        return $this->setPageRegionFormat( $region_name, $format );
    }
    
/**
<documentation><description><p>Sets <code>noBlock</code> of the named region with the
supplied value and returns the calling object.</p></description>
<example>u\DebugUtility::dump( $pc->setRegionNoBlock( "SEARCH PRINT", true )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>NoSuchPageRegionException, UnacceptableValueException</exception>
</documentation>
*/
    public function setRegionNoBlock( string $name, bool $no_block ) : Property
    {
        $this->checkPageRegion( $name );
        
        if( !c\BooleanValues::isBoolean( $no_block ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $no_block is not a boolean." . E_SPAN );
                
        $region = $this->page_region_map[ $name ];
        $region->setNoBlock( $no_block );        
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>noFormat</code> of the named region with the
supplied value and returns the calling object.</p></description>
<example>u\DebugUtility::dump( $pc->setRegionNoFormat( "SEARCH PRINT", true )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>NoSuchPageRegionException</exception>
</documentation>
*/
    public function setRegionNoFormat( string $name, bool $no_format ) : Property
    {
        $this->checkPageRegion( $name );

        if( !c\BooleanValues::isBoolean( $no_format ) )
            throw new e\UnacceptableValueException( 
                S_SPAN . "The value $no_format is not a boolean." . E_SPAN );
                
        $region = $this->page_region_map[ $name ];
        $region->setNoFormat( $no_format );
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>serializationType</code> and returns the
calling object.</p></description>
<example>u\DebugUtility::dump( $pc->setSerializationType( "XML" )->toStdClass() );</example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setSerializationType( string $serialization_type ) : Property
    {
        if( $serialization_type != self::DATA_TYPE_HTML &&
            $serialization_type != self::DATA_TYPE_XML &&
            $serialization_type != self::DATA_TYPE_PDF &&
            $serialization_type != self::DATA_TYPE_RTF )
            throw new e\UnacceptableValueException(
                S_SPAN . "The serialization type $serialization_type is unacceptable. " .
                E_SPAN );
    
        $this->serialization_type = $serialization_type;
        return $this;
    }

/**
<documentation><description><p>Sets <code>templateId</code> and <code>templatePath</code> and returns the calling object.</p></description>
<example>$pc->getTemplate()->dump();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setTemplate( a\Template $template ) : Property
    {
        $this->template_id   = $template->getId();
        $this->template_path = $template->getPath();
        return $this;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example></example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj                       = new \stdClass();
        $obj->id                   = $this->id;
        $obj->name                 = $this->name;
        $obj->defaultConfiguration = $this->default_configuration;
        $obj->templateId           = $this->template_id;
        $obj->templatePath         = $this->template_path;
        $obj->formatId             = $this->format_id;
        $obj->formatPath           = $this->format_path;
        $obj->formatRecycled       = $this->format_recycled;
        
        $region_count = count( $this->page_regions );
        
        if( $region_count > 0 )
        {
            if( $region_count == 1 )
            {
                if( $this->service->isSoap() )
                {
                    $obj->pageRegions = new \stdClass();
                    $obj->pageRegions->pageRegion = $this->page_regions[0]->toStdClass();
                }
                elseif( $this->service->isRest() )
                {
                    $obj->pageRegions = array( $this->page_regions[0]->toStdClass() );
                }
            }
            else
            {
                if( $this->service->isSoap() )
                {
                    $obj->pageRegions = new \stdClass();
                    $obj->pageRegions->pageRegion = array();
                }
                elseif( $this->service->isRest() )
                {
                    $obj->pageRegions = array();
                }
        
                foreach( $this->page_regions as $region )
                {
                    if( $this->service->isSoap() )
                        $obj->pageRegions->pageRegion[] = $region->toStdClass();
                    elseif( $this->service->isRest() )
                        $obj->pageRegions[] = $region->toStdClass();
                }
            }
        }
        else
        {
            if( $this->service->isSoap() )
                $obj->pageRegions = new \stdClass();
            elseif( $this->service->isRest() )
                $obj->pageRegions = array();
        }
        
        $obj->outputExtension       = $this->output_extension;
        $obj->serializationType     = $this->serialization_type;
        $obj->includeXMLDeclaration = $this->include_xml_declaration;
        $obj->publishable           = $this->publishable;
        
        return $obj;
    }

    private function addPageRegion( string $page_region_name ) : Property
    {
        if( !$this->getTemplate()->hasPageRegion( $page_region_name ) )
        {
            throw new e\NoSuchPageRegionException( 
                S_SPAN . "The page region $page_region_name does not exist.". E_SPAN );
        }
        
        // exists
        if( $this->hasPageRegion( $page_region_name ) )
        {
            return $this;
        }
        // does not exist
        $pr_std                  = new \stdClass();
        $pr_std->name            = $page_region_name;
        $pr_std->block_recycled  = false;
        $pr_std->no_block        = false;
        $pr_std->format_recycled = false;
        $pr_std->no_format       = false;
        
        $pr = new PageRegion( $pr_std, $this->service );
        $this->page_regions[]                       = $pr;
        $this->page_region_map[ $page_region_name ] = $pr;
        
        return $this;
    }
        
    private function checkPageRegion( string $name )
    {
        if( !isset( $this->page_region_map[ $name ] ) )
        {
            throw new e\NoSuchPageRegionException(
                S_SPAN . "The page region $name does not exist." . E_SPAN );
        }
    }

    private $id;
    private $name;
    private $default_configuration;
    private $template_id;
    private $template_path;
    private $format_id;
    private $format_path;
    private $format_recycled;
    private $output_extension;
    private $serialization_type;
    private $include_xml_declaration;
    private $publishable;
    private $page_regions;
    private $page_region_map;
    private $service;
    private $type;
}
?>