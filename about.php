<?php
session_start();
require_once 'config/database.php';

// Get all page content from database
$query = "SELECT section_key, content, content_type FROM page_content WHERE page_name = 'about'";
$result = $conn->query($query);
$page_content = [];
while ($row = $result->fetch_assoc()) {
    $page_content[$row['section_key']] = [
        'content' => $row['content'],
        'type' => $row['content_type']
    ];
}

// Get features from database
$features_query = "SELECT * FROM features WHERE is_active = 1 ORDER BY display_order ASC";
$features_result = $conn->query($features_query);
$features = [];
while ($row = $features_result->fetch_assoc()) {
    $features[] = $row;
}

// Get team members from database
$team_query = "SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC";
$team_result = $conn->query($team_query);
$team_members = [];
while ($row = $team_result->fetch_assoc()) {
    $team_members[] = $row;
}

// Get testimonials from database
$testimonials_query = "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC";
$testimonials_result = $conn->query($testimonials_query);
$testimonials = [];
while ($row = $testimonials_result->fetch_assoc()) {
    $testimonials[] = $row;
}

// Helper function to get content
function getContent($key, $default = '')
{
    global $page_content;
    if (isset($page_content[$key])) {
        if ($page_content[$key]['type'] == 'html') {
            return $page_content[$key]['content'];
        }
        return htmlspecialchars($page_content[$key]['content']);
    }
    return $default;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getContent('hero_title', 'About Us'); ?> - FreshGrocer</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            line-height: 1.6;
        }

        /* Modern Container */
        .about-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 60px 24px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(105deg, #1e293b 0%, #334155 50%, #475569 100%);
            border-radius: 48px;
            padding: 100px 48px;
            margin-bottom: 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: movePattern 20s linear infinite;
            opacity: 0.3;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            pointer-events: none;
        }

        @keyframes movePattern {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        .hero-section h1 {
            font-size: 64px;
            font-weight: 800;
            margin-bottom: 24px;
            color: white;
            position: relative;
            animation: fadeInUp 0.8s ease;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #fff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-section p {
            font-size: 20px;
            max-width: 700px;
            margin: 0 auto;
            color: #cbd5e1;
            position: relative;
            animation: fadeInUp 0.8s ease 0.2s both;
            line-height: 1.7;
        }

        /* Story Section */
        .story-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            background: white;
            border-radius: 40px;
            padding: 60px;
            margin-bottom: 80px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .story-section:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.15);
            border-color: rgba(99, 102, 241, 0.2);
        }

        .story-content h2 {
            font-size: 42px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .story-content p {
            color: #475569;
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.8;
        }

        .story-image {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .story-image:hover {
            transform: scale(1.02);
        }

        .story-image i {
            font-size: 140px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: float 3s ease-in-out infinite;
        }

        /* Features Section */
        .features-section {
            margin-bottom: 80px;
        }

        .features-section h2 {
            text-align: center;
            font-size: 48px;
            margin-bottom: 60px;
            background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
        }

        .feature-card {
            background: white;
            padding: 40px 32px;
            border-radius: 28px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 40px -12px rgba(0, 0, 0, 0.15);
            border-color: rgba(59, 130, 246, 0.2);
        }

        .feature-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #eff6ff 0%, #f3e8ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
        }

        .feature-icon i {
            font-size: 44px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-card h3 {
            font-size: 24px;
            margin-bottom: 16px;
            color: #0f172a;
            font-weight: 600;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.7;
            font-size: 15px;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 48px;
            padding: 70px 48px;
            margin-bottom: 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -20%;
            width: 140%;
            height: 140%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: pulse 10s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .stats-section h2 {
            color: white;
            font-size: 48px;
            margin-bottom: 60px;
            position: relative;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
            position: relative;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 32px 24px;
            border-radius: 28px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 52px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #cbd5e1;
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Team Section */
        .team-section {
            margin-bottom: 80px;
        }

        .team-section h2 {
            text-align: center;
            font-size: 48px;
            margin-bottom: 60px;
            background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
        }

        .team-card {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .team-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 40px -12px rgba(0, 0, 0, 0.15);
        }

        .team-photo {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .team-photo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .team-card:hover .team-photo::before {
            left: 100%;
        }

        .team-photo i {
            font-size: 90px;
            color: white;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .team-card h3 {
            font-size: 24px;
            margin: 24px 0 8px;
            color: #0f172a;
            font-weight: 600;
        }

        .team-position {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .team-bio {
            color: #64748b;
            padding: 0 24px 28px;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Testimonials Section */
        .testimonials-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 48px;
            padding: 70px 48px;
            margin-bottom: 80px;
        }

        .testimonials-section h2 {
            text-align: center;
            font-size: 48px;
            margin-bottom: 60px;
            background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 32px;
        }

        .testimonial-card {
            background: white;
            padding: 36px;
            border-radius: 28px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            position: relative;
            border: 1px solid #e2e8f0;
        }

        .testimonial-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 40px -12px rgba(0, 0, 0, 0.12);
            border-color: #cbd5e1;
        }

        .testimonial-card::before {
            content: '"';
            font-size: 100px;
            position: absolute;
            top: 20px;
            left: 24px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: Georgia, serif;
            opacity: 0.15;
            font-weight: bold;
        }

        .rating {
            color: #fbbf24;
            margin-bottom: 20px;
            font-size: 18px;
            letter-spacing: 2px;
        }

        .testimonial-text {
            color: #334155;
            line-height: 1.8;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
            font-size: 15px;
        }

        .testimonial-author {
            font-weight: 700;
            color: #0f172a;
            margin-top: 8px;
            font-size: 16px;
        }

        .testimonial-role {
            color: #94a3b8;
            font-size: 13px;
            margin-top: 4px;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 48px;
            padding: 80px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
            animation: rotate 25s linear infinite;
        }

        .cta-section::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -30%;
            width: 80%;
            height: 200%;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%);
            animation: rotateReverse 30s linear infinite;
        }

        @keyframes rotateReverse {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(-360deg);
            }
        }

        .cta-section h2 {
            color: white;
            font-size: 52px;
            margin-bottom: 20px;
            position: relative;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .cta-section p {
            color: #cbd5e1;
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 32px;
            position: relative;
            line-height: 1.7;
        }

        .cta-button {
            display: inline-block;
            background: white;
            color: #1e293b;
            padding: 16px 48px;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        .cta-button:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -8px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .about-container {
                padding: 40px 20px;
            }

            .hero-section h1 {
                font-size: 48px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 32px;
            }

            .hero-section h1 {
                font-size: 36px;
            }

            .hero-section p {
                font-size: 16px;
            }

            .story-section {
                grid-template-columns: 1fr;
                padding: 40px 32px;
                gap: 40px;
            }

            .story-content h2 {
                font-size: 32px;
            }

            .features-section h2,
            .stats-section h2,
            .team-section h2,
            .testimonials-section h2,
            .cta-section h2 {
                font-size: 36px;
            }

            .stats-section {
                padding: 50px 32px;
            }

            .testimonials-section {
                padding: 50px 32px;
            }

            .cta-section {
                padding: 60px 32px;
            }

            .cta-section h2 {
                font-size: 36px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            .stat-number {
                font-size: 36px;
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .features-grid,
            .team-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .hero-section {
                padding: 40px 24px;
            }

            .hero-section h1 {
                font-size: 32px;
            }

            .story-section {
                padding: 32px 24px;
            }

            .feature-card {
                padding: 32px 24px;
            }

            .testimonial-card {
                padding: 28px 24px;
            }
        }

        @media (max-width: 480px) {
            .about-container {
                padding: 30px 16px;
            }

            .hero-section h1 {
                font-size: 28px;
            }

            .features-section h2,
            .stats-section h2,
            .team-section h2,
            .testimonials-section h2,
            .cta-section h2 {
                font-size: 28px;
            }

            .stat-number {
                font-size: 32px;
            }

            .cta-button {
                padding: 14px 36px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="about-container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1><?php echo getContent('hero_title', 'About FreshGrocer'); ?></h1>
            <p><?php echo getContent('hero_subtitle', 'Your trusted partner for fresh groceries'); ?></p>
        </div>

        <!-- Story Section -->
        <div class="story-section">
            <div class="story-content">
                <h2><?php echo getContent('story_title', 'Our Story'); ?></h2>
                <?php echo getContent('story_content', '<p>FreshGrocer started in 2020 with a simple yet powerful vision: to revolutionize the way people shop for groceries.</p>'); ?>
            </div>
            <div class="story-image">
                <i class="fas fa-seedling"></i>
            </div>
        </div>

        <!-- Features Section -->
        <?php if (count($features) > 0): ?>
            <div class="features-section">
                <h2><?php echo getContent('features_title', 'Why Choose Us'); ?></h2>
                <div class="features-grid">
                    <?php foreach ($features as $feature): ?>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas <?php echo htmlspecialchars($feature['icon']); ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($feature['title']); ?></h3>
                            <p><?php echo htmlspecialchars($feature['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <!-- Team Section -->
        <?php if (count($team_members) > 0): ?>
            <div class="team-section">
                <h2><?php echo getContent('team_title', 'Meet Our Leadership Team'); ?></h2>
                <div class="team-grid">
                    <?php foreach ($team_members as $member): ?>
                        <div class="team-card">
                            <div class="team-photo">
                                <?php if ($member['photo_url']): ?>
                                    <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-user-circle"></i>
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                            <div class="team-position"><?php echo htmlspecialchars($member['position']); ?></div>
                            <div class="team-bio"><?php echo htmlspecialchars($member['bio']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Testimonials Section -->
        <?php if (count($testimonials) > 0): ?>
            <div class="testimonials-section">
                <h2><?php echo getContent('testimonials_title', 'What Our Customers Say'); ?></h2>
                <div class="testimonials-grid">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="testimonial-card">
                            <div class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $testimonial['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="testimonial-text"><?php echo htmlspecialchars($testimonial['content']); ?></div>
                            <div class="testimonial-author"><?php echo htmlspecialchars($testimonial['customer_name']); ?></div>
                            <div class="testimonial-role"><?php echo htmlspecialchars($testimonial['customer_role']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- CTA Section -->
        <div class="cta-section">
            <h2><?php echo getContent('cta_title', 'Join Our Fresh Community'); ?></h2>
            <?php echo getContent('cta_content', '<p>Experience the convenience of fresh groceries delivered to your doorstep.</p>'); ?>
            <a href="http://localhost:9090/Groceryproject/pages/register.php" class="cta-button"><?php echo getContent('cta_button_text', 'Get Started Now'); ?></a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>