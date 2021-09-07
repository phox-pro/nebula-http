<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Exceptions\AnotherInjectionExists;
use Phox\Nebula\Atom\Implementation\StateContainer;
use Phox\Nebula\Http\Implementation\BodyDataResolver;
use Phox\Nebula\Http\Implementation\ParamsValueContainer;
use Phox\Nebula\Http\Implementation\Request;
use Phox\Nebula\Http\Implementation\States\HttpState;
use Phox\Nebula\Http\TestCase;
use Phox\Structures\Collection;

class RequestTest extends TestCase
{
    public function testParamsAtRequest(): void
    {
        $params = new ParamsValueContainer([
            'one' => 1,
            'two' => 'TwoValue',
        ]);

        $this->assertEquals(1, $params->get('one'));
        $this->assertEquals('TwoValue', $params->get('two'));
        $this->assertNull($params->get('Unknown'));
    }

    /**
     * @throws AnotherInjectionExists
     */
    public function testGetServerValue(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $httpState = $this->container()->get(StateContainer::class)->getState(HttpState::class);
        $httpState->listen(function (Request $request) {
            $this->assertEquals('application/json', $request->getServerValue('CONTENT_TYPE'));
            $this->assertNull($request->getServerValue('UNKNOWN_KEY'));
        });

        $this->nebula->run();
    }

    /**
     * @throws AnotherInjectionExists
     */
    public function testRequestSpecificValues(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $this->replaceOriginalBodyDataResolver('{"testFoo":"testBar"}');

        $httpState = $this->container()->get(StateContainer::class)->getState(HttpState::class);
        $httpState->listen(fn(Request $request) => $this->assertEquals(
            ['testFoo' => 'testBar'],
            $request->getValues()->all(),
        ));

        $this->nebula->run();
    }

    public function testAllRequestValues(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_REQUEST = ['testRequestFoo' => 'testRequestBar'];

        $this->replaceOriginalBodyDataResolver('{"testFoo":"testBar"}');

        $httpState = $this->container()->get(StateContainer::class)->getState(HttpState::class);
        $httpState->listen(function (Request $request) {
            $this->assertTrue($request->getValues()->has('testRequestFoo'));
            $this->assertTrue($request->getValues()->has('testFoo'));

            $this->assertEquals('testRequestBar', $request->getValues()->get('testRequestFoo'));
            $this->assertEquals('testBar', $request->getValues()->get('testFoo'));
        });

        $this->nebula->run();
    }

    protected function replaceOriginalBodyDataResolver(string $body): BodyDataResolver
    {
        $originalDataResolver = $this->container()->get(BodyDataResolver::class);

        $fakeDataResolver = new class($originalDataResolver->contentResolvers, $body) extends BodyDataResolver {
            public function __construct(Collection $resolvers, private string $body)
            {
                parent::__construct();

                $this->contentResolvers = $resolvers;
            }

            protected function getUnresolvedData(): string|bool
            {
                return $this->body;
            }
        };

        $this->container()->singleton($fakeDataResolver, BodyDataResolver::class);

        return $fakeDataResolver;
    }
}
