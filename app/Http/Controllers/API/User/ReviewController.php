<?php

namespace App\Http\Controllers\API\User;

use App\User;
use App\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function createReview(Request $request, $id)
    {
        $this->validate($request, [
            'description' => 'required',
            'rating' => 'integer|max:5|min:0'
        ]);
        $review = new Review;

        $user = User::findOrFail($id);
        $review->description = $request->description;
        $review->rating = $request->rating;


        try {
            $user->review()->save($review);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Review added",
                'data' => User::with('review')->findOrFail($id)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function getRating($id)
    {
        $rating = Review::where('user_id', $id)->avg('rating');

        return response()->json([
            'message' => "User rating",
            'data' => number_format((float) $rating, 1, '.', '')
        ], 200);
    }

    public function getUserReview()
    {
        $reviews = Auth()->user()->review;
        
        return response()->json([
            'message' => "User rating",
            'data' => $reviews
        ], 200);
    }

    public function getReviewByUserId($id)
    {
        $reviews = Review::where('user_id', $id)->get();

        return response()->json([
            'message' => "Reviews fetched",
            'data' => $reviews
        ], 200);
    }
}
