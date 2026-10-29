<?php
// stories.php - Stories & Updates (Blog Listing Page)

// Define page-specific CSS
$extraCSS = [
    'assets/css/stories.css'
];

// Include DB connection
require_once 'includes/db.php';

// Pagination setup
$perPage = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

// Fetch total published stories
$totalStmt = $pdo->query("SELECT COUNT(*) FROM stories WHERE status = 'published'");
$totalStories = (int)$totalStmt->fetchColumn();
$totalPages = (int)ceil($totalStories / $perPage);

// Fetch paginated stories
$sql = "SELECT id, title, slug, excerpt, featured_image, category, author, published_at 
        FROM stories 
        WHERE status = 'published' 
        ORDER BY published_at DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$stories = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- STORIES BANNER SECTION -->
<section class="stories-banner-section">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/stories-banner-mobile.jpg">
        <img src="assets/images/stories-banner.jpg" alt="Stories & Updates">
    </picture>
</section>

<!-- STORIES LISTING SECTION -->
<section class="stories-listing-section">
    <div class="container">
        
        <?php if (empty($stories)): ?>
            <div class="no-stories">
                <i class="fas fa-newspaper"></i>
                <h3>No Stories Yet</h3>
                <p>We're working on bringing you updates. Please check back soon!</p>
            </div>
        <?php else: ?>
            
            <div class="stories-grid">
                <?php foreach ($stories as $story): ?>
                    <article class="story-card">
                        <a href="story.php?slug=<?php echo urlencode($story['slug']); ?>" class="story-card-link">
                            
                            <div class="story-card-image">
                                <?php if (!empty($story['featured_image']) && file_exists($story['featured_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($story['featured_image']); ?>" alt="<?php echo htmlspecialchars($story['title']); ?>">
                                <?php else: ?>
                                    <div class="story-placeholder">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="story-category"><?php echo htmlspecialchars($story['category']); ?></span>
                            </div>

                            <div class="story-card-content">
                                <div class="story-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($story['published_at'])); ?></span>
                                    <span><i class="far fa-user"></i> <?php echo htmlspecialchars($story['author']); ?></span>
                                </div>
                                <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                                <p><?php echo htmlspecialchars($story['excerpt']); ?></p>
                                <span class="story-read-more">Read More <i class="fas fa-arrow-right"></i></span>
                            </div>

                        </a>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- PAGINATION -->
            <?php if ($totalPages > 1): ?>
                <div class="stories-pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-btn prev">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>

                    <div class="page-numbers">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="page-num <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-btn next">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<?php
include 'includes/footer.php';
?>