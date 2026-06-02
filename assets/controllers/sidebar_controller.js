import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = { collapsed: { type: Boolean, default: false } };

    connect() {
        const saved = localStorage.getItem("beacon-sidebar-collapsed");
        if (saved !== null) {
            this.collapsedValue = saved === "true";
        }
        this.applyState();
    }

    toggle(event) {
        if (event) {
            event.preventDefault();
        }
        this.collapsedValue = !this.collapsedValue;
        localStorage.setItem("beacon-sidebar-collapsed", String(this.collapsedValue));
        this.applyState();
    }

    applyState() {
        this.element.classList.toggle("beacon-sidebar--collapsed", this.collapsedValue);
        document.body.classList.toggle("beacon-sidebar-collapsed", this.collapsedValue);
    }
}
