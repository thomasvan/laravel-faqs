# Laravel Learning

## Installation

```bash
composer global require laravel/installer
echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc
source ~/.bashrc
tee -a ~/.profile << SCRIPT
```

## Set PATH so it includes user's composer bin if it exists

```bash
if [ -d "\$HOME/.composer/vendor/bin" ] ; then
    PATH="\$HOME/.composer/vendor/bin:\$PATH"
fi
SCRIPT

source ~/.profile
laravel new faqs
```

## Command line tool

```bash
php artisan tinker
use Faker\Factory;
rtrim($faker->sentence(rand(5,10)),'.')
=> "Rem suscipit omnis voluptas corporis et"
```

Publish Tinker's configuration file

```bash
php artisan vendor:publish --provider="Laravel\Tinker\TinkerServiceProvider"
```

---

1. Initialize

    ```bash
    composer update
    composer install
    npm install
    ```

2. .env

    Configure DB info

    ```bash
    php artisan make:auth
    php artisan mirgrate
    php artisan key:generate
    php artisan config:cache
    ```

3. Create model and table

    ```bash
    php artisan make:model Question -m
    ```

    change database/migrations/2019_05_02_102128_create_questions_table.php then

    ```bash
    php artisan migrate
    ```

4. Generate Fake Data
   database/factories/UserFactory.php
   database/factories/QuestionFactory.php

    ```bash
    php artisan make:factory QuestionFactory --model=Question
    ```

   - Seeding the database

   ```bash
   php artisan make:seeder UsersTableSeeder
   php artisan migrate:fresh --seed
   ```

   The difference between “refresh” and “fresh” is that the new fresh command skips all the down methods or the rollback by dropping the tables, then running through the up methods.

   - Call the Seeder class

   ```php
   class DatabaseSeeder extends Seeder
   {
       /**
        * Seed the application's database.
        *
        * @return void
        */
       public function run()
       {
           $this->call(UsersTableSeeder::class);
       }
   }
   ```

5. Resource Controllers

    ```bash
    php artisan make:controller QuestionController --resource --model Question
    ```

    then set route in routes/web.php

    ```php
    Route: resource('questions', 'QuestionController');
    ```

    get the questions list with pagination applied

    ```php
    class QuestionController extends Controller
    {
        /**
        * Display a listing of the resource.
        *
        * @return \Illuminate\Http\Response
        */
        public function index()
        {
            $questions = Question::latest()->paginate(5);
            return view('questions.index', compact('questions'));
        }
    }
    ```

    and customize the pagination using [bootstrap](https://getbootstrap.com/docs/4.3/components/pagination/)

    ```bash
    php artisan vendor:publish --tag=laravel-pagination
    nano resources/views/vendor/pagination/bootstrap-4.blade.php
    ```

6. Debugging

    ```bash
    composer require barryvdh/laravel-debugbar --dev
    ```

    OR

    ```php
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \DB::enableQueryLog();

        $questions = Question::with('user')->latest()->paginate(5);
        view('questions.index', compact('questions'))->render();

        dd(\DB::getQueryLog());
    }
    ```