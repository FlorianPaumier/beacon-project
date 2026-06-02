import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        autoDismiss: { type: Boolean, default: true },
        dismissAfter: { type: Number, default: 5000 },
    };

    connect() {
        this.element.classList.add("beacon-alert--visible");

        if (this.autoDismissValue) {
            this.timeout = setTimeout(() => {
                this.dismiss();
            }, this.dismissAfterValue);
        }
    }

    disconnect() {
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
    }

    dismiss(event) {
        if (event) {
            event.preventDefault();
        }

        if (this.timeout) {
            clearTimeout(this.timeout);
        }

        this.element.classList.remove("beacon-alert--visible");
        this.element.classList.add("beacon-alert--dismissing");

        setTimeout(() => {
            if (this.element.parentNode) {
                this.element.parentNode.removeChild(this.element);
            }
        }, 300);
    }
}
