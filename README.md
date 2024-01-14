# Laravel Book Store Backend App

Demo step:

1. Clone the repository.

```bash
git clone https://www.github.com/aufarijaal/online-book-store-backend
```

2. Go to cloned folder

```bash
cd online-book-store-backend
```

3. Install dependencies

```bash
composer install
```

4. Activate the laravel sail

```bash
./vendor/bin/sail up -d
```

5. Migrate and seed the database

```bash
./vendor/bin/sail artisan migrate --seed
```

6. Generate `ngrok` public url, copy the `uri` and then put it in the Midtrans configuration.

```bash
curl localhost:4040/api/tunnels
```
