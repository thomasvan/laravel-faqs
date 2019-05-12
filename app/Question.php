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
     * Get the user that owns the question.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
        return route("questions.show", $this);
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

    public function getStatusAttribute()
    {
        if ($this->answers > 0) {
            if ($this->best_answer_id) {
                return 'best-answered-accepted';
            }
            return 'answered';
        }
        return 'unanswered';
    }
}
