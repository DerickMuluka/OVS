<header style="display: flex; flex-direction: column; align-items: center; padding: 0; margin: 0;">
    <h1 style="margin: 0; font-size: 2em; width: 100%; padding: 20px 0; background-color: #003366; color: #fff; text-align: center; position: relative; z-index: 1;">Online Voting System</h1>
    <nav class="navbar" style="width: 100%; padding: 10px 20px; background-color: #03A9F4; border-radius: 0; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); position: relative; top: 0; z-index: 0;">
        <div style="display: flex; justify-content: center; width: 100%; margin: 0;">
            <a href="http://localhost/online_voting_system/index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="http://localhost/online_voting_system/info/how_it_works.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'how_it_works.php' ? 'active' : ''; ?>">How It Works</a>
            <a href="http://localhost/online_voting_system/voter/register.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">Register</a>
            <a href="http://localhost/online_voting_system/voter/login.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a>
            <a href="http://localhost/online_voting_system/info/about_us.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about_us.php' ? 'active' : ''; ?>">About Us</a>
            <a href="http://localhost/online_voting_system/info/logout.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Logout</a>
        </div>
        <a href="http://localhost/online_voting_system/admin/login.php" style="
    position: absolute;
    right: 30px; /* Increased from 20px to 30px */
    background-color: #005f73;
    color: #f0f4f8;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 0.9em;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s, transform 0.2s;
" class="admin-login">Officials</a>
    </nav>
</header>
