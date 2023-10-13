<?php

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'block_openai_chat/openai_key',
        get_string('openai_key', 'block_openai_chat'),
        get_string('openai_key_desc', 'block_openai_chat'),
        '',
        PARAM_RAW
    ));
}