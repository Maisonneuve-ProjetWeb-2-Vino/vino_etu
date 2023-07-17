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

