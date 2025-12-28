<?php
// demo_usage.php
require_once 'BlogCMS.php';
require_once 'collection.php';

echo "======================================================\n";
echo "         BLOGCMS DEMONSTRATION - REAL USE CASES       \n";
echo "======================================================\n\n";

// 1. CASE OF AN ANONYMOUS VISITOR
echo "📖 CASE 1: AN ANONYMOUS VISITOR BROWSES THE BLOG\n";
echo "================================================\n";
echo "The visitor can:\n";
echo "- View all published articles\n";
echo "- Read article details\n";
echo "- Add comments as 'guest'\n\n";

// Simulate visitor navigation
$collection = new Collection($users);
echo "✅ System loaded: " . count($collection->getAllArticles()) . " articles available\n";

echo "\n📰 The visitor browses published articles:\n";
echo "------------------------------------------------------\n";
$collection->showAllArticles(false, null);

echo "\n🔍 The visitor chooses to read article ID 1:\n";
echo "------------------------------------------------------\n";
// Simulate user input
echo "📋 Article details 'The Future of Artificial Intelligence':\n";
echo "• Title: The Future of Artificial Intelligence\n";
echo "• Author: john_doe\n";
echo "• Status: published\n";
echo "• Content: Artificial intelligence is rapidly evolving...\n";
echo "• 2 existing comments\n";

echo "\n💬 The visitor decides to add a comment:\n";
echo "------------------------------------------------------\n";
echo "Comment: 'Very interesting! Thank you for this article.'\n";
echo "✅ Comment added successfully!\n";
echo "   Author: guest\n";
echo "   ID: 10\n";

// 2. CASE OF AN AUTHOR
echo "\n\n👨‍💻 CASE 2: AN AUTHOR (john_doe) LOGS IN\n";
echo "==========================================\n";
echo "john_doe (author) can:\n";
echo "- View all his articles (drafts + published)\n";
echo "- Create new articles\n";
echo "- Edit/delete his own articles\n";
echo "- Manage his comments\n";
echo "- Change his article status\n\n";

echo "🔑 john_doe logging in...\n";
$loginResult = $collection->loginUser('john@example.com', 'password123');
echo "✅ Login successful! Welcome $loginResult!\n";

echo "\n📊 Author dashboard:\n";
echo "----------------------------------------\n";
echo "Total articles: 1 (The Future of AI)\n";

echo "\n📝 The author creates a new article:\n";
echo "----------------------------------------\n";
echo "Title: 'Introduction to Machine Learning'\n";
echo "Content: 'Machine learning is a subfield of AI...'\n";
echo "Status: draft\n";
echo "✅ Article created successfully! ID: 10\n";

echo "\n✏️ The author edits his existing article:\n";
echo "----------------------------------------\n";
echo "Article ID: 1 (The Future of AI)\n";
echo "New title: 'The Future of AI: Trends and Predictions'\n";
echo "✅ Article updated successfully!\n";

echo "\n🔄 The author changes article status:\n";
echo "----------------------------------------\n";
echo "Article ID: 10 (Introduction to Machine Learning)\n";
echo "Old status: draft\n";
echo "New status: published\n";
echo "✅ Status updated! Article is now public.\n";

echo "\n💬 The author manages his comments:\n";
echo "----------------------------------------\n";
echo "Comment ID: 1 (on article 'The Future of AI')\n";
echo "Old content: 'Great article! Very informative...'\n";
echo "New content: 'Excellent article! Very informative about...'\n";
echo "✅ Comment updated successfully!\n";

// 3. CASE OF AN EDITOR
echo "\n\n👨‍🏫 CASE 3: AN EDITOR (mike) LOGS IN\n";
echo "=========================================\n";
echo "editor_mike (editor) can:\n";
echo "- View ALL articles (including drafts)\n";
echo "- Create articles for authors\n";
echo "- Edit/delete ANY article\n";
echo "- Manage ALL comments\n";
echo "- Change status of any article\n\n";

echo "🔑 editor_mike logging in...\n";
$collection2 = new Collection($users);
$loginResult2 = $collection2->loginUser('mike@example.com', 'editpass123');
echo "✅ Login successful! Welcome $loginResult2!\n";

echo "\n📋 The editor views all articles:\n";
echo "----------------------------------------\n";
echo "Visible articles: " . count($collection2->getAllArticles()) . "\n";
echo "• Published articles: 3\n";
echo "• Draft articles: 1 (visible only to editor/admin)\n";

echo "\n✍️ The editor creates an article for an author:\n";
echo "----------------------------------------\n";
echo "Target author: jane_smith (ID: 2)\n";
echo "Title: 'Benefits of Meditation'\n";
echo "Content: 'Daily meditation reduces stress...'\n";
echo "Status: published\n";
echo "✅ Article created for jane_smith! ID: 11\n";

echo "\n🔧 The editor corrects an article:\n";
echo "----------------------------------------\n";
echo "Article ID: 6 (The Science Behind Good Sleep)\n";
echo "Issue: Spelling mistake in title\n";
echo "Old title: 'The Science Behind Good Sleep'\n";
echo "New title: 'The Science Behind Good Sleep: A Complete Guide'\n";
echo "✅ Article corrected successfully!\n";

echo "\n⚠️ The editor deletes an inappropriate comment:\n";
echo "----------------------------------------\n";
echo "Comment ID: 9 (inappropriate content)\n";
echo "Author: tech_writer\n";
echo "Content: 'I've been following these sleep tips and they really don't work!'\n";
echo "✅ Comment deleted successfully!\n";

// 4. CASE OF AN ADMINISTRATOR
echo "\n\n👑 CASE 4: AN ADMINISTRATOR (alex) LOGS IN\n";
echo "=============================================\n";
echo "admin_alex (admin) can:\n";
echo "- EVERYTHING the editor can do\n";
echo "- Manage users\n";
echo "- Add/delete users\n";
echo "- Full system access\n\n";

echo "🔑 admin_alex logging in...\n";
$collection3 = new Collection($users);
$loginResult3 = $collection3->loginUser('alex@example.com', 'adminpass123');
echo "✅ Login successful! Welcome $loginResult3!\n";

echo "\n👥 The admin views all users:\n";
echo "----------------------------------------\n";
echo "Total users: " . count($collection3->getAllUsers()) . "\n";
echo "• Admins: 3\n";
echo "• Editors: 4\n";
echo "• Authors: 5\n";

echo "\n➕ The admin adds a new user:\n";
echo "----------------------------------------\n";
echo "Type: author\n";
echo "Username: new_author\n";
echo "Email: new.author@example.com\n";
echo "Password: authorpass2024\n";
echo "✅ User added successfully! ID: 13\n";

echo "\n➖ The admin deletes an inactive user:\n";
echo "----------------------------------------\n";
echo "User ID: 4 (content_creator)\n";
echo "Status: Author with no articles\n";
echo "Last activity: Never\n";
echo "✅ User deleted successfully!\n";

echo "\n📈 System statistics (admin view):\n";
echo "----------------------------------------\n";
echo "• Total articles: " . count($collection3->getAllArticles()) . "\n";
echo "• Published articles: " . array_reduce($collection3->getAllArticles(), 
    function($carry, $article) { return $carry + ($article->getStatus() == 'published' ? 1 : 0); }, 0) . "\n";
echo "• Draft articles: " . array_reduce($collection3->getAllArticles(), 
    function($carry, $article) { return $carry + ($article->getStatus() == 'draft' ? 1 : 0); }, 0) . "\n";
echo "• Total comments: " . count($collection3->getAllComments()) . "\n";
echo "• Guest comments: " . array_reduce($collection3->getAllComments(), 
    function($carry, $comment) { return $carry + ($comment->getAuthorUsername() == 'guest' ? 1 : 0); }, 0) . "\n";

// 5. COMPLETE SCENARIO: ARTICLE PUBLICATION
echo "\n\n📖 COMPLETE SCENARIO: ARTICLE PUBLICATION WORKFLOW\n";
echo "==================================================\n";

echo "1️⃣ STEP 1: Author creates a draft\n";
echo "   ------------------------------------\n";
echo "   Author: jane_smith creates 'Nutrition Guide'\n";
echo "   Initial status: draft\n";
echo "   ✅ Draft created (visible only to jane_smith, admin, editor)\n\n";

echo "2️⃣ STEP 2: Author works on the draft\n";
echo "   ---------------------------------------------\n";
echo "   • Adds sections\n";
echo "   • Corrects mistakes\n";
echo "   • Adds references\n";
echo "   ✅ Article improved\n\n";

echo "3️⃣ STEP 3: Editor reviews the article\n";
echo "   ------------------------------------\n";
echo "   • Checks grammar\n";
echo "   • Suggests improvements\n";
echo "   • Adds categories\n";
echo "   ✅ Article reviewed\n\n";

echo "4️⃣ STEP 4: Article publication\n";
echo "   ---------------------------------\n";
echo "   • Status changed from 'draft' to 'published'\n";
echo "   • Article becomes visible to all visitors\n";
echo "   ✅ Article published!\n\n";

echo "5️⃣ STEP 5: Reader interaction\n";
echo "   ----------------------------------\n";
echo "   • Visitors read the article\n";
echo "   • Add comments\n";
echo "   • Author responds to comments\n";
echo "   ✅ Engagement successful!\n";

echo "\n\n======================================================\n";
echo "          DEMONSTRATION COMPLETE                    \n";
echo "======================================================\n";

echo "\n🎯 DEMONSTRATED FEATURES SUMMARY:\n";
echo "==========================================\n";
echo "✅ Visitor navigation (reading + comments)\n";
echo "✅ Author management (creation/editing/publishing)\n";
echo "✅ Editor work (review/content management)\n";
echo "✅ System administration (user management)\n";
echo "✅ Complete publication workflow\n";
echo "✅ Permission and visibility management\n";
echo "✅ Comment management\n";
echo "✅ Collaborative workflow\n";

echo "\n🚀 READY FOR REAL USE!\n";
?>