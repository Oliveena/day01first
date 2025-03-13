<?php

// Coolors used for color palette: https://coolors.co/palette/eae4e9-fff1e6-fde2e4-fad2e1-e2ece9-bee1e6-f0efeb-dfe7fd-cddafd 
if ($_SERVER['QUERY_STRING'] == 'noname') {
    //unset($_SESSION['name']);
    session_unset();
}
$name = $_SESSION['name'] ?? 'Guest';
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog: Index</title>
    <style>
        /*TODO: fix the front-end
        .header {
            padding: 20px 0;
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #FAD2E1;
        }*/

/* Header Styling */
.header {
    /*background-color: #E2ECE9;*/
    padding: 20px 0;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.brand-logo {
    font-size: 2rem;
    font-family: 'Georgia', serif;
    color: #84A4FC;
    text-decoration: none;
    font-weight: bold;
    margin-left: 20px; /* Add some margin to space out from the left */
}

/* Navbar Styles */
.navbar {
    margin-top: 10px;
    text-align: center;
}

.nav-links {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex; /* Change to flex to align items horizontally */
    justify-content: center; /* Center the items horizontally */
    align-items: center; /* Align items vertically */
}

.nav-item {
    margin: 0 15px; /* Space between items */
    font-size: 1rem;
}

.nav-item a {
    color: #F8A9C6;
    text-decoration: none;
    font-weight: bold;
}

.nav-item a:hover {
    text-decoration: underline;
}

/* Button Styles */
.btn {
    background-color: #84A4FC;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    text-transform: uppercase;
}

.btn:hover {
    background-color: #FDE2E4;
    color: #F8A9C6;
    border: 1px solid #F8A9C6;
}

/* Login/Register Messages */
p {
    color: #333;
    font-size: 0.9rem;
    margin: 5px 0;
}

p a {
    color: #F8A9C6;
    text-decoration: none;
}

p a:hover {
    text-decoration: underline;
}

    </style>
</head>

<body>
    <!-- Header Navigation -->
    <header class="header">
        <a href="index.php" class="brand-logo">My Blog</a>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item">You are logged in as <?php echo htmlspecialchars($name); ?></li>
                <?php if (!isset($_SESSION['name'])): ?>
                    <li class="nav-item"><a href="login.php" class="btn">Login</a></li>
                    <p>or</p>
                    <li class="nav-item"><a href="registration.php" class="btn">Register</a></li>
                    <p>to post articles and comments</p>
                <?php else: ?>
                    <li class="nav-item"><a href="logout.php" class="btn">Log Out</a></li>

                    <li class="nav-item"><a href="articleadd.php" class="btn">Write A Blog Post</a></li>

                <?php endif; ?>
            </ul>
        </nav>