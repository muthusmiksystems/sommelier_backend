
<?php $__env->startSection("title"); ?> Modules - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php if(config('setting.moduleRedownloadNotice') == "false"): ?>
<div class="px-3 mt-4 mx-3"
    style="background: linear-gradient(to right, #f12711, #f5af19); color: #fff; border-radius: 8px" id="modulesNotice">
    <div class="d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
        <div>
            <p class="mb-0">Version 3.0 has many core changes that requires all modules to be updated.</p>
            <p class="mb-0">Kindly <b>redownload</b> all your modules from CodeCanyon and reupload here.</p>
        </div>
        <div>
            <span class="bannerButton">I
                Understand</span>
        </div>
    </div>
</div>
<script>
    $(".acceptNoticeBtn").click(function (e) { 
        $.ajax({
            type: "get",
            url: "<?php echo e(route('admin.acceptNotice')); ?>",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $('#modulesNotice').remove();
                }
            }
        }); 
    });
</script>
<?php endif; ?>

<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2">Modules </span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <?php if($checkZipExtension): ?>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="uploadNewModuleBtn">
                    <b><i class="icon-plus2"></i></b>
                    Upload Module
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
    $('#uploadNewModuleBtn').click(function(event) {
      $('#moduleUploadBlock').toggle(500);
    });
</script>
<div class="content">
    <?php if(!$checkZipExtension): ?>
    <div class="col-md-12">
        <p class="text-danger font-weight-bold"><b>Zip PHP Extension</b> is not enabled.
            <br>
            Therefore, you will not be able to upload any Premium Modules.
            <br>
            <span class="font-weight-normal">Kindly contact your hosting provider to enable the <b>Zip PHP Extension</b>
                for your server.</span>
        </p>
    </div>
    <?php else: ?>
    <div class="col-md-12" id="moduleUploadBlock" style="display: none;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.uploadModuleZipFile')); ?>" enctype="multipart/form-data"
                    class="dropzone" id="module_uploader">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-center"><i class="icon-circle-down2"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($module->getStudlyName()); ?></strong></td>
                                <td>
                                    <small><?php echo e($module->getDescription()); ?></small>
                                </td>
                                <td>
                                    <?php if($module->isEnabled()): ?>
                                    <span class="badge badge-flat border-grey-800 text-primary text-capitalize mr-1">
                                        Enabled
                                    </span>
                                    <?php else: ?>
                                    <span class="badge badge-flat border-grey-800 text-danger text-capitalize mr-1">
                                        Disabled
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-justified align-items-center" <?php if(!$module->
                                        isEnabled()): ?> style="flex-direction: row-reverse;" <?php endif; ?>>
                                        <?php if($module->isEnabled()): ?>
                                        <a href="<?php echo e(url($module->getLowerName())); ?>/settings"
                                            class="btn btn-secondary btn-labeled btn-labeled-left btn-sm"
                                            data-placement="left">
                                            <b><i class="icon-gear ml-1"></i> </b>
                                            Settings
                                        </a>
                                        <a href="<?php echo e(route('admin.disableModule', $module->getStudlyName())); ?>"
                                            class="btn btn-danger btn-labeled btn-labeled-left btn-sm enDisBtn ml-2"
                                            data-popup="tooltip" title="Double Click to Disable" data-placement="left">
                                            <b><i class="icon-cross2 ml-1"></i></b>
                                            Disable
                                        </a>
                                        <?php else: ?>
                                        <a href="<?php echo e(route('admin.enableModule', $module->getStudlyName())); ?>"
                                            class="btn btn-primary btn-labeled btn-labeled-left btn-sm enDisBtn"
                                            data-popup="tooltip" title="Double Click to Enable" data-placement="left"
                                            style="max-width: 125px;">
                                            <b><i class="icon-checkmark3 ml-1"></i></b>
                                            Enable
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="installingModule" class="modal fade mt-5" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header pb-3">
                <h5 class="modal-title">
                    <span class="font-weight-bold">
                        <i class="icon-spinner10 spinner mr-1"></i>
                        Pending Verification
                    </span>
                </h5>
            </div>
            <div class="modal-body">
                <form id="pcForm">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Purchase Code</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="pc"
                                placeholder="Enter the purchase code of this module" required id="pc">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary" id="verifyInstall">
                            Verify & Install
                            <i class="icon-arrow-right8 ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    Dropzone.autoDiscover = false;
    
    $(function() {
    var myDropzone =  $("#module_uploader").dropzone({
           paramName: "file", // The name that will be used to transfer the file
           maxFilesize: 50, // MB
           maxFiles: 1,
           dictDefaultMessage: 'Drag & Drop <strong>UPLOAD-THIS-MODULE.zip</strong> file',
           autoProcessQueue: false,
           acceptedFiles: '.zip',
           init: function() {
               this.on('addedfile', function(file){
                   if (this.fileTracker) {
                   this.removeFile(this.fileTracker);
               }
                   this.fileTracker = file;
               });
       
               var dropzone = this;
       
               //when file added or dropped, process the file for auto-upload
               dropzone.on("addedfile", function(file) {
                   if (file.name == "UPLOAD-THIS-MODULE.zip") {
                       $.jGrowl("Uploading file please wait...", {
                           position: 'bottom-center',
                           header: 'File Added ✅',
                           theme: 'bg-success',
                           life: '1800'
                       }); 
                       setTimeout(function() {
                           dropzone.processQueue();
                       }, 2200);
                   }
               });
       
               //on upload success to server's filesystem, show popup
               dropzone.on("success", function(file) {
                   setTimeout(function() {
                       dropzone.removeFile(file);
                       var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                       $.ajax({
                         url: '<?php echo e(route('admin.installModule')); ?>',
                         type: 'POST',
                         dataType: 'JSON',
                         data: {_token: token},
                       })
                       .done(function(data) {
                         if (data.success) {
                       
                           console.log(data.message)
                       
                           $.jGrowl("Module installation was successful", {
                               position: 'bottom-center',
                               header: 'Success ✅',
                               theme: 'bg-success',
                               life: '1800'
                           }); 
                       
                           setTimeout(function() {
                              window.location.reload();
                           }, 600);
                         }
                       })
                   }, 500);
               });
    
               //if anything goes wrong during upload, show error message and remove file
               dropzone.on("error", function(file, errorMessage, xhr) {
                   dropzone.removeFile(file);
                   console.log(errorMessage);
                   $.jGrowl("Server Error. Check the console for full log.", {
                       position: 'bottom-center',
                       header: 'Wooopsss ⚠️',
                       theme: 'bg-warning',
                       life: '5000'
                   }); 
               });
           },
           accept: function(file, done) {
               //if file name is UPLOAD-THIS.zip then accept the file
               if (file.name == "UPLOAD-THIS-MODULE.zip") {
                   done();
               }
               else {
                   //else remove the file and show error message
                   this.removeFile(file);
                   $(function () {
                       $.jGrowl("This seems to be an incorrect file. Please get the 'UPLOAD-THIS-MODULE.zip' file.", {
                           position: 'bottom-center',
                           header: 'Wooopsss ⚠️',
                           theme: 'bg-warning',
                           life: '5000'
                       });    
                   });
                   done();
               }
           },
           success: function(file, response) 
           {
               console.log(response);
           },
           error: function(file, response)
           {
              return false;
           }
       });
    
          $('#pcForm').submit(function(event) {
              event.preventDefault();
              
              $('#verifyInstall').attr('disabled', true);
              $('#verifyInstall').html("Please Wait...")
    
              var pc = $('#pc').val();
              var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              $.ajax({
                url: '<?php echo e(route('admin.installModule')); ?>',
                type: 'POST',
                dataType: 'JSON',
                data: {pc: pc, _token: token},
              })
              .done(function(data) {
                if (data.success) {
    
                  console.log(data.message)
    
                  $.jGrowl("Module installation was successful", {
                      position: 'bottom-center',
                      header: 'Success ✅',
                      theme: 'bg-success',
                      life: '1800'
                  }); 
    
                  setTimeout(function() {
                     window.location.reload();
                  }, 600);
                }
              })
              .fail(function(err) {
                console.log("error");
    
                $('#verifyInstall').attr({
                  disabled: false,
                  html: 'Verify & Install'
                });
    
                $('#verifyInstall').attr('disabled', false);
                $('#verifyInstall').html("Verify & Install <i class='icon-arrow-right8 ml-1'></i>")
    
                $(function () {
                    $.jGrowl($.parseJSON(err.responseText).message, {
                        position: 'bottom-center',
                        header: 'Wooopsss ⚠️',
                        theme: 'bg-warning',
                        life: '5000'
                    });    
                });
              })              
          });
    
           $('.select').select2({
               minimumResultsForSearch: Infinity,
           });
       
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
    
          $(".colorpicker-show-input").spectrum({
            showInput: true
          });
    
    $('.enDisBtn').dblclick(function(event) {
    $(this).addClass('pointer-none');
                window.location = this.href;
                return false;
    }).click(function(event) {
    return false;
    });;
    });
    
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/modules.blade.php ENDPATH**/ ?>