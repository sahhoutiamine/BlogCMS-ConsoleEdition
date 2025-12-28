BlogCMS Console Edition
A simple PHP-based Content Management System (CMS) with a console interface for managing a blog platform.

Features
User Roles & Permissions
Visitors: View published articles and add comments as "guest"

Authors: Create, update, and delete their own articles; manage their comments

Editors: Manage any article; create articles for authors; manage all comments

Admins: Full system control - manage users, articles, comments, and everything else

Article Management
Create articles with draft or published status

Update article content and status

Delete articles (authors can only delete their own)

Draft articles: visible only to author, admin, and editor

Published articles: visible to everyone

Comment System
Visitors can comment as "guest"

Logged-in users comment with their username

Authors can update/delete their own comments

Editors/Admins can manage all comments

User Management (Admin only)
Add new users (Author, Editor, Admin)

Delete users (can't delete yourself or last admin)

View all users with statistics

Project Structure
Main Files:
BlogCMS.php - Core classes (User, Author, Editor, Admin, Article, Comment, Category)

collection.php - Main application with Collection class and menu system

Classes:
User: Base user class with login functionality

Author: Can create and manage articles/comments

Editor: Can manage any article and comments

Admin: Full system administration

Article: Blog articles with status management

Comment: Article comments

Category: Article categories

Collection: Main application logic and data management

How to Run
Make sure you have PHP installed

Place both BlogCMS.php and collection.php in the same directory

Run from command line:

bash
php collection.php
Usage Flow
Main Menu: Choose between viewing articles or logging in

Visitor Mode: View published articles and add comments

Login: Use credentials from the BlogCMS.php file

Dashboard: Access role-specific features

Article Viewing: All users can view articles based on permissions

Management: Each role has specific management capabilities

Sample User Credentials
Check BlogCMS.php for sample users:

Authors: john@example.com (password123), jane@example.com (secure456)

Editors: mike@example.com (editpass123), sarah@example.com (editpass456)

Admins: alex@example.com (adminpass123), lisa@example.com (adminpass456)

Key Features
Simple console interface with clear menus

Role-based access control

Article status management (draft/published)

Comment system with user attribution

Data persistence within session

Input validation and error handling

Clean separation of concerns

Technologies Used
PHP 7.4+ (Object-Oriented Programming)

Console input/output (STDIN/STDOUT)

No database required (in-memory data storage)

Simple and clean code structure

Note
This is a console application for educational purposes. Data is stored in memory and will be lost when the program ends.
