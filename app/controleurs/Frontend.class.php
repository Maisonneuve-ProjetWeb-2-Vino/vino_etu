<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend.
 */

class Frontend extends Routeur {

  /**
   * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
   * 
   */
  public function __construct() {
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Afficher la page d'accueil.
   * 
   * @return void
   */  
  public function voirAccueil() {

    $bouteilles = $this->oRequetesSQL->obtenirListeBouteilleCellier();

    new Vue("/Frontend/vAccueil",
      array(
        'titre'     => "Un petit verre de vino",
        'bouteilles'  => $bouteilles
      ),
      "/Frontend/gabarit-frontend");
  }

}