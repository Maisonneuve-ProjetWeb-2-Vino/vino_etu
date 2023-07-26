import Affichage from "./Affichage.js";
import Fetch from "./Fetch.js";

export default class Cellier {

    #elBoireBouteille;
    #elAjouterBouteille;
    #elModifierBouteille;
    #elInputNomBouteille;
    #elBtnEntrerBouteillePersonnalisee;
    #listeResultats;
    #elCible;
    #elParent;
    #bouteille;
    #cellier;
    #elNouvelleBouteille;
    #modificationBouteille;
    #elBtnModifier;
    #elBtnModifierPersonnalisee;
    #elBtnModifierCellier;
    #celliers;
    #cellierPreferentiel;
    #idBouteilleChoisie;

    /**
     * Constructeur de la classe Cellier
     */
    constructor() {

        // Sur la page Liste des bouteilles
        this.#elBoireBouteille = document.querySelectorAll(".btnBoire");
        this.#elAjouterBouteille = document.querySelectorAll(".btnAjouter");
        this.#elModifierBouteille = document.querySelectorAll(".btnModifier");

        // Sur la page d'Ajout de bouteille
        this.#elInputNomBouteille = document.querySelector("[name='nom_bouteille']");
        this.#listeResultats = document.querySelector('#liste_resultats');
        this.#elNouvelleBouteille = document.querySelector(".nouvelleBouteille");
        this.#elBtnEntrerBouteillePersonnalisee = document.querySelector("[name='entrerBouteillePersonnalisee']");

        // Sur la page de Modification de bouteille de la SAQ
        this.#modificationBouteille = document.querySelector(".modificationBouteille");
        this.#elBtnModifier = document.querySelector("[name='modifierBouteilleCellier']");

        // Sur la page de modification de bouteille personnalisée
        this.#elBtnModifierPersonnalisee = document.querySelector("[name='modifierBouteillePersonnalisee']");
        
        // Sur la page de modification de cellier
        this.#elBtnModifierCellier = document.querySelector("[name='modifierCellier']");
    
        this.initialiser();
    }

    /**
     * Initialisation des écouteurs d'événements sur les différentes pages.
     */
    initialiser() {
        // Lier les écouteurs d'événements aux boutons
        this.#elBoireBouteille.forEach(function(element){element.addEventListener("click",this.boireBouteille.bind(this))}, this);
        this.#elAjouterBouteille.forEach(function(element){element.addEventListener("click",this.ajouterBouteille.bind(this))}, this);
        this.#elModifierBouteille.forEach(function(element){element.addEventListener("click",this.afficherPageModificationBouteille.bind(this))}, this);

        // Utilisé pour les requêtes de gestion de bouteilles
        this.#bouteille = {
            nom : document.querySelector(".nom_bouteille"),
            quantite : document.querySelector("[name='quantite']"),
            pays : document.querySelector("[name='pays']"),
            type : document.querySelector("[name='type']"),
            millesime : document.querySelector("[name='millesime']"),
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

        // Utilisé pour les requêtes de gestion de celliers
        this.#cellier = {
            nom : document.querySelector(".nom_cellier")
        }

        // Si on est sur la page d'ajout de bouteille
        if (this.#elNouvelleBouteille) {

            // Récupérer les noms des celliers et leurs ids
            const oFetch = new Fetch();
            oFetch.obtenirCelliers([], this.enregistrerCelliers.bind(this));

            // Enregistrer le cellier préférentiel
            this.#cellierPreferentiel = document.querySelector("#cellier_id").value;

            // Ajout des écouteurs sur les boutons
            this.#elInputNomBouteille.addEventListener("keyup", this.rechercherBouteille.bind(this));
        }

        // Si on est sur la page de modification de bouteille
        if (this.#modificationBouteille) {
            this.#elInputNomBouteille.addEventListener("keyup", this.rechercherBouteille.bind(this));
            //this.#liste.addEventListener("click", this.selectionnerBouteilleModification.bind(this));
            this.#elBtnModifier.addEventListener("click", this.modifierBouteille.bind(this));
        }

        // Si on est sur la page de modification de bouteille personnalisée
        if (this.#elBtnModifierPersonnalisee) {
            this.#elBtnModifierPersonnalisee.addEventListener("click", this.modifierBouteillePersonnalisee.bind(this));
        }

        // Si on est sur la page de modification de cellier
        if (this.#elBtnModifierCellier) {
            this.#elBtnModifierCellier.addEventListener("click", this.validerCellier.bind(this));
        }
    }

    /**
     * Fonction de rappel pour obtenir la liste des celliers.
     */
    enregistrerCelliers(celliers) {
        this.#celliers = celliers;
    }

    /**
     * Valider les champs pour la modification de cellier.
     * @param {Event} evt
     */
    validerCellier(evt) {
        if (this.validerChampsCellier(this.#cellier)) {

            let param = {
                "nom": this.#cellier.nom.value,
            };

            // Validation pour éviter la duplication du nom du cellier
            const oFetch = new Fetch();
            oFetch.validerNomCellier(param, this.modifierCellier.bind(this));
        }
    }
    /**
     * 
     * Vérifier le statut de la requête de vérification du nom de cellier et 
     * faire la requête de modification.
     * @param {Object} reponse
     */
    modifierCellier(reponse) {
        // Si le nom du cellier n'existe pas déjà
        if (!reponse.statut) {
            let param = {
                "cellier_id":document.querySelector("#cellier_id").value,
                "nom": this.#cellier.nom.value,
            };
            const oFetch = new Fetch();
            oFetch.modifierCellier(param, this.redirigerPageCellier.bind(this));
        } else 
        {
            document.querySelector(".erreur_nom").innerHTML = "Le nom du cellier existe déjà.";
        }

    }

    /**
     * Valider les champs pour la modification de bouteille personnalisée et faire la requête de
     * modification.
     * @param {Event} evt 
     */
    modifierBouteillePersonnalisee(evt) {
        if (this.validerChampsBouteille()) {
            let param = {
                "nom":document.querySelector(".nom_bouteille").value,
                "id_bouteille_cellier":document.querySelector("#bouteille_id").dataset.idBouteilleCellier,
                "id_bouteille_catalogue":this.#bouteille.nom.dataset.id,
                "quantite":parseInt(this.#bouteille.quantite.value),
                "pays":this.#bouteille.pays.value,
                "type":this.#bouteille.type.value,
                "annee":this.#bouteille.millesime.value,
                "format":this.#bouteille.format.value,
                "appellation":this.#bouteille.appellation.value,
                "cepage":this.#bouteille.cepage.value,
                "particularite":this.#bouteille.particularite.value,
                "degreAlcool":this.#bouteille.degreAlcool.value,
                "origine":this.#bouteille.origine.value,
                "producteur":this.#bouteille.producteur.value,
                "prix_saq":this.#bouteille.prix.value,
                "region":this.#bouteille.region.value,
                "tauxSucre":this.#bouteille.sucre.value,
            };
            const oFetch = new Fetch();
            oFetch.modifierBouteille(param, this.redirigerPageCellier.bind(this));
        }
    }

    /**
     * Valider les champs pour la requête de modification d'un vin SAQ et faire la requête de modification.
     * @param {Event} evt 
     */
    modifierBouteille(evt) {
        if (this.validerChampsBouteille()) {
            let param = {
                "id_bouteille_cellier":document.querySelector("#bouteille_id").dataset.idBouteilleCellier,
                "id_bouteille_catalogue":this.#bouteille.nom.dataset.id,
                "quantite":parseInt(this.#bouteille.quantite.value),
            };
            console.log(param);
            const oFetch = new Fetch();
            oFetch.modifierBouteille(param, this.redirigerPageCellier.bind(this));
        }
    }

    /**
     * Active et vide les champs détaillés pour l'ajout d'un vin personnalisé.
     * @param {Event} evt 
     */
    preparerChampsDetails(evt) {
        this.changerStatutInterfaceAjout(false);
        this.viderChampsAjout();
        this.#bouteille.nom.dataset.id = "";
        const erreur_nom = document.querySelector(".erreur_nom_bouteille");
        erreur_nom.innerHTML = "";
        this.montrerDetailsAjoutBouteille();
        document.querySelector("#nom").focus();
    }

    /**
     * Valide les champs pour l'ajout d'une bouteille et vérifie si une bouteille existe 
     * déjà dans le cellier, puis fait la requête d'ajout s'il n'y a pas d'erreurs.
     * @param {Event} evt 
     */
    verifierNouvelleBouteille(evt) {
        let validation = this.validerChampsBouteille();

        const erreur_nom = document.querySelector(".erreur_nom_bouteille");

        // Validation supplémentaire dans le cas de l'ajout de bouteille
        if (!this.#bouteille.nom.value) {
            erreur_nom.innerHTML = "Une bouteille doit être sélectionnée ou un nom entré.";
            validation = false;
        }
        else {
            erreur_nom.innerHTML = "";
        }

        if (validation) { // Si le vin provient du catalogue

            if(this.#bouteille.nom.dataset.id) {

              var param = {
                "id_bouteille":this.#bouteille.nom.dataset.id,
                "quantite":this.#bouteille.quantite.value,
                "id_cellier":document.querySelector("#cellier_id").value
              };

              const oFetch = new Fetch();
              oFetch.verifierDuplicationBouteille(param, this.ajouterNouvelleBouteille.bind(this));

            } else { // Vin personnalisé
             
              var param = {
                "nom":this.#bouteille.nom.value,
                "quantite":this.#bouteille.quantite.value,
                "id_cellier":document.querySelector("#cellier_id").value,
                "pays":this.#bouteille.pays.value,
                "type":this.#bouteille.type.value,
                "annee":this.#bouteille.millesime.value,
                "format":this.#bouteille.format.value,
                "appellation":this.#bouteille.appellation.value,
                "cepage":this.#bouteille.cepage.value,
                "particularite":this.#bouteille.particularite.value,
                "degreAlcool":this.#bouteille.degreAlcool.value,
                "origine":this.#bouteille.origine.value,
                "producteur":this.#bouteille.producteur.value,
                "prix_saq":this.#bouteille.prix.value,
                "region":this.#bouteille.region.value,
                "tauxSucre":this.#bouteille.sucre.value,
              };
              
              const oFetch = new Fetch();
              oFetch.ajouterBouteillePersonnalisee(param, this.redirigerPageCellier.bind(this))
            }

        }
    }
    
    /**
     * Fonction de rappel de la vérification de duplicat de bouteille. Ajoute une nouvelle
     * bouteille s'il n'y a pas de duplication, et un message d'erreur sinon.
     * @param {Object} reponse Le statut d'erreur
     */
    ajouterNouvelleBouteille(reponse) {

        // Si la bouteille ne se trouve pas déjà dans le cellier
        if (!reponse.statut) {
            const param = {
                "id_bouteille":this.#idBouteilleChoisie,
                "quantite":document.querySelector("#quantite_modale").innerText,
                "id_cellier":document.querySelector("#celliers").value
            };
            
            const oFetch = new Fetch();
            oFetch.ajouterNouvelleBouteille(param, this.redirigerPageCellier.bind(this))
        } else 
        {
            const erreur_nom = document.querySelector(".erreur_nom_bouteille");
            erreur_nom.innerHTML = "La bouteille se trouve déjà dans le cellier.";
        }

    }

    /**
     * Redirige vers la page des celliers.
     */
    redirigerPageCellier() {
        window.location.assign("cellier");
    }

    /**
     * Sélectionne une bouteille avec ses détails lors de l'opération d'ajout de bouteille.
     * @param {Event} evt 
     */
    selectionnerBouteilleAjout(evt) {
                
        // Enregistre l'id de la bouteille pour utilisation dans la modale
        this.#idBouteilleChoisie = evt.target.dataset.id;
         
        const param = {
            "id_bouteille": this.#idBouteilleChoisie
        }

        const oFetch = new Fetch();
        oFetch.obtenirDetailsBouteille(param, this.ouvrirModaleAjout.bind(this));
    }

    /**
     * Rempli la modale avec les noms de la bouteille et des celliers, et ouvre la modale.
     * @param {Object} details 
     */
    ouvrirModaleAjout(details) {

        // Réinitialiser les champs de la modale
        document.querySelector(".modale_nom_bouteille").innerHTML = details.nom;
        document.querySelector(".erreur_nom_bouteille").innerText = "";
        document.querySelector("#quantite_modale").innerText = 1;

        // Afficher tous les celliers de l'utilisateur
        const gabaritCelliers = document.querySelector("#tmpl_celliers").innerHTML;
        const domParent = document.querySelector("#celliers");
        Affichage.afficher(this.#celliers, gabaritCelliers, domParent);

        // Sélectionne le cellier préférentiel
        document.querySelector("#celliers").value = this.#cellierPreferentiel;

        // Attache les écouteurs sur les boutons de la modale
        const elModaleAjout = document.getElementById('modaleAjout');
        document.querySelector(".fermer_modale_ajout").addEventListener("click", () => {elModaleAjout.close()});
        document.querySelector(".btn_ajouter_modale").addEventListener("click", this.verifierDuplicationBouteille.bind(this));
        document.querySelector(".btnAjouter_modale").addEventListener("click", this.augmenterQuantiteAjout.bind(this));
        document.querySelector(".btnBoire_modale").addEventListener("click", this.diminuerQuantiteAjout.bind(this));

        elModaleAjout.showModal();
    }

    /**
     * Augmente la quantité de 1 avant d'ajouter une bouteille au cellier.
     * @param {Event} evt 
     */
    augmenterQuantiteAjout(evt) {
        
        let elQuantite = document.querySelector("#quantite_modale");
        const elBtnBoire = document.querySelector(".btnBoire_modale");

        let quantiteBouteille = parseInt(elQuantite.innerHTML);
        quantiteBouteille += 1;
 
        if (quantiteBouteille > 0){
            elBtnBoire.disabled = false;
            if (elBtnBoire.classList.contains("disabled-svg")) {
                elBtnBoire.classList.remove("disabled-svg");
            }
        }

        elQuantite.innerText = quantiteBouteille;
    }

    /**
     * Diminue la quantité de 1 avant d'ajouter une bouteille au cellier.
     * @param {Event} evt 
     */
    diminuerQuantiteAjout(evt) {

        const elQuantite = document.querySelector("#quantite_modale");
        const elBtnBoire = document.querySelector(".btnBoire_modale");

        let quantiteBouteille = elQuantite.innerHTML;

        if (quantiteBouteille > 0) {
            quantiteBouteille -= 1;
        }
                
        if (quantiteBouteille == 0){
            elBtnBoire.disabled = true;
            elBtnBoire.classList.add("disabled-svg");
        }

        elQuantite.innerHTML = quantiteBouteille;
    }


    /**
     * Vérifie qu'une bouteille n'est pas déjà présente dans un cellier.
     */
    verifierDuplicationBouteille() {

        var param = {
            "id_bouteille": this.#idBouteilleChoisie,
            "id_cellier": document.querySelector("#celliers").value
        };
        const oFetch = new Fetch();
        oFetch.verifierDuplicationBouteille(param, this.ajouterNouvelleBouteille.bind(this));
    }
    
    /**
     * Sélectionne une bouteille avec ses détails lors de l'opération de modification de bouteille.
     * @param {Event} evt 
     */
    selectionnerBouteilleModification(evt) {
        if(evt.target.tagName == "LI") {
            this.selectionnerBouteille(evt);

            const param = {
                "id_bouteille":bouteille.nom.dataset.id,
            }
            const oFetch = new Fetch();
            oFetch.obtenirDetailsBouteille(param, this.afficheConsole.bind(this));
        }
    }

    /**
     * Pour afficher en console le résultat d'un Fetch.
     * @param {Object} resultat 
     */
    afficheConsole(resultat) {
        console.log(resultat);
    }

    /**
     * Obtient l'id de la bouteille lors d'une sélection par l'usager.
     * @param {Event} evt 
     */
    selectionnerBouteille(evt) {
        this.#bouteille.nom.dataset.id = evt.target.dataset.id;
        this.#bouteille.nom.value = evt.target.innerHTML;
        
        //this.#liste.innerHTML = "";
        this.#elInputNomBouteille.value = "";

        const erreur_nom = document.querySelector(".erreur_nom_bouteille");

        if (erreur_nom) {
            erreur_nom.innerHTML = "";
        }
    }

    /**
     * Obtient le nom de la bouteille et le recherche dans le catalogue.
     * @param {Event} evt 
     */
    rechercherBouteille(evt) {
        let nom = this.#elInputNomBouteille.value;
        
        if(nom){
            const param = {
                "nom":nom
            }
            const oFetch = new Fetch();
            oFetch.rechercherBouteille(param, this.afficherResultatsRecherche.bind(this));
        }
    }

    /**
     * Insère les résultats de recherche dans la liste des résultats proposés à l'usager pour la sélection.
     * @param {Array} listeResultats 
     */
    afficherResultatsRecherche(resultats) {

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

        const domParent = this.#listeResultats;
        const gabaritResultats = document.querySelector("#tmpl_resultats").innerHTML;
        Affichage.afficher(resultats, gabaritResultats, domParent);

        document.querySelectorAll(".ajouter_cellier").forEach(function(element){element.addEventListener("click",this.selectionnerBouteilleAjout.bind(this))}, this);
    }

    /**
     * Obtient l'id de la bouteille bue et fait la requête de diminution de quantité.
     * @param {Event} evt 
     */
    boireBouteille(evt) {

        // Aller chercher l'id de la bouteille du cellier
        let id = evt.target.closest(".options").dataset.id;
        this.#elCible = evt.currentTarget;
        this.#elParent = evt.currentTarget.parentElement;

        // Faire la requête et ajuster la quantité
        const param = {
            "id":parseInt(id)
        };
        const oFetch = new Fetch();
        oFetch.boireBouteille(param, this.diminuerQuantiteBouteille.bind(this));
    }

    /**
     * Obtient l'id de la bouteille à ajouter et fait la requête d'augmentation de quantité.
     * @param {Event} evt 
     */
    ajouterBouteille(evt) {
        // Aller chercher l'id de la bouteille du cellier
        let id = evt.target.closest(".options").dataset.id;
        this.#elCible = evt.currentTarget;
        this.#elParent = evt.currentTarget.parentElement;

        // Faire la requête et ajuster la quantité
        const param = {
            "id":parseInt(id)
        };
        const oFetch = new Fetch();
        oFetch.ajouterBouteille(param, this.augmenterQuantiteBouteille.bind(this));
    }


    // Valide les champs nécessaires à la création ou modification des champs des bouteilles d'un cellier
    validerChampsBouteille() {

        let validation = true;

        // On enlève les erreur par défaut
        document.querySelector(".erreur_quantite").innerHTML = "";
        document.querySelector(".erreur_prix").innerHTML = "";
        document.querySelector(".erreur_type").innerHTML = "";

        // Validation de la quantité
        if (this.#bouteille.quantite) {
            if (this.#bouteille.quantite.value){
                if(isNaN(this.#bouteille.quantite.value) || (this.#bouteille.quantite.value < 0)) {
                    document.querySelector(".erreur_quantite").innerHTML = "La quantité doit être un nombre entier supérieur ou égal à 0.";
                    validation = false;
                }
            }
        }

        // Validation du prix
        if(this.#bouteille.prix) {
            if (this.#bouteille.prix.value) {
                if(isNaN(this.#bouteille.prix.value) || (this.#bouteille.prix.value < 0)) {
                    document.querySelector(".erreur_prix").innerHTML = "Le prix doit être un nombre supérieur ou égal à 0.";
                    validation = false;
                }
            }
        }

        //Validation du type
        if (this.#bouteille.type) {
            if (!this.#bouteille.type.value) {
                document.querySelector(".erreur_type").innerHTML = "Veuillez choisir un type de vin.";
                validation = false;
            }
        }
        return validation;
    }

    /**
     * Valide les champs lors de l'ajout ou modification de cellier.
     * @param {Object} cellier 
     * @returns 
     */
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

    /**
     * Diminue la quantité de bouteille de 1 pour une bouteille donnée.
     */
    diminuerQuantiteBouteille() {
        for (let elEnfant of this.#elParent.children) {
            if (elEnfant.classList.contains("quantite")) {
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

    /**
     * Augmente la quantité de bouteille de 1 pour une bouteille donnée.
     */
    augmenterQuantiteBouteille() {

        for (let elEnfant of this.#elParent.children) {
            if (elEnfant.classList.contains("quantite")) {
              let quantiteBouteille = parseInt(elEnfant.children[0].innerHTML);
              quantiteBouteille += 1;

              if (quantiteBouteille > 0){
                const elIcones = this.#elCible.closest(".icones_gauche");
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

    /**
     * Affiche la page de modification de bouteille.
     * @param {Event} evt 
     */
    afficherPageModificationBouteille(evt) {
        let id = evt.target.dataset.id;
        console.log(`cellier?action=m&bouteille_id=${id}`);
        window.location.assign(`cellier?action=m&bouteille_id=${id}`);
    }

}
