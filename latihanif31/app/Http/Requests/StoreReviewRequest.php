<?php

namespace App\Http\Requests;

use App\Models\Review;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'spot_id' => 'required|exists:spots,id',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string'
        ];
    }
    public function store(StoreReviewRequest $request)
    {
        try{
            $validated = $request->safe()->all();
            $validated['user_id'] = Auth::user()->id;

            $response = Review::create($validated);

            if($response){
                return Response::json([
                    'message' => "Review Berhasil ",
                    'data' => null
                ],200);

                return Response::json([
                    'message' => "Review Gagal, Sedihnya",
                    'data' => null
                ],500);
            }
            
        }
        catch(Exception $e){
            return Response::json([
                    'message' => $e->getMessage(),
                    'data' => null
                    ],500);
            }
    }
}
