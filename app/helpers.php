<?php

if (!function_exists('getMessageTemplate')) {
    function getMessageTemplate(string $key, array $data = []): string
    {
        $template = config("message_templates.{$key}", 'Default message');

        foreach ($data as $placeholder => $value) {
            $template = str_replace(":$placeholder", $value, $template);
        }

        return $template;
    }
}

