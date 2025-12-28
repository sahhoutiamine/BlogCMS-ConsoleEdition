<?php
// collection.php
require_once 'BlogCMS.php'; 

class Collection {
    private $users;
    private $articles;
    private $comments;
    private $currentUserEmail = null;
    
    public function __construct($users) {
        $this->users = $users;
        $this->articles = [];
        $this->comments = [];
        
        $this->extractAllData();
    }
    
    public function setCurrentUserEmail($email) {
        $this->currentUserEmail = $email;
    }
    
    private function extractAllData() {
        // Get all articles from authors
        foreach ($this->users as $user) {
            if ($user instanceof Author) {
                $userArticles = $user->getArticles();
                foreach ($userArticles as $article) {
                    $this->articles[] = $article;
                    
                    // Extract comments from each article
                    $articleComments = $article->getComments();
                    foreach ($articleComments as $comment) {
                        $this->comments[] = $comment;
                    }
                }
            }
        }
    }
    
    public function getAllUsers() {
        return $this->users;
    }
    
    public function getAllArticles() {
        return $this->articles;
    }
    
    public function getAllComments() {
        return $this->comments;
    }
    
    // Show all articles with visibility control
    public function showAllArticles($showAll = false, $currentEmail = null) {
        echo "\n=== ALL ARTICLES ===\n";
        echo str_repeat("=", 50) . "\n";
        
        if (empty($this->articles)) {
            echo "No articles found.\n";
            return;
        }
        
        $visibleArticles = [];
        
        // Filter articles based on status and user
        foreach ($this->articles as $article) {
            $status = $article->getStatus();
            
            if ($showAll) {
                $visibleArticles[] = $article;
            } elseif ($status == 'published') {
                $visibleArticles[] = $article;
            } elseif ($currentEmail && $status == 'draft') {
                $author = $this->getAuthorByArticleId($article->getId());
                $user = $this->getUserByEmail($currentEmail);
                
                if ($user instanceof Admin || $user instanceof Editor) {
                    $visibleArticles[] = $article;
                } elseif ($user instanceof Author && $author && $author->getEmail() == $currentEmail) {
                    $visibleArticles[] = $article;
                }
            }
        }
        
        if (empty($visibleArticles)) {
            echo "No articles found.\n";
            return;
        }
        
        foreach ($visibleArticles as $article) {
            echo "ID: " . $article->getId() . "\n";
            echo "Title: " . $article->getTitle() . "\n";
            echo "Author: " . $this->getAuthorName($article->getId()) . "\n";
            echo "Status: " . $article->getStatus() . "\n";
            echo "Created: " . $article->getCreatedAt() . "\n";
            
            $categories = $article->getCategories();
            if (!empty($categories)) {
                echo "Categories: ";
                $categoryNames = [];
                foreach ($categories as $cat) {
                    $categoryNames[] = $cat->getName();
                }
                echo implode(", ", $categoryNames) . "\n";
            }
            
            echo "Comments: " . count($article->getComments()) . "\n";
            echo str_repeat("-", 50) . "\n";
        }
        
        echo "\nTotal Articles: " . count($visibleArticles) . "\n";
    }
    
    // Show only articles for a specific author
    public function showAuthorArticles($authorEmail, $showAll = false) {
        $author = null;
        
        // Find the author
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail && $user instanceof Author) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            echo "\nAuthor not found!\n";
            return 0;
        }
        
        $authorArticles = $author->getArticles();
        
        echo "\n=== MY ARTICLES ===\n";
        echo str_repeat("=", 50) . "\n";
        
        if (empty($authorArticles)) {
            echo "You don't have any articles yet.\n";
            return 0;
        }
        
        $visibleArticles = [];
        
        foreach ($authorArticles as $article) {
            $status = $article->getStatus();
            
            if ($showAll || $status == 'published') {
                $visibleArticles[] = $article;
            } elseif ($status == 'draft') {
                // Draft articles always visible to their author
                $visibleArticles[] = $article;
            }
        }
        
        if (empty($visibleArticles)) {
            echo "No articles found.\n";
            return 0;
        }
        
        foreach ($visibleArticles as $article) {
            echo "ID: " . $article->getId() . "\n";
            echo "Title: " . $article->getTitle() . "\n";
            echo "Status: " . $article->getStatus() . "\n";
            echo "Created: " . $article->getCreatedAt() . "\n";
            echo "Comments: " . count($article->getComments()) . "\n";
            echo str_repeat("-", 50) . "\n";
        }
        
        echo "\nTotal Articles: " . count($visibleArticles) . "\n";
        return count($visibleArticles);
    }
    
    // Show all comments by a specific author
    public function showAuthorComments($authorEmail) {
        $author = null;
        
        // Find the author
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            echo "\nAuthor not found!\n";
            return 0;
        }
        
        $authorUsername = $author->getUsername();
        $authorComments = [];
        
        // Find all comments by this author
        foreach ($this->comments as $comment) {
            if ($comment->getAuthorUsername() == $authorUsername) {
                $authorComments[] = $comment;
            }
        }
        
        echo "\n=== MY COMMENTS ===\n";
        echo str_repeat("=", 50) . "\n";
        
        if (empty($authorComments)) {
            echo "You haven't made any comments yet.\n";
            return 0;
        }
        
        foreach ($authorComments as $comment) {
            echo "Comment ID: " . $comment->getId() . "\n";
            echo "Article: " . $this->getArticleTitleForComment($comment->getId()) . "\n";
            echo "Date: " . $comment->getCreatedAt() . "\n";
            echo "Content: " . $comment->getContent() . "\n";
            echo str_repeat("-", 50) . "\n";
        }
        
        echo "\nTotal Comments: " . count($authorComments) . "\n";
        return count($authorComments);
    }
    
    // Show all comments in the system
    public function showAllComments() {
        echo "\n=== ALL COMMENTS ===\n";
        echo str_repeat("=", 50) . "\n";
        
        if (empty($this->comments)) {
            echo "No comments found in the system.\n";
            return 0;
        }
        
        foreach ($this->comments as $comment) {
            echo "Comment ID: " . $comment->getId() . "\n";
            echo "Author: " . $comment->getAuthorUsername() . "\n";
            echo "Article: " . $this->getArticleTitleForComment($comment->getId()) . "\n";
            echo "Date: " . $comment->getCreatedAt() . "\n";
            echo "Content: " . $comment->getContent() . "\n";
            echo str_repeat("-", 30) . "\n";
        }
        
        echo "\nTotal Comments: " . count($this->comments) . "\n";
        return count($this->comments);
    }
    
    // Delete any comment (for admin/editor)
    public function deleteAnyComment($commentId) {
        // Find the comment
        $commentIndex = null;
        $comment = null;
        
        foreach ($this->comments as $index => $c) {
            if ($c->getId() == $commentId) {
                $commentIndex = $index;
                $comment = $c;
                break;
            }
        }
        
        if ($comment === null) {
            echo "Comment with ID $commentId not found!\n";
            return false;
        }
        
        // Remove from article's comments
        foreach ($this->articles as $article) {
            $articleComments = $article->getComments();
            foreach ($articleComments as $cIndex => $articleComment) {
                if ($articleComment->getId() == $commentId) {
                    $article->deleteComment($commentId);
                    break;
                }
            }
        }
        
        // Remove from collection's comments array
        unset($this->comments[$commentIndex]);
        $this->comments = array_values($this->comments);
        
        echo "Comment deleted successfully!\n";
        return true;
    }
    
    public function showArticleDetails($articleId, $commentAsAuthor = false, $authorEmail = null) {
        // Find the article
        $article = null;
        foreach ($this->articles as $art) {
            if ($art->getId() == $articleId) {
                $article = $art;
                break;
            }
        }
        
        if ($article === null) {
            echo "\nArticle with ID $articleId not found!\n";
            return;
        }
        
        // Check article visibility
        $status = $article->getStatus();
        $canView = false;
        
        if ($status == 'published') {
            $canView = true;
        } elseif ($status == 'draft' && $authorEmail) {
            // Check if user can view draft
            $author = $this->getAuthorByArticleId($articleId);
            $user = $this->getUserByEmail($authorEmail);
            
            if ($user instanceof Admin || $user instanceof Editor) {
                $canView = true;
            } elseif ($user instanceof Author && $author && $author->getEmail() == $authorEmail) {
                $canView = true;
            }
        }
        
        if (!$canView) {
            echo "\nArticle with ID $articleId not found or you don't have permission to view it!\n";
            return;
        }
        
        echo "\n=== ARTICLE DETAILS ===\n";
        echo str_repeat("=", 50) . "\n";
        echo "ID: " . $article->getId() . "\n";
        echo "Title: " . $article->getTitle() . "\n";
        echo "Content: " . $article->getContent() . "\n";
        echo "Status: " . $article->getStatus() . "\n";
        echo "Created: " . $article->getCreatedAt() . "\n";
        
        $publishedAt = $article->getPublishedAt();
        if (!empty($publishedAt)) {
            echo "Published: " . $publishedAt . "\n";
        }
        
        echo "Author: " . $this->getAuthorName($article->getId()) . "\n";
        
        // Show categories
        $categories = $article->getCategories();
        if (!empty($categories)) {
            echo "Categories: ";
            $categoryNames = [];
            foreach ($categories as $cat) {
                $categoryNames[] = $cat->getName();
            }
            echo implode(", ", $categoryNames) . "\n";
        }
        
        // Show comments
        $comments = $article->getComments();
        echo "\n=== COMMENTS (" . count($comments) . ") ===\n";
        
        if (empty($comments)) {
            echo "No comments for this article.\n";
        } else {
            foreach ($comments as $comment) {
                echo str_repeat("-", 30) . "\n";
                echo "Comment ID: " . $comment->getId() . "\n";
                echo "Author: " . $comment->getAuthorUsername() . "\n";
                echo "Date: " . $comment->getCreatedAt() . "\n";
                echo "Content: " . $comment->getContent() . "\n";
            }
            echo str_repeat("-", 30) . "\n";
        }
        
        // Ask if they want to add a comment (only for published articles)
        if ($status == 'published') {
            if ($commentAsAuthor && $authorEmail) {
                echo "\n=== ADD COMMENT ===\n";
                echo "Would you like to add a comment? (yes/no): ";
                $answer = trim(fgets(STDIN));
                
                if (strtolower($answer) == 'yes' || strtolower($answer) == 'y') {
                    $this->addCommentToArticleAsAuthor($article, $authorEmail);
                }
            } else {
                echo "\n=== ADD COMMENT ===\n";
                echo "Would you like to add a comment? (yes/no): ";
                $answer = trim(fgets(STDIN));
                
                if (strtolower($answer) == 'yes' || strtolower($answer) == 'y') {
                    $this->addCommentToArticle($article);
                }
            }
        } else {
            echo "\nNote: Comments are only allowed on published articles.\n";
        }
    }
    
    private function addCommentToArticle($article) {
        echo "\nEnter your comment: ";
        $commentContent = trim(fgets(STDIN));
        
        if (empty($commentContent)) {
            echo "Comment cannot be empty!\n";
            return;
        }
        
        // Create new comment with ID = next available ID
        $newCommentId = $this->getNextCommentId();
        $currentDate = date('Y-m-d H:i:s');
        $authorUsername = "guest";
        
        // Create the comment object
        $newComment = new Comment($newCommentId, $commentContent, $currentDate, $authorUsername);
        
        // Add comment to article
        $article->createComment($newComment);
        
        // Also add to our collection's comments array
        $this->comments[] = $newComment;
        
        echo "\nComment added successfully!\n";
        echo "Comment ID: " . $newCommentId . "\n";
        echo "Author: " . $authorUsername . "\n";
        echo "Date: " . $currentDate . "\n";
        echo "Content: " . $commentContent . "\n";
    }
    
    private function addCommentToArticleAsAuthor($article, $authorEmail) {
        echo "\nEnter your comment: ";
        $commentContent = trim(fgets(STDIN));
        
        if (empty($commentContent)) {
            echo "Comment cannot be empty!\n";
            return;
        }
        
        // Find author username
        $authorUsername = "";
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail) {
                $authorUsername = $user->getUsername();
                break;
            }
        }
        
        if (empty($authorUsername)) {
            $authorUsername = "guest";
        }
        
        // Create new comment with ID = next available ID
        $newCommentId = $this->getNextCommentId();
        $currentDate = date('Y-m-d H:i:s');
        
        // Create the comment object with author's username
        $newComment = new Comment($newCommentId, $commentContent, $currentDate, $authorUsername);
        
        // Add comment to article
        $article->createComment($newComment);
        
        // Also add to our collection's comments array
        $this->comments[] = $newComment;
        
        echo "\nComment added successfully!\n";
        echo "Comment ID: " . $newCommentId . "\n";
        echo "Author: " . $authorUsername . "\n";
        echo "Date: " . $currentDate . "\n";
        echo "Content: " . $commentContent . "\n";
    }
    
    // Update author's comment
    public function updateAuthorComment($commentId, $authorEmail, $newContent) {
        // Find the author
        $author = null;
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            return false;
        }
        
        $authorUsername = $author->getUsername();
        
        // Find the comment and check if author owns it
        foreach ($this->comments as $comment) {
            if ($comment->getId() == $commentId && $comment->getAuthorUsername() == $authorUsername) {
                // Update the comment
                $comment->setContent($newContent);
                return true;
            }
        }
        
        return false;
    }
    
    // Delete author's comment
    public function deleteAuthorComment($commentId, $authorEmail) {
        // Find the author
        $author = null;
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            return false;
        }
        
        $authorUsername = $author->getUsername();
        
        // Find the comment and check if author owns it
        foreach ($this->comments as $index => $comment) {
            if ($comment->getId() == $commentId && $comment->getAuthorUsername() == $authorUsername) {
                // Also remove from article's comments
                foreach ($this->articles as $article) {
                    $articleComments = $article->getComments();
                    foreach ($articleComments as $cIndex => $articleComment) {
                        if ($articleComment->getId() == $commentId) {
                            $article->deleteComment($commentId);
                            break;
                        }
                    }
                }
                
                // Remove from collection's comments array
                unset($this->comments[$index]);
                $this->comments = array_values($this->comments);
                return true;
            }
        }
        
        return false;
    }
    
    // Update article status
    public function updateArticleStatus($articleId, $newStatus, $authorEmail = null) {
        // Find the article
        $article = null;
        foreach ($this->articles as $art) {
            if ($art->getId() == $articleId) {
                $article = $art;
                break;
            }
        }
        
        if ($article === null) {
            echo "Article with ID $articleId not found!\n";
            return false;
        }
        
        // Check if author is trying to update someone else's article
        if ($authorEmail !== null) {
            if (!$this->isArticleOwner($articleId, $authorEmail)) {
                echo "You can only update the status of your own articles!\n";
                return false;
            }
        }
        
        // Validate status - only draft or published
        $validStatuses = ['draft', 'published'];
        if (!in_array(strtolower($newStatus), $validStatuses)) {
            echo "Invalid status! Please use: draft or published.\n";
            return false;
        }
        
        // Update the status
        $article->setStatus(strtolower($newStatus));
        
        return true;
    }
    
    private function getNextCommentId() {
        $maxId = 0;
        foreach ($this->comments as $comment) {
            if ($comment->getId() > $maxId) {
                $maxId = $comment->getId();
            }
        }
        return $maxId + 1;
    }
    
    private function getAuthorName($articleId) {
        foreach ($this->users as $user) {
            if ($user instanceof Author) {
                $userArticles = $user->getArticles();
                foreach ($userArticles as $article) {
                    if ($article->getId() == $articleId) {
                        return $user->getUsername();
                    }
                }
            }
        }
        return "Unknown";
    }
    
    private function getAuthorByArticleId($articleId) {
        foreach ($this->users as $user) {
            if ($user instanceof Author) {
                $userArticles = $user->getArticles();
                foreach ($userArticles as $article) {
                    if ($article->getId() == $articleId) {
                        return $user;
                    }
                }
            }
        }
        return null;
    }
    
    private function getUserByEmail($email) {
        foreach ($this->users as $user) {
            if ($user->getEmail() == $email) {
                return $user;
            }
        }
        return null;
    }
    
    private function getArticleTitleForComment($commentId) {
        foreach ($this->articles as $article) {
            $comments = $article->getComments();
            foreach ($comments as $comment) {
                if ($comment->getId() == $commentId) {
                    return $article->getTitle();
                }
            }
        }
        return "Unknown Article";
    }
    
    // Find author by article ID
    private function findAuthorByArticleId($articleId) {
        foreach ($this->users as $user) {
            if ($user instanceof Author) {
                $userArticles = $user->getArticles();
                foreach ($userArticles as $article) {
                    if ($article->getId() == $articleId) {
                        return $user;
                    }
                }
            }
        }
        return null;
    }
    
    // Check if article belongs to specific author
    public function isArticleOwner($articleId, $authorEmail) {
        $author = null;
        
        // Find the author
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail && $user instanceof Author) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            return false;
        }
        
        // Check if author owns this article
        $authorArticles = $author->getArticles();
        foreach ($authorArticles as $article) {
            if ($article->getId() == $articleId) {
                return true;
            }
        }
        
        return false;
    }
    
    // Author creates their own article
    public function createAuthorArticle($authorEmail, $title, $content, $status) {
        // Find the author
        $author = null;
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail && $user instanceof Author) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            echo "Author not found!\n";
            return false;
        }
        
        // Validate status - only draft or published
        $validStatuses = ['draft', 'published'];
        if (!in_array(strtolower($status), $validStatuses)) {
            echo "Invalid status! Please use: draft or published.\n";
            return false;
        }
        
        // Create new article
        $articleId = $this->getNextArticleId();
        $currentDate = date('Y-m-d H:i:s');
        $newArticle = new Article($articleId, $title, $content, strtolower($status), $currentDate);
        
        // Add article to author
        $author->createArticle($newArticle);
        
        // Also add to collection's articles array
        $this->articles[] = $newArticle;
        
        return $articleId;
    }
    
    // Author updates their own article
    public function updateAuthorArticle($articleId, $authorEmail, $newTitle = null, $newContent = null) {
        // First check if author owns this article
        if (!$this->isArticleOwner($articleId, $authorEmail)) {
            echo "You can only update your own articles!\n";
            return false;
        }
        
        // Find the author
        $author = null;
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail && $user instanceof Author) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            return false;
        }
        
        // Update the article
        return $author->updateArticle($articleId, $newTitle, $newContent);
    }
    
    // Author deletes their own article
    public function deleteAuthorArticle($articleId, $authorEmail) {
        // First check if author owns this article
        if (!$this->isArticleOwner($articleId, $authorEmail)) {
            echo "You can only delete your own articles!\n";
            return false;
        }
        
        // Find the author
        $author = null;
        foreach ($this->users as $user) {
            if ($user->getEmail() == $authorEmail && $user instanceof Author) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            return false;
        }
        
        // Delete the article
        $result = $author->deleteArticle($articleId);
        
        // Also remove from collection's articles array
        if ($result) {
            foreach ($this->articles as $index => $article) {
                if ($article->getId() == $articleId) {
                    unset($this->articles[$index]);
                    $this->articles = array_values($this->articles);
                    break;
                }
            }
        }
        
        return $result;
    }
    
    // ADMIN FUNCTIONS
    public function showAllUsers($excludeCurrentUser = false) {
        echo "\n=== ALL USERS ===\n";
        echo str_repeat("=", 50) . "\n";
        
        $authorCount = 0;
        $editorCount = 0;
        $adminCount = 0;
        $totalShown = 0;
        
        foreach ($this->users as $user) {
            // Skip current user if excludeCurrentUser is true
            if ($excludeCurrentUser && $this->currentUserEmail && $user->getEmail() == $this->currentUserEmail) {
                continue;
            }
            
            echo "ID: " . $user->getId() . "\n";
            echo "Username: " . $user->getUsername() . "\n";
            echo "Email: " . $user->getEmail() . "\n";
            
            if ($user instanceof Admin) {
                echo "Type: Admin\n";
                $adminCount++;
            } elseif ($user instanceof Editor) {
                echo "Type: Editor\n";
                $editorCount++;
            } elseif ($user instanceof Author) {
                echo "Type: Author\n";
                $authorCount++;
                echo "Articles: " . count($user->getArticles()) . "\n";
            } else {
                echo "Type: User\n";
            }
            
            echo str_repeat("-", 50) . "\n";
            $totalShown++;
        }
        
        if ($totalShown == 0) {
            echo "No other users found.\n";
        }
        
        echo "\n=== USER SUMMARY ===\n";
        echo "Total Users: " . count($this->users) . "\n";
        echo "Admins: " . $adminCount . "\n";
        echo "Editors: " . $editorCount . "\n";
        echo "Authors: " . $authorCount . "\n";
    }
    
    public function showAllAuthors() {
        echo "\n=== ALL AUTHORS ===\n";
        echo str_repeat("=", 50) . "\n";
        
        $authorCount = 0;
        
        foreach ($this->users as $user) {
            if ($user instanceof Author) {
                echo "ID: " . $user->getId() . "\n";
                echo "Username: " . $user->getUsername() . "\n";
                echo "Email: " . $user->getEmail() . "\n";
                echo "Articles: " . count($user->getArticles()) . "\n";
                echo str_repeat("-", 50) . "\n";
                $authorCount++;
            }
        }
        
        if ($authorCount == 0) {
            echo "No authors found.\n";
        }
        
        echo "\nTotal Authors: " . $authorCount . "\n";
    }
    
    public function addUser($userType, $username, $email, $password) {
        // Find the next available user ID
        $nextId = $this->getNextUserId();
        
        // Create the user based on type
        switch (strtolower($userType)) {
            case 'admin':
                $newUser = new Admin($nextId, $username, $email, $password);
                break;
            case 'editor':
                $newUser = new Editor($nextId, $username, $email, $password);
                break;
            case 'author':
                $newUser = new Author($nextId, $username, $email, $password);
                break;
            default:
                echo "Invalid user type!\n";
                return false;
        }
        
        // Add user to array
        $this->users[] = $newUser;
        return true;
    }
    
    public function deleteUser($userId) {
        foreach ($this->users as $index => $user) {
            if ($user->getId() == $userId) {
                // Check if trying to delete current user
                if ($this->currentUserEmail && $user->getEmail() == $this->currentUserEmail) {
                    echo "You cannot delete yourself!\n";
                    return false;
                }
                
                // Don't allow deleting all admins (keep at least one)
                if ($user instanceof Admin) {
                    $adminCount = 0;
                    foreach ($this->users as $u) {
                        if ($u instanceof Admin) {
                            $adminCount++;
                        }
                    }
                    
                    if ($adminCount <= 1) {
                        echo "Cannot delete the last admin!\n";
                        return false;
                    }
                }
                
                unset($this->users[$index]);
                $this->users = array_values($this->users);
                return true;
            }
        }
        return false;
    }
    
    // ARTICLE MANAGEMENT FUNCTIONS (for Admin and Editor)
    public function createArticleForAuthor($authorId, $article) {
        // Find the author
        $author = null;
        foreach ($this->users as $user) {
            if ($user->getId() == $authorId && $user instanceof Author) {
                $author = $user;
                break;
            }
        }
        
        if ($author === null) {
            echo "Author with ID $authorId not found!\n";
            return false;
        }
        
        // Add article to author
        $author->createArticle($article);
        
        // Also add to collection's articles array
        $this->articles[] = $article;
        
        return true;
    }
    
    // Updated: Only needs article ID, not author ID
    public function updateArticle($articleId, $newTitle = null, $newContent = null) {
        // Find the article's author
        $author = $this->findAuthorByArticleId($articleId);
        
        if ($author === null) {
            echo "Article with ID $articleId not found!\n";
            return false;
        }
        
        // Update the article using Author's updateArticle method
        return $author->updateArticle($articleId, $newTitle, $newContent);
    }
    
    // Updated: Only needs article ID, not author ID
    public function deleteArticle($articleId) {
        // Find the article's author
        $author = $this->findAuthorByArticleId($articleId);
        
        if ($author === null) {
            echo "Article with ID $articleId not found!\n";
            return false;
        }
        
        // Delete the article using Author's deleteArticle method
        $result = $author->deleteArticle($articleId);
        
        // Also remove from collection's articles array
        if ($result) {
            foreach ($this->articles as $index => $article) {
                if ($article->getId() == $articleId) {
                    unset($this->articles[$index]);
                    $this->articles = array_values($this->articles);
                    break;
                }
            }
        }
        
        return $result;
    }
    
    public function getNextArticleId() {
        $maxId = 0;
        foreach ($this->articles as $article) {
            if ($article->getId() > $maxId) {
                $maxId = $article->getId();
            }
        }
        return $maxId + 1;
    }
    
    private function getNextUserId() {
        $maxId = 0;
        foreach ($this->users as $user) {
            if ($user->getId() > $maxId) {
                $maxId = $user->getId();
            }
        }
        return $maxId + 1;
    }
    
    // Use the User::login() static method from BlogCMS
    public function loginUser($email, $password) {
        return User::login($this->users, $email, $password);
    }
}

// Admin Dashboard Function
function showAdminDashboard($collection, $currentEmail) {
    while (true) {
        echo "\n=== ADMIN DASHBOARD ===\n";
        echo "1. View All Articles\n";
        echo "2. View All Users\n";
        echo "3. View All Comments\n";
        echo "4. Add New User\n";
        echo "5. Delete User\n";
        echo "6. Create Article for Author\n";
        echo "7. Update Article\n";
        echo "8. Delete Article\n";
        echo "9. Manage Article Status\n";
        echo "10. Manage Comments\n";
        echo "11. Back to Main Menu\n";
        echo "Enter your choice: ";
        
        $choice = trim(fgets(STDIN));
        
        if ($choice == '1') {
            $collection->showAllArticles(true, $currentEmail); // Show all articles including drafts
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '2') {
            $collection->showAllUsers(false); // Show all users including current
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '3') {
            $collection->showAllComments();
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '4') {
            echo "\n=== ADD NEW USER ===\n";
            
            echo "Enter username: ";
            $username = trim(fgets(STDIN));
            
            echo "Enter email: ";
            $email = trim(fgets(STDIN));
            
            echo "Enter password: ";
            $password = trim(fgets(STDIN));
            
            echo "Select user type (admin/editor/author): ";
            $userType = trim(fgets(STDIN));
            
            if ($collection->addUser($userType, $username, $email, $password)) {
                echo "\nUser added successfully!\n";
            } else {
                echo "\nFailed to add user.\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '5') {
            echo "\n=== DELETE USER ===\n";
            $collection->showAllUsers(true); // Exclude current user
            
            echo "\nEnter User ID to delete: ";
            $userId = trim(fgets(STDIN));
            
            if (is_numeric($userId)) {
                if ($collection->deleteUser((int)$userId)) {
                    echo "\nUser deleted successfully!\n";
                } else {
                    echo "\nFailed to delete user. User not found, cannot delete last admin, or trying to delete yourself.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '6') {
            echo "\n=== CREATE ARTICLE FOR AUTHOR ===\n";
            $collection->showAllAuthors(); // Show only authors
            
            echo "\nEnter Author ID: ";
            $authorId = trim(fgets(STDIN));
            
            if (is_numeric($authorId)) {
                echo "Enter article title: ";
                $title = trim(fgets(STDIN));
                
                echo "Enter article content: ";
                $content = trim(fgets(STDIN));
                
                echo "Enter article status (draft/published): ";
                $status = trim(fgets(STDIN));
                
                $articleId = $collection->getNextArticleId();
                $currentDate = date('Y-m-d H:i:s');
                
                // Create new article
                $newArticle = new Article($articleId, $title, $content, $status, $currentDate);
                
                if ($collection->createArticleForAuthor((int)$authorId, $newArticle)) {
                    echo "\nArticle created successfully!\n";
                    echo "Article ID: " . $articleId . "\n";
                    echo "Title: " . $title . "\n";
                    echo "Status: " . $status . "\n";
                } else {
                    echo "\nFailed to create article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '7') {
            echo "\n=== UPDATE ARTICLE ===\n";
            $collection->showAllArticles(true, $currentEmail);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                echo "Enter new title (press Enter to skip): ";
                $newTitle = trim(fgets(STDIN));
                if (empty($newTitle)) $newTitle = null;
                
                echo "Enter new content (press Enter to skip): ";
                $newContent = trim(fgets(STDIN));
                if (empty($newContent)) $newContent = null;
                
                if ($collection->updateArticle((int)$articleId, $newTitle, $newContent)) {
                    echo "\nArticle updated successfully!\n";
                } else {
                    echo "\nFailed to update article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '8') {
            echo "\n=== DELETE ARTICLE ===\n";
            $collection->showAllArticles(true, $currentEmail);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                if ($collection->deleteArticle((int)$articleId)) {
                    echo "\nArticle deleted successfully!\n";
                } else {
                    echo "\nFailed to delete article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '9') {
            echo "\n=== MANAGE ARTICLE STATUS ===\n";
            $collection->showAllArticles(true, $currentEmail);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                echo "Enter new status (draft/published): ";
                $newStatus = trim(fgets(STDIN));
                
                if ($collection->updateArticleStatus((int)$articleId, $newStatus)) {
                    echo "\nArticle status updated successfully!\n";
                } else {
                    echo "\nFailed to update article status.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '10') {
            // Manage Comments
            echo "\n=== MANAGE COMMENTS ===\n";
            $commentCount = $collection->showAllComments();
            
            if ($commentCount > 0) {
                echo "\n1. Delete Comment\n";
                echo "2. Back to Admin Dashboard\n";
                echo "Enter your choice: ";
                
                $commentChoice = trim(fgets(STDIN));
                
                if ($commentChoice == '1') {
                    echo "\nEnter Comment ID to delete: ";
                    $commentId = trim(fgets(STDIN));
                    
                    if (is_numeric($commentId)) {
                        if ($collection->deleteAnyComment((int)$commentId)) {
                            echo "\nComment deleted successfully!\n";
                        } else {
                            echo "\nFailed to delete comment.\n";
                        }
                    } else {
                        echo "Please enter a valid number!\n";
                    }
                } 
                elseif ($commentChoice == '2') {
                    continue;
                } 
                else {
                    echo "Invalid choice!\n";
                }
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '11') {
            break;
        } 
        else {
            echo "Invalid choice!\n";
        }
    }
}

// Editor Dashboard Function
function showEditorDashboard($collection, $currentEmail) {
    while (true) {
        echo "\n=== EDITOR DASHBOARD ===\n";
        echo "1. View All Articles\n";
        echo "2. Create Article for Author\n";
        echo "3. Update Article\n";
        echo "4. Delete Article\n";
        echo "5. Manage Article Status\n";
        echo "6. View All Comments\n";
        echo "7. Manage Comments\n";
        echo "8. Back to Main Menu\n";
        echo "Enter your choice: ";
        
        $choice = trim(fgets(STDIN));
        
        if ($choice == '1') {
            $collection->showAllArticles(true, $currentEmail); // Show all articles including drafts
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '2') {
            echo "\n=== CREATE ARTICLE FOR AUTHOR ===\n";
            $collection->showAllAuthors(); // Show only authors
            
            echo "\nEnter Author ID: ";
            $authorId = trim(fgets(STDIN));
            
            if (is_numeric($authorId)) {
                echo "Enter article title: ";
                $title = trim(fgets(STDIN));
                
                echo "Enter article content: ";
                $content = trim(fgets(STDIN));
                
                echo "Enter article status (draft/published): ";
                $status = trim(fgets(STDIN));
                
                $articleId = $collection->getNextArticleId();
                $currentDate = date('Y-m-d H:i:s');
                
                // Create new article
                $newArticle = new Article($articleId, $title, $content, $status, $currentDate);
                
                if ($collection->createArticleForAuthor((int)$authorId, $newArticle)) {
                    echo "\nArticle created successfully!\n";
                    echo "Article ID: " . $articleId . "\n";
                    echo "Title: " . $title . "\n";
                    echo "Status: " . $status . "\n";
                } else {
                    echo "\nFailed to create article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '3') {
            echo "\n=== UPDATE ARTICLE ===\n";
            $collection->showAllArticles(true, $currentEmail);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                echo "Enter new title (press Enter to skip): ";
                $newTitle = trim(fgets(STDIN));
                if (empty($newTitle)) $newTitle = null;
                
                echo "Enter new content (press Enter to skip): ";
                $newContent = trim(fgets(STDIN));
                if (empty($newContent)) $newContent = null;
                
                if ($collection->updateArticle((int)$articleId, $newTitle, $newContent)) {
                    echo "\nArticle updated successfully!\n";
                } else {
                    echo "\nFailed to update article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '4') {
            echo "\n=== DELETE ARTICLE ===\n";
            $collection->showAllArticles(true, $currentEmail);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                if ($collection->deleteArticle((int)$articleId)) {
                    echo "\nArticle deleted successfully!\n";
                } else {
                    echo "\nFailed to delete article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '5') {
            echo "\n=== MANAGE ARTICLE STATUS ===\n";
            $collection->showAllArticles(true, $currentEmail);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                echo "Enter new status (draft/published): ";
                $newStatus = trim(fgets(STDIN));
                
                if ($collection->updateArticleStatus((int)$articleId, $newStatus)) {
                    echo "\nArticle status updated successfully!\n";
                } else {
                    echo "\nFailed to update article status.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '6') {
            $collection->showAllComments();
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '7') {
            // Manage Comments
            echo "\n=== MANAGE COMMENTS ===\n";
            $commentCount = $collection->showAllComments();
            
            if ($commentCount > 0) {
                echo "\n1. Delete Comment\n";
                echo "2. Back to Editor Dashboard\n";
                echo "Enter your choice: ";
                
                $commentChoice = trim(fgets(STDIN));
                
                if ($commentChoice == '1') {
                    echo "\nEnter Comment ID to delete: ";
                    $commentId = trim(fgets(STDIN));
                    
                    if (is_numeric($commentId)) {
                        if ($collection->deleteAnyComment((int)$commentId)) {
                            echo "\nComment deleted successfully!\n";
                        } else {
                            echo "\nFailed to delete comment.\n";
                        }
                    } else {
                        echo "Please enter a valid number!\n";
                    }
                } 
                elseif ($commentChoice == '2') {
                    continue;
                } 
                else {
                    echo "Invalid choice!\n";
                }
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '8') {
            break;
        } 
        else {
            echo "Invalid choice!\n";
        }
    }
}

// Author Dashboard Function
function showAuthorDashboard($collection, $currentEmail) {
    while (true) {
        // Show article count first
        $articleCount = $collection->showAuthorArticles($currentEmail, true);
        
        echo "\n=== AUTHOR DASHBOARD ===\n";
        echo "Total Articles: " . $articleCount . "\n";
        echo "1. Get All Articles\n";
        echo "2. Create Article\n";
        echo "3. Update My Article\n";
        echo "4. Delete My Article\n";
        echo "5. Manage My Article Status\n";
        echo "6. Manage My Comments\n";
        echo "7. Back to Main Menu\n";
        echo "Enter your choice: ";
        
        $choice = trim(fgets(STDIN));
        
        if ($choice == '1') {
            // Article menu (same as visitor but with draft visibility for own articles)
            while (true) {
                $collection->showAllArticles(false, $currentEmail);
                
                echo "\n=== ARTICLE OPTIONS ===\n";
                echo "1. View Article Details\n";
                echo "2. Back to Author Dashboard\n";
                echo "Enter your choice: ";
                
                $subChoice = trim(fgets(STDIN));
                
                if ($subChoice == '1') {
                    echo "Enter Article ID: ";
                    $articleId = trim(fgets(STDIN));
                    
                    if (is_numeric($articleId)) {
                        $collection->showArticleDetails((int)$articleId, true, $currentEmail);
                    } else {
                        echo "Please enter a valid number!\n";
                    }
                    
                    echo "\nPress Enter to continue...";
                    fgets(STDIN);
                } 
                elseif ($subChoice == '2') {
                    break;
                } 
                else {
                    echo "Invalid choice!\n";
                }
            }
        } 
        elseif ($choice == '2') {
            echo "\n=== CREATE ARTICLE ===\n";
            
            echo "Enter article title: ";
            $title = trim(fgets(STDIN));
            
            if (empty($title)) {
                echo "Title cannot be empty!\n";
                echo "\nPress Enter to continue...";
                fgets(STDIN);
                continue;
            }
            
            echo "Enter article content: ";
            $content = trim(fgets(STDIN));
            
            if (empty($content)) {
                echo "Content cannot be empty!\n";
                echo "\nPress Enter to continue...";
                fgets(STDIN);
                continue;
            }
            
            echo "Enter article status (draft/published): ";
            $status = trim(fgets(STDIN));
            
            $articleId = $collection->createAuthorArticle($currentEmail, $title, $content, $status);
            
            if ($articleId !== false) {
                echo "\nArticle created successfully!\n";
                echo "Article ID: " . $articleId . "\n";
                echo "Title: " . $title . "\n";
                echo "Status: " . $status . "\n";
            } else {
                echo "\nFailed to create article.\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '3') {
            echo "\n=== UPDATE MY ARTICLE ===\n";
            $collection->showAuthorArticles($currentEmail, true);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                echo "Enter new title (press Enter to skip): ";
                $newTitle = trim(fgets(STDIN));
                if (empty($newTitle)) $newTitle = null;
                
                echo "Enter new content (press Enter to skip): ";
                $newContent = trim(fgets(STDIN));
                if (empty($newContent)) $newContent = null;
                
                if ($collection->updateAuthorArticle((int)$articleId, $currentEmail, $newTitle, $newContent)) {
                    echo "\nArticle updated successfully!\n";
                } else {
                    echo "\nFailed to update article. Make sure you own this article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '4') {
            echo "\n=== DELETE MY ARTICLE ===\n";
            $collection->showAuthorArticles($currentEmail, true);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                if ($collection->deleteAuthorArticle((int)$articleId, $currentEmail)) {
                    echo "\nArticle deleted successfully!\n";
                } else {
                    echo "\nFailed to delete article. Make sure you own this article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '5') {
            echo "\n=== MANAGE MY ARTICLE STATUS ===\n";
            $collection->showAuthorArticles($currentEmail, true);
            
            echo "\nEnter Article ID: ";
            $articleId = trim(fgets(STDIN));
            
            if (is_numeric($articleId)) {
                echo "Enter new status (draft/published): ";
                $newStatus = trim(fgets(STDIN));
                
                if ($collection->updateArticleStatus((int)$articleId, $newStatus, $currentEmail)) {
                    echo "\nArticle status updated successfully!\n";
                } else {
                    echo "\nFailed to update article status. Make sure you own this article.\n";
                }
            } else {
                echo "Please enter a valid number!\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '6') {
            // Manage Comments
            echo "\n=== MANAGE MY COMMENTS ===\n";
            $commentCount = $collection->showAuthorComments($currentEmail);
            
            if ($commentCount > 0) {
                echo "\n1. Update Comment\n";
                echo "2. Delete Comment\n";
                echo "3. Back to Author Dashboard\n";
                echo "Enter your choice: ";
                
                $commentChoice = trim(fgets(STDIN));
                
                if ($commentChoice == '1') {
                    echo "\nEnter Comment ID to update: ";
                    $commentId = trim(fgets(STDIN));
                    
                    if (is_numeric($commentId)) {
                        echo "Enter new comment content: ";
                        $newContent = trim(fgets(STDIN));
                        
                        if ($collection->updateAuthorComment((int)$commentId, $currentEmail, $newContent)) {
                            echo "\nComment updated successfully!\n";
                        } else {
                            echo "\nFailed to update comment. Make sure you own this comment.\n";
                        }
                    } else {
                        echo "Please enter a valid number!\n";
                    }
                } 
                elseif ($commentChoice == '2') {
                    echo "\nEnter Comment ID to delete: ";
                    $commentId = trim(fgets(STDIN));
                    
                    if (is_numeric($commentId)) {
                        if ($collection->deleteAuthorComment((int)$commentId, $currentEmail)) {
                            echo "\nComment deleted successfully!\n";
                        } else {
                            echo "\nFailed to delete comment. Make sure you own this comment.\n";
                        }
                    } else {
                        echo "Please enter a valid number!\n";
                    }
                } 
                elseif ($commentChoice == '3') {
                    continue;
                } 
                else {
                    echo "Invalid choice!\n";
                }
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
        } 
        elseif ($choice == '7') {
            break;
        } 
        else {
            echo "Invalid choice!\n";
        }
    }
}

// Simple Menu System
echo "=== BLOG CMS SYSTEM ===\n";
echo "Loading data...\n";

$collection = new Collection($users);

echo "Data loaded successfully!\n";
echo "Users: " . count($collection->getAllUsers()) . "\n";
echo "Articles: " . count($collection->getAllArticles()) . "\n";
echo "Comments: " . count($collection->getAllComments()) . "\n";

// Main menu loop
while (true) {
    echo "\n=== MAIN MENU ===\n";
    echo "1. Get All Articles\n";
    echo "2. Login\n";
    echo "3. Exit\n";
    echo "Enter your choice: ";
    
    $choice = trim(fgets(STDIN));
    
    if ($choice == '1') {
        // Article menu for visitors - only published articles
        while (true) {
            $collection->showAllArticles(false, null); // Only published articles for visitors
            
            echo "\n=== ARTICLE OPTIONS ===\n";
            echo "1. View Article Details\n";
            echo "2. Back to Main Menu\n";
            echo "Enter your choice: ";
            
            $subChoice = trim(fgets(STDIN));
            
            if ($subChoice == '1') {
                echo "Enter Article ID: ";
                $articleId = trim(fgets(STDIN));
                
                if (is_numeric($articleId)) {
                    $collection->showArticleDetails((int)$articleId);
                } else {
                    echo "Please enter a valid number!\n";
                }
                
                echo "\nPress Enter to continue...";
                fgets(STDIN);
            } 
            elseif ($subChoice == '2') {
                break;
            } 
            else {
                echo "Invalid choice!\n";
            }
        }
    } 
    elseif ($choice == '2') {
        // Login using User::login() static method
        echo "\n=== LOGIN ===\n";
        echo "Email: ";
        $email = trim(fgets(STDIN));
        
        echo "Password: ";
        $password = trim(fgets(STDIN));
        
        $loginResult = $collection->loginUser($email, $password);
        
        if ($loginResult != "invalid") {
            echo "\nLogin successful! Welcome $loginResult!\n";
            
            // Set current user email in collection
            $collection->setCurrentUserEmail($email);
            
            // Show appropriate dashboard based on user type
            if ($loginResult == 'admin') {
                showAdminDashboard($collection, $email);
            } 
            elseif ($loginResult == 'editor') {
                showEditorDashboard($collection, $email);
            } 
            elseif ($loginResult == 'author') {
                showAuthorDashboard($collection, $email);
            }
        } 
        else {
            echo "\nLogin failed! Invalid email or password.\n";
            echo "Press Enter to continue...";
            fgets(STDIN);
        }
    } 
    elseif ($choice == '3') {
        echo "\nGoodbye!\n";
        break;
    } 
    else {
        echo "Invalid choice! Please enter 1, 2, or 3.\n";
    }
}
?>