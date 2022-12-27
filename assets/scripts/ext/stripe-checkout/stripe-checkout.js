let elements;
let stripe;
let payment_intent_id;
let client_secret;
let stripe_form;
let stripe_wrap;
let checkout_inputs;
document.addEventListener("DOMContentLoaded", function(e) {
    stripe_form = document.querySelector("#stripe_form");
    stripe_wrap = document.querySelector("#stripe_payment_wrapper");
    stripe = Stripe( stripe_key );
    const clientSecretParam = new URLSearchParams(window.location.search).get('payment_intent_client_secret');
    setProcessing(true);
    if(!clientSecretParam){
        setProcessing(false);
        post( payment_intent_action, { 'data': stripe_data }, '', '', '', '', 'initialize');
    }
    checkStatus();
    if( stripe_form !== null ) {
        stripe_form.addEventListener("submit", handleSubmit);
    }
})

function initialize(r) {
    payment_intent_id = r.id;
    client_secret = r.clientSecret;
    elements = stripe.elements({ clientSecret: client_secret, appearance: appearance });
    const paymentElement = elements.create("payment");
    paymentElement.mount("#checkout_inputs");
}

// Card form submit handler
async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);
    post( create_customer_action, { payment_intent_id: payment_intent_id, data: stripe_data }, '', '', '', '', 'handle_init');
}

async function handle_init(r) {
    const {error} = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: window.location.href + '?customer_id=' + r.customer_id,
        },
    });
    if (error.type === "card_error" || error.type === "validation_error") {
        notify(error.message, 8, 'error', 'report');
    } else {
        notify('An unexpected error prevent from payment processing!', 8, 'red', 'report');
    }
    setLoading(false);
}

// Fetch the PaymentIntent status after payment submission
async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get('payment_intent_client_secret');
    const customerID = new URLSearchParams(window.location.search).get('customer_id');
    if (!clientSecret) {
        return;
    }
    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
    if (paymentIntent) {
        switch (paymentIntent.status) {
            case "succeeded":
                post(process_payment_response_backend, { payment_intent: paymentIntent, customer_id: customerID },'','','','',process_payment_response_frontend);
                showMessage("Your payment is processed successfully.");
                break;
            case "processing":
                showMessage("Your payment is processing.");
                setTimeout(function(){ location.reload() },5000);
                break;
            case "requires_payment_method":
                showMessage("Your payment was not successful, please try again.");
                setTimeout(function(){ location.reload() },5000);
                break;
            default:
                showMessage("Something went wrong.");
                setTimeout(function(){ location.reload() },5000);
                break;
        }
    } else {
        showMessage("Something went wrong.");
        setTimeout(function(){ location.reload() },5000);
    }
}

function process_response(r) {
    console.log(r);
    console.log(r.payment_txn_id);
}


// Display message
function showMessage(messageText) {
    const messageContainer = document.querySelector("#payment_response");
    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;
    setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageText.textContent = "";
    }, 5000);
}

// Show a spinner on payment submission
function setLoading(isLoading) {
    if (isLoading) {
        // Disable the button and show a spinner
        document.querySelector("#stripe_pay_btn").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#stripe_pay_btn_text").classList.add("hidden");
    } else {
        // Enable the button and hide spinner
        document.querySelector("#stripe_pay_btn").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#stripe_pay_btn_text").classList.remove("hidden");
    }
}

function setProcessing(isProcessing) {
    if( stripe_form !== null ) {
        if (isProcessing) {
            stripe_form.classList.add('dn');
            stripe_form.classList.remove('load');
        } else {
            stripe_form.classList.remove('dn');
            stripe_form.classList.add('load');
        }
    }
}