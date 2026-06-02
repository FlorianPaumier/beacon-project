import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["checkbox", "selectAll", "selectAllContainer", "bar", "count", "idsContainer", "form"];

    connect() {
        this.update();
    }

    toggle() {
        this.update();
    }

    toggleAll(event) {
        const checked = event.target.checked;
        this.checkboxTargets.forEach((cb) => {
            cb.checked = checked;
        });
        this.update();
    }

    update() {
        const checked = this.checkboxTargets.filter((cb) => cb.checked);
        const count = checked.length;

        this.countTarget.textContent = `${count} selected`;

        if (count > 0) {
            this.barTarget.style.display = "";
        } else {
            this.barTarget.style.display = "none";
        }

        this.selectAllTarget.indeterminate = count > 0 && count < this.checkboxTargets.length;
        this.selectAllTarget.checked = count > 0 && count === this.checkboxTargets.length;

        const idsContainer = this.idsContainerTarget;
        idsContainer.innerHTML = "";
        checked.forEach((cb) => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "ids[]";
            input.value = cb.value;
            idsContainer.appendChild(input);
        });
    }
}
