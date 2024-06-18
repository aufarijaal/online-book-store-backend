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

3. Generate key

```bash
php artisan key:genereate
```

4. Set up midtrans keys and the front end url in .env

```
MIDTRANS_SERVER_KEY="serverkey"
MIDTRANS_CLIENT_KEY="clientkey"
MIDTRANS_IS_PRODUCTION=false
FRONTEND_URL=urlhere # if not specified, it will use http://localhost:3000
```

5. Install dependencies

```bash
composer install
```

6. Migrate and seed the database

```bash
php artisan migrate --seed
```

7. Generate `ngrok` public url by running. ensure `ngrok` is installed.

```bash
ngrok http http://localhost:8000 # or adjust it to your laravel server port
```
