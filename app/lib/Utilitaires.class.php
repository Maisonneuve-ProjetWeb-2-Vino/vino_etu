<?php

class Utilitaires {

    const COULEUR_ROUGE = "rgb(143,72,72)";
    const COULEUR_ROSE = "rgb(181,112,125)";
    const COULEUR_BLANC = "rgb(134,132,124)";

    /**
     * Calcule le nombre total de bouteilles pour tous types de vins.
     * 
     * @param array tableau contenant les quantités de bouteilles pour chaque type de vin
     * @return int total de bouteilles
     */
    public static function calculerTotalBouteilles($quantites) {

        $total = 0;
        foreach($quantites as $type => $quantite) {
            $total += $quantite;
        }

        return $total;
    }

    /**
     * Calcule les proportions en pourcentage pour chaque type de vin.
     * 
     * @param array $quantites tableau contenant les quantités de bouteilles pour chaque type de vin
     * @throws Exception si la quantité totale de bouteilles est de 0
     * @return array tableau contenant les proportions de bouteilles pour chaque type de vin  
     */
    public static function calculerProportionsTypes($quantites) {
    
        $total = Utilitaires::calculerTotalBouteilles($quantites);

        // Pour éviter une division par 0
        if ($total == 0){
            throw new Exception("La quantité totale de bouteilles ne peut pas être 0");
        }

        // Calcul des proportions
        $proportions = [];
        foreach($quantites as $type => $quantite) {
            $proportions[$type] = $quantite / $total * 100;
        }

        return $proportions;
    }

  /**
   * Retourne l'argument d'un conic-gradient à utiliser pour former un diagramme circulaire 
   * de la proportion de bouteilles.
   * 
   * @param $proportions_cellier tableau contenant les proportions de bouteilles pour chaque type de vin  
   * @return string argument du conic-gradient
   */
    public static function formerDiagrammeCirculaire($proportions_cellier) {

        $finRouge = $proportions_cellier['Rouge'];
        $finRose =  $proportions_cellier['Rouge'] + $proportions_cellier['Rosé'];

        $couleur_rouge = self::COULEUR_ROUGE;
        $couleur_rose = self::COULEUR_ROSE;
        $couleur_blanc = self::COULEUR_BLANC;

        return "$couleur_rouge 0% $finRouge%, $couleur_rose $finRouge% $finRose%, $couleur_blanc $finRose% 100%";

    }

}
