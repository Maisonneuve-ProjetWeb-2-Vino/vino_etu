<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend.
 */

class Frontend extends Routeur {

  const BASEURL = "http://localhost:8080/vino_etu/";
  /**
   * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
   * 
   */
  public function __construct() {
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Afficher la page d'accueil.
   */  
  public function voirAccueil() {

    $bouteilles = $this->oRequetesSQL->getListeBouteilleCellier();

    new Vue("/Frontend/vAccueil",
      array(
        'titre'     => "Un petit verre de vino",
        'BASEURL'     => self::BASEURL,
        'bouteilles'  => $bouteilles
      ),
      "/Frontend/gabarit-frontend");
  }

}