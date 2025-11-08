# Setup Guide - Ez IT WordPress Plugin Template

This guide will walk you through customizing this template for your specific WordPress plugin.

## 📋 Prerequisites

- Text editor or IDE (VS Code, PHPStorm, etc.)
- Basic knowledge of WordPress plugin development
- Understanding of PHP and WordPress hooks

## 🎯 Step-by-Step Setup

### Step 1: Copy the Template

1. Copy the entire `ez-it-wordpress-template` folder
2. Rename it to your plugin's slug (e.g., `my-awesome-plugin`)
3. Place it in your development environment

### Step 2: Rename Main Plugin File

1. Rename `ez-it-plugin-template.php` to match your plugin slug
   - Example: `my-awesome-plugin.php`

### Step 3: Update Plugin Headers

Open your renamed main plugin file and update the headers:

```php
/**
 * Plugin Name: Your Plugin Name
 * Plugin URI: https://your-website.com
 * Description: Your plugin description
 * Version: 1.0.0
 * Author: Your Name or Company
 * Author URI: https://your-website.com
 * License: Your License
 * Text Domain: your-plugin-slug
 */
```

### Step 4: Find and Replace

Use your editor's find-and-replace feature (case-sensitive) to update all references:

#### Replace Plugin Slug
Find: `ez-it-template`  
Replace with: `your-plugin-slug`

**Files to update:**
- Main plugin file
- `admin/class-admin-page-tabbed.php`
- `assets/js/admin.js`

#### Replace Class Prefix
Find: `EzIT_Template`  
Replace with: `YourPlugin`

**Files to update:**
- Main plugin file
- `admin/class-admin-page-tabbed.php`

#### Replace Function Prefix
Find: `ez_it_template`  
Replace with: `your_plugin`

**Files to update:**
- Main plugin file
- `admin/class-admin-page-tabbed.php`

#### Replace Text Domain
Find: `ez-it-template`  
Replace with: `your-plugin-slug`

### Step 5: Update Constants

In your main plugin file, update the constants:

```php
define('YOUR_PLUGIN_VERSION', '1.0.0');
define('YOUR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('YOUR_PLUGIN_URL', plugin_dir_url(__FILE__));
```

### Step 6: Customize Plugin Title

In `admin/class-admin-page-tabbed.php`, update the header title:

```php
<h1 class="ezit-title">
    <span class="dashicons dashicons-your-icon"></span>
    Your Company – Your Plugin Name
</h1>
```

### Step 7: Customize Tabs

#### Remove Unwanted Tabs
In `admin/class-admin-page-tabbed.php`, remove tabs you don't need from the navigation.

#### Add New Tabs
1. Add tab link in navigation:
```php
<a href="?page=your-plugin&tab=features" class="ezit-tab" data-tab="features">
    <span class="dashicons dashicons-star-filled"></span>
    Features
</a>
```

2. Create render method:
```php
public static function render_features_tab() {
    ?>
    <h2 class="ezit-section-title">Features</h2>
    <div class="ezit-card">
        <h3><span class="dashicons dashicons-star-filled"></span> Your Features</h3>
        <p>Feature content here...</p>
    </div>
    <?php
}
```

3. Add case to AJAX handler in main plugin file:
```php
case 'features':
    YourPlugin_Admin_Page_Tabbed::render_features_tab();
    break;
```

### Step 8: Customize Settings

In the `render_settings_tab()` method:

1. **Update Advanced Settings:**
   - Modify existing settings
   - Add new settings
   - Remove unwanted settings

2. **Update Option Names:**
   - Change `ez_it_template_cache_enabled` to `your_plugin_cache_enabled`
   - Update all option names consistently

3. **Update Form Action:**
```php
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <?php wp_nonce_field('your_plugin_save_settings'); ?>
    <input type="hidden" name="action" value="your_plugin_save_settings">
    <!-- Your settings fields -->
</form>
```

### Step 9: Update About Tab

In `render_about_tab()` method, update:
- Plugin version
- Author information
- Website URL
- Features list
- System requirements

### Step 10: Customize Dashboard

In `render_dashboard_tab()` method:
- Update Quick Stats
- Modify sidebar content
- Add your plugin-specific information

### Step 11: Update Menu Icon

In main plugin file, change the menu icon:

```php
add_menu_page(
    'Your Plugin',
    'Your Plugin',
    'manage_options',
    'your-plugin-slug',
    ['YourPlugin_Admin_Page_Tabbed', 'render'],
    'dashicons-your-icon',  // Change this
    30
);
```

[View all available Dashicons](https://developer.wordpress.org/resource/dashicons/)

### Step 12: Customize Colors (Optional)

If you want different brand colors:

1. Open `assets/css/admin-tabbed.css`
2. Find and replace color values:
   - Dark mode primary: `#a3e635` → your color
   - Light mode primary: `#16a34a` → your color
3. Update `BRANDING.md` with your new colors

### Step 13: Add Your Functionality

Now add your plugin's core functionality:

1. Create new PHP classes in a new `includes/` folder
2. Add your business logic
3. Hook into WordPress actions and filters
4. Create database tables if needed
5. Add your custom features

### Step 14: Update Documentation

1. Update `README.md` with your plugin details
2. Modify `BRANDING.md` if you changed colors
3. Create `CHANGELOG.md` for version tracking

### Step 15: Test Everything

- [ ] Test in dark mode
- [ ] Test in light mode
- [ ] Test all tabs
- [ ] Test settings save functionality
- [ ] Test theme toggle
- [ ] Test on different screen sizes
- [ ] Test with different WordPress versions
- [ ] Test with different PHP versions

## 🎨 Customization Examples

### Example 1: Adding a Stats Card

```php
<div class="ezit-stat">
    <span class="ezit-stat-value">42</span>
    <span class="ezit-stat-label">Total Items</span>
</div>
```

### Example 2: Adding a Toggle Setting

```php
<div class="ezit-setting-item">
    <div class="ezit-setting-header">
        <label class="ezit-setting-label">
            <span class="dashicons dashicons-admin-generic"></span>
            Enable Feature
        </label>
        <label class="ezit-toggle">
            <input type="checkbox" name="feature_enabled" value="1" <?php checked($feature_enabled); ?>>
            <span class="ezit-toggle-slider"></span>
        </label>
    </div>
    <p class="ezit-setting-description">Enable this awesome feature.</p>
</div>
```

### Example 3: Adding a Sidebar Card

```php
<div class="ezit-sidebar-card">
    <h3><span class="dashicons dashicons-info"></span> Quick Tip</h3>
    <p>Your helpful tip here...</p>
</div>
```

## 🔍 Verification Checklist

After customization, verify:

- [ ] All plugin headers are updated
- [ ] All class names are changed
- [ ] All function prefixes are changed
- [ ] All option names are changed
- [ ] All text domains are changed
- [ ] Menu title and icon are correct
- [ ] All tabs work correctly
- [ ] Settings save properly
- [ ] Theme toggle works
- [ ] No PHP errors or warnings
- [ ] No JavaScript console errors
- [ ] Responsive design works
- [ ] Documentation is updated

## 🚀 Deployment

### For Development
1. Copy to `wp-content/plugins/`
2. Activate in WordPress admin
3. Test thoroughly

### For Production
1. Remove development files
2. Minify CSS and JavaScript
3. Test on staging environment
4. Create plugin ZIP file
5. Deploy to production

### Creating a ZIP File
```bash
cd /path/to/plugins
zip -r your-plugin-name.zip your-plugin-name/ -x "*.git*" "node_modules/*" "*.DS_Store"
```

## 📞 Need Help?

If you encounter issues during setup:

1. Check the [README.md](README.md) for general information
2. Review [BRANDING.md](BRANDING.md) for design guidelines
3. Contact Ez IT Solutions support

## 🎓 Learning Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Dashicons Reference](https://developer.wordpress.org/resource/dashicons/)
- [WordPress AJAX](https://codex.wordpress.org/AJAX_in_Plugins)

---

**Happy Plugin Development! 🎉**

*Developed by Ez IT Solutions | Chris Hultberg*  
*Website: [www.Ez-IT-Solutions.com](https://www.Ez-IT-Solutions.com)*
