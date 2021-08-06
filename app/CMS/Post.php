<?php


namespace App\CMS;


use App\CMS\Post\Category;
use App\CMS\Post\Comment;
use App\CMS\Post\Status;
use App\CMS\Post\Type;
use Illuminate\Database\QueryException;

class Post {

    private $id;

    public function __construct($post = null) {

        if ($post !== null) {
            if(is_numeric($post)){
                $this->id = (int)$post;
            } elseif($id = db('cms')->table('post')->where('slug', '=', $post)->first('id')) {
                $this->id = (int)$id->id;
            } else {
                throw new \RuntimeException('Post ID not found.');
            }
        }

    }

    /** Get an array of post or a single post
     * @return \Illuminate\Support\Collection|mixed
     */
    public function get() {

        $query = db()->table('post')
            ->select([
                'post.id',
                db()->raw('MAX(post.slug) as slug'),
                db()->raw('MAX(post.author) as author_id'),
                db()->raw('CONCAT(MAX(user.first_name), " ",MAX(user.last_name)) as author_name'),
                db()->raw('MAX(post.title) as title'),
                db()->raw('MAX(post.description) as description'),
                db()->raw('MAX(post.type) as type'),
                db()->raw('MAX(post.status) as status'),
                db()->raw('MAX(post.thumbnail) as thumbnail'),
                db()->raw('MAX(post.resource) as resource'),
                db()->raw('MAX(post.json_data) as json_data'),
                db()->raw('GROUP_CONCAT(post_has_category.category) as categories'),
                db()->raw('COUNT(post_comment.id) as comment_count'),
            ])
            ->leftJoin('post_has_category','post.id','=','post_has_category.post')
            ->join('user','post.author','=','user.id')
            ->leftJoin('post_comment','post.id','=','post_comment.post')
            ->groupBy(['post.id']);


        if ($this->id !== null) {

            $result = $query->where('post.id', '=', $this->id)->get();
            if (!$result->isEmpty()) {
                $result->first()->comments = $this->comment()->get();
            }

        } else {
            $result = $query->get();
        }

        if (!$result->isEmpty()) {

            $types = $this->type()->get();
            $categories = $this->category()->get();
            $status = $this->status()->get();

            $result->each(function($item) use ($types, $categories, $status) {

                $item->type = $types->where('id','=',$item->type)->map(function ($type) {
                    return collect($type)->only(['id', 'slug', 'title']);
                })->first();

                $item->status = $status->where('id','=',$item->status)->map(function ($state) {
                    return collect($state)->only(['id', 'slug', 'title']);
                })->first();

                $item->categories = $categories->whereIn('id', explode(',', $item->categories))->values();

            });

        }

        return $result;
    }

    /**
     * @param $save_as_status
     * @param $type
     * @param $title
     * @param string $description
     * @param string $body
     * @param null $thumbnail
     * @param null|\Illuminate\Http\UploadedFile $resource
     * @param null $json_data
     * @return Post
     * @throws \Exception
     */
    public function add($save_as_status, $type, $title, $description = '', $body = '', $thumbnail = null, $resource = null, $json_data = null) {

        if (!is_numeric($save_as_status)  || !in_array($save_as_status, [Status::ID_DRAFT, Status::ID_SUBMITTED, Status::ID_PUBLISHED])) {
            throw new \Exception('Invalid/missing save as value', 400);
        }

        if (!$this->type()->get()->where('id','=', $type)->count()) {
            throw new \Exception('Invalid/missing post type', 400);
        }

        if ($title === null || !is_string($title)) {
            throw new \Exception('Missing post title', 400);
        }

        try {

            $slug = preg_replace('/[^a-z0-9]/', '-', strtolower(trim(strip_tags($title))));

            if ($count = db('cms')->table('post')->where('slug', 'like', $slug.'%')->count()) {
                $slug .= "-$count";
            }

            $description = (string)$description;
            $body = strip_tags($body);

            $this->id = db('cms')->table('post')
                ->insertGetId([
                    'type'=> $type,
                    'slug'=> $slug,
                    'author'=> auth()->id(),
                    'title'=> $title,
                    'description'=> $description,
                    'status'=> Status::ID_PUBLISHED,
                    'body'=> $body,
                    'resource'=> $resource,
                    'json_data'=> $json_data,
                    'thumbnail'=> null
                ]);

            $this->set_thumbnail($thumbnail);

        } catch (QueryException $e) {
            throw new \Exception($e->getMessage(), 500);
        }

        return $this;

    }

    /**
     * @param $save_as_status
     * @param null|int $type
     * @param null|string $title
     * @param string $description
     * @param string $body
     * @param null $thumbnail
     * @param null $resource
     * @param null $json_data
     * @return int
     * @throws \Exception
     */
    public function update($save_as_status, $type = null, $title = null, $description = '', $body = '', $thumbnail = null, $resource = null, $json_data = null) {

        $values = [
            'description'=> (string)$description,
            'body'=> strip_tags($body),
            'resource'=> $resource,
            'json_data'=> $json_data,
        ];

        if ($title !== null && is_string($title)) {

            $values['title'] = $title;
            $slug = preg_replace('/[^a-z0-9]/', '-', strtolower(trim(strip_tags($title))));

            if ($count = db('cms')->table('post')->where('slug', 'like', $slug.'%')->count()) {
                $slug .= "-$count";
            }
            $values['slug'] = "$slug-$count";

        }

        // verify status is correct
        if ($save_as_status !== null && is_numeric($save_as_status) && $this->status()->get()->where('id','=', $save_as_status)->count()) {
            $values['status'] =   $save_as_status;
        } else {
            throw new \Exception('Invalid save as status value',400);
        }

        if ($type !== null && is_numeric($type) && $this->type()->get()->where('id','=', $type)->count()) {
            $values['type'] =   $type;
        }

        try {

            $this->set_thumbnail($thumbnail);

            return db('cms')->table('post')
                ->where('post.id','=', $this->id())
                ->update($values);

        } catch (QueryException | \Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }

    }

    public function archive() {
        try {
            return $this->update( Status::ID_ARCHIVED);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e);
        }
    }

    private function set_thumbnail($thumbnail) {

        if ($thumbnail instanceof \Illuminate\Http\UploadedFile) {

            $filename = '/cms/' . uniqid(). $thumbnail->getClientOriginalName();

            if (storage('local')->put( $filename, $thumbnail->getContent())) {

                db('cms')->table('post')
                    ->where('id','=', $this->id())
                    ->update([
                        'thumbnail'=> $filename
                    ]);

            }

        }

    }

    /**
     * @return int
     * @throws \Exception
     */
    public function id(){
        $this->id_required();
        return $this->id;
    }

    public function type($type = null){
        return new Type($type);
    }

    public function category($category = null) {
        return new Category($category);
    }

    public function comment($comment = null) {
        return new Comment($this->id, $comment);
    }

    public function status() {
        return new Status();
    }

    /**
     * @throws \Exception
     */
    private function id_required(){
        if(empty($this->id)){
            throw new \Exception('Missing post id.');
        }
    }
}
