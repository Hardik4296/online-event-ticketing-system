const calculateTotal = () => {
    let total = 0;

    // Calculate total amount based on ticket quantity and price
    document.querySelectorAll('.quantity-input').forEach(input => {
        const quantity = parseInt(input.value) || 0;
        const priceText = input
            .closest('.quantity-content')
            .querySelector('.left-content p')
            .textContent.replace('$', '')
            .replace(',', '');
        const price = parseFloat(priceText);
        total += quantity * price;
    });

    // Format the total amount as currency
    const formattedTotal = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(total);

    document.getElementById('total-amount').textContent = formattedTotal;
    document.getElementById('payment-amount').textContent = formattedTotal;
};

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Stripe and elements
    $('#payment-form').hide();
    const stripe = Stripe(STRIPE_KEY);
    const elements = stripe.elements();

    // Create and mount the card Element
    const card = elements.create('card');
    card.mount('#card-element');

    // Function to handle increment/decrement of ticket quantity
    const adjustQuantity = (button, isIncrement) => {
        const ticketId = button.dataset.ticketId;
        const input = document.querySelector(`.quantity-input[data-ticket-id="${ticketId}"]`);
        let currentValue = parseInt(input.value) || 0;

        if (isIncrement && currentValue < 10) {
            currentValue++;
        } else if (!isIncrement && currentValue > 0) {
            currentValue--;
        }

        input.value = currentValue;
        calculateTotal();
    };

    // Add event listeners for + and - buttons
    document.querySelectorAll('.plus').forEach(button => {
        button.addEventListener('click', () => adjustQuantity(button, true));
    });

    document.querySelectorAll('.minus').forEach(button => {
        button.addEventListener('click', () => adjustQuantity(button, false));
    });

    // Handle manual quantity input changes
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', () => {
            let currentValue = parseInt(input.value) || 0;
            if (currentValue > 10) input.value = 10;
            else if (currentValue < 0) input.value = 0;
            calculateTotal();
        });
    });

    // Function to gather ticket data
    function getTicketData() {
        const ticketData = [];
        document.querySelectorAll('.quantity-input').forEach(input => {
            const ticketId = input.dataset.ticketId;
            const quantity = parseInt(input.value) || 0;
            if (quantity > 0) {
                ticketData.push({
                    ticket_id: ticketId,
                    quantity,
                });
            }
        });

        if (ticketData.length === 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Please select at least one ticket.',
                icon: 'error',
                confirmButtonText: 'Ok',
            });
            return null;
        }

        return ticketData;
    }

    // Handle "Purchase Tickets" button click
    const purchaseButton = document.getElementById('purchase-tickets');
    purchaseButton.addEventListener('click', (e) => {
        e.preventDefault();
        const ticketData = getTicketData();
        if (ticketData) {
            $('#payment-form').show();
            $('#paymentModal').modal('show');
        }
    });

    // Handle payment form submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        submitButton.disabled = true;
        submitButton.innerText = 'Processing... Please wait...';

        const ticketData = getTicketData();
        if (!ticketData) {
            resetSubmitButton();
            return;
        }

        try {
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
            });

            if (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'Ok',
                });
                resetSubmitButton();
                return;
            }

            // Send ticket data and payment method to the backend to create the payment intent
            const response = await $.ajax({
                url: PURCHASE_TICKET_API_URL,
                method: 'POST',
                data: {
                    tickets: ticketData,
                    payment_method: paymentMethod.id,
                    event_id: $('#event_id').val(),
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
            });

            if (response.client_secret) {
                const result = await stripe.confirmCardPayment(response.client_secret, {
                    payment_method: paymentMethod.id,
                });

                if (result.error) {
                    Swal.fire({
                        title: 'Error!',
                        text: result.error.message,
                        icon: 'error',
                        confirmButtonText: 'Ok',
                    });
                } else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {

                    const confirmResponse = await $.ajax({
                        url: CONFIRM_PAYMENT_API_URL, // Route pointing to confirmPayment method
                        method: 'POST',
                        data: {
                            payment_intent_id: result.paymentIntent.id,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        }
                    });

                    if (confirmResponse.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Payment successful!',
                            icon: 'success',
                            confirmButtonText: 'Ok',
                        }).then(() => {
                            window.location.href = `${TICKET_DETAILS_URL}/${confirmResponse.group_id}`;
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to update payment status. Please contact support.',
                            icon: 'error',
                        });
                    }
                }
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Ok',
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An unknown error occurred.',
                icon: 'error',
                confirmButtonText: 'Ok',
            });
        } finally {
            resetSubmitButton();
        }
    });

    function resetSubmitButton() {
        submitButton.disabled = false;
        submitButton.innerText = 'Pay';
    }
});
