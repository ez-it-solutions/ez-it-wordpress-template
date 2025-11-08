<?php
namespace EzDhSsoFix;

if ( ! defined('ABSPATH') ) { exit; }

class Admin_Page {
    public static function render() {
        if ( ! current_user_can('manage_options') ) { wp_die('Insufficient permissions.'); }

        $notice = isset($_GET['ez_notice']) ? sanitize_text_field($_GET['ez_notice']) : '';
        $last_urls = get_transient('ez_dh_last_urls') ?: [];
        delete_transient('ez_dh_last_urls');

        $roles = wp_roles()->get_names();
        $selected_role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : (is_array($roles) ? array_key_first($roles) : 'administrator');
        $users = get_users(['role__in' => [$selected_role], 'orderby'=>'user_login', 'order'=>'ASC']);

        $dh_status = Dh_Installer::status();
        $unhide = get_option('ez_dh_unhide_dreamhost_plugin', false);
        
        // Get current theme (default to dark)
        $current_theme = get_option('ez_dh_theme', 'dark');
        $theme_class = $current_theme === 'light' ? 'ezit-light' : 'ezit-dark';
        
        // Get current tab
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';

        ?>
        <div class="ezit-fullpage <?php echo esc_attr($theme_class); ?>" id="ezit-main-wrap" data-theme="<?php echo esc_attr($current_theme); ?>">
            
            <!-- Header with Title and Theme Switcher -->
            <div class="ezit-header">
                <h1 class="ezit-title">Ez IT Solutions – DreamHost SSO Utility</h1>
                <button type="button" id="ezit-theme-toggle" class="button ezit-theme-btn">
                    <span class="ezit-theme-icon"><?php echo $current_theme === 'dark' ? '☀️' : '🌙'; ?></span>
                    <span class="ezit-theme-text"><?php echo $current_theme === 'dark' ? 'Light Mode' : 'Dark Mode'; ?></span>
                </button>
            </div>
            
            <!-- Tab Navigation -->
            <nav class="ezit-tabs">
                <a href="?page=ez-dh-sso&tab=dashboard" class="ezit-tab <?php echo $current_tab === 'dashboard' ? 'ezit-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-dashboard"></span> Dashboard
                </a>
                <a href="?page=ez-dh-sso&tab=sso" class="ezit-tab <?php echo $current_tab === 'sso' ? 'ezit-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-admin-network"></span> SSO & Tokens
                </a>
                <a href="?page=ez-dh-sso&tab=impersonation" class="ezit-tab <?php echo $current_tab === 'impersonation' ? 'ezit-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-admin-users"></span> Impersonation
                </a>
                <a href="?page=ez-dh-sso&tab=migration" class="ezit-tab <?php echo $current_tab === 'migration' ? 'ezit-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-database-export"></span> Migration Tools
                </a>
                <a href="?page=ez-dh-sso&tab=settings" class="ezit-tab <?php echo $current_tab === 'settings' ? 'ezit-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-admin-settings"></span> Settings
                </a>
            </nav>
            
            <div class="ezit-content-wrapper">
                <div class="ezit-main-content"><?php
                
                // Render tab content based on current tab
                switch($current_tab) {
                    case 'sso':
                        self::render_sso_tab($dh_status, $unhide, $roles, $selected_role, $users, $notice, $last_urls);
                        break;
                    case 'impersonation':
                        self::render_impersonation_tab($roles, $selected_role, $users);
                        break;
                    case 'migration':
                        self::render_migration_tab($notice);
                        break;
                    case 'settings':
                        self::render_settings_tab($current_theme);
                        break;
                    case 'dashboard':
                    default:
                        self::render_dashboard_tab($dh_status, $notice, $last_urls);
                        break;
                }
                
                ?></div>
                
                <!-- Sidebar -->
                <aside class="ezit-sidebar">
                    <?php self::render_sidebar($current_tab); ?>
                </aside>
            </div>
            
            <footer class="ezit-footer">
                <p>Built by <a href="https://www.Ez-IT-Solutions.com" target="_blank">Ez IT Solutions</a> | Chris Hultberg | Powered by DreamHost</p>
            </footer>
        </div>
        <?php
    }
    
    private static function render_dashboard_tab($dh_status, $notice, $last_urls) {
        ?>

            <?php if ($notice === 'tokens' && !empty($last_urls)): ?>
                <div class="notice notice-success p-4 mb-4 rounded border border-lime-500 bg-gray-800">
                    <p class="font-semibold">Generated Login URL(s):</p>
                    <?php foreach ($last_urls as $url): ?>
                        <p><input class="w-full ezit-copy" type="text" readonly value="<?php echo esc_attr($url); ?>"></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($notice === 'installed'): ?>
                <div class="notice notice-success p-4 mb-4 rounded border border-lime-500 bg-gray-800">
                    <p>Bundled DreamHost plugin installed/copied and (re)activated.</p>
                </div>
            <?php elseif ($notice === 'unhide_toggled'): ?>
                <div class="notice notice-success p-4 mb-4 rounded border border-lime-500 bg-gray-800">
                    <p>DreamHost plugin visibility toggled.</p>
                </div>
            <?php elseif ($notice === 'db_connected'): ?>
                <div class="notice notice-success p-4 mb-4 rounded border border-lime-500 bg-gray-800">
                    <p>✓ External database connection successful! You can now use migration tools.</p>
                </div>
            <?php elseif ($notice === 'urls_updated'): ?>
                <div class="notice notice-success p-4 mb-4 rounded border border-lime-500 bg-gray-800">
                    <p>✓ Site URLs updated successfully in external database!</p>
                </div>
            <?php elseif ($notice === 'db_cleared'): ?>
                <div class="notice notice-success p-4 mb-4 rounded border border-lime-500 bg-gray-800">
                    <p>Database connection cleared.</p>
                </div>
            <?php elseif ($notice === 'db_error'): ?>
                <div class="notice notice-error p-4 mb-4 rounded border border-red-500 bg-gray-800">
                    <p><strong>Database Error:</strong> <?php echo esc_html(isset($_GET['ez_error']) ? urldecode($_GET['ez_error']) : 'Unknown error'); ?></p>
                </div>
            <?php elseif ($notice === 'urls_error'): ?>
                <div class="notice notice-error p-4 mb-4 rounded border border-red-500 bg-gray-800">
                    <p>Failed to update site URLs. Please check your database connection.</p>
                </div>
            <?php elseif ($notice === 'token_error'): ?>
                <div class="notice notice-error p-4 mb-4 rounded border border-red-500 bg-gray-800">
                    <p>Failed to generate token in external database.</p>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-800 p-4 rounded shadow">
                    <h2 class="text-xl font-semibold text-lime-400 mb-3">DreamHost Plugin Status</h2>
                    <ul class="list-disc list-inside text-sm mb-3">
                        <li><strong>Bundled present:</strong> <?php echo $dh_status['bundled_present'] ? 'Yes' : 'No'; ?></li>
                        <li><strong>Installed in /plugins:</strong> <?php echo $dh_status['installed_present'] ? 'Yes' : 'No'; ?></li>
                        <li><strong>Active:</strong> <?php echo $dh_status['active'] ? 'Yes' : 'No'; ?></li>
                        <li><strong>Hidden in list:</strong> <?php echo $unhide ? 'No (unhidden)' : 'Possibly yes'; ?></li>
                    </ul>
                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <?php wp_nonce_field('ez_dh_install_dh_plugin'); ?>
                        <input type="hidden" name="action" value="ez_dh_install_dh_plugin">
                        <button class="button button-primary bg-lime-500 border-lime-500 hover:bg-lime-400">Install/Update from Bundle</button>
                    </form>
                    <form class="mt-2" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <?php wp_nonce_field('ez_dh_toggle_unhide'); ?>
                        <input type="hidden" name="action" value="ez_dh_toggle_unhide">
                        <button class="button bg-gray-700 hover:bg-gray-600"><?php echo $unhide ? 'Hide DH Plugin' : 'Unhide DH Plugin'; ?></button>
                    </form>
                </div>

                <div class="bg-gray-800 p-4 rounded shadow">
                    <h2 class="text-xl font-semibold text-lime-400 mb-3">Generate One-Time Login Token</h2>
                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <?php wp_nonce_field('ez_dh_generate_token'); ?>
                        <input type="hidden" name="action" value="ez_dh_generate_token">

                        <p class="mb-2">
                            <label class="block text-sm mb-1">Role</label>
                            <select name="role" onchange="this.form.submit()" class="min-w-[240px]">
                                <?php foreach ($roles as $role_key=>$role_label): ?>
                                    <option value="<?php echo esc_attr($role_key); ?>" <?php selected($selected_role, $role_key); ?>>
                                        <?php echo esc_html($role_label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>

                        <p class="mb-2">
                            <label class="block text-sm mb-1">User</label>
                            <select name="user_id" class="min-w-[320px]">
                                <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u->ID; ?>"><?php echo esc_html($u->user_login . ' (' . $u->user_email . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>

                        <p class="mb-2">
                            <label class="block text-sm mb-1">How many tokens?</label>
                            <input type="number" name="count" value="1" min="1" max="10">
                        </p>

                        <p><button class="button button-primary bg-lime-500 border-lime-500 hover:bg-lime-400">Generate Token(s)</button></p>
                    </form>
                </div>

                <div class="bg-gray-800 p-4 rounded shadow">
                    <h2 class="text-xl font-semibold text-lime-400 mb-3">Impersonation</h2>
                    <form method="get" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <?php wp_nonce_field('ez_dh_impersonate'); ?>
                        <input type="hidden" name="action" value="ez_dh_impersonate">
                        <p class="mb-2">
                            <label class="block text-sm mb-1">User</label>
                            <select name="user_id" class="min-w-[320px]">
                                <?php foreach (get_users(['orderby'=>'user_login','order'=>'ASC']) as $u): ?>
                                    <option value="<?php echo $u->ID; ?>"><?php echo esc_html($u->user_login . ' (' . $u->user_email . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p><button class="button bg-gray-700 hover:bg-gray-600">Impersonate</button></p>
                    </form>

                    <?php if ( Impersonator::has_original_admin() ): ?>
                        <form class="mt-2" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                            <?php wp_nonce_field('ez_dh_return_admin'); ?>
                            <input type="hidden" name="action" value="ez_dh_return_admin">
                            <button class="button bg-gray-700 hover:bg-gray-600">Return to Admin</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="bg-gray-800 p-4 rounded shadow">
                    <h2 class="text-xl font-semibold text-lime-400 mb-3">Diagnostics</h2>
                    <ul class="list-disc list-inside text-sm">
                        <li>Token count for user #1: <strong><?php echo count(\EzDhSsoFix\Sso_Manager::get_tokens(1)); ?></strong></li>
                        <li>Last token (truncated): <code><?php echo esc_html(substr(\EzDhSsoFix\Sso_Manager::last_token(1) ?: '',0,10)); ?>...</code></li>
                    </ul>
                </div>
            </div>

            <!-- Migration Tools Section -->
            <div class="mt-6 bg-gradient-to-r from-lime-900 to-gray-800 p-6 rounded shadow">
                <h2 class="text-2xl font-bold text-lime-400 mb-4">🚀 DreamHost Migration Tools</h2>
                <p class="text-sm text-gray-300 mb-4">Use these tools when migrating from another server to DreamHost. Connect to your external database to update site URLs and generate login tokens.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Database Connection Test -->
                    <div class="bg-gray-800 p-4 rounded">
                        <h3 class="text-lg font-semibold text-lime-400 mb-3">External Database Connection</h3>
                        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                            <?php wp_nonce_field('ez_dh_test_db'); ?>
                            <input type="hidden" name="action" value="ez_dh_test_db">
                            
                            <p class="mb-2">
                                <label class="block text-sm mb-1">Database Host</label>
                                <input type="text" name="db_host" class="min-w-[320px]" placeholder="mysql.example.com" value="<?php echo esc_attr(get_option('ez_dh_ext_db_host', '')); ?>">
                            </p>
                            
                            <p class="mb-2">
                                <label class="block text-sm mb-1">Database Name</label>
                                <input type="text" name="db_name" class="min-w-[320px]" placeholder="wordpress_db" value="<?php echo esc_attr(get_option('ez_dh_ext_db_name', '')); ?>">
                            </p>
                            
                            <p class="mb-2">
                                <label class="block text-sm mb-1">Database User</label>
                                <input type="text" name="db_user" class="min-w-[320px]" placeholder="db_username" value="<?php echo esc_attr(get_option('ez_dh_ext_db_user', '')); ?>">
                            </p>
                            
                            <p class="mb-2">
                                <label class="block text-sm mb-1">Database Password</label>
                                <input type="password" name="db_pass" class="min-w-[320px]" placeholder="••••••••">
                            </p>
                            
                            <p class="mb-2">
                                <label class="block text-sm mb-1">Port (default: 3306)</label>
                                <input type="number" name="db_port" value="<?php echo esc_attr(get_option('ez_dh_ext_db_port', '3306')); ?>" min="1" max="65535">
                            </p>
                            
                            <p class="mb-2">
                                <label class="block text-sm mb-1">Table Prefix (default: wp_)</label>
                                <input type="text" name="db_prefix" value="<?php echo esc_attr(get_option('ez_dh_ext_db_prefix', 'wp_')); ?>" placeholder="wp_">
                            </p>
                            
                            <p><button class="button button-primary bg-lime-500 border-lime-500 hover:bg-lime-400">Test Connection & Save</button></p>
                        </form>
                    </div>
                    
                    <!-- Migration Actions -->
                    <div class="bg-gray-800 p-4 rounded">
                        <h3 class="text-lg font-semibold text-lime-400 mb-3">Migration Actions</h3>
                        
                        <?php if (get_option('ez_dh_ext_db_connected')): ?>
                            <div class="mb-4 p-3 bg-lime-900 border border-lime-500 rounded">
                                <p class="text-sm">✓ External DB Connected</p>
                                <p class="text-xs text-gray-300">Host: <?php echo esc_html(get_option('ez_dh_ext_db_host')); ?></p>
                            </div>
                            
                            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="mb-3">
                                <?php wp_nonce_field('ez_dh_update_urls'); ?>
                                <input type="hidden" name="action" value="ez_dh_update_urls">
                                <p class="mb-2">
                                    <label class="block text-sm mb-1">New DreamHost URL</label>
                                    <input type="url" name="new_url" class="min-w-[320px]" placeholder="https://yourdomain.com" required>
                                </p>
                                <p><button class="button button-primary bg-lime-500 border-lime-500 hover:bg-lime-400">Update Site URLs in External DB</button></p>
                            </form>
                            
                            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="mb-3">
                                <?php wp_nonce_field('ez_dh_generate_external_token'); ?>
                                <input type="hidden" name="action" value="ez_dh_generate_external_token">
                                <p class="mb-2">
                                    <label class="block text-sm mb-1">User ID (usually 1 for admin)</label>
                                    <input type="number" name="ext_user_id" value="1" min="1" required>
                                </p>
                                <p><button class="button bg-gray-700 hover:bg-gray-600">Generate Token in External DB</button></p>
                            </form>
                            
                            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                                <?php wp_nonce_field('ez_dh_clear_db'); ?>
                                <input type="hidden" name="action" value="ez_dh_clear_db">
                                <p><button class="button bg-gray-700 hover:bg-gray-600">Clear Connection</button></p>
                            </form>
                        <?php else: ?>
                            <p class="text-sm text-gray-400">Please test and save your database connection first.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-sm text-gray-300">Built by <a class="text-lime-400" href="https://www.Ez-IT-Solutions.com" target="_blank">Ez IT Solutions</a> | Chris Hultberg | Powered by DreamHost</p>
        </div>
        <?php
    }
}
