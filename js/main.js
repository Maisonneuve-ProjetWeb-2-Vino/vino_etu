/**
 * @file Script contenant les fonctions de base
 * @author Jonathan Martel (jmartel@cmaisonneuve.qc.ca)
 * @version 0.1
 * @update 2019-01-21
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 *
 */

//const BaseURL = "https://jmartel.webdev.cmaisonneuve.qc.ca/n61/vino/";
const BaseURL = document.baseURI;
console.log(BaseURL);
window.addEventListener('load', function() {
    console.log("load");
    document.querySelectorAll(".btnBoire").forEach(function(element){
        console.log(element);
        element.addEventListener("click", function(evt){
            let id = evt.target.parentElement.dataset.id;
            let requete = new Request(BaseURL+"index.php?requete=boireBouteilleCellier", {method: 'POST', body: '{"id": '+id+'}'});

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
              }).catch(error => {
                console.error(error);
              });
        })

    });

    document.querySelectorAll(".btnAjouter").forEach(function(element){
        console.log(element);
        element.addEventListener("click", function(evt){
            let id = evt.target.parentElement.dataset.id;
            let requete = new Request(BaseURL+"index.php?requete=ajouterBouteilleCellier", {method: 'POST', body: '{"id": '+id+'}'});

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
              }).catch(error => {
                console.error(error);
              });
        })

    });
   
    document.querySelectorAll(".btnModifier").forEach(function(element){
        console.log(element);
        element.addEventListener("click", function(evt){
            let id = evt.target.parentElement.dataset.id;
            console.log(BaseURL+`index.php?requete=modifierBouteilleCellier&bouteille_id=${id}`);
            window.location.assign(BaseURL+`index.php?requete=modifierBouteilleCellier&bouteille_id=${id}`);
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
          let requete = new Request(BaseURL+"index.php?requete=autocompleteBouteille", {method: 'POST', body: '{"nom": "'+nom+'"}'});
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
        millesime : document.querySelector("[name='millesime']"),
        quantite : document.querySelector("[name='quantite']"),
        date_achat : document.querySelector("[name='date_achat']"),
        prix : document.querySelector("[name='prix']"),
        garde_jusqua : document.querySelector("[name='garde_jusqua']"),
        notes : document.querySelector("[name='notes']"),
      };


      liste.addEventListener("click", function(evt){
        console.dir(evt.target)
        if(evt.target.tagName == "LI"){
          bouteille.nom.dataset.id = evt.target.dataset.id;
          bouteille.nom.innerHTML = evt.target.innerHTML;
          
          liste.innerHTML = "";
          inputNomBouteille.value = "";

        }
      });

      let btnAjouter = document.querySelector("[name='ajouterBouteilleCellier']");
      if(btnAjouter){
        btnAjouter.addEventListener("click", function(evt){
          var param = {
            "id_bouteille":bouteille.nom.dataset.id,
            "date_achat":bouteille.date_achat.value,
            "garde_jusqua":bouteille.garde_jusqua.value,
            "notes":bouteille.notes.value,
            "prix":bouteille.prix.value,
            "quantite":bouteille.quantite.value,
            "millesime":bouteille.millesime.value,
          };
          console.log(param['id_bouteille']);
          let requete = new Request(BaseURL+"index.php?requete=ajouterNouvelleBouteilleCellier", {method: 'POST', body: JSON.stringify(param)});
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
                  
                  }).catch(error => {
                    console.error(error);
                  });
        
        });
      }

      let btnModifier = document.querySelector("[name='modifierBouteilleCellier']");
      if(btnModifier){
        btnModifier.addEventListener("click", function(evt){

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
          
          // Validation du prix
          if (bouteille.prix.value){
            if(isNaN(bouteille.prix.value)) {
              document.querySelector(".erreur_prix").innerHTML = "Le prix doit être un nombre.";
              validation = false;
            }
            else {
              document.querySelector(".erreur_prix").innerHTML = "";
            }
          }

          // Validation du millésime
          if (bouteille.millesime.value) {
            if(isNaN(bouteille.millesime.value) || bouteille.millesime.value > new Date().getFullYear()) {
              document.querySelector(".erreur_millesime").innerHTML = "Le millésime doit être une année inférieure ou égale à l'année courante.";
              validation = false;
            }
            else {
              document.querySelector(".erreur_millesime").innerHTML = "";
            }
          }

          if (validation) {
            let param = {
              "id_bouteille_cellier":document.querySelector("#bouteille_id").dataset.idCellier,
			        "id_bouteille":bouteille.nom.dataset.id,
              "date_achat":bouteille.date_achat.value,
              "garde_jusqua":bouteille.garde_jusqua.value,
              "notes":bouteille.notes.value,
              "prix":bouteille.prix.value,
              "quantite":parseInt(bouteille.quantite.value),
              "millesime":bouteille.millesime.value,
            };
            console.log(param);
            let requete = new Request(BaseURL+"index.php?requete=modifierBouteilleCellier", {method: 'POST', body: JSON.stringify(param)});
              fetch(requete)
                  .then(response => {
                      if (response.status === 200) {
                        return response.json();
                      } else {
                        throw new Error('Erreur');
                      }
                    })
                    .then(response => {
                      window.location.assign(BaseURL+"index.php?requete=accueil");
                    
                    }).catch(error => {
                      console.error(error);
                    });
          

          }

        
        });
      } 
  }
    

});

