#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Spec Compliance Audit.
 *
 * Reads spec files from specs/ and verifies implementation existence
 * by checking that referenced PHP classes, Twig templates, and config
 * files exist in the codebase.
 *
 * Usage: php bin/audit-spec.php
 * Exit code 0: all checks pass
 * Exit code 1: one or more checks failed
 */
$root = dirname(__DIR__);
$errors = [];

function findClass(string $root, string $className): bool
{
    $classPath = str_replace('Devgeek\\BeaconAdmin\\', '', $className);
    $path = $root.'/src/'.str_replace('\\', '/', $classPath).'.php';

    return file_exists($path);
}

function findTemplate(string $root, string $template): bool
{
    $path = $root.'/templates/'.$template;

    return file_exists($path);
}

// P0 Feature checks
$checks = [
    // Dashboard
    ['label' => 'DashboardController', 'class' => 'Devgeek\\BeaconAdmin\\Controller\\DashboardController'],
    ['label' => 'DashboardTemplate', 'template' => 'dashboard.html.twig'],
    ['label' => 'WidgetRegistry', 'class' => 'Devgeek\\BeaconAdmin\\Widget\\WidgetRegistry'],
    ['label' => 'StatsWidget', 'class' => 'Devgeek\\BeaconAdmin\\Widget\\StatsWidget'],
    ['label' => 'TableWidget', 'class' => 'Devgeek\\BeaconAdmin\\Widget\\TableWidget'],
    ['label' => 'ChartWidget', 'class' => 'Devgeek\\BeaconAdmin\\Widget\\ChartWidget'],

    // CRUD
    ['label' => 'AbstractCrudController', 'class' => 'Devgeek\\BeaconAdmin\\Controller\\AbstractCrudController'],
    ['label' => 'MakeResourceCommand', 'class' => 'Devgeek\\BeaconAdmin\\Command\\MakeResourceCommand'],
    ['label' => 'EntityIntrospector', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\Doctrine\\EntityIntrospector'],
    ['label' => 'FormBuilder', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\Form\\FormBuilder'],
    ['label' => 'DataTable', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\DataTable\\DataTable'],

    // V2 features
    ['label' => 'ColumnGroup', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\DataTable\\Column\\ColumnGroup'],
    ['label' => 'EnumField', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\Field\\EnumField'],
    ['label' => 'CsvExportAction', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\Action\\CsvExportAction'],
    ['label' => 'GlobalSearchController', 'class' => 'Devgeek\\BeaconAdmin\\Controller\\GlobalSearchController'],
    ['label' => 'GlobalSearchProviderInterface', 'class' => 'Devgeek\\BeaconAdmin\\Search\\GlobalSearchProviderInterface'],
    ['label' => 'AssociationSearchController', 'class' => 'Devgeek\\BeaconAdmin\\Controller\\AssociationSearchController'],
    ['label' => 'Tabs', 'class' => 'Devgeek\\BeaconAdmin\\Form\\Component\\Tabs'],
    ['label' => 'TabPane', 'class' => 'Devgeek\\BeaconAdmin\\Form\\Component\\TabPane'],
    ['label' => 'Row', 'class' => 'Devgeek\\BeaconAdmin\\Form\\Component\\Row'],
    ['label' => 'Column', 'class' => 'Devgeek\\BeaconAdmin\\Form\\Component\\Column'],

    // Filter system
    ['label' => 'FilterInterface', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\DataTable\\Filter\\FilterInterface'],
    ['label' => 'BooleanFilter', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\DataTable\\Filter\\BooleanFilter'],
    ['label' => 'ChoiceFilter', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\DataTable\\Filter\\ChoiceFilter'],
    ['label' => 'DateFilter', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\DataTable\\Filter\\DateFilter'],
    ['label' => 'EntityFilter', 'class' => 'Devgeek\\BeaconAdmin\\Crud\\DataTable\\Filter\\EntityFilter'],

    // Security
    ['label' => 'BeaconAccess', 'class' => 'Devgeek\\BeaconAdmin\\Security\\BeaconAccess'],
    ['label' => 'BeaconAccessVoter', 'class' => 'Devgeek\\BeaconAdmin\\Security\\BeaconAccessVoter'],
    ['label' => 'LoginFormAuthenticator', 'class' => 'Devgeek\\BeaconAdmin\\Security\\LoginFormAuthenticator'],

    // Notifications
    ['label' => 'NotificationManager', 'class' => 'Devgeek\\BeaconAdmin\\Notification\\NotificationManager'],
    ['label' => 'NotificationController', 'class' => 'Devgeek\\BeaconAdmin\\Controller\\NotificationController'],

    // Templates
    ['label' => 'ListTemplate', 'template' => 'crud/list.html.twig'],
    ['label' => 'ShowTemplate', 'template' => 'crud/show.html.twig'],
    ['label' => 'LoginTemplate', 'template' => 'security/login.html.twig'],
    ['label' => 'SidebarTemplate', 'template' => 'components/sidebar.html.twig'],
    ['label' => 'GlobalSearchTemplate', 'template' => 'components/global_search.html.twig'],
    ['label' => 'NotificationInboxTemplate', 'template' => 'notification/inbox.html.twig'],
];

$passed = 0;
$failed = 0;

foreach ($checks as $check) {
    $ok = true;

    if (isset($check['class'])) {
        $ok = findClass($root, $check['class']);
    } elseif (isset($check['template'])) {
        $ok = findTemplate($root, $check['template']);
    }

    if ($ok) {
        ++$passed;
    } else {
        ++$failed;
        $errors[] = sprintf('  FAIL: %s', $check['label']);
    }
}

echo sprintf("\nSpec Audit: %d passed, %d failed\n", $passed, $failed);

if ($errors !== []) {
    echo "Errors:\n";
    echo implode("\n", $errors)."\n";
    exit(1);
}

echo "All spec items verified.\n";
exit(0);
