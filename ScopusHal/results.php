<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
  <title>CouvertureHAL : Comparaison HAL vs Scopus</title>
  <meta name="Description" content="CouvertureHAL : Comparaison HAL vs Scopus">
  <link rel="stylesheet" type="text/css" media="screen" href="style.css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">  
</head>

<body>
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
<h1>Résultats</h1>

<b>Le script ci-dessous ne se fonde que sur la détection d'un titre identique (après suppression des caractères spéciaux et passage en minuscules)
pour identifier une référence Scopus avec un dépôt HAL.</b><br/><br/>

Récupération des résultats de HAL en cours...

<?php

function normalize($st) { 
    //return preg_replace('/\W+/', '', $st); 
    $st=strtr($st,' ()"-!?[]{}:,;./*+$^=\'\\','                       ');
    return preg_replace('/\s+/', '', $st);
    //strtr($st,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
} 


$halId=array();
$halTitles=array();
$halFullRef=array();
$halYears=array();
$halAuthors=array();
$halWhere=array();


if(isset($_POST['hal'])){
   $hal=$_POST['hal'];
   echo "<h2>Chargement de la requête HAL</h2>";
   echo "Requête : ".$hal;
   $contents = file_get_contents($hal);
   $results = json_decode($contents);
   
   $nbHAL=0;
   $nbHalPerYear=array();
   //echo "<br/><br/>Résultats :<ul>";
   foreach($results->response->docs as $entry){
      //The title of the HAL file will be the main key to look for the article in HAL, we now simplify it (lowercase, no punctuation or spaces, etc.)
      $encodedTitle=normalize(utf8_encode(mb_strtolower(utf8_decode($entry->title_s[0]))));
      //So we store all parameters using the simplified HAL title
      //We also use the DOI as a key if it is present:
      $halId[$encodedTitle]=($entry->halId_s);
      
      //Saving the DOI
      $doi=$entry->doiId_s;
      if(strlen($doi)>0){
         $halId[$doi]=($entry->doiId_s);
         //echo "<span style=\"background-color:blue\">DOI:".$doi."</span>";
      }
      
      //Saving the year
      $halYears[$encodedTitle]=($entry->producedDateY_i);
      if(strlen($doi)>0){$halYears[$doi]=($entry->producedDateY_i);}

      //Record number of HAL publications per year      
      if(array_key_exists(($entry->producedDateY_i),$nbHalPerYear)){
         $nbHalPerYear[$entry->producedDateY_i]+=1;
      } else {
         $nbHalPerYear[$entry->producedDateY_i]=1;
      }

      //Saving the title
      $halTitles[$encodedTitle]=($entry->title_s);
      if(strlen($doi)>0){$halTitles[$doi]=($entry->title_s);}

      //Saving the publication location: journal, conference or book
      if(strlen($entry->journalTitle_s)>0){
         $halWhere[$encodedTitle]=($entry->journalTitle_s);
         if(strlen($doi)>0){$halWhere[$doi]=($entry->journalTitle_s);}
      }
      if(strlen($entry->proceedings_s)>0){
         $halWhere[$encodedTitle]=($entry->proceedings_s);
         if(strlen($doi)>0){$halWhere[$doi]=($entry->proceedings_s);}
      }
      if(strlen($entry->bookTitle_s)>0){
         $halWhere[$encodedTitle]=($entry->bookTitle_s);
         if(strlen($doi)>0){$halWhere[$doi]=($entry->bookTitle_s);}
      }
      
      //Saving authors:
      $authors="";
      $initial = 1;
      foreach($entry->authFullName_s as $author){
         if ($initial==1){
            $authors = $author;
            $initial=0;
         } else {
            $authors = $authors.", ".$author;
         }
      }
      $halAuthors[$encodedTitle]=$authors;
      if(strlen($doi)>0){$halAuthors[$doi]=$authors;}

      //Saving full citation
      $halFullRef[$encodedTitle]=($entry->citationFull_s);
      if(strlen($doi)>0){$halFullRef[$doi]=($entry->citationFull_s);}
      //echo "<li>".$entry->halId_s." - ".normalize(utf8_encode(mb_strtolower(utf8_decode($entry->title_s[0]))))."</li>";      
      
      $nbHAL+=1;
   }
   echo "</ul>";
}

echo "<h2>Chargement du fichier Scopus</h2>";
ini_set('auto_detect_line_endings',TRUE);
$handle = fopen($_FILES['scopus']['tmp_name'],'r');
echo "<table>";
$nbScopus=-1;
$nbScopusPerYear=array();
$nbNotFoundPerYear=array();
$nbNotFound=0;
$papers=array();
while ( ($data = fgetcsv($handle) ) !== FALSE ) {
  //var_dump($data);
  if($nbScopus>-1){
     // Increment the number of Scopus papers for the year of this paper
     if(array_key_exists($data[2],$nbScopusPerYear)){
        $nbScopusPerYear[$data[2]]+=1;
     } else {
        $nbScopusPerYear[$data[2]]=1;
     }
     
     // Extract the Scopus title
     $scopusTitle=(utf8_encode(mb_strtolower(utf8_decode($data[1]))));
     
     // Extract the English and French title if found
     $englishTitle="";
     $frenchTitle="";
     $words=preg_split('/[\[]+/u',$scopusTitle);
     if(sizeof($words)>1){
        $englishTitle=normalize($words[0]);
        $frenchTitle=normalize($words[1]);     
     } else {
        $scopusTitle=normalize($scopusTitle);
     }
     $foundInHAL=FALSE;
     
     //echo "<li><span style=\"background-color:#FFEEEE\">"" (".$data[2].") ".$data[1]." - <i>".$data[3]."</i></span>";
     //echo "<li><span style=\"background-color:#EE0000\">".$data[11]."|-|".$scopusTitle."|-|".$englishTitle."|-|".$frenchTitle."</span></li>";
     
     // Trying to match with DOI
     if(array_key_exists($data[11],$halTitles)){
        echo "<tr><td></td><td>".$data[2]."</td><td>".$data[0]."</td><td>".$data[1]."</td><td>".$data[3]."</td></tr>";
        echo "<tr style=\"background-color:#EEFFEE\"><td><a href=\"http://hal.archives-ouvertes.fr/".$halId[$data[11]]."\">&hearts;</a></td><td>".$halYears[$data[11]]."</td><td>".$halAuthors[$data[11]]."</td><td>".$halTitles[$data[11]][0]."</td><td>".$halWhere[$data[11]]."</td><td>DOI match</td></tr>";
        $foundInHAL=TRUE;
        //echo "<li><span style=\"background-color:#00FF00\">Found DOI</span></li>";
     }          
     
     // Trying to match with full title
     if((!$foundInHAL) and (array_key_exists($scopusTitle,$halTitles))){
        echo "<tr><td></td><td>".$data[2]."</td><td>".$data[0]."</td><td>".$data[1]."</td><td>".$data[3]."</td></tr>";
        echo "<tr style=\"background-color:#EEFFEE\"><td><a href=\"http://hal.archives-ouvertes.fr/".$halId[$scopusTitle]."\">&hearts;</a></td><td>".$halYears[$scopusTitle]."</td><td>".$halAuthors[$scopusTitle]."</td><td>".$halTitles[$scopusTitle][0]."</td><td>".$halWhere[$scopusTitle]."</td><td>full title match</td></tr>";
        $foundInHAL=TRUE;
        //echo "<li><span style=\"background-color:#00FF00\">Found title</span></li>";
     }

     // Trying to match with english title
     if((!$foundInHAL) and (array_key_exists($englishTitle,$halTitles))){
        echo "<tr><td></td><td>".$data[2]."</td><td>".$data[0]."</td><td>".$data[1]."</td><td>".$data[3]."</td></tr>";
        echo "<tr style=\"background-color:#EEFFEE\"><td><a href=\"http://hal.archives-ouvertes.fr/".$halId[$englishTitle]."\">&hearts;</a></td><td>".$halYears[$englishTitle]."</td><td>".$halAuthors[$englishTitle]."</td><td>".$halTitles[$englishTitle][0]."</td><td>".$halWhere[$englishTitle]."</td><td>english title match</td></tr>";
        $foundInHAL=TRUE;
        //echo "<li><span style=\"background-color:#00FF00\">Found English title</span></li>";
     }
     
     // Trying to match with other language title
     if((!$foundInHAL) and (array_key_exists($frenchTitle,$halTitles))){
        echo "<tr><td></td><td>".$data[2]."</td><td>".$data[0]."</td><td>".$data[1]."</td><td>".$data[3]."</td></tr>";
        echo "<tr style=\"background-color:#EEFFEE\"><td><a href=\"http://hal.archives-ouvertes.fr/".$halId[$frenchTitle]."\">&hearts;</a></td><td>".$halYears[$frenchTitle]."</td><td>".$halAuthors[$frenchTitle]."</td><td>".$halTitles[$frenchTitle][0]."</td><td>".$halWhere[$frenchTitle]."</td><td>french title match</td></tr>";
        $foundInHAL=TRUE;
        //echo "<li><span style=\"background-color:#00FF00\">Found French title</span></li>";
     }

     if(!$foundInHAL){
        echo "<tr><td></td><td>".$data[2]."</td><td>".$data[0]."</td><td>".$data[1]."</td><td>".$data[3]."</td></tr>";
        $nbNotFound+=1;
        array_push($papers,$data);
        if(array_key_exists($data[2],$nbNotFoundPerYear)){
           $nbNotFoundPerYear[$data[2]]+=1;
        } else {
           $nbNotFoundPerYear[$data[2]]=1;
        }
     }
     echo "</li>";
  }
  $nbScopus+=1;
}
echo "</table>";
ini_set('auto_detect_line_endings',FALSE);


?>

<h2>Références de Scopus non trouvées dans HAL</h2>

<p><b>Attention, il est possible que la référence soit présente dans HAL mais qu'elle n'ait pas été trouvée en raison d'une légère différence dans le titre.</b></p>

<ul>
<?
foreach($papers as $data){
   echo "<li>".$data[0]." (".$data[2].") <a href=\"https://scholar.google.fr/scholar?hl=fr&q=".$st=strtr($data[1],'"<>','   ')."\">".$data[1]."</a> - <i>".$data[3]."</i></li>";
}
?>
</ul>


<h2>Auteurs des références de Scopus non trouvées dans HAL</h2>

<p>Vous pouvez utiliser le logiciel <a href="http://www.treecloud.org">TreeCloud</a> pour afficher une figure
résumant les auteurs les plus présents dans cette liste d'articles manquants sur HAL, et les sensibiliser
au dépôt dans cette archive ouverte.</p>
<ul>
<?
foreach($papers as $data){
   $formattedAuthors=$data[0].', ';
   $formattedAuthors=preg_replace('#\., #', '|', $formattedAuthors);
   $formattedAuthors=preg_replace('#, #', '_', $formattedAuthors);
   $formattedAuthors=preg_replace('# #', '_', $formattedAuthors);
   $formattedAuthors=preg_replace('#\.#', '_', $formattedAuthors);
   $formattedAuthors=preg_replace('#-#', '_', $formattedAuthors);
   $formattedAuthors=preg_replace('#__#', '_', $formattedAuthors);
   $formattedAuthors=preg_replace('#\|#', ' ', $formattedAuthors);
   echo $formattedAuthors." de de de de de de de de de de de de de de de de de de de de de de de de de <br/>";
}
?>
</ul>


<h2>Bilan quantitatif</h2>

<table border=1>
<?
echo "<tr><th>Année</th><th>sur HAL</th><th>sur Scopus</th><th>pourcentage de Scopus trouvé dans HAL</th></tr>";
$years = array_keys($nbScopusPerYear);
sort($years);
foreach($years as $year){
   echo "<tr><td>".$year."</td><td>".$nbHalPerYear[$year]."</td><td>".$nbScopusPerYear[$year]."</td><td>".(round(10000*($nbScopusPerYear[$year]-$nbNotFoundPerYear[$year])/($nbScopusPerYear[$year]))/100)."%</td></tr>";
}

?>
</table>

<br/><br/>
<a href="index.php">Retour à l'accueil du site</a>


</body></html>