/**
 * Entity Management Module
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.Entity = {
  allEntities: [],

  /**
   * Initialize entity functionality
   * @param {Object} $ - jQuery object
   */
  init: function ($) {
    this.$ = $;
    this.bindEvents();
  },

  /**
   * Bind entity-related events
   */
  bindEvents: function () {
    const self = this;
    const $ = this.$;

    // Handle entity click
    $(document).on("click", ".entity-item", function () {
      const entityId = $(this).data("entity-id");
      const entityLink = $(this).data("entity-link");

      self.handleEntityClick(entityId, entityLink);
    });
  },

  /**
   * Handle entity item click
   * @param {string} entityId - Entity ID
   * @param {string} entityLink - Entity link
   */
  handleEntityClick: function (entityId, entityLink) {
    if (!entityLink) {
      return; // No link to navigate to
    }

    // Check if lesson has password
    window.SchoolManagement.Password.checkLessonPassword(entityId, entityLink);
  },

  /**
   * Load entities for a specific class
   * @param {string} classId - Class ID
   */
  loadEntities: function (classId) {
    const self = this;

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/entities/" +
        classId,
      method: "GET",
      success: function (data) {
        if (data && data.length > 0) {
          // Sort entities naturally by title before storing
          data.sort(window.SchoolManagement.Utils.naturalSort);
          self.allEntities = data;

          // Set pagination and display
          window.SchoolManagement.Pagination.setTotalEntities(data.length);
          self.displayEntities();
          window.SchoolManagement.Pagination.createPagination();
        } else {
          self.showNoEntitiesMessage();
        }
      },
      error: function () {
        console.error("Error loading entities");
      },
    });
  },

  /**
   * Display entities for current page
   */
  displayEntities: function () {
    const $ = this.$;
    const $grid = $("#entity-grid");
    $grid.empty();

    const entitiesToShow =
      window.SchoolManagement.Pagination.getCurrentPageEntities(
        this.allEntities
      );

    $.each(
      entitiesToShow,
      function (index, entity) {
        const entityHtml = this.createEntityItemHtml(entity);
        $grid.append(entityHtml);
      }.bind(this)
    );
  },

  /**
   * Create HTML for a single entity item
   * @param {Object} entity - Entity data
   * @returns {string} Entity HTML
   */
  createEntityItemHtml: function (entity) {
    let entityHtml =
      '<div class="entity-item" data-entity-id="' + entity.id + '"';

    if (entity.link) {
      entityHtml += ' data-entity-link="' + entity.link + "?" + entity.id + '"';
    }

    entityHtml += ">";

    if (entity.image) {
      entityHtml +=
        '<img src="' + entity.image + '" alt="' + entity.title + '">';
    } else {
      entityHtml += '<div class="no-image">Không có hình</div>';
    }

    entityHtml += "<h4>" + entity.title + "</h4>";
    entityHtml += "</div>";

    return entityHtml;
  },

  /**
   * Show message when no entities are found
   */
  showNoEntitiesMessage: function () {
    const $ = this.$ || window.SchoolManagement.$ || jQuery;
    const $grid = $("#entity-grid");
    $grid.html("<p>Không có bài học nào trong lớp này.</p>");
    $("#pagination-container").hide();
  },

  /**
   * Reset entity state
   */
  reset: function () {
    this.allEntities = [];
    window.SchoolManagement.Pagination.reset();
  },
};
