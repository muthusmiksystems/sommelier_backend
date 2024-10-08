
<?php $__env->startSection("title"); ?> Roles and Permission Management - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .assigning-checkboxes label {
    margin-right: 10px;
    background-color: rgba(250, 250, 250, 0.3);
    border-radius: 8px;
    margin-bottom: 1.2rem;
    }
    .assigning-checkboxes label:hover {
        cursor: pointer;
    }
    .assigning-checkboxes label span {
    text-align: center;
    display: block;
    padding: 4px 7.5px;
    border: 1px solid #e0e0e0;
    border-radius: 25px;
    }
    .assigning-checkboxes label input {
    position: absolute;
    top: -20px;
    display: none;
    }
    .assigning-checkboxes input:checked + span {
    background-color: #2ebf91;
    padding: 8px 15px;
    color: #fff;
    border: 1px solid #e0e0e0;
    }
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <a href="<?php echo e(route('admin.rolesManagement')); ?>" class="btn btn-md btn-default mr-2" data-popup="tooltip" title="Go back" data-placement="right"><i class="icon-circle-left2"></i></a>
                <span class="font-weight-bold mr-2">Editing</span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($role->name); ?></span>
            </h4>
        </div>
    </div>
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('admin.updateRoleAndPermissions')); ?>" method="POST" enctype="multipart/form-data">
                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                        <i class="icon-collaboration mr-2"></i> Role Details
                    </legend>
                    <input type="hidden" name="id" value="<?php echo e($role->id); ?>">

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Role Name:</label>
                        <div class="col-lg-9">
                            <input value="<?php echo e($role->name); ?>" type="text" class="form-control form-control-lg"
                                name="name" placeholder="Enter the role name. Example: Manager" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-9 col-form-label"><strong>Permissions:</strong></label>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary btn-labeled btn-labeled-left btn-sm" id="checkAll" data-popup="tooltip" title="Double Click to Check all Permissions" data-placement="top">
                            <b><i class="icon-check ml-1"></i></b>
                                Check All
                            </button>
                            <button type="button" class="btn btn-primary btn-labeled btn-labeled-left btn-sm" id="unCheckAll" data-popup="tooltip" title="Double Click to Un-check all Permissions" data-placement="top">
                            <b><i class="icon-cross3 ml-1"></i></b>
                                Un-check All
                            </button>
                        </div>
                        <div class="assigning-checkboxes">
                            <div class="col-lg-12">
                                <div class="form-group form-group-feedback form-group-feedback-right search-box">
                                    <input type="text" class="form-control form-control-lg search-input"
                                        placeholder="Filter with permission name..." style="border: 1px solid #ccc; box-shadow: none; height: 2.5rem;">
                                    <div class="form-control-feedback form-control-feedback-lg">
                                        <i class="icon-search4"></i>
                                    </div>
                                </div>

                                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="mr-2 mb-2">
                                    <input type="checkbox" name="permission[]" data-name="<?php echo e($permission->readable_name); ?>" value="<?php echo e($permission->name); ?>" <?php if(in_array($permission->id, $rolePermissionIds)): ?> checked="checked" <?php endif; ?>/>
                                    <span><?php echo e($permission->readable_name); ?></span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    <?php echo csrf_field(); ?>
                    <div class="text-right mt-5">
                        <button type="submit" class="btn btn-primary btn-labeled btn-labeled-left btn-lg btnUpdateUser">
                        <b><i class="icon-database-insert ml-1"></i></b>
                        Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if(count($users) > 0): ?>
<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <legend class="font-weight-semibold text-uppercase font-size-sm">
                    <i class="icon-users2 mr-2"></i> Users with the role of <b><?php echo e($role->name); ?></b>
                </legend>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4">
                        <div class="col-md-12 new-users p-2">
                        <a href="<?php echo e(route('admin.get.editUser', $user->id)); ?>">
                            <div class="float-right">
                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge badge-flat border-grey-800 text-default text-capitalize">
                                    <?php echo e($role->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="d-flex justify-content-start">
                                <div class="first-letter-icon custom-bg-<?php echo e(rand(1,8)); ?>">
                                   <?php echo e(returnAcronym($user->name)); ?>

                                </div>
                                <div class="ml-2">
                                    <span><?php echo e($user->name); ?></span><br>
                                    <span><?php echo e($user->email); ?></span><br>
                                    <span><?php echo e($user->created_at->diffForHumans()); ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <p class="mb-0">No users found with the role of <b><?php echo e($role->name); ?></b></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="mb-5"></div>
<script>
    $(function() {
        $('.assigning-checkboxes label').each(function(){
            $(this).attr('data-name', $(this).text().toLowerCase());
        });

        $('.search-input').on('keyup', function(){
        var searchTerm = $(this).val().toLowerCase();
            $('.assigning-checkboxes label').each(function(){
                if ($(this).filter('[data-name *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        $('#checkAll').dblclick(function(event) {
            $("input:checkbox").prop("checked", true);
        });
        $('#unCheckAll').dblclick(function(event) {
            $("input:checkbox").prop("checked", false);
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/editRoleAndPermissions.blade.php ENDPATH**/ ?>