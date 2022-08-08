// Get API Key
let STRIPE_PUBLISHABLE_KEY = $('[data-stripe-public-key]').data('stripe-public-key');

// Create an instance of the Stripe object and set your publishable API key
const stripe = Stripe(STRIPE_PUBLISHABLE_KEY);

// Select subscription form element
const subscrFrm = $('#subscription_form');

// Attach an event handler to subscription form
subscrFrm.addEventListener("submit", handleSubscrSubmit);

let elements = stripe.elements();
let ir = $($('[type=text]')[0]);
let style = {
    base: {
        iconColor: ir.css('color'),
        border: ir.css('border'),
        height: ir.css('height'),
        borderRadius: ir.css('border-radius'),
        color: ir.css('color'),
        fontWeight: ir.css('font-weight'),
        fontFamily: ir.css('font-family'),
        fontSize: ir.css('font-size'),
        backgroundColor: ir.css('background-color'),
        letterSpacing: ir.css('letter-spacing'),
        fontSmoothing: 'antialiased',
        padding: ir.css('padding'),
    },
    invalid: {
        iconColor: 'firebrick',
        color: 'firebrick',
    },
};
let cardElement = elements.create('card', { style: style });
cardElement.mount('#card-element');

cardElement.on('change', function (event) {
    displayError(event);
});

function displayError(event) {
    if (event.error) {
        notify(event.error.message);
    }
}

function handleSubscrSubmit(e) {
    e.preventDefault();
    setLoading(true);

    let plan_id = $('[data-key=plan]').val();
    let customer_name = $('[data-key=name]').val();
    let customer_email = $('[data-key=email]').val();

    // Post the subscription info to the server-side script
    fetch( location.origin, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ request_type:'create_customer_subscription', plan_id: plan_id, name: customer_name, email: customer_email }),
    })
        .then(function(response){ response.json() })
        .then(function(data){
            if (data.subscriptionId && data.clientSecret) {
                paymentProcess(data.subscriptionId, data.clientSecret, data.customerId);
            } else {
                notify(data.error);
            }

            setLoading(false);
        })
        .catch(console.error);
}

function paymentProcess(subscriptionId, clientSecret, customerId){
    setProcessing(true);

    let plan_id = $('[data-key=plan]').val();
    let customer_name = $('[data-key=name]').val();

    // Create payment method and confirm payment intent.
    stripe.confirmCardPayment(clientSecret, {
        payment_method: {
            card: cardElement,
            billing_details: {
                name: customer_name,
            },
        }
    }).then(function(result) {
        if(result.error) {
            notify(result.error.message);

            setProcessing(false);
            setLoading(false);
        } else {
            // Successful subscription payment
            //console.log(result);
            // Post the transaction info to the server-side script and redirect to the payment status page
            fetch( location.origin, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ request_type:'payment_insert', subscription_id: subscriptionId, customer_id: customerId, plan_id: plan_id, payment_intent: result.paymentIntent }),
            })
                .then(function(response){ response.json() })
                .then(function(data) {
                    if (data.payment_id) {
                        alert(data.payment_id);
                        console.log(data.payment_id);
                        //window.location.href = 'payment-status.php?sid='+data.payment_id;
                    } else {
                        notify(data.error);

                        setProcessing(false);
                        setLoading(false);
                    }
                })
                .catch(console.error);
        }
    });
}

// Show a spinner on payment submission
function setLoading(isLoading) {
    if (isLoading) {
        // Disable the button and show a spinner
        $('#submit_button').attr('disabled',true);
        $('#spinner').removeClass('dn');
        $('#buttonText').addClass('dn');
    } else {
        // Enable the button and hide spinner
        $('#submit_button').attr('disabled',false);
        $('#spinner').addClass('dn');
        $('#buttonText').removeClass('dn');
    }
}

// Show a spinner on payment form processing
function setProcessing(isProcessing) {
    if (isProcessing) {
        $(subscrFrm).addClass('dn');
        $('#frmProcess').removeClass('dn');
    } else {
        $(subscrFrm).removeClass('dn');
        $('#frmProcess').addClass('dn');
    }
}