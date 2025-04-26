<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galgatábor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/galgatabor/css/style.css">
</head>
<body>
<!-- Extra top bar -->
<div class="text-light py-2 top-bar" style="font-size: 0.85rem;">
    <div class="container d-flex justify-content-between">
<div>
    <img src="/galgatabor/public/call_icon.png" alt="Phone" style="height: 20px;">
    <span><a href="tel:+36702613126">+36 70 261 3126</a></span>
    <span class="mx-3">|</span>
    <img src="/galgatabor/public/mail_icon.png" alt="Email" style="height: 20px;">
    <span><a href="mailto:info@galgatabor.hu">info@galgatabor.hu</a></span>
</div>


        <div>
        <span id="date" class="text-light me-3" style="font-size: 0.85rem;"></span>
            <img src="/galgatabor/public/facebook_logo.png" style="height: 0.85rem; opacity: 50%" alt="Facebook logó">
        </div>
    </div>
</div>

<!-- Main navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-wheat">
    <div class="container">
        <a class="navbar-brand" href="/galgatabor/index.php">
            <img src="/galgatabor/public/logo1.png" alt="Főoldal" height="30">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/galgatabor/index.php">Táboraink 2025</a></li>
                <li class="nav-item"><a class="nav-link" href="/galgatabor/pages/gyik.php">Fontos tudnivalók</a></li>
                <li class="nav-item"><a class="nav-link" href="/galgatabor/pages/rolunk.php">Rólunk</a></li>
                <li class="nav-item"><a class="nav-link" href="/galgatabor/pages/kapcsolat.php">Kapcsolat</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">

<script>
    const months = ["január", "február", "március", "április", "május", "június",
                    "július", "augusztus", "szeptember", "október", "november", "december"];

    const today = new Date();
    const formatted = today.getFullYear() + ". " + months[today.getMonth()] + " " + today.getDate() + ".";
    document.getElementById("date").innerText = formatted;
</script>
