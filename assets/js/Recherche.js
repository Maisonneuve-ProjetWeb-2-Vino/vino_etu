import Affichage from "./Affichage.js";
import Fetch from "./Fetch.js";

export default class Recherche {
    #elRecherche;
    #elPays;
    #elCouleur;
    #requete;
    #elExpression;
    #elCatalogue;
    #elCelliers;
    #select;

    /**
     * Constructeur de la classe Recherche
     */
    constructor() {
        // Sur la page de Recherche
        this.#elRecherche = document.querySelector(".recherche");
        this.#elPays = document.querySelector("#pays");
        this.#elCouleur = document.querySelector("#couleur");
        this.#elExpression = document.querySelector("#recherche_termes");
        this.#elCatalogue = document.querySelector("#catalogue");
        this.#elCelliers = document.querySelector("#celliers");

        this.initialiser();
    }

    initialiser() {
        if (this.#elRecherche) {
            this.#elPays.addEventListener('change', this.ajouterFiltrePays.bind(this));
            this.#elCouleur.addEventListener('change', this.ajouterFiltreCouleur.bind(this));
            this.#elExpression.addEventListener('keypress', this.entrerRechercheClavier.bind(this))
            this.#elCatalogue.addEventListener('change', this.changerSourceDonnees.bind(this));
            this.#elCelliers.addEventListener('change', this.changerSourceDonnees.bind(this));

            this.#requete = {
                "donnees":"catalogue",
                "recherche":"",
                "filtre": {
                    "couleur":[],
                    "pays":[]
                }
            }

            this.#select = {
                'couleur':this.#elCouleur,
                'pays':this.#elPays
            }

            this.faireRequeteRecherche();
        }
    }

    changerSourceDonnees(evt) {
        this.#requete["donnees"] = evt.target.value;
        this.faireRequeteRecherche();
    }

    entrerRechercheClavier(evt) {
        if (evt.key == "Enter") {
            evt.preventDefault();
            let expressionRecherche = this.#elExpression.value;
            this.#requete["recherche"] = expressionRecherche;
            this.faireRequeteRecherche();
        }
    }

    ajouterFiltrePays(evt) {
        const nomFiltre = "pays";
        const elSelectPays = document.querySelector("#pays");
        const elDomPays = document.querySelector(".pays_actifs");
        const cssPays = ["bulle_filtre_neutre", "bulle_filtre_blanche"];

        this.ajouterFiltre(nomFiltre, elSelectPays, elDomPays, cssPays);
    }

    ajouterFiltreCouleur(evt) {
        const nomFiltre = "couleur";
        const elSelectCouleur = document.querySelector("#couleur");
        const elDomCouleur = document.querySelector(".couleurs_actives");

        // Cas spécial pour les bulles de couleur
        const valeurFiltre = elSelectCouleur.value;
        let cssCouleur = ["bulle_filtre_neutre"];
        switch(valeurFiltre) {
            case 'Blanc': 
                cssCouleur.push("bulle_filtre_vin_blanc");
                break;
            case 'Rosé': 
                cssCouleur.push("bulle_filtre_vin_rose");
                break;
            case 'Rouge': 
                cssCouleur.push("bulle_filtre_vin_rouge");
                break;
        }

        this.ajouterFiltre(nomFiltre, elSelectCouleur, elDomCouleur, cssCouleur);
    }

    ajouterFiltre(nomFiltre, elSelect, elDomInsertion, classesCSS) {

        const valeurAfficheeFiltre = elSelect.options[elSelect.selectedIndex].text;
        const valeurFiltre = elSelect.value;

        // On cache l'élément sélectionné et on affiche l'option par défaut
        elSelect.options[elSelect.selectedIndex].style.display = "none";
        elSelect.value = "";

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
        this.#requete['filtre'][nomFiltre].push(valeurFiltre);
        
        // Obtenir termes de recherche
        let expressionRecherche = this.#elExpression.value;
        this.#requete["recherche"] = expressionRecherche;
        console.log(expressionRecherche);
        
        this.faireRequeteRecherche();
    }

    faireRequeteRecherche() {
        const oFetch = new Fetch();
        oFetch.filtrerBouteilles(this.#requete, this.afficherResultats.bind(this));
    }

    afficherResultats(resultats) {
        if (typeof resultats === 'object' && resultats !== null) {
            resultats = Object.values(resultats);
        }

        const nombreTotalResultats = resultats.length;

        resultats = resultats.slice(0, 24);
        const domParent = document.querySelector(".resultats");
        const gabaritResultats = document.querySelector("#tmpl_resultats").innerHTML;

        resultats.forEach(resultat => {
            if (resultat.idtype == "Rouge") {
                resultat.bouteille_classe = "bouteille_rouge"
            } else if (resultat.idtype == "Rosé") {
                resultat.bouteille_classe = "bouteille_rose";
            } else if (resultat.idtype == "Blanc") {
                resultat.bouteille_classe = "bouteille_blanche";
            }

            if (!resultat.image_url) {
                resultat.image_url = "assets/img/default-bottle-img.png";
            }

             if (!resultat.pays) {
                resultat.pays = "";
             }
        });


        //Affichage du titre des résultats
        const nombreResultatsAffiches = resultats.length;
        let titre = "";
        if (nombreResultatsAffiches >= 24) {
            titre = `Résultats 1-24 sur ${nombreTotalResultats}`;
        } else {
            titre = `Résultats 1-${nombreResultatsAffiches} sur ${nombreTotalResultats}`;
        }

        // Si aucun resultat
        if (nombreResultatsAffiches == 0) {
            titre = "Aucun résultat trouvé. Enlevez des filtres ou changez l'expression recherchée."
        }

        document.querySelector(".titre_resultats").textContent = titre;

        Affichage.afficher(resultats, gabaritResultats, domParent);
    }

    enleverFiltre(evt) {
        // Enlever la bulle du filtre
        const noeudFiltre = evt.currentTarget;
        const filtre = noeudFiltre.dataset.filtre;
        const filtreValeur = noeudFiltre.dataset.filtreValeur;
        noeudFiltre.parentNode.removeChild(noeudFiltre);

        // Enlever la valeur du filtre de la requête
        let tableauFiltre = this.#requete['filtre'][filtre];
        const index = tableauFiltre.indexOf(filtreValeur);
        if (index > -1) {
            tableauFiltre.splice(index, 1);
        } else {
            throw new Error('Erreur: valeur de filtre non trouvée');
        }

        // On réaffiche le filtre dans la Sélect
        const elSelect = this.#select[filtre];
        elSelect.querySelector(`option[value="${filtreValeur}"]`).style.display = "block";

        this.faireRequeteRecherche();
    }

}