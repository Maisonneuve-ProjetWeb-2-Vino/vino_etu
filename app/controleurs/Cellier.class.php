<?php

/**
 * Classe Contrôleur des requêtes de l'interface Cellier.
 */

class Cellier extends Routeur {

  private $action;
  private $bouteille_id;

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
    $this->bouteille_id = $_GET['bouteille_id'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }

  
  public function listeCellier(){
    
    new Vue("/Cellier/vCelliers",
        array(
          'titre'       => "Cellier",
 
        ),
      "/Frontend/gabarit-frontend");
  }


  /**
   * Redirige les requêtes de l'interface Cellier vers les méthodes demandées.
   * 
   * @throws Exception Si l'action spécifiée dans la requête n'existe pas
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
   * @throws Exception Si la bouteille insérée contient des informations invalides
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
        throw new Exception("Erreur: bouteille invalide, non insérée:" . implode($oBouteille->erreurs));
      }

    }
    else{
      new Vue("/Cellier/vAjoutBouteille",
        array(
          'titre'     => "Ajout de bouteille"
        ),
      "/Frontend/gabarit-frontend");
    }

  }

  /**
   * Recherche le cellier par nom de bouteille. Renvoit la liste des bouteilles trouvées
   * en format JSON.
   * 
   * @return void
   */
  public function autocompleteBouteille() {

			$body = json_decode(file_get_contents('php://input'));
            
      $listeBouteilles = $this->oRequetesSQL->autocomplete($body->nom);
            
      echo json_encode($listeBouteilles);
  }

  /**
   * Modifier une bouteille du cellier.
   * 
   * @throws Exception Si la requête de modification de bouteille contient des informations invalides,
   *                   ou le bouteille_id d'une requête JSON est invalide.
   * @return void
   */
  public function modifierBouteilleCellier() {

    $body = json_decode(file_get_contents('php://input'));

    if(!empty($body)){

      // Création d'un objet Bouteille pour contrôler la saisie
      $oBouteille = new Bouteille([
          'id_bouteille_cellier'=> $body->id_bouteille_cellier,
          'id_bouteille'       => $body->id_bouteille,
          'date_achat'         => $body->date_achat,
          'garde_jusqua'       => $body->garde_jusqua,
          'notes'              => $body->notes,
          'prix'               => $body->prix,
          'quantite'           => $body->quantite,
          'millesime'          => $body->millesime
      ]);
      
      $erreursBouteille = $oBouteille->erreurs;

      if (count($erreursBouteille) === 0) {

        $resultat = $this->oRequetesSQL->modifierBouteilleCellier([
          'id_bouteille_cellier'=> $oBouteille->id_bouteille_cellier,
          'id_bouteille'        => $oBouteille->id_bouteille,
          'date_achat'          => $oBouteille->date_achat,
          'garde_jusqua'        => $oBouteille->garde_jusqua,
          'notes'               => $oBouteille->notes,
          'prix'                => $oBouteille->prix,
          'quantite'            => $oBouteille->quantite,
          'millesime'           => $oBouteille->millesime
        ]);

        echo json_encode($resultat);
      }
      else {
        // Pas supposé étant donné la validation front-end
        throw new Exception("Erreur: bouteille invalide, non insérée:" . implode($oBouteille->erreurs));
      }

    }
    else{
      if (!$this->bouteille_id) {
        throw new Exception(self::ERROR_BAD_REQUEST);
      }

      $bouteilleAModifier = $this->oRequetesSQL->obtenirBouteilleCellier($this->bouteille_id);

      new Vue("/Cellier/vModificationBouteille",
        array(
          'titre'       => "Modification de bouteille",
          'bouteille'   => $bouteilleAModifier
        ),
      "/Frontend/gabarit-frontend");
    }

  }

  /**
   * Incrémente la quantité pour une bouteille donnée.
   * 
   * @throws Exception Si l'id de la bouteille à ajouter est invalide, ou si la modification
   *                   à la base de données ne réussit pas.
   * @return void
   */
  private function ajouterBouteilleCellier()
  {
    $body = json_decode(file_get_contents('php://input'));
    
    // Création d'un objet Bouteille pour contrôler la saisie
    $oBouteille = new Bouteille([
      'id_bouteille_cellier'=> $body->id
    ]);

    if (count($oBouteille->erreurs) === 0) {
      $resultat = $this->oRequetesSQL->modifierQuantiteBouteilleCellier($oBouteille->id_bouteille_cellier, 1);
    }
    else {
      throw new Exception("Id invalide pour incrément de la quantité de bouteilles.");
    }

    if (!$resultat) {
      throw new Exception("Incrément de la quantité de bouteilles non mis à jour dans la db.");
    }

    echo json_encode($resultat);
  }

  /**
   * Décrémente la quantité pour une bouteille donnée.
   * 
   * @throws Exception Si l'id de la bouteille à décrémenter est invalide
   * @return void
   */
  private function boireBouteilleCellier()
  {
    $body = json_decode(file_get_contents('php://input'));
    
    // Création d'un objet Bouteille pour contrôler la saisie
    $oBouteille = new Bouteille([
      'id_bouteille_cellier'=> $body->id
    ]);

    if (count($oBouteille->erreurs) === 0) {
      $resultat = $this->oRequetesSQL->modifierQuantiteBouteilleCellier($oBouteille->id_bouteille_cellier, -1);
    }
    else {
      throw new Exception("Id invalide pour décrément de la quantité de bouteilles.");
    }

    echo json_encode($resultat);
  }

}