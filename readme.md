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
$faker = Faker\Factory::create();
rtrim($faker->sentence(rand(5,10)),'.')
$faker->paragraphs(rand(3,7),true)
=> "Rem subscript omanis volutes corporal et"

# Update a question
$answer = App\Answer::find(48);
$question = $answer->question;
$question->best_answer_id = 48;
$question->save();
# ...
$question->refresh();
```

Publish Tinker's configuration file

```bash
php artisan vendor:publish --provider="Laravel\Tinker\TinkerServiceProvider"
```

---

1.  Initialize

    ```bash
    composer update
    composer install
    npm install
    ```

2.  .env

    Configure DB info

    ```bash
    php artisan make:auth
    php artisan mirgrate
    php artisan key:generate
    php artisan config:cache
    ```

3.  Create model and table

    ```bash
    php artisan make:model Question -m
    php artisan make:model Question -m
    ```

    change database/migrations/2019_05_02_102128_create_questions_table.php then

    ```bash
    php artisan migrate
    ```

4.  Create model relationship

    ```php
    // in CreateQuestionsTable
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            // ...
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    // in Question Model
    /**
     * Get the user that owns the question.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // in User Model
        /**
     * Get the questions of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    ```

    > Avoid naming a column name the same as an relationship function. Laravel will retrieve the column name first and return it if it is existing.

5.  Generate Fake Data
    database/factories/UserFactory.php
    database/factories/QuestionFactory.php

    ```bash
    php artisan make:factory QuestionFactory --model=Question
    php artisan make:factory AnswerFactory
    ```

    > Seeding the database

    ```bash
    php artisan make:seeder UsersTableSeeder
    php artisan migrate:fresh --seed
    ```

    The difference between “refresh” and “fresh” is that the new fresh command skips all the down methods or the rollback by dropping the tables, then running through the up methods.

    > Call the Seeder class

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

6.  Perform a migration

    ```bash
     php artisan make:migration rename_answer_column_in_questions_table --table=questions
     # do s.t in 2019_06_02_085014_rename_answer_column_in_questions_table ... see below
     # and then run...
     php artisan migrate
    ```

    ```php
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
    ```

7.  Resource Controllers

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

8.  Debugging

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

9.  CSS

    1. Css location
       Related files are located at `webpack.mix.js` `node_modules\bootstrap\scss\_variables.scss` `resources/sass/_variables.scss` `resources/sass/app.scss` `public/css/app.css` and loaded at `resources/views/layouts/app.blade.php`
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

10. Create saving/editing form

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

        ```xml
        <div class="card-header">
            <div class="d-flex align-items-centers">
                <h2>Edit Question</h2>
                <div class="ml-auto">
                    <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Back to All Questions</a>
                </div>
            </div>

        </div>

        <div class="card-body">
            <form action="{{ route('questions.update', $question) }}" method="post">
                {{ method_field('PUT') }} or @method('PUT')
                @include('questions._form', ['submitButtonTitle' => 'Update Question'])
            </form>
        </div>
        ```

11. Form Request Validation

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

12. Editing form

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

            ```

        3. Create the view

            > Change the action method to PUT instead of POST

            ```xml
            {{ method_field('PUT') }} or @method('PUT')
            {{ method_field('PATCH') }} or @method('PATCH') <!-- if there is one field updated -->
            ```

            > Use the @old directive to keep the old value as error occurred

            ```xml
            <form action="{{ route('questions.answers.update', [$question->id, $answer->id]) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <textarea class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}" rows="7" name="body">{{ old('body', $answer->body) }}</textarea>
                    @if ($errors->has('body'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('body') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-outline-primary">Update</button>
                </div>
            </form>
            ```

        4. Update action in QuestionController

            ```php
            /**
             * Update the specified resource in storage.
            *
            * @param  \Illuminate\Http\Request  $request
            * @param  \App\Question  $question
            * @return \Illuminate\Http\Response
            */
            public function update(AskQuestionRequest $request, Question $question)
            {
                $question->update($request->only('title', 'body'));
                return redirect('/questions')->with('success', 'Your question has been updated successfully.');
            }
            ```

13. Deleting form

    1. Notes:

        ```xml
        {{ method_field('DELETE') }} >> @method('DELETE') {{ csrf_token() }} >>
        @csrf
        ```

    2. Form

        ```php
        /**
         * Remove the specified resource from storage.
         *
         * @param  \App\Question  $question
        * @return \Illuminate\Http\Response
        */
        public function destroy(Question $question)
        {
            $question->delete();
            return redirect()->route('questions.index')->with('success', 'Your question has been deleted.');
        }
        ```

14. Showing form

    1. Enable {slug} parameter

        ```php
        // route the web.php

        Route::resource('questions', 'QuestionController')->except('show');
        Route::get('/questions/{slug}', 'QuestionController@show')->name('questions.show');
        ```

        ```php
        // modify the boot method in app/Providers/RouteServiceProvider.php

        /**
        * Define your route model bindings, pattern filters, etc.
        *
        * @return void
        */
        public function boot()
        {
            Route::bind('slug', function ($slug) {
                return Question::where('slug', $slug)->first() ?? abort(404);
            });

            parent::boot();
        }
        ```

    2. Preparing for showing form

        ```php
        // using the accessor to return body html

        public function getBodyHtmlAttribute()
        {
            return \Parsedown::instance()->text($this->body);
        }
        ```

        ```php
        /**
         * Get the created date using Accessor
         *
         * @return string
         */
        public function getCreatedDateAttribute()
        {
            return $this->created_at->diffForHumans();
        }
        ```

        ```php
        // using the accessor to return the slug for url: <h3 class="mt-0"><a href="{{ $question->url }}">{{ $question->title }}</a></h3>

        /**
         * Get the url using Accessor
        *
        * @return string
        */
        public function getUrlAttribute()
        {
            return route("questions.show", $this);
        }
        ```

        ```xml
        <!-- prevent escape html using {!! -->
        <div class="card-body">
            {!! $question->body_html !!}
        </div>
        ```

    3. Controller/Action handler

        ```php
        /**
        * Display the specified resource.
        *
        * @param  \App\Question  $question
        * @return \Illuminate\Http\Response
        */
        public function show(Question $question)
        {
            $question->increment('views');
            return view('questions.show', compact('question'));
        }
        ```

15. Creating form for child model

    1. Define the route for child model

        ```php
        // in routes/web.php
        Route::resource('questions.answers', 'AnswerController');
        ```

    2. Check the route nnd define the Controller if necessary

        ```bash
        php artisan make:controller AnswerController -r -m Answer # resource and model
        php artisan route:list --name=questions
        # questions/{question}/answers/create        | questions.answers.create  | App\Http\Controllers\AnswerController@create
        ```

    3. Re-define the route

        ```php
        Route::post('/questions/{question}/answers', 'AnswerController@store');
        // recheck the route to see the results:  php artisan route:list --name=questions
        ```

    4. Prepare the form

        ```xml
        <form action="{{ route('questions.answers.store', $question->id) }}" method="post">
            @csrf
            <div class="form-group">
                <textarea class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}" rows="7" name="body"></textarea>
                @if ($errors->has('body'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('body') }}</strong>
                    </div>
                @endif
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-lg btn-outline-primary">Submit</button>
            </div>
        </form>
        ```

    5. Controller/Action handler

        ```php
        $question->answers()->create($request->validate([
            'body' => 'required'
        ]) + ['user_id' => \Auth::id()]);

        return back()->with('success', "Your answer has been submitted successfully");
        ```

16. Authorizing the Question Detail using Gates

    1. Edit the AuthServiceProvider

        ```php
        public function boot()
        {
            $this->registerPolicies();

            // Implement the gate authorization
            \Gate::define('update-questions', function ($user, $question) {
                return $user->id === $question->user_id;
            });
            \Gate::define('delete-questions', function ($user, $question) {
                return $user->id === $question->user_id;
            });
        }
        ```

    2. Change the action method

        ```php
        /**
        * Show the form for editing the specified resource.
        *
        * @param  \App\Question  $question
        * @return \Illuminate\Http\Response
        */
        public function edit(Question $question)
        {
            if (\Gate::allows('update-questions', $question))
                return view("questions.edit", compact('question'));
            abort(403, "Access denied");
        }
        /**
        * Remove the specified resource from storage.
        *
        * @param  \App\Question  $question
        * @return \Illuminate\Http\Response
        */
        public function destroy(Question $question)
        {
            if (\Gate::denies('delete-questions')) {
                abort(403, 'Access denied');
            }
            $question->delete();
            return redirect()->route('questions.index')->with('success', 'Your question has been deleted.');
        }
        ```

    3. Change the view

        ```xml
        @can('update-questions', $question)
            <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm btn-outline-info">Edit</a>
        @endcan
        ```

17. Authorizing the Question Detail using Policy

    1. Generate the Questions policy

        ```bash
        php artisan make:policy QuestionPolicy --model=Question
        # >> app/Policies/QuestionPolicy.php
        ```

        ```php
        class QuestionPolicy
        {
            use HandlesAuthorization;

            /**
             * Determine whether the user can update the question.
             *
             * @param  \App\User  $user
            * @param  \App\Question  $question
            * @return mixed
            */
            public function update(User $user, Question $question)
            {
                return $user->id == $question->user_id;
            }

            /**
             * Determine whether the user can delete the question.
             *
             * @param  \App\User  $user
            * @param  \App\Question  $question
            * @return mixed
            */
            public function delete(User $user, Question $question)
            {
                return $user->id == $question->user_id && $question->answers_count < 1;
            }
        }
        ```

    2. Register in AuthServiceProvider

        ```php
        protected $policies = [
            Question::class => QuestionPolicy::class
        ];
        ```

    3. Usage

        ```php
        public function edit(Question $question)
        {
            $this->authorize('update', $question);
            return view("questions.edit", compact('question'));
        }
        ```

        ```xml
        <!-- use can directive -->
        @can('update', $question)
            <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm btn-outline-info">Edit</a>
        @endcan

        @can('delete', $question)
            <form class="form-delete" method="post" action="{{ route('questions.destroy', $question->id) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');">Delete</button>
            </form>
        @endcan
        ```

        ```php
        /**
        * Restrict access to all page excepts for index and show
        */
        public function __construct()
        {
            $this->middleware('auth', ['except' => ['index', 'show']]);
        }
        ```

18. Events Listening

    ```php
    /**
     * Listening an event using static::
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($answer) {
            $answer->question->increment('answers_count');
        });

        static::deleted(function ($answer) {
            $answer->question->decrement('answers_count');
        });
    }
    ```

19. Eager Loading

    ```php
    # Eager Loading a relationship: question->answers->user in this case
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('slug', function ($slug) {
            return Question::with('answers.user')->where('slug', $slug)->first() ?? abort(404);
        });

        parent::boot();
    }
    ```

20. Install fontawesome package using npm

    1. Search package at https://www.npmjs.com/search?q=fortawesome

    2. Chose and copy install cmd: `npm i @fortawesome/free-solid-svg-icons`

    3. Run install `npm i @fortawesome/fontawesome @fortawesome/free-solid-svg-icons -D`

    4. Add a new .js file `resources\js\fontawesome.js`

        ```js
        import fontawesome from "@fortawesome/fontawesome";
        import faCaretUp from "@fortawesome/fontawesome-free-solid/faCaretUp";
        import faCaretDown from "@fortawesome/fontawesome-free-solid/faCaretDown";
        import faStar from "@fortawesome/fontawesome-free-solid/faStar";
        import faCheck from "@fortawesome/fontawesome-free-solid/faCheck";
        ```

        then include it in `resources\js\app.js`

        ```js
        require("./bootstrap");
        require("./fontawesome");
        ```

    5. Run cmd `npm run watch # Run every times you make changes`

    6. Be used in blade

        ```html
        <a title="This question is useful" class="vote-up">
            <i class="fas fa-caret-up fa-3x"></i>
        </a>
        <span class="votes-count">1230</span>
        <a title="This question is not useful" class="vote-down off">
            <i class="fas fa-caret-down fa-3x"></i>
        </a>
        ```

    7. Extra styles in `resources\sass\app.scss`

        ```scss
        .vote-controls {
            min-width: 60px;
            margin-right: 30px;
            text-align: center;
            color: $gray-700; /** comes from node_modules\bootstrap\scss\_variables.scss **/

            span,
            a {
                display: block;
            }
        }
        ```

21. Passing route parameters

    ```bash
    # in case you found the route for answer/edit as below
    php artisan route:list --name questions.answers
    >> questions/{question}/answers/{answer}/edit | questions.answers.edit
    ```

    ```xml
    // then let define the route with parameters like this
    <a href="{{ route('questions.answers.edit', [$question->id, $answer->id]) }}" ...
    ```

22. Add the foreign key by migration

    ```bash
    php artisan make:migration add_foreign_key_best_answer_id_to_questions_schema --table=questions
    ```

    ```php
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('best_answer_id')
                ->references('id')
                ->on('answers')
                ->onDelete('SET NULL');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['best_answer_id']);
        });
    }
    ```

    ```bash
    php artisan migrate
    ```

23. Define a single action controller

    ```bash
    # Define the route
    Route::post('/answers/{answer}/accept','AcceptAnswerController')->name('answers.accept');
    # then create a controller
    php artisan make:controller AcceptAnswerController
    ```

    ```php
    // then define the single actions
    class AcceptAnswerController extends Controller
    {
        public function __invoke(Answer $answer)
        {
            $answer->question->acceptBestAnswer($answer);
            return back();
        }
    }
    ```
