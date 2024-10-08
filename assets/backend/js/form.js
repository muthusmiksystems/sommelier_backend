
var script_tag = document.getElementById('booking_form_script');
if (script_tag.hasAttribute('data-id'))
  //  return false;

  var id = script_tag.getAttribute('data-id');
var web_url = "https://localhost"; // put url without / at the end
var card; // Declare card in the global scope
var stripe; // Declare stripe in the global scope
//var down = document.getElementById("form_data");

var outer = document.createElement('div');
outer.setAttribute('id', "booking_form_ozeats");


// Create a break line element
var br = document.createElement("br");


// Create a form synamically
var form = document.createElement("form");
form.setAttribute("method", "post");
form.setAttribute("style", "display:block;padding:10% 0;max-width:100%;border-radius:4px;border:1px solid #9e9e9e;");
form.setAttribute("id", "strip_payment_booking");

var RESNAME = document.createElement("label");
RESNAME.setAttribute("for", "area");
RESNAME.innerHTML = "Select Venue";
RESNAME.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var TRESNAME = document.createElement("select");
TRESNAME.setAttribute("name", "select_venue");
TRESNAME.setAttribute("placeholder", "Select Venue");
TRESNAME.setAttribute("id", "select_venue");
TRESNAME.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
TRESNAME.setAttribute("required", "required");
TRESNAME.addEventListener("change", function () {
  updateTableLocation();
  getrestaurantsetting()
  getrestaurantstripekey()
});


// Create an input element for PAX NUMBER
var LFN = document.createElement("label");
LFN.setAttribute("for", "no_of_seats");
LFN.innerHTML = "No. of pax";
LFN.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var FN = document.createElement("input");
FN.setAttribute("type", "number");
FN.setAttribute("min", "1");
FN.setAttribute("max", "100000");
FN.setAttribute("name", "No. of pax");
FN.setAttribute("placeholder", "No. of pax");
FN.setAttribute("id", "no_of_seats");
FN.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
FN.setAttribute("required", "required");
FN.setAttribute('onchange', 'check_max_pax()');

var FNER = document.createElement("label");
FNER.setAttribute("for", "no_of_pax");
FNER.setAttribute('id', 'no_of_pax_er');
FNER.setAttribute("style", "display:none;margin-bottom:20px;width:100%;max-width:70%;float:left;margin-left:50px;color:red;");



// Create an input element for DATE-TIME
var LTM = document.createElement("label");
LTM.setAttribute("for", "booking_date");
LTM.innerHTML = "Booking Date";
LTM.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var TM = document.createElement("input");
TM.setAttribute("type", "date");
TM.setAttribute("name", "booking_date");
TM.setAttribute("placeholder", "Booking Date");
TM.setAttribute("id", "booking_date");
TM.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
TM.setAttribute("required", "required");
TM.setAttribute("onchange", "timeval();");


// Create an input element for DATE-TIME
var BKT = document.createElement("label");
BKT.setAttribute("for", "booking_time");
BKT.innerHTML = "Booking Time";
BKT.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");


var booking_time_btn = document.createElement('div');
booking_time_btn.setAttribute('id', "booking_time_btn");

// Create an input element for area
var BTL = document.createElement("label");
BTL.setAttribute("for", "area");
BTL.innerHTML = "Table Location";
BTL.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var TBLS = document.createElement("select");
TBLS.setAttribute("name", "table_location");
TBLS.setAttribute("placeholder", "Table Location");
TBLS.setAttribute("id", "table_location");
TBLS.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
TBLS.setAttribute("required", "required");




// Create an input element for mobile number
var LMN = document.createElement("label");
LMN.setAttribute("for", "mobile_number");
LMN.innerHTML = "Mobile Number";
LMN.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var MN = document.createElement("input");
MN.setAttribute("type", "text");
MN.setAttribute("name", "Mobile Number");
MN.setAttribute("placeholder", "Mobile Number");
MN.setAttribute("id", "mobile_number");
MN.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
MN.setAttribute("required", "required");
MN.setAttribute("value", "04");
MN.setAttribute("min", "10");
MN.setAttribute("max", "10");
MN.setAttribute("pattern", "[0-9+]{10}");
MN.setAttribute("title", "04 XXX XXX XX");
//            MN.setAttribute("onkeydown","return validateMobile(event, this.value);");

// Create an input element for email
var LEID = document.createElement("label");
LEID.setAttribute("for", "email_address");
LEID.innerHTML = "Email";
LEID.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var EID = document.createElement("input");
EID.setAttribute("type", "email");
EID.setAttribute("name", "Email");
EID.setAttribute("placeholder", "Email");
EID.setAttribute("id", "email_address");
EID.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
EID.setAttribute("required", "required");

// Create an input element for first name
var LFIN = document.createElement("label");
LFIN.setAttribute("for", "first_name");
LFIN.innerHTML = "First Name";
LFIN.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var FIN = document.createElement("input");
FIN.setAttribute("type", "text");
FIN.setAttribute("name", "First Name");
FIN.setAttribute("placeholder", "First Name");
FIN.setAttribute("id", "first_name");
FIN.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
FIN.setAttribute("required", "required");

// Create an input element for last name
var LLAN = document.createElement("label");
LLAN.setAttribute("for", "last_name");
LLAN.innerHTML = "Last Name";
LLAN.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");



var LAN = document.createElement("input");
LAN.setAttribute("type", "text");
LAN.setAttribute("name", "Last Name");
LAN.setAttribute("placeholder", "Last Name");
LAN.setAttribute("id", "last_name");
LAN.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
LAN.setAttribute("required", "required");

// Create an input element for date of birth
var LDOB = document.createElement("label");
LDOB.setAttribute("for", "dob");
LDOB.innerHTML = "Date of Birth (Optional)";
LDOB.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var DOB = document.createElement("input");
DOB.setAttribute("type", "date");
DOB.setAttribute("name", "Date of Birth");
DOB.setAttribute("placeholder", "Date of Birth");
DOB.setAttribute("id", "dob");

DOB.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
//            DOB.setAttribute("required","required");

// Create an input element for comment

var LCM = document.createElement("label");
LCM.setAttribute("for", "comments");
LCM.innerHTML = "Any Special Comment";
LCM.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");

var CM = document.createElement("input");
CM.setAttribute("type", "text");
CM.setAttribute("name", "comment");
CM.setAttribute("placeholder", "Comment");
CM.setAttribute("id", "comments");
CM.setAttribute("style", "display:inline-block;margin-bottom:15px;height:35px;text-indent:10px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
var LPM = document.createElement("label");
LPM.setAttribute("for", "payment-element");
LPM.innerHTML = "Payment Information";
LPM.setAttribute("style", "margin-bottom:5px;width:100%;max-width:48%;float:left;margin-left:50px;");
LPM.style.display = "none";
// payment gateway

var PM = document.createElement("div");
PM.setAttribute("id", "payment-element");
PM.setAttribute("style", "display:inline-block;margin-bottom:15px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;padding-top:40px;padding-bottom:40px;padding-left:5px");

var errorDiv = document.createElement("div");
errorDiv.setAttribute("id", "card-errors");
errorDiv.setAttribute("role", "alert");

var Term_block = document.createElement('div');
Term_block.setAttribute('id', "term_and_condition_blk");
Term_block.setAttribute("style", "display:flex;align-item:center;vertial-align:middle;");

var Terms = document.createElement("input");
Terms.setAttribute("type", "checkbox");
Terms.setAttribute("name", "terms_conditions");
Terms.setAttribute("id", "terms_conditions");
Terms.setAttribute("value", "yes");
Terms.setAttribute("checked", "checked");
Terms.setAttribute("style", "display:inline-block;height:35px;text-indent:10px;border:1px solid #cacaca;width:25px;margin-left:50px;");
Terms.setAttribute('onclick', "return false");

var TCLB = document.createElement("label");
TCLB.setAttribute("for", "terms_conditions");
TCLB.innerHTML = "do you agree <a href='https://www.cloudappstechnology.com/terms' target='_blank'>Terms & Conditions?</a>";
TCLB.setAttribute("style", "width:100%;max-width:48%;float:left;margin-left:15px;line-height:2;");

Term_block.appendChild(Terms);
Term_block.appendChild(TCLB);



// create a submit button
var s = document.createElement("button");
s.setAttribute("type", "button");
s.setAttribute("id", "first")
s.textContent = "Submit";
s.setAttribute("style", "height:35px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
s.addEventListener("click", button_click_first);

var s2 = document.createElement("button");
s2.setAttribute("type", "button");
s2.setAttribute("id", "second")
s2.textContent = "Submit";
s2.setAttribute("style", "height:35px;border:1px solid #cacaca;min-width:70%;max-width:70%;margin-left:50px;");
s2.addEventListener("click", send_data);
s2.style.display = "none";

// Append the NO OF PAX input to the form

form.appendChild(RESNAME);
form.appendChild(br.cloneNode());
form.appendChild(TRESNAME);
form.appendChild(br.cloneNode());

form.appendChild(LFN);
form.appendChild(br.cloneNode());

form.appendChild(FN);
//form.appendChild(br.cloneNode());

form.appendChild(FNER);
form.appendChild(br.cloneNode());

// Append the TIME to the form
form.appendChild(LTM);
form.appendChild(br.cloneNode());

form.appendChild(TM);
form.appendChild(br.cloneNode());

form.appendChild(BKT);
form.appendChild(br.cloneNode());


form.appendChild(booking_time_btn);
form.appendChild(br.cloneNode());

form.appendChild(BTL);
form.appendChild(br.cloneNode());

form.appendChild(TBLS);
form.appendChild(br.cloneNode());


// Append the MOBILE NUMBER to the form
form.appendChild(LMN);
form.appendChild(br.cloneNode());

form.appendChild(MN);
form.appendChild(br.cloneNode());

// Append the EMAIL to the form
form.appendChild(LEID);
form.appendChild(br.cloneNode());

form.appendChild(EID);
form.appendChild(br.cloneNode());

// Append the FIRST NAME to the form
form.appendChild(LFIN);
form.appendChild(br.cloneNode());

form.appendChild(FIN);
form.appendChild(br.cloneNode());

// Append the LAST NAME to the form
form.appendChild(LLAN);
form.appendChild(br.cloneNode());

form.appendChild(LAN);
form.appendChild(br.cloneNode());

// Append the DATE OF BIRTH to the form
form.appendChild(LDOB);
form.appendChild(br.cloneNode());

form.appendChild(DOB);
form.appendChild(br.cloneNode());

// Append the COMMENT to the form
form.appendChild(LCM);
form.appendChild(br.cloneNode());

form.appendChild(CM);
form.appendChild(br.cloneNode());

// payment gateway
form.appendChild(LPM);
form.appendChild(br.cloneNode());
form.appendChild(PM);
PM.appendChild(errorDiv);


//Append the Term and condition checkbox
form.appendChild(Term_block);
form.appendChild(br.cloneNode());

// Append the BOOKING STORE to the form
//form.appendChild(BS);
//form.appendChild(br.cloneNode());

// Append the submit button to the form
form.appendChild(s);
form.appendChild(s2);
outer.appendChild(form);

//document.getElementsByTagName("body").appendChild(form);
//down.appendChild(form);
script_tag.before(outer);
// First API call for table location
function updateTableLocation() {
  const xhttp_location = new XMLHttpRequest();
  xhttp_location.onload = function () {
    const res = JSON.parse(this.response);
    document.getElementById("table_location").innerHTML = res.html;
  };

  // Get the selected venue ID or default to 2
  let id = document.getElementById("select_venue").value;
  let res_id = id ? id : 2;

  xhttp_location.open("GET", web_url + "/public/api/restaurant-table-areas-ext/" + res_id, true);
  xhttp_location.setRequestHeader("Content-Type", "application/json");
  xhttp_location.withCredentials = false;
  xhttp_location.send();
}

// Second API call for select venue
const xhttp_venue = new XMLHttpRequest();
// xhttp_venue.onload = function () {
//   res = JSON.parse(this.response);
//   document.getElementById("select_venue").innerHTML = res.html;
//   document.getElementById("select_venue").addEventListener("change", updateTableLocation);

//   // Call updateTableLocation to set the table location for the initial value
//   updateTableLocation();
// }
xhttp_venue.open("GET", web_url + "/public/api/restaurant-venue/" + id, true);
xhttp_venue.setRequestHeader("Content-Type", "application/json");
xhttp_venue.withCredentials = false;
xhttp_venue.send();
// function getResDetails() {
//   const xhttp_venue = new XMLHttpRequest();
//   xhttp_venue.onload = function () {
//     res = JSON.parse(this.response);
//     document.getElementById("select_venue").innerHTML = res.html;
//     document.getElementById("select_venue").addEventListener("change", updateTableLocation);

//     // Call updateTableLocation to set the table location for the initial value
//     updateTableLocation();
//     getrestaurantsetting()
//   }
//   xhttp_venue.open("GET", web_url + "/public/store-owner/store/restaurant-venue", true);
//   xhttp_venue.setRequestHeader("Content-Type", "application/json");
//   xhttp_venue.withCredentials = false;
//   xhttp_venue.send();
// }

// Function to send data and create Stripe token
function send_data() {
  showLoader()
  stripe.createToken(card).then(function (result) {
    console.log('Stripe token creation result:', result);
    if (result.error) {
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
    } else {
      console.log('Stripe token:', result.token.id);
      var hiddenInput = document.createElement('input');
      hiddenInput.setAttribute('type', 'hidden');
      hiddenInput.setAttribute('name', 'stripeToken');
      hiddenInput.setAttribute('id', 'stripeToken'); // Add ID to the hidden input
      hiddenInput.setAttribute('value', result.token.id);
      form.appendChild(hiddenInput);

      var currencyInput = document.createElement('input');
      currencyInput.setAttribute('type', 'hidden');
      currencyInput.setAttribute('name', 'currency');
      currencyInput.setAttribute('value', 'AUD');
      form.appendChild(currencyInput);

      form_submit(); // Call form_submit after appending hidden inputs
    }
  }).catch(function (error) {
    hideLoader()
    console.error('Error creating Stripe token:', error);
  });
}

function form_submit() {

  let restaurant_id = document.getElementById("select_venue").value;
  let stripeTokenElement = document.getElementById("stripeToken");
  let stripeTokenValue = stripeTokenElement ? stripeTokenElement.value : null;
  let stripeToken = stripeTokenValue;

  let data = {
    no_of_seats: document.getElementById("no_of_seats").value,
    booking_date: document.getElementById("booking_date").value,
    area_id: document.getElementById("table_location").value,
    mobile_number: document.getElementById("mobile_number").value,
    email_address: document.getElementById("email_address").value,
    first_name: document.getElementById("first_name").value,
    last_name: document.getElementById("last_name").value,
    dob: document.getElementById("dob").value,
    comment: document.getElementById("comments").value,
    stripeToken: stripeToken,
    booking_time: document.querySelector('input[name="booking_time"]:checked').value
  };

  const xhttp = new XMLHttpRequest();
  xhttp.onload = function () {
    outer.scrollTop = 0;
    let res = JSON.parse(this.response);
    let color = "red";
    if (res.message == "Booking Saved")
      color = "green";
    let modalHtml = `
       <div id="bookingModal" tabindex="-1" style="display: block; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0.4);">
         <div style="background-color: white; margin: 15% auto; padding: 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); width: 80%; max-width: 500px; text-align: center;">
             <span style="color: ${res.message == "Booking Saved" ? "green" : "red"}; font-size: 20px; font-weight: bold;">${res.message}</span>
         </div>
     </div>
       `;

    document.getElementById("booking_form_ozeats").innerHTML = modalHtml;
    document.getElementById("bookingModal").focus();
    setTimeout(() => window.location.reload(), 5000);
  }

  xhttp.open("POST", web_url + "/public/api/new-booking/" + restaurant_id, false);
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(JSON.stringify(data));
}


function showLoader() {
  var loader = document.createElement('div');
  loader.setAttribute('id', 'loader');
  loader.textContent = 'Loading...';
  loader.style.textAlign = 'center';
  form.appendChild(loader); // Assuming 'form' is the ID of your form element
}

// Function to hide loader
function hideLoader() {
  var loader = document.getElementById('loader');
  if (loader) {
    loader.parentNode.removeChild(loader);
  }
}
// window.addEventListener('load', function(event) {   
function timeval() {
  const xhttp1 = new XMLHttpRequest();
  xhttp1.onload = function () {
    res = JSON.parse(this.response);
    document.getElementById("booking_time_btn").innerHTML =
      res.html;
  }
  let id = document.getElementById("select_venue").value;
  let restaurant_id = id ? id : 2;
  booking_date = document.getElementById("booking_date").value;
  xhttp1.open("GET", web_url + "/public/api/store/shift-timing-ext/" + restaurant_id + "/" + booking_date, false);
  xhttp1.setRequestHeader("Content-Type", "application/json");
  //xhttp1.setRequestHeader("Access-Control-Allow-Origin", "*");
  xhttp1.send();
}


function add_remove_class(event) {
  var booking_time_txt = document.querySelectorAll(".booking_time_text");
  for (var i = 0; i < booking_time_txt.length; i++) {
    var cards = booking_time_txt[i];
    cards.classList.remove("active");
  }
  event.classList.add("active");
  event.querySelector('.booking_time_input').setAttribute("checked", "checked");
}

function check_max_pax() {
  var no_of_pax = document.getElementById("no_of_seats").value;
  if (no_of_pax > 0) {
    let id = document.getElementById("select_venue").value;
    let restaurant_id = id ? id : 2;
    let data = {
      no_of_pax: no_of_pax,
      restaurant_id: restaurant_id
    }
    var depositCovers = parseInt(localStorage.getItem('deposit_covers_webform')) ? parseInt(localStorage.getItem('deposit_covers_webform')) : 0;

    // Check if the new value is less than depositCovers
    if (no_of_pax >= depositCovers) {
      localStorage.setItem('payment_gateway_webform', true);
    } else {
      localStorage.setItem('payment_gateway_webform', false);
    }
    const xhttp2 = new XMLHttpRequest();
    xhttp2.onload = function () {
      outer.scrollTop = 0;
      ele = document.getElementById("no_of_pax_er");
      res = JSON.parse(this.response);
      if (res.success == false) {
        ele.innerHTML = res.message;
        ele.style.display = "block";
      } else {
        ele.innerHTML = res.message;
        ele.style.display = "none";
      }
    }
    xhttp2.open("POST", web_url + "/public/api/check-max-pax", false);
    xhttp2.setRequestHeader("Content-Type", "application/json");
    xhttp2.send(JSON.stringify(data));
  }
}
function getrestaurantsetting() {
  const ressetting = new XMLHttpRequest();

  ressetting.onload = function () {
    // Handle successful response
    const response = JSON.parse(ressetting.responseText);
    if (response.restaurant_settings.enable_deposit != null) {
      var depositCovers = response.restaurant_settings.deposit_covers;
      var deposit_amount_per_cover = response.restaurant_settings.deposit_amount_per_cover;
      // Store deposit_covers in localStorage
      localStorage.setItem('deposit_covers_webform', depositCovers);
      localStorage.setItem('deposit_amount_per_cover_webform', deposit_amount_per_cover)
    }
    else {
      alert("Please Enable deposit for this Restaurant");

    }
    // Example: Update UI or process response data
    console.log(response); // Log the response for debugging

  };

  ressetting.onerror = function () {
    // Handle network errors
    console.error('Request failed');
  };

  let id = document.getElementById("select_venue").value;
  console.log("id======", id)
  let res_id = id ? id : 2;
  ressetting.open("GET", web_url + "/public/api/restaurantbook/settings/" + res_id, true); // Asynchronous request
  ressetting.setRequestHeader("Content-Type", "application/json");
  ressetting.send();
}
function getrestaurantstripekey() {
  const resstripekey = new XMLHttpRequest();

  resstripekey.onload = function () {
    // Handle successful response
    const response = JSON.parse(resstripekey.responseText);
    var stripe_public_key = response.restaurant.stripe_public_key;
    // Store deposit_covers in localStorage
    if (response.restaurant.stripe_public_key != null) {
      localStorage.setItem('stripe_public_key_webform', stripe_public_key);
    }
    else {
      alert("Please provide Stripe Api key")
    }
    // Example: Update UI or process response data
    console.log(response); // Log the response for debugging

  };

  resstripekey.onerror = function () {
    // Handle network errors
    console.error('Request failed');
  };

  let id = document.getElementById("select_venue").value;
  let res_id = id ? id : 2;
  resstripekey.open("GET", web_url + "/public/api/restaurant/settings/" + res_id, true); // Asynchronous request
  resstripekey.setRequestHeader("Content-Type", "application/json");
  resstripekey.send();
}
paymentSection = document.getElementById('payment-element');
paymentSection.style.display = 'none';
function button_click_first() {
  var paymentSection = document.getElementById('payment-element');
  var paymentGateway = localStorage.getItem('payment_gateway_webform') === 'true';
  showLoader()
  if (paymentGateway) {
    hideLoader()
    paymentSection.style.display = 'block';
    document.getElementById('first').style.display = 'none'; // Hide the first button
    document.getElementById('second').style.display = 'block'; // Show the second button
    stripe = Stripe(localStorage.getItem('stripe_public_key_webform')); // Initialize stripe
    var elements = stripe.elements();

    var style = {
      hidePostalCode: true,
      base: {
        fontSize: '25px',
        color: '#32325d',
      },
    };

    card = elements.create('card', { hidePostalCode: true, style: style });
    card.mount('#payment-element');
  } else {
    paymentSection.style.display = 'none'; // Hide the payment section
    form_submit()
  }
}

MN.addEventListener('click', dosomething, false);
EID.addEventListener('click', dosomething, false);
FIN.addEventListener('click', dosomething, false);
LAN.addEventListener('click', dosomething, false);
DOB.addEventListener('click', dosomething, false);
CM.addEventListener('click', dosomething, false);
function dosomething() {
  booking_date = document.getElementById("booking_date").value;
  if (booking_date == null || booking_date == '')
    alert('Please choose booking date first');
}

//                    function validateMobile(event, value) {
//
//                        var key = window.event ? event.keyCode : event.which;
//                        var cc = value.substring(0,2);
//                        console.log(cc + ' ' + value.length);
//                        if(value.length == 2 && cc == '04') {
//                            if (event.keyCode == 9 || event.keyCode == 13) {
//                                return true;
//                            } else if ( (key >= 48 && key <= 57) || (key >= 96 && key <= 105) ) {
//                                return true;
//                            } else return false;
//                        } else if(value.length > 2 && cc == '04') {
//                            if (event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 9
//                            || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 13) {
//                                return true;
//                            } else if ( (key >= 48 && key <= 57) || (key >= 96 && key <= 105) ) {
//                                return true;
//                            } else return false;
//                        } else return false;
//                    }
//                