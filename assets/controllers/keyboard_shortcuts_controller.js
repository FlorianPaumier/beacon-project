import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        enabled: { type: Boolean, default: true },
    };

    connect() {
        this.bound = this.handleKeydown.bind(this);
        document.addEventListener("keydown", this.bound);
    }

    disconnect() {
        document.removeEventListener("keydown", this.bound);
    }

    isTyping(target) {
        if (!(target instanceof HTMLElement)) {
            return false;
        }
        const tag = target.tagName;
        if (tag === "INPUT" || tag === "TEXTAREA" || tag === "SELECT") {
            return true;
        }
        if (target.isContentEditable) {
            return true;
        }
        return false;
    }

    handleKeydown(event) {
        if (!this.enabledValue) {
            return;
        }
        if (event.defaultPrevented) {
            return;
        }

        if ((event.metaKey || event.ctrlKey) && event.key === "k") {
            event.preventDefault();
            this.openGlobalSearch();
            return;
        }

        if (event.metaKey || event.ctrlKey || event.altKey || event.shiftKey) {
            return;
        }
        if (this.isTyping(event.target)) {
            return;
        }

        switch (event.key) {
            case "n":
                this.triggerShortcut(event, "[data-beacon-shortcut=\"new\"]");
                break;
            case "/":
                this.focusSearch(event);
                break;
            case "Escape":
                this.handleEscape(event);
                break;
        }
    }

    openGlobalSearch() {
        const dialog = document.getElementById("beacon-global-search-dialog");
        if (!dialog) {
            return;
        }
        const controller = this.application.getControllerForElementAndIdentifier(
            dialog,
            "@beacon-admin/global-search"
        );
        if (controller) {
            controller.open();
        }
    }

    triggerShortcut(event, selector) {
        const el = document.querySelector(selector);
        if (!el) {
            return;
        }
        event.preventDefault();
        if (typeof el.click === "function") {
            el.click();
        } else if (el.dataset && el.dataset.href) {
            window.location.href = el.dataset.href;
        }
    }

    focusSearch(event) {
        const el = document.querySelector("[data-beacon-shortcut=\"search\"]");
        if (!el) {
            return;
        }
        event.preventDefault();
        el.focus();
        if (typeof el.select === "function") {
            el.select();
        }
    }

    handleEscape(event) {
        const openDialog = document.querySelector("dialog[open]");
        if (openDialog) {
            event.preventDefault();
            if (typeof openDialog.close === "function") {
                openDialog.close();
            }
            return;
        }
        const closeEl = document.querySelector("[data-beacon-shortcut=\"close\"]");
        if (closeEl) {
            event.preventDefault();
            if (typeof closeEl.click === "function") {
                closeEl.click();
            }
        }
    }
}
