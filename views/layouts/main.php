<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?= $pageDescription ?? 'University Portal - Student Management System' ?>">
    <meta name="author" content="University Portal">
    
    <title><?= $pageTitle ?? 'University Portal' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/utilities.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Page-specific CSS -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">
    
    <!-- Header Component -->
    <?php if (!isset($hideHeader) || !$hideHeader): ?>
        <?php include __DIR__ . '/../components/header.php'; ?>
    <?php endif; ?>
    
    <!-- Main Content Area -->
    <main class="main-content" id="mainContent">
        <?php 
        // Render the page content
        echo $content ?? ''; 
        ?>
    </main>
    
    <!-- Bottom Navigation (for students) -->
    <?php if (isset($showBottomNav) && $showBottomNav): ?>
        <?php include __DIR__ . '/../components/bottom-nav.php'; ?>
    <?php endif; ?>
    
    <!-- Sidebar Navigation (for admin/teacher) -->
    <?php if (isset($showSidebar) && $showSidebar): ?>
        <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <?php endif; ?>
    
    <!-- Footer Component -->
    <?php if (!isset($hideFooter) || !$hideFooter): ?>
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; <?= date('Y') ?> University Portal. All rights reserved.</p>
            </div>
        </footer>
    <?php endif; ?>
    
    <!-- Core JavaScript -->
    <script src="/assets/js/dark-mode.js"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript -->
    <?php if (isset($inlineJS)): ?>
        <script>
            <?= $inlineJS ?>
        </script>
    <?php endif; ?>
    
</body>
</html>
