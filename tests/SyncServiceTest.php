<?php
/**
 * Laravel-Env-Sync
 *
 * @author Julien Tant - Craftyx <julien@craftyx.fr>
 */

namespace Jtant\LaravelEnvSync;

use Jtant\LaravelEnvSync\Reader\ReaderInterface;
use org\bovigo\vfs\vfsStream;

class SyncServiceTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function it_should_return_the_difference_between_files()
    {
        $root = vfsStream::setup("sync_service");
        $source = $root->url() . '/source';
        $destination = $root->url() . '/dest';
        touch($source);
        touch($destination);


        $readerInterface = \Mockery::mock(ReaderInterface::class)
            ->shouldReceive('read')->twice()->andReturn([
                "foo" => "bar",
                "baz" => "foo",
            ], [
                "foo" => "bar",
                "bar" => "baz",
                "baz" => "foo"
            ]);

        $sync = new SyncService($readerInterface->getMock());


        $result = $sync->getDiff($source, $destination);

        $this->assertEquals([
            'bar' => 'baz'
        ], $result);
    }

    /** @test */
    public function it_should_throw_an_exception_if_file_is_not_found()
    {
        $root = vfsStream::setup("sync_service_2");
        $source = $root->url() . '/source';
        $destination = $root->url() . '/dest';
        touch($source);

        $this->setExpectedException(FileNotFound::class, sprintf("%s must exists", $destination));

        $sync = new SyncService(\Mockery::mock(ReaderInterface::class));

        $sync->getDiff($source, $destination);

    }

    protected function tearDown()
    {
        \Mockery::close();
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}
