<form action="{{ route('login.attempt') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Email/Username</label>
        <input type="text" name="login" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button class="btn btn-primary w-100">Login</button>
</form>
