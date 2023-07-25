
let touchEvent = 'ontouchstart' in window ? 'touchstart' : 'click';

document.querySelectorAll('.confirmer').forEach(e => e.addEventListener(touchEvent, afficherFenetreModale));

/**
 * Affichage d'une fenêtre modale
 */
function afficherFenetreModale() {
  let locationHref = () => {location.href = this.dataset.href};
  let annuler      = () => {document.getElementById('modaleSuppression').close()}; 
  document.querySelector('#modaleSuppression .OK').onclick = locationHref;
  document.querySelector('#modaleSuppression .KO').onclick = annuler;
  document.getElementById('modaleSuppression').showModal();
  document.querySelector('#modaleSuppression .focus').focus();
}