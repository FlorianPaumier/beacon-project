import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["form"];
    static values = { id: String };

    confirm() {
        if (confirm("Are you sure you want to delete this item? This action cannot be undone.")) {
            this.formTarget.submit();
        }
    }

    cancel() {
        this.element.remove();
    }
}
