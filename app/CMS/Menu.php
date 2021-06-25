<?php


namespace App\CMS;

use Illuminate\Support\Collection;
use function PHPUnit\Framework\isEmpty;


class Menu {

    private $id;

    public function __construct($id = null) {
        $this->id = $id;
    }

    /**
     * @return Collection
     */
    public function user_menu_old() {

        if (!auth()->active()) {
            throw new \RuntimeException('Not logged in');
        }

        $current_uri = request()->getRequestUri();
        $user_access = auth()->access();

        $menu = db('cms')->table('menu')
            ->select([
                'menu.id',
                'menu.parent_id',
                'menu.title',
                'menu.url',
            ])
            ->orderBy('menu.parent_id','DESC')
            ->get();

        $menu_access = db('cms')->table('menu_has_access')
            ->select([
                'menu_has_access.menu',
                'access.slug'
            ])
            ->join('access','access.id','=','menu_has_access.access')
            ->get();

        $results = $menu->each(function ($item) use ($menu_access, $current_uri) {

            $item->access = $menu_access->where('menu','=',$item->id)->pluck('slug');
            $item->active = str_starts_with($item->url, $current_uri);

        })->filter(function($item) use ($user_access) {

            if ($item->access->isEmpty() || array_intersect($item->access->toArray(), $user_access)) {
                return $item;
            }

        });

        return self::build_tree($results);

    }

    /**
     * @param Collection $elements
     * @param int $parent_id
     * @return Collection
     */
    private static function build_tree(Collection $elements, $parent_id = 0) {

        $branch = [];

        foreach ($elements as $element) {

            if ($element->parent_id == $parent_id) {

                $children = new Collection(self::build_tree($elements, $element->id));

                if (!$children->isEmpty()) {
                    $element->children = $children;
                    $element->active =  $element->active ?: $children->where('active','=', true)->count() > 0;
                } else {
                    $element->children = false;
                }
                $branch[] = $element;

            }
        }

        return new Collection($branch);
    }

}
