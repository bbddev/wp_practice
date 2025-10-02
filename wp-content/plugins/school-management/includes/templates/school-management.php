<div id="school-management-container">
    <div id="select-school">
        <label for="school-dropdown" style="text-align: center;">Chọn khối học sinh tham gia:</label>
        <select id="school-dropdown" style="margin: auto;">
            <option value="">-- Chọn khối học sinh tham gia --</option>
        </select>
    </div>

    <div id="select-class" style="display: none;">
        <label for="class-dropdown" style="text-align: center;">Chọn lớp:</label>
        <select id="class-dropdown" style="margin: auto;">
            <option value="">-- Chọn lớp --</option>
        </select>
    </div>

    <div id="entity-container" style="display: none;">
        <h3 style="text-align: center;">Danh sách tham gia:</h3>
        <div id="entity-grid" class="entity-grid">
            <!-- AJAX -->
        </div>
        <div id="pagination-container" style="display: none; margin-top: 20px;">
            <!-- Pagination will be generated here -->
        </div>
    </div>
</div>

<!-- Bootstrap Modal cho Class Password -->
<div class="modal fade" id="classPasswordModal" tabindex="-1" role="dialog" aria-labelledby="classPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classPasswordModalLabel">Nhập mật khẩu lớp học</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="classPasswordInput">Mật khẩu lớp học:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="classPasswordInput"
                            placeholder="Nhập mật khẩu...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="toggleClassPassword">
                                <i class="fa fa-eye" id="classPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div id="classPasswordError" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="submitClassPassword">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal cho Lesson Password -->
<div class="modal fade" id="lessonPasswordModal" tabindex="-1" role="dialog" aria-labelledby="lessonPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lessonPasswordModalLabel">Nhập mật khẩu bài học</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="lessonPasswordInput">Mật khẩu bài học:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="lessonPasswordInput"
                            placeholder="Nhập mật khẩu...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="toggleLessonPassword">
                                <i class="fa fa-eye" id="lessonPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div id="lessonPasswordError" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="submitLessonPassword">Xác nhận</button>
            </div>
        </div>
    </div>
</div>