/**
 * Pagination Management Module
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.Pagination = {
  currentPage: 1,
  entitiesPerPage: 12,
  totalEntities: 0,

  /**
   * Initialize pagination functionality
   * @param {Object} $ - jQuery object
   */
  init: function ($) {
    this.$ = $;
    this.bindEvents();
  },

  /**
   * Bind pagination events
   */
  bindEvents: function () {
    const self = this;
    const $ = this.$;

    // Handle pagination click
    $(document).on("click", ".pagination .page-link[data-page]", function (e) {
      e.preventDefault();
      const page = parseInt($(this).data("page"));
      if (page !== self.currentPage) {
        self.goToPage(page);
      }
    });
  },

  /**
   * Go to specific page
   * @param {number} page - Page number
   */
  goToPage: function (page) {
    this.currentPage = page;
    window.SchoolManagement.Entity.displayEntities();
    this.createPagination();

    // Scroll to top of entity container
    const $ = this.$;
    $("#entity-container")[0].scrollIntoView({ behavior: "smooth" });
  },

  /**
   * Set total entities and reset to first page
   * @param {number} total - Total number of entities
   */
  setTotalEntities: function (total) {
    this.totalEntities = total;
    this.currentPage = 1;
  },

  /**
   * Get current page entities slice
   * @param {Array} entities - All entities
   * @returns {Array} Current page entities
   */
  getCurrentPageEntities: function (entities) {
    const startIndex = (this.currentPage - 1) * this.entitiesPerPage;
    const endIndex = startIndex + this.entitiesPerPage;
    return entities.slice(startIndex, endIndex);
  },

  /**
   * Create pagination HTML
   */
  createPagination: function () {
    const $ = this.$;
    const totalPages = Math.ceil(this.totalEntities / this.entitiesPerPage);

    if (totalPages <= 1) {
      $("#pagination-container").hide();
      return;
    }

    const $pagination = $("#pagination-container");
    $pagination.empty();

    let paginationHtml =
      '<nav aria-label="Phân trang bài học"><ul class="pagination justify-content-center">';

    // Previous button
    paginationHtml += this.createPreviousButton();

    // Page numbers
    paginationHtml += this.createPageNumbers(totalPages);

    // Next button
    paginationHtml += this.createNextButton(totalPages);

    paginationHtml += "</ul></nav>";

    $pagination.html(paginationHtml).show();
  },

  /**
   * Create previous button HTML
   * @returns {string} Previous button HTML
   */
  createPreviousButton: function () {
    if (this.currentPage > 1) {
      return (
        '<li class="page-item"><a class="page-link" href="#" data-page="' +
        (this.currentPage - 1) +
        '">« Trước</a></li>'
      );
    } else {
      return '<li class="page-item disabled"><span class="page-link">« Trước</span></li>';
    }
  },

  /**
   * Create next button HTML
   * @param {number} totalPages - Total number of pages
   * @returns {string} Next button HTML
   */
  createNextButton: function (totalPages) {
    if (this.currentPage < totalPages) {
      return (
        '<li class="page-item"><a class="page-link" href="#" data-page="' +
        (this.currentPage + 1) +
        '">Sau »</a></li>'
      );
    } else {
      return '<li class="page-item disabled"><span class="page-link">Sau »</span></li>';
    }
  },

  /**
   * Create page numbers HTML
   * @param {number} totalPages - Total number of pages
   * @returns {string} Page numbers HTML
   */
  createPageNumbers: function (totalPages) {
    let html = "";
    const maxVisiblePages = 3;
    let startPage = Math.max(
      1,
      this.currentPage - Math.floor(maxVisiblePages / 2)
    );
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    // Adjust start page if we're near the end
    if (endPage - startPage < maxVisiblePages - 1) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    // First page with ellipsis if needed
    if (startPage > 1) {
      html +=
        '<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>';
      if (startPage > 2) {
        html +=
          '<li class="page-item disabled"><span class="page-link">...</span></li>';
      }
    }

    // Page numbers in visible range
    for (let i = startPage; i <= endPage; i++) {
      if (i === this.currentPage) {
        html +=
          '<li class="page-item active"><span class="page-link">' +
          i +
          "</span></li>";
      } else {
        html +=
          '<li class="page-item"><a class="page-link" href="#" data-page="' +
          i +
          '">' +
          i +
          "</a></li>";
      }
    }

    // Last page with ellipsis if needed
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        html +=
          '<li class="page-item disabled"><span class="page-link">...</span></li>';
      }
      html +=
        '<li class="page-item"><a class="page-link" href="#" data-page="' +
        totalPages +
        '">' +
        totalPages +
        "</a></li>";
    }

    return html;
  },

  /**
   * Reset pagination to initial state
   */
  reset: function () {
    const $ = this.$ || window.SchoolManagement.$ || jQuery;
    this.currentPage = 1;
    this.totalEntities = 0;
    $("#pagination-container").hide();
  },
};
