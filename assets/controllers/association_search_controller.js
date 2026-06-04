import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        url: String,
        debounce: { type: Number, default: 300 },
        entity: String,
        searchField: { type: String, default: "name" },
    };

    connect() {
        this.debounceTimer = null;
        this.input = this.element.querySelector("input[type='search']");

        if (!this.input) {
            return;
        }

        this.input.addEventListener("input", this.onInput.bind(this));
    }

    onInput() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.doSearch(this.input.value);
        }, this.debounceValue);
    }

    async doSearch(query) {
        const list = this.element.querySelector("ul, datalist");
        if (!list) {
            return;
        }

        if (!query || query.length < 1) {
            list.innerHTML = "";
            return;
        }

        try {
            const url = new URL(this.urlValue, window.location.origin);
            url.searchParams.set("entity", this.entityValue);
            url.searchParams.set("q", query);
            url.searchParams.set("field", this.searchFieldValue);

            const response = await fetch(url);
            const data = await response.json();

            list.innerHTML = "";

            data.results.forEach((result) => {
                const option = document.createElement("option");
                option.value = result.id;
                option.textContent = result.label;
                list.appendChild(option);
            });
        } catch (e) {
            // silently fail
        }
    }
}
