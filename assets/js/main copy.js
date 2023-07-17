/**
 * @file Script contenant les fonctions de base
 * @author Jonathan Martel (jmartel@cmaisonneuve.qc.ca)
 * @version 0.1
 * @update 2019-01-21
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 *
 */






window.addEventListener('load', function() {
    console.log("load");

   

    // Si on est sur la page de modification de bouteille
    let modificationBouteille = document.querySelector(".modificationBouteille");
    if(modificationBouteille){

      let bouteille = {
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

      // Modification d'un vin de la SAQ
      if (inputNomBouteille) {
        inputNomBouteille.addEventListener("keyup", function(evt){

          let nom = inputNomBouteille.value;
          liste.innerHTML = "";
          if(nom){
            let requete = new Request("cellier?action=c", {method: 'POST', body: '{"nom": "'+nom+'"}'});
            fetch(requete)
                .then(response => {
                    if (response.status === 200) {
                      return response.json();
                    } else {
                      throw new Error('Erreur');
                    }
                  })
                  .then(response => {
                    console.log(response);
                    
                    response.forEach(function(element){
                      liste.innerHTML += "<li data-id='"+element.id +"'>"+element.nom+"</li>";
                    })
                  }).catch(error => {
                    console.error(error);
                  });
          }
        
        });
      }


      // Écouteur sur la liste des résultats de recherche d'une bouteille
      if (liste) {
        liste.addEventListener("click", function(evt){
          console.dir(evt.target)
          if(evt.target.tagName == "LI"){
            bouteille.nom.dataset.id = evt.target.dataset.id;
            console.log(evt.target.innerHTML)
            console.log(bouteille.nom)
            bouteille.nom.innerHTML = evt.target.innerHTML;

            liste.innerHTML = "";
            inputNomBouteille.value = "";

            let param = {
              "id_bouteille":bouteille.nom.dataset.id,
            }

            let requete = new Request("cellier?action=r", {method: 'POST', body: JSON.stringify(param)});
                fetch(requete)
                    .then(response => {
                        if (response.status === 200) {
                          return response.json();
                        } else {
                          throw new Error('Erreur');
                        }
                      })
                      .then(response => {
                        console.log(response)
                        //remplirChampsAjout(bouteille, response);
                      
                      }).catch(error => {
                        console.error(error);
                      });
          }
        });
      }


      let btnModifier = document.querySelector("[name='modifierBouteilleCellier']");
      if(btnModifier){
        btnModifier.addEventListener("click", function(evt){

          if (validerChampsBouteille(bouteille)) {
            let param = {
              "id_bouteille_cellier":document.querySelector("#bouteille_id").dataset.idBouteilleCellier,
			        "id_bouteille_catalogue":bouteille.nom.dataset.id,
              "quantite":parseInt(bouteille.quantite.value),
            };
            console.log(param);
            let requete = new Request("cellier?action=m", {method: 'POST', body: JSON.stringify(param)});
              fetch(requete)
                  .then(response => {
                      if (response.status === 200) {
                        return response.json();
                      } else {
                        throw new Error('Erreur');
                      }
                    })
                    .then(response => {
                      console.log(response);
                      window.location.assign("cellier");
                    
                    }).catch(error => {
                      console.error(error);
                    });
          

          }

        
        });
      }

      let btnModifierPersonnalisee = document.querySelector("[name='modifierBouteillePersonnalisee']");
      if(btnModifierPersonnalisee){
        btnModifierPersonnalisee.addEventListener("click", function(evt){

          if (validerChampsBouteille(bouteille)) {
            let param = {
              "nom":document.querySelector(".nom_bouteille").value,
              "id_bouteille_cellier":document.querySelector("#bouteille_id").dataset.idBouteilleCellier,
			        "id_bouteille_catalogue":bouteille.nom.dataset.id,
              "quantite":parseInt(bouteille.quantite.value),
              "pays":bouteille.pays.value,
              "type":bouteille.type.value,
              "annee":bouteille.millesime.value,
              "format":bouteille.format.value,
              "appellation":bouteille.appellation.value,
              "cepage":bouteille.cepage.value,
              "particularite":bouteille.particularite.value,
              "degreAlcool":bouteille.degreAlcool.value,
              "origine":bouteille.origine.value,
              "producteur":bouteille.producteur.value,
              "prix_saq":bouteille.prix.value,
              "region":bouteille.region.value,
              "tauxSucre":bouteille.sucre.value,
            };
            console.log(param);
            let requete = new Request("cellier?action=m", {method: 'POST', body: JSON.stringify(param)});
              fetch(requete)
                  .then(response => {
                      if (response.status === 200) {
                        return response.json();
                      } else {
                        throw new Error('Erreur');
                      }
                    })
                    .then(response => {
                      console.log(response);
                      window.location.assign("cellier");
                    
                    }).catch(error => {
                      console.error(error);
                    });
          

          }

        
        });
      }

    }

    let cellier = {
      nom : document.querySelector(".nom_cellier")
    }

    // Page de modification de cellier
    let btnModifierCellier = document.querySelector("[name='modifierCellier']");
    if(btnModifierCellier){
      btnModifierCellier.addEventListener("click", function(evt){

        if (validerChampsCellier(cellier)) {
          let param = {
            "cellier_id":document.querySelector("#cellier_id").value,
            "nom": cellier.nom.value,
          };
          console.log(param);
          let requete = new Request("cellier?action=q", {method: 'POST', body: JSON.stringify(param)});
            fetch(requete)
              .then(response => {
                  if (response.status === 200) {
                    return response.json();
                  } else {
                    throw new Error('Erreur');
                  }
                })
                .then(response => {
                  console.log(response);
                  window.location.assign("cellier");
                
                }).catch(error => {
                  console.error(error);
                });
        
        }

      });
    } 
    

});

