<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Faker\Provider\Uuid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BlogPostController extends Controller
{
    const TABLE = 'blog_post';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Create a new blog post.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request)
    {
        $this->ValidateBlogPost($request);
        $data = $request->all();
        $blog_post = new BlogPost($data);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $imageName = uniqid() . $image->getClientOriginalName();
            $image->storeAs('public', $imageName);
            $blog_post['image'] = isset($imageName) ? '/public/storage/' . $imageName : '';
        }
       
        $id = DB::table(self::TABLE)->insertGetId($blog_post->attributesToArray());
        $uri = $this->getUri($request, $id);
        return (new Response())->header('Location', $uri);
    }

    /**
     * Get a list of all blog posts.
     *
     * @return Response
     */
    public function find()
    {
        $blog_posts = DB::table(self::TABLE)->get();
        $status = count($blog_posts) == 0 ? 404 : 200;
        return new JsonResponse($blog_posts, $status);
    }

    /**
     * Get a blog post by id.
     *
     * @param  string   $id
     * @return Response
     */
    public function get(string $id)
    {
        $blog_post = DB::table(self::TABLE)->find($id);
        $image = 
        $status = empty($blog_post) ? 404 : 200;
        return new JsonResponse($blog_post, $status);
    }

    /**
     * Update a blog post by id.
     *
     * @param  Request  $request
     * @param  int      $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->ValidateBlogPost($request);

        $foundBlogPost = DB::table(self::TABLE)->find($id);
        if (empty($foundBlogPost)) {
            return new Response('', 404);
        }

        $data = $request->all();
        $blog_post = new BlogPost($data);
        DB::table(self::TABLE)->where('id', '=', $id)->update($blog_post->attributesToArray());
        return new Response('', 204);
    }

    /**
     * Delete a blog post by id.
     *
     * @param  int   $id
     * @return Response
     */
    public function delete($id)
    {
        $deleted = DB::table(self::TABLE)->delete($id);
        return new Response('', 204);
    }

    /**
     * Validate the fields in a blog post.
     * 
     * @param Request $request
     */
    protected function ValidateBlogPost($request)
    {
        $this->validate($request, [
            'title' => 'required|min:1|max:128',
            'summary' => 'required',
            'body' => 'required',
            'image' => 'nullable',
        ]);
    }

    /**
     * Get the uri of the current request with the id of the resource.
     * 
     * @param Request   $request
     * @param int       $id
     */
    protected function getUri($request, $id)
    {
        return $request->path() . '/' . $id;
    }
}
