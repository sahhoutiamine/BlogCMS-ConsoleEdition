<?php
// test.php - Comprehensive test file for all methods

require_once('BlogCMS.php'); 

class TestSuite {
    private array $users;
    private int $passed = 0;
    private int $failed = 0;
    
    public function __construct(array $users) {
        $this->users = $users;
    }
    
    private function test($description, $expected, $actual): bool {
        $passed = $expected === $actual;
        $status = $passed ? "✓ PASS" : "✗ FAIL";
        
        echo "Test: $description\n";
        echo "Expected: " . (is_array($expected) ? print_r($expected, true) : $expected) . "\n";
        echo "Actual: " . (is_array($actual) ? print_r($actual, true) : $actual) . "\n";
        echo "Status: $status\n";
        echo str_repeat("-", 60) . "\n";
        
        if ($passed) {
            $this->passed++;
        } else {
            $this->failed++;
        }
        
        return $passed;
    }
    
    private function testType($description, $expectedType, $actual): bool {
        $passed = $actual instanceof $expectedType;
        $status = $passed ? "✓ PASS" : "✗ FAIL";
        
        echo "Test: $description\n";
        echo "Expected type: $expectedType\n";
        echo "Actual type: " . get_class($actual) . "\n";
        echo "Status: $status\n";
        echo str_repeat("-", 60) . "\n";
        
        if ($passed) {
            $this->passed++;
        } else {
            $this->failed++;
        }
        
        return $passed;
    }
    
    public function runAllTests(): void {
        echo "=== STARTING COMPREHENSIVE TEST SUITE ===\n\n";
        
        $this->testUserMethods();
        $this->testAuthorMethods();
        $this->testArticleMethods();
        $this->testCommentMethods();
        $this->testCategoryMethods();
        $this->testLoginSystem();
        $this->testModeratorHierarchy();
        
        echo "\n=== TEST SUMMARY ===\n";
        echo "Total tests passed: {$this->passed}\n";
        echo "Total tests failed: {$this->failed}\n";
        echo "Success rate: " . round(($this->passed / ($this->passed + $this->failed)) * 100, 2) . "%\n";
    }
    
    private function testUserMethods(): void {
        echo "=== TESTING USER CLASS METHODS ===\n\n";
        
        $user = new User(100, 'test_user', 'test@example.com', 'testpass');
        
        // Test getter methods
        $this->test("User getId()", 100, $user->getId());
        $this->test("User getUsername()", 'test_user', $user->getUsername());
        $this->test("User getEmail()", 'test@example.com', $user->getEmail());
        $this->test("User getPassword()", 'testpass', $user->getPassword());
    }
    
    private function testAuthorMethods(): void {
        echo "\n=== TESTING AUTHOR CLASS METHODS ===\n\n";
        
        // Get an author from the users array
        $author = $this->users[0]; // john_doe
        
        // Test getArticles()
        $articles = $author->getArticles();
        $this->test("Author getArticles() returns array", true, is_array($articles));
        $this->test("Author has articles", true, count($articles) > 0);
        
        // Test createArticle()
        $newArticle = new Article(100, 'New Test Article', 'Test content', 'draft', '2024-03-01 10:00:00');
        $initialCount = count($author->getArticles());
        $author->createArticle($newArticle);
        $this->test("Author createArticle() increases article count", $initialCount + 1, count($author->getArticles()));
        
        // Test findArticle()
        $foundArticle = $author->findArticle(1);
        $this->testType("Author findArticle() returns Article", Article::class, $foundArticle);
        $this->test("Author findArticle() finds correct article", 'The Future of Artificial Intelligence', $foundArticle->getTitle());
        
        // Test updateArticle()
        $updateResult = $author->updateArticle(1, 'Updated Title', 'Updated content');
        $this->test("Author updateArticle() returns true", true, $updateResult);
        
        // Verify update worked
        $updatedArticle = $author->findArticle(1);
        $this->test("Article title updated", 'Updated Title', $updatedArticle->getTitle());
        
        // Test deleteArticle()
        $deleteResult = $author->deleteArticle(100); // Delete the test article we created
        $this->test("Author deleteArticle() returns true", true, $deleteResult);
        $this->test("Author deleteArticle() reduces article count", $initialCount, count($author->getArticles()));
        
        // Test delete non-existent article
        $deleteResult = $author->deleteArticle(999);
        $this->test("Author deleteArticle() on non-existent returns false", false, $deleteResult);
    }
    
    private function testArticleMethods(): void {
        echo "\n=== TESTING ARTICLE CLASS METHODS ===\n\n";
        
        $article = new Article(
            200,
            'Test Article',
            'Test content',
            'draft',
            '2024-03-01 10:00:00',
            '2024-03-02 12:00:00',
            [
                new Category(1, 'Technology'),
                new Category(2, 'Science')
            ],
            [
                new Comment(1, 'First comment', '2024-03-01 11:00:00', 'user1'),
                new Comment(2, 'Second comment', '2024-03-01 12:00:00', 'user2')
            ]
        );
        
        // Test getter methods
        $this->test("Article getId()", 200, $article->getId());
        $this->test("Article getTitle()", 'Test Article', $article->getTitle());
        $this->test("Article getCategories() count", 2, count($article->getCategories()));
        $this->test("Article getComments() count", 2, count($article->getComments()));
        
        // Test setter methods
        $article->setTitle('Updated Article Title');
        $article->setContent('Updated article content');
        $this->test("Article setTitle() works", 'Updated Article Title', $article->getTitle());
        
        // Test findComment()
        $foundComment = $article->findComment(1);
        $this->testType("Article findComment() returns Comment", Comment::class, $foundComment);
        $this->test("Article findComment() finds correct comment", 'First comment', $foundComment->getContent());
        
        // Test createComment()
        $newComment = new Comment(3, 'Third comment', '2024-03-01 13:00:00', 'user3');
        $initialCount = count($article->getComments());
        $article->createComment($newComment);
        $this->test("Article createComment() increases comment count", $initialCount + 1, count($article->getComments()));
        
        // Test updateComment()
        $updateResult = $article->updateComment(1, 'Updated first comment');
        $this->test("Article updateComment() returns true", true, $updateResult);
        
        // Verify update worked
        $updatedComment = $article->findComment(1);
        $this->test("Comment content updated", 'Updated first comment', $updatedComment->getContent());
        
        // Test deleteComment()
        $deleteResult = $article->deleteComment(3); // Delete the test comment we created
        $this->test("Article deleteComment() returns true", true, $deleteResult);
        $this->test("Article deleteComment() reduces comment count", $initialCount, count($article->getComments()));
        
        // Test delete non-existent comment
        $deleteResult = $article->deleteComment(999);
        $this->test("Article deleteComment() on non-existent returns false", false, $deleteResult);
    }
    
    private function testCommentMethods(): void {
        echo "\n=== TESTING COMMENT CLASS METHODS ===\n\n";
        
        $comment = new Comment(300, 'Test comment content', '2024-03-01 10:00:00', 'test_author');
        
        // Test getter methods
        $this->test("Comment getId()", 300, $comment->getId());
        $this->test("Comment getContent()", 'Test comment content', $comment->getContent());
        $this->test("Comment getAuthorUsername()", 'test_author', $comment->getAuthorUsername());
        
        // Test setter method
        $comment->setContent('Updated comment content');
        $this->test("Comment setContent() works", 'Updated comment content', $comment->getContent());
    }
    
    private function testCategoryMethods(): void {
        echo "\n=== TESTING CATEGORY CLASS METHODS ===\n\n";
        
        $category = new Category(400, 'Test Category');
        
        // Test getter methods
        $this->test("Category getId()", 400, $category->getId());
        $this->test("Category getName()", 'Test Category', $category->getName());
    }
    
    private function testLoginSystem(): void {
        echo "\n=== TESTING LOGIN SYSTEM ===\n\n";
        
        // Test author login
        $result = User::login($this->users, 'john@example.com', 'password123');
        $this->test("Author login (john)", 'author', $result);
        
        // Test editor login
        $result = User::login($this->users, 'mike@example.com', 'editpass123');
        $this->test("Editor login (mike)", 'editor', $result);
        
        // Test admin login
        $result = User::login($this->users, 'alex@example.com', 'adminpass123');
        $this->test("Admin login (alex)", 'admin', $result);
        
        // Test invalid credentials
        $result = User::login($this->users, 'john@example.com', 'wrongpassword');
        $this->test("Invalid password", 'invalid', $result);
        
        $result = User::login($this->users, 'nonexistent@example.com', 'password123');
        $this->test("Non-existent email", 'invalid', $result);
        
        // Test all user types
        $testCases = [
            ['john@example.com', 'password123', 'author'],
            ['jane@example.com', 'secure456', 'author'],
            ['tech@example.com', 'techpass789', 'author'],
            ['max@example.com', 'blogpass456', 'author'],
            ['mike@example.com', 'editpass123', 'editor'],
            ['sarah@example.com', 'editpass456', 'editor'],
            ['alex@example.com', 'adminpass123', 'admin'],
            ['lisa@example.com', 'adminpass456', 'admin'],
            ['root@example.com', 'superadmin123', 'admin'],
        ];
        
        foreach ($testCases as $i => $testCase) {
            $result = User::login($this->users, $testCase[0], $testCase[1]);
            $this->test("Login test case " . ($i + 1), $testCase[2], $result);
        }
    }
    
    private function testModeratorHierarchy(): void {
        echo "\n=== TESTING INHERITANCE HIERARCHY ===\n\n";
        
        // Test object creation
        $admin = new Admin(500, 'test_admin', 'admin@test.com', 'pass');
        $editor = new Editor(501, 'test_editor', 'editor@test.com', 'pass');
        $moderator = new Moderator(502, 'test_mod', 'mod@test.com', 'pass');
        $author = new Author(503, 'test_author', 'author@test.com', 'pass');
        
        // Test inheritance
        $this->testType("Admin is instance of Moderator", Moderator::class, $admin);
        $this->testType("Admin is instance of User", User::class, $admin);
        $this->testType("Editor is instance of Moderator", Moderator::class, $editor);
        $this->testType("Editor is instance of User", User::class, $editor);
        $this->testType("Moderator is instance of User", User::class, $moderator);
        $this->testType("Author is instance of User", User::class, $author);
    }
}

// Run the tests
$testSuite = new TestSuite($users);
$testSuite->runAllTests();
?>