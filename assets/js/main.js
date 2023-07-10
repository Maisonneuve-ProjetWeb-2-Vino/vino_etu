/**
 * @file Script contenant les fonctions de base
 * @author Jonathan Martel (jmartel@cmaisonneuve.qc.ca)
 * @version 0.1
 * @update 2019-01-21
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 *
 */

// Valide les champs nécessaires à la création ou modification des champs du cellier
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

    if(inputNomBouteille){
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

      let bouteille = {
        nom : document.querySelector(".nom_bouteille"),
        quantite : document.querySelector("[name='quantite']"),
        notes : document.querySelector("[name='notes']")
      };


      liste.addEventListener("click", function(evt){
        console.dir(evt.target)
        if(evt.target.tagName == "LI"){
          bouteille.nom.dataset.id = evt.target.dataset.id;
          bouteille.nom.innerHTML = evt.target.innerHTML;
          
          liste.innerHTML = "";
          inputNomBouteille.value = "";

          const erreur_nom = document.querySelector(".erreur_nom_bouteille");
          erreur_nom.innerHTML = "";
        }
      });

      let btnAjouter = document.querySelector("[name='ajouterBouteilleCellier']");
      if(btnAjouter){
        btnAjouter.addEventListener("click", function(evt){

          let validation = validerChampsBouteille(bouteille);

          // Validation supplémentaire dans le cas de l'ajout de bouteille
          if (!bouteille.nom.dataset.id) {
            const erreur_nom = document.querySelector(".erreur_nom_bouteille");
            erreur_nom.innerHTML = "Une bouteille doit être sélectionnée.";
            validation = false;
          }

          if (validation) {
            var param = {
              "id_bouteille":bouteille.nom.dataset.id,
              "quantite":bouteille.quantite.value,
              "id_cellier":document.querySelector("#cellier_id").value
            };
            console.log(param['id_cellier']);
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
              "notes":bouteille.notes.value,
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
                      window.location.assign("accueil");
                    
                    }).catch(error => {
                      console.error(error);
                    });
          

          }

        
        });
      } 
  }
    

});

