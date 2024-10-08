
<?php $__env->startSection("title"); ?> Users - Dashboard
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
        <h3><strong>All Users </strong></h3>
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_users_edit')): ?>
            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewUser"
                data-toggle="modal" data-target="#addNewUserModal">
                <b><i class="icon-plus2"></i></b>
                Add New User
            </button>
            <?php endif; ?>
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
            <a href="<?php echo e(route('admin.rolesManagement')); ?>" class="btn btn-secondary btn-labeled btn-labeled-left mr-2">
                <b><i class="icon-collaboration"></i></b>
                Manage Roles and Permissions
            </a>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="clearFilterAndState"> <b><i
                        class=" icon-reload-alt"></i></b> Reset All Filters</button>
        </div>
    </div>

    <?php if(Request::is('callandorder/users')): ?>
    <?php if(config('setting.allowStoreOwnersPlaceLoginOrders') == "true"): ?>
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
    <?php endif; ?>
    <?php else: ?>
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
    <?php endif; ?>

</div>
<div id="addNewUserModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold">Add New User</span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route("admin.saveNewUser")); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">First Name:</label>
                        <div class="col-lg-9">
                            <input type="text" name="first_name" class="form-control form-control-lg"
                                placeholder="Enter First Name" required autocomplete="new-name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Last Name:</label>
                        <div class="col-lg-9">
                            <input type="text" name="last_name" class="form-control form-control-lg"
                                placeholder="Enter Last Name" required autocomplete="new-name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Email:</label>
                        <div class="col-lg-9">
                            <input type="text" name="email" class="form-control form-control-lg"
                                placeholder="Enter Email Address" required autocomplete="new-email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Phone:</label>
                        <div class="col-lg-9">
                            <input type="text" name="phone" class="form-control form-control-lg"
                                placeholder="Enter Phone Number" required autocomplete="new-phone">
                        </div>
                    </div>
                    <div class="form-group row form-group-feedback form-group-feedback-right">
                        <label class="col-lg-3 col-form-label">Password:</label>
                        <div class="col-lg-9">
                            <input name="password" type="password" class="form-control form-control-lg"
                                placeholder="Enter Password (min 6 characters)" required autocomplete="new-password"
                                id="newUserPassword">
                        </div>
                        <div class="form-control-feedback form-control-feedback-lg">
                            <span id="showPassword"><i class="icon-unlocked2"></i> Show</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Role:</label>
                        <div class="col-lg-9">
                            <select name="role" class="form-control select" data-fouc>
                                <?php $__currentLoopData = $roles->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->name); ?>" class="text-capitalize"><?php echo e($role->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div id="deliveryGuyDetails" class="hidden">
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            <i class="icon-truck mr-2"></i> Delivery Guy Details
                        </legend>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Name or Nick Name:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="delivery_name"
                                    placeholder="Enter Name or Nickname of Delivery Guy" autocomplete="new-name">
                                <span class="help-text text-muted">This name will be displayed to the
                                    user/customers</span>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Age</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="delivery_age"
                                    placeholder="Enter Delivery Guy's Age">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Delivery Guy's Photo:</label>
                            <div class="col-lg-9">
                                <input type="file" class="form-control-uniform" name="delivery_photo" data-fouc>
                                <span class="help-text text-muted">Image size 250x250</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Description</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="delivery_description"
                                    placeholder="Enter Short Description about this Delivery Guy">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Vehicle Number</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="delivery_vehicle_number"
                                    placeholder="Enter Delivery Guy's Vehicle Number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Commission Rate %</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg commission_rate"
                                    name="delivery_commission_rate"
                                    placeholder="Commission Rate % (By detault, it's set to 5%)" value="5"
                                    required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Cash Limit
                                (<?php echo e(config('setting.currencyFormat')); ?>)</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg cash_limit" name="cash_limit"
                                    value="0.00" />
                                <p>Enter an amount after which you don't want delivery guy to receive any orders.
                                    <strong><mark>Zero(0) means no limit.</mark></strong></p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Tip Commission Rate %</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg tip-commission_rate"
                                    name="tip_commission_rate"
                                    placeholder="Commission Rate % (By detault, it's set to 5%)" value="100"
                                    required="required">
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            SAVE
                            <i class="icon-database-insert ml-1"></i></button>
                    </div>
                    <?php echo csrf_field(); ?>
                </form>
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
            <?php if(\Nwidart\Modules\Facades\Module::find('CallAndOrder') && \Nwidart\Modules\Facades\Module::find('CallAndOrder')->isEnabled() && Request::is('callandorder/users')): ?>
            ajax: '<?php echo e(route('cao.usersDatatable')); ?>',
            <?php else: ?>
            ajax: '<?php echo e(route('admin.usersDatatable')); ?>',
            <?php endif; ?>
            columns: [
                {data: 'id', visible: false, searchable: false},
                {data: 'name'},
                {data: 'email'},
                {data: 'phone'},
                {data: 'role', sortable: false, name: 'roles.name'},
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/users.blade.php ENDPATH**/ ?>