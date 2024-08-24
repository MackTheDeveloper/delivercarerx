<div class="modal fade text-left" id="update-order-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <input type="hidden" name="id" id="id">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white" id="myModalLabel120">Update Order Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="card-body card-dashboard">
                                <div class="col-md-12">
                                <select name="status" id="order_status" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="1">Pending</option>
                                    <option value="2">In Progress</option>
                                    <option value="3">Shipped</option>
                                </select>
                                <span id="err_order_status" style="color: red"></span>
                                </div>
                                <div class="col-md-12 mt-1">
                                <input type="text" style="display: none" name="tracking_number" id="tracking_number" class="form-control" placeholder="Tracking Number">
                                <span id="err_tracking_number" style="color: red"></span>
                                </div>
                                <div class="col-md-12 mt-1">
                                <select name="shipping_carrier" style="display: none" id="shipping_carrier" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($logistics as $lg)
                                    <option value="{{$lg->id}}">{{$lg->name}}</option>
                                    @endforeach
                                    
                                </select>
                                <span id="err_shipping_carrier" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                
            <div class="modal-footer">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary mr-1"  id="updateOrderStatus">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Update</span>
                    </button>
                    </div>
            </div>
        </div>
    </div>
</div>
