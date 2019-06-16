<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Question extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = ['title', 'body'];

    use VotableTrait;

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
     * Set the body content with Purifier::clean XSS attack
     * 
     * @return void
     */
    // public function setBodyAttribute($value)
    // {
    //     $this->attributes['body'] = clen($value);
    // }

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
        return Purifier::clean($this->bodyHtml());
    }


    /**
     * Accept the Best Answer which is using for single action controller
     *
     * @param  \App\Answer $answer
     *
     * @return void
     */
    public function acceptBestAnswer(Answer $answer)
    {
        $this->best_answer_id = $answer->id;
        $this->save();
    }
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps(); //'question_id','user_id' keys as default
    }

    public function isFavorited()
    {
        return $this->favorites()->where('user_id', auth()->id())->count() > 0;
    }
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }
    public function getFavoritesCountAttribute()
    {
        return $this->favorites()->count();
    }
    public function getExcerptAttribute()
    {
        return $this->excerpt(250);
    }
    public function excerpt($length)
    {
        return str_limit($this->bodyHtml(), $length);
    }
    private function bodyHtml()
    {
        return \Parsedown::instance()->text(strip_tags($this->body));
    }
}
