<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['body', 'user_id'];
    /**
     * Get a question that contains the answer
     *
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get a user that contains the answer
     *
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the HTML text from Body attribute using Accessor
     *
     * @return string
     */
    public function getBodyHtmlAttribute()
    {
        return $this->body;
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }
    public function getStatusAttribute()
    {
        return $this->id === $this->question->best_answer_id ? 'vote-accepted' : '';
    }

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
            $question = $answer->question;
            $question->decrement('answers_count');
            if ($question->best_answer_id === $answer->id) {
                $question->best_answer_id = NULL;
                $question->save();
            }
        });
    }
}
