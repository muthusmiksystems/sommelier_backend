
<?php $__env->startSection("title"); ?> Edit User - Dashboard
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
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2">Editing</span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($user->name); ?></span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>


<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body" style="min-height: 60vh;">
                <form action="<?php echo e(route('admin.updateUser')); ?>" method="POST" enctype="multipart/form-data"
                    id="storeMainForm" style="min-height: 60vh;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="window_redirect_hash" value="">
                    <input type="hidden" name="id" value="<?php echo e($user->id); ?>">

                    <div class="d-lg-flex justify-content-lg-left">
                        <ul class="nav nav-pills nav-pills-main flex-column mr-lg-3 wmin-lg-250 mb-lg-0">
                            <li class="nav-item">
                                <a href="#userDetails" class="nav-link active" data-toggle="tab">
                                    <i class="icon-store2 mr-2"></i>
                                    User Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#userRole" class="nav-link" data-toggle="tab">
                                    <i class="icon-tree7 mr-2"></i>
                                    User Role <?php if(isset($navZones) && count($navZones) > 0): ?> & Zone <?php endif; ?>
                                </a>
                            </li>
                            <?php if($user->hasRole("Delivery Guy")): ?>
                            <li class="nav-item">
                                <a href="#deliveryGuyDetails" class="nav-link" data-toggle="tab">
                                    <i class="icon-truck mr-2"></i>
                                    Delivery Guy Details
                                </a>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="javascript:void(0)" class="nav-link" data-toggle="tab" id="walletBalance">
                                    <i class="icon-piggy-bank mr-2"></i>
                                    Wallet Balance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#walletTransactions" class="nav-link" data-toggle="tab">
                                    <i class="icon-transmission mr-2"></i>
                                    Wallet Transactions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#userOrders" class="nav-link" data-toggle="tab">
                                    <i class="icon-basket mr-2"></i>
                                    Orders
                                </a>
                            </li>
                            <?php if($user->hasRole("Delivery Guy")): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('admin.viewDeliveryReviews', $user->id)); ?>" class="nav-link">
                                    <i class="icon-stars mr-2"></i>
                                    Rating & Reviews <span
                                        class="ml-1 badge badge-flat text-white <?php echo e(ratingColorClass($rating)); ?>"><?php echo e($rating); ?>

                                        <i class="icon-star-full2 text-white" style="font-size: 0.6rem;"></i></span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="#userAddresses" class="nav-link" data-toggle="tab">
                                    <i class="icon-home7 mr-2"></i>
                                    User Addresses
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" style="width: 100%; padding: 0 25px;">

                            <div class="tab-pane fade show active" id="userDetails">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    User Details
                                </legend>
                                
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">First Name:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="first_name"
                                            value="<?php echo e($user->first_name); ?>" placeholder="Enter First Name" required
                                            autocomplete="new-name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Last Name:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="last_name"
                                            value="<?php echo e($user->last_name); ?>" placeholder="Enter Last Name" required
                                            autocomplete="new-name">
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Email:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="email"
                                            value="<?php echo e($user->email); ?>" placeholder="Emter Email Address" required
                                            autocomplete="new-email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Phone:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="phone"
                                            value="<?php echo e($user->phone); ?>" placeholder="Enter Phone Number" required
                                            autocomplete="new-phone">
                                    </div>
                                </div>
                                <div class="form-group row form-group-feedback form-group-feedback-right">
                                    <label class="col-lg-3 col-form-label">Password:</label>
                                    <div class="col-lg-9">
                                        <input id="passwordInput" type="password" class="form-control form-control-lg"
                                            name="password" placeholder="Enter Password (min 6 characters)"
                                            autocomplete="new-password">
                                    </div>
                                    <div class="form-control-feedback form-control-feedback-lg">
                                        <span id="showPassword"><i class="icon-unlocked2"></i> Show</span>
                                    </div>
                                </div>

                                <div class="form-group row">
                            <label class="col-lg-3 col-form-label">DOB:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="dob"
                                    value="<?php echo e(date('d-m-Y', strtotime($user->dob))); ?>" placeholder="DD-MM-YYYY" required
                                    autocomplete="new-name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Licence No:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="licence_no"
                                    value="<?php echo e($user->licence_no); ?>" placeholder="Enter Licence No"
                                    autocomplete="new-name">
                            </div>
                        </div>
                        <div class="form-group row">
                                <label class="col-lg-3 col-form-label">State: </label>
                                <div class="col-lg-9">
                                    <select class="form-control form-control-lg" name="state">
                                        <option value="">Select State</option>
                                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($state->id); ?>" <?php echo e(($user->state_id == $state->id) ? "selected='selected'" : ""); ?>><?php echo e($state->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <?php if(!empty($user->licence_photo)): ?>
                                <div class="col-lg-9 offset-lg-3">
                                    <img src="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->licence_photo); ?>" alt="delivery-photo" class="img-fluid mb-2" style="width: 90px; border-radius: 50%">
                                </div>
                                <?php endif; ?>
                                <label class="col-lg-3 col-form-label">Licence:</label>
                                <div class="col-lg-9">
                                    <input type="file" class="form-control-uniform" name="licence_photo" data-fouc>
                                    <span class="help-text text-muted">Image size 250x250</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Restaurant:</label>
                                    <div class="col-lg-9">
                                    <div class="assigning-checkboxes mt-3">
                                    <?php $__currentLoopData = $userRestaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <label>
        <input type="radio" data-slug="<?php echo e($ar->slug); ?>" name="user_restaurants[]" value="<?php echo e($ar->id); ?>" onchange="updateSlug()" <?php if($key === 0 ): ?> checked="checked" <?php endif; ?>/>
        <span><?php echo e($ar->name); ?></span>
    </label>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



<p id="selectedSlug" onclick="copyURL()">URL:<strong>https://<?php echo e(request()->getHttpHost()); ?>/stores/<span id="storeURL"><?php echo e($userRestaurants[0]->slug ?? ''); ?></span></strong>


                        </div>
                                    </div>
                                </div>

                                <div class="text-left">
                                    <div class="btn-group btn-group-justified" style="width: 150px">
                                        <?php if($user->is_active): ?>
                                        <a class="btn btn-danger" href="<?php echo e(route('admin.banUser', $user->id)); ?>"
                                            data-popup="tooltip"
                                            title="User will not be able to place orders if banned">
                                            Ban User
                                        </a>
                                        <?php else: ?>
                                        <a class="btn btn-success" href="<?php echo e(route('admin.banUser', $user->id)); ?>"
                                            data-popup="tooltip"
                                            title="Currently, <?php echo e($user->name); ?> is banned from placing any orders">
                                            Reactivate User
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <p class="mt-2">User IP used during regisration: <?php if($user->user_ip != null): ?>
                                    <b><?php echo e($user->user_ip); ?></b> <?php else: ?> IP Not found <?php endif; ?> </p>

                            </div>

                            <div class="tab-pane fade" id="userRole">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    Role Management
                                </legend>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Current Role:</label>
                                    <div class="col-lg-9 d-flex align-items-center">
                                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge badge-success font-size-lg">
                                            <?php echo e($role->name); ?>

                                        </span> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Assign Role:</label>
                                    <div class="col-lg-9">
                                        <?php if($user->id == "1"): ?>
                                        <span>Super Admin Role cannot be changed</span>
                                        <?php else: ?>
                                        <select class="form-control select" data-fouc name="roles">
                                            <option></option>
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($key != 1): ?>
                                            <option value="<?php echo e($role->name); ?>" class="text-capitalize"><?php echo e($role->name); ?>

                                            </option>
                                            <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if(count($zones) > 0): ?>
                                <?php
                                $protectedRoles = ['Admin', 'Store Owner', 'Customer'];
                                $hidden = false;
                                if (in_array($user->roles()->pluck('name')[0], $protectedRoles)) {
                                $hidden = true;
                                }
                                ?>
                                <div id="userAreaSelection" class="<?php if($hidden): ?> hidden <?php endif; ?>">
                                    <hr>
                                    <?php if($user->zone_id != null): ?>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Current Zone:</label>
                                        <div class="col-lg-9 d-flex align-items-center">
                                            <span class="badge badge-success font-size-lg">
                                                <?php echo e($user->zone->name); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-danger"><strong><?php echo e($user->name); ?></strong> is not assigned to any
                                        zone.</p>
                                    <?php endif; ?>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Assign Zone:</label>
                                        <div class="col-lg-9">
                                            <select class="form-control select-zone" name="zone_id">
                                                <option></option>
                                                <?php $__currentLoopData = $zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($zone->id); ?>" <?php if($user->zone_id == $zone->id): ?>
                                                    selected <?php endif; ?>><?php echo e($zone->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                            </div>
                            <script>
                                $(".select-zone").select2({
                                    placeholder: "Select a zone",
                                    allowClear: true
                                });
                                $('select[name="roles"]').on('change',(event) => {
                                    var protectedRoles = ['Admin', 'Delivery Guy', 'Store Owner', 'Customer'] 
                                    if ($.inArray(event.target.value, protectedRoles) !== -1){
                                        $('#userAreaSelection').addClass('hidden');
                                    } else {
                                        $('#userAreaSelection').removeClass('hidden');
                                    }
                                });
                            </script>

                            <?php if($user->hasRole("Delivery Guy")): ?>
                            <div class="tab-pane fade" id="deliveryGuyDetails">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    Delivery Guy Details
                                </legend>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Stripe Account Id:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="stripe_account_id"
                                            value="<?php echo e(!empty($user->stripe_account_id) ? $user->stripe_account_id : ""); ?>" placeholder="Enter Stripe Account Id" autocomplete="new-name">
                                    </div>
                                    
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Name or Nick Name:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="delivery_name"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->name) ? $user->delivery_guy_detail->name : ""); ?>"
                                            placeholder="Enter Name or Nickname of Delivery Guy" required
                                            autocomplete="new-name">
                                        <span class="help-text text-muted">This name will be displayed to the
                                            user/customers</span>
                                    </div>

                                </div>
                                <!--<div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Age</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="delivery_age"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->age) ? $user->delivery_guy_detail->age : ""); ?>"
                                            placeholder="Enter Delivery Guy's Age">
                                    </div>
                                </div> -->
                                <div class="form-group row">
                                    <?php if(!empty($user->delivery_guy_detail->photo)): ?>
                                    <div class="col-lg-9 offset-lg-3">
                                        <img src="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->photo); ?>"
                                            alt="delivery-photo" class="img-fluid mb-2"
                                            style="width: 90px; border-radius: 50%">
                                    </div>
                                    <?php endif; ?>
                                    <label class="col-lg-3 col-form-label">Delivery Guy's Photo:</label>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-control-uniform" name="delivery_photo" data-fouc>
                                        <span class="help-text text-muted">Image size 250x250</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Description</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg"
                                            name="delivery_description"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->description) ? $user->delivery_guy_detail->description : ""); ?>"
                                            placeholder="Enter Short Description about this Delivery Guy">
                                    </div>
                                </div>
                               <!-- <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Vehicle Number</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg"
                                            name="delivery_vehicle_number"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->vehicle_number) ? $user->delivery_guy_detail->vehicle_number : ""); ?>"
                                            placeholder="Enter Delivery Guy's Vehicle Number">
                                    </div>
                                </div> -->

                                <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Vehicle Type</label>
                                <div class="col-lg-9">
                                    <select class="form-control form-control-lg" name="vehicle_type">
                                        <option value="">Select Vehicle Type</option>
                                        <?php $__currentLoopData = $vehicle_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vehicle_type->id); ?>" <?php echo e(($user->delivery_guy_detail->vehicle_type == $vehicle_type->id) ? "selected='selected'" : ""); ?>><?php echo e($vehicle_type->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Registration No:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="registration_no"
                                        value="<?php echo e($user->delivery_guy_detail->registration_no); ?>" placeholder="Enter Registartion No" required
                                        autocomplete="new-name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">ABN No:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="abn_no"
                                        value="<?php echo e($user->delivery_guy_detail->abn_no); ?>" placeholder="Enter ABN No" required
                                        autocomplete="new-name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Bank Name:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="bank_name"
                                        value="<?php echo e($user->delivery_guy_detail->bank_name); ?>" placeholder="Enter Bank Name" required
                                        autocomplete="new-name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">BSB:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="bsb"
                                        value="<?php echo e($user->delivery_guy_detail->bsb); ?>" placeholder="Enter BSB" required
                                        autocomplete="new-name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Account Number:</label>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control form-control-lg" name="account_number"
                                        value="<?php echo e($user->delivery_guy_detail->account_number); ?>" placeholder="Enter Account Number" required
                                        autocomplete="new-name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Account Name:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="account_name"
                                        value="<?php echo e($user->delivery_guy_detail->account_name); ?>" placeholder="Enter Account Name" required
                                        autocomplete="new-name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <?php if(!empty($user->delivery_guy_detail->vehicle_registration)): ?>
                                <div class="col-lg-9 offset-lg-3">
                                    <a href="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->vehicle_registration); ?>" target="_blank"><img src="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->vehicle_registration); ?>" alt="delivery-photo" class="img-fluid mb-2" style="width: 90px; border-radius: 50%"></a>
                                </div>
                                <?php endif; ?>
                                <label class="col-lg-3 col-form-label">Vehicle Registarion:</label>
                                <div class="col-lg-9">
                                    <input type="file" class="form-control-uniform" name="vehicle_registration" data-fouc>
                                    <span class="help-text text-muted">Image size 250x250</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <?php if(!empty($user->delivery_guy_detail->vehicle_insurance_policy)): ?>
                                <div class="col-lg-9 offset-lg-3">
                                <a href="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->vehicle_insurance_policy); ?>" target="_blank"><img src="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->vehicle_insurance_policy); ?>" alt="delivery-photo" class="img-fluid mb-2" style="width: 90px; border-radius: 50%"></a>
                                </div>
                                <?php endif; ?>
                                <label class="col-lg-3 col-form-label">Vehicle Insurance Policy:</label>
                                <div class="col-lg-9">
                                    <input type="file" class="form-control-uniform" name="vehicle_insurance_policy" data-fouc>
                                    <span class="help-text text-muted">Image size 250x250</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <?php if(!empty($user->delivery_guy_detail->certificate)): ?>
                                <div class="col-lg-9 offset-lg-3">
                                <a href="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->certificate); ?>" target="_blank"><img src="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->certificate); ?>" alt="delivery-photo" class="img-fluid mb-2" style="width: 90px; border-radius: 50%"></a>
                                </div>
                                <?php endif; ?>
                                <label class="col-lg-3 col-form-label">RSA Certificate:</label>
                                <div class="col-lg-9">
                                    <input type="file" class="form-control-uniform" name="certificate" data-fouc>
                                    <span class="help-text text-muted">Image size 250x250</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <?php if(!empty($user->delivery_guy_detail->police_clearence_certificate)): ?>
                                <div class="col-lg-9 offset-lg-3">
                                <a href="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->police_clearence_certificate); ?>" target="_blank"><img src="<?php echo e(substr(url('/'), 0, strrpos(url('/'), '/'))); ?>/assets/img/delivery/<?php echo e($user->delivery_guy_detail->police_clearence_certificate); ?>" alt="delivery-photo" class="img-fluid mb-2" style="width: 90px; border-radius: 50%"></a>
                                </div>
                                <?php endif; ?>
                                <label class="col-lg-3 col-form-label">Police Clearence Certificate:</label>
                                <div class="col-lg-9">
                                    <input type="file" class="form-control-uniform" name="police_clearence_certificate" data-fouc>
                                    <span class="help-text text-muted">Image size 250x250</span>
                                </div>
                            </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">SMS Notification for New Orders?</label>
                                    <div class="col-lg-9">
                                        <div class="checkbox checkbox-switchery mt-2">
                                            <label>
                                                <input value="true" type="checkbox" class="switchery-primary"
                                                    <?php if(!empty($user->delivery_guy_detail->is_notifiable) &&
                                                $user->delivery_guy_detail->is_notifiable): ?> checked="checked" <?php endif; ?>
                                                name="is_notifiable">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Commission Rate %</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg commission_rate"
                                            name="delivery_commission_rate" placeholder="Commission Rate %"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->commission_rate) ? $user->delivery_guy_detail->commission_rate : "0"); ?>"
                                            required="required">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Cash Limit
                                        (<?php echo e(config('setting.currencyFormat')); ?>)</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg cash_limit"
                                            name="cash_limit"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->cash_limit) ? $user->delivery_guy_detail->cash_limit : "0"); ?>" />
                                        <p>Enter an amount after which you don't want delivery guy to receive any
                                            orders. <strong><mark>Zero(0) means no limit.</mark></strong></p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Max Orders in Queue</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg max_orders"
                                            name="max_accept_delivery_limit" placeholder="Max Orders in Queue"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->max_accept_delivery_limit) ? $user->delivery_guy_detail->max_accept_delivery_limit : "100"); ?>"
                                            required="required">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Tip Commission Rate %</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg commission_rate"
                                            name="tip_commission_rate" placeholder="Commission Rate %"
                                            value="<?php echo e(!empty($user->delivery_guy_detail->tip_commission_rate) ? $user->delivery_guy_detail->tip_commission_rate : "100"); ?>"
                                            required="required">
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="tab-pane fade" id="walletTransactions">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    Wallet Transactions
                                </legend>
                                <?php if(count($user->transactions) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Type
                                                </th>
                                                <th width="20%">
                                                    Amount
                                                </th>
                                                <th>
                                                    Description
                                                </th>
                                                <th>
                                                    Date
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $user->transactions->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <?php if($transaction->type === "deposit"): ?>
                                                    <span
                                                        class="badge badge-flat border-grey-800 text-success text-capitalize"><?php echo e($transaction->type); ?></span>
                                                    <?php else: ?>
                                                    <span
                                                        class="badge badge-flat border-grey-800 text-danger text-capitalize"><?php echo e($transaction->type); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php echo e(config('setting.currencyFormat')); ?>

                                                    <!-- <?php echo e(number_format($transaction->amount / 100, 2,'.', '')); ?> -->
                                                      <?php echo e($transaction->amount); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($transaction->meta["description"]); ?>

                                                </td>
                                                <td class="small">
                                                    <?php echo e($transaction->created_at->format('Y-m-d  - h:i A')); ?>

                                                    (<?php echo e($transaction->created_at->diffForHumans()); ?>)
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p class="text-muted text-center mb-0">No transactions has been made from
                                    <?php echo e(config('setting.walletName')); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="tab-pane fade" id="userOrders">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    Orders
                                </legend>
                                <?php if(count($orders) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Order ID
                                                </th>
                                                <th width="20%">
                                                    Order Status
                                                </th>
                                                <th>
                                                    Order Date
                                                </th>
                                                <th>
                                                    Order Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $orders->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <a
                                                        href="<?php echo e(route('admin.viewOrder', $order->unique_order_id )); ?>"><?php echo e($order->unique_order_id); ?></a>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-flat border-grey-800 text-primary <?php if($order->orderstatus_id == 6): ?> text-danger <?php endif; ?> text-capitalize">
                                                        <?php echo e(getOrderStatusName($order->orderstatus_id)); ?>

                                                    </span>
                                                </td>

                                                <td class="small">
                                                    <?php echo e($order->created_at->format('Y-m-d  - h:i A')); ?>

                                                    (<?php echo e($order->created_at->diffForHumans()); ?>)
                                                </td>

                                                <td class="text-right">
                                                    <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->total); ?>

                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p class="text-muted text-center mb-0">No Orders Placed From This User</p>
                                <?php endif; ?>
                            </div>

                            <div class="tab-pane fade" id="userAddresses">
                                <?php if(count($user->addresses) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Address
                                                </th>
                                                <th width="20%">
                                                    House
                                                </th>
                                                <th>
                                                    Tag
                                                </th>
                                                <th class="text-center"><i class="
                                                    icon-circle-down2"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $user->addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <?php if($user->default_address_id == $address->id): ?>
                                                    <i class="icon-star-full2 text-warning" data-popup="tooltip"
                                                        title="Primary Address" data-placement="left"></i>
                                                    <?php endif; ?>

                                                    <?php if($address->address != null): ?>
                                                    <?php echo e($address->address); ?>

                                                    <?php else: ?>
                                                    --
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($address->house != null): ?>
                                                    <?php echo e($address->house); ?>

                                                    <?php else: ?>
                                                    --
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($address->landmark != null): ?>
                                                    <?php echo e($address->landmark); ?>

                                                    <?php else: ?>
                                                    --
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-left">
                                                        <?php if($user->default_address_id != $address->id): ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger mr-1 deleteAddressBtn"
                                                            data-popup="tooltip" title="Delete Address"
                                                            data-placement="left" data-addressid=<?php echo e($address->id); ?>><i
                                                                class="icon-trash"></i></button>
                                                        <?php endif; ?>
                                                        <a href="https://maps.google.com/?q=<?php echo e($address->latitude); ?>,<?php echo e($address->longitude); ?>"
                                                            target="_blank" class="btn btn-sm btn-secondary w-100"
                                                            data-popup="tooltip" title="Locate on Google Maps"
                                                            data-placement="left">Locate</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p class="text-muted text-center mb-0">No Addresses found</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-5">
                        <button type="submit" class="btn btn-primary btn-labeled btn-labeled-left btn-lg btnUpdateUser">
                            <b><i class="icon-database-insert ml-1"></i></b>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="<?php echo e(route('admin.deleteUserAddress')); ?>" id="deleteAddressForm" method="POST">
    <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">
    <input type="hidden" name="address_id" id="deleteAddressId">
    <input type="hidden" name="window_redirect_hash" value="#userAddresses">
    <?php echo csrf_field(); ?>
</form>

<div class="content" id="walletBalanceBlock" style="margin-bottom: 10rem;">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <legend class="font-weight-semibold h6">
                    <mark><?php echo e(config("setting.walletName")); ?> Balance:
                        <?php echo e(config('setting.currencyFormat')); ?><?php echo e($user->balanceFloat); ?></mark>
                </legend>

                <div class="d-lg-flex justify-content-lg-left">
                    <ul class="nav nav-pills flex-column mr-lg-3 wmin-lg-250 mb-lg-0">
                        <li class="nav-item">
                            <a href="#addWallet" class="nav-link active" data-toggle="tab">
                                Add Money
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#deductWallet" class="nav-link" data-toggle="tab">
                                Deduct Money
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" style="width: 100%; padding: 0 25px;">
                        <div class="tab-pane fade show active" id="addWallet">
                            <form action="<?php echo e(route('admin.addMoneyToWallet')); ?>" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Add
                                        Money:</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control form-control-lg balance"
                                            name="add_amount"
                                            placeholder="Amount in <?php echo e(config('setting.currencyFormat')); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Message:</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control form-control-lg"
                                            name="add_amount_description" placeholder="Short Description or Message"
                                            required>
                                    </div>
                                </div>
                                <?php echo csrf_field(); ?>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-secondary">
                                        Update Balance
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="deductWallet">
                            <form action="<?php echo e(route('admin.substractMoneyFromWallet')); ?>" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Deduct
                                        Money:</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control form-control-lg balance"
                                            name="substract_amount"
                                            placeholder="Amount in <?php echo e(config('setting.currencyFormat')); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Message:</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control form-control-lg"
                                            name="substract_amount_description"
                                            placeholder="Short Description or Message" required>
                                    </div>
                                </div>
                                <?php echo csrf_field(); ?>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-secondary">
                                        Update Balance
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {

        if (Array.prototype.forEach) {
                 var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-primary'));
                 elems.forEach(function(html) {
                     var switchery = new Switchery(html, { color: '#2196F3' });
                 });
             }
             else {
                 var elems = document.querySelectorAll('.switchery-primary');
                 for (var i = 0; i < elems.length; i++) {
                     var switchery = new Switchery(elems[i], { color: '#2196F3' });
                 }
             }
             
        $('.form-control-uniform').uniform();

        $("#showPassword").click(function (e) { 
            $("#passwordInput").attr("type", "text");
        });
        $('.select').select2({
            minimumResultsForSearch: Infinity,
            placeholder: 'Select Role (Old role will be revoked and the new role will be applied)',
        });
        $('.balance').numeric({allowThouSep:false, maxDecimalPlaces: 2 });

        $("#addAmountButton").click(function(event) {
            $('#addAmountButton').hide();
            $('#substractAmountButton').hide();
            $("#addAmountForm").removeClass('hidden');
            $("#substractAmountForm").addClass('hidden');
        });

        $("#substractAmountButton").click(function(event) {
            $('#addAmountButton').hide();
            $('#substractAmountButton').hide();
            $("#addAmountForm").addClass('hidden');
            $("#substractAmountForm").removeClass('hidden');
        });

        $("#viewTransactions").click(function(event) {
            var targetOffset = $('#tansactionsDiv').offset().top - 70;
            $('html, body').animate({scrollTop: targetOffset}, 500);
        });

        $('.commission_rate').numeric({ allowThouSep:false, maxDecimalPlaces: 2, max: 100, allowMinus: false });
        $('.max_orders').numeric({ allowThouSep:false, maxDecimalPlaces: 0, max: 99999, allowMinus: false });
        $('.cash_limit').numeric({allowThouSep:false, maxDecimalPlaces: 2, allowMinus: false });

        /* Navigate with hash */
        var hash = window.location.hash;
        $("[name='window_redirect_hash']").val(hash);
        hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        $('.nav-pills-main a').click(function (e) {
            $(this).tab('show');
            var scrollmem = $('body').scrollTop();
            window.location.hash = this.hash;
            $("[name='window_redirect_hash']").val(this.hash);
            $('html, body').scrollTop(scrollmem);
        });

        $('#walletBalance').click(function(event) {
            var targetOffset = $('#walletBalanceBlock').offset().top - 70;
            $('html, body').animate({scrollTop: targetOffset}, 500);
        });

        $('.btnUpdateUser').click(function () {
            $('input:invalid').each(function () {
                // Find the tab-pane that this element is inside, and get the id
                var $closest = $(this).closest('.tab-pane');
                var id = $closest.attr('id');

                // Find the link that corresponds to the pane and have it show
                $('ul.nav a[href="#' + id + '"]').tab('show');

                // var hash = '#'+id;
                // window.location.hash = hash;
                // console.log("hash: ", hash)
                // $("[name='window_redirect_hash']").val(hash);

                return false;
            });
        });

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

        $('.deleteAddressBtn').click(function (e) { 

            var address_id = $(this).attr('data-addressid');

            $('#deleteAddressId').val(address_id);

            var query = confirm("Confirm delete?");
            if (query == true) {
                $('#deleteAddressForm').submit();
            } else {
                $('#deleteAddressId').val(null);
            }
        });
    });
    function updateSlug() {
    var checkboxes = document.querySelectorAll('input[name="user_restaurants[]"]:checked');
    var selectedSlugs = Array.from(checkboxes).map(function(checkbox) {
        return checkbox.dataset.slug;
    });
    var storeURL = document.getElementById('storeURL');
    if (selectedSlugs.length > 0) {
        storeURL.textContent = selectedSlugs.join('-');
    } else {
        storeURL.textContent = 'No restaurant selected';
    }
}


    // Function to copy the URL to the clipboard
    function copyURL() {
        var storeURL = document.getElementById('storeURL').innerText;
        navigator.clipboard.writeText(storeURL).then(function() {
            // alert('URL copied to clipboard: ' + storeURL);
        }, function(err) {
            // console.error('Could not copy URL: ', err);
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/editUser.blade.php ENDPATH**/ ?>