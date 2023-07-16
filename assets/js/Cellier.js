import Fetch from "./Fetch.js";

export default class Cellier {

    #elBoireBouteille;
    #elAjouterBouteille;
    #elModifierBouteille;
    #elInputNomBouteille;
    #liste;
    #elCible;
    #elParent;
    #bouteille;

    /**
     * Constructeur de la classe Cellier
     */
    constructor() {

        this.elBoireBouteille = document.querySelectorAll(".btnBoire");
        this.elAjouterBouteille = document.querySelectorAll(".btnAjouter");
        this.#elModifierBouteille = document.querySelectorAll(".btnModifier");  
        this.#inputNomBouteille = document.querySelector("[name='nom_bouteille']");
        this.#liste = document.querySelector('.listeAutoComplete');
        this.#nouvelleBouteille = document.querySelector(".nouvelleBouteille");

        this.initialiser();
    }

    initialiser() {
        // Lier les écouteurs d'événements aux boutons
        this.#elBoireBouteille.forEach(function(element){element.addEventListener("click",this.boireBouteille.bind(this))});
        this.#elAjouterBouteille.forEach(function(element){element.addEventListener("click",this.ajouterBouteille.bind(this))});
        this.#elModifierBouteille.forEach(function(element){element.addEventListener("click",this.afficherPageModificationBouteille.bind(this))});

        // Si on est sur la page d'ajout de bouteille
        if (this.#inputNomBouteille) {
            this.#bouteille = {
                nom : document.querySelector(".nom_bouteille"),
                quantite : document.querySelector("[name='quantite']"),
                pays : document.querySelector("[name='pays']"),
                type : document.querySelector("[name='type']"),
                millesime : document.querySelector("[name='millesime']"),
                pastille : document.querySelector("[name='pastille']"),
                appellation : document.querySelector("[name='appellation']"),
                format : document.querySelector("[name='format']"),
                cepage : document.querySelector("[name='cepage']"),
                particularite : document.querySelector("[name='particularite']"),
                degreAlcool : document.querySelector("[name='degreAlcool']"),
                origine : document.querySelector("[name='origine']"),
                producteur : document.querySelector("[name='producteur']"),
                prix : document.querySelector("[name='prix']"),
                region : document.querySelector("[name='region']"),
                sucre : document.querySelector("[name='sucre']")
            };

            // Bloquer les champs des détails
            changerStatutInterfaceAjout(bouteille, true);

            this.#inputNomBouteille.addEventListener("keyup", this.rechercherBouteille.bind(this));
            this.#liste.addEventListener("click", this.selectionnerBouteille.bind(this));
        }
    }

    selectionnerBouteille(evt) {
        if(evt.target.tagName == "LI"){
          bouteille.nom.dataset.id = evt.target.dataset.id;
          bouteille.nom.value = evt.target.innerHTML;
          
          liste.innerHTML = "";
          inputNomBouteille.value = "";

          const erreur_nom = document.querySelector(".erreur_nom_bouteille");
          erreur_nom.innerHTML = "";
          changerStatutInterfaceAjout(bouteille, true)

          let param = {
            "id_bouteille":bouteille.nom.dataset.id,
          }

          let requete = new Request("cellier?action=r", {method: 'POST', body: JSON.stringify(param)});
          const oFetch = new Fetch;
          oFetch.obtenirDetailsBouteille(requete, this.remplirChampsAjout.bind(this));

        }

    }

    rechercherBouteille(evt) {
        let nom = inputNomBouteille.value;
        liste.innerHTML = "";
        if(nom){
            let requete = new Request("cellier?action=c", {method: 'POST', body: '{"nom": "'+nom+'"}'});
            const oFetch = new Fetch();
            oFetch.rechercherBouteille(requete, this.afficherResultatsRecherche.bind(this));

        }
    }

    afficherResultatsRecherche(listeResultats) {
        listeResultats.forEach(function(element){
            this.#liste.innerHTML += "<li data-id='"+element.id +"'>"+element.nom+"</li>";
        });
    }


    boireBouteille(evt) {
        // Aller chercher l'id de la bouteille du cellier
        let id = evt.target.closest(".options").dataset.id;
        this.#elCible = evt.currentTarget;
        this.#elParent = evt.currentTarget.parentElement;

        // Faire la requête et ajuster la quantité
        let requete = new Request("cellier?action=b", {method: 'POST', body: '{"id": '+id+'}'});
        const oFetch = new Fetch();
        oFetch.boireBouteille(requete, this.diminuerQuantiteBouteille.bind(this));
    }

    ajouterBouteille(evt) {
        // Aller chercher l'id de la bouteille du cellier
        let id = evt.target.closest(".options").dataset.id;
        this.#elParent = evt.currentTarget.parentElement;

        // Faire la requête et ajuster la quantité
        let requete = new Request("cellier?action=a", {method: 'POST', body: '{"id": '+id+'}'});
        const oFetch = new Fetch();
        oFetch.ajouterBouteille(requete, this.augmenterQuantiteBouteille.bind(this));
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

    diminuerQuantiteBouteille() {
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

    augmenterQuantiteBouteille() {
        for (let elEnfant of this.#elParent.children) {
            if (elEnfant.classList.contains("quantite")) {
              let quantiteBouteille = parseInt(elEnfant.children[0].innerHTML);
              quantiteBouteille += 1;

              if (quantiteBouteille > 0){
                const elIcones = evt.target.closest(".icones_gauche");
                for (let enfant of elIcones.children) {
                  if (enfant.classList.contains("btnBoire")) {
                    enfant.disabled = false;
                    if (enfant.classList.contains("disabled-svg")) {
                      enfant.classList.remove("disabled-svg");
                    }
                  }
                }
              }

              elEnfant.children[0].innerHTML = quantiteBouteille;
              break;
            }
        }
    }

    afficherPageModificationBouteille() {
        let id = evt.target.dataset.id;
        console.log(`cellier?action=m&bouteille_id=${id}`);
        window.location.assign(`cellier?action=m&bouteille_id=${id}`);
    }

}