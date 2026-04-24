<?php
return [
    'url_string' => 'flashdata-module',
    'headline' => 'The Flashdata Module',
    'description' => 'The Flashdata Module provides one-time session messages, commonly used for success and error notifications after form submissions and redirects.',
    'info' => '<p class="container-xs">The Flashdata Module implements a "set it and forget it" pattern for temporary session messages. Messages are stored in the session with <code>set_flashdata()</code>, survive a redirect, and are automatically cleared after being retrieved with <code>flashdata()</code>. This ensures each notification is displayed exactly once to the user.</p><p class="container-xs">The module supports custom HTML wrapping via <code>\'opening_html\'</code> and <code>\'closing_html\'</code> parameters, global defaults via the <code>FLASHDATA_OPEN</code> and <code>FLASHDATA_CLOSE</code> config constants, and a built-in green <code>&lt;p&gt;</code> tag fallback.</p>'
];