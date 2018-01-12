<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 6/19/2017 Replaced static WSDL code with call to getXMLFragments.
  * 6/13/2017 Added WSDL.
  * 8/26/2016 Added constant NAME_SPACE. Fixed a bug in getAuditedAsset.
  * 2/10/2016 Fixed a bug in getUser and changed the return value.
  * 5/28/2015 Added namespaces.
  * 8/1/2014 Added toStdClass.
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
<p>An <code>Audit</code> object represents an audit on an asset. This class is an independent class that does not extend another class.</p>
<p>Reading audits using web services can be particularly useful. This is because for many asset types like data definitions and content types (the components that do not appear in the asset tree panel), the \"Audits\" tab is missing from the Cascade back-end. Audit records of these asset types can only be retrieved by web services.</p>
<h2>Action Types</h2>
<p>When reading audits, we can pass in a type string to restrict the returned audits to a certain action type. Possible actions are:</p>
<ul>
<li><code>constants\T::LOGIN</code></li>
<li><code>constants\T::LOGINFAILED</code></li>
<li><code>constants\T::LOGOUT</code></li>
<li><code>constants\T::STARTWORKFLOW</code></li>
<li><code>constants\T::ADVANCEWORKFLOW</code></li>
<li><code>constants\T::EDIT</code></li>
<li><code>constants\T::COPY</code></li>
<li><code>constants\T::CREATE</code></li>
<li><code>constants\T::REFERENCE</code></li>
<li><code>constants\T::DELETE</code></li>
<li><code>constants\T::DELETEUNPUBLISH</code></li>
<li><code>constants\T::CHECKIN</code></li>
<li><code>constants\T::CHECKOUT</code></li>
<li><code>constants\T::ACTIVATEVERSION</code></li>
<li><code>constants\T::PUBLISH</code></li>
<li><code>constants\T::UNPUBLISH</code></li>
<li><code>constants\T::RECYCLE</code></li>
<li><code>constants\T::RESTORE</code></li>
<li><code>constants\T::MOVE</code></li>
</ul>
<p>Both the <a href=\"http://www.upstate.edu/web-services/api/asset-classes/asset.php\"><code>Asset</code></a> class and <a href=\"http://www.upstate.edu/web-services/api/cascade.php\"><code>Cascade</code></a> class have a method <code>getAudits</code> defined to retrieve the audits associated with an asset. The returned array stores <code>Audit</code> objects.</p>
<h2>Structure of <code>audit</code></h2>
<pre>audit
  user
  action
  identifier
    id
    path
    type
    recycled
  date
</pre>
<h2>WSDL</h2>";
$doc_string .=
    $service->getXMLFragments( array(
        array( "getComplexTypeXMLByName" => "audits" ),
        array( "getComplexTypeXMLByName" => "audit" ),
        array( "getSimpleTypeXMLByName"  => "auditTypes" ),
        array( "getComplexTypeXMLByName" => "identifier" ),
        array( "getComplexTypeXMLByName" => "path" ),
    ) );
return $doc_string;
?>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/audit.php">audit.php</a></li></ul>
<h2>JSON Dump</h2>
<pre>
{
  "user":"chanw",
  "action":"edit",
  "identifier":{
    "id":"9a1416488b7f08ee5d439b31921d08b6",
    "type":"page",
    "recycled":false
  },
  "date":"Oct 3, 2017 10:51:58 AM"
}
</pre>
</postscript>
</documentation>
*/
class Audit
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
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $audit_std )
    {
        if( $service == NULL )
        {
            throw new e\NullServiceException(
                S_SPAN . c\M::NULL_SERVICE . E_SPAN );
        }
        
        if( $audit_std == NULL )
        {
            throw new e\EmptyValueException(
                S_SPAN . c\M::EMPTY_AUDIT . E_SPAN );
        }
        
        if( self::DEBUG ) { u\DebugUtility::dump( $audit_std->identifier ); }
        
        $this->service    = $service;
        $this->audit_std  = $audit_std;
        $this->user       = $audit_std->user;
        $this->action     = $audit_std->action;
        $this->identifier = new p\Identifier( $audit_std->identifier );
        $this->date_time  = new \DateTime( $audit_std->date );
    }
    
/**
<documentation><description><p>Displays the audit and returns the object.</p></description>
<example>$audit->display();</example>
<return-type>Audit</return-type>
<exception></exception>
</documentation>
*/
    public function display()
    {
        echo c\L::USER       . $this->user . BR .
             c\L::ACTION     . $this->action . BR .
             c\L::ID         . $this->identifier->getId() . BR .
             c\L::ASSET_TYPE . $this->identifier->getType() . BR .
             c\L::DATE       . date_format( $this->date_time, 'Y-m-d H:i:s' ) . BR . HR;
        
        return $this;
    }
    
/**
<documentation><description><p>Returns <code>action</code>.</p></description>
<example>echo $audit->getAction(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getAction() : string
    {
        return $this->action;
    }
    
/**
<documentation><description><p>Returns the audited <code>Asset</code> object.</p></description>
<example>u\DebugUtility::dump( $audit->getAuditedAsset() );</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function getAuditedAsset() : Asset
    {
        return $this->identifier->getAsset( $this->service );
    }
    
/**
<documentation><description><p>Returns <code>date</code>.</p></description>
<example>echo "Date: ", date_format( $audit->getDate(), 'Y-m-d H:i:s' ), BR;</example>
<return-type>DateTime</return-type>
<exception></exception>
</documentation>
*/
    public function getDate() : \DateTime
    {
        return $this->date_time;
    }
    
/**
<documentation><description><p>Returns <code>identifier</code>.</p></description>
<example>u\DebugUtility::dump( $audit->getIdentifier() );</example>
<return-type>Child</return-type>
<exception></exception>
</documentation>
*/
    public function getIdentifier() : p\Child
    {
        return $this->identifier;
    }
    
/**
<documentation><description><p>Returns <code>user</code>.</p></description>
<example>echo $audit->getUser(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getUser() : string
    {
        return $this->user;
    }
    
/**
<documentation><description><p>An alias of <code>getUser</code>.</p></description>
<example>echo $audit->getUserString(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getUserString() : string
    {
        return $this->user;
    }
    
/**
<documentation><description><p>Returns the <code>stdClass</code> object passed into the constructor.</p></description>
<example>u\DebugUtility::dump( $audit->toStdClass() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        return $this->audit_std;
    }
    
/**
<documentation><description><p>A method used to sort <code>Audit</code> objects,
using the <code>date</code> object. The sort order is ascending.
The sorting is performed when an array of <code>Audit</code> objects is returned.</p></description>
<example>echo a\Audit::compare( $audit0, $audit1 ), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public static function compare( Audit $a1, Audit $a2 ) : int
    {
        if( $a1->getDate() == $a2->getDate() )
        {
            return 0;
        }
        else if( $a1->getDate() < $a2->getDate() )
        {
            return -1;
        }
        else
        {
            return 1;
        }
    }
    
/**
<documentation><description><p>An alias of <code>compare</code>.</p></description>
<example>echo a\Audit::compareAscending( $audit0, $audit1 ), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public static function compareAscending( Audit $a1, Audit $a2 ) : int
    {
        return self::compare( $a1, $a2 );
    }

/**
<documentation><description><p>A method used to sort <code>Audit</code> objects,
using the <code>date</code> object. The sort order is descending.
The sorting is performed when an array of <code>Audit</code> objects is returned.</p></description>
<example>echo a\Audit::compareDescending( $audit0, $audit1 ), BR;</example>
<return-type>int</return-type>
<exception></exception>
</documentation>
*/
    public static function compareDescending( Audit $a1, Audit $a2 ) : int
    {
        if( $a1->getDate() == $a2->getDate() )
        {
            return 0;
        }
        else if( $a1->getDate() < $a2->getDate() )
        {
            return 1;
        }
        else
        {
            return -1;
        }
    }

    private $service;
    private $user;
    private $action;
    private $identifier;
    private $date_time;
    private $audit_std;
}
?>