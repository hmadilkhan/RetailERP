<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Your Kashees Jewellery Order is Ready for Delivery - Track Your Shipment</title>
</head>

<body>
    <p>Dear {{ $customerName }},</p>

    <p>Great news! Your order {{ $orderNumber }} is now ready for delivery. It has been carefully packed and handed
        over to our courier partner.</p>

    <p><strong>Order Details:</strong></p>
    <ul>
        <li><strong>Items Ordered:</strong> </li>
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
        <li><strong>Total Amount:</strong> {{ $orderAmount }}</li>
    </ul>

    <p><strong>If you have any questions or need assistance with your delivery, feel free to contact us</strong></p>

    <p>For further assistance, reach out to us:</p>
    <ul>
        <li>Email: {{ $supportEmail }}</li>
        <li>Phone: {{ $supportPhoneNumber }}</li>
    </ul>

    <p>Thank you for choosing Kashees Jewellery. We hope you love your purchase and look forward to serving you again
        soon!</p>

    <p>Warm regards,</p>
    <p>The Kashees Jewellery Team</p>

    <p><a href="{{ $websiteUrl }}">{{ $websiteUrl }}</a></p>
    <p>Follow us on social media:</p>
    <ul>
        <li><a href="{{ $facebookUrl }}">Facebook</a></li>
        <li><a href="{{ $instagramUrl }}">Instagram</a></li>
        <li><a href="{{ $twitterUrl }">Twitter</a></li>
    </ul>
</body>

</html>
