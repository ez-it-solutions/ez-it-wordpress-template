(function($){
  // Auto-select text on focus
  $(document).on('focus', '.ezit-copy', function(){ this.select(); });
  
  // AJAX Theme Toggle
  $('#ezit-theme-toggle').on('click', function(e) {
    e.preventDefault();
    
    const $button = $(this);
    const $wrap = $('#ezit-main-wrap');
    const $icon = $button.find('.ezit-theme-icon');
    const $text = $button.find('.ezit-theme-text');
    const currentTheme = $wrap.data('theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    // Store original content
    const originalIcon = $icon.html();
    const originalText = $text.text();
    
    // Disable button and show loading state
    $button.prop('disabled', true);
    $icon.html('<span class="ezit-spinner"></span>');
    $text.text('Switching...');
    
    // Send AJAX request to save theme preference
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'ez_dh_togglezitTemplateAjax',
        nonce: ezDhAjax.nonce,
        theme: newTheme
      },
      success: function(response) {
        if (response.success) {
          // Update theme class with smooth transition
          $wrap.removeClass('ezit-dark ezit-light').addClass('ezit-' + newTheme);
          $wrap.data('theme', newTheme);
          
          // Update button text and icon
          if (newTheme === 'dark') {
            $icon.text('☀️');
            $text.text('Light Mode');
          } else {
            $icon.text('🌙');
            $text.text('Dark Mode');
          }
          
          // Re-enable button after a short delay for smooth UX
          setTimeout(function() {
            $button.prop('disabled', false);
          }, 300);
        } else {
          // Restore original state on failure
          $icon.html(originalIcon);
          $text.text(originalText);
          $button.prop('disabled', false);
          alert('Failed to save theme preference. Please try again.');
        }
      },
      error: function() {
        // Restore original state on error
        $icon.html(originalIcon);
        $text.text(originalText);
        $button.prop('disabled', false);
        alert('Failed to save theme preference. Please try again.');
      }
    });
  });
  
  // AJAX Tab Switching
  $('.ezit-tab').on('click', function(e) {
    e.preventDefault();
    
    const $tab = $(this);
    const tabUrl = $tab.attr('href');
    const urlParams = new URLSearchParams(tabUrl.split('?')[1]);
    const tabName = urlParams.get('tab') || 'dashboard';
    
    // Don't reload if already active
    if ($tab.hasClass('ezit-tab-active')) {
      return;
    }
    
    // Update active tab styling
    $('.ezit-tab').removeClass('ezit-tab-active');
    $tab.addClass('ezit-tab-active');
    
    // Show loading modal with correct theme class
    const $modal = $('#ezit-loading-modal');
    const $wrap = $('#ezit-main-wrap');
    const currentTheme = $wrap.data('theme');
    
    // Apply theme class to modal
    $modal.removeClass('ezit-dark ezit-light').addClass('ezit-' + currentTheme);
    $modal.addClass('active');
    
    // Load tab content via AJAX
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'ez_dh_load_tab',
        nonce: ezDhAjax.nonce,
        tab: tabName
      },
      success: function(response) {
        if (response.success) {
          // Update content
          $('.ezit-main-content').html(response.data.content);
          $('.ezit-sidebar').html(response.data.sidebar);
          
          // Update URL without reload
          window.history.pushState({tab: tabName}, '', tabUrl);
          
          // Hide loading modal
          $('#ezit-loading-modal').removeClass('active');
          
          // Scroll to top
          $('.ezit-fullpage').scrollTop(0);
        } else {
          // Fallback to page reload on error
          window.location.href = tabUrl;
        }
      },
      error: function() {
        // Fallback to page reload on error
        window.location.href = tabUrl;
      }
    });
  });
  
  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.tab) {
      $('.ezit-tab[href*="tab=' + e.state.tab + '"]').trigger('click');
    }
  });
  
  // Theme selector in settings
  $(document).on('click', '.ezit-theme-option', function() {
    const theme = $(this).data('theme');
    const currentTheme = $('#ezit-main-wrap').data('theme');
    
    if (theme === currentTheme) {
      return;
    }
    
    // Update active states immediately
    $('.ezit-theme-option').removeClass('active');
    $('.ezit-theme-active-badge').remove();
    $(this).addClass('active');
    $(this).append('<span class="ezit-theme-active-badge">Active</span>');
    
    // Trigger the theme toggle
    $('#ezit-theme-toggle').trigger('click');
  });
})(jQuery);
