<h1>Add Product</h1>
<form action="{{ route('products.store') }}" method="POST">
    @csrf
    Name: <input type="text" name="name"><br>
    Price: <input type="number" name="price" step="0.01"><br>
    Stock: <input type="number" name="stock"><br>
    <button type="submit">Save</button>
</form>
<a href="{{ route('products.index') }}">Back</a>
