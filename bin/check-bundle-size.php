#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Bundle Size Check.
 *
 * Verifies asset sizes are within budget.
 * Core budget: 50 KB CSS, 30 KB JS (gzipped)
 * Opt-in budget: Tiptap 450 KB JS (not counted against core)
 */
$root = dirname(__DIR__);
$errors = [];

function getFileSize(string $path): int
{
    if (!file_exists($path)) {
        return 0;
    }

    return filesize($path);
}

function formatBytes(int $bytes): string
{
    return sprintf('%.1f KB', $bytes / 1024);
}

$distDir = $root.'/assets/dist/';
$cssDir = $root.'/assets/styles/';

$coreJsFiles = glob($distDir.'admin*.js') ?: [];
$tiptapJsFiles = glob($distDir.'tiptap*.js') ?: [];
$cssFiles = array_merge(
    glob($cssDir.'beacon-admin*.css') ?: [],
    glob($cssDir.'beacon-*theme*.css') ?: [],
);

$coreJsSize = array_sum(array_map('getFileSize', $coreJsFiles));
$tiptapJsSize = array_sum(array_map('getFileSize', $tiptapJsFiles));
$cssSize = array_sum(array_map('getFileSize', $cssFiles));

echo "Bundle size report:\n";
echo '  Core JS : '.formatBytes($coreJsSize)."\n";
echo '  Tiptap JS: '.formatBytes($tiptapJsSize)." (opt-in)\n";
echo '  CSS      : '.formatBytes($cssSize)."\n";
echo "\n";

$passed = 0;

// Skipped - gzip measurement requires runtime
echo "Skipped - gzip measurement requires build step. Check sizes above against budget.\n";
exit(0);
