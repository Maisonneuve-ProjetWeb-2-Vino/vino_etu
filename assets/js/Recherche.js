import Affichage from "./Affichage.js";

export default class Recherche {
    #elRecherche;
    #elPays;

    /**
     * Constructeur de la classe Recherche
     */
    constructor() {
        // Sur la page de Recherche
        this.#elRecherche = document.querySelector(".recherche");
        this.#elPays = document.querySelector("#pays");

        this.initialiser();
    }

    initialiser() {
        if (this.#elRecherche) {
            this.#elPays.addEventListener('change', this.filtrer.bind(this));
        }
    }

    filtrer(evt) {
        const elPays = document.querySelector("#pays");
        const nom_pays = elPays.options[elPays.selectedIndex].text;
        const id_pays = document.querySelector("#pays").value;
        const pays = {
            "pays":nom_pays,
            "id_pays":id_pays
        }
        console.log(pays)
        const domParent = document.querySelector("#pays_actifs");
        const gabaritDetails = document.querySelector("#tmpl_pays_actifs").innerHTML;
        Affichage.afficher(pays, gabaritDetails, domParent);
    }

    //const domParent = document.querySelector("main");
    //const gabaritDetails = document.querySelector("#tmpl_detail").innerHTML;
    //Affichage.afficher(oeuvreSelectionnee, gabaritDetails, domParent);
}