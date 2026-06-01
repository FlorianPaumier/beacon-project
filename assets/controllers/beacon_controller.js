/*──────────────────────────────────────────────────────────────
  Beacon Admin -- Stimulus Dashboard Controller
──────────────────────────────────────────────────────────────*/

import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["widget"];

    connect() {
        this.element.classList.add("beacon-dashboard--loaded");
    }
}
