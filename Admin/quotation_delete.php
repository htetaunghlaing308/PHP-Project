<?php

require_once "auth.php";
require_once "database.php";

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {

    $st = $conn->prepare("DELETE FROM quotations WHERE id = ?");
    $st->bind_param("i", $id);
    $st->execute();
}

/* Stay on Quotations page */
header("Location: quotations.php");
exit;
?>