<?php

namespace Test\Actions;

use App\Container\DIContainer;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;
use App\Http\Actions\FindUserByEmail;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\SuccessfulResponse;
use App\Repositories\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyLogger;

class FindUserByEmailTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoEmailProvided(): void
    {
        $request = new Request([], [], '');
        $userRepository = $this->getUserRepository([]);

        $action = new FindUserByEmail($userRepository, $this->getLogger());
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString(
            '{"success":false,"reason":"No such query param in the request: email"}'
        );

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request(['email' => 'vano@mail.com'], [], '');

        $usersRepository = $this->getUserRepository([]);
        $action = new FindUserByEmail($usersRepository, $this->getLogger());

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['email' => 'vano@mail.com'], [], '');

        $usersRepository = $this->getUserRepository([
            new User(
                'Ivan',
                'Ivanov',
                'vano@mail.com'
            ),
        ]);

        $action = new FindUserByEmail($usersRepository, $this->getLogger());
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"email":"vano@mail.com","name":"Ivan Ivanov"}}');

        $response->send();
    }

    private function getUserRepository(array $users): UserRepositoryInterface
    {
        return new class($users) implements UserRepositoryInterface {

            public function __construct(
                private array $users
            ) {
            }

            public function findById(int $id): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getUserByEmail(string $email): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $email === $user->getEmail()) {
                        return $user;
                    }
                }

                throw new UserNotFoundException("Not found");
            }

            public function getUserById(int $id): User
            {
                throw new UserNotFoundException("Not found");
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