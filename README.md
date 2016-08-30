# php7-cascade-ws-ns
My Cascade web service library, using namespaces, written in PHP 7. Last modified: Aug 30, 2016, 12:54 PM
<p>This version of the library makes use of features in PHP 7. Since I have just started making changes,
most files are identical to those in the older version. Everything is posted here so that the library 
constantly remains complete with all new features I am adding, and users can use this version to
take advantage of the new features. I expect that this upgrade process will last for a few months.</p>

<h2>Purpose of the Upgrade</h2>
<p>The library is upgraded to PHP 7 due to two main reasons:</p>
<ul>
<li>When using parameter types and return types, I can have tighter control over client code</li>
<li>I can use the type information, together with the ReflectionUtility class, to generate documentation pages; for an example of a generated page, see http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/asset.php</li>
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

<h2>Progress</h2>
<ul>
<li>Updated Asset, Audit and associated test code</li>
<li>All utility classes have been updated</li>

</ul>


<hr />
<ol>
<li>For more information, visit http://www.upstate.edu/cascade-admin/web-services/index.php</li>
<li>Online tutorials: http://www.upstate.edu/cascade-admin/web-services/courses/ws-online-tutorials.php</li>
<li>Online tutorial recordings: https://www.youtube.com/playlist?list=PLiPcpR6GRx5cGyfQESK6ZAj4My8rJidt4</li>
<li>Examples and recipes: https://github.com/wingmingchan/php-cascade-ws-ns-examples</li>
<li>Asset classes API: http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/index.php</li>
<li>Cascade Web Services courses: http://www.upstate.edu/cascade-admin/web-services/courses/index.php</li>
<li>Cascade Web Services Library Support: https://groups.google.com/forum/#!forum/cascade-web-services-library-support</li>
<li>Erik España: https://github.com/espanae/cascade-server-web-services</li>
<li>Mark Nokes: https://github.com/marknokes</li>
<li>Christopher John Walsh: https://gist.github.com/quantegy</li>
