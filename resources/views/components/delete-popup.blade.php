<div class="modal fade text-left" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <input type="hidden" name="id" id="id">
            <div class="modal-header bg-danger">
                <h5 class="modal-title white" id="myModalLabel120">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="modal-body" id="message_delete">Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">No</span>
                </button>
                <button type="button" class="btn btn-danger ml-1" data-dismiss="modal" id="deleteButton">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Yes</span>
                </button>
            </div>
        </div>
    </div>
</div>