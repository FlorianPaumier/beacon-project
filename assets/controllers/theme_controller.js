import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = { theme: { type: String, default: "modern" } };

    connect() {
        const saved = localStorage.getItem("beacon-theme");
        this.themeValue = saved || this.element.dataset.theme || "modern";

        const colorScheme = localStorage.getItem("beacon-color-scheme");
        if (colorScheme === "dark") {
            document.documentElement.classList.add("dark");
        } else if (colorScheme === "light") {
            document.documentElement.classList.remove("dark");
        } else {
            const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            if (prefersDark) {
                document.documentElement.classList.add("dark");
            }
        }

        this.applyTheme();

        window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", (event) => {
            if (localStorage.getItem("beacon-color-scheme") === null) {
                document.documentElement.classList.toggle("dark", event.matches);
            }
        });
    }

    setTheme(event) {
        const theme = event.target.value;
        this.themeValue = theme;
        localStorage.setItem("beacon-theme", theme);
        this.applyTheme();
    }

    toggleColorScheme(event) {
        if (event) {
            event.preventDefault();
        }
        const isDark = document.documentElement.classList.toggle("dark");
        localStorage.setItem("beacon-color-scheme", isDark ? "dark" : "light");
    }

    applyTheme() {
        document.documentElement.setAttribute("data-theme", this.themeValue);

        const link = document.getElementById("beacon-theme-css");
        if (!link) return;

        const themes = this.getThemePaths();
        const cssPath = themes[this.themeValue];
        if (cssPath && link.getAttribute("href") !== cssPath) {
            const newLink = document.createElement("link");
            newLink.rel = "stylesheet";
            newLink.href = cssPath;
            newLink.id = "beacon-theme-css";
            newLink.onload = () => link.remove();
            link.parentNode.insertBefore(newLink, link.nextSibling);
            link.remove();
        }
    }

    getThemePaths() {
        try {
            const raw = this.element.dataset.themes;
            return raw ? JSON.parse(raw) : {};
        } catch {
            return {};
        }
    }
}
