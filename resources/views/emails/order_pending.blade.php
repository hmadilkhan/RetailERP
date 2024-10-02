<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Pending</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #d4af37;
            /* Kashees Jewelry's Gold color */
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-content {
            padding: 20px;
            color: #333;
        }

        .email-content h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .order-details p {
            margin: 8px 0;
            line-height: 1.6;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border-top: 1px solid #ddd;
        }

        .email-footer p {
            margin: 5px 0;
            color: #666;
        }

        .email-footer a {
            color: #d4af37;
            text-decoration: none;
        }

        .btn {
            background-color: #d4af37;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        @media only screen and (max-width: 600px) {

            .email-content,
            .email-footer {
                padding: 15px;
            }

            .email-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <img src="{{ asset('storage/images/company/' . $logo) }}" alt="Kashees Jewellery Logo" width="300"
                height="150">
            <h1>Kashees Jewellery</h1>
        </div>

        <!-- Content Section -->
        <div class="email-content">
            <h2>Dear {{ $customerName }},</h2>
            <p>Thank you for shopping with Kashees Jewellery! We have received your order
                <strong>{{ $orderNumber }}</strong>, placed on
            </p><strong>{{ $orderDate }}</strong>

            <!-- Order Details Section -->
            <div class="order-details">
                <p><strong>Order Details:</strong></p>
                <table>
                    @foreach ($itemsList as $key => $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ number_format($item->total_qty, 0) }}</td>
                            <td>{{ session('currency') . ' ' . number_format(($item->item_price != '' ? $item->item_price : 1) * $item->total_qty, 0) }}
                            </td>
                        </tr>
                    @endforeach
                </table>
                <p><strong>Total Amount:</strong> {{ $orderAmount }}</p>
                <p><strong>Payment Method:</strong> {{ $orderPaymentMethod }}</p>
            </div>

            <p><strong>Next Steps :</strong></p>

            <p>Your order is currently in pending status. To proceed with processing your order, you will receive a
                confirmation call from our team shortly.
                During the call, we will request an advance payment to confirm and secure your order.</p>

            <p>Once the advance payment is confirmed, your order will move to processing, and we will notify you with
                further updates, including the expected delivery timeline..</p>

            <p>If you have any questions or require further assistance, feel free to reach out to us:</p>
            <p>Email: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a><br>
                Phone: {{ $supportPhoneNumber }}</p>
            
            <p>We look forward to confirming your order and delivering our beautiful jewellery to you soon!</p>
            
            <p>Thank you for choosing Kashees Jewellery.</p>
        </div>

        <!-- Footer Section -->
        <div class="email-footer">
            <p>Warm regards,<br>The Kashees Jewellery Team</p>
            <p><a href="{{ $websiteUrl }}">{{ $websiteUrl }}</a></p>
            <p>Follow us:
                <a href="{{ $facebookUrl }}">Facebook</a> |
                <a href="{{ $instagramUrl }}">Instagram</a> |
                <a href="{{ $twitterUrl }}">Twitter</a>
            </p>
        </div>
    </div>
</body>

</html>
