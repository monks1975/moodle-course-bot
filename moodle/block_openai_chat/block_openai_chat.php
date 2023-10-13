<?php

//require_once $CFG->dirroot . '/lib/externallib.php';
require_once $CFG->libdir . '/externallib.php';

// Define a class for the OpenAI Chat Block, extending Moodle's base block class
class block_openai_chat extends block_base
{

    // Initialize the block with a title
    public function init()
    {
        $this->title = 'Jons OpenAi Block';
    }

    // Specifies that this block has a settings page
    public function has_config()
    {
        return true;
    }

    // Save the block's settings data
    public function config_save($data)
    {
        // The set_config function is used to save configuration settings
        // It saves data into the config_plugins table in Moodle's database
        set_config('openai_key', $data->openai_key, 'block_openai_chat');
    }

    // Load the block's settings data into the settings form
    public function config_load(&$form)
    {
        // The get_config function retrieves configuration settings from the database
        $config = get_config('block_openai_chat');

        // If settings exist, populate them in the form
        if ($config) {
            $form->set_data(['openai_key' => $config->openai_key]);
        }
    }

    // Function to generate the content inside the block
    public function get_content()
    {
        global $PAGE, $COURSE, $DB, $CFG;

        // Early exit if content is already generated
        if ($this->content !== null) {
            return $this->content;
        }

        $service = $DB->get_record('external_services', ['shortname' => 'openai_crawler']);
        if (!$service) {
            debugging("Service with shortname 'moodle_mobile_app' not found.", DEBUG_DEVELOPER);
            return null;
        }

        // Use a dedicated method to fetch or generate a token.
        $tokenString = $this->fetch_or_generate_token($service->id, $userId = 3);

        $this->enqueue_js();
        $this->initialize_content();

        $data = $this->prepare_data($COURSE->id, $tokenString);
        $this->inject_svelte_app_and_data($data);

        return $this->content;
    }

// Fetches or generates a new external token
    private function fetch_or_generate_token($serviceId, $userId)
    {
        global $DB;

        $existingToken = $DB->get_record('external_tokens', ['userid' => $userId, 'externalserviceid' => $serviceId]);
        return $existingToken->token;
    }

// Enqueues the necessary JavaScript for the block
    private function enqueue_js()
    {
        global $PAGE;

        $PAGE->requires->js(new moodle_url('/local/js/block_openai_chat.js'));
    }

// Initializes the content object
    private function initialize_content()
    {
        $this->content = new stdClass;
        $this->content->text = '';
    }

// Prepares the data to be passed into the JavaScript
    private function prepare_data($courseId, $tokenString)
    {
        return [
            'courseId' => $courseId,
            'token' => $tokenString,
        ];
    }

// Injects a div to mount the Svelte app and injects the JSON-encoded data
    private function inject_svelte_app_and_data($data)
    {
        $this->content->text .= '<div id="svelte-root"></div>';
        $this->content->text .= '<script type="application/json" id="block-data">'
        . json_encode($data) . '</script>';
        $this->content->footer = '';
    }
}
