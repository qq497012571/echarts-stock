<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

use HyperfTest\HttpTestCase;

/**
 * @internal
 * @coversNothing
 */
class TeeUserTest extends HttpTestCase
{
    public function testUserDaoFirst3()
    {
        dump([11111111111]);
        $this->assertSame(1, 1);
    }

    public function testUserDaoFirst4()
    {
        dump([1111111111111111]);
        $this->assertSame(1, 1);
    }
}
