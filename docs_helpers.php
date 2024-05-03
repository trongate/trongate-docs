<?php
/**
 * Encode data as JSON and optionally display it with preformatted HTML.
 *
 * @param mixed $data The data to be encoded as JSON.
 * @param bool|null $kill_script (Optional) If true, terminate the script after displaying the JSON. Default is null.
 * @return void
 */
function json($data, ?bool $kill_script = null): void {
    echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre';

    if (isset($kill_script)) {
        die();
    }
}

/**
 * Fetches a segment from the URL relative to the BASE_URL.
 *
 * This function parses the current URL to extract a specific segment. It removes the BASE_URL to derive
 * the relative path, normalizes this path, and then splits it into segments. It returns the segment
 * corresponding to the provided segment number. If the segment does not exist, it returns an empty string.
 *
 * @param int $segment_number The 1-based index of the URL segment to retrieve.
 * @return string The value of the specified URL segment or an empty string if not found.
 */
function segment(int $segment_number): string {
    // Get the current URL


    // Remove the BASE_URL from the current URL to get the relative path
    $relative_path = str_replace(BASE_URL, '', current_url());

    // Normalize the relative path to remove any leading/trailing slashes
    $relative_path = trim($relative_path, '/');

    // Split the relative path into segments
    $segments = explode('/', $relative_path);

    // Adjust the segment number for 0-based index array
    $segment_index = $segment_number - 1;

    // Check if the requested segment exists
    if (isset($segments[$segment_index])) {
        return $segments[$segment_index];
    }

    // Return an empty string if the segment does not exist
    return '';
}

/**
 * Converts a string into a URL-friendly slug format.
 *
 * This function will transliterate the string if the 'intl' extension is loaded and transliteration is set to true.
 * It then converts any non-alphanumeric characters to dashes, trims them from the start and end, and converts everything to lowercase.
 *
 * @param string $value The string to be converted into a slug.
 * @param bool $transliteration Whether to transliterate characters to ASCII.
 * @return string The slugified version of the input string.
 */
function url_title(string $value, bool $transliteration = true): string {
    if (extension_loaded('intl') && $transliteration === true) {
        $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
        $value = $transliterator->transliterate($value);
    }
    $slug = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
    $slug = trim($slug, '- ');
    $slug = strtolower($slug);
    return $slug;
}

/**
 * Get the current URL of the web page.
 *
 * @return string The current URL as a string.
 */
function current_url(): string {
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];
    return $current_url;
}

/**
 * Generate an HTML anchor (link) element.
 *
 * @param string $target_url The URL to link to.
 * @param mixed $text The link text or boolean value to indicate no link.
 * @param array|null $attributes (Optional) An associative array of HTML attributes for the anchor element.
 * @param string|null $additional_code (Optional) Additional HTML code to append to the anchor element.
 * @return string The HTML anchor element as a string.
 */
function anchor(string $target_url, $text, ?array $attributes = null, ?string $additional_code = null): string {
    $str = substr($target_url, 0, 4);
    if ($str != 'http') {
        $target_url = BASE_URL . $target_url;
    }

    $text_type = gettype($text);

    if ($text_type === 'boolean') {
        return $target_url;
    }

    $extra = '';
    if (isset($attributes)) {
        foreach ($attributes as $key => $value) {
            $extra .= ' ' . $key . '="' . $value . '"';
        }
    }

    if (isset($additional_code)) {
        $extra .= ' ' . $additional_code;
    }

    $link = '<a href="' . $target_url . '"' . $extra . '>' . $text . '</a>';
    return $link;
}