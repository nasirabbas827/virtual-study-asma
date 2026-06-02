# virtual-study-Asmafinal

A PHP‑based web application that enables students and educators to create, manage, and participate in virtual study groups, resources, and discussion sessions.

---

## Overview

`virtual-study-Asmafinal` provides a simple yet powerful platform for collaborative learning. Users can register, join groups, upload resources, schedule study sessions, and engage in threaded discussions. An admin panel offers full control over users, groups, resources, and sessions.

---

## Features

- **User Management** – Register, login, edit profile, and logout.
- **Group Management** – Create, edit, and delete study groups.
- **Resource Library** – Upload and share files (PDFs, images, etc.) within groups.
- **Session Scheduling** – Add, edit, and view virtual study sessions.
- **Discussion Boards** – Threaded discussions linked to each resource or session.
- **Admin Dashboard** – Manage users, groups, resources, and sessions; update admin credentials.
- **Responsive UI** – Clean layout powered by a custom CSS stylesheet.

---

## Tech Stack

| Layer          | Technology |
|----------------|------------|
| Backend        | PHP 7+ |
| Database       | MySQL (see `Database/virtualstudy_db.sql`) |
| Front‑end      | HTML5, CSS3 (see `css/style.css`) |
| Server         | Apache / Nginx |
| Version Control| Git |

---

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-username/virtual-study-Asmafinal.git
   cd virtual-study-Asmafinal
   ```

2. **Create the database**

   - Import `Database/virtualstudy_db.sql` into your MySQL server.

   ```bash
   mysql -u root -p < Database/virtualstudy_db.sql
   ```

3. **Configure the application**

   - Copy `config.php.example` to `config.php` (or edit the existing `config.php`).
   - Update the database credentials:

     ```php
     // config.php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'virtualstudy');
     define('DB_USER', 'YOUR_DB_USERNAME');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ```

   - If you use an external API (e.g., for email notifications), replace any real keys with `YOUR_OWN_API_KEY`.

4. **Set up the web server**

   - Point your virtual host document root to the project folder.
   - Ensure the `uploads/` directory is writable:

     ```bash
     chmod -R 755 uploads/
     ```

5. **Install dependencies (optional)**

   The project does not rely on Composer packages, but you may add them if you extend functionality.

---

## Usage

### Normal User Flow
1. Open `index.php` in a browser.
2. Register a new account via `register.php` or log in with `login.php`.
3. Navigate using the main navbar to:
   - **Home** – Overview of available groups and sessions.
   - **My Profile** – Edit personal details (`update_profile.php`).
   - **Groups** – View, join, or create groups (`add_group.php`, `manage_groups.php`).
   - **Resources** – Upload or view resources (`add_resource.php`).
   - **Sessions** – Schedule or join study sessions (`add_session.php`).
   - **Discussions** – Participate in discussions (`view_discussion.php`).

### Admin Flow
1. Access the admin portal via `admin/admin_login.php`.
2. After authentication, you’ll land on `admin/admin_home.php` where you can:
   - Manage users (`admin/manage_users.php`).
   - Manage groups (`admin/manage_groups.php`).
   - Manage resources & discussions (`admin/manage_resources_and_discussions.php`).
   - Update admin credentials (`admin/update_admin.php`).
   - Log out (`admin/logout.php`).

### Common Scripts
| Script | Purpose |
|--------|