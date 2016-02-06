<?php
/**
* @brief class Commentaire
* @author David Lachambre
* @version 1.0
* @update 2015-12-14
*/
class Commentaire {
    
    /**
    * @var string $id Id du commentaire
    * @access private
    */
    private $id;
    
    /**
    * @var string $texte Texte du commentaire
    * @access private
    */
    private $texte;
    
    /**
    * @var string $vote Vote de l'utilisateur ayant laissé le commentaire
    * @access private
    */
    private $vote;
    
    /**
    * @var string $langue Langue originale du commentaire
    * @access private
    */
    private $langue;
    
    /**
    * @var string $authorise Détermine si le commentaire a passé l'étape de l'audit
    * @access private
    */
    private $authorise;

    /**
    * @var object Connection à la BDD
    */
    private static $database;
    
	function __construct() {
		
        if (!isset(self::$database)) {
            
            self::$database = BaseDeDonnees::getInstance();
        }
	}
    
    /**
    * @brief Méthode qui assigne des valeurs aux propriétés du commentaire
    * @param integer $id
    * @param string $texte
    * @param integer $vote
    * @param string $langue
    * @param boolean $authorise
    * @return void
    */
    public function setData($id, $texte, $vote, $langue, $authorise) {
        
		$this->id = $id;
		$this->texte = $texte;
		$this->vote = $vote;
		$this->langue = $langue;
		$this->authorise = $authorise;
	}
		
	/**
    * @brief Méthode qui récupère les valeurs des propriétés de cet objet
    * @access public
    * @return array
    */
	public function getData() {
        
        $resultat = array("id"=>$this->id, "texte"=>$this->texte, "vote"=>$this->vote, "langue"=>$this->langue, "authorise"=>$this->authorise);
        
        return $resultat;
	}
    
    /**
    * @brief Méthode qui récupère tous les commentaires associés à une oeuvre
    * @param $id integer
    * @param $langue string
    * @access public
    * @return array
    */
    public function getCommentairesByOeuvre($idOeuvre, $langue) {
        
        $infoCommentaires = array();
        
        self::$database->query('SELECT * FROM Commentaires JOIN Utilisateurs ON Utilisateurs.idUtilisateur = Commentaires.idUtilisateur WHERE Commentaires.idOeuvre = :id AND Commentaires.authorise = true AND Commentaires.langueCommentaire = :langue');
        
        //Lie les paramètres aux valeurs
        self::$database->bind(':id', $idOeuvre);
        self::$database->bind(':langue', $langue);
        
        if ($commentairesBDD = self::$database->resultset()) {
            foreach ($commentairesBDD as $commentaire) {
                $unCommentaire = array("texteCommentaire"=>$commentaire["texteCommentaire"], "voteCommentaire"=>$commentaire["voteCommentaire"], "nomUsager"=>$commentaire["nomUsager"], "photoProfil"=>$commentaire["photoProfil"]);
                $infoCommentaires[] = $unCommentaire;
            }
        }
        return $infoCommentaires;
    }
    
     public function ajoutCommentairesByOeuvre($idOeuvre, $langue, $texte, $vote, $idUtilisateur, $authorise) {
        $msgUtilisateur = "";
        $erreurs = false;
       
         
        if (isset($_POST["ajoutCommentaire"])) {      
           if (!empty($_POST["commentaireAjout"])){
              
       self::$database->query('INSERT INTO Commentaires ( texteCommentaire, voteCommentaire, langueCommentaire, authorise, idOeuvre, idUtilisateur) VALUES (:texte, :vote, :langue, :authorise, :id, :idUtilisateur)');
        
        //Lie les paramètres aux valeurs
        self::$database->bind(':id', $idOeuvre);
        self::$database->bind(':langue', $langue);
        self::$database->bind(':texte', $texte);
        self::$database->bind(':vote', $vote);
        self::$database->bind(':idUtilisateur', $idUtilisateur);
        self::$database->bind(':authorise', $authorise);
        self::$database->execute();
        $msgUtilisateur = "Succes";       
            //$msgUtilisateur = var_dump($texte, $vote, $langue, $authorise, $idOeuvre, $idUtilisateur);
        }
        else {
        
         $msgUtilisateur = "ne laissez rien en blanc";
        } 
      
    }
         return $msgUtilisateur;
     }
}    
?>