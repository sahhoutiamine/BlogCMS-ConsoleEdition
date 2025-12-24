<?php 

// User Class 
class User {
    protected int $id;
    protected string $username;
    protected string $email;
    protected string $pw;


    public function __construct ($id, $username, $email , $pw) {
        this->id = $id;
        this->username = $username;
        this->email = $email;
        this->pw = $pw;
    }

    // public function login ($email, $pw) {

    // }


}

// category class
class Category {
    private int $id;
    private string $name;



    public function __construct ($id, $name) {
        this->id = $id;
        this->name = $name;
       

    }

    


}
// article class
class Article {
    private int $id;
    private string $title;
    private string $content;
    private string $status;
    private string $createdAt;
    private string $publishedAt;
    private int $authorId;
    private Category $categories = [];


    public function __construct ($id, $title, $content , $status, $createdAt, $publishedAt, $authorId, $categories) {
        this->id = $id;
        this->title = $title;
        this->content = $content;
        this->status = $status;
        this->createdAt = $createdAt;
        this->publishedAt = $publishedAt;
        this->authorId = $authorId;
        this->categories = $categories;

    }

    


}
// comment class
class Comment {
    private int $id;
    private string $content;
    private string $createdAt;
    private int $authorId;
    private int $articleId;


    public function __construct ($id, $content , $createdAt, $authorId, $articleId) {
        this->id = $id;
        this->content = $content;
        this->createdAt = $createdAt;
        this->authorId = $authorId;
        this->createdAt = $createdAt;
        this->articleId = $articleId;
    }

    


}



?>