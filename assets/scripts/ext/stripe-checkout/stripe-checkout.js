let elements;
let stripe;
let payment_intent_id;
let clientSecret;
let payment_form;
document.addEventListener("DOMContentLoaded", function(event) {
    let key = document.querySelector('#payment-form').getAttribute('data-stripe');
    stripe = Stripe( key );
    payment_form = document.querySelector("#payment-element");
    const clientSecretParam = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
    );
    setProcessing(true);
    if(!clientSecretParam){
        setProcessing(false);
        post( document.querySelector('#payment-form').getAttribute('data-pre'), { request_type:'create_payment_intent' }, '', '', '', '', 'init');
    }
    checkStatus();
    payment_form.addEventListener("submit", handleSubmit);
})

function init(r) {
    console.log(r);
    payment_intent_id = r.id;
    clientSecret = r.secret;
    const appearance = {
        theme: 'stripe',
        rules: {
            '.Label': {
                fontWeight: 'bold',
                textTransform: 'uppercase',
            }
        }
    };
    elements = stripe.elements({ clientSecret: clientSecret, appearance: appearance });
    const paymentElement = elements.create("payment");
    paymentElement.mount("#paymentElement");
}

// Card form submit handler
async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    let customer_name = document.getElementById("name").value;
    let customer_email = document.getElementById("email").value;

    post(document.querySelector('#payment-form').getAttribute('data-post'),
        {
            request_type: 'create_customer',
            payment_intent_id: payment_intent_id,
            name: customer_name,
            email: customer_email
        },
        0, 0, '', '', 'handle_init');
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
    const clientSecret = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
    );
    const customerID = new URLSearchParams(window.location.search).get(
        "customer_id"
    );
    if (!clientSecret) {
        return;
    }
    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
    if (paymentIntent) {
        switch (paymentIntent.status) {
            case "succeeded":
                post(
                    document.querySelector('#payment-form').getAttribute('payment'),
                    { request_type:'payment_insert', payment_intent: paymentIntent, customer_id: customerID },
                    '','','','','process_response');
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
    const messageContainer = document.querySelector("#paymentResponse");

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
        document.querySelector("#submitBtn").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#buttonText").classList.add("hidden");
    } else {
        // Enable the button and hide spinner
        document.querySelector("#submitBtn").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#buttonText").classList.remove("hidden");
    }
}

function setProcessing(isProcessing) {
    if (isProcessing) {
        payment_form.classList.add('dn');
        payment_form.classList.remove('load');
    } else {
        payment_form.classList.remove('dn');
        payment_form.classList.add('load');
    }
}