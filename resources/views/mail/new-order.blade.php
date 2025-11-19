<div style="
  font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
  background-color: #f5f7fa;
  padding: 40px 0;
  color: #2d3436;
">
  <div style="
    max-width: 720px;
    margin: auto;
    background-color: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    overflow: hidden;
    border: 1px solid #eceff1;
  ">
    <!-- Header -->
    <h1 style="
      font-size: 28px;
      color: #fff;
      text-align: center;
      background: linear-gradient(90deg, #2563eb, #3b82f6);
      padding: 24px 0;
      margin: 0;
      letter-spacing: 0.5px;
    ">
      🛒 New Order Received
    </h1>

    <!-- Order Info -->
    <div style="padding: 30px;">
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 15px;">
        <tr>
          <th align="left" style="padding: 12px 10px; width: 40%; text-transform: uppercase; color: #64748b;">Order ID</th>
          <td style="padding: 12px 10px;">
            <a href="{{ env('ADMIN_URL') . '/app/orders/' . $order->id }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">
              #{{ $order->id }}
            </a>
          </td>
        </tr>
        <tr style="background-color: #f8fafc;">
          <th align="left" style="padding: 12px 10px; text-transform: uppercase; color: #64748b;">Order Status</th>
          <td style="padding: 12px 10px;">
            <span style="
              background-color: #e0f7ec;
              color: #16a34a;
              font-weight: 600;
              padding: 5px 12px;
              border-radius: 8px;
              text-transform: capitalize;
            ">
              {{ $order->status }}
            </span>
          </td>
        </tr>
        <tr>
          <th align="left" style="padding: 12px 10px; text-transform: uppercase; color: #64748b;">Total Price</th>
          <td style="padding: 12px 10px; font-weight: 700; color: #111827;">
            ${{ number_format($order->total_price, 2) }}
          </td>
        </tr>
        <tr style="background-color: #f8fafc;">
          <th align="left" style="padding: 12px 10px; text-transform: uppercase; color: #64748b;">Order Date</th>
          <td style="padding: 12px 10px;">{{ $order->created_at->format('M d, Y H:i') }}</td>
        </tr>
      </table>

      <!-- Items Table -->
      <h3 style="margin: 0 0 15px 0; color: #1e293b; font-size: 18px;">Order Items</h3>
      <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
        <thead>
          <tr style="background: linear-gradient(90deg, #2563eb, #3b82f6); color: white;">
            <th style="padding: 12px; text-align: left;">Image</th>
            <th style="padding: 12px; text-align: left;">Title</th>
            <th style="padding: 12px; text-align: left;">Price</th>
            <th style="padding: 12px; text-align: left;">Qty</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($order->items as $item)
            <tr style="border-bottom: 1px solid #e5e7eb; background-color: #fff;">
              <td style="padding: 12px;">
                <img src="{{ $item->product->image }}" alt="Product" style="width: 80px; border-radius: 10px; object-fit: cover; border: 1px solid #e5e7eb;">
              </td>
              <td style="padding: 12px; font-weight: 500;">{{ $item->product->title }}</td>
              <td style="padding: 12px;">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
              <td style="padding: 12px;">{{ $item->quantity }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
