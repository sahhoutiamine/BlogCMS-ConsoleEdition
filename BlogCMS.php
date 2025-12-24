<?php 

// User Class 
class User {
    protected int $id;
    protected string $username;
    protected string $email;
    protected string $pw;

    public function __construct(int $id, string $username, string $email, string $pw) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->pw = $pw;
    }
}

// Author Class 
class Author extends User {
    public function __construct(int $id, string $username, string $email, string $pw) {
        parent::__construct($id, $username, $email, $pw);
    }
}

// Moderator Class 
class Moderator extends User {
    public function __construct(int $id, string $username, string $email, string $pw) {
        parent::__construct($id, $username, $email, $pw);
    }
}

// Admin Class 
class Admin extends Moderator {
    public function __construct(int $id, string $username, string $email, string $pw) {
        parent::__construct($id, $username, $email, $pw);
    }
}

// Editor Class 
class Editor extends Moderator {
    public function __construct(int $id, string $username, string $email, string $pw) {
        parent::__construct($id, $username, $email, $pw);
    }
}

// Category class
class Category {
    private int $id;
    private string $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

// Article class
class Article {
    private int $id;
    private string $title;
    private string $content;
    private string $status;
    private string $createdAt;
    private string $publishedAt;
    private int $authorId;
    private array $categories = []; 

    public function __construct(int $id, string $title, string $content, string $status, 
                               string $createdAt, string $publishedAt, int $authorId, array $categories) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->publishedAt = $publishedAt;
        $this->authorId = $authorId;
        $this->categories = $categories;
    }
}

// Comment class
class Comment {
    private int $id;
    private string $content;
    private string $createdAt;
    private int $authorId;
    private int $articleId;

    public function __construct(int $id, string $content, string $createdAt, int $authorId, int $articleId) {
        $this->id = $id;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->authorId = $authorId;
        $this->articleId = $articleId; 
    }
}

?>