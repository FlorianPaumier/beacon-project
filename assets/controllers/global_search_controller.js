import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ["dialog", "input", "results", "resultTemplate", "emptyTemplate"];
    static values = {
        url: String,
        debounce: { type: Number, default: 300 },
    };

    connect() {
        this.debounceTimer = null;
        this.selectedIndex = -1;
    }

    open() {
        this.dialogTarget.showModal();
        this.inputTarget.value = "";
        this.inputTarget.focus();
        this.selectedIndex = -1;
        this.resultsTarget.innerHTML = "";
    }

    close() {
        this.dialogTarget.close();
        this.inputTarget.value = "";
        this.resultsTarget.innerHTML = "";
    }

    performSearch() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.doSearch(this.inputTarget.value);
        }, this.debounceValue);
    }

    async doSearch(query) {
        if (!query || query.length < 1) {
            this.resultsTarget.innerHTML = "";
            return;
        }

        this.resultsTarget.innerHTML = '<div class="beacon-global-search-modal__loading">Searching...</div>';

        try {
            const url = new URL(this.urlValue, window.location.origin);
            url.searchParams.set("q", query);
            const response = await fetch(url);
            const data = await response.json();

            if (data.results.length === 0) {
                this.resultsTarget.innerHTML = this.emptyTemplateTarget.innerHTML;
                return;
            }

            this.resultsTarget.innerHTML = "";
            this.selectedIndex = -1;

            data.results.forEach((result, index) => {
                const clone = this.resultTemplateTarget.content.cloneNode(true);
                const link = clone.querySelector("a");
                link.href = result.url;
                link.dataset.index = index;
                clone.querySelector(".beacon-global-search-modal__result-title").textContent = result.title;
                clone.querySelector(".beacon-global-search-modal__result-description").textContent = result.description;
                clone.querySelector(".beacon-global-search-modal__result-provider").textContent = result.provider;
                this.resultsTarget.appendChild(clone);
            });

            this.navigateTo(0);
        } catch (e) {
            this.resultsTarget.innerHTML = '<div class="beacon-global-search-modal__error">Search failed. Try again.</div>';
        }
    }

    handleKeydown(event) {
        const links = this.resultsTarget.querySelectorAll("a");

        switch (event.key) {
            case "ArrowDown":
                event.preventDefault();
                if (links.length > 0) {
                    this.selectedIndex = Math.min(this.selectedIndex + 1, links.length - 1);
                    this.navigateTo(this.selectedIndex);
                }
                break;
            case "ArrowUp":
                event.preventDefault();
                if (links.length > 0) {
                    this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                    this.navigateTo(this.selectedIndex);
                }
                break;
            case "Enter":
                event.preventDefault();
                if (this.selectedIndex >= 0 && links[this.selectedIndex]) {
                    links[this.selectedIndex].click();
                }
                break;
            case "Escape":
                this.close();
                break;
        }
    }

    navigateTo(index) {
        const links = this.resultsTarget.querySelectorAll("a");
        links.forEach((link, i) => {
            link.classList.toggle("beacon-global-search-modal__result--selected", i === index);
        });
    }
}
