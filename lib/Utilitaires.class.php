<?php
/**
 * Class MonSQL
 * Classe qui génère ma connection à MySQL à travers un singleton
 *
 *
 * @author Jonathan Martel
 * @version 1.0
 * @see : http://www.apprendre-php.com/tutoriels/tutoriel-45-singleton-instance-unique-d-une-classe.html
 *
 *
 */
class Utilitaires {
	
	/**
	 * 
	 *
	 * @param void
	 * @return Singleton
	 */
	public static function afficheTable($data) {
		$res = '';
		$header = '';
		foreach ($data as $cle => $enregistrement) 
		{
			$res .= '<tr>';
			$header = '';
			foreach ($enregistrement as $colonne => $valeur) {
				$header .= '<td>'. $colonne.'</td>';
				$res .= '<td>'. $valeur .'</td>';
			}
			$res .= '</tr>';
			$header = '<tr>' . $header .'</tr>';
		}
		$res = '<table>'. $header . $res . '</table>';
		return $res;
	}

}
?>