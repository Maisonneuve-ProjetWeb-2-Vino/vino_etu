<?php

/**
 * Classe Contrôleur des requêtes de l'interface Cellier.
 */

class ControleurCellier extends Routeur {

  private $action;
  private $bouteille_id;
  private $cellier_id;
  private $oUtilConn;

  private $methodes = [
    'a' => 'ajouterBouteilleCellier',
    'b' => 'boireBouteilleCellier',
    'c' => 'autocompleteBouteille',
    'd' => 'afficherFicheBouteille',
    'e' => 'ajouterBouteillePersonnaliseeCellier',
    'l' => 'listeBouteille',
    'm' => 'modifierBouteilleCellier',
    'n' => 'ajouterNouvelleBouteilleCellier',
    'o' => 'listeCellier',
    'p' => 'ajouterCellier',
    'q' => 'modifierCellier',
    'r' => 'obtenirDetailsBouteille',
    's' => 'supprimerCellier',
    't' => 'supprimerBouteille',
    'v' => 'verifierBouteilleCellier',
    'u' => 'verifierNomCellier'
  ];

  /**
   * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
   * 
   */
  public function __construct() {
    $this->action = $_GET['action'] ?? 'o';
    $this->bouteille_id = $_GET['bouteille_id'] ?? null;
    $this->cellier_id = $_GET['cellier_id'] ?? null;
    $this->oUtilConn = $_SESSION['oConnexion'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Redirige les requêtes de l'interface Cellier vers les méthodes demandées.
   * 
   * @throws Exception Si l'action spécifiée dans la requête n'existe pas
   * @return void
   */  
  public function gererCellier() {

    if (isset($this->methodes[$this->action])) {
      if ($this->oUtilConn) {
        $methode = $this->methodes[$this->action];
        $this->$methode();
      }
      else {
        header("Location: accueil"); // retour sur la page de connexion
        exit;
      }
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

    $utilisateur_id = $this->oUtilConn->id_membre;

    $body = json_decode(file_get_contents('php://input'));

    if(!empty($body)){

      // Création d'un objet Bouteille pour contrôler la saisie
      $oBouteille = new BouteilleCellier([
          'id_bouteille'  => $body->id_bouteille,
          'id_cellier'    => $body->id_cellier,
          'quantite'      => $body->quantite
      ]);
      
      $erreursBouteille = $oBouteille->erreurs;

      if (count($erreursBouteille) === 0) {

        $resultat = $this->oRequetesSQL->ajouterBouteilleCellier([
          'id_bouteille'  => $oBouteille->id_bouteille,
          'id_cellier'    => $oBouteille->id_cellier,
          'quantite'      => $oBouteille->quantite,
        ]);

        echo json_encode($resultat);
      }
      else {
        // Pas supposé étant donné la validation front-end
        throw new Exception("Erreur: bouteille invalide, non insérée:" . implode($oBouteille->erreurs));
      }

    }
    else{

      $cellier_preferentiel = $this->cellier_id ?? null;
      $celliers = $this->oRequetesSQL->obtenirListeCelliers($utilisateur_id);
      $pays = $this->oRequetesSQL->obtenirListePays();
      $types = $this->oRequetesSQL->obtenirListeTypes();
      $lien = "cellier?action=l&cellier_id=".$this->cellier_id;

      new Vue("/Cellier/vAjoutBouteille",
        array(
          'lien'          => $lien,
          'titre'                 => "Ajout de bouteille",
          'cellier_preferentiel'  => $cellier_preferentiel,
          'celliers'              => $celliers,
          'pays'                  => $pays,
          'types'                 => $types
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

    $utilisateur_id = $this->oUtilConn->id_membre;

    $body = json_decode(file_get_contents('php://input'));
          
    $listeBouteilles = $this->oRequetesSQL->autocomplete($body->nom, $utilisateur_id);
          
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
      $this->traiterFormulaireModificationBouteille($body);
    }
    else{
      if (!$this->bouteille_id) {
        throw new Exception(self::ERROR_BAD_REQUEST);
      }

      $bouteilleAModifier = $this->oRequetesSQL->obtenirDetailsBouteilleCellier($this->bouteille_id);

      if ($bouteilleAModifier['idmembre']){

        // Si bouteille personnalisée
        $pays = $this->oRequetesSQL->obtenirListePays();
        $types = $this->oRequetesSQL->obtenirListeTypes();

        new Vue("/Cellier/vModificationBouteillePersonnalisee",
          array(
            'titre'       => "Modification de bouteille personnalisée",
            'bouteille'   => $bouteilleAModifier,
            'pays'        => $pays,
            'types'       => $types
          ),
        "/Frontend/gabarit-frontend");

      } else {
        // Sinon bouteille de la saq
        new Vue("/Cellier/vModificationBouteilleSAQ",
          array(
            'titre'       => "Modification de bouteille de la SAQ",
            'bouteille'   => $bouteilleAModifier
          ),
        "/Frontend/gabarit-frontend");
      }

    }

  }

  private function traiterFormulaireModificationBouteille($body) {

    $bouteilleAModifier = $this->oRequetesSQL->obtenirDetailsBouteilleCellier($body->id_bouteille_cellier);
    
    if ($bouteilleAModifier['idmembre']) {
      // Si vin personnalisé

      // Création d'un objet BouteilleCatalogue pour contrôler la saisie
      $oBouteilleCatalogue = new BouteilleCatalogue([
        'id_bouteille'  => $body->id_bouteille_catalogue,
        'nom'           => $body->nom,
        'pays'          => $body->pays,
        'type'          => $body->type,
        'annee'         => $body->annee,
        'format'        => $body->format,
        'cepage'        => $body->cepage,
        'particularite' => $body->particularite,
        'appellation'   => $body->appellation,
        'degreAlcool'   => $body->degreAlcool,
        'origine'       => $body->origine,
        'producteur'    => $body->producteur,
        'prix_saq'      => $body->prix_saq,
        'region'        => $body->region,
        'tauxSucre'     => $body->tauxSucre
      ]);
      
      if (count($oBouteilleCatalogue->erreurs) === 0) {
        $resultat = $this->oRequetesSQL->modifierBouteilleCatalogue([ 
          'nom'                     => $oBouteilleCatalogue->nom,
          'prix_saq'                => $oBouteilleCatalogue->prix_saq,
          'annee'                   => $oBouteilleCatalogue->annee,
          'type'                    => $oBouteilleCatalogue->type,
          'origine'                 => $oBouteilleCatalogue->origine,
          'region'                  => $oBouteilleCatalogue->region,
          'appellation'             => $oBouteilleCatalogue->appellation,
          'cepage'                  => $oBouteilleCatalogue->cepage,
          'degreAlcool'             => $oBouteilleCatalogue->degreAlcool,
          'particularite'           => $oBouteilleCatalogue->particularite,
          'format'                  => $oBouteilleCatalogue->format,
          'producteur'              => $oBouteilleCatalogue->producteur,
          'pays'                    => $oBouteilleCatalogue->pays,
          'id_bouteille_catalogue'  => $oBouteilleCatalogue->id_bouteille
        ]);

        // Création d'un objet BouteilleCellier pour contrôler la saisie
        $oBouteilleCellier = new BouteilleCellier([
          'id_bouteille_cellier'=> $body->id_bouteille_cellier,
          'id_bouteille'        => $body->id_bouteille_catalogue,
          'quantite'            => $body->quantite
        ]);

        if (count($oBouteilleCellier->erreurs) === 0) {

          $resultat = $this->oRequetesSQL->modifierBouteilleCellier([
            'id_bouteille_cellier'=> $oBouteilleCellier->id_bouteille_cellier,
            'id_bouteille'        => $oBouteilleCellier->id_bouteille,
            'quantite'            => $oBouteilleCellier->quantite
          ]);

        } else {
          // Pas supposé étant donné la validation front-end
          throw new Exception("Erreur: bouteille cellier invalide, non insérée:" . implode($oBouteilleCellier->erreurs));
        }
      } else {
        // Pas supposé étant donné la validation front-end
        throw new Exception("Erreur: bouteille catalogue invalide, non insérée:" . implode($oBouteilleCatalogue->erreurs));
      }

    } else {  
      // Sinon vin de la SAQ

      // Création d'un objet Bouteille pour contrôler la saisie
      $oBouteilleCellier = new BouteilleCellier([
          'id_bouteille_cellier'=> $body->id_bouteille_cellier,
          'id_bouteille'       => $body->id_bouteille_catalogue,
          'quantite'           => $body->quantite
      ]);
      
      if (count($oBouteilleCellier->erreurs) === 0) {

        $resultat = $this->oRequetesSQL->modifierBouteilleCellier([
          'id_bouteille_cellier'=> $oBouteilleCellier->id_bouteille_cellier,
          'id_bouteille'        => $oBouteilleCellier->id_bouteille,
          'quantite'            => $oBouteilleCellier->quantite
        ]);
      }
      else {
        // Pas supposé étant donné la validation front-end
        throw new Exception("Erreur: bouteille invalide, non insérée:" . implode($oBouteilleCellier->erreurs));
      }
    }
    echo json_encode($resultat);

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
    $oBouteille = new BouteilleCellier([
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
    $oBouteille = new BouteilleCellier([
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

  /**
   * Liste les celliers pour un utilisateur donné.
   * 
   * @return void
   */
  public function listeCellier() {

    $utilisateur_id = $this->oUtilConn->id_membre;

    // Extraction nom et id de tous les celliers de l'utilisateur
    $celliers = $this->oRequetesSQL->obtenirListeCelliers($utilisateur_id);

    $lien = "";
    $celliers_details = [];
    foreach ($celliers as $cellier) {

      // Extraction et calcul des proportions pour chaque type de vin
      $quantites_cellier = $this->oRequetesSQL->obtenirQuantitesCellier($cellier['id']);
      $total_bouteilles = Utilitaires::calculerTotalBouteilles($quantites_cellier);
      $cellier_details = [];

      if ($total_bouteilles > 0) {
        $proportions_cellier = Utilitaires::calculerProportionsTypes($quantites_cellier);
        $cellier_details['pourcentages'] = Utilitaires::formerDiagrammeCirculaire($proportions_cellier);
      }

      // Remettre toutes les infos dans une variable pour Twig
      $cellier_details['id'] = $cellier['id'];
      $cellier_details['nom'] = $cellier['nom'];
      $cellier_details['quantite'] = $total_bouteilles;
      $celliers_details[] = $cellier_details;
    }

    new Vue("/Cellier/vListeCelliers",
      array(
        'oUtilConn' => $this->oUtilConn,
        'lien'        => $lien,
        'titre'     => "Vos celliers",
        'celliers'  => $celliers_details,
      ),
      "/Frontend/gabarit-frontend");
  }

  /**
   * Liste les bouteilles pour un cellier donné.
   * 
   * @return void
   */  
  public function listeBouteille() {

    $bouteilles = $this->oRequetesSQL->obtenirListeBouteilleCellier($this->cellier_id);

    $cellier = $this->oRequetesSQL->obtenirNomCellier($this->cellier_id);
    $lien = "cellier";
    $message = "Voulez-vous vraiment supprimer ce cellier avec tout son contenu ?";

    new Vue("/Cellier/vListeBouteilles",
      array(
        'titre'       => $cellier["nom"],
        'lien'        => $lien,
        'bouteilles'  => $bouteilles,
        'cellier'     => $cellier,
        'message'     => $message
      ),
      "/Frontend/gabarit-frontend");
  }

  /**
   * Ajoute un cellier pour l'utilisateur authentifié.
   * 
   * @throws Exception Si une erreur survient lors de l'insertion du cellier
   * @return void
   */
  public function ajouterCellier() {

    $utilisateur_id = $this->oUtilConn->id_membre;

    $oCellier = [];
    $erreursCellier = [];
    $lien = "cellier";


    if (count($_POST) !== 0) {

      // Retour de saisie du formulaire
      $oCellier = new Cellier([
        'nom'       => $_POST['nom'],
        'id_membre' => $utilisateur_id
      ]); 

      $erreursCellier = $oCellier->erreurs;

      // Vérification pour le nom déjà existant dans le cellier
      if ($this->oRequetesSQL->verifierNomCellier($utilisateur_id, $_POST['nom'])) {
        $erreursCellier['nom'] = "Un cellier du même nom existe déjà.";
      }


      if (count($erreursCellier) === 0) {
        $resultat = $this->oRequetesSQL->ajouterCellier([
          'nom'       =>  $oCellier->nom,
          'idmembre'  =>  $oCellier->id_membre
        ]);

        if (!$resultat) {
          throw new Exception("Une erreur est survenue lors de l'insertion du cellier");
        }

        $this->listeCellier();
        exit;
      }
    }

    new Vue("/Cellier/vAjoutCellier",
      array(
        'lien'      =>$lien,
        'titre'     => "Ajouter un cellier",
        'cellier'   => $oCellier,
        'erreurs'    => $erreursCellier
      ),
      "/Frontend/gabarit-frontend");

  }

  /**
   * Affiche la fiche détaillée pour une bouteille donnée.
   * 
   * @throws Exception Si la requête de lecture des détails échoue.
   * @return void
   */
  public function afficherFicheBouteille() {

    $bouteille = $this->oRequetesSQL->obtenirDetailsBouteilleCellier($this->bouteille_id);
    
    $id_cellier = $bouteille["id_cellier"];

    $lien = "cellier?action=l&cellier_id=".$id_cellier;
    if (!$bouteille) {
      throw new Exception(self::ERROR_BAD_REQUEST);
    }

    $message = "Voulez-vous vraiment supprimer cette bouteille ?";

    new Vue("/Cellier/vFicheBouteille",
      array(
        'lien'      => $lien,
        'titre'     => 'Fiche détaillée',
        'bouteille' => $bouteille,
        'message'   => $message
      ),
      "/Frontend/gabarit-frontend");
  }

  /**
   * Modifie le nom d'un cellier.
   * 
   * @throws Exception Si une requête est faite sans envoi du numéro de cellier
   * @return void
   */
  public function modifierCellier() {

    $body = json_decode(file_get_contents('php://input'));

    if(!empty($body)){

      $resultat = $this->oRequetesSQL->modifierCellier([
        'cellier_id'  => $body->cellier_id,
        'nom'         => $body->nom
      ]);

      echo json_encode($resultat);

    }
    else {
      if (!$this->cellier_id) {
        throw new Exception(self::ERROR_BAD_REQUEST);
      }

      $cellier = $this->oRequetesSQL->obtenirNomCellier($this->cellier_id);
      $lien = "cellier?action=l&cellier_id=".$this->cellier_id;

      new Vue("/Cellier/vModificationCellier",
        array(
          'lien'        => $lien,
          'titre'       => "Modification du cellier",
          'cellier'     => $cellier
      ),
      "/Frontend/gabarit-frontend");
    }
  }

  /**
   * Supprime un cellier avec tout son contenu.
   * 
   * @throws Exception Si la requête ne contient pas l'id du membre, ou s'il y a 
   *                   une erreur lors de la suppression.
   * @return void
   */
  public function supprimerCellier() {

    $utilisateur_id = $this->oUtilConn->id_membre;

    if (!$this->cellier_id) {
      throw new Exception(self::ERROR_BAD_REQUEST);
    }

    // Vérifier que le membre supprime bien un de ses propres celliers
    $id_membre_cellier = $this->oRequetesSQL->obtenirMembreCellier($this->cellier_id);
    if ($id_membre_cellier != $utilisateur_id) {
      throw new Exception(self::ERROR_FORBIDDEN);
    }

    $resultat = $this->oRequetesSQL->supprimerCellier($this->cellier_id);

    if (!$resultat) {
      throw new Exception("Erreur lors de la suppression du cellier");
    }

    // Redirection vers la liste des celliers
    header('Location: cellier');
  }

  /**
   * Supprime une bouteille d'un cellier.
   * 
   */
  public function supprimerBouteille() {

    $utilisateur_id = $this->oUtilConn->id_membre;

    if (!$this->bouteille_id) {
      throw new Exception(self::ERROR_BAD_REQUEST);
    }

    // Vérifier que le membre supprime bien une de ses propres bouteilles
    $details_bouteille = $this->oRequetesSQL->obtenirMembreBouteille($this->bouteille_id);
    if ($details_bouteille['idmembre'] != $utilisateur_id) {
      throw new Exception(self::ERROR_FORBIDDEN);
    }

    $resultat = $this->oRequetesSQL->supprimerBouteille($this->bouteille_id);
    $cellier_id = $details_bouteille['id_cellier'];

    if (!$resultat) {
      throw new Exception("Erreur lors de la suppression de la bouteille");
    }

    // Redirection vers la liste des bouteilles
    header("Location: cellier?action=l&cellier_id=$cellier_id");
  }

  /**
   * Donne la liste des détails pour une bouteille donnée du catalogue.
   * 
   * @return void
   */
  public function obtenirDetailsBouteille() {

    $body = json_decode(file_get_contents('php://input'));

    $bouteille = $this->oRequetesSQL->obtenirBouteilleCatalogue($body->id_bouteille);
            
    echo json_encode($bouteille);

  }

  /**
   * Ajouter une bouteille personnalisée au cellier.
   * 
   * @throws Exception S'il y a des erreurs dans les champs ou une erreur d'insertion
   *                   dans la bd.
   * @return void
   */
  public function ajouterBouteillePersonnaliseeCellier() {
        
    $utilisateur_id = $this->oUtilConn->id_membre;

    $body = json_decode(file_get_contents('php://input'));

    if(!empty($body)){

      // Création d'un objet BouteilleCatalogue pour contrôler la saisie
      $oBouteilleCatalogue = new BouteilleCatalogue([
        'nom'           => $body->nom,
        'pays'          => $body->pays,
        'type'          => $body->type,
        'annee'         => $body->annee,
        'format'        => $body->format,
        'cepage'        => $body->cepage,
        'particularite' => $body->particularite,
        'appellation'   => $body->appellation,
        'degreAlcool'   => $body->degreAlcool,
        'origine'       => $body->origine,
        'producteur'    => $body->producteur,
        'prix_saq'      => $body->prix_saq,
        'region'        => $body->region,
        'tauxSucre'     => $body->tauxSucre,
      ]);
      
      if (count($oBouteilleCatalogue->erreurs) === 0) {

        $id_bouteille_catalogue = $this->oRequetesSQL->ajouterBouteilleCatalogue([
          'nom'           => $oBouteilleCatalogue->nom,
          'pays'          => $oBouteilleCatalogue->pays,
          'type'          => $oBouteilleCatalogue->type,
          'annee'         => $oBouteilleCatalogue->annee,
          'format'        => $oBouteilleCatalogue->format,
          'cepage'        => $oBouteilleCatalogue->cepage,
          'particularite' => $oBouteilleCatalogue->particularite,
          'degreAlcool'   => $oBouteilleCatalogue->degreAlcool,
          'origine'       => $oBouteilleCatalogue->origine,
          'appellation'   => $oBouteilleCatalogue->appellation,
          'producteur'    => $oBouteilleCatalogue->producteur,
          'prix_saq'      => $oBouteilleCatalogue->prix_saq,
          'region'        => $oBouteilleCatalogue->region,
          'tauxSucre'     => $oBouteilleCatalogue->tauxSucre,
          'idmembre'      => $utilisateur_id
        ]);

        // Création d'un objet BouteilleCellier pour contrôler la saisie
        $oBouteilleCellier = new BouteilleCellier([
            'id_bouteille'  => $id_bouteille_catalogue,
            'id_cellier'    => $body->id_cellier,
            'quantite'      => $body->quantite
        ]);

        if (count($oBouteilleCellier->erreurs) === 0) {

          $resultat = $this->oRequetesSQL->ajouterBouteilleCellier([
            'id_bouteille'  => $oBouteilleCellier->id_bouteille,
            'id_cellier'    => $oBouteilleCellier->id_cellier,
            'quantite'      => $oBouteilleCellier->quantite,
          ]);

          echo json_encode($resultat);

        } else { // Pas supposé étant donné la validation front-end
            
           throw new Exception("Erreur: bouteille cellier invalide, non insérée:" . implode($oBouteilleCellier->erreurs));
        }
      }
      else { // Pas supposé étant donné la validation front-end
        throw new Exception("Erreur: bouteille invalide, non insérée:" . implode($oBouteilleCatalogue->erreurs));
      }

    }
    else{
      // requête REST seulement
      throw new Exception(self::ERROR_BAD_REQUEST);
    }
  }

  /**
   * Vérifie si une bouteille du catalogue se trouve déjà dans un cellier.
   * 
   * @return void
   */
  public function verifierBouteilleCellier() {

    $utilisateur_id = $this->oUtilConn->id_membre;

    $body = json_decode(file_get_contents('php://input'));

    $resultat = $this->oRequetesSQL->verifierBouteilleDansCellier($body->id_bouteille, $body->id_cellier);

    $msgRetour = ['statut' =>  $resultat];
    echo json_encode($msgRetour);
  }

  /**
   * Vérifie si un cellier avec un nom donné existe déjà pour un utilisateur.
   * 
   * @return void
   */
  public function verifierNomCellier() {

    $utilisateur_id = $this->oUtilConn->id_membre;

    $body = json_decode(file_get_contents('php://input'));

    $resultat = $this->oRequetesSQL->verifierNomCellier($utilisateur_id, $body->nom);

    $msgRetour = ['statut' =>  $resultat];
    echo json_encode($msgRetour);
  }
}
