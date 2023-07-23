import Affichage from "./Affichage.js";
import Fetch from "./Fetch.js";

export default class Recherche {
    #elRecherche;
    #elPays;
    #elCouleur;
    #requete;

    /**
     * Constructeur de la classe Recherche
     */
    constructor() {
        // Sur la page de Recherche
        this.#elRecherche = document.querySelector(".recherche");
        this.#elPays = document.querySelector("#pays");
        this.#elCouleur = document.querySelector("#couleur");

        this.initialiser();
    }

    initialiser() {
        if (this.#elRecherche) {
            this.#elPays.addEventListener('change', this.ajouterFiltrePays.bind(this));
            this.#elCouleur.addEventListener('change', this.ajouterFiltreCouleur.bind(this));
        }

        this.#requete = {
            "donnee":"catalogue",
            "recherche":"",
            "couleur":[],
            "pays":[]
         }
    }

    ajouterFiltrePays(evt) {
        const nomFiltre = "pays";
        const elSelectPays = document.querySelector("#pays");
        const elDomPays = document.querySelector(".pays_actifs");
        const cssPays = ["filtre_pays"];

        this.ajouterFiltre(nomFiltre, elSelectPays, elDomPays, cssPays);
    }

    ajouterFiltreCouleur(evt) {
        const nomFiltre = "couleur";
        const elSelectCouleur = document.querySelector("#couleur");
        const elDomCouleur = document.querySelector(".couleurs_actives");
        const cssCouleur = ["filtre_pays"];

        this.ajouterFiltre(nomFiltre, elSelectCouleur, elDomCouleur, cssCouleur);
    }

    ajouterFiltre(nomFiltre, elSelect, elDomInsertion, classesCSS) {

        const valeurAfficheeFiltre = elSelect.options[elSelect.selectedIndex].text;
        //elSelect.options[elSelect.selectedIndex].style.display = "none";
        const valeurFiltre = elSelect.value;

        const noeudEnfant = document.createElement("div");

        for (const classe of classesCSS) {
            noeudEnfant.classList.add(classe);
        }

        noeudEnfant.setAttribute("data-filtre", nomFiltre);
        noeudEnfant.setAttribute("data-filtre-valeur", valeurFiltre);
        noeudEnfant.addEventListener("click", this.enleverFiltre.bind(this));
        elDomInsertion.appendChild(noeudEnfant);
        
        const valeurs = {
            "valeur_filtre":valeurAfficheeFiltre
        }
        
        const gabaritDetails = document.querySelector("#tmpl_filtres_actifs").innerHTML;
        Affichage.afficher(valeurs, gabaritDetails, noeudEnfant);

        // Faire la requête de recherche
        this.#requete[nomFiltre].push(valeurFiltre);
        console.log(this.#requete);
        const oFetch = new Fetch();
        //oFetch.filtrerBouteilles(this.#requete, this.afficherResultats.bind(this));
    }

    afficherResultats(resultats) {

    }

    enleverFiltre(evt) {
        const noeudFiltre = evt.currentTarget;
        const filtre = noeudFiltre.dataset.filtre;
        const filtreValeur = noeudFiltre.dataset.filtreValeur;
        noeudFiltre.parentNode.removeChild(noeudFiltre);


        //console.log(filtre_valeur);
    }
    //const domParent = document.querySelector("main");
    //const gabaritDetails = document.querySelector("#tmpl_detail").innerHTML;
    //Affichage.afficher(oeuvreSelectionnee, gabaritDetails, domParent);

    // Get the dropdown list element
    //const dropdown = document.getElementById("vehicles");

// Get the option to hide
//const optionToHide = dropdown.querySelector("option[value='bike']");

// Hide the option by setting its "display" style property to "none"
//optionToHide.style.display = "none";
}