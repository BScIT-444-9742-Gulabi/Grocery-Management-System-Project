<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../config/database.php';
require_once '../includes/content_helper.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_content'])) {
        foreach ($_POST['content'] as $key => $value) {
            list($page, $section) = explode('|', $key);
            $stmt = $conn->prepare("INSERT INTO page_content (page_name, section_key, content, content_type) VALUES (?, ?, ?, 'text') ON DUPLICATE KEY UPDATE content = ?");
            $stmt->bind_param("ssss", $page, $section, $value, $value);
            $stmt->execute();
        }
        $success_message = "Content updated successfully!";
    }

    if (isset($_POST['add_feature'])) {
        $stmt = $conn->prepare("INSERT INTO features (icon, title, description, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $_POST['icon'], $_POST['title'], $_POST['description'], $_POST['display_order']);
        $stmt->execute();
        $success_message = "Feature added successfully!";
    }

    if (isset($_POST['add_team_member'])) {
        $stmt = $conn->prepare("INSERT INTO team_members (name, position, bio, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $_POST['name'], $_POST['position'], $_POST['bio'], $_POST['display_order']);
        $stmt->execute();
        $success_message = "Team member added successfully!";
    }

    if (isset($_POST['add_testimonial'])) {
        $stmt = $conn->prepare("INSERT INTO testimonials (customer_name, customer_role, content, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $_POST['customer_name'], $_POST['customer_role'], $_POST['content'], $_POST['display_order']);
        $stmt->execute();
        $success_message = "Testimonial added successfully!";
    }
}

// Get current content
$sections = [
    'hero_title' => getPageContent('about', 'hero_title'),
    'hero_subtitle' => getPageContent('about', 'hero_subtitle'),
    'story_title' => getPageContent('about', 'story_title'),
    'story_content' => getPageContent('about', 'story_content'),
    'stats_title' => getPageContent('about', 'stats_title'),
    'team_title' => getPageContent('about', 'team_title'),
    'testimonials_title' => getPageContent('about', 'testimonials_title'),
    'cta_title' => getPageContent('about', 'cta_title'),
    'cta_content' => getPageContent('about', 'cta_content'),
    'cta_button_text' => getPageContent('about', 'cta_button_text')
];

$features = getFeatures();
$team_members = getTeamMembers();
$testimonials = getTestimonials();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit About Page - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #1f2937;
            line-height: 1.6;
            min-height: 100vh;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        /* Header Section */
        .admin-header {
            background: white;
            border-radius: 24px;
            padding: 35px 40px;
            margin-bottom: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .admin-header h1 {
            font-size: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .admin-header p {
            color: #6b7280;
            font-size: 16px;
        }

        /* Button Group */
        .button-group {
            display: flex;
            gap: 18px;
            margin-bottom: 35px;
            flex-wrap: wrap;
        }

        /* Previous Page Button */
        .prev-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .prev-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .prev-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .prev-btn:hover {
            transform: translateX(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
        }

        .prev-btn i {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        .prev-btn:hover i {
            transform: translateX(-5px);
        }

        /* Dashboard Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .back-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .back-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .back-btn:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .back-btn i {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        .back-btn:hover i {
            transform: translateX(5px);
        }

        /* Message Alert */
        .alert {
            padding: 18px 24px;
            border-radius: 16px;
            margin-bottom: 30px;
            font-weight: 500;
            animation: slideDown 0.5s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert i {
            font-size: 22px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 5px solid #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 5px solid #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Section Cards */
        .section-card {
            background: white;
            border-radius: 28px;
            padding: 35px;
            margin-bottom: 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .section-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.12);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .section-card h2 {
            font-size: 26px;
            margin-bottom: 25px;
            color: #1f2937;
            border-left: 5px solid #667eea;
            padding-left: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-card h2 i {
            color: #667eea;
            font-size: 28px;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        .form-group label i {
            margin-right: 8px;
            color: #667eea;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
            background: #f9fafb;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkbox-group input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(239, 68, 68, 0.3);
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        /* Item Lists */
        .item-list {
            margin-top: 25px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .item-list::-webkit-scrollbar {
            width: 8px;
        }

        .item-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .item-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        .item-card {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            padding: 18px 20px;
            margin-bottom: 12px;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .item-card:hover {
            background: white;
            transform: translateX(8px);
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .item-info {
            flex: 1;
        }

        .item-info strong {
            color: #1f2937;
            font-size: 16px;
            display: block;
            margin-bottom: 5px;
        }

        .item-info p {
            color: #6b7280;
            font-size: 13px;
            margin-top: 4px;
            line-height: 1.5;
        }

        /* Add Form */
        .add-form {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            padding: 25px;
            border-radius: 20px;
            margin-top: 25px;
            border: 2px dashed #e5e7eb;
            transition: all 0.3s ease;
        }

        .add-form:hover {
            border-color: #667eea;
            background: white;
        }

        .add-form h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #374151;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-form h3 i {
            color: #667eea;
        }

        /* Grid Layout */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .admin-container {
                padding: 30px 20px;
            }

            .section-card {
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 20px 16px;
            }

            .admin-header {
                padding: 25px 20px;
            }

            .admin-header h1 {
                font-size: 28px;
            }

            .grid-2 {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .button-group {
                flex-direction: column;
                gap: 12px;
            }

            .prev-btn,
            .back-btn {
                justify-content: center;
                width: 100%;
            }

            .section-card {
                padding: 20px;
                margin-bottom: 25px;
            }

            .section-card h2 {
                font-size: 22px;
            }

            .item-card {
                flex-direction: column;
                text-align: center;
                gap: 12px;
            }

            .btn-danger {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .admin-header h1 {
                font-size: 24px;
            }

            .admin-header p {
                font-size: 14px;
            }

            .form-group input[type="text"],
            .form-group textarea {
                padding: 12px 15px;
            }

            .btn-primary {
                width: 100%;
                justify-content: center;
            }
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }

        /* Tooltip Styles */
        [data-tooltip] {
            position: relative;
            cursor: pointer;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: #1f2937;
            color: white;
            font-size: 12px;
            border-radius: 8px;
            white-space: nowrap;
            display: none;
            z-index: 1000;
        }

        [data-tooltip]:hover:before {
            display: block;
        }

        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .badge-primary {
            background: linear-gradient(135deg, #667eea20, #764ba220);
            color: #667eea;
        }

        /* Footer Buttons */
        .form-footer {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 480px) {
            .form-footer {
                flex-direction: column;
            }

            .form-footer .btn-primary,
            .form-footer .btn-danger {
                width: 100%;
                justify-content: center;
            }
        }

        #btnBack {
            margin-bottom: 10px;
        }
        .prev-btn{
            margin-left: 60px;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="button-group">
            <button onclick="history.back()" class="prev-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Previous Page</span>
            </button>
            <a href="dashboard.php" class="back-btn">
                <i class="fas fa-tachometer-alt"></i>
                <span>Go To Dashboard</span>
            </a>
        </div>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Edit Text Content -->
        <form method="POST" class="section-card">
            <h2>Text Content</h2>

            <div class="form-group">
                <label>Hero Title</label>
                <input type="text" name="content[about|hero_title]" value="<?php echo htmlspecialchars($sections['hero_title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Hero Subtitle</label>
                <textarea name="content[about|hero_subtitle]" rows="3"><?php echo htmlspecialchars($sections['hero_subtitle']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Story Title</label>
                <input type="text" name="content[about|story_title]" value="<?php echo htmlspecialchars($sections['story_title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Story Content (HTML allowed)</label>
                <textarea name="content[about|story_content]" rows="6"><?php echo htmlspecialchars($sections['story_content']); ?></textarea>
                <small>You can use HTML tags like &lt;p&gt;, &lt;strong&gt;, etc.</small>
            </div>

            <div class="form-group">
                <label>Statistics Title</label>
                <input type="text" name="content[about|stats_title]" value="<?php echo htmlspecialchars($sections['stats_title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Team Section Title</label>
                <input type="text" name="content[about|team_title]" value="<?php echo htmlspecialchars($sections['team_title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Testimonials Title</label>
                <input type="text" name="content[about|testimonials_title]" value="<?php echo htmlspecialchars($sections['testimonials_title']); ?>" required>
            </div>

            <div class="form-group">
                <label>CTA Title</label>
                <input type="text" name="content[about|cta_title]" value="<?php echo htmlspecialchars($sections['cta_title']); ?>" required>
            </div>

            <div class="form-group">
                <label>CTA Content (HTML allowed)</label>
                <textarea name="content[about|cta_content]" rows="3"><?php echo htmlspecialchars($sections['cta_content']); ?></textarea>
            </div>

            <div class="form-group">
                <label>CTA Button Text</label>
                <input type="text" name="content[about|cta_button_text]" value="<?php echo htmlspecialchars($sections['cta_button_text']); ?>" required>
            </div>

            <button type="submit" name="update_content">Update Text Content</button>
        </form>

        <!-- Manage Features -->
        <div class="section-card">
            <h2>Manage Features</h2>
            <div class="item-list">
                <?php foreach ($features as $feature): ?>
                    <div class="item-card">
                        <div>
                            <strong><i class="fas <?php echo $feature['icon']; ?>"></i> <?php echo htmlspecialchars($feature['title']); ?></strong><br>
                            <?php echo htmlspecialchars(substr($feature['description'], 0, 100)); ?>...
                        </div>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this feature?')">
                            <input type="hidden" name="delete_id" value="<?php echo $feature['id']; ?>">
                            <button type="submit" name="delete_feature" class="delete-btn">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="add-form">
                <h3>Add New Feature</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Icon Class (e.g., fa-seedling)</label>
                        <input type="text" name="icon" placeholder="fa-seedling" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" value="0">
                    </div>
                    <button type="submit" name="add_feature">Add Feature</button>
                </form>
            </div>
        </div>

        <!-- Manage Team Members -->
        <div class="section-card">
            <h2>Manage Team Members</h2>
            <div class="item-list">
                <?php foreach ($team_members as $member): ?>
                    <div class="item-card">
                        <div>
                            <strong><?php echo htmlspecialchars($member['name']); ?></strong><br>
                            <?php echo htmlspecialchars($member['position']); ?>
                        </div>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this team member?')">
                            <input type="hidden" name="delete_id" value="<?php echo $member['id']; ?>">
                            <button type="submit" name="delete_team_member" class="delete-btn">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="add-form">
                <h3>Add Team Member</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" name="position" required>
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" value="0">
                    </div>
                    <button type="submit" name="add_team_member">Add Team Member</button>
                </form>
            </div>
        </div>

        <!-- Manage Testimonials -->
        <div class="section-card">
            <h2>Manage Testimonials</h2>
            <div class="item-list">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="item-card">
                        <div>
                            <strong><?php echo htmlspecialchars($testimonial['customer_name']); ?></strong><br>
                            <?php echo htmlspecialchars(substr($testimonial['content'], 0, 100)); ?>...
                        </div>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this testimonial?')">
                            <input type="hidden" name="delete_id" value="<?php echo $testimonial['id']; ?>">
                            <button type="submit" name="delete_testimonial" class="delete-btn">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="add-form">
                <h3>Add Testimonial</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Role</label>
                        <input type="text" name="customer_role">
                    </div>
                    <div class="form-group">
                        <label>Testimonial Content</label>
                        <textarea name="content" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" value="0">
                    </div>
                    <button type="submit" name="add_testimonial">Add Testimonial</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<div class="button-group">
    <button onclick="goBack()" class="prev-btn">
        <i class="fas fa-arrow-left"></i>
        <span>Previous Page</span>
    </button>

    <button onclick="goToDashboard()" class="back-btn">
        <i class="fas fa-tachometer-alt"></i>
        <span>Go To Dashboard</span>
    </button>
</div>

<script>
    function goBack() {
        window.history.back();
    }

    function goToDashboard() {
        window.location.href = 'dashboard.php';
    }
</script>