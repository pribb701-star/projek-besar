<?php
if (!defined('APP_BASE_URL')) {
    $document_root = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $project_root = realpath(__DIR__);
    $relative_path = '';

    if ($document_root !== false && $project_root !== false
        && strncasecmp($project_root, $document_root, strlen($document_root)) === 0) {
        $relative_path = substr($project_root, strlen($document_root));
    }

    $relative_path = str_replace(DIRECTORY_SEPARATOR, '/', $relative_path);
    define('APP_BASE_URL', '/' . trim($relative_path, '/') . '/');
}
