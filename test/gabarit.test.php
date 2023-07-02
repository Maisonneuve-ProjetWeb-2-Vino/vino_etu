<!DOCTYPE html >
<html lang="fr">

	<head>

		<title>Test unitaire</title>
		<meta charset="UTF-8" />
		<link href="../css/global.css" rel="stylesheet" type="text/css" />
	</head>

	<body>
		<div id="header">
			<h1>Test - Modèles</h1>
		</div>
		<div id="contenu">

			<h2>Connection DB</h2>
			<?php

			$connect = MonSQL::getInstance();
			if ($connect != null) {
				echo "Connection mysqli fonctionnelle";
			} else {
				echo "Erreur de connection mysqli";
			}
			?>

			<h2>getListeBouteille</h2>
			<?php
			/*
			$bout = new Bouteille();
			$listeBouteille = $bout -> getListeBouteille();
			echo Utilitaires::afficheTable($listeBouteille);
			?>
			<h2>getListeBouteilleCellier</h2>
			<?php

			$bout = new Bouteille();
			try{
				$listeBouteilleCellier = $bout -> getListeBouteilleCellier();
				echo Utilitaires::afficheTable($listeBouteilleCellier);	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
			*/
			?>
			
			<h2>Bouteille::autocomplete($nom, $nb_resultat=10)</h2>
			<h3>$nom = "ch*teau", $nb_resultat = 10</h3>
			<?php

			$bout = new Bouteille();
			try{
				$nom = "ch*teau";
				$nb_resultat = 10;
				$listeBouteilleCellier = $bout -> autocomplete($nom, 10);
				echo Utilitaires::afficheTable($listeBouteilleCellier);	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
			?>
			<h3>$nom = "chateau", $nb_resultat = 10</h3>
			<?php

			$bout = new Bouteille();
			try{
				$nom = "chateau";
				$nb_resultat = 10;
				$listeBouteilleCellier = $bout -> autocomplete($nom, 10);
				echo Utilitaires::afficheTable($listeBouteilleCellier);	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
			
			?>
			
			
			
			<h2>Bouteille::getListeBouteilleCellier()</h2>
			<h3></h3>
			<?php

			$bout = new Bouteille();
			try{
				$listeBouteilleCellier = $bout -> getListeBouteilleCellier();
				echo Utilitaires::afficheTable($listeBouteilleCellier);	
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
			?>
			
		</div>
		<div id="footer">

		</div>
	</body>
</html>

