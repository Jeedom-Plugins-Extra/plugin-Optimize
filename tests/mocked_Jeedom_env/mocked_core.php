<?php

/**
 * Classe inscrivant l'historique des actions effectuées lors des tests
 */
class MockedActions
{
    /**
     * @var array Liste des actions
     */
    public static $actionsList = array();

    /**
     * Ajoute une action à la liste
     *
     * @param string $action Nom de l'action
     * @param array $content Données de l'action
     */
    public static function add($action, $content = null)
    {
        array_push(self::$actionsList, array('action' => $action, 'content' => $content));
    }

    /**
     * Obtenir la liste des actions
     *
     * @return array Liste des actions
     */
    public static function get()
    {
        return self::$actionsList;
    }

    /**
     * Effacer la liste des actions
     */
    public static function clear()
    {
        self::$actionsList = array();
    }
}

/**
 * Classe définissant certaines variables pour orienter le comportement de Jeedom
 */
class JeedomVars
{
    /**
     * @var bool Valeur renvoyée par la fonction isConnect()
     */
    public static $isConnected = true;

    /**
     * @var array Tableau des réponses de la fonction init()
     */
    public static $initAnswers = array();
}

/**
 * Mock de la fonction d'inclusion d'un fichier
 *
 * @param string $folder Répertoire du fichier
 * @param string $name Nom du fichier
 * @param string $type Type de fichier
 * @param string $plugin Plugin si ce n'est pas un fichier du core
 */
function include_file($folder, $name, $type, $plugin = null)
{
    MockedActions::add('include_file', array('folder' => $folder, 'name' => $name, 'type' => $type, 'plugin' => $plugin));
}

/**
 * Mock de la fonction de test de connection de l'utilisateur
 * Renvoie la valeur stockée dans JeedomVars::$jeedomIsConnected
 *
 * @param string $user Utilisateur connecté (facultatif)
 *
 * @return bool Valeur de JeedomVars::$jeedomIsConnected
 */
function isConnect($user = null)
{
    return JeedomVars::$isConnected;
}

/**
 * Mock de la fonction d'initialisation d'une valeur
 * Renvoie la valeur correspondant à la clé du tableau JeedomVars::$initAnswers
 *
 * @param string $key Clé du tableau
 *
 * @return mixed Valeur de la clé du tableau JeedomVars::$initAnswers
 */
function init($key)
{
    return JeedomVars::$initAnswers[$key];
}

/**
 * Mock de la fonction de traduction
 * Renvoie le message en paramètre
 *
 * @param string $msg Chaine à traduire
 * @param string $file Fichier contenant la chaine à traduire
 *
 * @return string Chaine passée en paramètre
 */
function __($msg, $file = null)
{
    return $msg;
}

/**
 * Mock de la fonction d'affichage d'une exception
 * Renvoie le message en paramètre
 *
 * @param string $exceptionMsg Message à afficher
 *
 * @return string Message de l'exception
 */
function displayExeption($exceptionMsg)
{
    return displayException($exceptionMsg);
}

/**
 * Mock de la fonction d'affichage d'une exception
 * Renvoie le message en paramètre
 *
 * @param string $exceptionMsg Message à afficher
 *
 * @return string Message de l'exception
 */
function displayException($exceptionMsg)
{
    return $exceptionMsg;
}
