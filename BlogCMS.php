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

    public function login(string $email, string $pw): string {
        foreach ($users as $user) {
            if ($user->email === $email && $user->pw === $pw) {
                if ($user instanceof Admin) {
                    return "admin";
                } elseif ($user instanceof Editor) {
                    return "editor";
                } elseif ($user instanceof Author) {
                    return "author";
                } elseif ($user instanceof Moderator) {
                    return "moderator";
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

    public function __construct(int $id, string $title, string $content, string $status, string $createdAt, string $publishedAt, int $authorId, array $categories) {
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



$users = [];

$users[] = new Author(1, 'john_doe', 'john@example.com', 'password123');
$users[] = new Author(2, 'jane_smith', 'jane@example.com', 'secure456');
$users[] = new Author(3, 'tech_writer', 'tech@example.com', 'techpass789');
$users[] = new Author(4, 'content_creator', 'creator@example.com', 'createpass123');
$users[] = new Author(5, 'blogger_max', 'max@example.com', 'blogpass456');
$users[] = new Editor(6, 'editor_mike', 'mike@example.com', 'editpass123');
$users[] = new Editor(7, 'editor_sarah', 'sarah@example.com', 'editpass456');
$users[] = new Editor(8, 'editor_david', 'david@example.com', 'editpass789');
$users[] = new Editor(9, 'editor_emma', 'emma@example.com', 'editpass012');
$users[] = new Admin(10, 'admin_alex', 'alex@example.com', 'adminpass123');
$users[] = new Admin(11, 'admin_lisa', 'lisa@example.com', 'adminpass456');
$users[] = new Admin(12, 'admin_root', 'root@example.com', 'superadmin123');



$categories = [];

$categories[] = new Category(1, 'Technology');
$categories[] = new Category(2, 'Science');
$categories[] = new Category(3, 'Health & Wellness');
$categories[] = new Category(4, 'Business & Finance');
$categories[] = new Category(5, 'Entertainment');
$categories[] = new Category(6, 'Sports');
$categories[] = new Category(7, 'Education');
$categories[] = new Category(8, 'Travel');
$categories[] = new Category(9, 'Food & Cooking');


function getCategoriesByIds(array $ids, array $categories): array {
    $result = [];
    foreach ($categories as $category) {
        if (in_array($category->getId(), $ids)) {
            $result[] = $category;
        }
    }
    return $result;
}


$articles = [];
$articles[] = new Article(
    1,
    'The Future of Artificial Intelligence',
    'Artificial intelligence is rapidly evolving and transforming various industries...',
    'published',
    '2024-01-10 09:30:00',
    '2024-01-12 08:00:00',
    1, 
    getCategoriesByIds([1, 2, 7], $categories) 
);

$articles[] = new Article(
    2,
    'Healthy Eating Habits for Busy Professionals',
    'Maintaining a healthy diet while working long hours can be challenging...',
    'published',
    '2024-01-15 14:45:00',
    '2024-01-17 10:30:00',
    2, 
    getCategoriesByIds([3, 9], $categories) 
);

$articles[] = new Article(
    3,
    'Stock Market Trends in 2024',
    'The stock market shows interesting patterns as we enter the new year...',
    'published',
    '2024-01-05 11:20:00',
    '2024-01-08 09:15:00',
    1, 
    getCategoriesByIds([4], $categories) 
);

$articles[] = new Article(
    4,
    'PHP 8.3 New Features Overview',
    'PHP 8.3 introduces several new features and improvements...',
    'draft',
    '2024-01-20 16:10:00',
    '',
    3, 
    getCategoriesByIds([1, 7], $categories) 
);

$articles[] = new Article(
    5,
    'Best Travel Destinations for 2024',
    'Discover the most exciting travel destinations for the coming year...',
    'published',
    '2024-01-18 13:25:00',
    '2024-01-22 11:00:00',
    4, 
    getCategoriesByIds([8], $categories) 
);

$articles[] = new Article(
    6,
    'The Science Behind Good Sleep',
    'Understanding the science of sleep can help improve your rest...',
    'review',
    '2024-01-25 10:15:00',
    '', 
    2, 
    getCategoriesByIds([2, 3], $categories) 
);

$comments = [];

$comments[] = new Comment(
    1,
    'Great article! Very informative about AI trends.',
    '2024-01-13 14:30:00',
    5, 
    1  
);

$comments[] = new Comment(
    2,
    'Could you elaborate more on machine learning applications?',
    '2024-01-14 11:45:00',
    2,
    1 
);

$comments[] = new Comment(
    3,
    'Thanks for the healthy eating tips! Very practical advice.',
    '2024-01-18 09:20:00',
    8, 
    2 
);

$comments[] = new Comment(
    4,
    'As a finance professional, I found the market analysis very accurate.',
    '2024-01-09 13:40:00',
    6,
    3  
);

$comments[] = new Comment(
    5,
    'Looking forward to the PHP 8.3 article! When will it be published?',
    '2024-01-22 10:30:00',
    9,
    4  
);

$comments[] = new Comment(
    6,
    'Bali is definitely on my travel list for this year!',
    '2024-01-23 15:25:00',
    7, 
    5  
);

$comments[] = new Comment(
    7,
    'Sleep science is fascinating. More articles on this topic please!',
    '2024-01-26 12:10:00',
    5,
    6  
);

$comments[] = new Comment(
    8,
    'I\'ve been following these sleep tips and they really work!',
    '2024-01-27 14:50:00',
    3, 
    6  
);
$comments[] = new Comment(
    9,
    'I\'ve been following these sleep tips and they really don\'t work!',
    '2024-02-27 14:50:00',
    2, 
    6  
);

?>