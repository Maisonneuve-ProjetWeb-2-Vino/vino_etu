<?php

/**
 * Classe Contrôleur des requêtes de l'interface Cellier.
 */

class Cellier extends Routeur {

  const BASEURL = "http://localhost:8080/vino_refactor/";
  private $action;

  private $methodes = [
    'a' => 'ajouterBouteilleCellier',
    'b' => 'boireBouteilleCellier',
    'c' => 'autocompleteBouteille',
    'l' => 'listeBouteille',
    'm' => 'modifierBouteilleCellier',
    'n' => 'ajouterNouvelleBouteilleCellier'
  ];

  /**
   * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
   * 
   */
  public function __construct() {
    $this->action = $_GET['action'] ?? 'l';
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Gérer l'interface Cellier.
   * 
   * @return void
   */  
  public function gererCellier() {

    if (isset($this->methodes[$this->action])) {
      $methode = $this->methodes[$this->action];
      $this->$methode();
    } else {
      throw new Exception("L'action $this->action n'existe pas.");
    }

  }

  /**
   * Ajouter une nouvelle bouteille au cellier.
   * 
   * @return void
   */
  public function ajouterNouvelleBouteilleCellier() {

    $body = json_decode(file_get_contents('php://input'));

    if(!empty($body)){

      // Création d'un objet Bouteille pour contrôler la saisie
      $oBouteille = new Bouteille([
          'id_bouteille'  => $body->id_bouteille,
          'date_achat'    => $body->date_achat,
          'garde_jusqua'  => $body->garde_jusqua,
          'notes'         => $body->notes,
          'prix'          => $body->prix,
          'quantite'      => $body->quantite,
          'millesime'     => $body->millesime
      ]);
      
      $erreursBouteille = $oBouteille->erreurs;

      if (count($erreursBouteille) === 0) {

        $resultat = $this->oRequetesSQL->ajouterBouteilleCellier([
          'id_bouteille'  => $oBouteille->id_bouteille,
          'date_achat'    => $oBouteille->date_achat,
          'garde_jusqua'  => $oBouteille->garde_jusqua,
          'notes'         => $oBouteille->notes,
          'prix'          => $oBouteille->prix,
          'quantite'      => $oBouteille->quantite,
          'millesime'     => $oBouteille->millesime
        ]);

        echo json_encode($resultat);
      }
      else {
        // Pas supposé étant donné la validation front-end
        throw new Exception("Erreur: bouteille invalide, non insérée.");
      }

    }
    else{
      new Vue("/Cellier/vAjoutBouteille",
        array(
          'titre'     => "Ajout de bouteille",
          'BASEURL'     => self::BASEURL
        ),
      "/Frontend/gabarit-frontend");
    }

  }

}