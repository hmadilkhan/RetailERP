<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Canceled – Important Update on Your Kashees Jewellery Order</title>
</head>
<body>
    <p>Dear {{ $customerName }},</p>

    <p>We regret to inform you that your order {{ $orderNumber }}, placed on {{ $orderDate }}, has been canceled. This may have occurred due to one of the following reasons:</p>

    <ul>
        <li>Non-payment of the advance within the required time.</li>
        <li>Item(s) being out of stock at the time of processing.</li>
    </ul>

    <p>We understand this may be disappointing, and we sincerely apologize for any inconvenience caused.</p>

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

    <p>If you wish to place a new order or require assistance, please contact us. We are happy to help and can provide alternative product suggestions or resolve any concerns you may have.</p>

    <p>For further assistance, reach out to us:</p>
    <ul>
        <li>Email: {{ $supportEmail }}</li>
        <li>Phone: {{ $supportPhoneNumber }}</li>
    </ul>

    <p>Thank you for your understanding, and we hope to serve you in the future.</p>

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
