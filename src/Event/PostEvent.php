<?php

namespace App\Event;

use App\Entity\Development\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostEvent extends Event
{
    /**
     * @var Post
     */
    private Post $post;

    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

}
