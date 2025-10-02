/**
 * Utility functions for School Management
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.Utils = {
  /**
   * Sort text naturally (handles numbers in text)
   * @param {Object} a - First object with title property
   * @param {Object} b - Second object with title property
   * @returns {number} Sort comparison result
   */
  naturalSort: function (a, b) {
    const aTitle = a.title.toLowerCase();
    const bTitle = b.title.toLowerCase();

    return aTitle.localeCompare(bTitle, undefined, {
      numeric: true,
      sensitivity: "base",
    });
  },

  /**
   * Sort arrays by post_title property naturally
   * @param {Array} array - Array to sort
   * @returns {Array} Sorted array
   */
  sortByTitle: function (array) {
    return array.sort(function (a, b) {
      return a.post_title.localeCompare(b.post_title, undefined, {
        numeric: true,
        sensitivity: "base",
      });
    });
  },

  /**
   * Create AJAX request with proper headers
   * @param {Object} options - AJAX options
   * @returns {jqXHR} jQuery AJAX object
   */
  createAjaxRequest: function (options) {
    const $ = window.SchoolManagement.$ || jQuery;
    const defaultOptions = {
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
    };

    return $.ajax($.extend(defaultOptions, options));
  },
};
