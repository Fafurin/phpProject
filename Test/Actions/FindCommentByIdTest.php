<?php

namespace Test\Actions;

use App\Container\DIContainer;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Http\Actions\FindCommentById;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\SuccessfulResponse;
use App\Repositories\CommentRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyLogger;

class FindCommentByIdTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoIdProvided(): void
    {
        $request = new Request([], [], '');
        $commentRepository = $this->getCommentRepository([]);

        $action = new FindCommentById($commentRepository, $this->getLogger());
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString(
            '{"success":false,"reason":"No such query param in the request: id"}'
        );

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfCommentNotFound(): void
    {
        $request = new Request(['id' => '15'], [], '');

        $commentRepository = $this->getCommentRepository([]);
        $action = new FindCommentById($commentRepository, $this->getLogger());

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Cannot find comment"}');
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['id' => '7'], [], '');

        $commentRepository = $this->getCommentRepository([
            new Comment(
                '7',
                '3',
                '23',
                'Another text'
            ),
        ]);

        $action = new FindCommentById($commentRepository, $this->getLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"text":"Another text"}}');

        $response->send();
    }

    private function getCommentRepository(array $comments): CommentRepositoryInterface
    {
        return new class($comments) implements CommentRepositoryInterface {

            public function __construct(
                private array $comments
            ) {}

            public function findById(int $id): Comment
            {
                foreach ($this->comments as $comment) {
                    if ($comment instanceof Comment && $id === $comment->getId()) {
                        return $comment;
                    }
                }

                throw new CommentNotFoundException("Cannot find comment");
            }
        };
    }

    private function getLogger(): LoggerInterface{
        return $this->getContainer()->get(LoggerInterface::class);
    }

    private function getContainer(): ContainerInterface {
        $container = DIContainer::getInstance();

        $container->bind(
            LoggerInterface::class,
            new DummyLogger()
        );
        return $container;
    }
}