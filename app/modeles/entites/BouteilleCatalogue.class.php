<?php

/**
 * Classe de l'entité BouteilleCatalogue
 *
 */
class BouteilleCatalogue
{
    private $id_bouteille;
    private $nom;
    private $prix_saq;
    private $annee;
    private $pays;
    private $type;
    private $format;
    private $cepage;
    private $particularite;
    private $appellation;
    private $degreAlcool;
    private $origine;
    private $producteur;
    private $region;
    private $tauxSucre;
    private $produitQuebec;
    private $note;

    private $erreurs = array();

    /**
     * Constructeur de la classe
     * 
     * @param array $proprietes, tableau associatif des propriétés 
     *
     */ 
    public function __construct($proprietes = []) {
        $t = array_keys($proprietes);
        foreach ($t as $nom_propriete) {
            $this->__set($nom_propriete, $proprietes[$nom_propriete]);
        } 
    }

    /**
     * Accesseur magique d'une propriété de l'objet
     * 
     * @param string $prop, nom de la propriété
     * @return property value
     */     
    public function __get($prop) {
        return $this->$prop;
    }

    // Getters explicites nécessaires au moteur de templates TWIG
    public function getId_bouteille()       { return $this->id_bouteille;	}
    public function getNom()       { return $this->nom; }
    public function getPrix_saq()       { return $this->prix_saq; }
    public function getAnnee()       { return $this->annee; }
    public function getPays()       { return $this->pays; }
    public function getType()       { return $this->type; }
    public function getFormat()       { return $this->format; }
    public function getCepage()       { return $this->cepage; }
    public function getParticularite()       { return $this->particularite; }
    public function getAppellation()        { return $this->appellation; }
    public function getDegreAlcool()       { return $this->degreAlcool; }
    public function getPrigine()       { return $this->origine; }
    public function getProducteur()       { return $this->producteur; }
    public function getPrix()       { return $this->prix; }
    public function getRegion()       { return $this->region; }
    public function getTauxSucre()       { return $this->tauxSucre; }
    public function getProduitQuebec()       { return $this->produitQuebec; }
    public function getNote()       { return $this->note; }

    /**
     * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
     * 
     * @param string $prop, nom de la propriété
     * @param $val, contenu de la propriété à mettre à jour    
     */   
    public function __set($prop, $val) {
        $setProperty = 'set'.ucfirst($prop);
        $this->$setProperty($val);
    }

    /**
     * Mutateur de la propriété id_bouteille
     * 
     * @param int $id_bouteille
     * @return $this
     */    
    public function setId_bouteille($id_bouteille) {
        unset($this->erreurs['id_bouteille']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $id_bouteille)) {
            $this->erreurs['id_bouteille'] = "Numéro de bouteille incorrect.";
        }
        $this->id_bouteille = $id_bouteille;
        return $this;
    }

    /**
     * Mutateur de la propriété nom
     * 
     * @param int $nom
     * @return $this
     */    
    public function setNom($nom) {
        unset($this->erreurs['nom']);
        if (empty($nom)) {
            $this->erreurs['nom'] = "Nom de bouteille vide.";
        }
        $this->nom = $nom;
        return $this;
    }
    
    /**
     * Mutateur de la propriété prix_saq
     * 
     * @param int $prix_saq
     * @return $this
     */  
    public function setPrix_saq($prix_saq) {
        unset($this->erreurs['prix_saq']);
        $regExp = '/^\d+(\.\d{1,2})?$/';
        if (!empty($prix_saq)) {
            if (!preg_match($regExp, $prix_saq)) {
                $this->erreurs['prix_saq'] = "Format de prix incorrect.";
            }
        }
        $this->prix_saq = $prix_saq;
        return $this;
    }

    /**
     * Mutateur de la propriété annee
     * @param int $annee
     * @return $this
     */    
    public function setAnnee($annee) {
        unset($this->erreurs['annee']);
        $regExp = '/^[1-9]\d*$/';
        if (!empty($annee)) {
            if (!preg_match($regExp, $annee)) {
                $this->erreurs['annee'] = "Format de l'année incorrect";
            }
        }
        $this->annee = $annee;
        return $this;
    }

    /**
     * Mutateur de la propriété pays
     * 
     * @param int $pays
     * @return $this
     */    
    public function setPays($pays) {
        unset($this->erreurs['pays']);
        $regExp = '/^[0-9]\d*$/';
        if (!empty($pays)) {
            if (!preg_match($regExp, $pays)) {
                $this->erreurs['pays'] = "Numéro de pays incorrect.";
            }
        }

        $this->pays = $pays;
        return $this;
    }

    /**
     * Mutateur de la propriété type
     * 
     * @param int $type
     * @return $this
     */    
    public function setType($type) {
        unset($this->erreurs['type']);
        $this->type = $type;
        return $this;
    }

    /**
     * Mutateur de la propriété format
     * 
     * @param int $format
     * @return $this
     */    
    public function setFormat($format) {
        unset($this->erreurs['format']);
        $this->format = $format;
        return $this;
    }

    /**
     * Mutateur de la propriété cepage
     * 
     * @param int $cepage
     * @return $this
     */    
    public function setCepage($cepage) {
        unset($this->erreurs['cepage']);
        $this->cepage = $cepage;
        return $this;
    }

    /**
     * Mutateur de la propriété particularite
     * 
     * @param int $particularite
     * @return $this
     */    
    public function setParticularite($particularite) {
        unset($this->erreurs['particularite']);
        $this->particularite = $particularite;
        return $this;
    }

    /**
     * Mutateur de la propriété appellation
     * 
     * @param int $appellation
     * @return $this
     */    
    public function setAppellation($appellation) {
        unset($this->erreurs['appellation']);
        $this->appellation = $appellation;
        return $this;
    }

    /**
     * Mutateur de la propriété degreAlcool
     * 
     * @param int $degreAlcool
     * @return $this
     */    
    public function setDegreAlcool($degreAlcool) {
        unset($this->erreurs['degreAlcool']);
        $this->degreAlcool = $degreAlcool;
        return $this;
    }

    /**
     * Mutateur de la propriété origine
     * @param int $origine
     * @return $this
     */    
    public function setOrigine($origine) {
        unset($this->erreurs['origine']);
        $this->origine = $origine;
        return $this;
    }

    /**
     * Mutateur de la propriété producteur
     * @param int $producteur
     * @return $this
     */    
    public function setProducteur($producteur) {
        unset($this->erreurs['producteur']);
        $this->producteur = $producteur;
        return $this;
    }

    /**
     * Mutateur de la propriété region
     * @param int $region
     * @return $this
     */    
    public function setRegion($region) {
        unset($this->erreurs['region']);
        $this->region = $region;
        return $this;
    }

    /**
     * Mutateur de la propriété tauxSucre
     * @param int $tauxSucre
     * @return $this
     */    
    public function setTauxSucre($tauxSucre) {
        unset($this->erreurs['tauxSucre']);
        $this->tauxSucre = $tauxSucre;
        return $this;
    }

    /**
     * Mutateur de la propriété produitQuebec
     * @param int $produitQuebec
     * @return $this
     */    
    public function setProduitQuebec($produitQuebec) {
        unset($this->erreurs['produitQuebec']);
        $this->produitQuebec = $produitQuebec;
        return $this;
    }

    /**
     * Mutateur de la propriété note
     * 
     * @param int $note
     * @return $this
     */    
    public function setNote($note) {
        unset($this->erreurs['note']);
        $regExp = '/^[0-5]+$/';
        if (!empty($note)) {
            if (!preg_match($regExp, $note)) {
                $this->erreurs['note'] = "La note doit être comprise entre 0 et 5";
            }
        }
        $this->note = $note;
        return $this;
    }
    
}
