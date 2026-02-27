{{-- Premium Toast Notifications --}}
<div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 9999;">
    <div id="erpToast" class="toast hardened-glass border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(20px);">
        <div class="d-flex align-items-center p-3">
            <div id="toastIconContainer" class="me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i id="toastIcon" class="bi fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <h6 id="toastTitle" class="mb-0 fw-800 text-erp-deep">System Notification</h6>
                <p id="toastMessage" class="mb-0 small text-muted"></p>
            </div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- Global Command Palette --}}
<div id="commandPalette" class="command-palette-overlay">
    <div class="command-palette-modal">
        <input type="text" class="command-input" placeholder="Jump to anywhere... (try 'Inventory', 'HR', or 'New Employee')" id="cmdInput">
        <div id="cmdResults" class="p-2" style="max-height: 400px; overflow-y: auto;">
            <!-- Results will be injected here -->
        </div>
        <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center x-small text-muted">
            <span>↑↓ to navigate • ↵ to select</span>
            <span>ESC to close</span>
        </div>
    </div>
</div>

{{-- Global Rejection Modal --}}
<div class="modal fade" id="globalRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content hardened-glass-static p-0" style="background: white; border-radius: 24px;">
            <form id="globalRejectForm" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-800 text-erp-deep mb-0">Reject <span id="rejectTypeLabel">Request</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Are you sure you want to reject this request? Please provide a brief reason for the record.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase mb-2 text-erp-deep">Reason for Rejection</label>
                        <textarea name="rejection_reason" id="globalRejectReason" class="form-control rounded-3 bg-light border-0" rows="3" required placeholder="e.g. Documentation required..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Global Premium Confirmation Modal --}}
<div class="modal fade" id="premiumConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(25px);">
            <div class="modal-body p-5 text-center">
                <div class="mb-4 position-relative d-inline-block">
                    <div class="avatar-circle rounded-circle shadow-sm overflow-hidden border border-4 border-white mx-auto d-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                        <img id="confirmUserImage" src="" alt="Img" class="w-100 h-100 object-fit-cover" style="display: none;">
                        <span id="confirmUserInitial" class="text-white fw-900 fs-2">?</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 bg-warning rounded-circle p-2 shadow-sm border border-2 border-white">
                        <i class="bi bi-exclamation-triangle-fill text-white small"></i>
                    </div>
                </div>
                
                <h3 id="confirmTitle" class="fw-900 text-erp-deep mb-2">Are you sure?</h3>
                <div id="confirmUserName" class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 mb-3 fw-bold"></div>
                <p id="confirmMessage" class="text-muted mb-4"></p>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-bold" data-bs-dismiss="modal">Cancel Action</button>
                    <button id="confirmActionBtn" type="button" class="btn btn-erp-deep rounded-pill px-5 py-2 fw-bold shadow-sm">Confirm & Proceed</button>
                </div>
            </div>
        </div>
    </div>
</div>
