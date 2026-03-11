<div id="confirmDialog" class="confirmDialog" style="display:none;">
    <div aria-expanded="true" class="v--modal-overlay">
        <div class="v--modal-background-click">
            <div class="v--modal-top-right"></div>
            <div role="dialog" aria-modal="true" class="modal-student">
                <div class="box admin-modal">
                    <button type="button" class="btn-close" data-action="close">
                        <img src="/images/x.svg" alt="{{ trans('admin.btn.close') }}">
                    </button>
                    <div class="box">
                        <p class="box-title" id="confirmSubject"></p>
                        <p class="box-message" id="confirmMessage">{{ trans('student.confirm.text') }}</p>
                        <div class="box-btn">
                            <button type="button" class="box-btn_subprimary" data-action="cancel"  id="confirmNo">
                                {{ trans('admin.btn.cancel') }}
                            </button>
                            <button type="button" class="box-btn_primary" data-action="confirm" id="confirmYes">
                                {{ trans('admin.btn.yes') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
