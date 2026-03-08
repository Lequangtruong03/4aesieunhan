<?php

namespace App\Http\Controllers;

use App\Models\Cast;
use App\Models\Director;
use App\Models\Movie;
use App\Models\MovieGenres;
use App\Models\Rating;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class MovieController extends Controller
{

    function __construct()
    {
        $cloud_name = cloud_name();
        view()->share('cloud_name', $cloud_name);
    }

    public function movie()
    {
        $movies = Movie::orderBy('id', 'DESC')->Paginate(5);
        return view('admin.movie.list', ['movies' => $movies]);
    }

    public function getCreate()
    {
        $casts = Cast::all();
        $directors = Director::all();
        $movieGenres = MovieGenres::get()->where('status',1);
        $rating = Rating::all();
        return view('admin.movie.create', [
            'movieGenres' => $movieGenres,
            'directors' => $directors,
            'casts' => $casts,
            'rating' => $rating
        ]);
    }

    public function postCreate(Request $request)
    {

        if ($request->hasFile('Image')) {
            $file = $request->file('Image');
            $img = $request['image'] = $file;
            ####
           $upload = Cloudinary::upload($img->getRealPath(), [
                'folder' => 'movies',
            ]);

            $cloud = $upload->getSecurePath();
            $movie = new Movie(
                [
                    'name' => $request->name,
                    'image' => $cloud,
                    'showTime' => $request->showTime,
                    'releaseDate' => $request->releaseDate,
                    'endDate' => $request->endDate,
                    'national' => $request->national,
                    'rating_id' => $request->rating,
                    'description' => $request->description,
                    'trailer'=> $request->trailer
                ]
            );
            $movie->save();
            $casts = Cast::find($request->casts);
            $movie->casts()->attach($casts);

            $directors = Director::find($request->directors);
            $movie->directors()->attach($directors);

            $movieGenres = MovieGenres::find($request->movieGenres);
            $movie->movieGenres()->attach($movieGenres);
        }else{
            return redirect('admin/movie')->with('warning','Vui lòng nhập hình ảnh');
        }
        return redirect('admin/movie');
    }

    public function getEdit($id)
    {
        $casts = Cast::all();
        $directors = Director::all();
        $movieGenres = MovieGenres::all();
        $rating = Rating::all();
        $movie = Movie::find($id);
        return view('admin.movie.edit', ['movie' => $movie,
            'movieGenres' => $movieGenres,
            'directors' => $directors,
            'casts' => $casts,
            'rating' => $rating]);
    }

    public function postEdit(Request $request, $id)
    {

        $movie = Movie::find($id);
        if ($request->hasFile('Image')) {
            $file = $request->file('Image');
            $img = $request['image'] = $file;
            ###
            $upload = Cloudinary::upload($img->getRealPath(), [
                'folder' => 'movies',
            ]);

            $movie->image = $upload->getSecurePath();
        }
        $movie['name'] = $request['name'];
        $movie['showTime'] = $request['showTime'];
        $movie['releaseDate'] = $request['releaseDate'];
        $movie['endDate'] = $request['endDate'];
        $movie['national'] = $request['national'];
        $movie['description'] = $request['description'];
        $movie['trailer'] = $request['trailer'];
        $movie['rating_id'] = $request['rating'];

        $movie->update();

        $casts = Cast::find($request->casts);
        $movie->casts()->detach();
        $movie->casts()->attach($casts);

        $directors = Director::find($request->directors);
        $movie->directors()->detach();
        $movie->directors()->attach($directors);

        $movieGenres = MovieGenres::find($request->movieGenres);
        $movie->movieGenres()->detach();
        $movie->movieGenres()->attach($movieGenres);


        return redirect('admin/movie')->with('success', "Cập nhật thành công!");
    }

    public function delete($id)
    {
        $movie = Movie::find($id);
        Cloudinary::destroy($movie['image']);
        $movie->delete();
        return response()->json(['success' => 'Xóa thành công!']);
    }

    public function status(Request $request)
    {
        $movie = Movie::find($request->movie_id);
        $movie['status'] = $request->active;
        $movie->save();
        return response('success',200);
    }
    public function searchMovie(Request $request){
            $output = '';
            if($request->search == null){
                $movies = Movie::orderBy('id', 'DESC')->Paginate(5);
            }else{
                $movies = Movie::where('name', 'LIKE', '%' . $request->search . '%')->get();
            }
            if ($movies) {
                foreach ($movies as  $movie) {
                    $output .= '<tr>
                     <td class="align-middle text-center">';
                     foreach($movie->movieGenres as $genre){
                         $output.='
                     <h6 class="mb-0 text-sm ">'. $genre->name .'</h6>';
                     }
                    $output.='
                        </td>
                   <td class="align-middle text-center">';
                    if(strstr($movie->image,"https") == "") {
                        $output .= '
                        <img style="width: 300px"
                             src="https://res.cloudinary.com/'. cloud_name() .'/image/upload/'.$movie->image.'.jpg"
                             alt="user1">';
                    }
                    else {
                        $output .= '
                        <img style="width: 300px"
                             src="'. $movie->image .'" alt="user1">';
                    }
                     $output.='</td>
                     <td class="align-middle text-center">
                        <div class="accordion-body mt-4 mb-3 w-100">
                            '. strip_tags($movie->name) .'
                        </div>
                    </td>
                     <td class="align-middle text-center">
                            <span class="text-secondary font-weight-bold">
                             '.$movie->showTime.'  minutes
                            </span>
                        </td>
                     <td class="align-middle text-center">
                        <h6 class="mb-0 text-sm ">'. $movie->national .'</h6>
                      </td>
                       <td class="align-middle text-center">
                        <span class="text-secondary font-weight-bold">
                        '. date("d-m-Y", strtotime($movie->releaseDate )).'
                        </span>
                        </td>
                      <td class="align-middle text-center">
                           <span class="text-secondary font-weight-bold">
                           '.date("d-m-Y", strtotime($movie->endDate)).'
                           </span>
                       </td>
                     <td id="status'. $movie['id'] .'" class="align-middle text-center text-sm">';
                        if($movie['status'] == 1)
                        {
                        $output.='
                            <a href="javascript:void(0)" class="btn_active"  onclick="changestatus('. $movie['id'] .',0)">
                                <span class="badge badge-sm bg-gradient-success">Online</span>
                            </a>';
                        }
                        else
                        {
                        $output.='
                            <a href="javascript:void(0)" class="btn_active"  onclick="changestatus('. $movie['id'] .',1)">
                                <span class="badge badge-sm bg-gradient-secondary">Offline</span>
                            </a>';
                        }
                        $output.='</td>
                         <td class="align-middle">';
                        $output.='
                            <a href="admin/movie/edit/'. $movie['id'] .'" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip"
                               data-original-title="Edit user">
                                <i class="fa-solid fa-pen-to-square fa-lg"></i>
                            </a>';
                        $output.='
                        </td>
                    </tr>';

                }
            }
            return Response($output);

    }
}
