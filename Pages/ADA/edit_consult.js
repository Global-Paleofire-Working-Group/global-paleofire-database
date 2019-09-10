/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var requete = new XMLHttpRequest();
requete.onload = function() {
 
//La variable à passer est alors contenue dans l'objet response et l'attribut responseText.
var mode_edit_consult = this.responseText;
};
requete.open(get, "edit_consult_db.php", false); //True pour que l'exécution du script continue pendant le chargement, false pour attendre.
requete.send();