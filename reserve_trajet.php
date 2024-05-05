<?php
session_start();
require_once './includes/database.php';

if (!isset($_POST['trajet_id']) || !isset($_SESSION['user_id']) || $_SESSION['types'] !== 'passager') {
    header('Location: index.php');  // Rediriger si les conditions ne sont pas remplies
    exit;
}

$conn = connect();
$trajet_id = $_POST['trajet_id'];
$passager_id = $_SESSION['user_id'];

$sql = "INSERT INTO reservations (trajet_id, passager_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $trajet_id, $passager_id);
if ($stmt->execute()) {
    $message = "Réservation enregistrée avec succès. En attente de confirmation.";
} else {
    $message = "Erreur lors de la réservation: " . $conn->error;
}
$stmt->close();
$conn->close();

header('Location: reservations.php?message=' . urlencode($message));  // Rediriger avec un message
exit;
?>
