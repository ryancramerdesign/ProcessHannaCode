<?php namespace ProcessWire;
if(!defined("PROCESSWIRE")) die();
?>
<div class='ui-helper-clearfix'>
	<div class='HannaCodeUsageNote'>
		<h2>PHP usage notes</h2>	
		<ol>
			<li>
				<p>
				Your code should <code>echo</code> or <code>print</code> the value you want to appear as the replacement for the tag.
				</p>
			</li>
			<li>
				<p>
				It is not necessary to begin or close your statement with open/close PHP tags. Though you may use them when/if necessary. 
				An opening <code>&lt;?php namespace ProcessWire;</code> tag may be desirable to enable syntax highlighting.
				If do not specify an opening PHP tag then your code will execute from the ProcessWire namespace. 	
				</p>
			</li>
			<li>
				<p>
				Your code is executed the same way as a ProcessWire template file and all API variables are locally scoped. 
				Meaning, you can call upon <code>$page</code>, <code>$pages</code>, or any other API variables. directly.
				See also the PHP Properties section to the right.
				</p>
			</li>
			<li>
				<p>
				If attributes are specified in the tag (or in the Attributes field on this page), they will appear as locally scoped variables to your PHP code.
				For instance, in the tag <code>[[hello_world first_name=Karena]]</code>, your code will have a <code>$first_name</code> variable populated with 'Karena'.
				</p>
			</li>
			<li>
				<p>
				If using attributes, it is optional though recommended that you define defaults in the Attributes field on this screen (even if blank is the default value).
				</p>
			</li>
			<li>
				<p>
				All attributes are also populated to an <code>$attr</code> array of [key=value]. For example: <code>$attr['first_name'] == 'Karena'</code>, in case you find this syntax preferable, or necessary.
				</p>
			</li>
			<li>
				<p>
				If you use an attribute name that is the same as an API variable name (example: <code>$page</code>) then the API variable overrides the attribute name. In that case, the attribute value will only be accessible 
				through <code>$attr</code> (example: <code>$attr['page']</code>).
				</p>
			</li> 
			<li>
				<p>
				Your code receives an object named <code>$hanna</code>. This can be used for getting additional properties, or modifying the larger text value if necessary. See details in the PHP Properties section.
				</p>
			</li>
			<li>
				<p>
				The <code>$page</code> API variable available to your Hanna code represents the page where the Hanna code exists. 
				It is possible for this to be different from <code>wire('page')</code>, which represents the page that originated the request.
				</p>
			</li>
			<li>
				<p>
				These code snippets are written to <code>/site/assets/cache/HannaCode/[tag-name].php</code> and directly executed rather than eval'd.
				</p>
			</li>
		</ol>
	</div>

	<div class='HannaCodeUsageNote'>
		<h2>PHP properties</h2>
		<table class='AdminDataTable'>
			<tr><td><code>$attr</code></td><td>An array of [key=value] attributes passed to your Hanna code.</td></tr>
			<tr><td><code>$page</code></td><td>The page where the Hanna code exists. </td></tr>
			<tr><td><code>$hanna->name</code></td><td>The name (string) of the current Hanna code.</td></tr>
			<tr><td><code>$hanna->field</code></td><td>The Field object representing the text.</td></tr>
			<tr><td><code>$hanna->value</code></td><td>The larger text where the Hanna code lives. This property may also be set.</td></tr>
		</table>
		<p><strong>Please note that “name” is a reserved word and may not be used as an attribute name.</strong></p>
		
		<h2>Javascript usage notes</h2>	
		<ol>
		<li><p>It is not necessary to include <code>&lt;script&gt;</code> tags in your code unless you want to. They will be automatically inserted when not already present.</p></li>
		<li>
			<p>
			If attributes are specified in the tag (or in the attributes section of this page), they will appear as locally scoped variables to your Javascript code.
			For instance, in the tag <code>[[hello_world first_name=Karena]]</code>, your code will have a first_name variable populated with 'Karena'.
			</p>
		</li>
		<li><p>All attributes are also populated to an <code>attr</code> object of <code>attr.key=value</code> (i.e. <code>attr.first_name == 'Karena'</code>), in case you find this syntax preferable.</p></li>
		<li><p>If using attributes, it is recommended that you define defaults in the Attributes field on this screen (even if blank is the default value).</p></li>
		<li><p>Note that <code>name</code> is a reserved word and may not be used as an attribute name.</p></li>
		</ol>
	</div>
</div>
