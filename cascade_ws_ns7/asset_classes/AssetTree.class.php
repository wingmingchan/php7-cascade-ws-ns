<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 1/31/2017 Added more inline comments.
  * 9/26/2016 Fixed a bug in toListString.
  * 8/26/2016 Added constant NAME_SPACE.
  * 3/8/2016 Fixed a bug related to namespace.
  * 5/28/2015 Added namespaces.
  * 8/18/2014 Added NULL function in traverse for Report.
  * 7/14/2014 Changed applyFunctionsToChild to accept class static methods.
  * 6/23/2014 Added an optional parameter passed into toXml to generate lastmod.
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
<p>An asset tree is a tree data structure created to hold information of a container (the root of the tree) and everything within (information about all children in the root container). The container in question can be the Base Folder of a site, any folder within the Base Folder, or any types of containers or sub-containers. An <code>AssetTree</code> object can be created to represent an asset tree. There are two ways to create an <code>AssetTree</code> object:</p>
<ul>
<li>By calling the constructor of the class by passing in a <a href="http://www.upstate.edu/web-services/api/asset-classes/container.php"><code>Container</code></a> object:
<pre class="code">$tree = new AssetTree( $folder );
</pre>
</li>
<li>By calling the <code>Container::getAssetTree</code> method:
<pre class="code">$tree = $folder-&gt;getAssetTree();
</pre>
</li>
</ul>
<p>In fact, there is yet another way to get asset trees of a site (a site can have many different asset trees). An <code>AssetTree</code> can be obtained through a <a href="http://www.upstate.edu/web-services/api/asset-classes/site.php"><code>Site</code></a> object by calling <code>Site::getBaseFolderAssetTree</code> or similar methods:</p>
<pre class="code">$tree = $site-&gt;getBaseFolderAssetTree();
</pre>
<p>Although <code>AssetTree</code> is a very simple class, with the help of asset classes and global functions, it can be used to do a lot of different things. The most powerful method defined in this class is <code>AssetTree::traverse</code>. It can be used to manipulate any asset in a container in any way.</p>
<h2>Structure of an Asset tree</h2>
<p>Internally, an asset tree stores two entities: a root, and an array of children. The root is the <code>Container</code> object passed into the constructor. The children array can store two types of objects: <a href="http://www.upstate.edu/web-services/api/property-classes/child.php"><code>Child</code></a> objects and <code>AssetTree</code> objects. A <code>Child</code> object can represent any non-container asset, like pages, blocks, data definitions, metadata sets and so on. An <code>AssetTree</code> object represent a sub-container. Since an <code>AssetTree</code> can have other <code>AssetTree</code> objects, recursion is built into the class, both in the constructor and all other methods.</p>
<h2>What Can the <code>AssetTree</code> Class Do?</h2>
<p>The <code>AssetTree</code> class is good at two things:</p>
<ul>
<li>Generating reports: Everything inside the root container represented by an <code>AssetTree</code> object is visited when the <code>AssetTree::traverse</code> method is called. When a child is visited, the information it holds can be examined, and if necessary, stored. Therefore, we can use this mechanism to gather information of all descendants in the root container.</li>
<li>Modifying assets: When a child is visited, we can actually modify it. Parameters can be passed in and used to modify any descendants in the root container.</li>
</ul></description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/asset_tree.php">asset_tree.php</a></li></ul></postscript>
</documentation>
*/
class AssetTree
{
    const DEBUG      = false;
    const NAME_SPACE = "cascade_ws_asset";

/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( Container $container )
    {
        if( $container == NULL )
        {
            throw new e\NullAssetException( c\M::NULL_CONTAINER );
        }
        $this->root         = $container;
        $root_children      = $container->getChildren(); // Child objects
        $this->has_children = count( $root_children ) > 0;
        
        if( $this->has_children )
        {
            $this->children = array();
            
            foreach( $root_children as $root_child )
            {
                if( $root_child->getType() == $container->getType() )
                {
                    $class_name = c\T::$type_class_name_map[ $container->getType() ];
                    $class_name = Asset::NAME_SPACE . "\\" . $class_name;
                
                    $this->children[] = new AssetTree( 
                        $class_name::getAsset( $this->root->getService(),
                            $container->getType(),
                            $root_child->getId() )
                    );
                }
                else
                {
                    $this->children[] = $root_child;
                }
            }
        }
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the root container contains any children.</p></description>
<example>echo "Has children: ", u\StringUtility::boolToString( $at->hasChildren() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasChildren() : bool
    {
        return $this->has_children;
    }
    
/**
<documentation><description><p>Returns a string containing properly embedded <code>&lt;ul&gt;</code> and <code>&lt;li&gt;</code> elements, showing the structure of the container and its contents.</p></description>
<example>echo $at->toListString();</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function toListString( bool $root=true ) : string
    {
        // if this is the start point, add <ul>
        if( $root )
            $list_string = S_UL . S_LI;
        else
            $list_string = S_LI;
        
        $list_string .= $this->root->getType() . " " .
            $this->root->getPath() . " " .
            $this->root->getId();
            
        // the root has children
        if( $this->has_children )
        {
            $list_string .= S_UL;
            
            foreach( $this->children as $child )
            {
                // non-AssetTree objects
                if( get_class( $child ) == 'cascade_ws_property\Child' )
                {
                    $list_string .= $child->toLiString();
                }
                // AssetTree objects within container
                else
                {
                    $list_string .= $child->toListString( false );
                }
            }
            
            $list_string .= E_UL;
        }
        
        $list_string .= E_LI;
        
        if( $root )
            $list_string .= E_UL;
            
        return $list_string;
    }
    
/**
<documentation><description><p>Returns an XML representation of the entire tree.</p></description>
<example>echo S_PRE, u\XmlUtility::replaceBrackets( $at->toXml() ), E_PRE;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function toXml( string $indent="" ) : string
    {
        $xml_string = $indent . "<" . $this->root->getType() . " path=\"" .
            $this->root->getPath() . "\" id=\"" .
            $this->root->getId() . "\"";
            
        $child_indent = $indent . "  ";
            
        if( $this->has_children )
        {
            $xml_string .= ">\n";
            
            foreach( $this->children as $child )
            {
                $xml_string .= $child->toXml( $child_indent, $this->root->getService() );
            }
            $xml_string .= $indent . "</" . $this->root->getType() . ">\n";
        }
        else
        {
            $xml_string .= "/>\n";
        }
        
        return $xml_string;
    }
    
/**
<documentation><description><p>Traverses an asset tree and returns the calling object.
See <a href="http://www.upstate.edu/web-services/api/asset-tree/traversing-asset-tree.php">Traversing an Asset Tree</a>.</p></description>
<example>$results = array();
    
$at->traverse(
    array( a\Page::TYPE => array( "assetTreeCount" ) ),
    NULL,
    $results
);

u\DebugUtility::dump( $results );
</example>
<return-type>AssetTree</return-type>
<exception></exception>
</documentation>
*/
    public function traverse(
        array $function_array, 
        array $params=NULL, 
        array &$results=NULL ) : AssetTree
    {
        $service = $this->root->getService();
        
        // skip root container
        if( isset( $params ) && isset( $params[ c\F::SKIP_ROOT_CONTAINER ] ) && 
            $params[ c\F::SKIP_ROOT_CONTAINER ] == true )
        {
            // reset flag for child containers in recursion
            $params[ c\F::SKIP_ROOT_CONTAINER ] = false;
        }
        // process root container as well
        else
        {
            $this->applyFunctionsToChild( 
                $service, $this->root->toChild(), $function_array, $params, $results );
        }
        
        // process children; these are Child objects
        if( $this->has_children )
        {
            foreach( $this->children as $child )
            {
                // child is an asset tree
                if( get_class( $child ) != 'cascade_ws_property\Child' )
                {
                    // recursive traversal
                    $child->traverse( $function_array, $params, $results );
                }
                else
                {
                    $this->applyFunctionsToChild( 
                        $service, $child, $function_array, $params, $results );
                }
            }
        }
        return $this;
    }
    
    private function applyFunctionsToChild( 
        aohs\AssetOperationHandlerService $service, 
        p\Child $child, // the child object is passed in from traverse
        array $function_array, 
        array  $params=NULL, 
        array &$results=NULL )
    {
        $type = $child->getType();
        
        // match the type first
        if( isset( $function_array[ $type ] ) )
        {
            $functions  = $function_array[ $type ];
            $func_count = count( $functions );
            
            // check methods and functions first
            // quit if there is anything wrong without applying any of them
            for( $i = 0; $i < $func_count; $i++ )
            {
                if( $functions[ $i ] == NULL )
                {
                    continue;
                }

                // class static method
                if( strpos( $functions[ $i ], "::" ) !== false )
                {
                    // compute the class name and method name
                    $method_array = u\StringUtility::getExplodedStringArray(
                        ":", $functions[ $i ] );
                    $class_name   = $method_array[ 0 ];
                    $class_name   = Asset::NAME_SPACE . "\\" . $class_name;
                    $method_name  = $method_array[ 1 ];
                    
                    if( !method_exists( $class_name, $method_name ) )
                    {
                        throw new e\NoSuchFunctionException( 
                            "The function " . $functions[ $i ] . " does not exist." );
                    }
                }
                // global function
                else if( !function_exists( $functions[ $i ] ) )
                {
                    throw new e\NoSuchFunctionException( 
                        "The function " . $functions[ $i ] . " does not exist." );
                }
            }
            
            // apply function with parameters and results array
            // only if everything looks OK
            for( $i = 0; $i < $func_count; $i++ )
            {
                if( $functions[ $i ] == NULL )
                {
                    continue;
                }

                // apply each class method and function in sequence
                if( strpos( $functions[ $i ], "::" ) !== false )
                {
                    $method_array = u\StringUtility::getExplodedStringArray(
                        ":", $functions[ $i ] );
                    $class_name   = $method_array[ 0 ];
                    $class_name   = Asset::NAME_SPACE . "\\" . $class_name;
                    $method_name  = $method_array[ 1 ];
                    $class_name::$method_name( $service, $child, $params, $results );
                }
                else
                {
                    $func_name = $functions[ $i ];
                    $func_name( $service, $child, $params, $results );
                }
            }
        }
    }
    
    private $root;
    private $has_children;
    private $children;
}
?>