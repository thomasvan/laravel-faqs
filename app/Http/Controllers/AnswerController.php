<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Question $question
     * @return \Illuminate\Http\Response
     */
    public function store(Question $question, Request $request)
    {
        /**
        $request->validate([
            'body' => 'required'
        ]);
        $question->answers()->create([
            'body' => $request->body,
            'user_id' => \Auth::id()
        ]);
         **/

        /** The validate return the data that has been validated if it was passed */
        $question->answers()->create($request->validate([
            'body' => 'required'
        ]) + ['user_id' => \Auth::id()]);

        return back()->with('success', "Your answer has been submitted successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function show(Answer $answer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question  $question
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question, Answer $answer)
    {
        $this->authorize('update', $answer);
        return view('answers.edit', compact('question', 'answer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question, Answer $answer)
    {
        $this->authorize('update', $answer);
        $answer->update($request->validate([
            'body' => 'required'
        ]));

        if ($request->expectsJson()) {

            return response()->json([
                'message' => 'Your answer has been updated successfully.',
                'body_html' => $answer->body_html
            ]);
        }
        return redirect()->route('questions.show', $question->slug)->with('success', 'Your answer has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @param  \App\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Question $question, Answer $answer)
    {
        $this->authorize('delete', $answer);
        $answer->delete();
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your answer has been removed successfully.'
            ]);
        }
        return back()->with('success', 'Your answer has been removed successfully.');
    }
}
