<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2015 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/14/2017 Removed most constants, modified display, added getMap, getNames, and getNameList.
  * 6/26/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 1/17/2017 Added JSON dump.
  * 7/6/2015 File created.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;

/**
<documentation>
<description>
<?php global $service, $cascade;
$doc_string = "<h2>Introduction</h2>
<p>A <code>Preference</code> object encapsulates system preferences. This class can be used to work with system preferences, including both reading and editing them. This class is an independent class that does not extend another class.</p>
<p>As of Cascade 8.6, there are 57 entries in sytem preferences. Each entry is a key-value pair. To access an entry, we need to supply the key. To edit an entry, we need to supply a key-value pair.</p>
<p>Note that neither the keys nor the possible values are defined in the WSDL. Therefore, when editing an entry, any value can be attached to an existing key, and Cascade will accept the value. How a meaningless value is treated in the Cascade back-end depends on default values assigned to these keys. When a meaningless value is assigned, Cascade falls back to the default.</p>
<p>When this class is implemented, I only check the keys. That is to say, when reading or editing an entry, the input key must exist. But I do not check the values for editing. Garbage in, garbage out.</p><p>In Cascade 8.9, the value for <code>system_pref_system_url</code> is validated.";
$doc_string .= "<h2>Preference Entries (8.4.1)</h2>";
$doc_string .= $cascade->getPreference()->getNameList();
$doc_string .= "<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "preferencesList" ),
        array( "getComplexTypeXMLByName" => "preference" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/preference.php">preference.php</a></li></ul>

</postscript>
</documentation>
*/
class Preference
{
    const DEBUG = false;
    const DUMP  = false;

/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $preference_std )
    {
        if( $service == NULL )
        {
            throw new e\NullServiceException(
                S_SPAN . c\M::NULL_SERVICE . E_SPAN );
        }
        
        if( $preference_std == NULL )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::NULL_PREFERENCE . E_SPAN );
        }
        
        //if( self::DEBUG && self::dump ) { u\DebugUtility::dump( $preference_std ); }
        
        $this->service        = $service;
        $this->preference_std = $preference_std;
        $this->map            = array();
        $this->names          = array();
        
        $this->processPreferences( $this->preference_std );
        //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $this->names ); }
    }
    
/**
<documentation><description><p>Displays and returns the calling object.</p></description>
<example>$cascade->getPreference()->display();</example>
<return-type>Preference</return-type>
<exception></exception>
</documentation>
*/
    public function display() : Preference
    {
        $table_string = "<table  class='preferences' summary='Preferences'>" .
            "<caption>System Preferences</caption>" .
            "<tr class='preferences-header'><th>Name</th><th>Value</th></tr>";
            
        foreach( $this->names as $name )
        {
            $table_string .= "<tr><td>$name</td><td>" . 
                $this->map[ $name ] . "</th></tr>";
        }
        
        $table_string .= "</table>" . HR ;
        echo $table_string;

        return $this;
    }
    
/**
<documentation><description><p>Dumps the <code>stdClass</code> object and returns the calling object.</p></description>
<example>$cascade->getPreference()->dump();</example>
<return-type>Preference</return-type>
<exception></exception>
</documentation>
*/
    public function dump( bool $formatted=true ) : Preference
    {
        if( $formatted ) echo S_H2 . c\L::READ_DUMP . E_H2 . S_PRE;
        var_dump( $this->preference_std );
        if( $formatted ) echo E_PRE . HR;
        
        return $this;
    }

/**
<documentation><description><p>Returns the name-value map.</p></description>
<example>u\DebugUtility::dump( $cascade->getPreference()->getMap() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getMap()
    {
        return $this->map;
    }
  
/**
<documentation><description><p>Returns an unordered list of preference names.</p></description>
<example>echo $cascade->getPreference()->getNameList();</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getNameList() : string
    {
        $name_list = "<ul>";
        
        foreach( $this->names as $name )
        {
            $name_list .= "<li>$name</li>";
        }
        
        $name_list .= "</ul>";
        return $name_list;
    }
  
/**
<documentation><description><p>Returns an array of all names.</p></description>
<example>u\DebugUtility::dump( $cascade->getPreference()->getNames() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getNames()
    {
        return $this->names;
    }
  
/**
<documentation><description><p></p></description>
<example>echo $pref->getValue( a\Preference::ASSET_TREE_MODE );</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getValue( string $name ) : string
    {
        if( !in_array( $name, $this->names ) )
            throw new e\NoSuchNameException( "The name $name does not exist. " );
            
        return $this->map[ $name ];
    }
  
/**
<documentation><description><p></p></description>
<example>u\DebugUtility::dump( $pref->toStdClass() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        return $this->preference_std;
    }
    
/**
<documentation><description><p></p></description>
<example>$pref->setValue( a\Preference::ALLOW_TABLE_EDITING, "on" );</example>
<return-type>Preference</return-type>
<exception></exception>
</documentation>
*/
    public function setValue( string $name, string $value ) : Preference
    {
        if( !in_array( $name, $this->names ) )
            throw new e\NoSuchNameException( "The name $name does not exist. " );

        $this->service->editPreferences( $name, $value );
        $this->reloadPreferences();
        return $this;
    }
    
    private function reloadPreferences()
    {
        $this->service->readPreferences();
        $this->preference_std = $this->service->getPreferences();
        $this->processPreferences( $this->preference_std );
    }
    
    private function processPreferences( \stdClass $preference_std )
    {
        if( isset( $preference_std->preference ) && is_array( $preference_std->preference ) )
        {
            $pref_array = $preference_std->preference;
            
            //if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $pref_array ); }
            
            foreach( $pref_array as $pref )
            {
                $this->map[ $pref->name ] = $pref->value;
            }
            
            $this->names = array_keys( $this->map );
        }
    }
    
    private $service;
    private $preference_std;
    private $map;
    private $names;
}
?>