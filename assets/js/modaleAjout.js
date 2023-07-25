


let touchEvent = 'ontouchstart' in window ? 'touchstart' : 'click';

document.querySelectorAll('.btn_ajout_modale').forEach(e => e.addEventListener(touchEvent, afficherFenetreModale));

/**
 * Affichage d'une fenêtre modale
 */
function afficherFenetreModale() {
  let annuler      = () => {document.getElementById('modaleAjout').close()}; 
  document.querySelector('#modaleAjout .fermer_modale_ajout').onclick = annuler;
  document.getElementById('modaleAjout').showModal();
//   document.querySelector('#modaleAjout .focus').focus();
}