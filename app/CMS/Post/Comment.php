<?php


namespace App\CMS\Post;


use Illuminate\Database\QueryException;

class Comment {


    private $id;
    private $post;

    public function __construct($post= null, $comment = null) {

        if ($post !== null && is_numeric($post)) {
            $this->post = (int)$post;
        }

        if ($comment !== null && is_numeric($comment)) {
            $this->id = (int)$comment;
        }

    }

    public function get() {

        $query = db('cms')->table('post_comment');

        if ($this->id !== null) {
            $query->where('id', '=', $this->id);
        }

        if ($this->post !== null) {
            $query->where('post', '=', $this->post);
        }

        return $query->get();
    }

    public function add($content, $parent = 0) {

        if ($this->post === null) {
            throw new \RuntimeException('Missing post id');
        }

        if ($content === null) {
            throw new \RuntimeException('Missing comment content');
        }

        $parent = (int)$parent;

        try {
            $this->id = db('cms')->table('post_comment')
                ->insertGetId([
                    'post'=> $this->post,
                    'parent'=> $parent,
                    'author'=> auth()->id(),
                    'content'=> strip_tags($content),
                    'published'=> true
                ]);
        } catch (QueryException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $this;

    }

    public function update($published = true, $content = null) {

        $values = [
            'published'=> (bool)$published
        ];

        if ($content !== null) {
            $values['content'] = strip_tags($content);
        }

        try {

            return db('cms')->table('post_comment')
                ->where('id','=', $this->id())
                ->update($values);

        } catch (QueryException | \Exception $e) {
            throw new \RuntimeException($e->getMessage(), 500);
        }

    }

    public function delete() {
        try {
            return $this->update( false);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e);
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

    /**
     * @throws \Exception
     */
    private function id_required(){
        if(empty($this->id)){
            throw new \Exception('Missing comment id.');
        }
    }

}
