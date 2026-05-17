<form action="{{ route('checkout.stripe', ['payment' => 'stripe']) }}" method="POST">
    @csrf
    <button class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3" type="submit">Checkout</button>
</form>
