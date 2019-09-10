<?php
global $bdd_en_cours;

// Connexion à la base de données
// par défaut on se connecte sur la base de travail
connectionBaseInProgress();

function connectionBaseInProgress(){
    // Connexion à la base de données de travail
    global $bdd_en_cours;
    $bdd_en_cours = BDD_IN_PROGRESS_GCD_DATABASE;
    global $bdd_gcd;
    $bdd_gcd = new mysqli(BDD_IN_PROGRESS_GCD_HOSTNAME, BDD_IN_PROGRESS_GCD_LOGIN, BDD_IN_PROGRESS_GCD_PASSWORD, BDD_IN_PROGRESS_GCD_DATABASE);
    if (isset($bdd_gcd)) {
        if ($bdd_gcd->connect_errno) {
            throw new Exception("Echec lors de la connexion à MySQL : ".BDD_IN_PROGRESS_GCD_DATABASE."(" . $bdd_gcd->connect_errno . ") " . $bdd_gcd->connect_error, E_ERROR);
        }
        $bdd_gcd->set_charset('utf8');
    } else {
        throw new Exception("Echec lors de la connexion à MySQL".BDD_IN_PROGRESS_GCD_DATABASE, E_ERROR);
    }
}

function connectionBaseVersionned(){
    // Connexion à la base de données de travail
    global $bdd_en_cours;
    $bdd_en_cours = BDD_GCD_DATABASE;

    global $bdd_gcd;
    // Connexion à la base de données
    $bdd_gcd = new mysqli(BDD_GCD_HOSTNAME, BDD_GCD_LOGIN, BDD_GCD_PASSWORD, BDD_GCD_DATABASE);
    if (isset($bdd_gcd)) {
        if ($bdd_gcd->connect_errno) {
            throw new Exception("Echec lors de la connexion à MySQL : (" .BDD_GCD_DATABASE. $bdd_gcd->connect_errno . ") " . $bdd_gcd->connect_error, E_ERROR);
        }
        $bdd_gcd->set_charset('utf8');
    } else {
        throw new Exception("Echec lors de la connexion à MySQL".BDD_GCD_DATABASE, E_ERROR);
    }
}

function connectionBaseWebapp(){
    // Connexion à la base de données de travail
    global $bdd_en_cours;
    $bdd_en_cours = BDD_USER_DATABASE;
    global $bdd_gcd;
    $bdd_gcd = new mysqli(BDD_USER_HOSTNAME, BDD_USER_LOGIN, BDD_USER_PASSWORD, BDD_USER_DATABASE);
    if (isset($bdd_gcd)) {
        if ($bdd_gcd->connect_errno) {
            throw new Exception("Echec lors de la connexion à MySQL : ".BDD_IN_PROGRESS_GCD_DATABASE."(" . $bdd_gcd->connect_errno . ") " . $bdd_gcd->connect_error, E_ERROR);
        }
        $bdd_gcd->set_charset('utf8');
    } else {
        throw new Exception("Echec lors de la connexion à MySQL".BDD_IN_PROGRESS_GCD_DATABASE, E_ERROR);
    }
}

function getLabelBaseDeDonnées(){
    global $bdd_en_cours;
    return $bdd_en_cours;    
}

function  closeCurrentDBConnexion()
{
    global $bdd_gcd;
    $bdd_gcd->close();
}