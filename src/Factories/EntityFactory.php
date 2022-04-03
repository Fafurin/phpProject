<?php

namespace App\Factories;

use App\Decorators\ArticleDecorator;
use App\Decorators\CommentDecorator;
use App\Decorators\UserDecorator;
use App\Entities\EntityInterface;
use App\Enums\Argument;

class EntityFactory implements EntityFactoryInterface
{
    private ?UserFactoryInterface $userFactory;
    private ?ArticleFactoryInterface $articleFactory;
    private ?CommentFactoryInterface $commentFactory;

    public function __construct(
        UserFactoryInterface $userFactory = null,
        ArticleFactoryInterface $articleFactory = null,
        CommentFactoryInterface $commentFactory = null
    )
    {
        $this->userFactory = $userFactory ?? new UserFactory();
        $this->articleFactory = $articleFactory ?? new ArticleFactory();
        $this->commentFactory = $commentFactory ?? new CommentFactory();
    }

    public function create(string $entityType, $arguments): EntityInterface{
        return match ($entityType){
            Argument::USER->value => $this->userFactory->create(new UserDecorator($arguments)),
            Argument::ARTICLE->value => $this->articleFactory->create(new ArticleDecorator($arguments)),
            Argument::COMMENT->value => $this->commentFactory->create(New CommentDecorator($arguments)),
        };
    }
}