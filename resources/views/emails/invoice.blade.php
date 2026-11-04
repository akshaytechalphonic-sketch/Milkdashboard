<!DOCTYPE html>
<html>

    <head>
        <title>Milk Delivery Invoice</title>
    </head>

    <body>
        <p>Dear {{ $invoice->customer->name }},</p>
        <p>Please find attached your milk delivery invoice for {{ $invoice->month }}.</p>
        <p>Total Amount: ₹{{ $grandTotal }}</p>
        <p>Thank you,<br />Gir Govind A2 milk</p>
    </body>

</html>