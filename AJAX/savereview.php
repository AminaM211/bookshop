<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "You must be logged in to submit a review.";
    exit();
}

include_once '../classes/db.php';
include '../classes/Review.php'; 

// Maak databaseverbinding
$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$user_id = $_SESSION['user_id']; 


if (!empty($_POST)) {
    $r = new Review($conn);
    $r->setBook_id($_POST['book_id']);
    $r->setUser_id($user_id);
    $r->setComment($_POST['comment']);
    $r->setScore($_POST['score']);
    $r->setTitle($_POST['title']);
    $book_id = $_POST['book_id'];
    $comment = $_POST['comment'];
    $score = $_POST['score'];
    $title = $_POST['title'];
    $r->saveReview($user_id, $book_id, $comment, $score, $title);

    $response = [
        'status' => 'success',
        'message' => 'Review submitted successfully.',
        'body' => htmlspecialchars($r->getComment()),
        'title' => htmlspecialchars($r->getTitle()),
        'name' => htmlspecialchars($_SESSION['name']),
        'score' => htmlspecialchars($r->getScore())
    ];

    header ('Content-Type: application/json');
    echo json_encode($response);
}


$conn->close();
?>
