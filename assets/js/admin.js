import { Application } from "@hotwired/stimulus";

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

// Register with StimulusBundle-resolved names (beacon-admin--<name>)
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

// Also register with short names used directly in some templates (beacon--<name>)
app.register("beacon--bulk-actions", BulkActionsController);
app.register("beacon--datatable", DatatableController);
app.register("beacon--delete-confirm", DeleteConfirmController);
app.register("beacon--inline-toggle", InlineToggleController);
