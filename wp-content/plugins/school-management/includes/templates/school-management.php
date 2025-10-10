<div id="school-management-container">
    <div id="select-class" class="select-class">
        <label for="class-dropdown" class="center-label">Chọn danh sách bài học:</label>
        <select id="class-dropdown" class="center-select">
            <option value="">-- Chọn --</option>
        </select>
    </div>

    <div id="entity-container" class="entity-container">
        <div class="row d-flex justify-content-between align-items-center mb-3">
            <!-- <div class="col-auto">
                <button class="btn btn-secondary" onclick="showSchoolSelection()">Back</button>
            </div>
            <div class="col text-center">
                <h3 class="mb-0">Danh sách học sinh tham gia của <strong id="class-title"></strong>:</h3>
            </div> -->
            <div class="col-auto">
                <!-- Empty column for balance -->
            </div>
        </div>
        <div id="entity-grid" class="entity-grid">
            <!-- AJAX -->
        </div>
        <div id="pagination-container" class="pagination-container">
            <!-- Pagination will be generated here -->
        </div>
    </div>
</div>

<!-- Custom Modal cho Class Password -->
<div class="custom-modal-overlay" id="classPasswordModal" role="dialog" aria-labelledby="classPasswordModalLabel"
    aria-hidden="true">
    <div class="custom-modal" role="document">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title" id="classPasswordModalLabel">Nhập mật khẩu lớp học</h5>
            <button type="button" class="custom-modal-close" onclick="window.SchoolManagement.CustomModal.closeModal()"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="custom-modal-body">
            <div class="custom-form-group">
                <label for="classPasswordInput">Mật khẩu lớp học:</label>
                <div class="custom-input-group">
                    <input type="password" class="password-input" id="classPasswordInput"
                        placeholder="Nhập mật khẩu...">
                    <button class="custom-password-toggle" type="button" id="toggleClassPassword">
                        <i class="fa fa-eye" id="classPasswordIcon"></i>
                    </button>
                </div>
                <div id="classPasswordError" class="custom-error-message"></div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="custom-btn custom-btn-secondary"
                onclick="window.SchoolManagement.CustomModal.closeModal()">Hủy</button>
            <button type="button" class="custom-btn custom-btn-primary" id="submitClassPassword">Xác nhận</button>
        </div>
    </div>
</div>

<!-- Custom Modal cho Lesson Password -->
<div class="custom-modal-overlay" id="lessonPasswordModal" role="dialog" aria-labelledby="lessonPasswordModalLabel"
    aria-hidden="true">
    <div class="custom-modal" role="document">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title" id="lessonPasswordModalLabel">Truy cập bài học</h5>
            <button type="button" class="custom-modal-close" onclick="window.SchoolManagement.CustomModal.closeModal()"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="custom-modal-body">
            <div class="custom-form-group">
                <label for="usernameInput">Username:</label>
                <div class="custom-input-group">
                    <input type="text" id="usernameInput" placeholder="Nhập username...">
                </div>
                <div id="usernameError" class="custom-error-message"></div>
            </div>
            <div class="custom-form-group">
                <label for="lessonPasswordInput">Mật khẩu bài học:</label>
                <div class="custom-input-group">
                    <input type="password" class="password-input" id="lessonPasswordInput"
                        placeholder="Nhập mật khẩu...">
                    <button class="custom-password-toggle" type="button" id="toggleLessonPassword">
                        <i class="fa fa-eye" id="lessonPasswordIcon"></i>
                    </button>
                </div>
                <div id="lessonPasswordError" class="custom-error-message"></div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="custom-btn custom-btn-secondary"
                onclick="window.SchoolManagement.CustomModal.closeModal()">Hủy</button>
            <button type="button" class="custom-btn custom-btn-primary" id="submitLessonPassword">Xác nhận</button>
        </div>
    </div>
</div>

<!-- Custom Modal cho Student Login -->
<div class="custom-modal-overlay" id="studentLoginModal" role="dialog" aria-labelledby="studentLoginModalLabel"
    aria-hidden="true">
    <div class="custom-modal" role="document">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title" id="studentLoginModalLabel">Login</h5>
            <!-- <button type="button" class="custom-modal-close" onclick="window.SchoolManagement.CustomModal.closeModal()"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> -->
        </div>
        <div class="custom-modal-body">
            <div class="custom-form-group">
                <label for="studentUsernameInput">Username:</label>
                <div class="custom-input-group">
                    <input type="text" id="studentUsernameInput" placeholder="Nhập username...">
                </div>
                <div id="studentUsernameError" class="custom-error-message"></div>
            </div>
            <div class="custom-form-group">
                <label for="studentPasswordInput">Password:</label>
                <div class="custom-input-group">
                    <input type="password" class="password-input" id="studentPasswordInput"
                        placeholder="Nhập mật khẩu...">
                    <button class="custom-password-toggle" type="button" id="toggleStudentPassword">
                        <i class="fa fa-eye" id="studentPasswordIcon"></i>
                    </button>
                </div>
                <div id="studentPasswordError" class="custom-error-message"></div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="custom-btn custom-btn-primary" id="submitStudentLogin">Confirm</button>
        </div>
    </div>
</div>
<div id="student-status" class="student-status">
    <span id="student-info">Xin chào <strong id="student-name"></strong></span>
    <button type="button" id="student-logout-btn"
        class="btn btn-sm btn-outline-secondary student-logout-btn">Logout</button>
</div>