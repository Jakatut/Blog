<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

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
        $this->middleware('auth');
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
            $disk = Storage::disk('gcs');
            $image = $request->file('image');
            $imageName = $disk->put('', $image, 'public');
            $blog_post['image'] = $disk->url($imageName);
        }

        $blog_post->save();
        $uri = $this->getUri($request, $blog_post->id);
        return (new Response())->header('Location', $uri);
    }

    /**
     * Get a list of all blog posts.
     *
     * @return Response
     */
    public function find()
    {
        $blog_posts = BlogPost::all();
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
        $blog_post = BlogPost::find($id);
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

        $blog_post = BlogPost::find($id);
        if ($blog_post) {
            return new Response('', 404);
        }

        // Check if a file exists in the query.
        // If the blog post already has an image, delete and replace it with the new image.
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $disk = Storage::disk('gcs');
            if ($blog_post->image) {
                $disk->delete($blog_post->image);
            }
            $image = $request->file('image');
            $imageName = $disk->put('', $image, 'public');
            $blog_post['image'] = $disk->url($imageName);
        }

        $blog_post->update($blog_post->attributesToArray());
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

        $blog_post = BlogPost::find($id);
        if (!$blog_post) {
            return new Response('', 404);
        }
        
        $blog_post->delete();
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
