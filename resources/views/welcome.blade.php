<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: #fff; }
        .receipt-container { width: 300px; margin: 20px auto; padding: 15px; border: 1px solid #000; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .logo { text-align: center; margin-bottom: 20px; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header h2, .header p { margin: 0; padding: 0; }
        .order-details, .totals { margin-top: 20px; border-top: 1px dashed #000; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
        td { border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .warning { text-align: center; font-size: 0.8em; margin-top: 20px; }
        .totals p { margin: 5px 0; }
        .footer p { margin: 5px 0; font-size: 0.8em; }
    </style>
    <title>Receipt</title>
</head>
<body>
<div class="receipt-container">
    <div class="logo">
        <img src="path-to-your-logo.png" alt="ChicChicken Logo" style="width: 50px; height: auto;">
    </div>
    <div class="header">
        <h2>CHICK CHICKEN</h2>
        <p>123 Rich Quay<br>Zip: City<br>TVA (Numri TVSH): xxxxxx<br>Tel: 123 456 78 90</p>
    </div>
    <h3>ORDER RECEIPT</h3>
    <div class="order-info">
        <p><strong>Order No:</strong> 77</p>
        <p><strong>Type:</strong> Take-away</p>
        <p><strong>Locator Id:</strong> ##</p>
        <p><strong>E-kiosk Id:</strong> ####</p>
        <p><strong>Customer Name:</strong> Walking Customer (by default)</p>
    </div>
    <div class="order-details">
        <table>
            <thead>
            <tr>
                <th>QTY</th>
                <th>ITEM</th>
                <th class="text-right">PRICE</th>
                <th class="text-right">TOTAL</th>
            </tr>
            </thead>
            <tbody>
            <!-- Repeat this TR for each item -->
            <tr>
                <td>1</td>
                <td>Menu Wrap</td>
                <td class="text-right">13.90</td>
                <td class="text-right">13.90</td>
            </tr>
            <!-- ... other items ... -->
            </tbody>
        </table>
    </div>
    <div class="totals">
        <p><strong>Items:</strong> 06</p>
        <p><strong>Subtotal (incl VAT):</strong> 14.90</p>
        <p><strong>Discounts:</strong> 0.00</p>
        <p><strong>TOTAL IN CHF:</strong> 14.90</p>
        <p><strong>VAT C: 2.6%</strong> 0.38</p>
        <p><strong>Total without VAT:</strong> 14.52</p>
    </div>
    <div class="footer">
        <p>E-kiosk ID, Data (H.D.), du maji wwv, 01:57:48</p>
    </div>
    <div class="warning">
        <p>YOUR ORDER HAS NOT BEEN PAID OR PROCESSED YET!<br>
            Please continue to the next available Cashier, to finish payment and to
            receive your final receipt.
        </p>
        <p>This is not a fiscal receipt.</p>
    </div>
</div>
</body>
</html>
