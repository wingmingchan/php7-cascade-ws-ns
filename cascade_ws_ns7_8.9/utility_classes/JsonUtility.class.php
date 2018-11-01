<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 12/28/2016 File created.
 */
namespace cascade_ws_utility; 

/**
<documentation><description><h2>Introduction</h2>
<p>This class provides JSON utility services.</p>
</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class JsonUtility
{
    const NAME_SPACE = "cascade_ws_utility";
    
    public static $names_to_fix = array(
        "children"            => "child",
        "contentTypePageConfigurations" => "contentTypePageConfiguration",
        "dynamicFields"        => "dynamicField",
        "fieldValues"          => "fieldValue",
        "inlineEditableFields" => "inlineEditableField",
        "pageConfigurations"   => "pageConfiguration",
        "pageRegions"          => "pageRegion",
        "parameters"           => "parameter",
        "plugins"              => "plugin",
        //"structuredDataNodes"  => "structuredDataNode"
    );

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function arrayToJson( array $array ) : string
    {
        return json_encode( $array );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function arrayToStdClass( array $array ) : \stdClass
    {
        return json_decode( json_encode( $array ) );
    }

/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function getAssetStdClass( \cascade_ws_asset\Asset $asset ) : \stdClass
    {
        $prop_name = 
            \cascade_ws_constants\T::$type_property_name_map[ $asset->getType() ];
        echo $prop_name, BR;
        $prop = $asset->getProperty();
        $prop = self::objectToArray( $prop );
        $prop = self::arrayToStdClass( $prop );
        $asset = new \stdClass();
        $asset->$prop_name = $prop;
        
        return $asset;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function jsonApiOperation( string $url, array $params )
    {
        return json_decode(
            file_get_contents(
                $url, 
                false, 
                stream_context_create(
                    array( 
                        'http' => array(
                            'method'  => 'POST',
                            'content' => json_encode( $params ) ) ) ) ) );
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function jsonCommit( \cascade_ws_asset\Asset $asset, string $url )
    {
        $asset_std = self::getAssetStdClass( $asset );
        
        DebugUtility::dump( $asset_std );
        
        $reply = self::jsonApiOperation( $url, array ( 'asset' => $asset_std ) );
        return $reply;
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function objectToArray( $obj )
    {
        if( is_object( $obj ) )
        {
            $obj = ( array )$obj;
        }
            
        if( is_array( $obj ) )
        {
            $array = array();
            
            foreach( $obj as $key => $val )
            {
                if( $key === "startDate" || $key === "endDate" ||
                    $key === "reviewDate"
                )
                {
                    continue;
                }

                if( $key === "lastModifiedDate" || $key === "createdDate" ||
                    $key === "lastPublishedDate"
                )
                {
                    DebugUtility::out( $key );
                    DebugUtility::dump( $val );
                    $date = new \DateTime( $val );
                    $val = $date->format( 'M j, Y g:i:s A' );
                }
                
                
                
                
                
                if( $key === "structuredDataNodes" )
                {
                    if( isset( $val->structuredDataNode ) )
                    {
                        if( is_array( $val->structuredDataNode ) )
                            $val = self::objectToArray( $val->structuredDataNode );
                        else
                            $val = self::objectToArray( array( $val->structuredDataNode ) );
                    }
                    
                    if( !is_null( $val ) )
                    {
                        if( is_array( $val ) )
                        {
                            $array[ $key ] = $val;
                        }
                        else
                        {
                            $array[ $key ] = array( $val );
                        }
                    }
                    //DebugUtility::dump( $val );
                }
                
            
                elseif( !is_null( $val ) )
                {
                    $keys = array_keys( self::$names_to_fix );
                    
                    if( in_array( $key, $keys ) )
                    {
                        $array[ $key ] = array();
                            
                        if( !is_numeric( $key ) )
                            $val_key = self::$names_to_fix[ $key ];
                        
                        if( isset( $val_key ) && isset( $val->$val_key ) )
                        {
                            $val_array = $val->$val_key;
                            
                            foreach( $val_array as $item_key => $item_val )
                            {
                                if( !is_null( $item_val ) )
                                {
                                    $temp = new \stdClass();
                                
                                    if( !is_numeric( $item_key ) )
                                    {
                                        //echo "item key ", $item_key, BR;
                                        $temp->$item_key = 
                                            self::objectToArray( $item_val );
                                    }
                                    else
                                    {
                                        //echo "Not item key", $item_key, BR;
                                        $temp = self::objectToArray( $item_val );
                                    }
                                
                                    //$temp->$item_key = self::objectToArray( $item_val );
                                    $array[ $key ][] = $temp;
                                }
                                else
                                {
                                    $array[ $key ][] = new \stdClass();
                                }
                            }
                        }
                        else
                        {
                            $array[ $key ] = self::objectToArray( $val );
                        }
                    }
                    else
                        $array[ $key ] = self::objectToArray( $val );
                }
            }
        }
        else $array = $obj;
        
        return $array;       
    }
    
/**
<documentation><description><p></p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public static function stdClassToArray( \stdClass $std_object )
    {
        return self::objectToArray( $std_object );
    }
}
?>