import Fetch from "./Fetch.js";

export default class Cellier {

    #elBoireBouteille;
    #elAjouterBouteille
    #elCible;
    #elParent;

    /**
     * Constructeur de la classe Cellier
     */
    constructor() {

        this.elBoireBouteille = document.querySelectorAll(".btnBoire");
        this.elAjouterBouteille = document.querySelectorAll(".btnAjouter");        

        this.initialiser();
    }

    initialiser() {
        this.#elBoireBouteille.forEach(function(element){element.addEventListener("click",this.boireBouteille.bind(this))});
        this.#elAjouterBouteille.forEach(function(element){element.addEventListener("click",this.ajouterBouteille.bind(this))});

    }

    boireBouteille(evt) {
        // Aller chercher l'id de la bouteille du cellier
        let id = evt.target.closest(".options").dataset.id;
        this.#elCible = evt.currentTarget;
        this.#elParent = evt.currentTarget.parentElement;

        // Faire la requête et ajuster la quantité
        let requete = new Request("cellier?action=b", {method: 'POST', body: '{"id": '+id+'}'});
        const oFetch = new Fetch();
        oFetch.boireBouteille(requete, this.diminuerQuantiteBouteilles.bind(this));
    }

    ajouterBouteille(evt) {
        let id = evt.target.closest(".options").dataset.id;
        const elParent = evt.currentTarget.parentElement;
    }


    // Valide les champs nécessaires à la création ou modification des champs des bouteilles d'un cellier
    validerChampsBouteille(bouteille) {

        let validation = true;

        // Validation de la quantité
        if (bouteille.quantite.value){
            if(isNaN(bouteille.quantite.value)) {
            document.querySelector(".erreur_quantite").innerHTML = "La quantité doit être un nombre entier.";
            validation = false;
            }
            else {
                document.querySelector(".erreur_quantite").innerHTML = "";
            }
        }

        return validation;
    }

    validerChampsCellier(cellier) {

        let validation = true;

        if (!cellier.nom.value) {
            document.querySelector(".erreur_nom").innerHTML = "Le nom du cellier ne peut pas être vide";
            validation = false;
        }
        else {
            document.querySelector(".erreur_nom").innerHTML = "";
        }

        return validation;
    }

     
    changerStatutInterfaceAjout(bouteille, statut) {
        bouteille.nom.disabled = statut;
        bouteille.pays.disabled = statut;
        bouteille.type.disabled = statut;
        bouteille.millesime.disabled = statut;
        bouteille.appellation.disabled = statut;
        bouteille.format.disabled = statut;
        bouteille.cepage.disabled = statut;
        bouteille.particularite.disabled = statut;
        bouteille.degreAlcool.disabled = statut;
        bouteille.origine.disabled = statut;
        bouteille.producteur.disabled = statut;
        bouteille.prix.disabled = statut;
        bouteille.region.disabled = statut;
        bouteille.sucre.disabled = statut;
    }

    remplirChampsAjout(bouteille, details) {
        bouteille.pays.value = details.idpays;
        bouteille.type.value = details.idtype;
        bouteille.millesime.value = details.annee;
        bouteille.pastille.value = details.pastille;
        bouteille.appellation.value = details.appellation;
        bouteille.format.value = details.format;
        bouteille.cepage.value = details.cepage;
        bouteille.particularite.value = details.particularite;
        bouteille.degreAlcool.value = details.degreAlcool;
        bouteille.origine.value = details.origine;
        bouteille.producteur.value = details.producteur;
        bouteille.prix.value = details.prix_saq;
        bouteille.region.value = details.region;
        bouteille.sucre.value = details.sucre;
    }

    viderChampsAjout(bouteille) {
        bouteille.nom.value = "";
        bouteille.pays.value = 1;
        bouteille.type.value = "Blanc";
        bouteille.millesime.value = "";
        bouteille.pastille.value = "";
        bouteille.appellation.value = "";
        bouteille.format.value = "";
        bouteille.cepage.value = "";
        bouteille.particularite.value = "";
        bouteille.degreAlcool.value = "";
        bouteille.origine.value = "";
        bouteille.producteur.value = "";
        bouteille.prix.value = "";
        bouteille.region.value = "";
        bouteille.sucre.value = "";
    }

    diminuerQuantiteBouteilles() {
        for (let elEnfant of this.#elParent.children) {
            if (elEnfant.classList.contains("quantite")) {
                console.log("ici")
                let quantiteBouteille = elEnfant.children[0].innerHTML;

                if (quantiteBouteille > 0) {
                    quantiteBouteille -= 1;
                }
                
                if (quantiteBouteille == 0){
                    this.#elCible.disabled = true;
                    this.#elCible.classList.add("disabled-svg");
                }

                elEnfant.children[0].innerHTML = quantiteBouteille;
            }
        }
    }

}