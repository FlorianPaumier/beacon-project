import { Application } from "@hotwired/stimulus";

const isDebug = document.head.querySelector('meta[name="beacon-debug"]')?.content === 'true'
    || document.documentElement.dataset.env === 'dev';

if (isDebug) {
    console.info(
        '%cBeacon Admin%c JS loaded',
        'font-weight:bold;color:#2563eb',
        'color:inherit',
    );
}

import AssociationSearchController from "../controllers/association_search_controller.js";
import BeaconController from "../controllers/beacon_controller.js";
import BulkActionsController from "../controllers/bulk_actions_controller.js";
import DatatableController from "../controllers/datatable_controller.js";
import DeleteConfirmController from "../controllers/delete_confirm_controller.js";
import GlobalSearchController from "../controllers/global_search_controller.js";
import InlineToggleController from "../controllers/inline_toggle_controller.js";
import KeyboardShortcutsController from "../controllers/keyboard_shortcuts_controller.js";
import NotificationBellController from "../controllers/notification_bell_controller.js";
import NotificationController from "../controllers/notification_controller.js";
import SidebarController from "../controllers/sidebar_controller.js";
import ThemeController from "../controllers/theme_controller.js";

const app = Application.start();

// Register with StimulusBundle format (@beacon-admin/<name>)
app.register("beacon-admin--association-search", AssociationSearchController);
app.register("beacon-admin--beacon", BeaconController);
app.register("beacon-admin--bulk-actions", BulkActionsController);
app.register("beacon-admin--datatable", DatatableController);
app.register("beacon-admin--delete-confirm", DeleteConfirmController);
app.register("beacon-admin--global-search", GlobalSearchController);
app.register("beacon-admin--inline-toggle", InlineToggleController);
app.register("beacon-admin--keyboard-shortcuts", KeyboardShortcutsController);
app.register("beacon-admin--notification", NotificationController);
app.register("beacon-admin--notification-bell", NotificationBellController);
app.register("beacon-admin--sidebar", SidebarController);
app.register("beacon-admin--theme", ThemeController);

if (isDebug) {
    const count = app.router.modules ? [...app.router.modules].length : 0;
    console.info(
        '%cBeacon Admin%c %d controllers ready',
        'font-weight:bold;color:#16a34a',
        'color:inherit',
        count,
    );
}
