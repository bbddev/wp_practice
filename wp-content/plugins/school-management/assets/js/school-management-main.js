window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.App = {

  init: function () {
    // Wait for DOM to be ready
    jQuery(document).ready(
      function ($) {
        window.SchoolManagement.$ = $;
        this.initializeModules($);
      }.bind(this)
    );
  },

  initializeModules: function ($) {

    if (window.SchoolManagement.Pagination) {
      window.SchoolManagement.Pagination.init($);
    }

    if (window.SchoolManagement.Entity) {
      window.SchoolManagement.Entity.init($);
    }

    if (window.SchoolManagement.Password) {
      window.SchoolManagement.Password.init($);
    }

    if (window.SchoolManagement.Modal) {
      window.SchoolManagement.Modal.init($);
    }

    if (window.SchoolManagement.SchoolClass) {
      window.SchoolManagement.SchoolClass.init($);
    }
  },
};

// Auto-initialize when script loads
window.SchoolManagement.App.init();
