<?php
return [
	'url_string' => 'the-model-class',
	'headline' => 'The Model Class',
	'description' => 'The Model class serves as the base class for all model files in Trongate v2, providing automatic database access, module loading capabilities, and method delegation to module-specific model files.',
	'info' => '<p class="container-xs">This core engine class provides the foundation for data operations in Trongate v2. The <code>Model</code> class automatically handles database connections (both default and alternative database groups), enables explicit module loading within model files, and delegates method calls to module-specific model files. All module model files (e.g., <code>Users_model.php</code>) extend this base class to inherit its functionality.</p>  
	<p class="container-xs">Most of the code within the <code>Model</code> class is for internal framework bootstrapping. From within model files, the only Model class method you\'ll typically call directly is <code>module()</code>.</p>
	<p class="container-xs"><strong>Related Documentation:</strong></p>
	<ul class="container-xs" style="margin: 0 auto; width: max-content;">
		<li><a href="documentation/trongate_php_framework/module-fundamentals/model-files">Understanding Model Files</a></li>
		<li><a href="documentation/trongate_php_framework/database-operations">Database Operations</a></li>
	</ul>'
];