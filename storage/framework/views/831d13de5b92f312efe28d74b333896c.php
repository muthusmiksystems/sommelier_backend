
<?php $__env->startSection("title"); ?> Delivery Collection Logs - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="content mt-3">
    <div class="d-flex justify-content-between my-2">
        <h3><strong>Delivery Collection Logs</strong> <?php if($user != null): ?> for <u><?php echo e($user->name); ?></u> <?php endif; ?></h3>
        <div>
            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="clearFilterAndState"> <b><i
                        class=" icon-reload-alt"></i></b> Reset All Filters</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="deliveryCollectionLogDatatable" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-left">Delivery Guy Name</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th class="text-left">Message</th>
                            <th>Date</th>
                            <th>Collected By</th>
                            <th class="text-center"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="cashCollectionDetails" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Cash Collection Details
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td id="modalName"></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td id="modalEmail"></td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td id="modalPhone"></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td id="modalAmount"></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td id="modalDate"></td>
                        </tr>
                        <tr>
                            <td>Message</td>
                            <td id="modalMessage"></td>
                        </tr>
                        <tr>
                            <td>Collected By</td>
                            <td id="modalCollectedBy"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('body').tooltip({selector: '[data-popup="tooltip"]'});
         var datatable = $('#deliveryCollectionLogDatatable').DataTable({
            searchDelay: 1000,
            processing: true,
            serverSide: true,
            stateSave: true,
            lengthMenu: [ 10, 25, 50, 100, 200, 500 ],
            order: [[ 0, "desc" ]],
            ajax:'<?php echo e(route('admin.deliveryCollectionLogDatatable')); ?><?php if($user_id != null): ?>?user_id=<?php echo e($user_id); ?><?php endif; ?>',
            columns: [
                {data: 'id', visible: false, searchable: false},
                {data: 'name', sortable: false,  searchable: true, name: "delivery_collection.user.name"},
                {data: 'phone', sortable: false, searchable: true, name: "delivery_collection.user.phone"},
                {data: 'amount',  sortable: true, searchable: true},
                {data: 'message',  sortable: false, searchable: true},
                {data: 'created_at',  sortable: true, searchable: true},
                {data: 'collected_by',  sortable: false, searchable: true, name: "user.name"},
                {data: 'action', sortable: true,  searchable: false},
            ],
            colReorder: false,
            drawCallback: function( settings ) {
                $('select').select2({
                   minimumResultsForSearch: Infinity,
                   width: 'auto'
                });
                var elems = Array.prototype.slice.call(document.querySelectorAll('.action-switch'));
                elems.forEach(function(html) {
                    var switchery = new Switchery(html, { color: '#02b875' });
                });
            },
            scrollX: true,
            scrollCollapse: true,
            dom: '<"custom-processing-banner"r>flBtip',
            language: {
                search: '_INPUT_',
                searchPlaceholder: 'Search with anything...',
                sEmptyTable: "No data found",
                lengthMenu: '_MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                processing: '<i class="icon-spinner2 spinner position-left mr-1"></i>Please wait...'
            },
           buttons: {
                   dom: {
                       button: {
                           className: 'btn btn-default'
                       }
                   },
                   buttons: [
                       {extend: 'csv', filename: 'cash-collection-logs-'+ new Date().toISOString().slice(0,10), text: 'Export as CSV'},
                   ]
               }
        });
    
         $('#clearFilterAndState').click(function(event) {
            if (datatable) {
                datatable.state.clear();
                window.location.reload();
            }
         });

         $('.custom_amount').numeric({ allowThouSep:false, maxDecimalPlaces: 2, allowMinus: false });

         $('body').on("click", ".viewDetails", function(e) {
            var name = $(this).attr("data-name");
            var email = $(this).attr("data-email");
            var phone = $(this).attr("data-phone");
            var message = $(this).attr("data-message");
            var date = $(this).attr("data-date");
            var amount = $(this).attr("data-amount");
            var collectedBy = $(this).attr("data-collected-by");

            $('#modalName').html(name);
            $('#modalEmail').html(email);
            $('#modalPhone').html(phone);
            $('#modalAmount').html(amount);
            $('#modalDate').html(date);
            $('#modalMessage').html(message);
            $('#modalCollectedBy').html(collectedBy);

            $('#cashCollectionDetails').modal('show');
        })

        $('#cashCollectionDetails').on('hidden.bs.modal', function () {
            $('#modalName').html('');
            $('#modalEmail').html('');
            $('#modalPhone').html('');
            $('#modalAmount').html('');
            $('#modalDate').html('');
            $('#modalMessage').html('');
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/deliveryCollectionLogs.blade.php ENDPATH**/ ?>