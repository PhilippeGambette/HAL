<html>
<head>
  <title>CouvertureHAL : Comparaison HAL vs Scopus</title>
  <meta name="Description" content="CouvertureHAL : Comparaison HAL vs Scopus">
  <link rel="stylesheet" type="text/css" media="screen" href="style.css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">  
</head>
<body>
<h1>CouvertureHAL : Comparaison HAL vs Scopus</h1>
<?php


/*
    CouvertureHAL 1.2 - 2016-10-03
    Copyright (C) 2016 Philippe Gambette (HAL_UPEMLV@univ-mlv.fr)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


?>
<div style="background-color:#AAAAFF;width:900px;padding:10px;font-family:calibri,verdana">
<p><b><a href="https://github.com/CCSDForge/HAL/tree/master/ScopusHal">CouvertureHAL</a> 1.2 - 2016-10-03</b><br/>
Copyright &copy; 2016 Philippe Gambette <a href="http://hal-upec-upem.archives-ouvertes.fr">HAL_UPEMLV@univ-mlv.fr</a>.<br/>
Développé sous <a href="http://www.gnu.org/licenses/gpl-3.0.fr.html">licence GPL</a> (<a href="CouvertureHAL.zip">sources</a>).
</p>
<p>Il est recommandé de <b>ne pas utiliser cet outil uniquement de manière quantitative</b>, la base de données Scopus
n'étant <b>pas représentative de la totalité des productions de recherche</b>. Toutefois, il est possible
d'utiliser cet outil pour <b>identifier des publications de revues ou conférences indexées par Scopus
qui pourraient être manquantes dans HAL</b> (l'auteur de ce programme décline toute responsabilité dans le cas où
l'article serait effectivement présent dans HAL, avec un titre légèrement différent) et <b>inciter
leurs auteurs à les y déposer</b>.</p>
<p>C'est l'objectif de la "liste des auteurs" qui apparaît sur la page de résultats
(<a href="CouvertureHAL%20_%20Comparaison%20HAL%20vs%20Scopus.html">exemple de résultats ici</a>), permettant suite à un
traitement par le logiciel <a href="http://www.treecloud.org">TreeCloud</a> d'afficher
<a href="ExempleAuteurs.pdf">une figure des auteurs les plus présents dans cette liste de publications manquantes sur HAL</a> 
(couleur rouge : publis manquantes les plus récentes ; la taille reflète le nombre de publications manquantes),
et de les sensibiliser au dépôt dans cette archive ouverte.</p>
</div>

<h2>Mode d'emploi</h2>
<p>
<b>Pour le moment, seule la comparaison avec Scopus est disponible.</b><br/><br/>
Pour l'utiliser :
<ul>
<li>rendez-vous <a href="http://www.scopus.com/search/form.url?display=advanced&amp;clear=t&amp;origin=SearchAffiliationLookup">sur le moteur de recherche avancé de Scopus</a> ;</li>
<li>lancez une requête ; par exemple, pour les publications du <a href="http://hal-upec-upem.archives-ouvertes.fr/LIGM">LIGM</a>, <tt>AFFIL (ligm OR (institut gaspard monge) OR "IGM LabInfo" OR "Labinfo IGM" OR (informatique gaspard monge))</tt> ;</li>
<li>dans la page de résultats qui apparaît, cliquez sur le bouton "Select all" à gauche de "Export", puis sur "Export", en choisissant alors "CSV" et "Citation information only", et enregistrez le fichier sur votre disque dur ;</li>
<li>chargez le fichier en utilisant le bouton "Choisissez un fichier" ci-dessous ;</li>
<li>ajoutez dans le cadre "Requête HAL" ci-dessous une requête HAL permettant de récupérer au format JSON les publications parmi lesquelles vous chercherez les publications de SCOPUS ; par exemple (une documentation et quelques exercices sur l'API HAL sont accessibles <a href="https://hal-upec-upem.archives-ouvertes.fr/page/api">depuis cette page</a>) :
<ul>
<li>pour récupérer les dépôts HAL de l'unité de recherche <b>ERUDITE</b>, <tt><a href="http://api.archives-ouvertes.fr/search/?q=collCode_s:ERUDITE&amp;rows=5000&amp;fl=docType_s,docid,halId_s,authFullName_s,title_s,journalTitle_s,volume_s,issue_s,page_s,producedDateY_i,proceedings_s,files_s,label_s,citationFull_s,bookTitle_s,doiId_s">http://api.archives-ouvertes.fr/search/?q=collCode_s:LIGM%20AND%20(submitType_s:file%20OR%20arxivId_s:?*)&amp;rows=5000&amp;fl=docType_s,docid,halId_s,authFullName_s,title_s,journalTitle_s,volume_s,issue_s,page_s,producedDateY_i,proceedings_s,files_s,label_s,citationFull_s,bookTitle_s,doiId_s</a></tt></li>
<li>pour récupérer les dépôts HAL du <b>LIGM</b> avec <b>texte intégral ou lien vers un dépôt ArXiV</b>, <tt><a href="http://api.archives-ouvertes.fr/search/?q=collCode_s:LIGM%20AND%20(submitType_s:file%20OR%20arxivId_s:?*)&amp;rows=5000&amp;fl=docType_s,docid,halId_s,authFullName_s,title_s,journalTitle_s,volume_s,issue_s,page_s,producedDateY_i,proceedings_s,files_s,label_s,citationFull_s,bookTitle_s,doiId_s">http://api.archives-ouvertes.fr/search/?q=collCode_s:LIGM%20AND%20(submitType_s:file%20OR%20arxivId_s:?*)&amp;rows=5000&amp;fl=docType_s,docid,halId_s,authFullName_s,title_s,journalTitle_s,volume_s,issue_s,page_s,producedDateY_i,proceedings_s,files_s,label_s,citationFull_s,bookTitle_s,doiId_s</a></tt></li>
</ul>
</li>
</ul>
</p>

<h2>Paramétrage</h2>
<p>
<form enctype="multipart/form-data" action="results.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
Envoyez le fichier résultat de Scopus (10 Mo maximum, voir ci-dessus le "mode d'emploi") : <input name="scopus" type="file" /><br/><br/>
Requête HAL :<br/>
<input type="text" name="hal" size=100 value="http://api.archives-ouvertes.fr/search/?q=collCode_s:LIGM%20AND%20(submitType_s:file%20OR%20arxivId_s:?*)&amp;rows=5000&amp;fl=docType_s,docid,halId_s,authFullName_s,title_s,journalTitle_s,volume_s,issue_s,page_s,producedDateY_i,proceedings_s,files_s,label_s,citationFull_s,bookTitle_s,doiId_s"><br/><br/>
<input type="submit" value="Envoyer">
</form>
</p>


</body>
</html>