<?php

/**
 * Classe Contrôleur des requêtes de l'interface Cellier.
 */

class Cellier extends Routeur {

  const BASEURL = "http://localhost:8080/vino_refactor/";
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

  /**
   * Redirige les requêtes de l'interface Cellier vers les méthodes demandées.
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
        throw new Exception("Erreur: bouteille invalide, non insérée:" . implode($oBouteille->erreurs));
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

      $bouteilleAModifier = $this->oRequetesSQL->getBouteilleCellier($this->bouteille_id);

      new Vue("/Cellier/vModificationBouteille",
        array(
          'titre'       => "Modification de bouteille",
          'bouteille'   => $bouteilleAModifier,
          'BASEURL'     => self::BASEURL
        ),
      "/Frontend/gabarit-frontend");
    }

  }
}