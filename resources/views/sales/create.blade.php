<h1>Add Sale</h1>
<form action="{{ route('sales.store') }}" method="POST">
    @csrf
    Product:
    <select name="product_id">
        @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->stock }} left)</option>
        @endforeach
    </select><br>
    Quantity: <input type="number" name="quantity" min="1"><br>
    <button type="submit">Sell</button>
</form>
<a href="{{ route('sales.index') }}">Back</a>
