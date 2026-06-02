import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["thumb", "label"];
    static values = { url: String, token: String, value: String, labels: Object };

    toggle() {
        const body = new URLSearchParams();
        body.set("_token", this.tokenValue);

        fetch(this.urlValue, {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body,
        })
            .then((r) => r.json())
            .then((data) => {
                if (!data.success) {
                    return;
                }

                const on = data.value;
                this.element.classList.toggle("beacon-toggle--on", on);
                this.valueValue = on ? "1" : "0";

                if (this.hasLabelTarget && this.labelsValue) {
                    this.labelTarget.textContent = on ? this.labelsValue.true : this.labelsValue.false;
                }
            });
    }
}
