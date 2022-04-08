<?php

namespace App\Http\Actions;

use App\Exceptions\CommentNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Repositories\CommentRepository;
use App\Repositories\CommentRepositoryInterface;


class FindCommentById implements ActionInterface
{

    public function __construct(private ?CommentRepositoryInterface $commentRepository = null)
    {
        $this->commentRepository = $this->commentRepository ?? new CommentRepository();
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $comment = $this->commentRepository->getCommentById((int)$id);
        } catch (CommentNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'text' => $comment->getText()
        ]);
    }
}