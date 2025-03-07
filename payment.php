<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's pending bills
$stmt = $pdo->prepare("SELECT ad_billing.*, ads.title FROM ad_billing JOIN ads ON ad_billing.ad_id = ads.id WHERE ad_billing.user_id = ? AND ad_billing.status = 'pending'");
$stmt->execute([$_SESSION['user_id']]);
$bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total amount due
$total_amount = 0;
foreach ($bills as $bill) {
    $total_amount += $bill['amount'];
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey('YOUR_STRIPE_SECRET_KEY');

    try {
        // Create a Stripe payment intent
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $total_amount * 100, // Amount in cents
            'currency' => 'usd',
            'payment_method' => $_POST['stripePaymentMethodId'],
            'confirm' => true,
            'return_url' => 'http://localhost:8000/payment_success.php',
        ]);

        // Mark bills as paid
        $stmt = $pdo->prepare("UPDATE ad_billing SET status = 'paid' WHERE user_id = ? AND status = 'pending'");
        $stmt->execute([$_SESSION['user_id']]);

        $_SESSION['success'] = "Payment successful!";
        header("Location: payment_success.php");
        exit();
    } catch (\Stripe\Exception\ApiErrorException $e) {
        $_SESSION['error'] = "Payment failed. Please try again.";
    }
}
?>

<h1 class="text-center mb-4">Payment</h1>

<!-- Display success or error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Display Pending Bills -->
<?php if (empty($bills)): ?>
    <p class="text-center">No pending bills.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ad Title</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bills as $bill): ?>
                <tr>
                    <td><?php echo htmlspecialchars($bill['title']); ?></td>
                    <td>$<?php echo $bill['amount']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><strong>Total Amount Due:</strong> $<?php echo $total_amount; ?></p>

    <!-- Stripe Payment Form -->
    <form id="payment-form">
        <div id="stripe-element"></div>
        <button id="submit-button" class="btn btn-primary">Pay Now</button>
    </form>

    <!-- Include Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('YOUR_STRIPE_PUBLIC_KEY');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#stripe-element');

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                alert(error.message);
            } else {
                // Submit the payment method ID to the server
                fetch('payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        stripePaymentMethodId: paymentMethod.id,
                    }),
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'payment_success.php';
                        } else {
                            alert('Payment failed. Please try again.');
                        }
                    });
            }
        });
    </script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>