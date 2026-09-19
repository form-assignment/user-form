<?php
// Supabase (PostgreSQL) connection via PDO_PGSQL
// Replace these with your Supabase project credentials
define('DB_HOST', 'aws-0-eu-west-2.pooler.supabase.com');
define('DB_PORT', '5432');
define('DB_NAME', 'postgres');
define('DB_USER', 'postgres.rwlmzeqiniruoqcwpyim');
define('DB_PASS', 'HAALAND9d#3');

function getDBConnection() {
    try {
        $dsn = 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';sslmode=require';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
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
    echo json_encode(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors]);
    exit;
}

try {
    $pdo = getDBConnection();

    $sql = "INSERT INTO users (full_name, email, age, gender, country, interests, bio, created_at)
            VALUES (:full_name, :email, :age, :gender, :country, :interests, :bio, NOW())
            RETURNING id";

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

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Welcome, ' . htmlspecialchars($fullName) . '.',
        'user_id' => $row['id'] ?? null
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>