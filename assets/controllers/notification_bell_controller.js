import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        unreadCountUrl: String,
        pollInterval: { type: Number, default: 30000 },
    };

    static targets = ["badge", "dropdown"];

    connect() {
        this.pollTimer = null;
        this.refresh();

        if (this.pollIntervalValue > 0) {
            this.pollTimer = setInterval(() => this.refresh(), this.pollIntervalValue);
        }

        this.boundClickOutside = this.handleClickOutside.bind(this);
        document.addEventListener("click", this.boundClickOutside);
    }

    disconnect() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
        document.removeEventListener("click", this.boundClickOutside);
    }

    async refresh() {
        if (!this.unreadCountUrlValue) {
            return;
        }

        try {
            const response = await fetch(this.unreadCountUrlValue, {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            if (!response.ok) {
                return;
            }
            const data = await response.json();
            this.updateBadge(Number(data.count) || 0);
            this.updateDropdown(Array.isArray(data.items) ? data.items : []);
        } catch (error) {
            // Silent failure: keep previous state.
        }
    }

    updateBadge(count) {
        for (const badge of this.badgeTargets) {
            badge.textContent = String(count);
            if (count > 0) {
                badge.classList.remove("beacon-notification-bell-badge--hidden");
            } else {
                badge.classList.add("beacon-notification-bell-badge--hidden");
            }
        }
    }

    updateDropdown(items) {
        if (!this.hasDropdownTarget) {
            return;
        }
        const list = this.dropdownTarget.querySelector("[data-notification-list]");
        if (!list) {
            return;
        }
        list.innerHTML = "";

        if (items.length === 0) {
            const empty = document.createElement("li");
            empty.className = "beacon-notification-bell-empty";
            empty.textContent = "No new notifications";
            list.appendChild(empty);
            return;
        }

        for (const item of items) {
            list.appendChild(this.buildItem(item));
        }
    }

    buildItem(item) {
        const li = document.createElement("li");
        li.className = `beacon-notification-bell-item beacon-notification-bell-item--${item.type}`;

        const icon = document.createElement("span");
        icon.className = "beacon-notification-bell-item-icon";
        icon.textContent = this.iconFor(item.type);
        li.appendChild(icon);

        const message = document.createElement("span");
        message.className = "beacon-notification-bell-item-message";
        message.textContent = item.message;
        li.appendChild(message);

        return li;
    }

    iconFor(type) {
        switch (type) {
            case "success":
                return "✓";
            case "error":
                return "✗";
            case "warning":
                return "⚠";
            default:
                return "ℹ";
        }
    }

    toggle(event) {
        event.preventDefault();
        event.stopPropagation();
        if (!this.hasDropdownTarget) {
            return;
        }
        const isOpen = !this.dropdownTarget.classList.contains("beacon-notification-bell-dropdown--hidden");
        if (isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        if (!this.hasDropdownTarget) {
            return;
        }
        this.dropdownTarget.classList.remove("beacon-notification-bell-dropdown--hidden");
        this.refresh();
    }

    close() {
        if (!this.hasDropdownTarget) {
            return;
        }
        this.dropdownTarget.classList.add("beacon-notification-bell-dropdown--hidden");
    }

    handleClickOutside(event) {
        if (!this.hasDropdownTarget) {
            return;
        }
        if (this.element.contains(event.target)) {
            return;
        }
        this.close();
    }
}
