<?php
/* 
 * fichier Pages/logout.php 
 * 
 */

// Code lu seulement si cette page a été inclue dans l'index
if (isset($_SESSION['started'])) {
    // On réinitialise ses variables de session	
    unset($_SESSION['gcd_user_login']);
    $_SESSION['gcd_user_role'] = WebAppRoleGCD::VISITOR;
    unset($_SESSION['gcd_sideap_menu']);
    unset($_SESSION['gcd_user_name']);

    // Et de toute façon sa session est détruite
    $_SESSION = array();
    session_destroy();

    echo "<script type='text/javascript'>redirection ('index.php');</script>";
}


