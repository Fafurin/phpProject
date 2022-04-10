<?php

namespace App\Http\Actions;

use App\Exceptions\CommentNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Repositories\CommentRepositoryInterface;
use Psr\Log\LoggerInterface;


class FindCommentById implements ActionInterface
{

    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
        } catch (HttpException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }
        try {
            $comment = $this->commentRepository->findById((int)$id);
        } catch (CommentNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'text' => $comment->getText()
        ]);
    }
}