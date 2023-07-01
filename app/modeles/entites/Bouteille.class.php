<?php

/**
 * Classe de l'entité Bouteille
 *
 */
class Bouteille
{
    private $id_bouteille;
    private $date_achat;
    private $garde_jusqua;
    private $notes;
    private $prix;
    private $quantite;
    private $millesime;

    private $erreurs = array();

    /**
     * Constructeur de la classe
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
     * @param string $prop, nom de la propriété
     * @return property value
     */     
    public function __get($prop) {
        return $this->$prop;
    }

    // Getters explicites nécessaires au moteur de templates TWIG
    public function getId_bouteille()       { return $this->id_bouteille; }
    public function getDate_achat()       { return $this->date_achat; }
    public function getGarde_jusqua()       { return $this->garde_jusqua; }
    public function getNotes()       { return $this->notes; }
    public function getPrix()       { return $this->prix; }
    public function getQuantite()       { return $this->quantite; }
    public function getMillesime()       { return $this->millesime; }

    /**
     * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
     * @param string $prop, nom de la propriété
     * @param $val, contenu de la propriété à mettre à jour    
     */   
    public function __set($prop, $val) {
        $setProperty = 'set'.ucfirst($prop);
        $this->$setProperty($val);
    }

    /**
     * Mutateur de la propriété bouteille_id
     * @param int $bouteille_id
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
     * Mutateur de la propriété date_achat
     * @param string $date_achat
     * @return $this
     */    
    public function setDate_achat($date_achat) {
        unset($this->erreurs['date_achat']);
        $regExp = "/^\d{4}-\d{2}-\d{2}$/";
        $currentDate = date("Y-m-d");
        if (!preg_match($regExp, $date_achat)) {
            $this->erreurs['date_achat'] = "Date d'achat invalide";
        }
        else {
            if (!empty($date_achat)) {
                if (strtotime($date_achat) > strtotime($currentDate)) {
                    $this->erreurs['date_achat'] = "Date d'achat supérieure à la date du jour";
                }
            }
        }
        $this->date_achat = $date_achat;
        return $this;
    }

    /**
     * Mutateur de la propriété garde_jusqua
     * @param string $garde_jusqua
     * @return $this
     */    
    public function setGarde_jusqua($garde_jusqua) {
        unset($this->erreurs['garde_jusqua']);
        $this->garde_jusqua = $garde_jusqua;
        return $this;
    }

    /**
     * Mutateur de la propriété notes
     * @param int $notes
     * @return $this
     */    
    public function setNotes($notes) {
        unset($this->erreurs['notes']);
        $this->notes = $notes;
        return $this;
    }

    /**
     * Mutateur de la propriété prix
     * @param int $prix
     * @return $this
     */  
    public function setPrix($prix) {
        unset($this->erreurs['prix']);
        $regExp = '/^\d+(\.\d{1,2})?$/';
        if (!empty($prix)) {
            if (!preg_match($regExp, $prix)) {
                $this->erreurs['prix'] = "Format de prix incorrect.";
            }
        }

        $this->prix = $prix;
        return $this;
    }

    /**
     * Mutateur de la propriété quantite
     * @param int $quantite
     * @return $this
     */    
    public function setQuantite($quantite) {
        unset($this->erreurs['quantite']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $quantite)) {
            $this->erreurs['quantite'] = "Quantité incorrecte";
        }
        $this->quantite = $quantite;
        return $this;
    }

    /**
     * Mutateur de la propriété millesime
     * @param int $millesime
     * @return $this
     */    
    public function setMillesime($millesime) {
        unset($this->erreurs['millesime']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $millesime)) {
            $this->erreurs['millesime'] = "Format du millésime incorrect";
        }
        $this->millesime = $millesime;
        return $this;
    }
}