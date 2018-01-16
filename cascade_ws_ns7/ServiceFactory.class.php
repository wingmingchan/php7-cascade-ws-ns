<?php
/**
  Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>, 
                       German Drulyk <drulykg@upstate.edu>
  MIT Licensed
  Modification history:
  1/16/2018 Class created.
 */
namespace cascade_ws_AOHS;

use cascade_ws_constants as c;
use cascade_ws_utility   as u;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_exception as e;

/**
<documentation>
<description>
<h2>Introduction</h2>
<p></p>
</description>
<postscript></postscript>
<advanced>
</advanced>
</documentation>
*/
class ServiceFactory
{
    public static function getService(
        string $type, string $url, string $username, string $password,
        $context=NULL ) :
        AssetOperationHandlerService
    {
        $type = strtolower( $type );
        
        if( $type === 'soap' )
        {
            return new AssetOperationHandlerServiceSoap(
                $type, $url, 
                ( object ) [ 'username' => $username, 'password' => $password ],
                $context );
        }
        elseif( $type === 'rest' )
        {
            return new AssetOperationHandlerServiceRest(
                $type, $url, ( object ) [ 'u' => $username, 'p' => $password ],
                $context );
        }
    }
}
?>