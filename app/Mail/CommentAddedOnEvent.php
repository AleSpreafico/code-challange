<?php

namespace App\Mail;

use App\Models\Comment;
use App\Models\Events;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommentAddedOnEvent extends Mailable
{
    use Queueable, SerializesModels;

    private Events $event;

    private Comment $comment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Events $event, Comment $comment)
    {
        $this->event = $event;
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('email.comment_added')
            ->with([
                'event_title' => $this->event->title,
                'comment_author' => $this->comment->user->nick_name,
                'comment_text' => $this->comment->content
            ]);
    }
}
