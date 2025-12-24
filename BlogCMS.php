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

class Article {
    private int $id;
    private string $title;
    private string $content;
    private string $status;
    private string $createdAt;
    private string $publishedAt;
    private Author $author;
    private Category $categories = [];


    public function __construct ($id, $title, $content , $status, $createdAt, $publishedAt) {
        this->id = $id;
        this->username = $username;
        this->email = $email;
        this->pw = $pw;
        this->createdAt = $createdAt;
        this->publishedAt = $publishedAt;
    }

    


}



?>