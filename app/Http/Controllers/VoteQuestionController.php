<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;

class VoteQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function __invoke(Question $question)
    {
        $vote = (int)request()->vote;
        auth()->user()->voteQuestion($question, $vote);
        return back();
    }
}
