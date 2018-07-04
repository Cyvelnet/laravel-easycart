<table class="table">

    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cart->items() as $index => $row)

            <tr>
                <td>{{ $index +1 }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->qty }}</td>
                <td>{{ $row->subtotal() }}</td>
            </tr>

        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Subtotal</td>
            <td>{{ $cart->subtotal() }}</td>
        </tr>
        <tr>
            <td colspan="3">Total</td>
            <td>{{ $cart->total() }}</td>
        </tr>
    </tfoot>

</table>    