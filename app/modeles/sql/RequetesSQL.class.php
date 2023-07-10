<?php

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO {

  /* GESTION DES BOUTEILLES DU CELLIER
     ================================= */

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
	 * @param integer $nb_resultat Le nombre de résultats maximal à retourner.
	 * @return array id et nom de la bouteille trouvée dans le catalogue
	 */
  public function autocomplete($nom, $nb_resultat=10) {

		$nom = preg_replace("/\*/","%" , $nom);
		$keywords = '%'. $nom .'%';

		$this->sql = "
      SELECT id_bouteille AS id, nom FROM bouteilles_catalogue
      WHERE LOWER(nom) LIKE LOWER(:keywords) 
      LIMIT 0, :nb_resultat
      ";

    return $this->obtenirLignes(['nb_resultat' => $nb_resultat, 'keywords' => $keywords]);
  }

  /**
	 * Modifie une bouteille au cellier
	 * 
	 * @param Array $data Tableau des données représentant la bouteille.
   * @return string|boolean clé primaire de la ligne modifiée, false sinon
	 */
	public function modifierBouteilleCellier($champs)
	{
    $champs['millesime'] = empty($champs['millesime']) ? null : $champs['millesime'];
    $champs['date_achat'] = empty($champs['date_achat']) ? null : $champs['date_achat'];
    $champs['quantite'] = empty($champs['quantite']) ? 0 : $champs['quantite'];
    $champs['prix'] = empty($champs['prix']) ? 0 : $champs['prix'];

    $this->sql = "
      UPDATE vino__cellier SET id_bouteille = :id_bouteille, date_achat = :date_achat,
      garde_jusqua = :garde_jusqua, notes = :notes, prix = :prix, quantite = :quantite,
      millesime = :millesime WHERE id = :id_bouteille_cellier
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
	public function modifierQuantiteBouteilleCellier($id, $nombre)
	{		
		$this->sql = "
      UPDATE bouteilles_cellier SET quantite = GREATEST(quantite + :nombre, 0) WHERE id_bouteille_cellier = :id
      ";

      return $this->CUDLigne(['nombre' => $nombre, 'id' => $id]); 
	}

  /**
	 * Récupère les données d'une bouteille d'un cellier, à partir de son id.
	 * 
	 * @param int $bouteille_id id de la bouteille
   * @return array|false ligne de la table, false sinon
	 */
	public function obtenirBouteilleCellier($bouteille_id)
	{
		$this->sql = "
			SELECT vino__cellier.id, vino__cellier.id_bouteille, nom, date_achat, garde_jusqua, notes, prix, quantite, millesime FROM vino__cellier
			JOIN vino__bouteille ON vino__cellier.id_bouteille = vino__bouteille.id
			WHERE vino__cellier.id = :id
      ";

		return $this->obtenirLignes(['id' => $bouteille_id], RequetesPDO::UNE_SEULE_LIGNE);
	}

  
  /* GESTION DES CELLIERS
     ==================== */

  /**
  * Retourne la liste des celliers pour un utilisateur donné.
  *
	* @param int $utilisateur_id id de l'utilisateur
  * @return array Tableau des données représentant le cellier
  */
  public function obtenirListeCelliers($utilisateur_id) {
		$this->sql = "
      SELECT id_cellier, nom
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

  public function obtenirNomCellier($id_cellier) {
    $this->sql = "
      SELECT nom, id_cellier AS id
      FROM celliers
      WHERE id_cellier = :id_cellier
      ";

    return $this->obtenirLignes(['id_cellier' => $id_cellier], RequetesPDO::UNE_SEULE_LIGNE);
  }

  public function obtenirDetailsBouteilleCellier($id_bouteille) {

    //TODO  extraire colonne sucre aussi
    $this->sql = "
      SELECT 
        c.id_bouteille_cellier,
        c.quantite,
        c.idcellier AS id_cellier,
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
        p.pays
      FROM bouteilles_cellier c 
      INNER JOIN bouteilles_catalogue b ON c.idbouteillecatalogue = b.id_bouteille
      INNER JOIN pays p ON b.idpays = p.id_pays
      WHERE c.id_bouteille_cellier = :id_bouteille
      ";

    return $this->obtenirLignes(['id_bouteille' => $id_bouteille], RequetesPDO::UNE_SEULE_LIGNE);

  }

  public function modifierCellier($champs) {
    
		$this->sql = "
      UPDATE celliers SET nom = :nom WHERE id_cellier = :cellier_id
      ";

      return $this->CUDLigne($champs);
  }
}
