<style>
    .extra-menu {
        min-width: 500px
    }

    @media (min-width:320px) and (max-width:767px) {
        .extra-menu {
            min-width: auto
        }
    }

    .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0;
    }
</style>
<div class="navbar navbar-expand-md navbar-dark">
    <div class="navbar-brand wmin-0 mr-5">

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("dashboard_view")): ?>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="d-inline-block">
            <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/global_assets/images/dashboard-logo.png"
                alt="Dashboard">
        </a>
        <?php else: ?>
        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
        <?php else: ?>
        <a href="<?php echo e(route('admin.manager')); ?>" class="d-inline-block">
            <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/global_assets/images/dashboard-logo.png"
                alt="Dashboard">
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
        <a href="<?php echo e(route('restaurant.dashboard')); ?>" class="d-inline-block">
            <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/global_assets/images/dashboard-logo.png"
                alt="Dashboard">
        </a>
        <?php endif; ?>
    </div>
    <div class="back_button">
        <a href="<?php echo url()->previous(); ?>" style="color:#ffffff;">Back</a>
    </div>
    <div class="d-md-none">
        <button class="navbar-toggler dropdown-toggle" type="button" data-toggle="collapse"
            data-target="#navbar-mobile">
            <span><?php echo e(Auth::user()->name); ?></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right" id="navbar-mobile">
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
            <a href="#" class="dropdown-item dropdown-toggle" data-toggle="dropdown">
                <span><i class="icon-earth"></i></span>
            </a>
            <div class="dropdown-menu">
                <?php $__currentLoopData = $translationLangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(url('locale', $lang)); ?>"
                    class="dropdown-item <?php if(app()->getLocale() === $lang): ?> active <?php endif; ?>"
                    style="text-transform: uppercase;"> <?php echo e($lang); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <a href="<?php echo e(route('restaurant.zenMode', "true")); ?>" class="dropdown-item"><i
                    class="icon-power3"></i><?php echo e(__('storeDashboard.zenMode')); ?></a>
            <?php endif; ?>
            <a href="<?php echo e(route('logout')); ?>" class="dropdown-item"><i class="icon-switch2"></i>
                <?php echo e(__('storeDashboard.navLogout')); ?></a>
        </div>
    </div>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
            <li class="nav-item dropdown dropdown-user">
                <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
                    <span><i class="icon-earth mx-2"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <?php $__currentLoopData = $translationLangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(url('locale', $lang)); ?>"
                        class="dropdown-item <?php if(app()->getLocale() === $lang): ?> active <?php endif; ?>"
                        style="text-transform: uppercase;"> <?php echo e($lang); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
            <?php if(isset($navZones) && count($navZones) > 0): ?>
            <?php if(!Request::is('admin/delivery-ratings/*') && !Request::is('admin/store-ratings/*')): ?>
            <li class="nav-item nav-zone-selection">
                <select name="nav_select_zone" class="nav-select-zone">
                    <option value="0" <?php if(session('selectedZone')=="0" ): ?> selected <?php endif; ?>> All Zones </option>
                    <?php $__currentLoopData = $navZones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($zone->id); ?>" <?php if(session()->has('selectedZone') &&
                        session('selectedZone') == $zone->id): ?> selected <?php endif; ?>><?php echo e($zone->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </li>
            <script>
                $('.nav-select-zone').select2();
                $('select[name="nav_select_zone"]').on('change',(event) => {
                    let url = "<?php echo e(url('change-zone-scope/')); ?>/"+event.target.value;
                    //delete datatable filters
                    var savedDatatables = findLocalItems("DataTables_");
                    if (savedDatatables.length > 0) {
                        console.log("Cleaning keys");
                        $.each(savedDatatables, function (indexInArray, valueOfElement) { 
                            localStorage.removeItem(valueOfElement.key);
                        });
                    }
                    setTimeout(() => {
                        window.location.href = url;
                    }, 500);
                });
            </script>
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>

            <?php if(Auth::user()): ?>
            <?php if(!Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Store Owner')): ?>
            <?php if(Auth::user()->zone_id != null): ?>
            <button class="manager-nav-role"><?php echo e(Auth::user()->zone->name); ?></button>
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>


            <li class="nav-item dropdown dropdown-user">
                <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
                    <span><?php echo e(Auth::user()->name); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
                    <a href="<?php echo e(route('restaurant.zenMode', "true")); ?>" class="dropdown-item"><i
                            class="icon-power3"></i><?php echo e(__('storeDashboard.zenMode')); ?></a>
                    <?php endif; ?>

                    <a href="<?php echo e(route('logout')); ?>" class="dropdown-item"><i class="icon-switch2"></i>
                        <?php echo e(__('storeDashboard.navLogout')); ?></a>
                </div>
            </li>

            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
            <?php if (is_impersonating()) : ?>
            <li class="nav-item">
                <a class="navbar-nav-link active" href="<?php echo e(route('impersonate.leave')); ?>"><i
                        class="icon-arrow-left15 mr-1"></i>Go back to Admin</a>
            </li>
            <?php endif; ?>
            <?php endif; ?>


        </ul>
    </div>
</div>
<div class="navbar navbar-expand-md navbar-light navbar-sticky">
    <div class="container">
        <div class="text-center d-md-none w-100">
            <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse"
                data-target="#navbar-navigation">
                <i class="icon-unfold mr-2"></i>
                Navigation
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-navigation">
            <ul class="navbar-nav">

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("stores_view")): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("admin.restaurants")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('admin/stores') ? 'active' : ''); ?>">
                        <i class="icon-store2 mr-2"></i>
                        Stores
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['addon_categories_view', 'addons_view', 'menu_categories_view', 'items_view'])): ?>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle <?php echo e(Request::is('admin/items')  || Request::is('admin/addoncategories') || Request::is('admin/addons') || Request::is('admin/itemcategories') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-stack-star mr-2"></i>
                        Items & Menu
                    </a>
                    <div class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("addon_categories_view")): ?>
                        <a href="<?php echo e(route("admin.addonCategories")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/addoncategories') ? 'active' : ''); ?>">
                            <i class="icon-tree6 mr-2"></i>
                            Addon Categories
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("addons_view")): ?>
                        <a href="<?php echo e(route("admin.addons")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/addons') ? 'active' : ''); ?>">
                            <i class="icon-list2 mr-2"></i>
                            Addons
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("menu_categories_view")): ?>
                        <a href="<?php echo e(route("admin.itemcategories")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/itemcategories') ? 'active' : ''); ?>">
                            <i class="icon-grid52 mr-2"></i>
                            Menu Categories
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("items_view")): ?>
                        <a href="<?php echo e(route("admin.items")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/items') ? 'active' : ''); ?>">
                            <i class="icon-grid mr-2"></i>
                            Items
                        </a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['all_users_view', 'delivery_guys_view', 'store_owners_view'])): ?>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle  <?php echo e(Request::is('admin/users') || Request::is('admin/manage-delivery-guys') || Request::is('admin/staffs') || Request::is('admin/customers') || Request::is('admin/usemanage-store-owners') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-users2 mr-2"></i>
                        Users
                    </a>

                    <div class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_users_view')): ?>
                        <a href="<?php echo e(route("admin.users")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/users') ? 'active' : ''); ?>"> <i
                                class="icon-users4 mr-2"></i> All Users</a>

                        <a href="<?php echo e(route("admin.customers")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/customers') ? 'active' : ''); ?>"> <i
                                class="icon-user mr-2"></i> Customers</a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('store_owners_view')): ?>
                        <a href="<?php echo e(route('admin.manageRestaurantOwners')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/manage-store-owners') ? 'active' : ''); ?>"> <i
                                class="icon-user-tie mr-2"></i> Store Owners</a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery_guys_view')): ?>
                        <a href="<?php echo e(route('admin.manageDeliveryGuys')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/manage-delivery-guys') ? 'active' : ''); ?>"> <i
                                class="icon-truck mr-2"></i> Delivery Guys</a>
                        <?php endif; ?>

                        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
                        <a href="<?php echo e(route("admin.staffs")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/staffs') ? 'active' : ''); ?>"> <i
                                class="icon-collaboration mr-2"></i> Staff</a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('order_view')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("admin.orders")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('admin/orders') ? 'active' : ''); ?>">
                        <i class="icon-basket mr-2"></i>
                        Orders
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['promo_sliders_manage', 'store_category_sliders_manage', 'coupons_manage', 'pages_manage',
                'send_notification_manage'])): ?>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle  <?php echo e(Request::is('admin/sliders') || Request::is('admin/coupons') || Request::is('admin/notifications') || Request::is('admin/store-category-slider') || Request::is('admin/pages') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-percent mr-2"></i>
                        Promotions
                    </a>
                    <div class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('promo_sliders_manage')): ?>
                        <a href="<?php echo e(route('admin.sliders')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/sliders') ? 'active' : ''); ?>">
                            <i class="icon-image2 mr-2"></i>
                            Promo Sliders
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('store_category_sliders_manage')): ?>
                        <a href="<?php echo e(route("admin.restaurantCategorySlider")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/store-category-slider') ? 'active' : ''); ?>">
                            <i class="icon-grid52 mr-2"></i>
                            Store Category Slider
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('coupons_manage')): ?>
                        <a href="<?php echo e(route('admin.coupons')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/coupons') ? 'active' : ''); ?>">
                            <i class="icon-price-tags2 mr-2"></i>
                            Coupons
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pages_manage')): ?>
                        <a href="<?php echo e(route('admin.pages')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/pages') ? 'active' : ''); ?>">
                            <i class="icon-files-empty mr-2"></i>
                            Pages
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send_notification_manage')): ?>
                        <a href="<?php echo e(route('admin.notifications')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/notifications') ? 'active' : ''); ?>">
                            <i class="icon-bubble-dots4 mr-2"></i>
                            Send Push Notifications
                        </a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['store_payouts_manage',
                'delivery_collection_manage', 'delivery_collection_logs_view', 'wallet_transactions_view',
                'reports_view'])): ?>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle  <?php echo e(Request::is('admin/delivery-collection-logs') || Request::is('admin/delivery-collection') || Request::is('admin/store-payouts') || Request::is('admin/wallet/transactions') || Request::is('admin/reports/top-items') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-books mr-2"></i>
                        Transactions
                    </a>
                    <div class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('store_payouts_manage')): ?>
                        <a href="<?php echo e(route('admin.restaurantpayouts')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/store-payouts') ? 'active' : ''); ?>">
                            <i class="icon-piggy-bank mr-2"></i>
                            Store Payouts
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery_collection_manage')): ?>
                        <a href="<?php echo e(route("admin.deliveryCollections")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/delivery-collections') ? 'active' : ''); ?>">
                            <i class="icon-cash3 mr-2"></i>
                            Delivery Collections
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery_collection_logs_view')): ?>
                        <a href="<?php echo e(route("admin.deliveryCollectionLogs")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/delivery-collection-logs') ? 'active' : ''); ?>">
                            <i class="icon-database-time2 mr-2"></i>
                            Delivery Collection Logs
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('wallet_transactions_view')): ?>
                        <a href="<?php echo e(route("admin.walletTransactions")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/wallet/transactions') ? 'active' : ''); ?>">
                            <i class="icon-transmission mr-2"></i>
                            Wallet Transactions
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports_view')): ?>
                        <a href="<?php echo e(route("admin.viewTopItems")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/reports/top-items') ? 'active' : ''); ?>">
                            <i class="icon-graph mr-2"></i>
                            Reports
                        </a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endif; ?>

                <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("admin.modules")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('admin/modules') ? 'active' : ''); ?>">
                        <i class="icon-stars mr-2"></i>
                        Modules
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('settings_manage')): ?>

                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle  <?php echo e(Request::is('admin/settings') || Request::is('admin/popular-geo-locations') || Request::is('admin/zones') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-cog3 mr-2"></i>
                        Settings
                    </a>
                    <div class="dropdown-menu">
                        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
                        <a href="<?php echo e(route("admin.zones")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/zones') ? 'active' : ''); ?>">
                            <i class="icon-location4 mr-2"></i>
                            Zones <span
                                class="badge badge-flat border-grey-800 text-danger text-capitalize ml-1 float-right">NEW</span>
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('popular_location_manage')): ?>
                        <a href="<?php echo e(route("admin.popularGeoLocations")); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/popular-geo-locations') ? 'active' : ''); ?>">
                            <i class="icon-location3 mr-2"></i>
                            Popular Geo Locations
                        </a>
                        <?php endif; ?>
                        <hr class="my-2">
                        <a href="<?php echo e(route("admin.settings", '#generalSettings')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/settings') ? 'active' : ''); ?>">
                            <i class="icon-gear mr-2"></i>
                            All Settings
                        </a>
                    </div>
                </li>
                <?php endif; ?>

                <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("restaurant.dashboard")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('/dashboard') ? 'active' : ''); ?>">
                        <i class="icon-meter-fast mr-2"></i>
                        <?php echo e(__('storeDashboard.navDashboard')); ?>

                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route("restaurant.restaurants")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/restaurants') ? 'active' : ''); ?>">
                        <i class="icon-store2 mr-2"></i>
                        <?php echo e(__('storeDashboard.navStores')); ?>

                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle  <?php echo e(Request::is('restaurant-owner/users') || Request::is('admin/manage-delivery-guys') || Request::is('admin/staffs') || Request::is('admin/customers') || Request::is('admin/usemanage-store-owners') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-users2 mr-2"></i>
                        Users
                    </a>

                    <div class="dropdown-menu">
                        <a href="<?php echo e(route("restaurant.users")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/users') ? 'active' : ''); ?>">
                        <i class="icon-users4 mr-2"></i>
                        All Users
                    </a>

                        <a href="<?php echo e(route("restaurant.customers")); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/customers') ? 'active' : ''); ?>"> <i
                                class="icon-user mr-2"></i> Customers</a>

                        <a href="<?php echo e(route('restaurant.manageRestaurantOwners')); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/manage-store-owners') ? 'active' : ''); ?>"> <i
                                class="icon-user-tie mr-2"></i> Store Owners</a>
                        <a href="<?php echo e(route('restaurant.manageDeliveryGuys')); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/manage-delivery-guys') ? 'active' : ''); ?>"> <i
                                class="icon-truck mr-2"></i> Delivery Guys</a>

                        <a href="<?php echo e(route("restaurant.staffs")); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/staffs') ? 'active' : ''); ?>"> <i
                                class="icon-collaboration mr-2"></i> Staff</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle <?php echo e(Request::is('restaurant-owner/items')  || Request::is('restaurant-owner/addons') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-stack-star mr-2"></i>
                        <?php echo e(__('storeDashboard.navItemsMenu')); ?>

                    </a>
                    <div class="dropdown-menu">
                        <a href="<?php echo e(route("restaurant.addonCategories")); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/addoncategories') ? 'active' : ''); ?>">
                            <i class="icon-tree6 mr-2"></i>
                            <?php echo e(__('storeDashboard.navSubAddonCat')); ?>

                        </a>
                        <a href="<?php echo e(route("restaurant.addons")); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/addons') ? 'active' : ''); ?>">
                            <i class="icon-list2 mr-2"></i>
                            <?php echo e(__('storeDashboard.navSubAddon')); ?>

                        </a>
                        <a href="<?php echo e(route("restaurant.itemcategories")); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/itemcategories') ? 'active' : ''); ?>">
                            <i class="icon-grid52 mr-2"></i>
                            <?php echo e(__('storeDashboard.navSubMenuCat')); ?>

                        </a>
                        <a href="<?php echo e(route("restaurant.items")); ?>"
                            class="dropdown-item <?php echo e(Request::is('restaurant-owner/items') ? 'active' : ''); ?>">
                            <i class="icon-grid mr-2"></i>
                            <?php echo e(__('storeDashboard.navSubItems')); ?>

                        </a>
                    </div>
                </li>
                <?php if((isset($is_active) && $is_active == 0) && (isset($reservation) && $reservation->sommelier_reservations == 'yes')): ?>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle <?php echo e(Request::is('restaurant-owner/bookings') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-book mr-2"></i>
                        <?php echo e(__('storeDashboard.navBookings')); ?>

                    </a>
                    <div class="dropdown-menu">
                        <a href="<?php echo e(route("restaurant.bookings")); ?>" class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/bookings') ? 'active' : ''); ?>">
                        <i class="icon-book mr-2"></i>
                        <?php echo e(__('storeDashboard.navBookingsList')); ?>

                        </a>
                        <a href="<?php echo e(route("restaurant.assignTable")); ?>" class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/assignTable') ? 'active' : ''); ?>">
                    <i class="icon-book mr-2"></i>
                    <?php echo e(__('storeDashboard.assignTable')); ?>

                    </a>
                    </div>
                </li>
                <?php elseif((isset($is_active) && $is_active == 1) && (isset($reservation) && $reservation->sommelier_reservations == 'no')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("restaurant.orders")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/orders') ? 'active' : ''); ?>">
                        <i class="icon-basket mr-2"></i>
                        <?php echo e(__('storeDashboard.navOrders')); ?>

                    </a>
                </li>
                <?php elseif((isset($is_active) && $is_active == 1) && (isset($reservation) && $reservation->sommelier_reservations == 'yes')): ?>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle <?php echo e(Request::is('restaurant-owner/bookings') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-book mr-2"></i>
                        <?php echo e(__('storeDashboard.navBookings')); ?>

                    </a>
                    <div class="dropdown-menu">
                        <a href="<?php echo e(route("restaurant.bookings")); ?>" class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/bookings') ? 'active' : ''); ?>">
                        <i class="icon-book mr-2"></i>
                        <?php echo e(__('storeDashboard.navBookingsList')); ?>

                        </a>
                        <a href="<?php echo e(route("restaurant.assignTable")); ?>" class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/assignTable') ? 'active' : ''); ?>">
                    <i class="icon-book mr-2"></i>
                    <?php echo e(__('storeDashboard.assignTable')); ?>

                    </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route("restaurant.orders")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/orders') ? 'active' : ''); ?>">
                        <i class="icon-basket mr-2"></i>
                        <?php echo e(__('storeDashboard.navOrders')); ?>

                    </a>
                </li>
               
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)"
                        class="navbar-nav-link dropdown-toggle  <?php echo e(Request::is('admin/sliders') || Request::is('admin/coupons') || Request::is('admin/notifications') || Request::is('admin/store-category-slider') || Request::is('admin/pages') ? 'active' : ''); ?>"
                        data-toggle="dropdown">
                        <i class="icon-percent mr-2"></i>
                        Promotions
                    </a>
                    <div class="dropdown-menu">
                        <a href="<?php echo e(route('restaurant.sliders')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/sliders') ? 'active' : ''); ?>">
                            <i class="icon-image2 mr-2"></i>
                            Promo Sliders
                        </a>
                        <a href="<?php echo e(route('restaurant.notifications')); ?>"
                            class="dropdown-item <?php echo e(Request::is('admin/notifications') ? 'active' : ''); ?>">
                            <i class="icon-bubble-dots4 mr-2"></i>
                            Send Push Notifications
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route("restaurant.earnings")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/earnings/*') ? 'active' : ''); ?>">
                        <i class="icon-coin-dollar mr-2"></i>
                        <?php echo e(__('storeDashboard.navEarnings')); ?>

                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route("restaurant.ratings")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('restaurant-owner/ratings/*') ? 'active' : ''); ?>">
                        <i class="icon-star-full2 mr-2"></i>
                        <?php echo e(__('storeDashboard.navRatings')); ?>

                    </a>
                </li>
                <?php if(\Nwidart\Modules\Facades\Module::find('CallAndOrder') &&
                \Nwidart\Modules\Facades\Module::find('CallAndOrder')->isEnabled()): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("login_as_customer")): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("cao.usersPage")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('callandorder/users') ? 'active' : ''); ?>">
                        <i class="icon-phone2 mr-2"></i>
                        <?php echo e(__('callAndOrderLang.callAndOrderNavMenuLabel')); ?>

                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
                <?php if(\Nwidart\Modules\Facades\Module::find('ThermalPrinter') &&
                \Nwidart\Modules\Facades\Module::find('ThermalPrinter')->isEnabled()): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("thermalprinter.settings")); ?>"
                        class="navbar-nav-link <?php echo e(Request::is('thermalprinter/settings') ? 'active' : ''); ?>">
                        <i class="icon-printer2 mr-2"></i>
                        <?php echo e(__('thermalPrinterLang.printerSettingsNav')); ?>

                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/includes/header.blade.php ENDPATH**/ ?>