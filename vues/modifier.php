<div class="ajouter">

    <div class="nouvelleBouteille" vertical layout>
        Recherche : <input type="text" name="nom_bouteille">
        <ul class="listeAutoComplete">

        </ul>
            <div >
                <p>Nom : <span data-id="<?php echo $bouteilleModifiee['id_bouteille'] ?>" class="nom_bouteille"><?php echo $bouteilleModifiee['nom'] ?></span></p>
                <p>Millesime : <input type="text" name="millesime" value="<?php echo $bouteilleModifiee['millesime'] ?>"><span class="erreur_champ erreur_millesime"></span></p>
                <p>Quantite : <input type="text" name="quantite" value="<?php echo $bouteilleModifiee['quantite'] ?>"><span class="erreur_champ erreur_quantite"></span></p>
                <p>Date achat : <input type="date" name="date_achat" value="<?php echo $bouteilleModifiee['date_achat'] ?>"></p>
                <p>Prix : <input type="text" name="prix" value="<?php echo $bouteilleModifiee['prix'] ?>"><span class="erreur_champ erreur_prix"></span></p>
                <p>Garde : <input name="garde_jusqua" value="<?php echo $bouteilleModifiee['garde_jusqua'] ?>"></p>
                <p>Notes <input name="notes" value="<?php echo $bouteilleModifiee['notes'] ?>"></p>
                <input id="bouteille_id" type="hidden" data-id-cellier="<?php echo $bouteilleModifiee['id'] ?>">
            </div>
            <button name="modifierBouteilleCellier">Modifier la bouteille</button>
            <li><a href="?requete=accueil">Annuler</a></li>
        </div>
    </div>
</div>
