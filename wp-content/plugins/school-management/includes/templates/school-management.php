<div id="school-management-container">
    <div id="select-school">
        <label for="school-dropdown">Chọn trường:</label>
        <select id="school-dropdown">
            <option value="">-- Chọn trường --</option>
        </select>
    </div>

    <div id="select-class" style="display: none;">
        <label for="class-dropdown">Chọn lớp:</label>
        <select id="class-dropdown">
            <option value="">-- Chọn lớp --</option>
        </select>
    </div>

    <div id="entity-container" style="display: none;">
        <h3>Danh sách entity:</h3>
        <div id="entity-grid" class="entity-grid">
            <!-- Entities will be loaded here via AJAX -->
        </div>
    </div>
</div>