/**
 * Custom Modal Management - No Bootstrap Dependencies
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.CustomModal = {
  /**
   * Initialize custom modal functionality
   */
  init: function () {
    this.bindEvents();
    this.currentModal = null;
    this.focusableElements =
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
  },

  /**
   * Bind modal events
   */
  bindEvents: function () {
    const self = this;

    // Close modal when clicking overlay
    document.addEventListener("click", function (e) {
      if (e.target.classList.contains("custom-modal-overlay")) {
        self.closeModal();
      }
    });

    // Close modal with Escape key
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && self.currentModal) {
        self.closeModal();
      }

      // Handle tab key for focus trap
      if (e.key === "Tab" && self.currentModal) {
        self.handleTabKey(e);
      }
    });

    // Prevent body scroll when modal is open
    this.preventBodyScroll();
  },

  /**
   * Show modal
   * @param {string} modalId - Modal ID
   * @param {Object} options - Modal options
   */
  showModal: function (modalId, options = {}) {
    const modal = document.getElementById(modalId);
    if (!modal) {
      console.error("Modal not found:", modalId);
      return;
    }

    this.currentModal = modal;

    // Set modal data if provided
    if (options.data) {
      Object.keys(options.data).forEach((key) => {
        modal.setAttribute("data-" + key, options.data[key]);
      });
    }

    // Show modal with animation
    modal.style.display = "flex";

    // Trigger reflow for animation
    modal.offsetHeight;

    modal.classList.add("show");
    document.body.classList.add("modal-open");

    // Focus management
    this.setFocus(modal);

    // Call onShow callback if provided
    if (options.onShow && typeof options.onShow === "function") {
      options.onShow(modal);
    }
  },

  /**
   * Hide modal
   * @param {string} modalId - Modal ID (optional)
   * @param {Object} options - Modal options
   */
  hideModal: function (modalId = null, options = {}) {
    const modal = modalId
      ? document.getElementById(modalId)
      : this.currentModal;
    if (!modal) return;

    modal.classList.remove("show");

    // Wait for animation to complete before hiding
    setTimeout(() => {
      modal.style.display = "none";
      document.body.classList.remove("modal-open");

      // Clear modal data
      this.clearModalData(modal);

      // Reset current modal
      if (this.currentModal === modal) {
        this.currentModal = null;
      }

      // Call onHide callback if provided
      if (options.onHide && typeof options.onHide === "function") {
        options.onHide(modal);
      }

      // Restore focus to previously focused element
      this.restoreFocus();
    }, 300);
  },

  /**
   * Close current modal (alias for hideModal)
   */
  closeModal: function () {
    this.hideModal();
  },

  /**
   * Set focus to modal
   * @param {Element} modal - Modal element
   */
  setFocus: function (modal) {
    // Store currently focused element
    this.previouslyFocused = document.activeElement;

    // Find first focusable element in modal
    const focusableElements = modal.querySelectorAll(this.focusableElements);
    if (focusableElements.length > 0) {
      focusableElements[0].focus();
    } else {
      modal.focus();
    }
  },

  /**
   * Restore focus to previously focused element
   */
  restoreFocus: function () {
    if (
      this.previouslyFocused &&
      typeof this.previouslyFocused.focus === "function"
    ) {
      this.previouslyFocused.focus();
    }
  },

  /**
   * Handle tab key for focus trap
   * @param {KeyboardEvent} e - Keyboard event
   */
  handleTabKey: function (e) {
    if (!this.currentModal) return;

    const focusableElements = this.currentModal.querySelectorAll(
      this.focusableElements
    );
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    if (e.shiftKey) {
      // Shift + Tab
      if (document.activeElement === firstElement) {
        lastElement.focus();
        e.preventDefault();
      }
    } else {
      // Tab
      if (document.activeElement === lastElement) {
        firstElement.focus();
        e.preventDefault();
      }
    }
  },

  /**
   * Clear modal data and reset form
   * @param {Element} modal - Modal element
   */
  clearModalData: function (modal) {
    // Clear form inputs
    const inputs = modal.querySelectorAll("input");
    inputs.forEach((input) => {
      if (input.type === "password" || input.type === "text") {
        input.value = "";
      }
    });

    // Hide error messages
    const errorMessages = modal.querySelectorAll(".custom-error-message");
    errorMessages.forEach((error) => {
      error.classList.remove("show");
      error.textContent = "";
    });

    // Remove error classes from inputs
    const errorInputs = modal.querySelectorAll(".error");
    errorInputs.forEach((input) => {
      input.classList.remove("error");
    });

    // Reset password visibility
    const passwordInputs = modal.querySelectorAll('input[type="text"]');
    passwordInputs.forEach((input) => {
      if (input.classList.contains("password-input")) {
        input.type = "password";
      }
    });

    const eyeIcons = modal.querySelectorAll(".fa-eye-slash");
    eyeIcons.forEach((icon) => {
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    });
  },

  /**
   * Prevent body scroll when modal is open
   */
  preventBodyScroll: function () {
    const style = document.createElement("style");
    style.textContent = `
      body.modal-open {
        overflow: hidden;
        padding-right: 17px; /* Compensate for scrollbar */
      }
      
      @media (max-width: 768px) {
        body.modal-open {
          padding-right: 0;
        }
      }
    `;
    document.head.appendChild(style);
  },

  /**
   * Show error message
   * @param {string} inputId - Input ID
   * @param {string} message - Error message
   */
  showError: function (inputId, message) {
    const input = document.getElementById(inputId);
    const errorElement = document.getElementById(
      inputId.replace("Input", "Error")
    );

    if (input) {
      input.classList.add("error");
    }

    if (errorElement) {
      errorElement.textContent = message;
      errorElement.classList.add("show");
    }
  },

  /**
   * Hide error message
   * @param {string} inputId - Input ID
   */
  hideError: function (inputId) {
    const input = document.getElementById(inputId);
    const errorElement = document.getElementById(
      inputId.replace("Input", "Error")
    );

    if (input) {
      input.classList.remove("error");
    }

    if (errorElement) {
      errorElement.classList.remove("show");
      errorElement.textContent = "";
    }
  },

  /**
   * Toggle password visibility
   * @param {string} inputId - Password input ID
   * @param {string} iconId - Eye icon ID
   */
  togglePasswordVisibility: function (inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (!input || !icon) return;

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    }
  },

  /**
   * Set button loading state
   * @param {string} buttonId - Button ID
   * @param {boolean} loading - Loading state
   */
  setButtonLoading: function (buttonId, loading) {
    const button = document.getElementById(buttonId);
    if (!button) return;

    if (loading) {
      button.classList.add("loading");
      button.disabled = true;
    } else {
      button.classList.remove("loading");
      button.disabled = false;
    }
  },

  /**
   * Get modal data
   * @param {string} modalId - Modal ID
   * @param {string} key - Data key
   * @returns {string} Data value
   */
  getModalData: function (modalId, key) {
    const modal = document.getElementById(modalId);
    return modal ? modal.getAttribute("data-" + key) : null;
  },

  /**
   * Set modal data
   * @param {string} modalId - Modal ID
   * @param {string} key - Data key
   * @param {string} value - Data value
   */
  setModalData: function (modalId, key, value) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.setAttribute("data-" + key, value);
    }
  },
};

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  window.SchoolManagement.CustomModal.init();
});
