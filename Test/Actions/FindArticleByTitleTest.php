<?php

namespace Test\Actions;

use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\SuccessfulResponse;
use App\Repositories\ArticleRepositoryInterface;
use App\Http\Actions\FindArticleByTitle;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyLogger;

class FindArticleByTitleTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoTitleProvided(): void
    {
        $request = new Request([], [], '');
        $articleRepository = $this->getArticleRepository([]);

        $action = new FindArticleByTitle($articleRepository, $this->getLogger());
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString(
            '{"success":false,"reason":"No such query param in the request: title"}'
        );

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfArticleNotFound(): void
    {
        $request = new Request(['title' => 'Test title'], [], '');

        $articleRepository = $this->getArticleRepository([]);
        $action = new FindArticleByTitle($articleRepository, $this->getLogger());

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Cannot find article"}');
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['title' => 'Test title'], [], '');

        $articleRepository = $this->getArticleRepository([
            new Article(
                '15',
                'Test title',
                'Some text'
            ),
        ]);

        $action = new FindArticleByTitle($articleRepository, $this->getLogger());
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"title":"Test title","text":"Some text"}}');

        $response->send();
    }

    private function getArticleRepository(array $articles): ArticleRepositoryInterface
    {
        return new class($articles) implements ArticleRepositoryInterface {

            public function __construct(
                private array $articles
            ) {
            }

            public function get(int $id): Article
            {
                throw new ArticleNotFoundException("Cannot find article");
            }

            public function getArticleById(int $id): Article
            {
                throw new ArticleNotFoundException("Cannot find article");
            }

            public function getArticleByTitle(string $title): Article
            {
                foreach ($this->articles as $article) {
                    if ($article instanceof Article && $title === $article->getTitle()) {
                        return $article;
                    }
                }

                throw new ArticleNotFoundException("Cannot find article");
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