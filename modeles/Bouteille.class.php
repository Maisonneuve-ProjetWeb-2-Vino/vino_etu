<?php
/**
 * Class Bouteille
 * Cette classe possède les fonctions de gestion des bouteilles dans le cellier et des bouteilles dans le catalogue complet.
 * 
 * @author Jonathan Martel
 * @version 1.0
 * @update 2019-01-21
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 * 
 */
class Bouteille extends Modele {
	const TABLE = 'vino__bouteille';
    
	public function getListeBouteille()
	{
		
		$rows = Array();
		$res = $this->_db->query('Select * from '. self::TABLE);
		if($res->num_rows)
		{
			while($row = $res->fetch_assoc())
			{
				$rows[] = $row;
			}
		}
		
		return $rows;
	}
	
	public function getListeBouteilleCellier()
	{
		
		$rows = Array();
		$requete ='SELECT 
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
						from vino__cellier c 
						INNER JOIN vino__bouteille b ON c.id_bouteille = b.id
						INNER JOIN vino__type t ON t.id = b.type
						'; 
		if(($res = $this->_db->query($requete)) ==	 true)
		{
			if($res->num_rows)
			{
				while($row = $res->fetch_assoc())
				{
					$row['nom'] = trim(utf8_encode($row['nom']));
					$rows[] = $row;
				}
			}
		}
		else 
		{
			throw new Exception("Erreur de requête sur la base de donnée", 1);
			 //$this->_db->error;
		}
		
		
		
		return $rows;
	}
	
	/**
	 * Cette méthode permet de retourner les résultats de recherche pour la fonction d'autocomplete de l'ajout des bouteilles dans le cellier
	 * 
	 * @param string $nom La chaine de caractère à rechercher
	 * @param integer $nb_resultat Le nombre de résultat maximal à retourner.
	 * 
	 * @throws Exception Erreur de requête sur la base de données 
	 * 
	 * @return array id et nom de la bouteille trouvée dans le catalogue
	 */
       
	public function autocomplete($nom, $nb_resultat=10)
	{
		
		$rows = Array();
		$nom = $this->_db->real_escape_string($nom);
		$nom = preg_replace("/\*/","%" , $nom);
		 
		//echo $nom;
		$requete ='SELECT id, nom FROM vino__bouteille where LOWER(nom) like LOWER("%'. $nom .'%") LIMIT 0,'. $nb_resultat; 
		//var_dump($requete);
		if(($res = $this->_db->query($requete)) ==	 true)
		{
			if($res->num_rows)
			{
				while($row = $res->fetch_assoc())
				{
					$row['nom'] = trim(utf8_encode($row['nom']));
					$rows[] = $row;
					
				}
			}
		}
		else 
		{
			throw new Exception("Erreur de requête sur la base de données", 1);
			 
		}
		
		
		//var_dump($rows);
		return $rows;
	}
	
	
	/**
	 * Cette méthode ajoute une ou des bouteilles au cellier
	 * 
	 * @param Array $data Tableau des données représentants la bouteille.
	 * 
	 * @return Boolean Succès ou échec de l'ajout.
	 */
	public function ajouterBouteilleCellier($data)
	{
		//TODO : Valider les données.
		//var_dump($data);	
		
		$requete = "INSERT INTO vino__cellier(id_bouteille,date_achat,garde_jusqua,notes,prix,quantite,millesime) VALUES (".
		"'".$data->id_bouteille."',".
		"'".$data->date_achat."',".
		"'".$data->garde_jusqua."',".
		"'".$data->notes."',".
		"'".$data->prix."',".
		"'".$data->quantite."',".
		"'".$data->millesime."')";

        $res = $this->_db->query($requete);
        
		return $res;
	}
	
	
	/**
	 * Cette méthode change la quantité d'une bouteille en particulier dans le cellier
	 * 
	 * @param int $id id de la bouteille
	 * @param int $nombre Nombre de bouteille a ajouter ou retirer
	 * 
	 * @return Boolean Succès ou échec de l'ajout.
	 */
	public function modifierQuantiteBouteilleCellier($id, $nombre)
	{
		//TODO : Valider les données.
			
			
		$requete = "UPDATE vino__cellier SET quantite = GREATEST(quantite + ". $nombre. ", 0) WHERE id = ". $id;
		//echo $requete;
        $res = $this->_db->query($requete);
        
		return $res;
	}

	/**
	 * Récupère les données d'une bouteille d'un cellier, à partir de son id.
	 * 
	 * @param int $id id de la bouteille
	 * 
	 * @return array Les détails de la bouteille trouvée dans le cellier
	 */
	public function getBouteilleCellier($id)
	{
		$id = $this->_db->real_escape_string($id);

		$requete = 
			"SELECT vino__cellier.id, vino__cellier.id_bouteille, nom, date_achat, garde_jusqua, notes, prix, quantite, millesime FROM vino__cellier
			JOIN vino__bouteille ON vino__cellier.id_bouteille = vino__bouteille.id
			WHERE vino__cellier.id = $id";

		$res = $this->_db->query($requete);
		return $res->fetch_assoc();
	}

	/**
	 * Change les informations d'une bouteille en particulier dans le cellier.
	 * 
	 * @param object $data Objet contenant les valeurs modifiées de la bouteille
	 * 
	 * @return Boolean Succès ou échec de l'ajout.
	 */
	public function modifierBouteilleCellier($data) {
		$id_bouteille_cellier = $this->_db->real_escape_string($data->id_bouteille_cellier);
		$id_bouteille = $this->_db->real_escape_string($data->id_bouteille);
		$date_achat = empty($data->date_achat) ? 'NULL' : "'" .$this->_db->real_escape_string($data->date_achat) ."'";
		$garde_jusqua = $this->_db->real_escape_string($data->garde_jusqua);
		$notes = $this->_db->real_escape_string($data->notes);
		$prix = $this->_db->real_escape_string($data->prix);
		$quantite =  empty($data->quantite) ? 0 : $this->_db->real_escape_string($data->quantite);
		$millesime = empty($data->millesime) ? 'NULL' : $this->_db->real_escape_string($data->millesime);

		$requete = "UPDATE vino__cellier ".
			"SET id_bouteille = $id_bouteille, ".
			"date_achat = ".$date_achat.", ".
			"garde_jusqua = '".$garde_jusqua."', ".
			"notes = '".$notes."', ".
			"prix = '".$prix."', ".
			"quantite = ".$quantite.", ".
			"millesime = ".$millesime." ".
			"WHERE id = $id_bouteille_cellier";

		$res = $this->_db->query($requete);
        
		return $res;
	}
}


?>