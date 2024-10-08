
<?php $__env->startSection("title"); ?> Customer - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    #showPassword {
        cursor: pointer;
        padding: 5px;
        border: 1px solid #E0E0E0;
        border-radius: 0.275rem;
        color: #9E9E9E;
    }

    #showPassword:hover {
        color: #616161;
    }
</style>


<div class="content mt-3">
    <div class="d-flex justify-content-between my-2">
        <h3><strong>Customers</strong></h3>
        <div>

            <?php if(\Nwidart\Modules\Facades\Module::find('CallAndOrder') &&
            \Nwidart\Modules\Facades\Module::find('CallAndOrder')->isEnabled()): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("login_as_customer")): ?>
            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="manualOrderForGuest">
                <b><i class="icon-clipboard3"></i></b>
                Order for Guest
            </button>
            <?php endif; ?>
            <?php endif; ?>


            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="clearFilterAndState"> <b><i
                        class=" icon-reload-alt"></i></b> Reset All Filters</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="usersDatatable" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th><?php echo e(config('setting.walletName')); ?></th>
                            <th>Created Date</th>
                            <th class="text-center"><i class="
                                    icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.form-control-uniform').uniform();

        $("#showPassword").click(function (e) { 
            $("#newUserPassword").attr("type", "text");
        });
    
        $('.select').select2({
            minimumResultsForSearch: Infinity,
            placeholder: 'Select Role/s (Old roles will be revoked and these roles will be applied)',
        });

         $("[name='role']").change(function(event) {
            if ($(this).val() == "Delivery Guy") {
                $('#deliveryGuyDetails').removeClass('hidden');
                $("[name='delivery_name']").attr('required', 'required');
            }
            else {
                $('#deliveryGuyDetails').addClass('hidden');
                $("[name='delivery_name']").removeAttr('required')
            }
        });
        
        $('.commission_rate').numeric({ allowThouSep:false, maxDecimalPlaces: 2, max: 100, allowMinus: false });
        $('.cash_limit').numeric({allowThouSep:false, maxDecimalPlaces: 2, allowMinus: false });

        $('body').tooltip({selector: '[data-popup="tooltip"]'});
         var datatable = $('#usersDatatable').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            lengthMenu: [ 10, 25, 50, 100, 200, 500 ],
            order: [[ 0, "desc" ]],
            ajax: '<?php echo e(route('admin.customerDatatable')); ?>',
            columns: [
                {data: 'id', visible: false, searchable: false},
                {data: 'name'},
                {data: 'email'},
                {data: 'phone'},
                {data: 'role', sortable: false, searchable: false},
                {data: 'wallet', sortable: false, searchable: false,},
                {data: 'created_at'},
                {data: 'action', sortable: false, searchable: false},
            ],
            colReorder: true,
            drawCallback: function( settings ) {
                $('select').select2({
                   minimumResultsForSearch: Infinity,
                   width: 'auto'
                });
            },
            scrollX: true,
            scrollCollapse: true,
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
            dom: '<"custom-processing-banner"r>flBtip',
            <?php else: ?>
            dom: '<"custom-processing-banner"r>fltip',
            <?php endif; ?>
            language: {
                search: '_INPUT_',
                searchPlaceholder: 'Search with anything...',
                lengthMenu: '_MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                processing: '<i class="icon-spinner10 spinner position-left mr-1"></i>Waiting for server response...'
            },
            
           buttons: {
                   dom: {
                       button: {
                           className: 'btn btn-default'
                       }
                   },
                   buttons: [
                       {extend: 'csv', filename: 'users-'+ new Date().toISOString().slice(0,10), text: 'Export as CSV'},
                   ]
               }
        });

         $('#clearFilterAndState').click(function(event) {
            if (datatable) {
                datatable.state.clear();
                window.location.reload();
            }
         });
    });
</script>

<?php if(\Nwidart\Modules\Facades\Module::find('CallAndOrder') &&
\Nwidart\Modules\Facades\Module::find('CallAndOrder')->isEnabled()): ?>
<?php echo $__env->make('callandorder::scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/manageCustomers.blade.php ENDPATH**/ ?>