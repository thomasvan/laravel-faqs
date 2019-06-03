<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
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
        return \Parsedown::instance()->text($this->body);
    }

    /**
     * Listening an event using static::
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($answer){
            $answer->question->increment('answers_count');
            $answer->save();
        });
    }
}
