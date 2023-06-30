<?php 

/**
 * Fonction qui s'exécute dès qu'une classe est manipulée pour la première fois par l'application
 * @param string $classe, nom de la classe avec son namespace éventuel 
 *
 */
function chargerClasse($classe) {

  $dossiers = array('modeles/sql/', 'modeles/entites/', 'vues/', 'controleurs/'); 

  foreach ($dossiers as $dossier) {
    $fichier = './app/'.$dossier.$classe.'.class.php';
    if (file_exists($fichier)) {
      require $fichier;
    }
  }

}

spl_autoload_register('chargerClasse');