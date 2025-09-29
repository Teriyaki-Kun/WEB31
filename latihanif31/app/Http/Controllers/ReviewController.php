<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReviewController extends Controller
{
    public function destroy(Review $review)
    {
        try{
            if($review->delete()){
                return Response::json([
                    'message' => "Review Berhasil Dihapus",
                    'data' => null
                ],200);

                return Response::json([
                    'message' => "Review Gagal Dihapus, Sedihnya",
                    'data' => null
                ],500);
            }
        }catch(Exception $e){
            return Response::json([
                    'message' => $e->getMessage(),
                    'data' => null
                    ],500);
            }
    }





}


