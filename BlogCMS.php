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







?>