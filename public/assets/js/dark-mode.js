/**
 * Dark Mode Toggle - University Portal
 * 
 * This script handles:
 * - Dark mode toggle functionality
 * - Storing preference in localStorage
 * - Applying dark mode on page load
 * - Smooth transitions between modes
 */

(function() {
  'use strict';

  // Constants
  const DARK_MODE_KEY = 'darkMode';
  const DARK_CLASS = 'dark';
  const TRANSITION_DURATION = 300; // milliseconds

  /**
   * Dark Mode Manager
   */
  const DarkMode = {
    /**
     * Initialize dark mode
     */
    init() {
      // Apply saved preference or system preference on load
      this.applyPreference();
      
      // Set up toggle button listeners
      this.setupToggleListeners();
      
      // Listen for system preference changes
      this.watchSystemPreference();
    },

    /**
     * Get current dark mode state
     * @returns {boolean} True if dark mode is enabled
     */
    isEnabled() {
      return document.documentElement.classList.contains(DARK_CLASS);
    },

    /**
     * Enable dark mode
     */
    enable() {
      document.documentElement.classList.add(DARK_CLASS);
      this.savePreference(true);
      this.updateToggleButtons(true);
      this.dispatchChangeEvent(true);
    },

    /**
     * Disable dark mode
     */
    disable() {
      document.documentElement.classList.remove(DARK_CLASS);
      this.savePreference(false);
      this.updateToggleButtons(false);
      this.dispatchChangeEvent(false);
    },

    /**
     * Toggle dark mode
     */
    toggle() {
      if (this.isEnabled()) {
        this.disable();
      } else {
        this.enable();
      }
    },

    /**
     * Get saved preference from localStorage
     * @returns {boolean|null} Saved preference or null if not set
     */
    getSavedPreference() {
      const saved = localStorage.getItem(DARK_MODE_KEY);
      if (saved === null) return null;
      return saved === 'true';
    },

    /**
     * Save preference to localStorage
     * @param {boolean} enabled - Whether dark mode is enabled
     */
    savePreference(enabled) {
      localStorage.setItem(DARK_MODE_KEY, enabled.toString());
    },

    /**
     * Get system preference
     * @returns {boolean} True if system prefers dark mode
     */
    getSystemPreference() {
      return window.matchMedia && 
             window.matchMedia('(prefers-color-scheme: dark)').matches;
    },

    /**
     * Apply saved preference or system preference
     */
    applyPreference() {
      const saved = this.getSavedPreference();
      
      // If user has saved preference, use it
      if (saved !== null) {
        if (saved) {
          document.documentElement.classList.add(DARK_CLASS);
        } else {
          document.documentElement.classList.remove(DARK_CLASS);
        }
        this.updateToggleButtons(saved);
        return;
      }
      
      // Otherwise, use system preference
      if (this.getSystemPreference()) {
        document.documentElement.classList.add(DARK_CLASS);
        this.updateToggleButtons(true);
      } else {
        this.updateToggleButtons(false);
      }
    },

    /**
     * Watch for system preference changes
     */
    watchSystemPreference() {
      if (!window.matchMedia) return;
      
      const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
      
      // Modern browsers
      if (mediaQuery.addEventListener) {
        mediaQuery.addEventListener('change', (e) => {
          // Only apply system preference if user hasn't set a preference
          if (this.getSavedPreference() === null) {
            if (e.matches) {
              this.enable();
            } else {
              this.disable();
            }
          }
        });
      }
      // Older browsers
      else if (mediaQuery.addListener) {
        mediaQuery.addListener((e) => {
          if (this.getSavedPreference() === null) {
            if (e.matches) {
              this.enable();
            } else {
              this.disable();
            }
          }
        });
      }
    },

    /**
     * Set up toggle button listeners
     */
    setupToggleListeners() {
      // Find all dark mode toggle buttons
      const toggleButtons = document.querySelectorAll('[data-dark-mode-toggle]');
      
      toggleButtons.forEach(button => {
        button.addEventListener('click', (e) => {
          e.preventDefault();
          this.toggle();
        });
      });
    },

    /**
     * Update toggle button states
     * @param {boolean} isDark - Whether dark mode is enabled
     */
    updateToggleButtons(isDark) {
      const toggleButtons = document.querySelectorAll('[data-dark-mode-toggle]');
      
      toggleButtons.forEach(button => {
        // Update aria-pressed attribute for accessibility
        button.setAttribute('aria-pressed', isDark.toString());
        
        // Update icon if button has data-icon-light and data-icon-dark attributes
        const lightIcon = button.getAttribute('data-icon-light');
        const darkIcon = button.getAttribute('data-icon-dark');
        
        if (lightIcon && darkIcon) {
          const icon = button.querySelector('i');
          if (icon) {
            icon.className = isDark ? darkIcon : lightIcon;
          }
        }
        
        // Update text if button has data-text-light and data-text-dark attributes
        const lightText = button.getAttribute('data-text-light');
        const darkText = button.getAttribute('data-text-dark');
        
        if (lightText && darkText) {
          const textElement = button.querySelector('[data-dark-mode-text]');
          if (textElement) {
            textElement.textContent = isDark ? darkText : lightText;
          }
        }
      });
    },

    /**
     * Dispatch custom event when dark mode changes
     * @param {boolean} isDark - Whether dark mode is enabled
     */
    dispatchChangeEvent(isDark) {
      const event = new CustomEvent('darkModeChange', {
        detail: { isDark },
        bubbles: true
      });
      document.dispatchEvent(event);
    }
  };

  /**
   * Apply dark mode immediately (before page renders)
   * This prevents flash of wrong theme
   */
  (function applyImmediately() {
    const saved = localStorage.getItem(DARK_MODE_KEY);
    
    if (saved === 'true') {
      document.documentElement.classList.add(DARK_CLASS);
    } else if (saved === 'false') {
      document.documentElement.classList.remove(DARK_CLASS);
    } else {
      // Use system preference
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add(DARK_CLASS);
      }
    }
  })();

  /**
   * Initialize when DOM is ready
   */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      DarkMode.init();
    });
  } else {
    DarkMode.init();
  }

  /**
   * Expose DarkMode to global scope for manual control
   */
  window.DarkMode = DarkMode;

})();

/**
 * Usage Examples:
 * 
 * 1. Add a toggle button in HTML:
 * <button 
 *   data-dark-mode-toggle
 *   data-icon-light="fas fa-moon"
 *   data-icon-dark="fas fa-sun"
 *   aria-label="Toggle dark mode"
 *   class="glass-btn glass-btn-icon"
 * >
 *   <i class="fas fa-moon"></i>
 * </button>
 * 
 * 2. Manually control dark mode:
 * DarkMode.enable();   // Enable dark mode
 * DarkMode.disable();  // Disable dark mode
 * DarkMode.toggle();   // Toggle dark mode
 * DarkMode.isEnabled(); // Check if dark mode is enabled
 * 
 * 3. Listen for dark mode changes:
 * document.addEventListener('darkModeChange', (e) => {
 *   console.log('Dark mode is now:', e.detail.isDark ? 'enabled' : 'disabled');
 * });
 */
