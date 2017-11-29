<?php
require_once( 'auth_rest_webapp.php' );

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;

try
{
    $asset = new \stdClass();
    $asset->textBlock                   = new \stdClass();
    $asset->textBlock->text             = "My new text block content";
    $asset->textBlock->metadataSetId    = "618861da8b7ffe8377b637e8ad3dd499";
    $asset->textBlock->metadataSetPath  = "_brisk:Block";
    $asset->textBlock->name             = "new-text-block";
    $asset->textBlock->parentFolderPath = "_cascade/blocks/code";
    $asset->textBlock->siteName         = "formats";
    
    $reply = $service->create( $asset );
    u\DebugUtility::dump( $reply );
}
catch( \Exception $e ) 
{
    echo S_PRE . $e . E_PRE;
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE;
}
?>