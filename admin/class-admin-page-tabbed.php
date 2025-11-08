<?php
/**
 * Admin Page with Tabbed Interface
 * 
 * Modern WordPress admin interface with tabs, dark/light themes, and advanced settings
 */

if (!defined('ABSPATH')) {
    exit;
}

class EzIT_Template_Admin_Page_Tabbed {
    
    public static function render() {
        if ( ! current_user_can('manage_options') ) { wp_die('Insufficient permissions.'); }

        // Get current theme (default to dark)
        $current_theme = get_option('ez_it_template_theme', 'dark');
        $theme_class = $current_theme === 'light' ? 'ezdh-light' : 'ezdh-dark';
        
        // Get current tab
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';

        ?>
        <div class="ezdh-fullpage <?php echo esc_attr($theme_class); ?>" id="ezdh-main-wrap" data-theme="<?php echo esc_attr($current_theme); ?>">
            
            <!-- Header with Title and Theme Switcher -->
            <div class="ezdh-header">
                <h1 class="ezdh-title">
                    <span class="dashicons dashicons-admin-generic"></span>
                    Ez IT Solutions – Plugin Name
                </h1>
                <button type="button" id="ezdh-theme-toggle" class="button ezdh-theme-btn">
                    <span class="ezdh-theme-icon"><?php echo $current_theme === 'dark' ? '☀️' : '🌙'; ?></span>
                    <span class="ezdh-theme-text"><?php echo $current_theme === 'dark' ? 'Light Mode' : 'Dark Mode'; ?></span>
                </button>
            </div>
            
            <!-- Tab Navigation -->
            <nav class="ezdh-tabs">
                <a href="?page=ez-it-template&tab=dashboard" class="ezdh-tab <?php echo $current_tab === 'dashboard' ? 'active' : ''; ?>" data-tab="dashboard">
                    <span class="dashicons dashicons-dashboard"></span>
                    Dashboard
                </a>
                <a href="?page=ez-it-template&tab=settings" class="ezdh-tab <?php echo $current_tab === 'settings' ? 'active' : ''; ?>" data-tab="settings">
                    <span class="dashicons dashicons-admin-settings"></span>
                    Settings
                </a>
                <a href="?page=ez-it-template&tab=about" class="ezdh-tab <?php echo $current_tab === 'about' ? 'active' : ''; ?>" data-tab="about">
                    <span class="dashicons dashicons-info"></span>
                    About
                </a>
            </nav>
            
            <div class="ezdh-content-wrapper">
                <!-- Loading Modal -->
                <div class="ezdh-loading-modal" id="ezdh-loading-modal">
                    <div class="ezdh-loading-content">
                        <div class="ezdh-loading-spinner"></div>
                        <p class="ezdh-loading-text">Loading...</p>
                    </div>
                </div>
                
                <div class="ezdh-main-content">
                    <?php
                    // Render notices if any
                    self::render_notices($notice, $last_urls);
                    
                    // Render tab content based on current tab
                    switch($current_tab) {
                        case 'sso':
                            self::render_sso_tab($dh_status, $unhide, $roles, $selected_role, $users);
                            break;
                        case 'impersonation':
                            self::render_impersonation_tab($roles, $selected_role, $users);
                            break;
                        case 'migration':
                            self::render_migration_tab();
                            break;
                        case 'settings':
                            $current_theme = get_option('ez_dh_theme', 'dark');
                            self::render_settings_tab($current_theme);
                            break;
                        case 'about':
                            self::render_about_tab();
                            break;
                        case 'dashboard':
                        default:
                            self::render_dashboard_tab($dh_status);
                            break;
                    }
                    ?>
                </div>
                
                <!-- Sidebar -->
                <aside class="ezdh-sidebar">
                    <?php self::render_sidebar($current_tab); ?>
                </aside>
            </div>
            
            <footer class="ezdh-footer">
                <p>
                    <span class="dashicons dashicons-heart"></span>
                    Built by <a href="https://www.Ez-IT-Solutions.com" target="_blank">Ez IT Solutions</a> | Chris Hultberg | Powered by DreamHost
                </p>
            </footer>
        </div>
        <?php
    }
    
    /**
     * Render notices
     */
    private static function render_notices($notice, $last_urls) {
        if (empty($notice)) return;
        
        $notices = [
            'tokens' => ['type' => 'success', 'message' => 'Login tokens generated successfully!'],
            'installed' => ['type' => 'success', 'message' => 'DreamHost plugin installed/updated and activated.'],
            'unhide_toggled' => ['type' => 'success', 'message' => 'DreamHost plugin visibility toggled.'],
            'db_connected' => ['type' => 'success', 'message' => '✓ External database connection successful!'],
            'urls_updated' => ['type' => 'success', 'message' => '✓ Site URLs updated successfully in external database!'],
            'db_cleared' => ['type' => 'success', 'message' => 'Database connection cleared.'],
            'db_error' => ['type' => 'error', 'message' => 'Database Error: ' . (isset($_GET['ez_error']) ? esc_html(urldecode($_GET['ez_error'])) : 'Unknown error')],
            'urls_error' => ['type' => 'error', 'message' => 'Failed to update site URLs. Please check your database connection.'],
            'token_error' => ['type' => 'error', 'message' => 'Failed to generate token in external database.'],
        ];
        
        if (isset($notices[$notice])) {
            $n = $notices[$notice];
            $class = $n['type'] === 'error' ? 'ezdh-notice-error' : 'ezdh-notice-success';
            echo '<div class="ezdh-notice ' . $class . '">';
            echo '<p>' . $n['message'] . '</p>';
            
            if ($notice === 'tokens' && !empty($last_urls)) {
                foreach ($last_urls as $url) {
                    echo '<p><input class="ezdh-copy" type="text" readonly value="' . esc_attr($url) . '"></p>';
                }
            }
            echo '</div>';
        }
    }
    
    /**
     * Dashboard Tab
     */
    public static function render_dashboard_tab($dh_status) {
        ?>
        <h2 class="ezdh-section-title">Welcome to Ez DH SSO Utility</h2>
        <p class="ezdh-intro">Manage DreamHost SSO, generate login tokens, impersonate users, and migrate WordPress sites with ease.</p>
        
        <div class="ezdh-dashboard-grid">
            <div class="ezdh-card">
                <div class="ezdh-card-icon ezdh-icon-success">
                    <span class="dashicons dashicons-admin-plugins"></span>
                </div>
                <h3>DreamHost Plugin</h3>
                <p class="ezdh-status-item">
                    <span class="ezdh-label">Status:</span>
                    <span class="ezdh-badge <?php echo $dh_status['active'] ? 'ezdh-badge-success' : 'ezdh-badge-warning'; ?>">
                        <?php echo $dh_status['active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </p>
                <p class="ezdh-status-item">
                    <span class="ezdh-label">Installed:</span>
                    <span><?php echo $dh_status['installed_present'] ? 'Yes' : 'No'; ?></span>
                </p>
                <a href="?page=ez-dh-sso&tab=sso" class="button button-primary">Manage SSO →</a>
            </div>
            
            <div class="ezdh-card">
                <div class="ezdh-card-icon ezdh-icon-primary">
                    <span class="dashicons dashicons-admin-users"></span>
                </div>
                <h3>User Impersonation</h3>
                <p>Safely test user accounts without knowing passwords. Perfect for troubleshooting and testing.</p>
                <a href="?page=ez-dh-sso&tab=impersonation" class="button button-primary">Impersonate Users →</a>
            </div>
            
            <div class="ezdh-card">
                <div class="ezdh-card-icon ezdh-icon-info">
                    <span class="dashicons dashicons-database-export"></span>
                </div>
                <h3>Migration Tools</h3>
                <p>Connect to external databases, update site URLs, and generate login tokens for migrations.</p>
                <a href="?page=ez-dh-sso&tab=migration" class="button button-primary">Migration Tools →</a>
            </div>
        </div>
        
        <div class="ezdh-quick-stats">
            <h3>Quick Stats</h3>
            <div class="ezdh-stats-grid">
                <div class="ezdh-stat">
                    <span class="ezdh-stat-value"><?php echo wp_count_posts('page')->publish; ?></span>
                    <span class="ezdh-stat-label">Pages</span>
                </div>
                <div class="ezdh-stat">
                    <span class="ezdh-stat-value"><?php echo count_users()['total_users']; ?></span>
                    <span class="ezdh-stat-label">Users</span>
                </div>
                <div class="ezdh-stat">
                    <span class="ezdh-stat-value"><?php echo is_plugin_active('dreamhost-panel-login/dreamhost-panel-login.php') ? 'Active' : 'Inactive'; ?></span>
                    <span class="ezdh-stat-label">DH Plugin</span>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * SSO & Tokens Tab
     */
    public static function render_sso_tab($dh_status, $unhide, $roles, $selected_role, $users) {
        ?>
        <h2 class="ezdh-section-title">SSO & Token Management</h2>
        
        <div class="ezdh-two-column">
            <div class="ezdh-card">
                <h3><span class="dashicons dashicons-admin-plugins"></span> DreamHost Plugin Status</h3>
                <ul class="ezdh-status-list">
                    <li><strong>Bundled present:</strong> <?php echo $dh_status['bundled_present'] ? 'Yes' : 'No'; ?></li>
                    <li><strong>Installed in /plugins:</strong> <?php echo $dh_status['installed_present'] ? 'Yes' : 'No'; ?></li>
                    <li><strong>Active:</strong> <?php echo $dh_status['active'] ? 'Yes' : 'No'; ?></li>
                    <li><strong>Hidden in list:</strong> <?php echo $unhide ? 'No (unhidden)' : 'Possibly yes'; ?></li>
                </ul>
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('ez_dh_install_dh_plugin'); ?>
                    <input type="hidden" name="action" value="ez_dh_install_dh_plugin">
                    <button class="button button-primary">Install/Update from Bundle</button>
                </form>
                <form class="mt-2" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('ez_dh_toggle_unhide'); ?>
                    <input type="hidden" name="action" value="ez_dh_toggle_unhide">
                    <button class="button"><?php echo $unhide ? 'Hide DH Plugin' : 'Unhide DH Plugin'; ?></button>
                </form>
            </div>

            <div class="ezdh-card">
                <h3><span class="dashicons dashicons-admin-network"></span> Generate One-Time Login Token</h3>
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('ez_dh_generate_token'); ?>
                    <input type="hidden" name="action" value="ez_dh_generate_token">

                    <p class="ezdh-form-field">
                        <label>Role</label>
                        <select name="role" onchange="this.form.submit()">
                            <?php foreach ($roles as $role_key=>$role_label): ?>
                                <option value="<?php echo esc_attr($role_key); ?>" <?php selected($selected_role, $role_key); ?>>
                                    <?php echo esc_html($role_label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p class="ezdh-form-field">
                        <label>User</label>
                        <select name="user_id">
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo esc_attr($u->ID); ?>">
                                    <?php echo esc_html($u->user_login . ' (' . $u->user_email . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p class="ezdh-form-field">
                        <label>How many tokens?</label>
                        <input type="number" name="count" value="1" min="1" max="10">
                    </p>

                    <button class="button button-primary">Generate Token(s)</button>
                </form>
            </div>
        </div>

        <div class="ezdh-card">
            <h3><span class="dashicons dashicons-info"></span> Diagnostics</h3>
            <?php
            $tokens_user1 = \EzDhSsoFix\Sso_Manager::get_tokens(1);
            $token_count = count($tokens_user1);
            $last_token = \EzDhSsoFix\Sso_Manager::last_token(1);
            $last_token_truncated = $last_token ? substr($last_token, 0, 10) : '';
            ?>
            <ul class="ezdh-status-list">
                <li><strong>Token count for user #1:</strong> <?php echo esc_html($token_count); ?></li>
                <?php if ($last_token_truncated): ?>
                    <li><strong>Last token (truncated):</strong> <code class="ezdh-code"><?php echo esc_html($last_token_truncated); ?>...</code></li>
                <?php endif; ?>
                <?php if ($last_token): ?>
                    <li><strong>Token format:</strong> JSON-compatible string</li>
                    <li><strong>Token length:</strong> <?php echo strlen($last_token); ?> characters</li>
                    <li><strong>Token structure:</strong> Base64-encoded data with timestamp</li>
                <?php endif; ?>
                <li><strong>DreamHost plugin active:</strong> <?php echo is_plugin_active('dreamhost-panel-login/dreamhost-panel-login.php') ? 'Yes' : 'No'; ?></li>
                <li><strong>SSO endpoint:</strong> <code class="ezdh-code"><?php echo esc_html(home_url('/wp-login.php')); ?></code></li>
            </ul>
            
            <?php if ($last_token): ?>
                <div class="ezdh-token-display">
                    <h4 style="margin: 0 0 8px 0; font-size: 0.9rem;">Full Token (for testing):</h4>
                    <textarea readonly class="ezdh-token-textarea" onclick="this.select();"><?php echo esc_textarea($last_token); ?></textarea>
                    <p style="margin: 8px 0 0 0; font-size: 0.85rem; opacity: 0.8;">Click to select and copy. Use this token for testing SSO integration.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Impersonation Tab
     */
    public static function render_impersonation_tab($roles, $selected_role, $users) {
        ?>
        <h2 class="ezdh-section-title">User Impersonation</h2>
        <p class="ezdh-intro">Safely login as any user to test their experience. You can return to your admin account at any time.</p>
        
        <div class="ezdh-card">
            <h3><span class="dashicons dashicons-admin-users"></span> Impersonate User</h3>
            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                <?php wp_nonce_field('ez_dh_impersonate'); ?>
                <input type="hidden" name="action" value="ez_dh_impersonate">

                <p class="ezdh-form-field">
                    <label>Select Role</label>
                    <select name="role" onchange="this.form.submit()">
                        <?php foreach ($roles as $role_key=>$role_label): ?>
                            <option value="<?php echo esc_attr($role_key); ?>" <?php selected($selected_role, $role_key); ?>>
                                <?php echo esc_html($role_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p class="ezdh-form-field">
                    <label>Select User</label>
                    <select name="user_id">
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo esc_attr($u->ID); ?>">
                                <?php echo esc_html($u->user_login . ' (' . $u->user_email . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <button class="button button-primary">Impersonate</button>
            </form>
        </div>
        
        <div class="ezdh-info-box">
            <h4><span class="dashicons dashicons-info"></span> How It Works</h4>
            <ul>
                <li>Select a user from the dropdown and click "Impersonate"</li>
                <li>You'll be logged in as that user immediately</li>
                <li>A "Return to Admin" link will appear in the admin bar</li>
                <li>Click it anytime to switch back to your account</li>
                <li>Perfect for testing user roles and permissions</li>
            </ul>
        </div>
        <?php
    }
    
    /**
     * Migration Tools Tab
     */
    public static function render_migration_tab() {
        $db_connected = get_option('ez_dh_ext_db_connected', false);
        $db_host = get_option('ez_dh_ext_db_host', '');
        $db_name = get_option('ez_dh_ext_db_name', '');
        
        ?>
        <h2 class="ezdh-section-title">DreamHost Migration Tools</h2>
        <p class="ezdh-intro">Connect to external WordPress databases to update site URLs and generate login tokens during migrations.</p>
        
        <div class="ezdh-two-column">
            <div class="ezdh-card">
                <h3><span class="dashicons dashicons-database"></span> External Database Connection</h3>
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('ez_dh_test_db'); ?>
                    <input type="hidden" name="action" value="ez_dh_test_db">

                    <p class="ezdh-form-field">
                        <label>Database Host</label>
                        <input type="text" name="db_host" value="<?php echo esc_attr($db_host); ?>" placeholder="mysql.example.com">
                    </p>

                    <p class="ezdh-form-field">
                        <label>Database Name</label>
                        <input type="text" name="db_name" value="<?php echo esc_attr($db_name); ?>" placeholder="wordpress_db">
                    </p>

                    <p class="ezdh-form-field">
                        <label>Database User</label>
                        <input type="text" name="db_user" value="<?php echo esc_attr(get_option('ez_dh_ext_db_user', '')); ?>" placeholder="db_username">
                    </p>

                    <p class="ezdh-form-field">
                        <label>Database Password</label>
                        <input type="password" name="db_pass" placeholder="••••••••">
                    </p>

                    <p class="ezdh-form-field">
                        <label>Port (default: 3306)</label>
                        <input type="number" name="db_port" value="<?php echo esc_attr(get_option('ez_dh_ext_db_port', '3306')); ?>">
                    </p>

                    <p class="ezdh-form-field">
                        <label>Table Prefix (default: wp_)</label>
                        <input type="text" name="db_prefix" value="<?php echo esc_attr(get_option('ez_dh_ext_db_prefix', 'wp_')); ?>">
                    </p>

                    <button class="button button-primary">Test Connection & Save</button>
                </form>
                
                <?php if ($db_connected): ?>
                    <form class="mt-2" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <?php wp_nonce_field('ez_dh_clear_db'); ?>
                        <input type="hidden" name="action" value="ez_dh_clear_db">
                        <button class="button">Clear Connection</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="ezdh-card">
                <h3><span class="dashicons dashicons-admin-tools"></span> Migration Actions</h3>
                <?php if ($db_connected): ?>
                    <p class="ezdh-success-message">✓ Connected to: <?php echo esc_html($db_name); ?></p>
                    
                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <?php wp_nonce_field('ez_dh_update_urls'); ?>
                        <input type="hidden" name="action" value="ez_dh_update_urls">
                        
                        <p class="ezdh-form-field">
                            <label>New Site URL</label>
                            <input type="url" name="new_url" placeholder="https://newsite.com" required>
                        </p>
                        
                        <button class="button button-primary">Update Site URLs</button>
                    </form>
                    
                    <hr>
                    
                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <?php wp_nonce_field('ez_dh_generate_remote_token'); ?>
                        <input type="hidden" name="action" value="ez_dh_generate_remote_token">
                        
                        <p class="ezdh-form-field">
                            <label>User ID</label>
                            <input type="number" name="user_id" value="1" min="1">
                        </p>
                        
                        <button class="button button-primary">Generate Remote Token</button>
                    </form>
                <?php else: ?>
                    <p class="ezdh-warning-message">Please test and save your database connection first.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Settings Tab
     */
    public static function render_settings_tab($current_theme) {
        $cache_enabled = get_option('ez_dh_cache_enabled', true);
        $debug_mode = get_option('ez_dh_debug_mode', false);
        $token_expiry = get_option('ez_dh_token_expiry_hours', 24);
        $auto_cleanup = get_option('ez_dh_auto_cleanup', true);
        ?>
        <h2 class="ezdh-section-title">Plugin Settings</h2>
        
        <?php if (isset($_GET['ez_notice']) && $_GET['ez_notice'] === 'settings_saved'): ?>
            <div class="ezdh-success-message" style="padding: 12px 16px; background: rgba(34, 197, 94, 0.1); border-left: 3px solid #22c55e; border-radius: 4px; margin-bottom: 20px;">
                <strong>✓ Settings saved successfully!</strong>
            </div>
        <?php endif; ?>
        
        <div class="ezdh-card">
            <h3><span class="dashicons dashicons-admin-appearance"></span> Appearance</h3>
            <p style="margin-bottom: 20px;">Choose your preferred theme for the plugin interface.</p>
            
            <div class="ezdh-theme-selector">
                <div class="ezdh-theme-option <?php echo $current_theme === 'light' ? 'active' : ''; ?>" data-theme="light">
                    <div class="ezdh-theme-preview ezdh-theme-preview-light">
                        <div class="ezdh-preview-header"></div>
                        <div class="ezdh-preview-content">
                            <div class="ezdh-preview-card"></div>
                            <div class="ezdh-preview-card"></div>
                        </div>
                    </div>
                    <div class="ezdh-theme-info">
                        <h4>☀️ Light Mode</h4>
                        <p>Clean and bright, ideal for daytime use</p>
                    </div>
                    <?php if ($current_theme === 'light'): ?>
                        <span class="ezdh-theme-active-badge">Active</span>
                    <?php endif; ?>
                </div>
                
                <div class="ezdh-theme-option <?php echo $current_theme === 'dark' ? 'active' : ''; ?>" data-theme="dark">
                    <div class="ezdh-theme-preview ezdh-theme-preview-dark">
                        <div class="ezdh-preview-header"></div>
                        <div class="ezdh-preview-content">
                            <div class="ezdh-preview-card"></div>
                            <div class="ezdh-preview-card"></div>
                        </div>
                    </div>
                    <div class="ezdh-theme-info">
                        <h4>🌙 Dark Mode</h4>
                        <p>Easy on the eyes, perfect for late-night work</p>
                    </div>
                    <?php if ($current_theme === 'dark'): ?>
                        <span class="ezdh-theme-active-badge">Active</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="ezdh-card">
            <h3><span class="dashicons dashicons-admin-generic"></span> Advanced Settings</h3>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="ezdh-advanced-settings-form">
                <?php wp_nonce_field('ez_dh_save_settings'); ?>
                <input type="hidden" name="action" value="ez_dh_save_settings">
                
                <div class="ezdh-settings-grid">
                    <div class="ezdh-setting-item">
                        <div class="ezdh-setting-header">
                            <label class="ezdh-setting-label">
                                <span class="dashicons dashicons-performance"></span>
                                Enable Caching
                            </label>
                            <label class="ezdh-toggle">
                                <input type="checkbox" name="cache_enabled" value="1" <?php checked($cache_enabled); ?>>
                                <span class="ezdh-toggle-slider"></span>
                            </label>
                        </div>
                        <p class="ezdh-setting-description">Cache plugin data to improve performance. Recommended for production sites.</p>
                    </div>
                    
                    <div class="ezdh-setting-item">
                        <div class="ezdh-setting-header">
                            <label class="ezdh-setting-label">
                                <span class="dashicons dashicons-code-standards"></span>
                                Debug Mode
                            </label>
                            <label class="ezdh-toggle">
                                <input type="checkbox" name="debug_mode" value="1" <?php checked($debug_mode); ?>>
                                <span class="ezdh-toggle-slider"></span>
                            </label>
                        </div>
                        <p class="ezdh-setting-description">Enable detailed logging for troubleshooting. Disable in production.</p>
                    </div>
                    
                    <div class="ezdh-setting-item">
                        <div class="ezdh-setting-header">
                            <label class="ezdh-setting-label">
                                <span class="dashicons dashicons-trash"></span>
                                Auto-Cleanup Tokens
                            </label>
                            <label class="ezdh-toggle">
                                <input type="checkbox" name="auto_cleanup" value="1" <?php checked($auto_cleanup); ?>>
                                <span class="ezdh-toggle-slider"></span>
                            </label>
                        </div>
                        <p class="ezdh-setting-description">Automatically delete expired tokens to keep database clean.</p>
                    </div>
                    
                    <div class="ezdh-setting-item">
                        <div class="ezdh-setting-header">
                            <label class="ezdh-setting-label">
                                <span class="dashicons dashicons-clock"></span>
                                Token Expiry Time
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                            <input type="number" name="token_expiry_hours" value="<?php echo esc_attr($token_expiry); ?>" min="1" max="168" style="width: 100px;">
                            <span>hours</span>
                        </div>
                        <p class="ezdh-setting-description">How long tokens remain valid before expiring (1-168 hours).</p>
                    </div>
                </div>
                
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-saved" style="margin-top: 4px;"></span>
                        Save Settings
                    </button>
                    <button type="button" class="button button-secondary" onclick="location.reload();">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        <?php
    }
    
    /**
     * About Tab
     */
    public static function render_about_tab() {
        global $wpdb;
        
        // Get system information
        $php_version = phpversion();
        $wp_version = get_bloginfo('version');
        $mysql_version = $wpdb->db_version();
        $server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $max_upload = ini_get('upload_max_filesize');
        $max_post = ini_get('post_max_size');
        $memory_limit = ini_get('memory_limit');
        $max_execution = ini_get('max_execution_time');
        
        // Get active plugins
        $active_plugins = get_option('active_plugins');
        $plugin_count = count($active_plugins);
        
        // Get theme info
        $theme = wp_get_theme();
        
        ?>
        <h2 class="ezdh-section-title">About Ez DH SSO Fix</h2>
        
        <div class="ezdh-two-column">
            <div class="ezdh-card">
                <h3><span class="dashicons dashicons-info"></span> Plugin Information</h3>
                <ul class="ezdh-status-list">
                    <li><strong>Version:</strong> 1.1.1</li>
                    <li><strong>Author:</strong> Ez IT Solutions | Chris Hultberg</li>
                    <li><strong>Website:</strong> <a href="https://www.Ez-IT-Solutions.com" target="_blank" class="ezdh-website-link">www.Ez-IT-Solutions.com</a></li>
                    <li><strong>License:</strong> Proprietary</li>
                </ul>
                
                <h4 style="margin-top: 20px;">Features</h4>
                <ul style="list-style: disc; padding-left: 20px; margin-top: 10px;">
                    <li>DreamHost SSO Plugin Management</li>
                    <li>One-Time Login Token Generator</li>
                    <li>User Impersonation System</li>
                    <li>External Database Migration Tools</li>
                    <li>Dark & Light Mode Interface</li>
                </ul>
            </div>
            
            <div class="ezdh-card">
                <h3><span class="dashicons dashicons-admin-tools"></span> System Diagnostics</h3>
                <ul class="ezdh-status-list">
                    <li><strong>WordPress Version:</strong> <?php echo esc_html($wp_version); ?></li>
                    <li><strong>PHP Version:</strong> <?php echo esc_html($php_version); ?></li>
                    <li><strong>MySQL Version:</strong> <?php echo esc_html($mysql_version); ?></li>
                    <li><strong>Active Theme:</strong> <?php echo esc_html($theme->get('Name')); ?></li>
                    <li><strong>Active Plugins:</strong> <?php echo esc_html($plugin_count); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="ezdh-card">
            <h3><span class="dashicons dashicons-performance"></span> System Information</h3>
            
            <div class="ezdh-two-column" style="margin-top: 15px;">
                <div>
                    <h4>Server Environment</h4>
                    <ul class="ezdh-status-list">
                        <li><strong>Server Software:</strong> <?php echo esc_html($server_software); ?></li>
                        <li><strong>PHP Memory Limit:</strong> <?php echo esc_html($memory_limit); ?></li>
                        <li><strong>Max Execution Time:</strong> <?php echo esc_html($max_execution); ?>s</li>
                        <li><strong>Max Upload Size:</strong> <?php echo esc_html($max_upload); ?></li>
                        <li><strong>Max Post Size:</strong> <?php echo esc_html($max_post); ?></li>
                    </ul>
                </div>
                
                <div>
                    <h4>WordPress Configuration</h4>
                    <ul class="ezdh-status-list">
                        <li><strong>Site URL:</strong> <?php echo esc_html(get_site_url()); ?></li>
                        <li><strong>Home URL:</strong> <?php echo esc_html(get_home_url()); ?></li>
                        <li><strong>WP Debug:</strong> <?php echo WP_DEBUG ? 'Enabled' : 'Disabled'; ?></li>
                        <li><strong>Multisite:</strong> <?php echo is_multisite() ? 'Yes' : 'No'; ?></li>
                        <li><strong>Language:</strong> <?php echo esc_html(get_locale()); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="ezdh-card">
            <h3><span class="dashicons dashicons-admin-plugins"></span> Active Plugins (<?php echo $plugin_count; ?>)</h3>
            <div style="max-height: 300px; overflow-y: auto; margin-top: 15px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                            <th style="text-align: left; padding: 10px;">Plugin Name</th>
                            <th style="text-align: left; padding: 10px;">Version</th>
                            <th style="text-align: left; padding: 10px;">Author</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($active_plugins as $plugin_path) {
                            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
                            echo '<tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">';
                            echo '<td style="padding: 10px;">' . esc_html($plugin_data['Name']) . '</td>';
                            echo '<td style="padding: 10px;">' . esc_html($plugin_data['Version']) . '</td>';
                            echo '<td style="padding: 10px;">' . esc_html($plugin_data['Author']) . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="ezdh-card">
            <h3><span class="dashicons dashicons-sos"></span> Support & Documentation</h3>
            <p>Need help with the plugin? We're here to assist you!</p>
            <p style="margin-top: 15px;">
                <a href="https://www.Ez-IT-Solutions.com" target="_blank" class="button button-primary">Visit Website</a>
                <a href="mailto:support@ez-it-solutions.com" class="button" style="margin-left: 10px;">Contact Support</a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Sidebar
     */
    public static function render_sidebar($current_tab) {
        ?>
        <div class="ezdh-sidebar-card">
            <h3><span class="dashicons dashicons-lightbulb"></span> Quick Tips</h3>
            <?php
            $tips = [
                'dashboard' => [
                    'Use the dashboard to get an overview of your plugin status',
                    'Check the DreamHost plugin status at a glance',
                    'Quick access to all major features'
                ],
                'sso' => [
                    'Generate tokens for any user role',
                    'Tokens are one-time use only',
                    'Perfect for DreamHost Panel integration'
                ],
                'impersonation' => [
                    'Test user experiences safely',
                    'No password required',
                    'Return to admin anytime'
                ],
                'migration' => [
                    'Connect to external databases securely',
                    'Update site URLs in bulk',
                    'Generate tokens for migrated sites'
                ],
                'settings' => [
                    'Customize your plugin experience',
                    'Switch between themes instantly',
                    'All settings auto-save'
                ],
                'about' => [
                    'View comprehensive system information',
                    'Check all active plugins and versions',
                    'Export system info for troubleshooting'
                ]
            ];
            
            $current_tips = $tips[$current_tab] ?? $tips['dashboard'];
            echo '<ul class="ezdh-tips-list">';
            foreach ($current_tips as $tip) {
                echo '<li>' . esc_html($tip) . '</li>';
            }
            echo '</ul>';
            ?>
        </div>
        
        <div class="ezdh-sidebar-card">
            <h3><span class="dashicons dashicons-sos"></span> Need Help?</h3>
            <p>Visit our documentation or contact support:</p>
            <p><a href="https://www.Ez-IT-Solutions.com" target="_blank" class="button button-small">Get Support</a></p>
        </div>
        
        <div class="ezdh-sidebar-card ezdh-promo">
            <h3><span class="dashicons dashicons-star-filled"></span> Love This Plugin?</h3>
            <p>Help us improve by leaving a review or sharing with others!</p>
        </div>
        <?php
    }
}
