/**
 * @file Script contenant les fonctions de base
 * @author Jonathan Martel (jmartel@cmaisonneuve.qc.ca)
 * @version 0.1
 * @update 2019-01-21
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 *
 */

// Valide les champs nécessaires à la création ou modification des champs des bouteilles d'un cellier
function validerChampsBouteille(bouteille) {

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

function validerChampsCellier(cellier) {

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

function changerStatutInterfaceAjout(bouteille, statut) {
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

function remplirChampsAjout(bouteille, details) {
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

function viderChampsAjout(bouteille) {
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

window.addEventListener('load', function() {
    console.log("load");
    document.querySelectorAll(".btnBoire").forEach(function(element){
        console.log(element);
        element.addEventListener("click", function(evt){
            let id = evt.target.parentElement.dataset.id;
            let requete = new Request("cellier?action=b", {method: 'POST', body: '{"id": '+id+'}'});

            fetch(requete)
            .then(response => {
                if (response.status === 200) {
                  return response.json();
                } else {
                  throw new Error('Erreur');
                }
              })
              .then(response => {
                console.debug(response);
                const elBouteille = evt.target.closest(".bouteille");
                for (let enfantBouteille of elBouteille.children) {
                  if (enfantBouteille.classList.contains("description")) {
                    for (let enfantDescription of enfantBouteille.children) {
                      if (enfantDescription.classList.contains("quantite")) {
                        let quantiteBouteille = enfantDescription.children[0].innerHTML;

                        if (quantiteBouteille > 0) {
                          quantiteBouteille -= 1;
                        }
                        
                        if (quantiteBouteille == 0){
                          evt.target.disabled = true;
                        }

                        enfantDescription.children[0].innerHTML = quantiteBouteille;
                        break;
                      }
                    }

                  }
                }

                //window.location.assign("accueil");
                

              }).catch(error => {
                console.error(error);
              });
        })

    });

    document.querySelectorAll(".btnAjouter").forEach(function(element){
        console.log(element);
        element.addEventListener("click", function(evt){
            let id = evt.target.parentElement.dataset.id;
            console.log(id)
            let requete = new Request("cellier?action=a", {method: 'POST', body: '{"id": '+id+'}'});

            fetch(requete)
            .then(response => {
                if (response.status === 200) {
                  return response.json();
                } else {
                  throw new Error('Erreur');
                }
              })
              .then(response => {
                console.debug(response);
                const elBouteille = evt.target.closest(".bouteille");
                for (let enfantBouteille of elBouteille.children) {
                  if (enfantBouteille.classList.contains("description")) {
                    for (let enfantDescription of enfantBouteille.children) {
                      if (enfantDescription.classList.contains("quantite")) {
                        let quantiteBouteille = parseInt(enfantDescription.children[0].innerHTML);
                        quantiteBouteille += 1;
                        
                        if (quantiteBouteille > 0){
                          const elOptions = evt.target.closest(".options");
                          for (let enfantOptions of elOptions.children) {
                            if (enfantOptions.classList.contains("btnBoire")) {
                              enfantOptions.disabled = false;
                            }
                          }
                        }

                        enfantDescription.children[0].innerHTML = quantiteBouteille;
                        break;
                      }
                    }

                  }
                }
              }).catch(error => {
                console.error(error);
              });
        })

    });
   
    document.querySelectorAll(".btnModifier").forEach(function(element){
        console.log(element);
        element.addEventListener("click", function(evt){
            let id = evt.target.parentElement.dataset.id;
            console.log(`cellier?action=m&bouteille_id=${id}`);
            window.location.assign(`cellier?action=m&bouteille_id=${id}`);
        });
    });


    let inputNomBouteille = document.querySelector("[name='nom_bouteille']");
    console.log(inputNomBouteille);
    let liste = document.querySelector('.listeAutoComplete');

    // Si on est sur la page d'ajout de bouteille
    if(inputNomBouteille){

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

      changerStatutInterfaceAjout(bouteille, true);
      inputNomBouteille.addEventListener("keyup", function(evt){
        console.log(evt);
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


      // Écouteur sur la liste des résultats de recherche d'une bouteille
      liste.addEventListener("click", function(evt){
        console.dir(evt.target)
        if(evt.target.tagName == "LI"){
          bouteille.nom.dataset.id = evt.target.dataset.id;
          console.log(evt.target.innerHTML)
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
                      remplirChampsAjout(bouteille, response);
                    
                    }).catch(error => {
                      console.error(error);
                    });
        }
      });

      // Ajouter une nouvelle bouteille au cellier
      let btnAjouter = document.querySelector("[name='ajouterBouteilleCellier']");
      if(btnAjouter){
        btnAjouter.addEventListener("click", function(evt){

          let validation = validerChampsBouteille(bouteille);

          // Validation supplémentaire dans le cas de l'ajout de bouteille
          if (!bouteille.nom.value) {
            const erreur_nom = document.querySelector(".erreur_nom_bouteille");
            erreur_nom.innerHTML = "Une bouteille doit être sélectionnée ou un nom entré.";
            validation = false;
          }

          if (validation) {

            // Si le vin provient de la saq
            if(bouteille.nom.dataset.id) {
              var param = {
                "id_bouteille":bouteille.nom.dataset.id,
                "quantite":bouteille.quantite.value,
                "id_cellier":document.querySelector("#cellier_id").value
              };
              let requete = new Request("cellier?action=n", {method: 'POST', body: JSON.stringify(param)});
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
            } else {
              // Vin personnalisé
              var param = {
                "nom":bouteille.nom.value,
                "quantite":bouteille.quantite.value,
                "id_cellier":document.querySelector("#cellier_id").value,
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
              let requete = new Request("cellier?action=e", {method: 'POST', body: JSON.stringify(param)});
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

          }


        
        });
      }

      let btnModifier = document.querySelector("[name='modifierBouteilleCellier']");
      if(btnModifier){
        btnModifier.addEventListener("click", function(evt){

          if (validerChampsBouteille(bouteille)) {
            let param = {
              "id_bouteille_cellier":document.querySelector("#bouteille_id").dataset.idCellier,
			        "id_bouteille":bouteille.nom.dataset.id,
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

      let btnEntrerBouteillePersonnalisee = document.querySelector("[name='entrerBouteillePersonnalisee']");
      console.log(btnEntrerBouteillePersonnalisee)
      if(btnEntrerBouteillePersonnalisee) {
        btnEntrerBouteillePersonnalisee.addEventListener("click", function(evt){
           changerStatutInterfaceAjout(bouteille, false);
           viderChampsAjout(bouteille);
           bouteille.nom.dataset.id = "";
           const erreur_nom = document.querySelector(".erreur_nom_bouteille");
           erreur_nom.innerHTML = "";
        });
      }

    } // Fin page ajout bouteille

    
    let cellier = {
      nom : document.querySelector(".nom_cellier")
    }

    let btnModifierCellier = document.querySelector("[name='modifierCellier']");
    console.log(btnModifierCellier)
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

