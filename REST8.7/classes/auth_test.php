<?php
$soap   = false;
$webapp = true;

$folderPath = "/Applications/MAMP/bin/php/php_include/cascade_ws_ns7/";
$fileName   = "AssetOperationHandlerService.class.php";

if( is_dir( $folderPath ) && $handle = opendir( $folderPath ) )
{
    if( file_exists( $folderPath . $fileName ) && is_file( $folderPath . $fileName ) )
    {
        if( $soap )
        {
            if( file_exists( $folderPath .
                "AssetOperationHandlerServiceSoap.class.php" ) && 
                is_file( $folderPath . "AssetOperationHandlerServiceSoap.class.php" ) )
            {
                rename( $folderPath . $fileName,
                    $folderPath . "AssetOperationHandlerServiceRest.class.php" );
                
                rename( $folderPath . "AssetOperationHandlerServiceSoap.class.php",
                $folderPath . $fileName );
            }
        }
        else
        {
            if( file_exists( $folderPath .
                "AssetOperationHandlerServiceRest.class.php" ) && 
                is_file( $folderPath . "AssetOperationHandlerServiceRest.class.php" ) )
            {
                rename( $folderPath . $fileName,
                    $folderPath . "AssetOperationHandlerServiceSoap.class.php" );

                rename( $folderPath . "AssetOperationHandlerServiceRest.class.php",
                $folderPath . $fileName );
            }
        }
    }
}
/*//*/

if( $soap && $webapp )
    require_once( "auth_tutorial7.php" );
elseif( $soap )
    require_once( "auth_chanw.php" );
elseif( !$soap )
    require_once( "auth_rest_webapp.php" );
else
    require_once( "auth_rest_web.php" );
?>
