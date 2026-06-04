import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        notifications: { type: Array, default: [] },
        maxVisible: { type: Number, default: 5 },
        defaultDuration: { type: Number, default: 5000 },
    };

    connect() {
        this.queue = [];
        this.active = new Set();

        if (Array.isArray(this.notificationsValue) && this.notificationsValue.length > 0) {
            this.enqueue(this.notificationsValue);
        }

        this.processQueue();
    }

    disconnect() {
        for (const node of this.active) {
            if (node._beaconTimer) {
                clearTimeout(node._beaconTimer);
            }
        }
        this.active.clear();
    }

    enqueue(notifications) {
        for (const n of notifications) {
            this.queue.push(n);
        }
        this.processQueue();
    }

    processQueue() {
        while (this.active.size < this.maxVisibleValue && this.queue.length > 0) {
            const notification = this.queue.shift();
            this.show(notification);
        }
    }

    show(notification) {
        const element = this.buildElement(notification);
        this.element.appendChild(element);

        requestAnimationFrame(() => {
            element.classList.add("beacon-toast--visible");
        });

        this.active.add(element);

        const isSticky = notification.sticky === true || notification.duration === 0;
        const duration = isSticky ? 0 : (Number(notification.duration) || this.defaultDurationValue);

        if (duration > 0) {
            element._beaconTimer = setTimeout(() => {
                this.dismiss({ target: element });
            }, duration);
        } else {
            element.addEventListener("click", (event) => {
                if (event.target.closest(".beacon-toast-action")) {
                    return;
                }
                this.dismiss({ target: element });
            });
        }

        element.querySelectorAll("[data-action='beacon-dismiss']").forEach((btn) => {
            btn.addEventListener("click", (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.dismiss({ target: element });
            });
        });
    }

    dismiss(event) {
        const target = event?.target;
        const element = target?.closest ? target.closest(".beacon-toast") : null;
        if (!element || !this.active.has(element)) {
            return;
        }

        if (event && typeof event.preventDefault === "function") {
            event.preventDefault();
        }

        if (element._beaconTimer) {
            clearTimeout(element._beaconTimer);
        }

        element.classList.remove("beacon-toast--visible");
        element.classList.add("beacon-toast--dismissing");

        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
            this.active.delete(element);
            this.processQueue();
        }, 300);
    }

    buildElement(notification) {
        const wrapper = document.createElement("div");
        wrapper.className = `beacon-toast beacon-toast--${notification.type}`;
        wrapper.setAttribute("role", "alert");

        const icon = document.createElement("span");
        icon.className = "beacon-toast-icon";
        icon.textContent = this.iconFor(notification.type);
        wrapper.appendChild(icon);

        const content = document.createElement("div");
        content.className = "beacon-toast-content";
        const message = document.createElement("span");
        message.className = "beacon-toast-message";
        message.textContent = notification.message ?? "";
        content.appendChild(message);

        if (notification.action_label && notification.action_url) {
            const action = document.createElement("a");
            action.className = "beacon-toast-action";
            action.href = notification.action_url;
            action.textContent = notification.action_label;
            content.appendChild(action);
        }

        wrapper.appendChild(content);

        const close = document.createElement("button");
        close.className = "beacon-toast-close";
        close.setAttribute("type", "button");
        close.setAttribute("aria-label", "Close");
        close.setAttribute("data-action", "beacon-dismiss");
        close.textContent = "×";
        wrapper.appendChild(close);

        return wrapper;
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
}
