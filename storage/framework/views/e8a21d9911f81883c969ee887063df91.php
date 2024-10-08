
<?php $__env->startSection("title"); ?> Orders - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .pulse {
        display: inline-block;
        width: 12.5px;
        height: 12.5px;
        border-radius: 50%;
        animation: pulse 1.2s infinite;
        vertical-align: middle;
        margin: -3px 5px 0 0;
    }

    .pulse-warning {
        background: #ffc107;
    }

    .pulse-danger {
        background: #ff5722;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 87, 34, 0.5);
        }

        50% {
            box-shadow: 0 0 0 26px rgba(255, 87, 34, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 87, 34, 0);
        }
    }

    .linked-item {
        color: #4e535a;
    }

    .linked-item:hover {
        color: #8360c3;
        text-decoration: underline;
        opacity: 1;
    }

    @media (min-width: 1200px) {
        .container {
            max-width: 95%;
        }
    }
</style>

<div class="content mt-3">
    <div class="d-flex justify-content-between my-2">
        <h3><strong>Order Management</strong></h3>
        <div>
            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="clearFilterAndState"> <b><i
                        class=" icon-reload-alt"></i></b> Reset All Filters</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-striped" id="ordersDataTable" width="100%">
                        <thead>
                            <tr>
                                <th class="hidden">ID</th>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Store Name</th>
                                <th>Mode</th>
                                <th>Total</th>
                                <th>Order Placed At</th>
                                <th class="text-center">Live Timer</th>
                                <th class="text-center" style="width: 10%;"><i class="
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
</div>
<script>
    $(function() {
    
        $('body').tooltip({selector: '[data-popup="tooltip"]'});
        
         var datatable = $('#ordersDataTable').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            lengthMenu: [ 10, 25, 50, 100, 200, 500 ],
            order: [[ 0, "desc" ]],
            ajax: '<?php echo e(route('admin.ordersDataTable')); ?>',
            columns: [
                {data: 'id', visible: false, searchable: false},
                {data: 'unique_order_id'},
                {data: 'orderstatus_id', name: "orderstatus.name"},
                {data: 'restaurant_name', name: "restaurant.name"},
                {data: 'payment_mode'},
                {data: 'total'},
                {data: 'created_at'},
                {data: 'live_timer', searchable: false, orderable: false},
                {data: 'action', sortable: false, searchable: false, reorder: false},
            ],
            fixedColumns: {
                leftColumns: 0,
                rightColumns: 1
            },
            colReorder: {
                   fixedColumnsRight: 1
               },
            drawCallback: function( settings ) {
                $('select').select2({
                   minimumResultsForSearch: Infinity,
                   width: 'auto'
                });
                timer = setInterval(updateClock, 1000);

                var newDate = new Date();
                console.log(newDate)
                var newStamp = newDate.getTime();

                var timer; 

                function updateClock() {

                    $('.liveTimer').each(function(index, el) {
                        var orderCreatedData = $(this).attr("title");
                        var startDateTime = new Date(orderCreatedData); 
                        var startStamp = startDateTime.getTime();
                    

                        newDate = new Date();
                        newStamp = newDate.getTime();
                        var diff = Math.round((newStamp-startStamp)/1000);
                        
                        var d = Math.floor(diff/(24*60*60));
                        diff = diff-(d*24*60*60);
                        var h = Math.floor(diff/(60*60));
                        diff = diff-(h*60*60);
                        var m = Math.floor(diff/(60));
                        diff = diff-(m*60);
                        var s = diff;
                        var checkDay = d > 0 ? true : false;
                        var checkHour = h > 0 ? true : false;
                        var checkMin = m > 0 ? true : false;
                        var checkSec = s > 0 ? true : false;
                        var formattedTime = checkDay ? d+ " day" : "";
                formattedTime += checkHour ? " " +h+ " hr" : "";
                formattedTime += checkMin ? " " +m+ " min" : "";
                formattedTime += checkSec ? " " +s+ " sec" : "";

                        $(this).text(formattedTime);
                    });
                }
            },
            scrollX: true,
            scrollCollapse: true,
            dom: '<"custom-processing-banner"r>flBtip',
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
                       {extend: 'csv', filename: 'orders-'+ new Date().toISOString().slice(0,10), text: 'Export as CSV'},
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/orders.blade.php ENDPATH**/ ?>