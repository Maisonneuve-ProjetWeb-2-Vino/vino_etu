import Cellier from "./Cellier.js";

export default class Application {

    #oCellier;

    /**
     * Constructeur de la classe Application
     */
    constructor() {
        this.#oCellier = new Cellier();
    }
}