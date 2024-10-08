@extends("admin.layouts.master")
@section("title")
Delivery Guy Registration
@endsection
@section("content")
<style>
    .btn-registerBtn {
        background-color: #ffffff;
        color: #2b2b2b;
        border-radius: 0.175rem;
        box-shadow: 0 1px 6px 1px rgba(0,0,0,.05);
        transition: 0.2s linear all !important;
    }
    .btn-registerBtn:hover {
        /*background-color: #fafafa !important;*/
        color: #2b2b2b;
        box-shadow: 0 3px 12px 2px rgba(0,0,0,.10) !important;
        transition: 0.2s linear all !important;
    }
    .btn-registerBtn-selected {
        color: #fff;
        background-color: #FF5722;
    }
    .btn-registerBtn-selected:hover {
        color: #fff;
    }
    .delivery-msg {
        background-color: #ffffff;
        color: #2b2b2b;
        border-radius: 0.175rem;
        box-shadow: 0 3px 12px 2px rgba(0,0,0,.05);
    }
    .back_to_login_block{
        text-align:center;
        margin-top:25px;
    }
</style>
<div class="content">
    <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
     <form class="registration-form py-5" action="{{ route('registerDeliverySave') }}" method="POST" id="regForm" style="margin: 0 auto 20px auto;" enctype="multipart/form-data">
        <input type="hidden" name="role" id="roleValue" value="DELIVERY">
        <div class="card mb-0">
            <div class="card-body">
                <div class="text-center mb-3">
                    <span id="regIcon">
                        <i class='icon-truck icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1'></i>
                    </span>
                    <h5 class="mb-0">Registration for OzEats <span id="regFor">Delivery</span></h5>
                    <span class="d-block">You need to posses RSA certificate to drive OzEats deliveries</span>
                    <span class="d-block text-muted">Please fill the form to register</span>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="First Name" name="first_name" value="{{ old('first_name') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-mail5 text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="number" class="form-control" placeholder="Phone number" name="phone" value="{{ old('phone') }}" min="8" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-mobile text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="password" class="form-control" placeholder="Password" name="password" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-lock2 text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" id="driver_dob" placeholder="DD-MM-YYYY" name="dob" value="{{ old('dob') }}" required="required" autocomplete="off" readonly>
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Licence No" name="licence_no" value="{{ old('licence_no') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback">
                    <select class="form-control" name="state">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ (old('state') == $state->id) ? "selected='selected'" : "" }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Licence:</label>
                    <div class="col-lg-9">
                        <input type="file" class="form-control-uniform" name="licence_photo">
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Nickname of Delivery Guy" name="delivery_name" value="{{ old('delivery_name') }}"required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Address of Delivery Guy" name="delivery_address" value="{{ old('delivery_address') }}"required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Suburb of Delivery Guy" name="delivery_suburb" value="{{ old('delivery_suburb') }}"required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="ZIP of Delivery Guy" name="delivery_zip" value="{{ old('delivery_zip') }}"required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <!--<div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="number" class="form-control" placeholder="Age" name="delivery_age" value="{{ old('delivery_age') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>-->

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Delivery Guy's Photo:</label>
                    <div class="col-lg-9">
                        <input type="file" class="form-control-uniform" name="delivery_photo">
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left hide">
                    <input type="hidden" class="form-control" placeholder="Description" name="delivery_description" value="{{ old('delivery_description') }}">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <!--<div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Vehicle Number" name="delivery_vehicle_number" value="{{ old('delivery_vehicle_number') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div> -->

                <div class="form-group form-group-feedback">
                    <select class="form-control" name="vehicle_type">
                        <option value="">Select Vehicle Type</option>
                        @foreach($vehicle_types as $vehicle_type)
                        <option value="{{ $vehicle_type->id }}" {{ (old('vehicle_type') == $vehicle_type->id) ? "selected='selected'" : "" }}>{{ $vehicle_type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Registration No" name="registration_no" value="{{ old('registration_no') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="ABN No" name="abn_no" value="{{ old('abn_no') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Bank Name" name="bank_name" value="{{ old('bank_name') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="BSB" name="bsb" value="{{ old('bsb') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="number" class="form-control" placeholder="Account No" name="account_number" value="{{ old('account_number') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="text" class="form-control" placeholder="Account Name" name="account_name" value="{{ old('account_name') }}" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Vehicle Registration:</label>
                    <div class="col-lg-9">
                        <input type="file" class="form-control-uniform" name="vehicle_registration">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Vehicle Insurance Policy:</label>
                    <div class="col-lg-9">
                        <input type="file" class="form-control-uniform" name="vehicle_insurance_policy">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">RSA Certificate:</label>
                    <div class="col-lg-9">
                        <input type="file" class="form-control-uniform" name="certificate">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Police Clearence Certificate:</label>
                    <div class="col-lg-9">
                        <input type="file" class="form-control-uniform" name="police_clearence_certificate">
                    </div>
                </div>

                
                {!! captcha_img('flat') !!}
                <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="text" class="form-control" placeholder="Enter Captcha" name="captcha" required="required">
                    <div class="form-control-feedback">
                        <i class="icon-font-size text-muted"></i>
                    </div>
                </div>
                @csrf
                <div class="form-group">
                    <button type="submit" id="sendOTPButton" class="btn btn-primary btn-block" style="height: 2.8rem; font-size: 1rem;">Register <i
                        class="icon-circle-right2 ml-2"></i></button>
                </div>

            </div>
        </div>
        <div class="back_to_login_block"><a href="{{ route('get.login') }}" class=""><i class="icon-arrow-left8 mr-2"></i>Back to login</a></div>
    </form>
    </div>
    <div class="col-md-3"></div>
    </div>
</div>


    <script>
        $('.regButtonDelivery').click(function(event) {
            $('.regButtonDelivery').addClass('btn-registerBtn-selected')
            $('.regButtonResOwn').removeClass('btn-registerBtn-selected');
            $('#loginForm').addClass('hidden');
            $('#regForm').removeClass('hidden');
            $('#regFor').html("Delivery Guy")
            $('#roleValue').attr('value', 'DELIVERY');
            $('#regIcon').html("<i class='icon-truck icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1'></i>")

        });
        $('.regButtonResOwn').click(function(event) {
            $('.regButtonResOwn').addClass('btn-registerBtn-selected')
            $('.regButtonDelivery').removeClass('btn-registerBtn-selected');
            $('#loginForm').addClass('hidden');
            $('#regForm').removeClass('hidden');
            $('#regFor').html("Restaurant Owner")
            $('#roleValue').attr('value', 'RESOWN');
            $('#regIcon').html("<i class='icon-store2 icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1'></i>")
        });

        $(document).ready(function(){
            $('#driver_dob').datepicker({
                format:"dd-mm-yyyy"
            });
            
        });
    </script>

@if(Session::has('delivery_register_message'))
<div class="d-flex justify-content-center align-items-center mt-3">
     <div class="delivery-msg p-3"><b>SUCCESS!!!</b> You can now login to the delivery application using your phone.</div>
</div>
@endif
@endsection