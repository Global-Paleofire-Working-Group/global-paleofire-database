<?php
/**
 * Description of data_securisation
 *
 * @author cbon2
 */
class data_securisation
{
    // data securisation before recording in db
    // SQL INJECTION
    public static function toBdd($string)
    {
        // on récupère la connexion à la bdd gérer en global
        global $bdd_gcd;
        // On regarde si le type de string est un nombre entier (int)
        if(ctype_digit($string))
        {
            $string = intval($string);
        }
        // Pour tous les autres types
        else
        {
            $string = $bdd_gcd->real_escape_string($string);
            $string = addcslashes($string, '%_');
        }

        return $string;

    }
    // data securation before displaying in html
    // XSS (Cross-Site Scripting)
    public static function tohtml($string)
    {
            return htmlentities($string);
    }
}
?>
