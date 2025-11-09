
class Menu extends HTMLElement {
    constructor() {
        super()
        this.loadHTML();
    }

    async loadHTML() {
        try {
            const response = await fetch("mis-tags/menu.php"); // Change the path as needed
            if (!response.ok) throw new Error("Failed to load menu.");
            this.innerHTML = await response.text();
        } catch (error) {
            console.error(error);
            this.innerHTML = "<p>Error loading menu.</p>";
        }
    }
}
window.customElements.define('mi-menu', Menu);
