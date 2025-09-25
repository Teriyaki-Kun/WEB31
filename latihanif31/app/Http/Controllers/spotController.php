<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotRequest;
use App\Models\Category;
use App\Models\Review;
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
    public function index()
    {
        try{
            $spots = spots::with([
                'user:id,name',
                'category:category,spot_id'
            ])
            ->withCount([
                'Review'
            ])
            ->withSum('review','rating')
            ->orderBy('created_at','desc')
            ->paginate(request('size',10));

            return Response::json([
                'message' => "List Spot",
                'data' => $spots
            ],200);
        } catch (Exception $e){
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    public function show(spots $spots)
    {
        try{
        return Response::json([
                'message' => "Detail Spot",
                'data' => $spots ->load(([
                    'user:id,name',
                    'category:category,spot_id'
                ]))
                ->loadCount([
                    'Review'
                ])
                ->loadSum(
                    'Review','rating'
                )
            ],200);
         } catch (Exception $e){
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function destroy(spots $spots){
        try{
            $user = Auth::user();

            if($spots->user_id == $user->id || $user->role == 'Admin'){
                if($spots->delete()){
                    return Response::json([
                        'message' => "Spot Berhasil Dihapus",
                        'data' => null
                    ],200);
                }else
                {
                    return Response::json([
                        'message' => "Spot Gagal Di Hapus",
                        'data' => null
                    ],200);
                }
            }

        }catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }    

    public function update (UpdateSpotRequest $request, spots $spots)
    {
        try{
            $validate = $request->safe()->all();

            if(isset($validate ['picture'])){
                $picture_path = Storage::disk('public')->putFile('spots',$request->file('picture'));
            }
            if(isset($validate ['category'])){
                Category::where('spot_id', $spots->id)->delete();

             $categories =[];

                foreach ($validate['category'] as $category){
                    $categories[] = [
                        'spot_id' => $spots->id,
                        'category' => $category
                    ];
                }

                Category::fillAndInsert($categories);
            }
            $spots->update([
                'name' => $validate['name'],
                'picture' => $picture_path ?? $spots->picture,
                'address' => $validate['address']
            ]);
        }catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }



    
}
