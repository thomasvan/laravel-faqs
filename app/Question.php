<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = ['title', 'body'];

    /**
     * Get a user that owns the question.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the answers of a questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Set the user's title and slug using Mutator
     * 
     * @param string $value
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }

    /**
     * Get the url using Accessor
     * 
     * @return string
     */
    public function getUrlAttribute()
    {
        return route("questions.show", $this->slug);
    }

    /**
     * Get the created date using Accessor
     * 
     * @return string
     */
    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the css class based on status using Accessor
     *
     * @return void
     */
    public function getStatusAttribute()
    {
        if ($this->answers_count > 0) {
            if ($this->best_answer_id) {
                return 'best-answered-accepted';
            }
            return 'answered';
        }
        return 'unanswered';
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
}
