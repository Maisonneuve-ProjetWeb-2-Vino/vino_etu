<?php

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO {

  /* GESTION DES CELLIERS
     ==================== */

  /**
  * Retourne les détails sur chaque bouteille du cellier.
  *
  * @return array Tableau des données représentant le cellier
  */
  public function obtenirListeBouteilleCellier($id_cellier) {

    $this->sql = "
      SELECT 
        c.id_bouteille_cellier,
        c.quantite,
        b.nom, 
        b.idtype AS type, 
        b.image_url,
        b.format,
        p.pays
      FROM bouteilles_cellier c 
      INNER JOIN bouteilles_catalogue b ON c.idbouteillecatalogue = b.id_bouteille
      INNER JOIN pays p ON b.idpays = p.id_pays
      WHERE c.idcellier = :id_cellier
      ";
      
    return $this->obtenirLignes(['id_cellier' => $id_cellier]);
  }

  /**
	 * Ajoute une ou des bouteilles au cellier
	 * 
	 * @param Array $data Tableau des données représentant la bouteille.
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
	 */
	public function ajouterBouteilleCellier($champs)
	{
    $champs['quantite'] = empty($champs['quantite']) ? 0 : $champs['quantite'];

    $this->sql = "
      INSERT INTO bouteilles_cellier SET idbouteillecatalogue = :id_bouteille,
      idcellier = :id_cellier, quantite = :quantite
      ";
        
    return $this->CUDLigne($champs);
	}

  /**
	 * Cette méthode permet de retourner les résultats de recherche pour la fonction d'autocomplete
   * de l'ajout des bouteilles dans le cellier
	 * 
	 * @param string $nom La chaine de caractère à rechercher
	 * @param integer $utilisateur_id L'id de l'utilisateur
	 * @param integer $nb_resultat Le nombre de résultats maximal à retourner.
	 * @return array id et nom de la bouteille trouvée dans le catalogue
	 */
  public function autocomplete($nom, $utilisateur_id, $nb_resultat=10) {

		$nom = preg_replace("/\*/","%" , $nom);
		$keywords = '%'. $nom .'%';

		$this->sql = "
      SELECT id_bouteille AS id, nom FROM bouteilles_catalogue
      WHERE LOWER(nom) LIKE LOWER(:keywords) 
      AND (idmembre is NULL OR idmembre = :utilisateur_id)
      LIMIT 0, :nb_resultat
      ";

    return $this->obtenirLignes(['nb_resultat' => $nb_resultat, 'keywords' => $keywords, 'utilisateur_id' => $utilisateur_id]);
  }

  /**
	 * Modifie une bouteille au cellier
	 * 
	 * @param Array $data Tableau des données représentant la bouteille.
   * @return string|boolean clé primaire de la ligne modifiée, false sinon
	 */
	public function modifierBouteilleCellier($champs)
	{
    $champs['quantite'] = empty($champs['quantite']) ? 0 : $champs['quantite'];

    $this->sql = "
      UPDATE bouteilles_cellier SET idbouteillecatalogue = :id_bouteille, quantite = :quantite
      WHERE id_bouteille_cellier = :id_bouteille_cellier
      ";
        
    return $this->CUDLigne($champs); 
	}

	/**
	 * Cette méthode change la quantité d'une bouteille en particulier dans le cellier.
	 * 
	 * @param int $id id de la bouteille
	 * @param int $nombre Nombre de bouteilles à ajouter ou retirer
   * @return string|boolean clé primaire de la ligne modifiée, false sinon
	 */
	public function modifierQuantiteBouteilleCellier($id, $nombre) {

		$this->sql = "
      UPDATE bouteilles_cellier SET quantite = GREATEST(quantite + :nombre, 0) WHERE id_bouteille_cellier = :id
      ";

    return $this->CUDLigne(['nombre' => $nombre, 'id' => $id]); 
	}

  /**
	 * Récupère les données d'une bouteille d'un cellier, à partir de son id dans le catalogue.
	 * 
	 * @param int $id_bouteille_catalogue L'id de la bouteille dans le catalogue
   * @return array|false ligne de la table, false sinon
	 */
	public function obtenirBouteilleCatalogue($id_bouteille_catalogue) {

		$this->sql = "
			SELECT *
      FROM bouteilles_catalogue
			WHERE id_bouteille = :id_bouteille
      ";

		return $this->obtenirLignes(['id_bouteille' => $id_bouteille_catalogue], RequetesPDO::UNE_SEULE_LIGNE);
	}

    /**
	 * Récupère les données d'une bouteille d'un cellier, à partir de son id dans le cellier.
	 * 
	 * @param int $id_bouteille_cellier L'id de la bouteille dans le cellier
   * @return array|false ligne de la table, false sinon
	 */
	public function obtenirBouteilleCellier($id_bouteille_cellier) {

		$this->sql = "
			SELECT *
      FROM bouteilles_catalogue
      JOIN bouteilles_cellier ON bouteilles_catalogue.id_bouteille = bouteilles_cellier.idbouteillecatalogue
			WHERE bouteilles_cellier.id_bouteille_cellier = :id_bouteille_cellier
      ";

		return $this->obtenirLignes(['id_bouteille_cellier' => $id_bouteille_cellier], RequetesPDO::UNE_SEULE_LIGNE);
	}

 /**
  * Retourne la liste des celliers pour un utilisateur donné.
  *
	* @param int $utilisateur_id id de l'utilisateur
  * @return array Tableau des données représentant le cellier
  */
  public function obtenirListeCelliers($utilisateur_id) {

		$this->sql = "
      SELECT id_cellier AS id, nom
      FROM celliers
      WHERE idmembre = :utilisateur_id
      ";

    return $this->obtenirLignes(['utilisateur_id' => $utilisateur_id]);
  }

  /**
  * Retourne la liste des quantités pour chaque type de vin, pour un cellier donné.
  *
	* @param int $cellier_id id du cellier
  * @return array Tableau des données représentant le cellier
  */
  public function obtenirQuantitesCellier($cellier_id) {

    // Extraction des types de vin
    $champs = [];
    $this->sql = "
      SELECT type
      FROM types
      ";
    $types = $this->obtenirLignes($champs);

    // Extraction des quantités pour chaque type de vin
    $quantite = [];
    foreach ($types as $type) {
      $this->sql = "
        SELECT SUM(quantite) AS quantite
        FROM bouteilles_catalogue b
        JOIN bouteilles_cellier c ON  b.id_bouteille = c.idbouteillecatalogue
        WHERE idcellier = :cellier_id
        AND b.idtype = :type
        ";

      $resultat = $this->obtenirLignes(['cellier_id' => $cellier_id, 'type' => $type['type'] ], RequetesPDO::UNE_SEULE_LIGNE);
      $quantite[$type['type']] = $resultat['quantite'] ?? 0;
    }

    return $quantite;

  }

  /**
	 * Ajoute un cellier pour un utilisateur donné.
	 * 
	 * @param Array $data Tableau des données contenant le nom du cellier et l'id de l'utilisateur.
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
	 */
	public function ajouterCellier($champs)
	{

    $this->sql = "
      INSERT INTO celliers SET nom = :nom, idmembre = :idmembre
      ";
        
    return $this->CUDLigne($champs); 
	}

  /**
   * Retourne le nom d'un cellier.
   * 
   * @param int $id_cellier L'id du cellier
   * @return array|false ligne de la table, false sinon
   */
  public function obtenirNomCellier($id_cellier) {

    $this->sql = "
      SELECT nom, id_cellier AS id
      FROM celliers
      WHERE id_cellier = :id_cellier
      ";

    return $this->obtenirLignes(['id_cellier' => $id_cellier], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Donne les détails du catalogue et du cellier (quantité) pour une bouteille donnée.
   * 
   * @param int $id_bouteille_cellier L'id de la bouteille dans le cellier
   */
  public function obtenirDetailsBouteilleCellier($id_bouteille_cellier) {

    //TODO  extraire colonne sucre aussi
    $this->sql = "
      SELECT 
        c.id_bouteille_cellier AS id_bouteille_cellier,
        c.quantite,
        c.idcellier AS id_cellier,
        b.id_bouteille AS id_bouteille_catalogue,
        b.idmembre,
        b.nom, 
        b.idtype AS type, 
        b.image_url,
        b.format,
        b.cepage,
        b.degreAlcool,
        b.producteur,
        b.prix_saq,
        b.region,
        b.pastille,
        b.tauxSucre,
        b.particularite,
        b.appellation,
        b.annee,
        b.origine,
        b.idpays,
        p.pays
      FROM bouteilles_cellier c 
      INNER JOIN bouteilles_catalogue b ON c.idbouteillecatalogue = b.id_bouteille
      INNER JOIN pays p ON b.idpays = p.id_pays
      WHERE c.id_bouteille_cellier = :id_bouteille_cellier
      ";

    return $this->obtenirLignes(['id_bouteille_cellier' => $id_bouteille_cellier], RequetesPDO::UNE_SEULE_LIGNE);

  }

  /**
   * Modifier le nom d'un cellier.
   * 
   * @param array $champs Tableau avec le nouveau nom et l'id du cellier à changer.
   * @return string|boolean clé primaire de la ligne modifiée, false sinon
   */
  public function modifierCellier($champs) {

		$this->sql = "
      UPDATE celliers SET nom = :nom WHERE id_cellier = :cellier_id
      ";

    return $this->CUDLigne($champs);
  }

  /**
   * Supprime un cellier et toutes les bouteilles en inventaire associées.
   * 
   * @param int $id_cellier L'id du cellier à supprimer
   * @return string|boolean clé primaire de la ligne modifiée, false sinon
   */
  public function supprimerCellier($id_cellier) {

    $this->sql = "
      DELETE FROM celliers WHERE id_cellier = :id_cellier
      ";

    return $this->CUDLigne(['id_cellier' => $id_cellier]);
  }

  /**
   * Supprime une bouteille du cellier.
   * 
   * @param int $id_bouteille L'id de la bouteille à supprimer
   * @return string|boolean clé primaire de la ligne modifiée, false sinon
   */
    public function supprimerBouteille($id_bouteille) {

    $this->sql = "
      DELETE FROM bouteilles_cellier WHERE id_bouteille_cellier = :id_bouteille
      ";

    return $this->CUDLigne(['id_bouteille' => $id_bouteille]);
  }

  /**
   * Retourne l'id du membre pour un cellier donné.
   * 
   * @param int $id_cellier L'id du cellier
   * @return int L'id du membre
   */
  public function obtenirMembreCellier($id_cellier) {
    
    $this->sql = "
      SELECT idmembre
      FROM celliers
      WHERE id_cellier = :id_cellier
      ";

    return $this->obtenirLignes(['id_cellier' => $id_cellier], RequetesPDO::UNE_SEULE_LIGNE)['idmembre'];
  }

  /**
   * Retourne l'id du membre pour une bouteille donnée.
   * 
   * @param int $id_bouteille L'id de la bouteille
   * @return int L'id du membre
   */
  public function obtenirMembreBouteille($id_bouteille) {
    
    $this->sql = "
      SELECT idmembre, id_cellier
      FROM bouteilles_cellier
      JOIN celliers ON bouteilles_cellier.idcellier = celliers.id_cellier
      WHERE bouteilles_cellier.id_bouteille_cellier = :id_bouteille
      ";

    return $this->obtenirLignes(['id_bouteille' => $id_bouteille], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Retourne la liste de tous les pays.
   * 
   * @return array Tableau avec les pays.
   */
  public function obtenirListePays() {

    $champs = [];
    $this->sql = "
      SELECT id_pays AS id, pays
      FROM pays
      ";

    return $this->obtenirLignes($champs);
  }

  /**
   * Retourne la liste de tous les types de vins.
   * 
   * @return array Tableau avec les types de vins.
   */
  public function obtenirListeTypes() {

    $champs = [];
    $this->sql = "
      SELECT type
      FROM types
      ";

    return $this->obtenirLignes($champs);
  }

  /**
   * Ajoute une bouteille personnalisée au catalogue.
   * 
   * @param array $champs tableau des champs de la bouteille
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterBouteilleCatalogue($champs) {

    $this->sql = "
      INSERT INTO bouteilles_catalogue SET nom = :nom, prix_saq = :prix_saq, annee = :annee,
      idtype = :type, origine = :origine, region = :region, appellation = :appellation,
      cepage = :cepage, degreAlcool = :degreAlcool, particularite = :particularite,
      format = :format, producteur = :producteur, idpays = :pays, tauxSucre = :tauxSucre,
      idmembre = :idmembre
      ";

    return $this->CUDLigne($champs);
  }

  /**
   * Vérifie si une bouteille du catalogue se trouve déjà dans un cellier.
   * 
   * @param int $id_bouteille L'id de la bouteille (catalogue)
   * @param int $id_cellier L'id du cellier
   * @return bool Vrai si la bouteille se trouve dans le cellier, faux sinon
   */
  public function verifierBouteilleDansCellier($id_bouteille, $id_cellier) {
        
    $this->sql = "
      SELECT COUNT(*) AS nombre FROM bouteilles_cellier
      WHERE idcellier = :id_cellier AND idbouteillecatalogue = :id_bouteille
      ";

    $resultat = $this->obtenirLignes([
      'id_cellier' => $id_cellier,
      'id_bouteille' => $id_bouteille
    ], RequetesPDO::UNE_SEULE_LIGNE);
    
    return $resultat['nombre'] > 0 ? true : false;
  }

  public function modifierBouteilleCatalogue($champs) {
       
    $this->sql = "
      UPDATE bouteilles_catalogue 
      SET nom = :nom,
      prix_saq = :prix_saq,
      annee = :annee,
      idtype = :type,
      origine = :origine,
      region = :region,
      appellation = :appellation,
      cepage = :cepage,
      degreAlcool = :degreAlcool,
      particularite = :particularite,
      format = :format,
      producteur = :producteur,
      idpays = :pays
      WHERE id_bouteille = :id_bouteille_catalogue
      ";
        
    return $this->CUDLigne($champs); 
  }

  /* GESTION DES USAGERS 
    ======================== */

  /**
   * Connecter un usager
   * @param array $champs, tableau avec les champs courriel et mdp  
   * @return array|false ligne de la table, false sinon 
   */
  public function connecter($champs) {
    
    //var_dump($champs);
    $this->sql = "
      SELECT id_membre, nom, prenom, courriel, idprofil, date_creation, mdp
      FROM membres
      WHERE courriel = :courriel AND mdp = SHA2(:mdp, 512)";

    return $this->obtenirLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

/**
   * Ajouter un membre
   * @param array $champs tableau des champs du membre
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function inscriptionMembre($champs)
  {
    $this->sql = '
      INSERT INTO membres SET nom = :nom, prenom = :prenom, courriel = :courriel, mdp = SHA2(:mdp, 512), renouvelermdp = SHA2(:renouvelermdp, 512),idprofil = :idprofil, date_creation = NOW()';
    return $this->CUDLigne($champs);
  }

  /**
   * Controler si un mail existe dans la base
   * @param array $champs, tableau avec le champs courriel  
   * @return array|false ligne de la table, false sinon 
   */
  public function controleMail($champs)
  {
    $this->sql = "
      SELECT *
      FROM membres
      WHERE courriel = :courriel";

    return $this->obtenirLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

  /* GESTION DES USAGERS 
     ======================== */

  /**
   * Connecter un membre
   * @param array $champs, tableau avec les champs courriel et mdp  
   * @return array|false ligne de la table, false sinon 
   */
  public function connexion($champs)

  {
    //var_dump($champs);
    $this->sql = "
      SELECT id_membre, nom, prenom, courriel, idprofil, date_creation
      FROM membres
      WHERE courriel = :courriel AND mdp = SHA2(:mdp, 512)";

    return $this->obtenirLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération d'un membre de la table membres
   * @param int $id_membre 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne
   */
  public function infoMembre($id_membre)
  {
    $this->sql = '
      SELECT *
      FROM membres
      WHERE id_membre = :id_membre';
    return $this->obtenirLignes(['id_membre' => $id_membre], RequetesPDO::UNE_SEULE_LIGNE);
    
  }


}
