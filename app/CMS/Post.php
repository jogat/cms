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

    public function search($parameters = [], $column_order = '', $order_direction = 'ASC', $items_per_page = 100) {

        $default_parameters = [
            'text'=> false,
            'types'=> false,
            'status'=> false,
            'categories'=> false,
            'audiences'=> false,
        ];

        $default_parameters = array_merge($default_parameters, $parameters);

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
                db()->raw('MAX(post.last_published_date) as last_published_date'),
                db()->raw('GROUP_CONCAT(post_has_category.category) as categories'),
                db()->raw('GROUP_CONCAT(post_has_audience.audience) as audiences'),
                db()->raw('COUNT(post_comment.id) as comment_count'),
            ])
            ->leftJoin('post_has_category','post.id','=','post_has_category.post')
            ->leftJoin('post_has_audience','post.id','=','post_has_audience.post')
            ->join('user','post.author','=','user.id')
            ->leftJoin('post_comment','post.id','=','post_comment.post')
            ->groupBy(['post.id']);

        if (!empty($column_order) && in_array(strtoupper($order_direction), ['ASC','DESC'])) {
            $query->orderBy($column_order, $order_direction);
        }

        if ($default_parameters['text']) {

            $text = '%'.strtolower($default_parameters['text']).'%';
            $query->where(function($q) use($text) {
                /** @var \Illuminate\Database\Query\Builder $q */

                $q->whereRaw("LOWER(post.title) like ?", [$text])
                    ->orWhereRaw('LOWER(CONCAT(MAX(user.first_name), " ",MAX(user.last_name))) like ?', [$text])
                    ->orWhereRaw('LOWER(MAX(post.description)) like ?', [$text]);

            });


        }

        if ($default_parameters['types']) {

            if (is_array($default_parameters['types'])) {
                $query->whereIn("post.type", $default_parameters['types']);
            } else {
                $query->where("post.type", '=', $default_parameters['types']);
            }

        }

        if ($default_parameters['status']) {

            if (is_array($default_parameters['status'])) {
                $query->whereIn("post.status", $default_parameters['status']);
            } else {
                $query->where("post.status", '=', $default_parameters['status']);
            }

        }

        if ($default_parameters['categories']) {

            if (is_array($default_parameters['categories'])) {
                $query->whereIn("post_has_category.category", $default_parameters['categories']);
            } else {
                $query->where("post_has_category.category", '=', $default_parameters['categories']);
            }

        }

        if ($default_parameters['audiences']) {

            if (is_array($default_parameters['audiences'])) {
                $query->whereIn("post_has_audience.audience", $default_parameters['audiences']);
            } else {
                $query->where("post_has_audience.audience", '=', $default_parameters['audiences']);
            }

        }


        $result = $query->paginate($items_per_page);

        if (!empty($result->items())) {

            $types = $this->type()->get();
            $categories = $this->category()->get();
            $status = $this->status()->get();

            $result->getCollection()->map(function ($item) use ($types, $categories, $status) {

                $item->type = $types->where('id','=',$item->type)->map(function ($type) {
                    return collect($type)->only(['id', 'slug', 'title']);
                })->first()->toArray();

                $item->status = $status->where('id','=',$item->status)->map(function ($state) {
                    return collect($state)->only(['id', 'slug', 'title']);
                })->first()->toArray();

                $item->categories = $categories->whereIn('id', explode(',', $item->categories))->values();

                return $item;
            });

        }

        return $result;

    }

    /** Get an array of post or a single post
     * @return \Illuminate\Support\Collection|mixed
     */
    public function get() {

        try {

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
                    db()->raw('MAX(post.last_published_date) as last_published_date'),
                    db()->raw('GROUP_CONCAT(post_has_category.category) as categories'),
                    db()->raw('GROUP_CONCAT(post_has_audience.audience) as audiences'),
                    db()->raw('COUNT(post_comment.id) as comment_count'),
                ])
                ->leftJoin('post_has_category','post.id','=','post_has_category.post')
                ->leftJoin('post_has_audience','post.id','=','post_has_audience.post')
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
                $audiences = cms()->audience()->get();

                $result->each(function($item) use ($types, $categories, $status, $audiences) {

                    $item->type = $types->where('id','=',$item->type)->map(function ($type) {
                        return collect($type)->only(['id', 'slug', 'title']);
                    })->first()->toArray();

                    $item->status = $status->where('id','=',$item->status)->map(function ($state) {
                        return collect($state)->only(['id', 'slug', 'title']);
                    })->first()->toArray();

                    $item->categories = $categories->whereIn('id', explode(',', $item->categories))->values();

                    $item->audiences = $item->audiences ? $audiences->whereIn('id', explode(',', $item->audiences))->map(function ($audience) {
                        return collect($audience)->only(['id', 'title']);
                    })->first()->toArray() : [];

                });

            }

            return $result;

        } catch (QueryException | \Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }

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
    public function add($save_as_status, $type, $title, $description = '', $body = '', $thumbnail = null, $resource = null, $json_data = null, $audiences = []) {

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
                    'status'=> $save_as_status,
                    'body'=> $body,
                    'resource'=> $resource,
                    'json_data'=> $json_data,
                    'thumbnail'=> null,
                    'created_at'=> db()->raw('NOW()'),
                    'updated_at'=> db()->raw('NOW()'),
                ]);

            $this->set_thumbnail($thumbnail);
            $this->set_audiences($audiences);

        } catch (QueryException | \Exception $e) {
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

        if (!empty($description)) {
            $values['description'] = $description;
        }

        if (!empty($body)) {
            $values['body'] = strip_tags($body);
        }

        if (!empty($resource)) {
            $values['resource'] = strip_tags($resource);
        }

        if (!empty($json_data)) {
            $values['json_data'] = strip_tags($json_data);
        }

        if (is_string($title)) {

            $values['title'] = $title;
            $slug = preg_replace('/[^a-z0-9]/', '-', strtolower(trim(strip_tags($title))));

            if ($count = db('cms')->table('post')->where('slug', 'like', $slug.'%')->count()) {
                $slug .= "-$count";
            }
            $values['slug'] = "$slug-$count";

        }

        // verify status is correct
        if ($save_as_status) {
            if (is_numeric($save_as_status) && $this->status()->get()->where('id','=', $save_as_status)->count()) {
                $values['status'] =   $save_as_status;
            } else {
                throw new \Exception('Invalid save as status value',400);
            }
        }

        if (is_numeric($type) && $this->type()->get()->where('id','=', $type)->count()) {
            $values['type'] =   $type;
        }

        if (empty($values)) {
            return false;
        }

        try {

            $values['updated_at'] = db()->raw('NOW()');

            $this->set_thumbnail($thumbnail);

            if ($values['status'] === Status::ID_SUBMITTED) {
                $values['last_published_date'] = db()->raw('NOW()');
            }

            $old_data = db('cms')->table('post')
                ->where('post.id','=', $this->id())
                ->first();

            $result =  db('cms')->table('post')
                ->where('post.id','=', $this->id())
                ->update($values) > 0;

            if ($result) {

                unset($values['last_published_date'],$values['updated_at']);

                auth()->log('cms/post/update',[
                    'id'=> $this->id(),
                    'new_values'=> $values,
                    'old_values'=> array_intersect_key((array)$old_data, $values)
                ]);

            }

            return $result;

        } catch (QueryException | \Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }

    }

    /**
     * @param false $hard_delete
     * @return bool|int
     * @throws \Exception
     */
    public function delete($hard_delete = false) {

        if (!$post = $this->get()->first()) {
            throw new \Exception('Failed to find post',400);
        }

        try {

            if ($hard_delete) {

                if (empty($post->last_published_date)) {
                    return db('cms')->table('post')->delete($this->id()) > 0;
                }

                throw new \Exception("Post can't be deleted, it has been published in the past",400);

            }

            return $this->update( Status::ID_ARCHIVED);

        } catch (\RuntimeException | \Exception $e) {
            throw new \Exception($e->getMessage(),$e->getCode() ?? 500);
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

    /** Set audience for an individual post
     * @throws \Exception
     */
    private function set_audiences($audiences = []) {

        try {

            db()->table('post_has_audience')
                ->where('post_has_audience.post','=', $this->id())
                ->delete();

            if (!empty($audiences)) {

                $values = [];
                foreach ($audiences as $audience) {
                    $values[] = [
                        'post'=> $this->id(),
                        'audience'=> $audience
                    ];
                }

                db()->table('post_has_audience')->insert($values);

            }

        }catch (\Exception | QueryException $e) {
            throw new \Exception($e->getMessage(), 500);
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

    public function comment($comment = null) {
        return new Comment($this->id, $comment);
    }

    public function status() {
        return new Status();
    }


    /**
     * @param null $category
     * @return Category
     */
    public function category($category = null) {
        return new Category($category);
    }

    /**
     * @throws \Exception
     */
    private function id_required(){
        if(empty($this->id)){
            throw new \Exception('Missing post id.', 400);
        }
    }
}
