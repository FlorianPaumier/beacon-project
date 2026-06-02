import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["search"];
    static values = { url: String };

    connect() {
        this.searchTimeout = null;
    }

    search(event) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            const params = new URLSearchParams(window.location.search);
            params.set("search", event.target.value);
            params.set("page", "1");
            window.location.href = `${this.urlValue}?${params.toString()}`;
        }, 300);
    }

    sort(event) {
        const field = event.params.field;
        const params = new URLSearchParams(window.location.search);
        const currentDir = params.get("dir") || "asc";
        params.set("sort", field);
        params.set("dir", currentDir === "asc" ? "desc" : "asc");
        params.set("page", "1");
        window.location.href = `${this.urlValue}?${params.toString()}`;
    }

    goToPage(event) {
        const page = event.params.page;
        const params = new URLSearchParams(window.location.search);
        params.set("page", page);
        window.location.href = `${this.urlValue}?${params.toString()}`;
    }
}
