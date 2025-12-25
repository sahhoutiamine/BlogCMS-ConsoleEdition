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
    
    public function getId(): int { 
        return $this->id; 
    }
    public function getUsername(): string {
         return $this->username; 
    }
    public function getEmail(): string {
         return $this->email;
    }
    public function getPassword(): string { 
        return $this->pw; 
    }

    public static function login(array $users, string $email, string $pw): string {
        foreach ($users as $user) {
            if ($user->getEmail() === $email && $user->getPassword() === $pw) {
                if ($user instanceof Admin) {
                    return "admin";
                } elseif ($user instanceof Editor) {
                    return "editor";
                } elseif ($user instanceof Author) {
                    return "author";
                } else {
                    return "user";
                }
            }
        }
        return "invalid"; 
    }
}

// Author Class 
class Author extends User {
    private array $articles = []; 

    public function __construct(int $id, string $username, string $email, string $pw, array $articles = []) {
        parent::__construct($id, $username, $email, $pw);
        $this->articles = $articles;
    }
    
    public function getArticles(): array {
        return $this->articles;
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
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
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
    private array $categories = []; 
    private array $comments = []; 

    public function __construct(int $id, string $title, string $content, string $status, string $createdAt, string $publishedAt = '', array $categories = [], array $comments = []) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->publishedAt = $publishedAt;
        $this->categories = $categories;
        $this->comments = $comments;
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getCategories(): array {
        return $this->categories;
    }
    
    public function getComments(): array {
        return $this->comments;
    }
}


class Comment {
    private int $id;
    private string $content;
    private string $createdAt;
    private string $author_username; 

    public function __construct(int $id, string $content, string $createdAt, string $author_username) {
        $this->id = $id;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->author_username = $author_username; 
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getContent(): string {
        return $this->content;
    }
    
    public function getAuthorUsername(): string {
        return $this->author_username;
    }
}



$users = [
    new Author(1, 'john_doe', 'john@example.com', 'password123', [
        new Article(1, 'The Future of Artificial Intelligence', 'Artificial intelligence is rapidly evolving and transforming various industries...', 'published', '2024-01-10 09:30:00', '2024-01-12 08:00:00', [
            new Category(1, 'Technology'),
            new Category(2, 'Science'),
            new Category(7, 'Education')
        ], [
            new Comment(1, 'Great article! Very informative about AI trends.', '2024-01-13 14:30:00', 'jane_smith'),
            new Comment(2, 'Could you elaborate more on machine learning applications?', '2024-01-14 11:45:00', 'tech_writer')
        ])
    ]),
    
    new Author(2, 'jane_smith', 'jane@example.com', 'secure456', [
        new Article(6, 'The Science Behind Good Sleep', 'Understanding the science of sleep can help improve your rest...', 'review', '2024-01-25 10:15:00', '', [
            new Category(2, 'Science'),
            new Category(3, 'Health & Wellness')
        ], [
            new Comment(7, 'Sleep science is fascinating. More articles on this topic please!', '2024-01-26 12:10:00', 'john_doe'),
            new Comment(8, 'I\'ve been following these sleep tips and they really work!', '2024-01-27 14:50:00', 'content_creator'),
            new Comment(9, 'I\'ve been following these sleep tips and they really don\'t work!', '2024-02-27 14:50:00', 'tech_writer')
        ])
    ]),
    
    new Author(3, 'tech_writer', 'tech@example.com', 'techpass789', []),
    
    new Author(4, 'content_creator', 'creator@example.com', 'createpass123', []),
    new Author(5, 'blogger_max', 'max@example.com', 'blogpass456', [
        new Article(5, 'Best Travel Destinations for 2024', 'Discover the most exciting travel destinations for the coming year...', 'published', '2024-01-18 13:25:00', '2024-01-22 11:00:00', [
            new Category(8, 'Travel')
        ], [
            new Comment(6, 'Bali is definitely on my travel list for this year!', '2024-01-23 15:25:00', 'tech_writer')
        ])
    ]),
    
    new Editor(6, 'editor_mike', 'mike@example.com', 'editpass123'),
    
    new Editor(7, 'editor_sarah', 'sarah@example.com', 'editpass456'),
    
    new Editor(8, 'editor_david', 'david@example.com', 'editpass789'),
    
    new Editor(9, 'editor_emma', 'emma@example.com', 'editpass012'),
    
    new Admin(10, 'admin_alex', 'alex@example.com', 'adminpass123'),
    new Admin(11, 'admin_lisa', 'lisa@example.com', 'adminpass456'),
    new Admin(12, 'admin_root', 'root@example.com', 'superadmin123')
];

$result = User::login($users, 'john@example.com', 'password123');
echo "Login result: " . $result . "\n"; 

?>