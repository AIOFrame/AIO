// Get API Key
let STRIPE_PUBLISHABLE_KEY = $('[data-stripe-public-key]').data('stripe-public-key');

// Create an instance of the Stripe object and set your publishable API key
const stripe = Stripe(STRIPE_PUBLISHABLE_KEY);

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

/*
function init_subscription(e) {
    setLoading(true);

    let plan_id = $('[data-key=plan]').val();
    let customer_name = $('[data-key=name]').val();
    let customer_email = $('[data-key=email]').val();

    // Post the subscription info to the server-side script
    fetch( location.href, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ request_type: 'create_customer_subscription', plan_id: plan_id, name: customer_name, email: customer_email }),
    })
    .then(function(response){
        console.log(response);
        response.json();
    }).then(function(data){
        console.log(data);
        if (data.subscriptionId && data.clientSecret) {
            paymentProcess(data.subscriptionId, data.clientSecret, data.customerId);
        } else {
            notify(data.error);
        }
        setLoading(false);
    })
    .catch(console.error);
}
*/


function process_payment( d ){

    let subscriptionId;
    let clientSecret;
    let customerId;
    if( d[0] === 1 ) {
        notify('Subscription created with Stripe, proceeding to process payment!');
        let data = JSON.parse( d[1] );
        console.log(data);
        subscriptionId = data['subscription_id'];
        clientSecret = data['client_secret'];
        customerId = data['customer_id'];
    }
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
    }).then(function( r ) {
        if( r.error ) {
            notify( r.error.message );
            setProcessing(false);
            setLoading(false);
        } else {
            post( $('#payment_response').data('action'), { subscription_id: subscriptionId, customer_id: customerId, plan_id: plan_id, payment_intent: r.paymentIntent }, 2, 2 );
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
        //$(subscrFrm).addClass('dn');
        //$('#frmProcess').removeClass('dn');
    } else {
        //$(subscrFrm).removeClass('dn');
        //$('#frmProcess').addClass('dn');
    }
}