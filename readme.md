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

7. CSS
    1. Css location
    Related files are located at `webpack.mix.js` `resources/sass/_variables.scss` `resources/sass/app.scss` `public/css/app.css` and loaded at `resources/views/layouts/app.blade.php`
    2. Css changes sample:

        ```css
        .counters {
            margin-right: 30px;
            font-size: 11px;
            text-align: center;
        }
        ```

        then running compilation required before viewing the effects

        ```bash
        npm run dev # Run compilation immediately
        npm run watch # Run every times you make changes
        ```

    3. Variables defined sample

        ```scss
        // customized variables defined
        $green: rgb(95, 187, 126);

        // and then use
        .status {
            &.unanswered {
                border: none;
            }

            &.answered {
                border: 1px dotted $green;
                color: $green;
            }
            &.best-answered-accepted {
                background: $green;
                color: $white;
            }
        }
        ```

8. Create saving/editing form

    1. Confirm a route if it's correct or not

        ```bash
        php artisan route:list
        php artisan route:list --name=questions # filtering by name
        ```

    2. Create a simple action and return the view route

        ```php
        /**
        * Show the form for creating a new resource.
        *
        * @return \Illuminate\Http\Response
        */
        public function create()
        {
            $question = new Question();
            return view('questions.create', compact($question));
        }
        ```

    3. Create a view based on .blade

        ```html
        @extends('layouts.app') @section('content')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-centers">
                                <h2>Ask Question</h2>
                                <div class="ml-auto">
                                    <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Back to All Questions</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('questions.store') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="question-title">Question Title</label>
                                    <input type="text" name="title" id="question-title" class="form-control {{ $errors->has('title')?'is-invalid' :'' }}" />
                                    @if ($errors->has('title'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="question-body">Explain your Question</label>
                                    <textarea name="question-body" id="question-body" rows="10" class="form-control {{ $errors->has('body')?'is-invalid' :'' }}"></textarea>

                                    @if ($errors->has('body'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('body') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-primary btn-lg">Ask this Question</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endsection
        ```

9. Form Request Validation
    1. Create a form request

        ```bash
        php artisan make:request AskQuestionRequest # will creata a request file located at app/Http/Requests/AskQuestionRequest.php
        ```

    2. Then add the validate rules

        ```php
        /**
        * Get the validation rules that apply to the request.
        *
        * @return array
        */
        public function rules()
        {
            return [
                'title' => 'required|255',
                'body' => 'required'
            ];
        }
        ```

    3. Change the request type-hint in Controller

        ```php
        /**
         * Store a newly created resource in storage.
            *
            * @param  \Illuminate\Http\Request  $request
            * @return \Illuminate\Http\Response
            */
        public function store(AskQuestionRequest $request)
        {
            //
        }
        ```

10. Editing form
    1. Define an action

        ```php
        /**
         * Show the form for editing the specified resource.
         *
         * @param  \App\Question  $question
            * @return \Illuminate\Http\Response
            */
        public function edit(Question $question)
        {
            return view("questions.edit", compact($question));
        }
        ```

    2. Check the routing

        ```bash
        php artisan route:list -name=questions.update
        --------+-----------+----------------------+------------------+------------------------------------------------+------------+
        | Domain | Method    | URI                  | Name             | Action                                         | Middleware |
        +--------+-----------+----------------------+------------------+------------------------------------------------+------------+
        |        | PUT|PATCH | questions/{question} | questions.update | App\Http\Controllers\QuestionController@update | web        |
        +--------+-----------+----------------------+------------------+------------------------------------------------+------------+

    3. Create the view 
    > Change the action method to PUT instead of POST
    