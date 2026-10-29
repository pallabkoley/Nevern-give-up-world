<?php
// story.php - Single Story Detail Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/story.css'
];

// Include DB connection
require_once 'includes/db.php';

// Get slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

// Redirect to stories list if no slug
if (empty($slug)) {
    header('Location: stories.php');
    exit;
}

// Fetch the story
$sql = "SELECT * FROM stories WHERE slug = :slug AND status = 'published' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
$stmt->execute();
$story = $stmt->fetch();

// If story not found, redirect to stories list
if (!$story) {
    header('Location: stories.php');
    exit;
}

// Fetch related stories (same category, excluding current)
$relSql = "SELECT id, title, slug, featured_image, category, published_at 
           FROM stories 
           WHERE status = 'published' 
             AND category = :category 
             AND id != :id 
           ORDER BY published_at DESC 
           LIMIT 3";
$relStmt = $pdo->prepare($relSql);
$relStmt->bindValue(':category', $story['category'], PDO::PARAM_STR);
$relStmt->bindValue(':id', $story['id'], PDO::PARAM_INT);
$relStmt->execute();
$relatedStories = $relStmt->fetchAll();

include 'includes/header.php';
?>

<!-- STORIES BANNER SECTION -->
<section class="stories-banner-section">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/stories-banner-mobile.jpg">
        <img src="assets/images/stories-banner.jpg" alt="Stories & Updates">
    </picture>
</section>

<!-- SINGLE STORY SECTION -->
<section class="single-story-section">
    <div class="container">
        
        <!-- Breadcrumb -->
        <div class="story-breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="stories.php">Stories &amp; Updates</a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($story['title']); ?></span>
        </div>

        <article class="single-story-article">
            
            <!-- Category Badge -->
            <span class="single-story-category"><?php echo htmlspecialchars($story['category']); ?></span>

            <!-- Title -->
            <h1 class="single-story-title"><?php echo htmlspecialchars($story['title']); ?></h1>

            <!-- Meta -->
            <div class="single-story-meta">
                <span><i class="far fa-calendar-alt"></i> <?php echo date('F d, Y', strtotime($story['published_at'])); ?></span>
                <span><i class="far fa-user"></i> <?php echo htmlspecialchars($story['author']); ?></span>
            </div>

            <!-- Featured Image -->
            <?php if (!empty($story['featured_image']) && file_exists($story['featured_image'])): ?>
                <div class="single-story-image">
                    <img src="<?php echo htmlspecialchars($story['featured_image']); ?>" alt="<?php echo htmlspecialchars($story['title']); ?>">
                </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="single-story-content">
                <?php echo $story['content']; ?>
            </div>

            <!-- Share Section -->
            <div class="single-story-share">
                <span>Share this story:</span>
                <div class="share-icons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener" class="share-facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($story['title']); ?>" target="_blank" rel="noopener" class="share-twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($story['title']); ?>" target="_blank" rel="noopener" class="share-linkedin">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($story['title'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener" class="share-whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <!-- Back Button -->
            <div class="single-story-back">
                <a href="stories.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Stories
                </a>
            </div>

        </article>

        <!-- RELATED STORIES -->
        <?php if (!empty($relatedStories)): ?>
            <section class="related-stories-section">
                <h2 class="related-title">Related Stories</h2>
                <div class="related-stories-grid">
                    <?php foreach ($relatedStories as $related): ?>
                        <article class="related-story-card">
                            <a href="story.php?slug=<?php echo urlencode($related['slug']); ?>" class="related-story-link">
                                <div class="related-story-image">
                                    <?php if (!empty($related['featured_image']) && file_exists($related['featured_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($related['featured_image']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                    <?php else: ?>
                                        <div class="related-story-placeholder">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="related-story-content">
                                    <span class="related-story-category"><?php echo htmlspecialchars($related['category']); ?></span>
                                    <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                                    <span class="related-story-date">
                                        <i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($related['published_at'])); ?>
                                    </span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    </div>
</section>

<?php
include 'includes/footer.php';
?>