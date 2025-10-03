/**
 * Modal Management Module
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.Modal = {
  /**
   * Initialize modal functionality
   * @param {Object} $ - jQuery object
   */
  init: function ($) {
    this.$ = $;
    this.bindEvents();
  },

  /**
   * Bind modal-related events
   */
  bindEvents: function () {
    const self = this;
    const $ = this.$;

    // Force modals on top when shown
    $("#classPasswordModal").on("show.bs.modal", function () {
      setTimeout(() => {
        self.forceModalOnTop();
      }, 50);
    });

    $("#lessonPasswordModal").on("show.bs.modal", function () {
      setTimeout(() => {
        self.forceModalOnTop();
      }, 50);
    });

    // Additional fix for when modal is fully shown
    $("#classPasswordModal").on("shown.bs.modal", function () {
      self.forceModalOnTop();
      // Focus on input to ensure it's interactive
      $("#classPasswordInput").focus();
    });

    $("#lessonPasswordModal").on("shown.bs.modal", function () {
      self.forceModalOnTop();
      // Focus on input to ensure it's interactive
      $("#usernameInput").focus();
    });
  },

  /**
   * Force modal to appear on top by removing backdrop conflicts
   */
  forceModalOnTop: function () {
    const $ = this.$;
    $(".modal-backdrop").remove();
  },
};
