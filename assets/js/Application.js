import Cellier from "./Cellier.js";
import Recherche from "./Recherche.js";

export default class Application {

    #oCellier;
    #oRecherche;

    /**
     * Constructeur de la classe Application
     */
    constructor() {
        this.#oCellier = new Cellier();
        this.#oRecherche = new Recherche();
    }
}