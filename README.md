# php7-cascade-ws-ns
<p>Although I am still updating the library code for Cascade 8.11, I will not share the new code here. Sooner or later, someone has to pick up the maintenance task from me. That is to say, the latest code works for 8.9 and is now frozen. After 8.9, I will let the library code break and fall apart, until someone else picks it up again.</p>

<p>The Upstate Cascade web service library, using namespaces, written in PHP 7 for Cascade 8.9, by German Drulyk and Wing Ming Chan.</p>
<p>We have tried to add new features introduced in Cascade 8.9.1. But due to bugs in this version, we will wait until they are fixed.</p>

<p>Last modified: 11/1/2018, 08:30 AM</p>

<p>Note that new code related to namingRuleAssets does not work for SOAP due to a bug.</p>

<p>This version of the library makes use of features in PHP 7.</p>

<h2>Purpose of the Upgrade</h2>
<p>The library has been upgraded to PHP 7 for two main reasons:</p>
<ul>
<li>When using parameter types and return types, we can have tighter control over client code</li>
<li>We can use the type information, together with the ReflectionUtility class, to generate documentation pages; for an example of a generated page, see http://www.upstate.edu/web-services/api/asset-classes/asset.php</li>
</ul>
<p>When a class is upgraded with required information, the following type of code is made possible:</p>
<pre>
echo u\ReflectionUtility::getClassDocumentation( "cascade_ws_asset\Asset", true );
u\ReflectionUtility::showMethodSignatures( "cascade_ws_asset\Asset" );
u\ReflectionUtility::showMethodSignature( "cascade_ws_asset\Asset", "edit" );
u\ReflectionUtility::showMethodDescription( "cascade_ws_asset\Asset", "edit" );
u\ReflectionUtility::showMethodExample( "cascade_ws_asset\Asset", "edit" );
</pre>
<p>The ReflectionUtility class can also be used to reveal information of any class and any function:</p>
<pre>
echo u\ReflectionUtility::getMethodSignatures( "SimpleXMLElement", true ), BR;

u\ReflectionUtility::showFunctionSignature( "str_replace", true );
</pre>

<h2>Resources</h2>
<ol>
<li>For more information, visit http://www.upstate.edu/web-services/index.php</li>
<li>Online tutorials: http://www.upstate.edu/web-services/courses/ws-online-tutorials.php</li>
<li>Online tutorial recordings:
<ul><li>The first series: https://www.youtube.com/playlist?list=PLiPcpR6GRx5cGyfQESK6ZAj4My8rJidt4</li>
<li>The second series: https://www.youtube.com/watch?v=oYndzPxZ7yg&index=1&list=PLXb2nCJdzD9B26kI9vEds-0bQoPSWj5--</li></ul></li>
<li>Examples and recipes: https://github.com/wingmingchan/php-cascade-ws-ns-examples</li>
<li>Asset classes API: http://www.upstate.edu/web-services/api/asset-classes/index.php</li>
<li>Cascade Web Services courses: http://www.upstate.edu/web-services/courses/index.php</li>
<li>Cascade Web Services Library Support: https://groups.google.com/forum/#!forum/cascade-web-services-library-support</li>
<li>Erik España: https://github.com/espanae/cascade-server-web-services</li>
<li>Mark Nokes: https://github.com/marknokes</li>
<li>Christopher John Walsh: https://gist.github.com/quantegy</li>
