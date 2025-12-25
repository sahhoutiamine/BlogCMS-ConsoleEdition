<?php

// ==================== Classes du Système ====================

class Utilisateur {
    protected $id_utilisateur;
    protected $username;
    protected $email;
    protected $password;
    protected $createdAt;
    protected $lastLogin;
    protected $role;
    protected static $nextId = 1;

    public function __construct($username, $email, $password, $role = 'user') {
        $this->id_utilisateur = self::$nextId++;
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->createdAt = new DateTime();
        $this->role = $role;
    }

    public function login($email, $password) {
        if ($this->email === $email && password_verify($password, $this->password)) {
            $this->lastLogin = new DateTime();
            return true;
        }
        return false;
    }

    public function getId() { return $this->id_utilisateur; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
}

class Author extends Utilisateur {
    private $bio;

    public function __construct($username, $email, $password, $bio = '') {
        parent::__construct($username, $email, $password, 'author');
        $this->bio = $bio;
    }

    public function createArticle($titre, $content, &$articles) {
        $article = new Article($titre, $content, $this);
        $articles[] = $article;
        return $article;
    }

    public function getBio() { return $this->bio; }
}

class Moderateur extends Utilisateur {
    public function __construct($username, $email, $password) {
        parent::__construct($username, $email, $password, 'moderateur');
    }

    public function approveComment(&$comment) {
        $comment->setApproved(true);
        return true;
    }

    public function createCategory($name, $parent, &$categories) {
        $category = new Categorie($name, $parent);
        $categories[] = $category;
        return $category;
    }

    public function publishArticle(&$article) {
        $article->publish();
        return true;
    }
}

class Admin extends Moderateur {
    public function __construct($username, $email, $password) {
        parent::__construct($username, $email, $password);
        $this->role = 'admin';
    }

    public function createUser($username, $email, $password, $role, &$users) {
        switch($role) {
            case 'author': $user = new Author($username, $email, $password); break;
            case 'moderateur': $user = new Moderateur($username, $email, $password); break;
            case 'admin': $user = new Admin($username, $email, $password); break;
            default: $user = new Utilisateur($username, $email, $password, $role);
        }
        $users[] = $user;
        return $user;
    }
}

class Categorie {
    private $id_categorie;
    private $name;
    private $parent;
    private static $nextId = 1;

    public function __construct($name, $parent = null) {
        $this->id_categorie = self::$nextId++;
        $this->name = $name;
        $this->parent = $parent;
    }

    public function getId() { return $this->id_categorie; }
    public function getName() { return $this->name; }
    public function getParent() { return $this->parent; }
}

class Article {
    private $id_article;
    private $titre;
    private $content;
    private $status;
    private $author;
    private $categories = [];
    private $comments = [];
    private static $nextId = 1;

    public function __construct($titre, $content, $author) {
        $this->id_article = self::$nextId++;
        $this->titre = $titre;
        $this->content = $content;
        $this->author = $author;
        $this->status = 'draft';
    }

    public function addCategory($category) {
        $this->categories[] = $category;
    }

    public function addComment($comment) {
        $this->comments[] = $comment;
    }

    public function publish() {
        $this->status = 'published';
    }

    public function getId() { return $this->id_article; }
    public function getTitre() { return $this->titre; }
    public function getContent() { return $this->content; }
    public function getStatus() { return $this->status; }
    public function getAuthor() { return $this->author; }
    public function getCategories() { return $this->categories; }
    public function getComments() { return $this->comments; }
    public function setTitre($t) { $this->titre = $t; }
    public function setContent($c) { $this->content = $c; }
}

class Commentaire {
    private $id_commentaire;
    private $libelle;
    private $description;
    private $article;
    private $author;
    private $approved = false;
    private static $nextId = 1;

    public function __construct($libelle, $description, $article, $author) {
        $this->id_commentaire = self::$nextId++;
        $this->libelle = $libelle;
        $this->description = $description;
        $this->article = $article;
        $this->author = $author;
    }

    public function getId() { return $this->id_commentaire; }
    public function getLibelle() { return $this->libelle; }
    public function getDescription() { return $this->description; }
    public function getAuthor() { return $this->author; }
    public function isApproved() { return $this->approved; }
    public function setApproved($a) { $this->approved = $a; }
}

// ==================== Interface CLI ====================

class CMS {
    private $users = [];
    private $articles = [];
    private $categories = [];
    private $comments = [];
    private $currentUser = null;

    public function __construct() {
        $admin = new Admin('admin', 'admin@cms.com', 'admin123');
        $author = new Author('author', 'author@cms.com', 'pass123', 'Écrivain');
        $mod = new Moderateur('mod', 'mod@cms.com', 'mod123');
        
        $this->users = [$admin, $author, $mod];
        
        $tech = new Categorie('Technologie');
        $prog = new Categorie('Programmation', $tech);
        $this->categories = [$tech, $prog];
        
        $article = $author->createArticle('Intro PHP', 'Contenu sur PHP', $this->articles);
        $article->addCategory($prog);
    }

    public function run() {
        system('clear');
        echo "╔═══════════════════════════════════════╗\n";
        echo "║      SYSTÈME CMS - INTERFACE CLI     ║\n";
        echo "╚═══════════════════════════════════════╝\n\n";
        
        while (true) {
            if (!$this->currentUser) {
                $this->menuLogin();
            } else {
                $this->menuPrincipal();
            }
        }
    }

    private function menuLogin() {
        echo "\n┌─── MENU CONNEXION ───┐\n";
        echo "1. Se connecter\n";
        echo "2. Voir utilisateurs\n";
        echo "3. Quitter\n";
        echo "Choix: ";
        
        $c = trim(fgets(STDIN));
        
        switch ($c) {
            case '1':
                $this->login();
                break;
            case '2':
                $this->afficherUsers();
                break;
            case '3':
                exit("\nAu revoir!\n");
        }
    }

    private function login() {
        echo "\nEmail: ";
        $email = trim(fgets(STDIN));
        echo "Mot de passe: ";
        $password = trim(fgets(STDIN));
        
        foreach ($this->users as $user) {
            if ($user->login($email, $password)) {
                $this->currentUser = $user;
                system('clear');
                echo "✅ Bienvenue {$user->getUsername()} ({$user->getRole()})\n";
                sleep(1);
                return;
            }
        }
        echo "❌ Échec connexion!\n";
        sleep(1);
    }

    private function menuPrincipal() {
        system('clear');
        echo "╔═══════════════════════════════════════╗\n";
        echo "║          MENU PRINCIPAL               ║\n";
        echo "╚═══════════════════════════════════════╝\n";
        echo "User: {$this->currentUser->getUsername()} ({$this->currentUser->getRole()})\n\n";
        
        echo "1. Gestion Articles\n";
        echo "2. Gestion Catégories\n";
        echo "3. Gestion Commentaires\n";
        echo "4. Gestion Utilisateurs\n";
        echo "5. Statistiques\n";
        echo "6. Déconnexion\n";
        echo "7. Quitter\n";
        echo "\nChoix: ";
        
        $c = trim(fgets(STDIN));
        
        switch ($c) {
            case '1': $this->menuArticles(); break;
            case '2': $this->menuCategories(); break;
            case '3': $this->menuCommentaires(); break;
            case '4': $this->menuUtilisateurs(); break;
            case '5': $this->stats(); break;
            case '6': $this->currentUser = null; break;
            case '7': exit("\nAu revoir!\n");
        }
    }

    private function menuArticles() {
        system('clear');
        echo "┌─── GESTION ARTICLES ───┐\n";
        echo "1. Lister articles\n";
        echo "2. Créer article\n";
        echo "3. Voir détails\n";
        echo "4. Publier article\n";
        echo "5. Retour\n";
        echo "Choix: ";
        
        $c = trim(fgets(STDIN));
        
        switch ($c) {
            case '1': $this->listerArticles(); break;
            case '2': $this->creerArticle(); break;
            case '3': $this->detailsArticle(); break;
            case '4': $this->publierArticle(); break;
            case '5': return;
        }
        $this->menuArticles();
    }

    private function listerArticles() {
        echo "\n═══ LISTE ARTICLES ═══\n";
        foreach ($this->articles as $a) {
            echo "\n[ID: {$a->getId()}] {$a->getTitre()}\n";
            echo "Auteur: {$a->getAuthor()->getUsername()}\n";
            echo "Status: {$a->getStatus()}\n";
            echo "Contenu: " . substr($a->getContent(), 0, 50) . "...\n";
            $cats = array_map(fn($c) => $c->getName(), $a->getCategories());
            echo "Catégories: " . implode(', ', $cats) . "\n";
        }
        echo "\n[Entrée pour continuer]";
        fgets(STDIN);
    }

    private function creerArticle() {
        if (!($this->currentUser instanceof Author)) {
            echo "\n❌ Réservé aux auteurs!\n";
            sleep(1);
            return;
        }
        
        echo "\nTitre: ";
        $titre = trim(fgets(STDIN));
        echo "Contenu: ";
        $content = trim(fgets(STDIN));
        
        $article = $this->currentUser->createArticle($titre, $content, $this->articles);
        
        echo "\nAjouter catégories? (o/n): ";
        if (trim(fgets(STDIN)) === 'o') {
            $this->listerCategories();
            echo "IDs catégories (séparés par virgule): ";
            $ids = explode(',', trim(fgets(STDIN)));
            foreach ($ids as $id) {
                foreach ($this->categories as $cat) {
                    if ($cat->getId() == trim($id)) {
                        $article->addCategory($cat);
                    }
                }
            }
        }
        
        echo "\n✅ Article créé (ID: {$article->getId()})\n";
        sleep(2);
    }

    private function detailsArticle() {
        $this->listerArticles();
        echo "\nID article: ";
        $id = intval(trim(fgets(STDIN)));
        
        foreach ($this->articles as $a) {
            if ($a->getId() === $id) {
                system('clear');
                echo "═══ DÉTAILS ARTICLE ═══\n\n";
                echo "ID: {$a->getId()}\n";
                echo "Titre: {$a->getTitre()}\n";
                echo "Auteur: {$a->getAuthor()->getUsername()}\n";
                echo "Status: {$a->getStatus()}\n";
                echo "\nContenu:\n{$a->getContent()}\n\n";
                
                $cats = array_map(fn($c) => $c->getName(), $a->getCategories());
                echo "Catégories: " . implode(', ', $cats) . "\n\n";
                
                echo "Commentaires (" . count($a->getComments()) . "):\n";
                foreach ($a->getComments() as $com) {
                    echo "  • {$com->getLibelle()} - {$com->getAuthor()->getUsername()}\n";
                    echo "    {$com->getDescription()}\n";
                    echo "    Approuvé: " . ($com->isApproved() ? 'Oui' : 'Non') . "\n\n";
                }
                
                echo "\n1. Ajouter commentaire\n2. Retour\nChoix: ";
                if (trim(fgets(STDIN)) === '1') {
                    $this->ajouterCommentaire($a);
                }
                return;
            }
        }
        echo "❌ Article introuvable!\n";
        sleep(1);
    }

    private function ajouterCommentaire($article) {
        echo "\nLibellé: ";
        $lib = trim(fgets(STDIN));
        echo "Description: ";
        $desc = trim(fgets(STDIN));
        
        $com = new Commentaire($lib, $desc, $article, $this->currentUser);
        $this->comments[] = $com;
        $article->addComment($com);
        
        echo "✅ Commentaire ajouté!\n";
        sleep(1);
    }

    private function publierArticle() {
        if (!($this->currentUser instanceof Moderateur)) {
            echo "\n❌ Réservé aux modérateurs!\n";
            sleep(1);
            return;
        }
        
        $this->listerArticles();
        echo "\nID article à publier: ";
        $id = intval(trim(fgets(STDIN)));
        
        foreach ($this->articles as $a) {
            if ($a->getId() === $id) {
                $this->currentUser->publishArticle($a);
                echo "✅ Article publié!\n";
                sleep(1);
                return;
            }
        }
        echo "❌ Article introuvable!\n";
        sleep(1);
    }

    private function menuCategories() {
        system('clear');
        echo "┌─── GESTION CATÉGORIES ───┐\n";
        echo "1. Lister catégories\n";
        echo "2. Créer catégorie\n";
        echo "3. Retour\n";
        echo "Choix: ";
        
        $c = trim(fgets(STDIN));
        
        switch ($c) {
            case '1': $this->listerCategories(); echo "\n[Entrée]"; fgets(STDIN); break;
            case '2': $this->creerCategorie(); break;
            case '3': return;
        }
        $this->menuCategories();
    }

    private function listerCategories() {
        echo "\n═══ CATÉGORIES ═══\n";
        foreach ($this->categories as $cat) {
            $parent = $cat->getParent() ? " ← " . $cat->getParent()->getName() : "";
            echo "[{$cat->getId()}] {$cat->getName()}{$parent}\n";
        }
    }

    private function creerCategorie() {
        if (!($this->currentUser instanceof Moderateur)) {
            echo "\n❌ Réservé aux modérateurs!\n";
            sleep(1);
            return;
        }
        
        echo "\nNom: ";
        $name = trim(fgets(STDIN));
        
        $parent = null;
        $this->listerCategories();
        echo "ID parent (0 = aucun): ";
        $pid = intval(trim(fgets(STDIN)));
        
        if ($pid > 0) {
            foreach ($this->categories as $cat) {
                if ($cat->getId() === $pid) {
                    $parent = $cat;
                    break;
                }
            }
        }
        
        $cat = $this->currentUser->createCategory($name, $parent, $this->categories);
        echo "✅ Catégorie créée (ID: {$cat->getId()})\n";
        sleep(1);
    }

    private function menuCommentaires() {
        system('clear');
        echo "┌─── GESTION COMMENTAIRES ───┐\n";
        echo "1. Lister commentaires\n";
        echo "2. Approuver commentaire\n";
        echo "3. Retour\n";
        echo "Choix: ";
        
        $c = trim(fgets(STDIN));
        
        switch ($c) {
            case '1': $this->listerCommentaires(); break;
            case '2': $this->approuverCommentaire(); break;
            case '3': return;
        }
        $this->menuCommentaires();
    }

    private function listerCommentaires() {
        echo "\n═══ COMMENTAIRES ═══\n";
        foreach ($this->comments as $com) {
            echo "\n[ID: {$com->getId()}] {$com->getLibelle()}\n";
            echo "Par: {$com->getAuthor()->getUsername()}\n";
            echo "{$com->getDescription()}\n";
            echo "Approuvé: " . ($com->isApproved() ? '✓' : '✗') . "\n";
        }
        echo "\n[Entrée]";
        fgets(STDIN);
    }

    private function approuverCommentaire() {
        if (!($this->currentUser instanceof Moderateur)) {
            echo "\n❌ Réservé aux modérateurs!\n";
            sleep(1);
            return;
        }
        
        $this->listerCommentaires();
        echo "\nID commentaire: ";
        $id = intval(trim(fgets(STDIN)));
        
        foreach ($this->comments as $com) {
            if ($com->getId() === $id) {
                $this->currentUser->approveComment($com);
                echo "✅ Commentaire approuvé!\n";
                sleep(1);
                return;
            }
        }
        echo "❌ Commentaire introuvable!\n";
        sleep(1);
    }

    private function menuUtilisateurs() {
        system('clear');
        echo "┌─── GESTION UTILISATEURS ───┐\n";
        echo "1. Lister utilisateurs\n";
        echo "2. Créer utilisateur\n";
        echo "3. Retour\n";
        echo "Choix: ";
        
        $c = trim(fgets(STDIN));
        
        switch ($c) {
            case '1': $this->afficherUsers(); break;
            case '2': $this->creerUser(); break;
            case '3': return;
        }
        $this->menuUtilisateurs();
    }

    private function afficherUsers() {
        echo "\n═══ UTILISATEURS ═══\n";
        foreach ($this->users as $u) {
            echo "[{$u->getId()}] {$u->getUsername()} ({$u->getRole()})\n";
            echo "    Email: {$u->getEmail()}\n";
        }
        echo "\n[Entrée]";
        fgets(STDIN);
    }

    private function creerUser() {
        if (!($this->currentUser instanceof Admin)) {
            echo "\n❌ Réservé aux admins!\n";
            sleep(1);
            return;
        }
        
        echo "\nUsername: ";
        $user = trim(fgets(STDIN));
        echo "Email: ";
        $email = trim(fgets(STDIN));
        echo "Mot de passe: ";
        $pass = trim(fgets(STDIN));
        echo "Rôle (user/author/moderateur/admin): ";
        $role = trim(fgets(STDIN));
        
        $u = $this->currentUser->createUser($user, $email, $pass, $role, $this->users);
        echo "✅ Utilisateur créé (ID: {$u->getId()})\n";
        sleep(2);
    }

    private function stats() {
        system('clear');
        echo "╔═══════════════════════════════╗\n";
        echo "║        STATISTIQUES           ║\n";
        echo "╚═══════════════════════════════╝\n\n";
        
        echo "👥 Utilisateurs: " . count($this->users) . "\n";
        echo "📝 Articles: " . count($this->articles) . "\n";
        $pub = count(array_filter($this->articles, fn($a) => $a->getStatus() === 'published'));
        echo "   ↳ Publiés: {$pub}\n";
        echo "   ↳ Brouillons: " . (count($this->articles) - $pub) . "\n";
        echo "📁 Catégories: " . count($this->categories) . "\n";
        echo "💬 Commentaires: " . count($this->comments) . "\n";
        $appr = count(array_filter($this->comments, fn($c) => $c->isApproved()));
        echo "   ↳ Approuvés: {$appr}\n";
        echo "   ↳ En attente: " . (count($this->comments) - $appr) . "\n";
        
        echo "\n[Entrée pour continuer]";
        fgets(STDIN);
    }
}

// ==================== LANCEMENT ====================
$cms = new CMS();
$cms->run();

?>