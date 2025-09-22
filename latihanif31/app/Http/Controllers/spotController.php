<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Models\Category;
use App\Models\spots;

use Exception;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class spotController extends Controller
{
    public function store(StoreSpotRequest $request){
        try {
            $validated = $request->safe()->all();

            $picture_path = Storage::disk('public')->putFile('spots',$request->file('picture'));

            $validated['user_id'] = Auth::user()->id;
            $validated['picture'] = $picture_path;

            $spot = spots::create($validated);

            if($spot){
                $categories =[];

                foreach ($validated['category'] as $category){
                    $categories[] = [
                        'spot_id' => $spot->id,
                        'category' => $category
                    ];
                }
                
                Category::fillAndInsert($categories);


                return Response::json([
                'message' => "Gagal Membuat Spot Baru",
                'data' => null
            ],500);

            }

            


        }catch(Exception $e){
            return Response::json([
                'message' => $e->getMessage(),
                'data' =>null
            ],500);
        }

    }
}
