# Ez IT | WordPress Plugin Template

A modern, professional WordPress plugin boilerplate featuring a beautiful tabbed admin interface with dark/light themes, advanced settings, and a complete design system.

![Version](https://img.shields.io/badge/version-1.0.0-green.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-Proprietary-red.svg)

## 🎨 Features

### Modern Admin Interface
- **Tabbed Navigation** - Clean, organized tabs with colored top borders
- **Dark & Light Themes** - Beautiful theme toggle with persistent storage
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile
- **AJAX Tab Loading** - Smooth transitions without page reloads
- **No Admin Notices** - Full-page layout with hidden WordPress notices

### UI Components
- **Toggle Switches** - Beautiful on/off switches for settings
- **Stats Cards** - Eye-catching cards with hover effects
- **Theme Preview Cards** - Interactive theme selector with visual previews
- **Sidebar Panels** - Help and information panels
- **Loading States** - Smooth loading animations

### Design System
- **Consistent Color Palette** - Lime green (#a3e635) for dark mode, green (#16a34a) for light mode
- **Typography System** - Well-defined font sizes and weights
- **Spacing Scale** - Consistent margins and padding
- **Component Library** - Reusable UI components
- **Comprehensive Documentation** - Full brand guidelines in BRANDING.md

### Developer Features
- **Clean Code Structure** - Well-organized files and folders
- **WordPress Standards** - Follows WordPress coding standards
- **Security Best Practices** - Nonces, capability checks, sanitization
- **Easy Customization** - Simple find-and-replace to rebrand
- **Extensible Architecture** - Easy to add new tabs and features

## 📋 Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

## 🚀 Quick Start

### 1. Clone or Download
```bash
git clone https://github.com/ez-it-solutions/ez-it-wordpress-template.git
```

### 2. Customize for Your Plugin
Follow the [SETUP-GUIDE.md](SETUP-GUIDE.md) for detailed instructions on customizing this template for your specific plugin.

**Quick customization checklist:**
- [ ] Rename main plugin file
- [ ] Update plugin headers
- [ ] Find and replace `ez-it-template` with your plugin slug
- [ ] Find and replace `EzIT_Template` with your plugin class prefix
- [ ] Update plugin name and description
- [ ] Customize tabs and functionality
- [ ] Update BRANDING.md with your colors (optional)

### 3. Install
1. Upload the customized plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to the plugin's admin page

## 📁 File Structure

```
ez-it-wordpress-template/
├── admin/
│   └── class-admin-page-tabbed.php    # Main admin interface class
├── assets/
│   ├── css/
│   │   └── admin-tabbed.css           # All admin styles
│   └── js/
│       └── admin.js                    # Admin JavaScript
├── ez-it-plugin-template.php          # Main plugin file
├── README.md                           # This file
├── SETUP-GUIDE.md                      # Customization instructions
└── BRANDING.md                         # Design system documentation
```

## 🎨 Design System

This template includes a complete design system with:

- **Color Palettes** - Dark and light mode colors
- **Typography** - Font families, sizes, and weights
- **UI Components** - Buttons, cards, toggles, tabs
- **Spacing System** - Consistent margins and padding
- **Animation Guidelines** - Transitions and hover effects

See [BRANDING.md](BRANDING.md) for complete design documentation.

## 🔧 Customization

### Adding a New Tab

1. **Add tab to navigation** in `class-admin-page-tabbed.php`:
```php
<a href="?page=your-plugin&tab=newtab" class="ezit-tab" data-tab="newtab">
    <span class="dashicons dashicons-admin-generic"></span>
    New Tab
</a>
```

2. **Create render method**:
```php
public static function render_newtab_tab() {
    ?>
    <h2 class="ezit-section-title">New Tab</h2>
    <div class="ezit-card">
        <h3>Content Here</h3>
        <p>Your tab content...</p>
    </div>
    <?php
}
```

3. **Add to AJAX handler** in main plugin file:
```php
case 'newtab':
    YourPlugin_Admin_Page_Tabbed::render_newtab_tab();
    break;
```

### Changing Colors

Edit `assets/css/admin-tabbed.css`:

**Dark Mode Primary Color:**
```css
/* Change #a3e635 to your color */
.ezit-stat-value { color: #a3e635; }
.ezit-card h3 { color: #a3e635; }
```

**Light Mode Primary Color:**
```css
/* Change #16a34a to your color */
.ezit-light .ezit-stat-value { color: #16a34a; }
.ezit-light .ezit-card h3 { color: #16a34a; }
```

### Adding Settings

Add new settings in the Settings tab render method and create corresponding save handlers in the main plugin file.

## 📚 Documentation

- **[SETUP-GUIDE.md](SETUP-GUIDE.md)** - Step-by-step customization guide
- **[BRANDING.md](BRANDING.md)** - Complete design system documentation
- **[CHANGELOG.md](CHANGELOG.md)** - Version history and changes

## 🤝 Support

Need help customizing this template?

- **Website:** [www.Ez-IT-Solutions.com](https://www.Ez-IT-Solutions.com)
- **Email:** support@ez-it-solutions.com

## 📄 License

This template is proprietary software developed by Ez IT Solutions.

**For Ez IT Solutions Internal Use:**
- ✅ Use for client projects
- ✅ Modify and customize
- ✅ Create derivative plugins
- ❌ Redistribute or resell the template itself
- ❌ Share with third parties

## 🎯 Best Practices

### Security
- Always use nonces for form submissions
- Check user capabilities before actions
- Sanitize all input data
- Escape all output data

### Performance
- Enqueue scripts only on plugin pages
- Use transients for caching
- Minimize database queries
- Optimize images and assets

### Code Quality
- Follow WordPress coding standards
- Comment complex logic
- Use meaningful variable names
- Keep functions focused and small

## 🔄 Updates

This template is actively maintained. Check back for updates and improvements.

**Current Version:** 1.0.0  
**Last Updated:** November 7, 2025

## 👨‍💻 Credits

**Developed by:** Ez IT Solutions | Chris Hultberg  
**Website:** [www.Ez-IT-Solutions.com](https://www.Ez-IT-Solutions.com)

---

**Made with ❤️ by Ez IT Solutions**
