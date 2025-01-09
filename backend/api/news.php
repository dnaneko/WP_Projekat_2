<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "news_portal";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!$conn->query($sql_create_db)) {
    die("Error creating database: " . $conn->error);
}
$conn->select_db($dbname);

// Create categories table
$sql_create_categories_table = "
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    );
";

$conn->query($sql_create_categories_table) or die("Error creating categories table: " . $conn->error);

$sql_create_users_table = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        lastName VARCHAR(100) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        status ENUM('Pending', 'Confirmed') DEFAULT 'Pending',
        role ENUM('Admin', 'Standard') NOT NULL DEFAULT 'Standard',
        theme VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
";

$conn->query($sql_create_users_table) or die("Error creating users table: " . $conn->error);

// Create news table with date column
$sql_create_news_table = "
    CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL UNIQUE,
        content TEXT NOT NULL,
        category_id INT,
        date DATE NOT NULL,
        user_id INT,
        FOREIGN KEY (category_id) REFERENCES categories(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    );
";

$conn->query($sql_create_news_table) or die("Error creating news table: " . $conn->error);

$sql_create_tokens_table = "
    CREATE TABLE IF NOT EXISTS tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        token VARCHAR(255) NOT NULL UNIQUE,
        expires_at DATETIME,
        type ENUM('auth', 'reset') NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
";
$conn->query($sql_create_tokens_table) or die("Error creating tokens table: " . $conn->error);

// Insert categories
$categories = ['Kino', 'Game Reviews', 'Biblioteka', 'Show biz news'];
foreach ($categories as $category) {
    $sql_insert_category = "INSERT IGNORE INTO categories (name) VALUES ('$category')";
    $conn->query($sql_insert_category) or die("Error inserting category: " . $conn->error);
}

// Insert admin and standard users
$users = [
    ['Admin', 'User', 'admin', password_hash('admin123', PASSWORD_DEFAULT), 'admin@news.com', 'Confirmed', 'Admin', 'Kino']
];
for ($i = 1; $i <= 19; $i++) {
    $users[] = ['Standard', "User$i", "user$i", password_hash("password$i", PASSWORD_DEFAULT), "user$i@news.com", 'Confirmed', 'Standard', 'Game Reviews'];
}

foreach ($users as $user) {
    $sql_insert_user = "INSERT IGNORE INTO users (name, lastName, username, password, email, status, role, theme) 
                        VALUES ('$user[0]', '$user[1]', '$user[2]', '$user[3]', '$user[4]', '$user[5]', '$user[6]', '$user[7]')";
    $conn->query($sql_insert_user) or die("Error inserting user: " . $conn->error);
}

// Insert news with date
$news = [
    ['Kino News 1', 'Details about the latest movie.', 1, '2022-03-15', 1],
    ['Kino News 2', 'Upcoming blockbuster.', 1, '2023-05-20', 2],
    ['Kino News 3', 'Director interview.', 1, '2021-11-10', 3],
    ['Kino News 4', 'Film festival highlights.', 1, '2020-09-25', 4],
    ['Kino News 5', 'Top movie of the month.', 1, '2023-07-30', 5],
    ['Kino News 6', 'Cinema awards update.', 1, '2024-01-05', 6],
    ['Kino News 7', 'Classic movie review.', 1, '2020-06-18', 7],
    ['Kino News 8', 'New streaming releases.', 1, '2022-12-12', 8],
    ['Kino News 9', 'Best cinematography.', 1, '2021-04-04', 9],
    ['Kino News 10', 'Acting masterclass.', 1, '2023-02-14', 10],
    
    ['Game Review 1', 'New game review: Adventure Quest.', 2, '2021-07-08', 11],
    ['Game Review 2', 'Top 10 RPGs of the year.', 2, '2020-10-22', 12],
    ['Game Review 3', 'Indie game spotlight.', 2, '2023-01-03', 13],
    ['Game Review 4', 'Esports championship update.', 2, '2022-09-15', 14],
    ['Game Review 5', 'Upcoming releases.', 2, '2024-03-29', 15],
    ['Game Review 6', 'Gaming hardware review.', 2, '2023-06-05', 16],
    ['Game Review 7', 'Game design deep dive.', 2, '2020-04-11', 17],
    ['Game Review 8', 'Retro gaming resurgence.', 2, '2021-05-19', 18],
    ['Game Review 9', 'Open world analysis.', 2, '2022-02-27', 19],
    ['Game Review 10', 'Best graphics of the year.', 2, '2023-12-10', 20],
    
    ['Library Event 1', 'New book releases this month.', 3, '2021-08-03', 1],
    ['Library Event 2', 'Author talk event.', 3, '2020-07-16', 2],
    ['Library Event 3', 'Top borrowed books.', 3, '2023-10-05', 3],
    ['Library Event 4', 'Library expansion project.', 3, '2022-11-20', 4],
    ['Library Event 5', 'Rare book collection.', 3, '2024-02-01', 5],
    ['Library Event 6', 'Children\'s reading week.', 3, '2020-12-07', 6],
    ['Library Event 7', 'Book donation drive.', 3, '2021-06-21', 7],
    ['Library Event 8', 'Digital library launch.', 3, '2023-03-13', 8],
    ['Library Event 9', 'Classic literature spotlight.', 3, '2022-05-25', 9],
    ['Library Event 10', 'Library member perks.', 3, '2024-04-17', 10],
    
    ['Showbiz 1', 'Celebrity awards night.', 4, '2022-01-09', 11],
    ['Showbiz 2', 'Upcoming TV series.', 4, '2021-09-30', 12],
    ['Showbiz 3', 'Hollywood scandals.', 4, '2023-11-11', 13],
    ['Showbiz 4', 'New album releases.', 4, '2020-08-05', 14],
    ['Showbiz 5', 'Celebrity interviews.', 4, '2024-01-23', 15],
    ['Showbiz 6', 'Film premiere event.', 4, '2022-06-15', 16],
    ['Showbiz 7', 'Red carpet fashion.', 4, '2020-03-29', 17],
    ['Showbiz 8', 'Music festival highlights.', 4, '2021-12-19', 18],
    ['Showbiz 9', 'Best acting performances.', 4, '2023-07-27', 19],
    ['Showbiz 10', 'Trending social media stars.', 4, '2024-05-14', 20]
];

foreach ($news as $item) {
    $title = $conn->real_escape_string($item[0]);
    $content = $conn->real_escape_string($item[1]);
    $category_id = $item[2];
    $date = $item[3];
    $user_id = $item[4];

    $sql_check_news = "SELECT id FROM news WHERE title = '$title'";
    $result = $conn->query($sql_check_news);

    if ($result->num_rows == 0) {
        $sql_insert_news = "
            INSERT INTO news (title, content, category_id, date, user_id) 
            VALUES ('$title', '$content', '$category_id', '$date', '$user_id')
        ";
        $conn->query($sql_insert_news) or die("Error inserting news '$title': " . $conn->error);
    }
}

// Fetch and return news sorted by date
$from = isset($_GET['from']) ? $_GET['from'] : null;
$to = isset($_GET['to']) ? $_GET['to'] : null;
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null; // New category_id parameter

// Start the query
$sql = "
    SELECT news.id, news.title, news.content, news.date, 
        news.user_id,  -- Add this line
        categories.id as category_id, categories.name as category 
    FROM news 
    INNER JOIN categories ON news.category_id = categories.id
";

// Add conditions for 'from', 'to', and 'category_id' if provided
$whereClauses = [];

if ($from) {
    $whereClauses[] = "news.date >= '$from'"; // 'From' date
}

if ($to) {
    $whereClauses[] = "news.date <= '$to'"; // 'To' date
}

if ($category_id) {
    $whereClauses[] = "news.category_id = '$category_id'"; // 'Category ID' filter
}

// If there are any 'where' conditions, append them to the SQL query
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Add the ORDER BY clause
$sql .= " ORDER BY news.date DESC";

$result = $conn->query($sql);

$newsArray = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $newsArray[] = [
            "id" => $row["id"],
            "title" => $row["title"],
            "content" => $row["content"],
            "category_id" => $row["category_id"],
            "category" => $row["category"],
            "date" => $row["date"],
            "user_id" => $row["user_id"]
        ];
    }
    echo json_encode($newsArray);
} else {
    echo json_encode([]); // Return empty array if no news found
}

// Register user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    $name = $_POST['name'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    
    $sql_register = "
        INSERT INTO users (name, lastName, username, password, email, status, role) 
        VALUES ('$name', '$lastName', '$username', '$password', '$email', 'Pending', 'Standard')
    ";
    if ($conn->query($sql_register)) {
        echo json_encode(["success" => true, "message" => "Registration successful."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
    exit();
}

// Login user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql_login = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql_login);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $sql_token = "
                INSERT INTO tokens (user_id, token, expires_at, type) 
                VALUES ('{$user['id']}', '$token', '$expires_at', 'auth')
            ";
            $conn->query($sql_token);
            
            echo json_encode(["success" => true, "token" => $token, "user" => $user]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid credentials."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
    exit();
}

// Password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $user_id = $_POST['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $sql_user = "SELECT * FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql_user);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($old_password, $user['password'])) {
            $sql_update = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
            $conn->query($sql_update);
            echo json_encode(["success" => true, "message" => "Password updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect old password."]);
        }
    }
    exit();
}

$conn->close();
?>
