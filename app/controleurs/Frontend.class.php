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
     $this->oUtilConn = $_SESSION['oConnexion'] ?? null;
    
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Afficher la page d'accueil.
   * 
   * @return void
   */  
  public function voirAccueil() {

   

    new Vue("/Frontend/vAccueil",
      array(
        'oUtilConn' => $this->oUtilConn,
        'titre'     => "Un petit verre de vino",
        
        
      ),
      "/Frontend/gabarit-frontend");
  }

}