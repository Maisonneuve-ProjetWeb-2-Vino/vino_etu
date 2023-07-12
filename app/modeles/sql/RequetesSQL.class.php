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
  public function obtenirListeBouteilleCellier() {

    $champs = [];
    $this->sql = "
      SELECT 
        c.id as id_bouteille_cellier,
        c.id_bouteille, 
        c.date_achat, 
        c.garde_jusqua, 
        c.notes, 
        c.prix, 
        c.quantite,
        c.millesime, 
        b.id,
        b.nom, 
        b.type, 
        b.image, 
        b.code_saq, 
        b.url_saq, 
        b.pays, 
        b.description,
        t.type 
        FROM vino__cellier c 
        INNER JOIN vino__bouteille b ON c.id_bouteille = b.id
        INNER JOIN vino__type t ON t.id = b.type
        ";
      
      return $this->obtenirLignes($champs);
  }

  /**
	 * Ajoute une ou des bouteilles au cellier
	 * 
	 * @param Array $data Tableau des données représentant la bouteille.
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
	 */
	public function ajouterBouteilleCellier($champs)
	{
    $champs['millesime'] = empty($champs['millesime']) ? null : $champs['millesime'];
    $champs['date_achat'] = empty($champs['date_achat']) ? null : $champs['date_achat'];
    $champs['quantite'] = empty($champs['quantite']) ? 0 : $champs['quantite'];
    $champs['prix'] = empty($champs['prix']) ? 0 : $champs['prix'];

    $this->sql = "
      INSERT INTO vino__cellier SET id_bouteille = :id_bouteille, date_achat = :date_achat,
      garde_jusqua = :garde_jusqua, notes = :notes, prix = :prix, quantite = :quantite,
      millesime = :millesime
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
      SELECT id, nom FROM vino__bouteille
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
      UPDATE vino__cellier SET quantite = GREATEST(quantite + :nombre, 0) WHERE id = :id
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

  /* GESTION DES USAGERS 
     ======================== */

  /**
   * Connecter un usager
   * @param array $champs, tableau avec les champs courriel et mdp  
   * @return array|false ligne de la table, false sinon 
   */
  public function connecter($champs)

  {
    //var_dump($champs);
    $this->sql = "
      SELECT id_membre, nom, prenom, courriel, idprofil
      FROM membres
      WHERE courriel = :courriel AND mdp = SHA2(:mdp, 512)";

    return $this->obtenirLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

}