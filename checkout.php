<?php
session_start();

// Sample cart data (in production, fetch from database)
$cart_items = [
    ['id' => 1, 'name' => 'Blood Pressure Monitor', 'price' => 45.99, 'quantity' => 2],
    ['id' => 3, 'name' => 'Pulse Oximeter', 'price' => 29.99, 'quantity' => 1],
];

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 10;
$tax = $subtotal * 0.075;
$total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Hospice Medical Supplies</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Simplified Header for Checkout -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="index.php" class="flex items-center space-x-2">
                    <img class="w-28 h-auto md:w-32 mr-[-40px] ml-[-40px]"  src="images/Blue_Logo.png" alt="" srcset="">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">WEDEX</h1>
                        <p class="text-xs text-gray-500">Healthcare Services</p>
                    </div>
                </a>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2">
                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-gray-600">Secure Checkout</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Progress Steps -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-teal-600 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                    <span class="ml-2 font-medium text-teal-600">Shipping</span>
                </div>
                <div class="w-16 h-1 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">2</div>
                    <span class="ml-2 font-medium text-gray-600">Payment</span>
                </div>
                <div class="w-16 h-1 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">3</div>
                    <span class="ml-2 font-medium text-gray-600">Review</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form action="process_checkout.php" method="POST" id="checkout-form">
                    
                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Contact Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                                <input 
                                    type="email" 
                                    name="email"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="your.email@example.com"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                                <input 
                                    type="tel" 
                                    name="phone"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="+234 800 000 0000"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Shipping Address</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                                <input 
                                    type="text" 
                                    name="first_name"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                                <input 
                                    type="text" 
                                    name="last_name"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Street Address *</label>
                                <input 
                                    type="text" 
                                    name="address"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="House number and street name"
                                >
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Apartment, suite, etc. (optional)</label>
                                <input 
                                    type="text" 
                                    name="address2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                                <input 
                                    type="text" 
                                    name="city"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">State *</label>
                                <select 
                                    name="state"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                                    <option value="">Select State</option>
                                    <option value="Lagos">Lagos</option>
                                    <option value="Abuja">Abuja</option>
                                    <option value="Rivers">Rivers</option>
                                    <option value="Kano">Kano</option>
                                    <option value="Oyo">Oyo</option>
                                    <option value="Delta">Delta</option>
                                    <option value="Ogun">Ogun</option>
                                    <option value="Kaduna">Kaduna</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Postal Code</label>
                                <input 
                                    type="text" 
                                    name="postal_code"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Shipping Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center justify-between p-4 border-2 border-teal-600 rounded-lg cursor-pointer bg-teal-50">
                                <div class="flex items-center">
                                    <input type="radio" name="shipping_method" value="standard" checked class="w-4 h-4 text-teal-600">
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-800">Standard Shipping</p>
                                        <p class="text-sm text-gray-600">3-5 business days</p>
                                    </div>
                                </div>
                                <span class="font-bold text-gray-800">₦10.00</span>
                            </label>
                            <label class="flex items-center justify-between p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-teal-300">
                                <div class="flex items-center">
                                    <input type="radio" name="shipping_method" value="express" class="w-4 h-4 text-teal-600">
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-800">Express Shipping</p>
                                        <p class="text-sm text-gray-600">1-2 business days</p>
                                    </div>
                                </div>
                                <span class="font-bold text-gray-800">₦25.00</span>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Payment Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-teal-600 rounded-lg cursor-pointer bg-teal-50">
                                <input type="radio" name="payment_method" value="card" checked class="w-4 h-4 text-teal-600">
                                <span class="ml-3 font-semibold text-gray-800">Credit/Debit Card</span>
                                <div class="ml-auto flex space-x-2">
                                    <div class="w-10 h-6 bg-blue-600 rounded flex items-center justify-center text-white text-xs font-bold">VISA</div>
                                    <div class="w-10 h-6 bg-red-600 rounded flex items-center justify-center text-white text-xs font-bold">MC</div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-teal-300">
                                <input type="radio" name="payment_method" value="bank" class="w-4 h-4 text-teal-600">
                                <span class="ml-3 font-semibold text-gray-800">Bank Transfer</span>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-teal-300">
                                <input type="radio" name="payment_method" value="cod" class="w-4 h-4 text-teal-600">
                                <span class="ml-3 font-semibold text-gray-800">Cash on Delivery</span>
                            </label>
                        </div>

                        <!-- Card Details (shown when card is selected) -->
                        <div id="card-details" class="mt-6 pt-6 border-t">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Card Number *</label>
                                    <input 
                                        type="text" 
                                        name="card_number"
                                        placeholder="1234 5678 9012 3456"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cardholder Name *</label>
                                    <input 
                                        type="text" 
                                        name="card_name"
                                        placeholder="John Doe"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    >
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date *</label>
                                        <input 
                                            type="text" 
                                            name="expiry"
                                            placeholder="MM/YY"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">CVV *</label>
                                        <input 
                                            type="text" 
                                            name="cvv"
                                            placeholder="123"
                                            maxlength="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Order Notes (Optional)</h2>
                        <textarea 
                            name="order_notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            placeholder="Notes about your order, e.g. special delivery instructions"
                        ></textarea>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6 pb-6 border-b">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex items-center space-x-4">
                            <div class="bg-gray-200 w-16 h-16 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-gray-400 text-xs">Img</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 text-sm"><?php echo $item['name']; ?></p>
                                <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <p class="font-semibold text-gray-800">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Totals -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold">₦<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-semibold">₦<?php echo number_format($shipping, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (VAT 7.5%)</span>
                            <span class="font-semibold">₦<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold text-gray-800">
                                <span>Total</span>
                                <span>₦<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" form="checkout-form" class="w-full bg-teal-600 text-white py-4 rounded-lg font-semibold hover:bg-teal-700 transition mb-4">
                        Place Order
                    </button>

                    <!-- Security Badges -->
                    <div class="text-center text-sm text-gray-600">
                        <div class="flex items-center justify-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Secure SSL Encrypted Payment</span>
                        </div>
                        <p class="text-xs">Your payment information is protected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
        // Show/hide card details based on payment method
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const cardDetails = document.getElementById('card-details');
        
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'card') {
                    cardDetails.style.display = 'block';
                } else {
                    cardDetails.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>