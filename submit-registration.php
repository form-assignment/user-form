<?php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'registration_db');
define('DB_USER', getenv('DB_USER') ?: '');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_SSL_CA', __DIR__ . '/ca-cert.pem');

function getDBConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::MYSQL_ATTR_SSL_CA => DB_SSL_CA,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html?status=error&message=' . urlencode('Method not allowed.'));
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$age = $_POST['age'] ?? null;
$gender = $_POST['gender'] ?? '';
$country = $_POST['country'] ?? '';
$interests = $_POST['interests'] ?? [];
$bio = trim($_POST['bio'] ?? '');

$errors = [];

if (empty($fullName)) {
    $errors[] = 'Full Name is required.';
}

if (empty($email)) {
    $errors[] = 'Email Address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

if ($age !== null && $age !== '') {
    $age = (int)$age;
    if ($age < 18 || $age > 120) {
        $errors[] = 'Age must be between 18 and 120.';
    }
}

if (empty($gender)) {
    $errors[] = 'Gender is required.';
} elseif (!in_array($gender, ['Male', 'Female', 'Prefer not to say'])) {
    $errors[] = 'Invalid gender selection.';
}

if (empty($country)) {
    $errors[] = 'Country is required.';
}

if (!is_array($interests)) {
    $errors[] = 'Invalid interests data.';
}

if (strlen($bio) > 500) {
    $errors[] = 'Bio must not exceed 500 characters.';
}

if (!empty($errors)) {
    header('Location: index.html?status=error&message=' . urlencode(implode(' ', $errors)));
    exit;
}

try {
    $pdo = getDBConnection();

    if ($pdo === null) {
        header('Location: index.html?status=error&message=' . urlencode('Database connection failed. Please try again later.'));
        exit;
    }

    $sql = "INSERT INTO users (full_name, email, age, gender, country, interests, bio, created_at)
            VALUES (:full_name, :email, :age, :gender, :country, :interests, :bio, NOW())";

    $stmt = $pdo->prepare($sql);

    $interestsString = implode(', ', $interests);

    $stmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindParam(':country', $country, PDO::PARAM_STR);
    $stmt->bindParam(':interests', $interestsString, PDO::PARAM_STR);
    $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);

    $stmt->execute();

    header('Location: index.html?status=success&message=' . urlencode('Registration successful! Welcome, ' . $fullName . '.'));
    exit;
} catch (PDOException $e) {
    header('Location: index.html?status=error&message=' . urlencode('Database error: ' . $e->getMessage()));
    exit;
}
?>